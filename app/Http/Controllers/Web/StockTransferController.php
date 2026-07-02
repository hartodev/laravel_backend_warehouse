<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Warehouse;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    public function index(Request $request)
    {
        $transfers = StockTransfer::with(['fromWarehouse:id,name,code', 'toWarehouse:id,name,code', 'requestedBy:id,name'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->warehouse_id, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('from_warehouse_id', $request->warehouse_id)
                  ->orWhere('to_warehouse_id', $request->warehouse_id);
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $warehouses = Warehouse::where('is_active', true)->get(['id', 'name']);

        return view('superadmin.stock_transfer.index', compact('transfers', 'warehouses'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('is_active', true)->get(['id', 'name']);
        $products   = Product::where('is_active', true)->get(['id', 'name', 'sku', 'unit']);
        return view('superadmin.stock_transfer.create', compact('warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_warehouse_id'          => 'required|exists:warehouses,id',
            'to_warehouse_id'            => 'required|exists:warehouses,id|different:from_warehouse_id',
            'transfer_date'              => 'required|date',
            'expected_arrival'           => 'nullable|date|after_or_equal:transfer_date',
            'notes'                      => 'nullable|string',
            'items'                      => 'required|array|min:1',
            'items.*.product_id'         => 'required|exists:products,id',
            'items.*.quantity_requested' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, &$transfer) {
            $count  = StockTransfer::whereYear('created_at', now()->year)->count() + 1;
            $number = 'TRF/' . now()->format('Y') . '/' . str_pad($count, 4, '0', STR_PAD_LEFT);

            $transfer = StockTransfer::create([
                'transfer_number'   => $number,
                'from_warehouse_id' => $request->from_warehouse_id,
                'to_warehouse_id'   => $request->to_warehouse_id,
                'requested_by'      => auth()->id(),
                'status'            => 'pending',
                'transfer_date'     => $request->transfer_date,
                'expected_arrival'  => $request->expected_arrival,
                'notes'             => $request->notes,
            ]);

            foreach ($request->items as $item) {
                StockTransferItem::create([
                    'stock_transfer_id'  => $transfer->id,
                    'product_id'         => $item['product_id'],
                    'quantity_requested' => $item['quantity_requested'],
                    'quantity_sent'      => 0,
                    'quantity_received'  => 0,
                ]);
            }
        });

        return redirect()->route('superadmin.stock-transfers.show', $transfer)
            ->with('success', 'Transfer stok berhasil dibuat.');
    }

    public function show(StockTransfer $stockTransfer)
    {
        $stockTransfer->load([
            'fromWarehouse:id,name,code',
            'toWarehouse:id,name,code',
            'requestedBy:id,name',
            'approvedBy:id,name',
            'receivedBy:id,name',
            'items.product:id,name,sku,unit',
        ]);
        return view('superadmin.stock_transfer.show', compact('stockTransfer'));
    }

    public function edit(StockTransfer $stockTransfer)
    {
        if ($stockTransfer->status !== 'pending') {
            return back()->with('error', 'Transfer yang sudah diproses tidak dapat diubah.');
        }
        $warehouses = Warehouse::where('is_active', true)->get(['id', 'name']);
        return view('superadmin.stock_transfer.edit', compact('stockTransfer', 'warehouses'));
    }

    public function update(Request $request, StockTransfer $stockTransfer)
    {
        if ($stockTransfer->status !== 'pending') {
            return back()->with('error', 'Transfer yang sudah diproses tidak dapat diubah.');
        }

        $request->validate([
            'transfer_date'    => 'required|date',
            'expected_arrival' => 'nullable|date|after_or_equal:transfer_date',
            'notes'            => 'nullable|string',
        ]);

        $stockTransfer->update($request->only('transfer_date', 'expected_arrival', 'notes'));

        return redirect()->route('superadmin.stock-transfers.show', $stockTransfer)
            ->with('success', 'Transfer berhasil diupdate.');
    }

    public function approve(StockTransfer $stockTransfer)
    {
        if ($stockTransfer->status !== 'pending') {
            return back()->with('error', 'Hanya transfer pending yang dapat disetujui.');
        }

        $stockTransfer->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Transfer disetujui.');
    }

    public function reject(Request $request, StockTransfer $stockTransfer)
    {
        if ($stockTransfer->status !== 'pending') {
            return back()->with('error', 'Hanya transfer pending yang dapat ditolak.');
        }

        $request->validate(['reject_reason' => 'required|string']);

        $stockTransfer->update([
            'status'        => 'rejected',
            'reject_reason' => $request->reject_reason,
        ]);

        return back()->with('success', 'Transfer ditolak.');
    }

    public function send(Request $request, StockTransfer $stockTransfer)
    {
        if ($stockTransfer->status !== 'approved') {
            return back()->with('error', 'Hanya transfer yang disetujui yang dapat dikirim.');
        }

        DB::transaction(function () use ($request, $stockTransfer) {
            foreach ($stockTransfer->items as $item) {
                $stock = Stock::where('warehouse_id', $stockTransfer->from_warehouse_id)
                              ->where('product_id', $item->product_id)
                              ->first();
                if ($stock) {
                    $before = $stock->quantity;
                    $stock->reduceStock($item->quantity_requested);
                    $item->update(['quantity_sent' => $item->quantity_requested]);

                    StockMovement::create([
                        'product_id'      => $item->product_id,
                        'warehouse_id'    => $stockTransfer->from_warehouse_id,
                        'type'            => 'transfer_out', // ← fix: pakai enum yang valid
                        'quantity'        => $item->quantity_requested,
                        'quantity_before' => $before,
                        'quantity_after'  => $stock->quantity,
                        'reference_type'  => 'stock_transfer', // ← fix: ganti stock_transfer_id
                        'reference_id'    => $stockTransfer->id,
                        'created_by'      => auth()->id(),
                        'note'            => "Transfer keluar ke gudang #{$stockTransfer->transfer_number}",
                    ]);
                }
            }

            $stockTransfer->update([
                'status'  => 'in_transit',
                'sent_at' => now(),
                'sent_by' => auth()->id(),
            ]);
        });

        return back()->with('success', 'Stok berhasil dikirim.');
    }

    public function receive(StockTransfer $stockTransfer)
    {
        if ($stockTransfer->status !== 'in_transit') {
            return back()->with('error', 'Hanya transfer in_transit yang dapat diterima.');
        }

        DB::transaction(function () use ($stockTransfer) {
            foreach ($stockTransfer->items as $item) {
                $stock = Stock::firstOrCreate(
                    ['warehouse_id' => $stockTransfer->to_warehouse_id, 'product_id' => $item->product_id],
                    ['quantity' => 0]
                );
                $before = $stock->quantity;
                $stock->addStock($item->quantity_sent);
                $item->update(['quantity_received' => $item->quantity_sent]);

                StockMovement::create([
                    'product_id'      => $item->product_id,
                    'warehouse_id'    => $stockTransfer->to_warehouse_id,
                    'type'            => 'transfer_in', // ← fix: pakai enum yang valid
                    'quantity'        => $item->quantity_sent,
                    'quantity_before' => $before,
                    'quantity_after'  => $stock->quantity,
                    'reference_type'  => 'stock_transfer', // ← fix: ganti stock_transfer_id
                    'reference_id'    => $stockTransfer->id,
                    'created_by'      => auth()->id(),
                    'note'            => "Transfer masuk dari gudang #{$stockTransfer->transfer_number}",
                ]);
            }

            $stockTransfer->update([
                'status'      => 'received',  // ← fix: ganti 'completed' → 'received' sesuai enum
                'received_at' => now(),
                'received_by' => auth()->id(),
            ]);
        });

        return back()->with('success', 'Stok berhasil diterima.');
    }
}

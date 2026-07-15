<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Warehouse;
use App\Models\Product;
use Illuminate\Http\Request;

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

    public function show(StockTransfer $stockTransfer)
    {
        $stockTransfer->load([
            'fromWarehouse:id,name,code',
            'toWarehouse:id,name,code',
            'requestedBy:id,name',
            'approvedBy:id,name',
            'cancelledBy:id,name',
            'receivedBy:id,name',
            'discrepancyReportedBy:id,name',
            'resolvedBy:id,name',
            'items.product:id,name,sku,unit',
        ]);
        return view('superadmin.stock_transfer.show', compact('stockTransfer'));
    }

    // Superadmin masih bisa membuat transfer manual kalau perlu (opsional, jarang dipakai
    // karena flow normal dibuat Admin Gudang A lewat app). Tetap dipertahankan untuk fleksibilitas.
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

        \DB::transaction(function () use ($request, &$transfer) {
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

    // ── APPROVE — Superadmin ──
public function approve(StockTransfer $stockTransfer)
{
    if ($stockTransfer->status !== 'pending_approval') {
        return back()->with('error', 'Hanya transfer yang menunggu approval yang dapat disetujui.');
    }

    $stockTransfer->update([
        'status'      => 'approved',
        'approved_by' => auth()->id(),
        'approved_at' => now(),
    ]);

    return back()->with('success', 'Transfer disetujui. Admin gudang asal dapat mulai mengirim barang.');
}

public function reject(Request $request, StockTransfer $stockTransfer)
{
    if ($stockTransfer->status !== 'pending_approval') {
        return back()->with('error', 'Hanya transfer yang menunggu approval yang dapat ditolak.');
    }

    $request->validate(['reject_reason' => 'required|string']);

    $stockTransfer->update([
        'status'        => 'rejected',
        'reject_reason' => $request->reject_reason,
    ]);

    return back()->with('success', 'Transfer ditolak.');
}

    // ── RESOLVE DISCREPANCY — Superadmin ──
    public function resolveDiscrepancy(Request $request, StockTransfer $stockTransfer)
    {
        if ($stockTransfer->status !== 'discrepancy') {
            return back()->with('error', 'Transfer ini tidak dalam status discrepancy.');
        }

        $request->validate([
            'resolution' => 'required|in:accept,cancel',
            'notes'      => 'required|string',
        ]);

        $newStatus = $request->resolution === 'accept' ? 'received' : 'cancelled';

        $stockTransfer->update([
            'status'           => $newStatus,
            'resolved_by'      => auth()->id(),
            'resolved_at'      => now(),
            'resolution_notes' => $request->notes,
        ]);

        $msg = $newStatus === 'received'
            ? 'Selisih diterima, transfer ditandai selesai.'
            : 'Transfer dibatalkan akibat selisih barang.';

        return back()->with('success', $msg);
    }
}






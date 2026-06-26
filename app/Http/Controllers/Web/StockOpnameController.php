<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    public function index(Request $request)
    {
        $opnames = StockOpname::with(['warehouse:id,name,code', 'createdBy:id,name'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->warehouse_id, fn($q) => $q->where('warehouse_id', $request->warehouse_id))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $warehouses = Warehouse::where('is_active', true)->get(['id', 'name']);

        return view('superadmin.stock_opname.index', compact('opnames', 'warehouses'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('is_active', true)->get(['id', 'name']);
        return view('superadmin.stock_opname.create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'opname_date'  => 'required|date',
            'notes'        => 'nullable|string',
        ]);

        $count  = StockOpname::whereYear('created_at', now()->year)->count() + 1;
        $number = 'OPN/' . now()->format('Y') . '/' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $opname = StockOpname::create([
            'opname_number' => $number,
            'warehouse_id'  => $request->warehouse_id,
            'created_by'    => auth()->id(),
            'status'        => 'draft',
            'opname_date'   => $request->opname_date,
            'notes'         => $request->notes,
        ]);

        return redirect()->route('superadmin.stock-opnames.show', $opname)
            ->with('success', 'Stock opname berhasil dibuat.');
    }

    public function show(StockOpname $stockOpname)
    {
        $stockOpname->load(['warehouse:id,name,code', 'createdBy:id,name', 'approvedBy:id,name', 'items.product:id,name,sku,unit']);
        return view('superadmin.stock_opname.show', compact('stockOpname'));
    }

    public function edit(StockOpname $stockOpname)
    {
        if (!in_array($stockOpname->status, ['draft', 'in_progress'])) {
            return back()->with('error', 'Opname yang sudah selesai tidak dapat diubah.');
        }
        $warehouses = Warehouse::where('is_active', true)->get(['id', 'name']);
        return view('superadmin.stock_opname.edit', compact('stockOpname', 'warehouses'));
    }

    public function update(Request $request, StockOpname $stockOpname)
    {
        if (!in_array($stockOpname->status, ['draft', 'in_progress'])) {
            return back()->with('error', 'Opname yang sudah selesai tidak dapat diubah.');
        }

        $request->validate([
            'opname_date' => 'required|date',
            'notes'       => 'nullable|string',
        ]);

        $stockOpname->update($request->only('opname_date', 'notes'));

        return redirect()->route('superadmin.stock-opnames.show', $stockOpname)
            ->with('success', 'Opname berhasil diupdate.');
    }

    public function start(StockOpname $stockOpname)
    {
        if ($stockOpname->status !== 'draft') {
            return back()->with('error', 'Hanya opname draft yang dapat dimulai.');
        }

        DB::transaction(function () use ($stockOpname) {
            $stocks = Stock::with('product:id,name,sku')
                ->where('warehouse_id', $stockOpname->warehouse_id)
                ->get();

            foreach ($stocks as $stock) {
                StockOpnameItem::create([
                    'stock_opname_id' => $stockOpname->id,
                    'product_id'      => $stock->product_id,
                    'system_stock'    => $stock->quantity,
                    'physical_stock'  => $stock->quantity,
                    'difference'      => 0,
                ]);
            }

            $stockOpname->update(['status' => 'in_progress', 'started_at' => now()]);
        });

        return redirect()->route('superadmin.stock-opnames.show', $stockOpname)
            ->with('success', 'Opname dimulai. Silakan update stok fisik.');
    }

    public function complete(Request $request, StockOpname $stockOpname)
    {
        if ($stockOpname->status !== 'in_progress') {
            return back()->with('error', 'Hanya opname in_progress yang dapat diselesaikan.');
        }

        $request->validate([
            'items'                        => 'required|array|min:1',
            'items.*.stock_opname_item_id' => 'required|exists:stock_opname_items,id',
            'items.*.physical_stock'       => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($request, $stockOpname) {
            foreach ($request->items as $item) {
                $opnameItem = StockOpnameItem::find($item['stock_opname_item_id']);
                if (!$opnameItem || $opnameItem->stock_opname_id !== $stockOpname->id) continue;

                $opnameItem->update([
                    'physical_stock' => $item['physical_stock'],
                    'difference'     => $item['physical_stock'] - $opnameItem->system_stock,
                ]);
            }

            $stockOpname->update(['status' => 'pending_approval', 'completed_at' => now()]);
        });

        return redirect()->route('superadmin.stock-opnames.show', $stockOpname)
            ->with('success', 'Hasil opname berhasil disimpan. Menunggu persetujuan.');
    }

    public function approve(StockOpname $stockOpname)
    {
        if ($stockOpname->status !== 'pending_approval') {
            return back()->with('error', 'Hanya opname pending_approval yang dapat disetujui.');
        }

        DB::transaction(function () use ($stockOpname) {
            foreach ($stockOpname->items as $item) {
                if ($item->difference === 0) continue;

                $stock = Stock::where('warehouse_id', $stockOpname->warehouse_id)
                    ->where('product_id', $item->product_id)
                    ->first();
                if (!$stock) continue;

                $before = $stock->quantity;
                $stock->update(['quantity' => $item->physical_stock]);

                StockMovement::create([
                    'product_id'      => $item->product_id,
                    'warehouse_id'    => $stockOpname->warehouse_id,
                    'type'            => 'adjustment',
                    'quantity'        => abs($item->difference),
                    'quantity_before' => $before,
                    'quantity_after'  => $item->physical_stock,
                    'stock_opname_id' => $stockOpname->id,
                    'created_by'      => auth()->id(),
                    'note'            => "Penyesuaian stok dari opname #{$stockOpname->opname_number}",
                ]);
            }

            $stockOpname->update(['status' => 'approved', 'approved_by' => auth()->id(), 'approved_at' => now()]);
        });

        return redirect()->route('superadmin.stock-opnames.show', $stockOpname)
            ->with('success', 'Opname disetujui dan stok telah disesuaikan.');
    }

    public function reject(Request $request, StockOpname $stockOpname)
    {
        if ($stockOpname->status !== 'pending_approval') {
            return back()->with('error', 'Hanya opname pending_approval yang dapat ditolak.');
        }

        $request->validate(['reject_reason' => 'required|string']);

        $stockOpname->update(['status' => 'in_progress', 'reject_reason' => $request->reject_reason]);

        return redirect()->route('superadmin.stock-opnames.show', $stockOpname)
            ->with('success', 'Opname dikembalikan untuk perbaikan.');
    }
}

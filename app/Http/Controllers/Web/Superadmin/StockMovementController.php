<?php

namespace App\Http\Controllers\Web\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $movements = StockMovement::with([
            'product:id,name,sku,unit',
            'warehouse:id,name,code',
            'createdBy:id,name',
        ])
            ->when($request->warehouse_id, fn($q) => $q->where('warehouse_id', $request->warehouse_id))
            ->when($request->product_id, fn($q) => $q->where('product_id', $request->product_id))
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $warehouses = Warehouse::where('is_active', true)->get(['id', 'name']);
        $products   = Product::where('is_active', true)->get(['id', 'name', 'sku']);

        return view('superadmin.stock_movement.index', compact('movements', 'warehouses', 'products'));
    }

    public function show(StockMovement $movement)
    {
        // dd($movement->toArray()); // ← tambah ini
        $movement->load(['product:id,name,sku,unit', 'warehouse:id,name,code', 'createdBy:id,name']);
        return view('superadmin.stock_movement.show', compact('movement'));
    }
    public function create()
    {
        $warehouses = Warehouse::where('is_active', true)->get(['id', 'name']);
        $products   = Product::where('is_active', true)->get(['id', 'name', 'sku', 'unit']);
        return view('superadmin.stock_movement.create', compact('warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id'   => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'type'         => 'required|in:in,out,adjustment',
            'quantity'     => 'required|integer|min:1',
            'note'         => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $stock = Stock::firstOrCreate(
                ['warehouse_id' => $request->warehouse_id, 'product_id' => $request->product_id],
                ['quantity' => 0]
            );

            $before = $stock->quantity;

            if ($request->type === 'out') {
                $stock->reduceStock($request->quantity);
            } else {
                $stock->addStock($request->quantity);
            }

            StockMovement::create([
                'product_id'      => $request->product_id,
                'warehouse_id'    => $request->warehouse_id,
                'type'            => $request->type,
                'quantity'        => $request->quantity,
                'quantity_before' => $before,
                'quantity_after'  => $stock->quantity,
                'created_by'      => auth()->id(),
                'note'            => $request->note,
            ]);
        });

        return redirect()->route('stock-movement.index')
            ->with('success', 'Pergerakan stok berhasil dicatat.');
    }
}

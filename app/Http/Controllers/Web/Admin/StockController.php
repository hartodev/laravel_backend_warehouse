<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockController extends Controller
{
    // ── GET /admin/stocks ──────────────────────────────────────
    public function index(Request $request): View
    {
        $stocks = Stock::with(['product:id,name,sku,unit,min_stock', 'warehouse:id,name,code'])
            ->when($request->warehouse_id, fn($q) => $q->where('warehouse_id', $request->warehouse_id))
            ->when($request->search, fn($q) => $q->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%");
            }))
            ->when($request->low_stock, fn($q) => $q->whereHas('product', function ($q) {
                $q->whereColumn('stocks.quantity', '<=', 'products.min_stock');
            }))
            ->orderBy('quantity')
            ->paginate(20)
            ->withQueryString();

        $warehouses = Warehouse::orderBy('name')->get(['id', 'name', 'code']);
        $products   = Product::where('is_active', true)->orderBy('name')->get(['id', 'name', 'sku', 'unit']);

        return view('admin.stocks.index', compact('stocks', 'warehouses', 'products'));
    }

    // ── POST /admin/stocks/manual-in ──────────────────────────
    public function manualIn(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id'   => 'required|exists:products,id',
            'quantity'     => 'required|integer|min:1',
            'note'         => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated) {
            $stock = Stock::firstOrCreate(
                ['warehouse_id' => $validated['warehouse_id'], 'product_id' => $validated['product_id']],
                ['quantity' => 0]
            );

            $before = $stock->quantity;
            $stock->addStock($validated['quantity']);

            StockMovement::create([
                'product_id'      => $validated['product_id'],
                'warehouse_id'    => $validated['warehouse_id'],
                'type'            => 'in',
                'quantity'        => $validated['quantity'],
                'quantity_before' => $before,
                'quantity_after'  => $stock->quantity,
                'reference_type'  => 'manual',
                'created_by'      => auth()->id(),
                'note'            => $validated['note'] ?? 'Input stok manual oleh admin',
            ]);
        });

        return redirect()->route('admin.stocks.index')
            ->with('success', 'Stok berhasil ditambahkan.');
    }
}

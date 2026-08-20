<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;


class StockController extends Controller
{
     // GET /api/stocks — semua stok
    public function index(Request $request): JsonResponse
    {
        $stocks = Stock::with(['product:id,name,sku,unit,min_stock', 'warehouse:id,name,code'])
            ->when($request->warehouse_id, fn($q) => $q->where('warehouse_id', $request->warehouse_id))
            ->when($request->product_id, fn($q) => $q->where('product_id', $request->product_id))
            ->when($request->search, fn($q) => $q->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%");
            }))
            ->paginate($request->per_page ?? 15);

        return response()->json(['success' => true, 'data' => $stocks]);
    }

    // GET /api/stocks/low — produk dengan stok mendekati minimum
    public function lowStock(Request $request): JsonResponse
    {
        $stocks = Stock::with(['product:id,name,sku,unit,min_stock', 'warehouse:id,name,code'])
            ->whereHas('product', fn($q) => $q->whereColumn('stocks.quantity', '<=', 'products.min_stock'))
            ->when($request->warehouse_id, fn($q) => $q->where('warehouse_id', $request->warehouse_id))
            ->get();

        return response()->json([
            'success' => true,
            'message' => "Terdapat {$stocks->count()} produk dengan stok rendah.",
            'data'    => $stocks,
        ]);
    }

    // GET /api/stocks/{warehouse} — stok per gudang
    public function byWarehouse(Request $request, Warehouse $warehouse): JsonResponse
    {
        $stocks = Stock::with('product:id,name,sku,unit,min_stock,purchase_price,selling_price')
            ->where('warehouse_id', $warehouse->id)
            ->when($request->search, fn($q) => $q->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%");
            }))
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data'    => [
                'warehouse'    => $warehouse->only('id', 'name', 'code', 'location'),
                'total_items'  => $stocks->total(),
                'total_value'  => Stock::where('warehouse_id', $warehouse->id)
                                       ->join('products', 'stocks.product_id', '=', 'products.id')
                                       ->sum(\DB::raw('stocks.quantity * products.purchase_price')),
                'stocks'       => $stocks,
            ],
        ]);
    }


    // POST /api/stocks/manual-in — tambah/input stok manual (bukan dari PO)
public function manualIn(Request $request): JsonResponse
{
    $validated = $request->validate([
        'warehouse_id' => 'required|exists:warehouses,id',
        'product_id'   => 'required|exists:products,id',
        'quantity'     => 'required|integer|min:1',
        'note'         => 'nullable|string|max:255',
    ]);

    $stock = DB::transaction(function () use ($validated) {
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

        return $stock;
    });

    return response()->json([
        'success' => true,
        'message' => 'Stok berhasil ditambahkan.',
        'data'    => $stock->fresh()->load('product:id,name,sku', 'warehouse:id,name,code'),
    ]);
}
}


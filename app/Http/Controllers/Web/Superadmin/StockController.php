<?php

namespace App\Http\Controllers\Web\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\Warehouse;
use App\Models\Product;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $stocks = Stock::with(['product:id,name,sku,unit,min_stock', 'warehouse:id,name,code'])
            ->when($request->warehouse_id, fn($q) => $q->where('warehouse_id', $request->warehouse_id))
            ->when($request->product_id, fn($q) => $q->where('product_id', $request->product_id))
            ->when($request->search, fn($q) => $q->whereHas('product', fn($q) =>
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%")
            ))
            ->paginate(20)
            ->withQueryString();

        $warehouses = Warehouse::where('is_active', true)->get(['id', 'name']);

        return view('superadmin.stocks.index', compact('stocks', 'warehouses'));
    }

    public function lowStock(Request $request)
    {
        $stocks = Stock::with(['product:id,name,sku,unit,min_stock', 'warehouse:id,name,code'])
            ->whereHas('product', fn($q) => $q->whereColumn('stocks.quantity', '<=', 'products.min_stock'))
            ->when($request->warehouse_id, fn($q) => $q->where('warehouse_id', $request->warehouse_id))
            ->get();

        $warehouses = Warehouse::where('is_active', true)->get(['id', 'name']);

        return view('superadmin.stocks.low_stock', compact('stocks', 'warehouses'));
    }

    public function byWarehouse(Request $request, Warehouse $warehouse)
    {
        $stocks = Stock::with('product:id,name,sku,unit,min_stock,purchase_price,selling_price')
            ->where('warehouse_id', $warehouse->id)
            ->when($request->search, fn($q) => $q->whereHas('product', fn($q) =>
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%")
            ))
            ->paginate(20)
            ->withQueryString();

        $totalValue = Stock::where('warehouse_id', $warehouse->id)
            ->join('products', 'stocks.product_id', '=', 'products.id')
            ->sum(\DB::raw('stocks.quantity * products.purchase_price'));

        return view('superadmin.stocks.by_warehouse', compact('warehouse', 'stocks', 'totalValue'));
    }
}
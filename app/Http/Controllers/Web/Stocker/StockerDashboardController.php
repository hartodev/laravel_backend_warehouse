<?php

namespace App\Http\Controllers\Web\Stocker;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class StockerDashboardController extends Controller
{
    /**
     * Dashboard khusus role stocker (warehouse_keeper).
     * Sengaja cuma nampilin ringkasan stok — tidak ada data
     * finance/PO/SO seperti di dashboard admin, karena stocker
     * hanya berhak lihat stok (read-only).
     */
    public function index()
    {
        $stats = [
            'total_products'   => Product::where('is_active', true)->count(),
            'total_warehouses' => Warehouse::where('is_active', true)->count(),
        ];

        $lowStocks = Stock::with(['product:id,name,sku,unit,min_stock', 'warehouse:id,name'])
            ->whereHas('product', fn ($q) => $q->whereColumn('stocks.quantity', '<=', 'products.min_stock'))
            ->orderBy('quantity')
            ->limit(10)
            ->get();

        $stockValueByWarehouse = Warehouse::withSum(
            [
                'stocks as stock_value' => fn ($q) => $q->join('products', 'stocks.product_id', '=', 'products.id')
                    ->select(DB::raw('SUM(stocks.quantity * products.purchase_price)')),
            ],
            'stock_value'
        )
            ->where('is_active', true)
            ->get(['id', 'name', 'stock_value']);

        return view('stocker.dashboard', compact('stats', 'lowStocks', 'stockValueByWarehouse'));
    }
}
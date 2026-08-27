<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\StockReport;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class StockReportController extends Controller
{
    /**
     * Halaman utama Laporan Stok — ringkasan nilai stok per gudang/produk.
     * Ini yang dipakai untuk route admin.stock-reports.index.
     */
    public function index(Request $request)
    {
        $warehouseId = $request->warehouse_id;

        $summary = Stock::with(['product:id,name,sku,unit,min_stock', 'warehouse:id,name,code'])
            ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
            ->when($request->search, fn($q) => $q->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('sku', 'like', "%{$request->search}%");
            }))
            ->paginate(20)
            ->withQueryString();

        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();

        return view('Admin.stock_report.index', compact('summary', 'warehouses'));
    }

    /**
     * Detail histori laporan periodik untuk satu gudang (opsional, kalau
     * halaman detail per-gudang dipakai nanti).
     */
    public function byWarehouse(Request $request, Warehouse $warehouse)
    {
        $reports = StockReport::with('product:id,name,sku,unit')
            ->where('warehouse_id', $warehouse->id)
            ->when($request->period_type, fn($q) => $q->where('period_type', $request->period_type))
            ->when($request->date_from, fn($q) => $q->where('period_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->where('period_date', '<=', $request->date_to))
            ->orderByDesc('period_date')
            ->paginate(20)
            ->withQueryString();

        return view('Admin.stock_report.by_warehouse', compact('reports', 'warehouse'));
    }
}

// namespace App\Http\Controllers\Web\Admin;

// use App\Http\Controllers\Controller;
// use App\Models\Stock;
// use App\Models\StockReport;
// use App\Models\Warehouse;
// use Illuminate\Http\Request;

// class StockReportController extends Controller
// {
//     /**
//      * Halaman utama Laporan Stok — ringkasan nilai stok per gudang/produk.
//      * Ini yang dipakai untuk route admin.stock-reports.index.
//      */
//     public function index(Request $request)
//     {
//         $warehouseId = $request->warehouse_id;

//         $summary = Stock::with(['product:id,name,sku,unit,min_stock', 'warehouse:id,name,code'])
//             ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
//             ->when($request->search, fn($q) => $q->whereHas('product', function ($q) use ($request) {
//                 $q->where('name', 'like', "%{$request->search}%")
//                     ->orWhere('sku', 'like', "%{$request->search}%");
//             }))
//             ->paginate(20)
//             ->withQueryString();

//         $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();

//         return view('Admin.stock-reports.index', compact('summary', 'warehouses'));
//     }

//     /**
//      * Detail histori laporan periodik untuk satu gudang (opsional, kalau
//      * halaman detail per-gudang dipakai nanti).
//      */
//     public function byWarehouse(Request $request, Warehouse $warehouse)
//     {
//         $reports = StockReport::with('product:id,name,sku,unit')
//             ->where('warehouse_id', $warehouse->id)
//             ->when($request->period_type, fn($q) => $q->where('period_type', $request->period_type))
//             ->when($request->date_from, fn($q) => $q->where('period_date', '>=', $request->date_from))
//             ->when($request->date_to, fn($q) => $q->where('period_date', '<=', $request->date_to))
//             ->orderByDesc('period_date')
//             ->paginate(20)
//             ->withQueryString();

//         return view('Admin.stock-reports.by-warehouse', compact('reports', 'warehouse'));
//     }
// }
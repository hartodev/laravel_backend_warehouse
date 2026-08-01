<?php

namespace App\Http\Controllers\Web\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\StockReport;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class StockReportController extends Controller
{
    public function index(Request $request)
    {
        $reports = StockReport::with(['product:id,name,sku,unit', 'warehouse:id,name,code'])
            ->when($request->period_type, fn($q) => $q->where('period_type', $request->period_type))
            ->when($request->warehouse_id, fn($q) => $q->where('warehouse_id', $request->warehouse_id))
            ->when($request->product_id, fn($q) => $q->where('product_id', $request->product_id))
            ->when($request->date_from, fn($q) => $q->where('period_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->where('period_date', '<=', $request->date_to))
            ->orderByDesc('period_date')
            ->paginate(20)
            ->withQueryString();

        $warehouses = Warehouse::where('is_active', true)->get(['id', 'name']);

        return view('superadmin.stock_report.index', compact('reports', 'warehouses'));
    }

    public function summary(Request $request)
    {
        $warehouses = Warehouse::where('is_active', true)->get(['id', 'name']);

        $summary = Stock::with(['product:id,name,sku,unit,min_stock', 'warehouse:id,name,code'])
            ->when($request->warehouse_id, fn($q) => $q->where('warehouse_id', $request->warehouse_id))
            ->get()
            ->map(fn($stock) => [
                'warehouse' => $stock->warehouse->name,
                'product'   => $stock->product->name,
                'sku'       => $stock->product->sku,
                'unit'      => $stock->product->unit,
                'quantity'  => $stock->quantity,
                'min_stock' => $stock->product->min_stock,
                'is_low'    => $stock->quantity <= $stock->product->min_stock,
            ]);

        return view('superadmin.stock_report.summary', compact('summary', 'warehouses'));
    }

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

        return view('superadmin.stock_report.by_warehouse', compact('warehouse', 'reports'));
    }
}


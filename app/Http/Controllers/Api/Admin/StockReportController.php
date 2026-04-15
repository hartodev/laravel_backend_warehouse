<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockReport;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockReportController extends Controller
{
      public function index(Request $request): JsonResponse
    {
        $reports = StockReport::with(['product:id,name,sku,unit', 'warehouse:id,name,code'])
            ->when($request->period_type, fn($q) => $q->where('period_type', $request->period_type))
            ->when($request->warehouse_id, fn($q) => $q->where('warehouse_id', $request->warehouse_id))
            ->when($request->product_id, fn($q) => $q->where('product_id', $request->product_id))
            ->when($request->date_from, fn($q) => $q->where('period_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->where('period_date', '<=', $request->date_to))
            ->orderByDesc('period_date')
            ->paginate($request->per_page ?? 20);
 
        return response()->json(['success' => true, 'data' => $reports]);
    }
 
    public function summary(Request $request): JsonResponse
    {
        $warehouseId = $request->warehouse_id;
 
        $summary = \App\Models\Stock::with(['product:id,name,sku,unit,min_stock', 'warehouse:id,name,code'])
            ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
            ->get()
            ->map(fn($stock) => [
                'warehouse'   => $stock->warehouse->name,
                'product'     => $stock->product->name,
                'sku'         => $stock->product->sku,
                'unit'        => $stock->product->unit,
                'quantity'    => $stock->quantity,
                'min_stock'   => $stock->product->min_stock,
                'is_low'      => $stock->isLow(),
            ]);
 
        return response()->json(['success' => true, 'data' => $summary]);
    }
 
    public function byWarehouse(Request $request, Warehouse $warehouse): JsonResponse
    {
        $reports = StockReport::with('product:id,name,sku,unit')
            ->where('warehouse_id', $warehouse->id)
            ->when($request->period_type, fn($q) => $q->where('period_type', $request->period_type))
            ->when($request->date_from, fn($q) => $q->where('period_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->where('period_date', '<=', $request->date_to))
            ->orderByDesc('period_date')
            ->paginate($request->per_page ?? 20);
 
        return response()->json(['success' => true, 'data' => $reports]);
    }
}

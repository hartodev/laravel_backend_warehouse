<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StockMovementController extends Controller
{
   public function index(Request $request): JsonResponse
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
            ->paginate($request->per_page ?? 20);
 
        return response()->json(['success' => true, 'data' => $movements]);
    }
 
    public function show(StockMovement $movement): JsonResponse
    {
        $movement->load(['product:id,name,sku,unit', 'warehouse:id,name,code', 'createdBy:id,name']);
 
        return response()->json(['success' => true, 'data' => $movement]);
    }
 
    // POST /api/stock-movements — input stok manual (adjustment)
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id'   => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'type'         => 'required|in:in,out,adjustment',
            'quantity'     => 'required|integer|min:1',
            'note'         => 'nullable|string',
        ]);
 
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }
 
        DB::transaction(function () use ($request, &$movement) {
            $stock = Stock::firstOrCreate(
                ['warehouse_id' => $request->warehouse_id, 'product_id' => $request->product_id],
                ['quantity' => 0]
            );
 
            $before = $stock->quantity;
 
            if ($request->type === 'out' || ($request->type === 'adjustment' && $request->adjustment_type === 'out')) {
                $stock->reduceStock($request->quantity);
            } else {
                $stock->addStock($request->quantity);
            }
 
            $movement = StockMovement::create([
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
 
        return response()->json(['success' => true, 'message' => 'Pergerakan stok berhasil dicatat.', 'data' => $movement->load(['product:id,name,sku', 'warehouse:id,name'])], 201);
    }
}

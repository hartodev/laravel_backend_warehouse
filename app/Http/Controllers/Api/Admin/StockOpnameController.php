<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StockOpnameController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $opnames = StockOpname::with(['warehouse:id,name,code', 'createdBy:id,name'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->warehouse_id, fn($q) => $q->where('warehouse_id', $request->warehouse_id))
            ->latest()
            ->paginate($request->per_page ?? 15);
 
        return response()->json(['success' => true, 'data' => $opnames]);
    }
 
    public function show(StockOpname $opname): JsonResponse
    {
        $opname->load(['warehouse:id,name,code', 'createdBy:id,name', 'approvedBy:id,name', 'items.product:id,name,sku,unit']);
 
        return response()->json(['success' => true, 'data' => $opname]);
    }
 
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'warehouse_id' => 'required|exists:warehouses,id',
            'opname_date'  => 'required|date',
            'notes'        => 'nullable|string',
        ]);
 
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }
 
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
 
        return response()->json(['success' => true, 'message' => 'Stock opname berhasil dibuat.', 'data' => $opname], 201);
    }
 
    public function update(Request $request, StockOpname $opname): JsonResponse
    {
        if (! in_array($opname->status, ['draft', 'in_progress'])) {
            return response()->json(['success' => false, 'message' => 'Opname yang sudah selesai tidak dapat diubah.'], 422);
        }
 
        $opname->update($request->only('opname_date', 'notes'));
 
        return response()->json(['success' => true, 'message' => 'Opname berhasil diupdate.', 'data' => $opname->fresh()]);
    }
 
    // POST start — mulai opname, snapshot stok sistem
    public function start(StockOpname $opname): JsonResponse
    {
        if ($opname->status !== 'draft') {
            return response()->json(['success' => false, 'message' => 'Hanya opname draft yang dapat dimulai.'], 422);
        }
 
        DB::transaction(function () use ($opname) {
            $stocks = Stock::with('product:id,name,sku')
                           ->where('warehouse_id', $opname->warehouse_id)
                           ->get();
 
            foreach ($stocks as $stock) {
                StockOpnameItem::create([
                    'stock_opname_id' => $opname->id,
                    'product_id'      => $stock->product_id,
                    'system_stock'    => $stock->quantity,
                    'physical_stock'  => $stock->quantity, // default sama, nanti diubah
                    'difference'      => 0,
                ]);
            }
 
            $opname->update(['status' => 'in_progress', 'started_at' => now()]);
        });
 
        return response()->json(['success' => true, 'message' => 'Opname dimulai. Silakan update stok fisik.', 'data' => $opname->fresh()->load('items.product:id,name,sku')]);
    }
 
    // POST complete — simpan hasil hitung fisik
    public function complete(Request $request, StockOpname $opname): JsonResponse
    {
        if ($opname->status !== 'in_progress') {
            return response()->json(['success' => false, 'message' => 'Hanya opname in_progress yang dapat diselesaikan.'], 422);
        }
 
        $validator = Validator::make($request->all(), [
            'items'                         => 'required|array|min:1',
            'items.*.stock_opname_item_id'  => 'required|exists:stock_opname_items,id',
            'items.*.physical_stock'        => 'required|integer|min:0',
        ]);
 
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }
 
        DB::transaction(function () use ($request, $opname) {
            foreach ($request->items as $item) {
                $opnameItem = StockOpnameItem::find($item['stock_opname_item_id']);
                if (! $opnameItem || $opnameItem->stock_opname_id !== $opname->id) continue;
 
                $opnameItem->update([
                    'physical_stock' => $item['physical_stock'],
                    'difference'     => $item['physical_stock'] - $opnameItem->system_stock,
                ]);
            }
 
            $opname->update(['status' => 'pending_approval', 'completed_at' => now()]);
        });
 
        return response()->json(['success' => true, 'message' => 'Hasil opname berhasil disimpan. Menunggu persetujuan.', 'data' => $opname->fresh()->load('items.product:id,name,sku')]);
    }
 
    // POST submit
    public function submitApproval(StockOpname $opname): JsonResponse
    {
        if ($opname->status !== 'in_progress') {
            return response()->json(['success' => false, 'message' => 'Status tidak sesuai.'], 422);
        }
 
        $opname->update(['status' => 'pending_approval']);
 
        return response()->json(['success' => true, 'message' => 'Opname diajukan untuk persetujuan.']);
    }
 
    // POST approve — setujui dan adjust stok
    public function approve(StockOpname $opname): JsonResponse
    {
        if ($opname->status !== 'pending_approval') {
            return response()->json(['success' => false, 'message' => 'Hanya opname pending_approval yang dapat disetujui.'], 422);
        }
 
        DB::transaction(function () use ($opname) {
            foreach ($opname->items as $item) {
                if ($item->difference === 0) continue;
 
                $stock = Stock::where('warehouse_id', $opname->warehouse_id)
                               ->where('product_id', $item->product_id)
                               ->first();
 
                if (! $stock) continue;
 
                $before = $stock->quantity;
                $stock->update(['quantity' => $item->physical_stock]);
 
                StockMovement::create([
                    'product_id'      => $item->product_id,
                    'warehouse_id'    => $opname->warehouse_id,
                    'type'            => 'adjustment',
                    'quantity'        => abs($item->difference),
                    'quantity_before' => $before,
                    'quantity_after'  => $item->physical_stock,
                    'stock_opname_id' => $opname->id,
                    'created_by'      => auth()->id(),
                    'note'            => "Penyesuaian stok dari opname #{$opname->opname_number}",
                ]);
            }
 
            $opname->update(['status' => 'approved', 'approved_by' => auth()->id(), 'approved_at' => now()]);
        });
 
        return response()->json(['success' => true, 'message' => 'Opname disetujui dan stok telah disesuaikan.', 'data' => $opname->fresh()]);
    }
 
    // POST reject
    public function reject(Request $request, StockOpname $opname): JsonResponse
    {
        if ($opname->status !== 'pending_approval') {
            return response()->json(['success' => false, 'message' => 'Hanya opname pending_approval yang dapat ditolak.'], 422);
        }
 
        $validator = Validator::make($request->all(), ['reject_reason' => 'required|string']);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Alasan penolakan wajib diisi.', 'errors' => $validator->errors()], 422);
        }
 
        $opname->update(['status' => 'in_progress', 'reject_reason' => $request->reject_reason]);
 
        return response()->json(['success' => true, 'message' => 'Opname dikembalikan untuk perbaikan.', 'data' => $opname->fresh()]);
    }
}

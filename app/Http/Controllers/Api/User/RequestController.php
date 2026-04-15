<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Request as StockRequest;
use App\Models\RequestItem;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RequestController extends Controller
{
     // GET — user lihat requestnya sendiri, admin lihat semua
    public function index(Request $request): JsonResponse
    {
        $query = StockRequest::with(['user:id,name', 'items.product:id,name,sku,unit', 'approvedBy:id,name'])
            ->when(! auth()->user()->isSuperAdmin() && ! auth()->user()->isAdmin(), fn($q) => $q->where('user_id', auth()->id()))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->purpose, fn($q) => $q->where('purpose', $request->purpose))
            ->latest()
            ->paginate($request->per_page ?? 15);
 
        return response()->json(['success' => true, 'data' => $query]);
    }
 
    // Admin lihat semua request
    public function indexAdmin(Request $request): JsonResponse
    {
        $requests = StockRequest::with(['user:id,name', 'items.product:id,name,sku,unit', 'approvedBy:id,name'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->user_id, fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->purpose, fn($q) => $q->where('purpose', $request->purpose))
            ->latest()
            ->paginate($request->per_page ?? 15);
 
        return response()->json(['success' => true, 'data' => $requests]);
    }
 
    public function show(StockRequest $request): JsonResponse
    {
        // User hanya bisa lihat miliknya sendiri
        if (auth()->user()->isUser() && $request->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }
 
        $request->load(['user:id,name', 'items.product:id,name,sku,unit,photo', 'approvedBy:id,name', 'stockMovements']);
 
        return response()->json(['success' => true, 'data' => $request]);
    }
 
    public function showAdmin(StockRequest $request): JsonResponse
    {
        $request->load(['user:id,name', 'items.product:id,name,sku,unit', 'approvedBy:id,name', 'stockMovements']);
 
        return response()->json(['success' => true, 'data' => $request]);
    }
 
    public function store(Request $httpRequest): JsonResponse
    {
        $validator = Validator::make($httpRequest->all(), [
            'purpose'            => 'required|in:maintenance,distribution,return,other',
            'note'               => 'nullable|string',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.note'       => 'nullable|string',
        ]);
 
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }
 
        // Cek duplikat produk dalam satu request
        $productIds = array_column($httpRequest->items, 'product_id');
        if (count($productIds) !== count(array_unique($productIds))) {
            return response()->json(['success' => false, 'message' => 'Produk yang sama tidak boleh duplikat dalam satu permintaan.'], 422);
        }
 
        DB::transaction(function () use ($httpRequest, &$stockRequest) {
            $stockRequest = StockRequest::create([
                'user_id' => auth()->id(),
                'purpose' => $httpRequest->purpose,
                'status'  => 'pending',
                'note'    => $httpRequest->note,
            ]);
 
            foreach ($httpRequest->items as $item) {
                RequestItem::create([
                    'request_id' => $stockRequest->id,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'note'       => $item['note'] ?? null,
                ]);
            }
        });
 
        return response()->json(['success' => true, 'message' => 'Permintaan barang berhasil dikirim.', 'data' => $stockRequest->load('items.product:id,name,sku')], 201);
    }
 
    public function update(Request $httpRequest, StockRequest $request): JsonResponse
    {
        if ($request->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }
 
        if (! $request->isPending()) {
            return response()->json(['success' => false, 'message' => 'Hanya permintaan pending yang dapat diubah.'], 422);
        }
 
        $request->update($httpRequest->only('purpose', 'note'));
 
        return response()->json(['success' => true, 'message' => 'Permintaan berhasil diupdate.', 'data' => $request->fresh()->load('items.product:id,name,sku')]);
    }
 
    // Cancel / delete
    public function destroy(StockRequest $request): JsonResponse
    {
        if ($request->user_id !== auth()->id() && ! auth()->user()->isAdmin() && ! auth()->user()->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }
 
        if (! $request->isPending()) {
            return response()->json(['success' => false, 'message' => 'Hanya permintaan pending yang dapat dibatalkan.'], 422);
        }
 
        $request->items()->delete();
        $request->delete();
 
        return response()->json(['success' => true, 'message' => 'Permintaan berhasil dibatalkan.']);
    }
 
    // Admin: approve
    public function approve(Request $httpRequest, StockRequest $request): JsonResponse
    {
        if (! $request->isPending()) {
            return response()->json(['success' => false, 'message' => 'Hanya permintaan pending yang dapat disetujui.'], 422);
        }
 
        $validator = Validator::make($httpRequest->all(), [
            'warehouse_id'                => 'required|exists:warehouses,id',
            'items'                       => 'required|array',
            'items.*.request_item_id'     => 'required|exists:request_items,id',
            'items.*.approved_quantity'   => 'required|integer|min:0',
        ]);
 
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }
 
        DB::transaction(function () use ($httpRequest, $request) {
            foreach ($httpRequest->items as $item) {
                $requestItem = RequestItem::find($item['request_item_id']);
                if (! $requestItem || $requestItem->request_id !== $request->id) continue;
 
                $approvedQty = $item['approved_quantity'];
                $requestItem->update(['approved_quantity' => $approvedQty]);
 
                if ($approvedQty > 0) {
                    $stock = Stock::where('warehouse_id', $httpRequest->warehouse_id)
                                   ->where('product_id', $requestItem->product_id)
                                   ->first();
 
                    if ($stock) {
                        $before = $stock->quantity;
                        $stock->reduceStock($approvedQty);
 
                        StockMovement::create([
                            'product_id'      => $requestItem->product_id,
                            'warehouse_id'    => $httpRequest->warehouse_id,
                            'type'            => 'out',
                            'quantity'        => $approvedQty,
                            'quantity_before' => $before,
                            'quantity_after'  => $stock->quantity,
                            'request_id'      => $request->id,
                            'request_item_id' => $requestItem->id,
                            'created_by'      => auth()->id(),
                            'note'            => "Pengeluaran untuk permintaan #{$request->id}",
                        ]);
                    }
                }
            }
 
            $request->update([
                'status'      => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
        });
 
        return response()->json(['success' => true, 'message' => 'Permintaan disetujui dan stok telah dikeluarkan.', 'data' => $request->fresh()->load('items.product:id,name,sku')]);
    }
 
    // Admin: reject
    public function reject(Request $httpRequest, StockRequest $request): JsonResponse
    {
        if (! $request->isPending()) {
            return response()->json(['success' => false, 'message' => 'Hanya permintaan pending yang dapat ditolak.'], 422);
        }
 
        $validator = Validator::make($httpRequest->all(), ['reject_reason' => 'required|string']);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Alasan penolakan wajib diisi.', 'errors' => $validator->errors()], 422);
        }
 
        $request->update([
            'status'        => 'rejected',
            'approved_by'   => auth()->id(),
            'approved_at'   => now(),
            'reject_reason' => $httpRequest->reject_reason,
        ]);
 
        return response()->json(['success' => true, 'message' => 'Permintaan ditolak.', 'data' => $request->fresh()]);
    }
 
    // Admin: complete
    public function complete(StockRequest $request): JsonResponse
    {
        if (! $request->isApproved()) {
            return response()->json(['success' => false, 'message' => 'Hanya permintaan approved yang dapat diselesaikan.'], 422);
        }
 
        $request->update(['status' => 'completed', 'completed_at' => now()]);
 
        return response()->json(['success' => true, 'message' => 'Permintaan selesai.', 'data' => $request->fresh()]);
    }
}

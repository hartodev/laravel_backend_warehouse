<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StockTransferController extends Controller
{
 public function index(Request $request): JsonResponse
    {
        $transfers = StockTransfer::with(['fromWarehouse:id,name,code', 'toWarehouse:id,name,code', 'requestedBy:id,name'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->warehouse_id, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('from_warehouse_id', $request->warehouse_id)
                  ->orWhere('to_warehouse_id', $request->warehouse_id);
            }))
            ->latest()
            ->paginate($request->per_page ?? 15);
 
        return response()->json(['success' => true, 'data' => $transfers]);
    }
 
    public function show(StockTransfer $transfer): JsonResponse
    {
        $transfer->load(['fromWarehouse:id,name,code', 'toWarehouse:id,name,code', 'requestedBy:id,name', 'approvedBy:id,name', 'receivedBy:id,name', 'items.product:id,name,sku,unit']);
 
        return response()->json(['success' => true, 'data' => $transfer]);
    }
 
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'from_warehouse_id'         => 'required|exists:warehouses,id',
            'to_warehouse_id'           => 'required|exists:warehouses,id|different:from_warehouse_id',
            'transfer_date'             => 'required|date',
            'expected_arrival'          => 'nullable|date|after_or_equal:transfer_date',
            'notes'                     => 'nullable|string',
            'items'                     => 'required|array|min:1',
            'items.*.product_id'        => 'required|exists:products,id',
            'items.*.quantity_requested'=> 'required|integer|min:1',
        ]);
 
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }
 
        DB::transaction(function () use ($request, &$transfer) {
            $count  = StockTransfer::whereYear('created_at', now()->year)->count() + 1;
            $number = 'TRF/' . now()->format('Y') . '/' . str_pad($count, 4, '0', STR_PAD_LEFT);
 
            $transfer = StockTransfer::create([
                'transfer_number'   => $number,
                'from_warehouse_id' => $request->from_warehouse_id,
                'to_warehouse_id'   => $request->to_warehouse_id,
                'requested_by'      => auth()->id(),
                'status'            => 'pending',
                'transfer_date'     => $request->transfer_date,
                'expected_arrival'  => $request->expected_arrival,
                'notes'             => $request->notes,
            ]);
 
            foreach ($request->items as $item) {
                StockTransferItem::create([
                    'stock_transfer_id'  => $transfer->id,
                    'product_id'         => $item['product_id'],
                    'quantity_requested' => $item['quantity_requested'],
                    'quantity_sent'      => 0,
                    'quantity_received'  => 0,
                ]);
            }
        });
 
        return response()->json(['success' => true, 'message' => 'Transfer stok berhasil dibuat.', 'data' => $transfer->load(['items.product:id,name,sku'])], 201);
    }
 
    public function update(Request $request, StockTransfer $transfer): JsonResponse
    {
        if ($transfer->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Transfer yang sudah diproses tidak dapat diubah.'], 422);
        }
 
        $transfer->update($request->only('transfer_date', 'expected_arrival', 'notes'));
 
        return response()->json(['success' => true, 'message' => 'Transfer berhasil diupdate.', 'data' => $transfer->fresh()]);
    }
 
    // POST approve
    public function approve(StockTransfer $transfer): JsonResponse
    {
        if ($transfer->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Hanya transfer pending yang dapat disetujui.'], 422);
        }
 
        $transfer->update(['status' => 'approved', 'approved_by' => auth()->id(), 'approved_at' => now()]);
 
        return response()->json(['success' => true, 'message' => 'Transfer disetujui.', 'data' => $transfer->fresh()]);
    }
 
    // POST reject
    public function reject(Request $request, StockTransfer $transfer): JsonResponse
    {
        if ($transfer->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Hanya transfer pending yang dapat ditolak.'], 422);
        }
 
        $validator = Validator::make($request->all(), ['reject_reason' => 'required|string']);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Alasan penolakan wajib diisi.', 'errors' => $validator->errors()], 422);
        }
 
        $transfer->update(['status' => 'rejected', 'reject_reason' => $request->reject_reason]);
 
        return response()->json(['success' => true, 'message' => 'Transfer ditolak.', 'data' => $transfer->fresh()]);
    }
 
    // POST send — kirim barang dari gudang asal
    public function send(Request $request, StockTransfer $transfer): JsonResponse
    {
        if ($transfer->status !== 'approved') {
            return response()->json(['success' => false, 'message' => 'Hanya transfer yang sudah disetujui yang dapat dikirim.'], 422);
        }
 
        $validator = Validator::make($request->all(), [
            'items'                           => 'required|array|min:1',
            'items.*.stock_transfer_item_id'  => 'required|exists:stock_transfer_items,id',
            'items.*.quantity_sent'           => 'required|integer|min:1',
        ]);
 
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }
 
        DB::transaction(function () use ($request, $transfer) {
            foreach ($request->items as $item) {
                $transferItem = StockTransferItem::find($item['stock_transfer_item_id']);
                if (! $transferItem || $transferItem->stock_transfer_id !== $transfer->id) continue;
 
                $qtySent = min($item['quantity_sent'], $transferItem->quantity_requested);
 
                // Kurangi stok gudang asal
                $stock = Stock::where('warehouse_id', $transfer->from_warehouse_id)
                               ->where('product_id', $transferItem->product_id)
                               ->firstOrFail();
 
                $before = $stock->quantity;
                $stock->reduceStock($qtySent);
 
                StockMovement::create([
                    'product_id'        => $transferItem->product_id,
                    'warehouse_id'      => $transfer->from_warehouse_id,
                    'type'              => 'transfer',
                    'quantity'          => $qtySent,
                    'quantity_before'   => $before,
                    'quantity_after'    => $stock->quantity,
                    'stock_transfer_id' => $transfer->id,
                    'created_by'        => auth()->id(),
                    'note'              => "Pengiriman transfer #{$transfer->transfer_number}",
                ]);
 
                $transferItem->update(['quantity_sent' => $qtySent]);
            }
 
            $transfer->update(['status' => 'in_transit']);
        });
 
        return response()->json(['success' => true, 'message' => 'Barang berhasil dikirim.', 'data' => $transfer->fresh()->load('items.product:id,name,sku')]);
    }
 
    // POST receive — terima barang di gudang tujuan
    public function receive(Request $request, StockTransfer $transfer): JsonResponse
    {
        if ($transfer->status !== 'in_transit') {
            return response()->json(['success' => false, 'message' => 'Hanya transfer in_transit yang dapat diterima.'], 422);
        }
 
        $validator = Validator::make($request->all(), [
            'items'                          => 'required|array|min:1',
            'items.*.stock_transfer_item_id' => 'required|exists:stock_transfer_items,id',
            'items.*.quantity_received'      => 'required|integer|min:1',
        ]);
 
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }
 
        DB::transaction(function () use ($request, $transfer) {
            foreach ($request->items as $item) {
                $transferItem = StockTransferItem::find($item['stock_transfer_item_id']);
                if (! $transferItem || $transferItem->stock_transfer_id !== $transfer->id) continue;
 
                $qtyReceived = min($item['quantity_received'], $transferItem->quantity_sent);
 
                // Tambah stok gudang tujuan
                $stock = Stock::firstOrCreate(
                    ['warehouse_id' => $transfer->to_warehouse_id, 'product_id' => $transferItem->product_id],
                    ['quantity' => 0]
                );
 
                $before = $stock->quantity;
                $stock->addStock($qtyReceived);
 
                StockMovement::create([
                    'product_id'        => $transferItem->product_id,
                    'warehouse_id'      => $transfer->to_warehouse_id,
                    'type'              => 'transfer',
                    'quantity'          => $qtyReceived,
                    'quantity_before'   => $before,
                    'quantity_after'    => $stock->quantity,
                    'stock_transfer_id' => $transfer->id,
                    'created_by'        => auth()->id(),
                    'note'              => "Penerimaan transfer #{$transfer->transfer_number}",
                ]);
 
                $transferItem->update(['quantity_received' => $qtyReceived]);
            }
 
            $transfer->update([
                'status'      => 'received',
                'received_by' => auth()->id(),
                'received_at' => now(),
            ]);
        });
 
        return response()->json(['success' => true, 'message' => 'Barang berhasil diterima.', 'data' => $transfer->fresh()->load('items.product:id,name,sku')]);
    }
}

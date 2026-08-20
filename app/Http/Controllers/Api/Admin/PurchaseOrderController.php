<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PurchaseOrderController extends Controller
{
     public function index(Request $request): JsonResponse
    {
        $pos = PurchaseOrder::with(['supplier:id,name,code', 'warehouse:id,name,code', 'createdBy:id,name'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->supplier_id, fn($q) => $q->where('supplier_id', $request->supplier_id))
            ->when($request->warehouse_id, fn($q) => $q->where('warehouse_id', $request->warehouse_id))
            ->when($request->search, fn($q) => $q->where('po_number', 'like', "%{$request->search}%"))
            ->when($request->date_from, fn($q) => $q->whereDate('order_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('order_date', '<=', $request->date_to))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json(['success' => true, 'data' => $pos]);
    }

    public function show(PurchaseOrder $po): JsonResponse
    {
        $po->load(['supplier', 'warehouse:id,name,code', 'createdBy:id,name', 'approvedBy:id,name', 'items.product:id,name,sku,unit']);

        return response()->json(['success' => true, 'data' => $po]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'supplier_id'           => 'required|exists:suppliers,id',
            'warehouse_id'          => 'required|exists:warehouses,id',
            'order_date'            => 'required|date',
            'expected_date'         => 'nullable|date|after_or_equal:order_date',
            'payment_term'          => 'nullable|string|max:100',
            'tax_percent'           => 'nullable|numeric|min:0|max:100',
            'discount_amount'       => 'nullable|numeric|min:0',
            'notes'                 => 'nullable|string',
            'items'                 => 'required|array|min:1',
            'items.*.product_id'    => 'required|exists:products,id',
            'items.*.quantity_ordered' => 'required|integer|min:1',
            'items.*.unit_price'    => 'required|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        DB::transaction(function () use ($request, &$po) {
            // Generate PO number
            $count    = PurchaseOrder::whereYear('created_at', now()->year)->count() + 1;
            $poNumber = 'PO/' . now()->format('Y') . '/' . str_pad($count, 4, '0', STR_PAD_LEFT);

            // Hitung subtotal
            $subtotal = 0;
            foreach ($request->items as $item) {
                $discount  = $item['discount_percent'] ?? 0;
                $subtotal += $item['quantity_ordered'] * $item['unit_price'] * (1 - $discount / 100);
            }

            $taxPercent     = $request->tax_percent ?? 0;
            $taxAmount      = $subtotal * ($taxPercent / 100);
            $discountAmount = $request->discount_amount ?? 0;
            $totalAmount    = $subtotal + $taxAmount - $discountAmount;

            $po = PurchaseOrder::create([
                'po_number'       => $poNumber,
                'supplier_id'     => $request->supplier_id,
                'warehouse_id'    => $request->warehouse_id,
                'created_by'      => auth()->id(),
                'status'          => 'pending',
                'order_date'      => $request->order_date,
                'expected_date'   => $request->expected_date,
                'subtotal'        => $subtotal,
                'tax_percent'     => $taxPercent,
                'tax_amount'      => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount'    => $totalAmount,
                'payment_term'    => $request->payment_term,
                'notes'           => $request->notes,
            ]);

            foreach ($request->items as $item) {
                $discount    = $item['discount_percent'] ?? 0;
                $itemSubtotal = $item['quantity_ordered'] * $item['unit_price'] * (1 - $discount / 100);

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id'        => $item['product_id'],
                    'quantity_ordered'  => $item['quantity_ordered'],
                    'quantity_received' => 0,
                    'unit_price'        => $item['unit_price'],
                    'discount_percent'  => $discount,
                    'subtotal'          => $itemSubtotal,
                ]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Purchase Order berhasil dibuat.', 'data' => $po->load(['items.product:id,name,sku', 'supplier:id,name'])], 201);
    }

    public function update(Request $request, PurchaseOrder $po): JsonResponse
    {
        if (! in_array($po->status, ['draft', 'pending'])) {
            return response()->json(['success' => false, 'message' => 'PO yang sudah diproses tidak dapat diubah.'], 422);
        }

        $po->update($request->only('expected_date', 'payment_term', 'notes', 'tax_percent', 'discount_amount'));

        return response()->json(['success' => true, 'message' => 'Purchase Order berhasil diupdate.', 'data' => $po->fresh()]);
    }

    public function destroy(PurchaseOrder $po): JsonResponse
    {
        if (! in_array($po->status, ['draft', 'pending'])) {
            return response()->json(['success' => false, 'message' => 'Hanya PO draft/pending yang dapat dihapus.'], 422);
        }

        $po->items()->delete();
        $po->delete();

        return response()->json(['success' => true, 'message' => 'Purchase Order berhasil dihapus.']);
    }

    // POST /api/purchase-orders/{po}/approve
    public function approve(PurchaseOrder $po): JsonResponse
    {
        if ($po->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Hanya PO pending yang dapat disetujui.'], 422);
        }

        $po->update(['status' => 'approved', 'approved_by' => auth()->id(), 'approved_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Purchase Order disetujui.', 'data' => $po->fresh()]);
    }

    // POST /api/purchase-orders/{po}/reject
    public function reject(Request $request, PurchaseOrder $po): JsonResponse
    {
        if ($po->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Hanya PO pending yang dapat ditolak.'], 422);
        }

        $validator = Validator::make($request->all(), [
            'reject_reason' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Alasan penolakan wajib diisi.', 'errors' => $validator->errors()], 422);
        }

        $po->update(['status' => 'cancelled', 'reject_reason' => $request->reject_reason]);

        return response()->json(['success' => true, 'message' => 'Purchase Order ditolak.', 'data' => $po->fresh()]);
    }

    // POST /api/purchase-orders/{po}/receive — terima barang
    public function receive(Request $request, PurchaseOrder $po): JsonResponse
    {
        if ($po->status !== 'approved') {
            return response()->json(['success' => false, 'message' => 'Hanya PO yang sudah disetujui yang dapat diterima.'], 422);
        }

        $validator = Validator::make($request->all(), [
            'items'                         => 'required|array|min:1',
            'items.*.purchase_order_item_id'=> 'required|exists:purchase_order_items,id',
            'items.*.quantity_received'     => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        DB::transaction(function () use ($request, $po) {
            foreach ($request->items as $item) {
                $poItem = PurchaseOrderItem::find($item['purchase_order_item_id']);
                if (! $poItem || $poItem->purchase_order_id !== $po->id) continue;

                $qtyReceived = min($item['quantity_received'], $poItem->remainingQty());
                if ($qtyReceived <= 0) continue;

                $poItem->increment('quantity_received', $qtyReceived);

                // Update stok
                $stock = Stock::firstOrCreate(
                    ['warehouse_id' => $po->warehouse_id, 'product_id' => $poItem->product_id],
                    ['quantity' => 0]
                );

                $before = $stock->quantity;
                $stock->addStock($qtyReceived);

                StockMovement::create([
                    'product_id'       => $poItem->product_id,
                    'warehouse_id'     => $po->warehouse_id,
                    'type'             => 'in',
                    'quantity'         => $qtyReceived,
                    'quantity_before'  => $before,
                    'quantity_after'   => $stock->quantity,
                    'reference_type'  => 'purchase_order',  // ← ganti
                    'reference_id'    => $po->id,
                    'created_by'       => auth()->id(),
                    'note'             => "Penerimaan barang PO #{$po->po_number}",
                ]);
            }

            // Update status PO
            $allReceived = $po->items()->whereColumn('quantity_received', '<', 'quantity_ordered')->doesntExist();
            $po->update([
                'status'        => $allReceived ? 'received' : 'partial',
                'received_date' => $allReceived ? now() : null,
            ]);
        });

        return response()->json(['success' => true, 'message' => 'Penerimaan barang berhasil dicatat.', 'data' => $po->fresh()->load('items.product:id,name,sku')]);
    }
}

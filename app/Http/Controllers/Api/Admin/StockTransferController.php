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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class StockTransferController extends Controller
{
    private function isSuperadmin($user): bool
    {
        return in_array($user->role, ['superadmin', 'super_admin']);
    }

    public function index(Request $request): JsonResponse
    {
        $transfers = StockTransfer::with([
            'fromWarehouse:id,name,code',
            'toWarehouse:id,name,code',
            'requestedBy:id,name',
            'items.product:id,name,sku,unit',
        ])
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
        $transfer->load([
            'fromWarehouse:id,name,code',
            'toWarehouse:id,name,code',
            'requestedBy:id,name',
            'confirmedBy:id,name',
            'approvedBy:id,name',
            'cancelledBy:id,name',
            'receivedBy:id,name',
            'discrepancyReportedBy:id,name',
            'resolvedBy:id,name',
            'items.product:id,name,sku,unit',
        ]);

        return response()->json(['success' => true, 'data' => $transfer]);
    }

    // ── 1. STORE — Admin Gudang A buat request ──────────────────────────────
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();

        if ($this->isSuperadmin($user)) {
            return response()->json(['success' => false, 'message' => 'Superadmin tidak membuat request transfer.'], 403);
        }
        if ((int) $user->warehouse_id !== (int) $request->from_warehouse_id) {
            return response()->json(['success' => false, 'message' => 'Gudang asal harus sesuai warehouse Anda.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'from_warehouse_id'          => 'required|exists:warehouses,id',
            'to_warehouse_id'            => 'required|exists:warehouses,id|different:from_warehouse_id',
            'transfer_date'              => 'required|date',
            'expected_arrival'           => 'nullable|date|after_or_equal:transfer_date',
            'notes'                      => 'nullable|string',
            'items'                      => 'required|array|min:1',
            'items.*.product_id'         => 'required|exists:products,id',
            'items.*.quantity_requested' => 'required|integer|min:1',
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
                'status'            => 'pending_confirmation',
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

        return response()->json(['success' => true, 'message' => 'Request transfer dibuat, silakan konfirmasi untuk lanjut.', 'data' => $transfer->load('items.product:id,name,sku')], 201);
    }

    // ── 2a. CONFIRM — Admin Gudang A lanjutkan ke approval superadmin ───────
    public function confirm(StockTransfer $transfer): JsonResponse
    {
        $user = auth()->user();

        if ($transfer->status !== 'pending_confirmation') {
            return response()->json(['success' => false, 'message' => 'Hanya transfer pending_confirmation yang bisa dikonfirmasi.'], 422);
        }
        if ((int) $transfer->requested_by !== (int) $user->id) {
            return response()->json(['success' => false, 'message' => 'Hanya pembuat request yang bisa konfirmasi.'], 403);
        }

        $transfer->update([
            'status'       => 'pending_approval',
            'confirmed_by' => $user->id,
            'confirmed_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Transfer dikonfirmasi, menunggu approval superadmin.', 'data' => $transfer->fresh()]);
    }

    // ── 2b. CANCEL — Admin Gudang A batal (wajib alasan) ─────────────────────
    public function cancel(Request $request, StockTransfer $transfer): JsonResponse
    {
        $user = auth()->user();

        if ($transfer->status !== 'pending_confirmation') {
            return response()->json(['success' => false, 'message' => 'Hanya transfer pending_confirmation yang bisa dibatalkan di tahap ini.'], 422);
        }
        if ((int) $transfer->requested_by !== (int) $user->id) {
            return response()->json(['success' => false, 'message' => 'Hanya pembuat request yang bisa membatalkan.'], 403);
        }

        $validator = Validator::make($request->all(), ['cancel_reason' => 'required|string']);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Alasan pembatalan wajib diisi.', 'errors' => $validator->errors()], 422);
        }

        $transfer->update([
            'status'        => 'cancelled',
            'cancelled_by'  => $user->id,
            'cancelled_at'  => now(),
            'cancel_reason' => $request->cancel_reason,
        ]);

        return response()->json(['success' => true, 'message' => 'Transfer dibatalkan.', 'data' => $transfer->fresh()]);
    }

    // ── 3a. APPROVE — Superadmin ──────────────────────────────────────────
    public function approve(StockTransfer $transfer): JsonResponse
    {
        if (!$this->isSuperadmin(auth()->user())) {
            return response()->json(['success' => false, 'message' => 'Hanya superadmin yang bisa approve.'], 403);
        }
        if ($transfer->status !== 'pending_approval') {
            return response()->json(['success' => false, 'message' => 'Hanya transfer pending_approval yang dapat disetujui.'], 422);
        }

        $transfer->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Transfer disetujui.', 'data' => $transfer->fresh()]);
    }

    // ── 3b. REJECT — Superadmin (wajib alasan) ───────────────────────────
    public function reject(Request $request, StockTransfer $transfer): JsonResponse
    {
        if (!$this->isSuperadmin(auth()->user())) {
            return response()->json(['success' => false, 'message' => 'Hanya superadmin yang bisa reject.'], 403);
        }
        if ($transfer->status !== 'pending_approval') {
            return response()->json(['success' => false, 'message' => 'Hanya transfer pending_approval yang dapat ditolak.'], 422);
        }

        $validator = Validator::make($request->all(), ['reject_reason' => 'required|string']);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Alasan penolakan wajib diisi.', 'errors' => $validator->errors()], 422);
        }

        $transfer->update(['status' => 'rejected', 'reject_reason' => $request->reject_reason]);

        return response()->json(['success' => true, 'message' => 'Transfer ditolak.', 'data' => $transfer->fresh()]);
    }

    // ── 4. SEND — Admin Gudang A kirim barang + lampiran wajib ──────────────
    public function send(Request $request, StockTransfer $transfer): JsonResponse
    {
        $user = auth()->user();

        if ($transfer->status !== 'approved') {
            return response()->json(['success' => false, 'message' => 'Hanya transfer yang sudah disetujui yang dapat dikirim.'], 422);
        }
        if ((int) $user->warehouse_id !== (int) $transfer->from_warehouse_id) {
            return response()->json(['success' => false, 'message' => 'Hanya admin gudang asal yang bisa mengirim.'], 403);
        }

        // ── Decode 'items' dari string JSON (multipart/form-data selalu kirim string) ──
        $items = $request->input('items');
        if (is_string($items)) {
            $items = json_decode($items, true) ?? [];
        }

        $validator = Validator::make(
            array_merge($request->all(), ['items' => $items]),
            [
                'items'                           => 'required|array|min:1',
                'items.*.stock_transfer_item_id'  => 'required|exists:stock_transfer_items,id',
                'items.*.quantity_sent'           => 'required|integer|min:1',
                'attachment'                      => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        $path = $request->file('attachment')->store('transfer-shipments', 'public');

        try {
            DB::transaction(function () use ($items, $transfer, $user, $path) {
                foreach ($items as $item) {
                    $transferItem = StockTransferItem::find($item['stock_transfer_item_id']);
                    if (! $transferItem || $transferItem->stock_transfer_id !== $transfer->id) continue;

                    $qtySent = min($item['quantity_sent'], $transferItem->quantity_requested);

                    $stock = Stock::where('warehouse_id', $transfer->from_warehouse_id)
                        ->where('product_id', $transferItem->product_id)
                        ->first();

                    if (! $stock) {
                        throw new \RuntimeException(
                            "Stok untuk produk \"{$transferItem->product->name}\" tidak ditemukan di gudang asal."
                        );
                    }

                    if ($stock->quantity < $qtySent) {
                        throw new \RuntimeException(
                            "Stok \"{$transferItem->product->name}\" tidak cukup. Tersedia: {$stock->quantity}, diminta kirim: {$qtySent}."
                        );
                    }

                    $before = $stock->quantity;
                    $stock->reduceStock($qtySent);

                    StockMovement::create([
                        'product_id'        => $transferItem->product_id,
                        'warehouse_id'      => $transfer->from_warehouse_id,
                        'type'              => 'transfer_out',
                        'quantity'          => $qtySent,
                        'quantity_before'   => $before,
                        'quantity_after'    => $stock->quantity,
                        'reference_type'    => 'stock_transfer',
                        'reference_id'      => $transfer->id,
                        'created_by'        => $user->id,
                        'note'              => "Pengiriman transfer #{$transfer->transfer_number}",
                    ]);

                    $transferItem->update(['quantity_sent' => $qtySent]);
                }

                $transfer->update([
                    'status'               => 'in_transit',
                    'sent_at'              => now(),
                    'sent_by'              => $user->id,
                    'shipment_attachment'  => $path,
                ]);
            });
        } catch (\RuntimeException $e) {
            // Hapus file attachment yang sudah kepalang diupload, karena transaksi gagal / dibatalkan
            Storage::disk('public')->delete($path);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true, 'message' => 'Barang berhasil dikirim.', 'data' => $transfer->fresh()->load('items.product:id,name,sku')]);
    }
    // ── 5. CHECKLIST — Admin Gudang B validasi penerimaan ────────────────────
    public function checklist(Request $request, StockTransfer $transfer): JsonResponse
    {
        $user = auth()->user();

        if ($transfer->status !== 'in_transit') {
            return response()->json(['success' => false, 'message' => 'Hanya transfer in_transit yang dapat divalidasi.'], 422);
        }
        if ((int) $user->warehouse_id !== (int) $transfer->to_warehouse_id) {
            return response()->json(['success' => false, 'message' => 'Hanya admin gudang tujuan yang bisa checklist.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'items'                          => 'required|array|min:1',
            'items.*.stock_transfer_item_id' => 'required|exists:stock_transfer_items,id',
            'items.*.quantity_received'      => 'required|integer|min:0',
            'discrepancy_notes'              => 'required_if:has_discrepancy,true|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        $hasDiscrepancy = false;

        DB::transaction(function () use ($request, $transfer, $user, &$hasDiscrepancy) {
            foreach ($request->items as $item) {
                $transferItem = StockTransferItem::find($item['stock_transfer_item_id']);
                if (! $transferItem || $transferItem->stock_transfer_id !== $transfer->id) continue;

                $qtyReceived = $item['quantity_received'];
                $isMatched   = $qtyReceived === $transferItem->quantity_sent;
                if (!$isMatched) $hasDiscrepancy = true;

                $stock = Stock::firstOrCreate(
                    ['warehouse_id' => $transfer->to_warehouse_id, 'product_id' => $transferItem->product_id],
                    ['quantity' => 0]
                );

                $before = $stock->quantity;
                if ($qtyReceived > 0) $stock->addStock($qtyReceived);

                StockMovement::create([
                    'product_id'      => $transferItem->product_id,
                    'warehouse_id'    => $transfer->to_warehouse_id,
                    'type'            => 'transfer_in',
                    'quantity'        => $qtyReceived,
                    'quantity_before' => $before,
                    'quantity_after'  => $stock->quantity,
                    'reference_type'  => 'stock_transfer',
                    'reference_id'    => $transfer->id,
                    'created_by'      => $user->id,
                    'note'            => "Penerimaan transfer #{$transfer->transfer_number}",
                ]);

                $transferItem->update([
                    'quantity_received' => $qtyReceived,
                    'is_matched'        => $isMatched,
                ]);
            }

            if ($hasDiscrepancy) {
                $transfer->update([
                    'status'                   => 'discrepancy',
                    'discrepancy_notes'        => $request->discrepancy_notes,
                    'discrepancy_reported_by'  => $user->id,
                    'discrepancy_reported_at'  => now(),
                ]);
            } else {
                $transfer->update([
                    'status'      => 'received',
                    'received_by' => $user->id,
                    'received_at' => now(),
                ]);
            }
        });

        $msg = $hasDiscrepancy
            ? 'Ada selisih barang, menunggu resolusi superadmin.'
            : 'Barang diterima sesuai, transfer selesai.';

        return response()->json(['success' => true, 'message' => $msg, 'data' => $transfer->fresh()->load('items.product:id,name,sku')]);
    }

    // ── 6. RESOLVE DISCREPANCY — Superadmin ─────────────────────────────────
    public function resolveDiscrepancy(Request $request, StockTransfer $transfer): JsonResponse
    {
        if (!$this->isSuperadmin(auth()->user())) {
            return response()->json(['success' => false, 'message' => 'Hanya superadmin yang bisa resolve.'], 403);
        }
        if ($transfer->status !== 'discrepancy') {
            return response()->json(['success' => false, 'message' => 'Hanya transfer berstatus discrepancy yang bisa diresolusi.'], 422);
        }

        $validator = Validator::make($request->all(), [
            'resolution' => 'required|in:accept,cancel',
            'notes'      => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        $newStatus = $request->resolution === 'accept' ? 'received' : 'cancelled';

        $transfer->update([
            'status'           => $newStatus,
            'resolved_by'      => auth()->id(),
            'resolved_at'      => now(),
            'resolution_notes' => $request->notes,
        ]);

        return response()->json(['success' => true, 'message' => 'Discrepancy diresolusi.', 'data' => $transfer->fresh()]);
    }
}
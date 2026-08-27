<?php

namespace App\Services;

use App\Exceptions\StockTransferException;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Business logic StockTransfer untuk panel Web\Admin (BARU).
 *
 * CATATAN: Controller Api\Admin\StockTransferController dan
 * Web\Superadmin\StockTransferController yang SUDAH ADA sebelumnya
 * TIDAK diubah dan TIDAK memakai service ini — mereka tetap
 * menyimpan logic-nya sendiri persis seperti kode aslinya.
 *
 * Aturan di sini (status, syarat gudang, dst) sudah disamakan MANUAL
 * dengan Api\Admin\StockTransferController per hari ini. Kalau nanti
 * kamu ubah aturan di Api\Admin atau Web\Superadmin, file ini TIDAK
 * ikut berubah otomatis — perlu di-update manual juga di sini supaya
 * ketiganya tetap konsisten.
 */
class StockTransferService
{
    public function isSuperadmin(User $user): bool
    {
        return in_array($user->role, ['superadmin', 'super_admin']);
    }

    /**
     * 1. STORE — Admin Gudang A membuat request transfer.
     * Status awal: pending_confirmation (masih perlu dikonfirmasi ulang oleh pembuatnya).
     */
    public function createRequest(array $data, User $user): StockTransfer
    {
        if ($this->isSuperadmin($user)) {
            throw new StockTransferException('Superadmin tidak membuat request transfer.', 403);
        }
        if ((int) $user->warehouse_id !== (int) $data['from_warehouse_id']) {
            throw new StockTransferException('Gudang asal harus sesuai warehouse Anda.', 403);
        }

        return DB::transaction(function () use ($data, $user) {
            $transfer = StockTransfer::create([
                'transfer_number'   => $this->generateNumber(),
                'from_warehouse_id' => $data['from_warehouse_id'],
                'to_warehouse_id'   => $data['to_warehouse_id'],
                'requested_by'      => $user->id,
                'status'            => 'pending_confirmation',
                'transfer_date'     => $data['transfer_date'],
                'expected_arrival'  => $data['expected_arrival'] ?? null,
                'notes'             => $data['notes'] ?? null,
            ]);

            $this->createItems($transfer, $data['items']);

            return $transfer->load('items.product:id,name,sku');
        });
    }

    /**
     * Superadmin membuat transfer manual (jarang dipakai, langsung skip ke
     * pending_approval karena yang mengajukan sudah superadmin sendiri).
     */
    public function createManualBySuperadmin(array $data, User $user): StockTransfer
    {
        if (! $this->isSuperadmin($user)) {
            throw new StockTransferException('Hanya superadmin yang bisa membuat transfer manual.', 403);
        }

        return DB::transaction(function () use ($data, $user) {
            $transfer = StockTransfer::create([
                'transfer_number'   => $this->generateNumber(),
                'from_warehouse_id' => $data['from_warehouse_id'],
                'to_warehouse_id'   => $data['to_warehouse_id'],
                'requested_by'      => $user->id,
                'status'            => 'pending_approval',
                'transfer_date'     => $data['transfer_date'],
                'expected_arrival'  => $data['expected_arrival'] ?? null,
                'notes'             => $data['notes'] ?? null,
            ]);

            $this->createItems($transfer, $data['items']);

            return $transfer->load('items.product:id,name,sku');
        });
    }

    /**
     * 2a. CONFIRM — Admin Gudang A lanjutkan ke approval superadmin.
     */
    public function confirm(StockTransfer $transfer, User $user): StockTransfer
    {
        if ($transfer->status !== 'pending_confirmation') {
            throw new StockTransferException('Hanya transfer pending_confirmation yang bisa dikonfirmasi.');
        }
        if ((int) $transfer->requested_by !== (int) $user->id) {
            throw new StockTransferException('Hanya pembuat request yang bisa konfirmasi.', 403);
        }

        $transfer->update([
            'status'       => 'pending_approval',
            'confirmed_by' => $user->id,
            'confirmed_at' => now(),
        ]);

        return $transfer->fresh();
    }

    /**
     * 2b. CANCEL — Admin Gudang A batal (wajib alasan).
     */
    public function cancel(StockTransfer $transfer, User $user, string $reason): StockTransfer
    {
        if ($transfer->status !== 'pending_confirmation') {
            throw new StockTransferException('Hanya transfer pending_confirmation yang bisa dibatalkan di tahap ini.');
        }
        if ((int) $transfer->requested_by !== (int) $user->id) {
            throw new StockTransferException('Hanya pembuat request yang bisa membatalkan.', 403);
        }

        $transfer->update([
            'status'        => 'cancelled',
            'cancelled_by'  => $user->id,
            'cancelled_at'  => now(),
            'cancel_reason' => $reason,
        ]);

        return $transfer->fresh();
    }

    /**
     * 3a. APPROVE — Superadmin.
     */
    public function approve(StockTransfer $transfer, User $user): StockTransfer
    {
        if (! $this->isSuperadmin($user)) {
            throw new StockTransferException('Hanya superadmin yang bisa approve.', 403);
        }
        if ($transfer->status !== 'pending_approval') {
            throw new StockTransferException('Hanya transfer pending_approval yang dapat disetujui.');
        }

        $transfer->update([
            'status'      => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return $transfer->fresh();
    }

    /**
     * 3b. REJECT — Superadmin (wajib alasan).
     */
    public function reject(StockTransfer $transfer, User $user, string $reason): StockTransfer
    {
        if (! $this->isSuperadmin($user)) {
            throw new StockTransferException('Hanya superadmin yang bisa reject.', 403);
        }
        if ($transfer->status !== 'pending_approval') {
            throw new StockTransferException('Hanya transfer pending_approval yang dapat ditolak.');
        }

        $transfer->update(['status' => 'rejected', 'reject_reason' => $reason]);

        return $transfer->fresh();
    }

    /**
     * 4. SEND — Admin Gudang A kirim barang + lampiran wajib.
     *
     * @param array $items [['stock_transfer_item_id' => int, 'quantity_sent' => int], ...]
     * @param string $attachmentPath Path file yang SUDAH di-store oleh controller
     *                               (upload dilakukan di controller karena beda
     *                               antara request()->file() di API vs Web).
     */
    public function send(StockTransfer $transfer, User $user, array $items, string $attachmentPath): StockTransfer
    {
        if ($transfer->status !== 'approved') {
            throw new StockTransferException('Hanya transfer yang sudah disetujui yang dapat dikirim.');
        }
        if ((int) $user->warehouse_id !== (int) $transfer->from_warehouse_id) {
            throw new StockTransferException('Hanya admin gudang asal yang bisa mengirim.', 403);
        }

        try {
            DB::transaction(function () use ($items, $transfer, $user, $attachmentPath) {
                foreach ($items as $item) {
                    $transferItem = StockTransferItem::find($item['stock_transfer_item_id']);
                    if (! $transferItem || $transferItem->stock_transfer_id !== $transfer->id) {
                        continue;
                    }

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
                        'product_id'      => $transferItem->product_id,
                        'warehouse_id'    => $transfer->from_warehouse_id,
                        'type'            => 'transfer_out',
                        'quantity'        => $qtySent,
                        'quantity_before' => $before,
                        'quantity_after'  => $stock->quantity,
                        'reference_type'  => 'stock_transfer',
                        'reference_id'    => $transfer->id,
                        'created_by'      => $user->id,
                        'note'            => "Pengiriman transfer #{$transfer->transfer_number}",
                    ]);

                    $transferItem->update(['quantity_sent' => $qtySent]);
                }

                $transfer->update([
                    'status'              => 'in_transit',
                    'sent_at'             => now(),
                    'sent_by'             => $user->id,
                    'shipment_attachment' => $attachmentPath,
                ]);
            });
        } catch (\RuntimeException $e) {
            // Transaksi batal → file yang kepalang terupload dibersihkan.
            Storage::disk('public')->delete($attachmentPath);
            throw new StockTransferException($e->getMessage());
        }

        return $transfer->fresh()->load('items.product:id,name,sku');
    }

    /**
     * 5. CHECKLIST — Admin Gudang B validasi penerimaan.
     *
     * @return array{transfer: StockTransfer, has_discrepancy: bool}
     */
    public function checklist(StockTransfer $transfer, User $user, array $items, ?string $discrepancyNotes): array
    {
        if ($transfer->status !== 'in_transit') {
            throw new StockTransferException('Hanya transfer in_transit yang dapat divalidasi.');
        }
        if ((int) $user->warehouse_id !== (int) $transfer->to_warehouse_id) {
            throw new StockTransferException('Hanya admin gudang tujuan yang bisa checklist.', 403);
        }

        $hasDiscrepancy = false;

        DB::transaction(function () use ($items, $transfer, $user, $discrepancyNotes, &$hasDiscrepancy) {
            foreach ($items as $item) {
                $transferItem = StockTransferItem::find($item['stock_transfer_item_id']);
                if (! $transferItem || $transferItem->stock_transfer_id !== $transfer->id) {
                    continue;
                }

                $qtyReceived = $item['quantity_received'];
                $isMatched   = $qtyReceived === $transferItem->quantity_sent;
                if (! $isMatched) {
                    $hasDiscrepancy = true;
                }

                $stock = Stock::firstOrCreate(
                    ['warehouse_id' => $transfer->to_warehouse_id, 'product_id' => $transferItem->product_id],
                    ['quantity' => 0]
                );

                $before = $stock->quantity;
                if ($qtyReceived > 0) {
                    $stock->addStock($qtyReceived);
                }

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
                    'status'                  => 'discrepancy',
                    'discrepancy_notes'       => $discrepancyNotes,
                    'discrepancy_reported_by' => $user->id,
                    'discrepancy_reported_at' => now(),
                ]);
            } else {
                $transfer->update([
                    'status'      => 'received',
                    'received_by' => $user->id,
                    'received_at' => now(),
                ]);
            }
        });

        return [
            'transfer'        => $transfer->fresh()->load('items.product:id,name,sku'),
            'has_discrepancy' => $hasDiscrepancy,
        ];
    }

    /**
     * 6. RESOLVE DISCREPANCY — Superadmin.
     */
    public function resolveDiscrepancy(StockTransfer $transfer, User $user, string $resolution, string $notes): StockTransfer
    {
        if (! $this->isSuperadmin($user)) {
            throw new StockTransferException('Hanya superadmin yang bisa resolve.', 403);
        }
        if ($transfer->status !== 'discrepancy') {
            throw new StockTransferException('Hanya transfer berstatus discrepancy yang bisa diresolusi.');
        }

        $newStatus = $resolution === 'accept' ? 'received' : 'cancelled';

        $transfer->update([
            'status'           => $newStatus,
            'resolved_by'      => $user->id,
            'resolved_at'      => now(),
            'resolution_notes' => $notes,
        ]);

        return $transfer->fresh();
    }

    private function generateNumber(): string
    {
        $count = StockTransfer::whereYear('created_at', now()->year)->count() + 1;
        return 'TRF/' . now()->format('Y') . '/' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }

    private function createItems(StockTransfer $transfer, array $items): void
    {
        foreach ($items as $item) {
            StockTransferItem::create([
                'stock_transfer_id'  => $transfer->id,
                'product_id'         => $item['product_id'],
                'quantity_requested' => $item['quantity_requested'],
                'quantity_sent'      => 0,
                'quantity_received'  => 0,
            ]);
        }
    }
}

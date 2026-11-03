<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\User;

/**
 * Aksi supplier atas PO yang sudah disetujui: konfirmasi pesanan dan tandai dikirim.
 * Dipakai bersama oleh API mobile dan portal web supplier.
 * Mengembalikan [bool sukses, string pesan].
 */
class SupplierPurchaseOrderService
{
    public static function confirm(PurchaseOrder $po, User $user, ?string $note = null): array
    {
        if ($po->status !== 'approved') {
            return [false, 'Hanya PO yang sudah disetujui yang dapat dikonfirmasi.'];
        }
        if ($po->supplier_confirmed_at) {
            return [false, 'PO ini sudah Anda konfirmasi.'];
        }

        $po->update([
            'supplier_confirmed_at' => now(),
            'supplier_confirmed_by' => $user->id,
            'supplier_note'         => $note ?: $po->supplier_note,
        ]);

        NotificationService::purchaseOrder($po->fresh(), 'supplier_confirmed', $user->id);

        return [true, 'PO dikonfirmasi. Admin telah diberi tahu.'];
    }

    public static function ship(PurchaseOrder $po, User $user, ?string $note = null, ?string $expectedDate = null): array
    {
        if (! in_array($po->status, ['approved', 'partial'])) {
            return [false, 'Hanya PO yang disetujui atau diterima sebagian yang dapat ditandai dikirim.'];
        }
        if (! $po->supplier_confirmed_at) {
            return [false, 'Konfirmasi PO terlebih dahulu sebelum menandai dikirim.'];
        }
        if ($po->status === 'approved' && $po->shipped_at) {
            return [false, 'PO ini sudah ditandai dikirim.'];
        }

        $data = [
            'shipped_at'    => now(),
            'shipped_by'    => $user->id,
            'supplier_note' => $note ?: $po->supplier_note,
        ];
        if ($expectedDate) {
            $data['expected_date'] = $expectedDate;
        }
        $po->update($data);

        NotificationService::purchaseOrder($po->fresh(), 'shipped', $user->id);

        return [true, 'PO ditandai sudah dikirim. Admin gudang telah diberi tahu.'];
    }
}

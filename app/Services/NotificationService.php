<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Kirim notifikasi ke satu user (simpan ke DB + nanti dirantai ke push FCM).
     */
    public static function send(User $user, string $type, string $title, string $body, array $data = []): Notification
    {
        $notification = Notification::create([
            'user_id' => $user->id,
            'type'    => $type,
            'title'   => $title,
            'body'    => $body,
            'data'    => $data,
        ]);

        // Push FCM (dikirim SETELAH response selesai, tidak memperlambat request,
        // dan kalau gagal tidak menggagalkan transaksi)
        self::sendPush($user, $title, $body, $data + [
            'action'          => 'notification',
            'type'            => $type,
            'notification_id' => $notification->id,
        ]);

        return $notification;
    }

    /** Kirim push FCM ke HP user (jika user punya fcm_token). */
    private static function sendPush(User $user, string $title, string $body, array $data): void
    {
        if (! $user->fcm_token) {
            return;
        }

        $userId = $user->id;

        dispatch(function () use ($userId, $title, $body, $data) {
            try {
                $target = User::find($userId);
                if ($target) {
                    app(FcmService::class)->sendToUser($target, $title, $body, $data);
                }
            } catch (\Throwable $e) {
                \Log::warning('Push FCM gagal: ' . $e->getMessage());
            }
        })->afterResponse();
    }

    /** Kirim ke banyak user sekaligus. */
    public static function sendToMany(iterable $users, string $type, string $title, string $body, array $data = []): void
    {
        foreach ($users as $user) {
            self::send($user, $type, $title, $body, $data);
        }
    }

    /** Kirim ke semua user dengan role tertentu (mis. semua admin). */
    public static function sendToRole(string $role, string $type, string $title, string $body, array $data = []): void
    {
        $users = User::where('role', $role)->where('is_active', true)->get();
        self::sendToMany($users, $type, $title, $body, $data);
    }

    // ── Helper alur bisnis (aman: gagal kirim tidak menggagalkan transaksi) ──

    /** Kirim ke semua user aktif dengan salah satu role, kecuali pelaku. */
    public static function notifyRoles(array $roles, string $type, string $title, string $body, array $data = [], ?int $exceptUserId = null): void
    {
        try {
            $users = User::whereIn('role', $roles)->where('is_active', true)
                ->when($exceptUserId, fn ($q) => $q->where('id', '!=', $exceptUserId))
                ->get();
            self::sendToMany($users, $type, $title, $body, $data);
        } catch (\Throwable $e) {
            \Log::warning('notifyRoles gagal: ' . $e->getMessage());
        }
    }

    /** Kirim ke satu user berdasarkan id (dilewati jika sama dengan pelaku). */
    public static function notifyUser(?int $userId, string $type, string $title, string $body, array $data = [], ?int $exceptUserId = null): void
    {
        if (! $userId || $userId === $exceptUserId) return;
        try {
            $user = User::find($userId);
            if ($user) self::send($user, $type, $title, $body, $data);
        } catch (\Throwable $e) {
            \Log::warning('notifyUser gagal: ' . $e->getMessage());
        }
    }

    /** Kirim ke semua akun portal supplier tertentu. */
    public static function notifySupplier(?int $supplierId, string $type, string $title, string $body, array $data = []): void
    {
        if (! $supplierId) return;
        try {
            $users = User::where('role', 'supplier')->where('supplier_id', $supplierId)->where('is_active', true)->get();
            self::sendToMany($users, $type, $title, $body, $data);
        } catch (\Throwable $e) {
            \Log::warning('notifySupplier gagal: ' . $e->getMessage());
        }
    }

    /** Notifikasi standar siklus PO ke pihak terkait. $event: created|approved|rejected|received|partial */
    public static function purchaseOrder(\App\Models\PurchaseOrder $po, string $event, ?int $actorId = null): void
    {
        $data  = ['purchase_order_id' => $po->id, 'po_number' => $po->po_number];
        $num   = $po->po_number;
        $type  = 'purchase_order';
        switch ($event) {
            case 'created':
                self::notifyRoles(['super_admin'], $type, 'PO Menunggu Persetujuan', "PO {$num} menunggu persetujuan Anda.", $data, $actorId);
                break;
            case 'approved':
                self::notifyUser($po->created_by, $type, 'PO Disetujui', "PO {$num} telah disetujui.", $data, $actorId);
                self::notifySupplier($po->supplier_id, $type, 'PO Baru Disetujui', "PO {$num} telah disetujui dan akan segera dikirim ke gudang.", $data);
                break;
            case 'rejected':
                self::notifyUser($po->created_by, $type, 'PO Ditolak', "PO {$num} ditolak. Alasan: " . ($po->reject_reason ?? '-'), $data, $actorId);
                self::notifySupplier($po->supplier_id, $type, 'PO Dibatalkan', "PO {$num} dibatalkan.", $data);
                break;
            case 'supplier_confirmed':
                self::notifyRoles(['super_admin'], $type, 'Supplier Konfirmasi PO', "Supplier mengonfirmasi PO {$num}.", $data, $actorId);
                self::notifyUser($po->created_by, $type, 'Supplier Konfirmasi PO', "Supplier mengonfirmasi PO {$num}.", $data, $actorId);
                break;
            case 'shipped':
                $body = "PO {$num} sudah dikirim supplier" . ($po->expected_date ? ' (estimasi tiba ' . $po->expected_date->format('d/m/Y') . ').' : '.');
                self::notifyRoles(['super_admin'], $type, 'PO Dalam Pengiriman', $body, $data, $actorId);
                self::notifyUser($po->created_by, $type, 'PO Dalam Pengiriman', $body, $data, $actorId);
                try {
                    User::whereIn('role', ['admin', 'warehouse_keeper'])->where('warehouse_id', $po->warehouse_id)->where('is_active', true)
                        ->where('id', '!=', $po->created_by ?? 0)
                        ->get()
                        ->each(fn ($u) => self::send($u, $type, 'Barang PO Segera Tiba', $body, $data));
                } catch (\Throwable $e) {
                    \Log::warning('Notif shipped gudang gagal: ' . $e->getMessage());
                }
                break;
            case 'received':
            case 'partial':
                $label = $event === 'received' ? 'diterima penuh' : 'diterima sebagian';
                self::notifyRoles(['super_admin'], $type, 'Barang PO Diterima', "Barang PO {$num} {$label}.", $data, $actorId);
                self::notifyUser($po->created_by, $type, 'Barang PO Diterima', "Barang PO {$num} {$label}.", $data, $actorId);
                self::notifySupplier($po->supplier_id, $type, 'Barang PO Diterima', "Barang PO {$num} {$label} di gudang.", $data);
                break;
        }
    }

    /** Notifikasi siklus pengajuan produk. $event: submitted|forwarded|approved|rejected */
    public static function productSubmission(\App\Models\ProductSubmission $sub, string $event, ?int $actorId = null): void
    {
        $data = ['product_submission_id' => $sub->id];
        $type = 'product_submission';
        $name = $sub->name ?? optional($sub->product)->name ?? 'produk';
        switch ($event) {
            case 'submitted':
                self::notifyRoles(['admin'], $type, 'Pengajuan Produk Baru', "Pengajuan produk \"{$name}\" perlu direview.", $data, $actorId);
                break;
            case 'forwarded':
                self::notifyRoles(['super_admin'], $type, 'Pengajuan Produk Menunggu Persetujuan', "Pengajuan produk \"{$name}\" telah direview admin dan menunggu persetujuan Anda.", $data, $actorId);
                break;
            case 'approved':
                self::notifyUser($sub->admin_id, $type, 'Pengajuan Produk Disetujui', "Pengajuan produk \"{$name}\" disetujui.", $data, $actorId);
                break;
            case 'rejected':
                self::notifyUser($sub->admin_id, $type, 'Pengajuan Produk Ditolak', "Pengajuan produk \"{$name}\" ditolak. Alasan: " . ($sub->reject_reason ?? '-'), $data, $actorId);
                break;
        }
    }

    /** Notifikasi siklus RAB. $event: submitted|forwarded|rejected|postponed|approved_final|rejected_final|realisasi */
    public static function budgetRequest(\App\Models\BudgetRequest $br, string $event, ?int $actorId = null): void
    {
        $data = ['budget_request_id' => $br->id, 'nomor_form' => $br->nomor_form];
        $type = 'budget_request';
        $no   = $br->nomor_form ?? ('#' . $br->id);
        switch ($event) {
            case 'submitted':
                self::notifyRoles(['admin'], $type, 'RAB Baru Masuk', "RAB {$no} menunggu review admin.", $data, $actorId);
                break;
            case 'forwarded':
                self::notifyRoles(['super_admin'], $type, 'RAB Menunggu Persetujuan Final', "RAB {$no} sudah disetujui admin dan menunggu persetujuan final Anda.", $data, $actorId);
                self::notifyUser($br->user_id, $type, 'RAB Diteruskan ke Super Admin', "RAB {$no} disetujui admin dan diteruskan ke Super Admin.", $data, $actorId);
                break;
            case 'rejected':
                self::notifyUser($br->user_id, $type, 'RAB Ditolak', "RAB {$no} ditolak admin.", $data, $actorId);
                break;
            case 'postponed':
                self::notifyUser($br->user_id, $type, 'RAB Ditunda', "RAB {$no} ditunda. Silakan revisi lalu ajukan ulang.", $data, $actorId);
                break;
            case 'approved_final':
                self::notifyUser($br->user_id, $type, 'RAB Disetujui', "RAB {$no} disetujui final dan dana telah dialokasikan.", $data, $actorId);
                self::notifyUser($br->branch_manager_id, $type, 'RAB Disetujui Final', "RAB {$no} yang Anda teruskan telah disetujui final.", $data, $actorId);
                break;
            case 'rejected_final':
                self::notifyUser($br->user_id, $type, 'RAB Ditolak Super Admin', "RAB {$no} ditolak pada tahap final.", $data, $actorId);
                self::notifyUser($br->branch_manager_id, $type, 'RAB Ditolak Final', "RAB {$no} yang Anda teruskan ditolak Super Admin.", $data, $actorId);
                break;
            case 'realisasi':
                self::notifyUser($br->user_id, $type, 'Realisasi Dana RAB', "Realisasi dana RAB {$no} telah dicatat.", $data, $actorId);
                break;
        }
    }
}

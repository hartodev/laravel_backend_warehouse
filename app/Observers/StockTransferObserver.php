<?php

namespace App\Observers;

use App\Models\StockTransfer;
use App\Models\User;
use App\Services\NotificationService;

/**
 * Satu-satunya titik notifikasi transfer stok, supaya perubahan status dari
 * API, web admin, maupun web super admin menghasilkan notifikasi yang sama.
 */
class StockTransferObserver
{
    private const TYPE = 'stock_transfer';

    public function created(StockTransfer $t): void
    {
        if ($t->status === 'pending_confirmation') {
            $this->warehouseAdmins($t->from_warehouse_id, 'Transfer Stok Perlu Konfirmasi', "Transfer {$this->no($t)} menunggu konfirmasi gudang Anda.", $t);
        }
    }

    public function updated(StockTransfer $t): void
    {
        if (! $t->wasChanged('status')) return;

        $no    = $this->no($t);
        $actor = auth()->id();

        switch ($t->status) {
            case 'pending_approval':
                NotificationService::notifyRoles(['super_admin'], self::TYPE, 'Transfer Menunggu Persetujuan', "Transfer {$no} menunggu persetujuan Super Admin.", $this->data($t), $actor);
                break;
            case 'approved':
                $this->parties($t, 'Transfer Disetujui', "Transfer {$no} disetujui. Barang siap dikirim.", [$t->from_warehouse_id]);
                break;
            case 'in_transit':
                $this->parties($t, 'Barang Transfer Dikirim', "Barang transfer {$no} sedang dalam perjalanan.", [$t->to_warehouse_id]);
                break;
            case 'received':
                $this->parties($t, 'Transfer Selesai', "Transfer {$no} telah diterima.", [$t->from_warehouse_id], true);
                break;
            case 'rejected':
                $this->parties($t, 'Transfer Ditolak', "Transfer {$no} ditolak. Alasan: " . ($t->reject_reason ?? '-'), [$t->from_warehouse_id, $t->to_warehouse_id]);
                break;
            case 'cancelled':
                $this->parties($t, 'Transfer Dibatalkan', "Transfer {$no} dibatalkan.", [$t->from_warehouse_id, $t->to_warehouse_id]);
                break;
            case 'discrepancy':
                NotificationService::notifyRoles(['super_admin'], self::TYPE, 'Selisih Transfer Stok', "Transfer {$no} memiliki selisih jumlah dan perlu diselesaikan.", $this->data($t), $actor);
                break;
        }
    }

    private function parties(StockTransfer $t, string $title, string $body, array $warehouseIds, bool $alsoSuperAdmin = false): void
    {
        $actor = auth()->id();
        NotificationService::notifyUser($t->requested_by, self::TYPE, $title, $body, $this->data($t), $actor);
        foreach ($warehouseIds as $wid) {
            $this->warehouseAdmins($wid, $title, $body, $t);
        }
        if ($alsoSuperAdmin) {
            NotificationService::notifyRoles(['super_admin'], self::TYPE, $title, $body, $this->data($t), $actor);
        }
    }

    private function warehouseAdmins(?int $warehouseId, string $title, string $body, StockTransfer $t): void
    {
        if (! $warehouseId) return;
        $actor = auth()->id();
        try {
            User::whereIn('role', ['admin', 'warehouse_keeper'])->where('warehouse_id', $warehouseId)->where('is_active', true)
                ->where('id', '!=', $actor ?? 0)
                ->where('id', '!=', $t->requested_by ?? 0) // sudah dinotifikasi langsung
                ->get()
                ->each(fn ($u) => NotificationService::send($u, self::TYPE, $title, $body, $this->data($t)));
        } catch (\Throwable $e) {
            \Log::warning('Notif transfer gagal: ' . $e->getMessage());
        }
    }

    private function no(StockTransfer $t): string
    {
        return $t->transfer_number ?? $t->number ?? ('#' . $t->id);
    }

    private function data(StockTransfer $t): array
    {
        return ['stock_transfer_id' => $t->id];
    }
}

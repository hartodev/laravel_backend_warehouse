<?php

namespace App\Observers;

use App\Models\StockOpname;
use App\Services\NotificationService;

class StockOpnameObserver
{
    private const TYPE = 'stock_opname';

    public function updated(StockOpname $o): void
    {
        if (! $o->wasChanged('status')) return;

        $actor = auth()->id();
        $no    = $o->opname_number ?? $o->number ?? ('#' . $o->id);
        $data  = ['stock_opname_id' => $o->id];
        $prev  = $o->getOriginal('status');

        if ($o->status === 'pending_approval') {
            NotificationService::notifyRoles(['super_admin'], self::TYPE, 'Opname Menunggu Persetujuan', "Stok opname {$no} menunggu persetujuan Anda.", $data, $actor);
        } elseif ($o->status === 'approved') {
            NotificationService::notifyUser($o->created_by, self::TYPE, 'Opname Disetujui', "Stok opname {$no} disetujui dan stok disesuaikan.", $data, $actor);
        } elseif ($o->status === 'in_progress' && $prev === 'pending_approval') {
            NotificationService::notifyUser($o->created_by, self::TYPE, 'Opname Dikembalikan', "Stok opname {$no} ditolak dan dikembalikan untuk dihitung ulang.", $data, $actor);
        } elseif ($o->status === 'cancelled') {
            NotificationService::notifyUser($o->created_by, self::TYPE, 'Opname Dibatalkan', "Stok opname {$no} dibatalkan.", $data, $actor);
        }
    }
}

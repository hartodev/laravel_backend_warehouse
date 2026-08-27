<?php

namespace App\Services;

use App\Exceptions\BudgetRequestException;
use App\Models\BudgetRequest;
use App\Models\User;

/**
 * Business logic review RAB oleh Admin untuk panel Web\Admin (BARU).
 *
 * CATATAN: Api\Admin\AdminBudgetRequestController (dipakai Android) dan
 * controller Superadmin (BudgetRequestController, BudgetVerificationController)
 * yang SUDAH ADA sebelumnya TIDAK diubah dan TIDAK memakai service ini.
 *
 * Aturan di sini (gate status 'pending', field yang diisi) sudah disamakan
 * MANUAL dengan Api\Admin\AdminBudgetRequestController per hari ini dibuat.
 * Kalau nanti aturan di controller Api/Superadmin berubah, file ini TIDAK
 * ikut berubah otomatis — perlu diupdate manual juga di sini.
 *
 * Flow: pending (user submit) ─┬─ admin approve ─→ pending_sa (giliran Super Admin)
 *                               ├─ admin reject  ─→ ditolak
 *                               └─ admin tunda   ─→ ditunda (user revisi & submit ulang)
 */
class BudgetRequestAdminService
{
    public function approve(BudgetRequest $br, User $user, ?string $catatan): BudgetRequest
    {
        if ($br->status !== 'pending') {
            throw new BudgetRequestException('Hanya pengajuan dengan status "pending" yang dapat disetujui admin.');
        }

        $br->update([
            'status'                 => 'pending_sa',
            'branch_manager_id'      => $user->id,
            'branch_manager_at'      => now(),
            'catatan_branch_manager' => $catatan,
        ]);

        return $br->fresh();
    }

    public function reject(BudgetRequest $br, User $user, string $catatan): BudgetRequest
    {
        if ($br->status !== 'pending') {
            throw new BudgetRequestException('Hanya pengajuan dengan status "pending" yang dapat ditolak admin.');
        }

        $br->update([
            'status'                 => 'ditolak',
            'branch_manager_id'      => $user->id,
            'branch_manager_at'      => now(),
            'catatan_branch_manager' => $catatan,
        ]);

        return $br->fresh();
    }

    public function tunda(BudgetRequest $br, User $user, string $catatan): BudgetRequest
    {
        if ($br->status !== 'pending') {
            throw new BudgetRequestException('Hanya pengajuan dengan status "pending" yang dapat ditunda.');
        }

        $br->update([
            'status'                 => 'ditunda',
            'branch_manager_id'      => $user->id,
            'branch_manager_at'      => now(),
            'catatan_branch_manager' => $catatan,
        ]);

        return $br->fresh();
    }
}

<?php
// app/Models/BudgetRequest.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BudgetRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'tanggal_pengajuan'  => 'date',
        'branch_manager_at'  => 'datetime',
        'finance_at'         => 'datetime',
        'total_estimasi'     => 'decimal:2',
        'total_realisasi'    => 'decimal:2',
    ];

    // ─────────────────────────────────────────────────────────────
    // Relasi
    // ─────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BudgetRequestItem::class);
    }

    /** Admin yang melakukan review pertama */
    public function adminApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_approver_id');
    }

    /** Super admin yang melakukan approval final */
    public function superAdminApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'super_admin_approver_id');
    }

    public function branchManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'branch_manager_id');
    }

    public function finance(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finance_id');
    }

    public function verification(): HasOne
    {
        return $this->hasOne(BudgetVerification::class);
    }

    public function expenseReport(): HasOne
    {
        return $this->hasOne(ExpenseReport::class);
    }

    public function revision(): HasOne
    {
        return $this->hasOne(BudgetRevision::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function cashBooks(): HasMany
    {
        return $this->hasMany(CashBook::class);
    }

    // ─────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────

    public function isRab(): bool      { return $this->jenis === 'rab'; }
    public function isLuarRab(): bool  { return $this->jenis === 'luar_rab'; }
    public function isDraft(): bool    { return $this->status === 'draft'; }
    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isMendesak(): bool { return $this->urgensi === 'mendesak'; }

    public function recalculateTotal(): void
    {
        $this->update(['total_estimasi' => $this->items()->sum('total')]);
    }

    public function getSisaAnggaranAttribute(): float
    {
        return (float) $this->total_estimasi - (float) ($this->total_realisasi ?? 0);
    }

    public function getIsUrgentAttribute(): bool
    {
        return $this->urgensi === 'mendesak';
    }

    /**
     * Generate nomor form unik
     * Format: RAB/DIV/2024/001 atau LRAB/DIV/2024/001
     */
    public static function generateNomorForm(string $jenis, string $divisi): string
    {
        $prefix   = $jenis === 'rab' ? 'RAB' : 'LRAB';
        $divKode  = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $divisi), 0, 4));
        $year     = now()->format('Y');
        $month    = now()->format('m');

        $lastCount = static::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('jenis', $jenis)
            ->count();

        $seq = str_pad($lastCount + 1, 3, '0', STR_PAD_LEFT);

        return "{$prefix}/{$divKode}/{$year}{$month}/{$seq}";
    }

    public function budgetVerifications()
{
    return $this->hasMany(BudgetVerification::class);
}

public function budgetRevisions()
{
    return $this->hasMany(BudgetRevision::class);
}
}

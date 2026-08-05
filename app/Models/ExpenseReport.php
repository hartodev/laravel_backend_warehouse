<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseReport extends Model
{
    use HasFactory;
  protected $guarded = [];

    protected $casts = [
        'tanggal_transaksi'   => 'date',
        'nominal_realisasi'   => 'float',
        'selisih'             => 'float',
        'lamp_invoice'        => 'boolean',
        'lamp_bukti_transfer' => 'boolean',
        'lamp_kartu_garansi'  => 'boolean',
        'lamp_serah_terima'   => 'boolean',
        'attachment_files'    => 'array',
        'verified_at'         => 'datetime',
    ];

    public function budgetRequest()
    {
        return $this->belongsTo(BudgetRequest::class);
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function revision()
    {
        return $this->hasOne(BudgetRevision::class);
    }

    // // ── Helpers ──────────────────────────────────────────────
    // public function calculateSelisih(): void
    // {
    //     $estimasi      = (float) ($this->budgetRequest?->estimasi_biaya ?? 0);
    //     $this->selisih = $estimasi - $this->nominal_realisasi;
    // }

    public function isOverBudget(): bool
    {
        return $this->selisih < 0;
    }

    public function calculateSelisih(): void
    {
        $estimasi      = (float) ($this->budgetRequest?->total_estimasi ?? 0);
        $this->selisih = $estimasi - $this->nominal_realisasi;
    }
}




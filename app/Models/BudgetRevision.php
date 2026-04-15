<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetRevision extends Model
{
    use HasFactory;
      protected $guarded = [];
 
    protected $casts = [
        'anggaran_awal'     => 'float',
        'realisasi'         => 'float',
        'nominal_perubahan' => 'float',
        'approved_at'       => 'datetime',
    ];
 
    public function budgetRequest()
    {
        return $this->belongsTo(BudgetRequest::class);
    }
 
    public function expenseReport()
    {
        return $this->belongsTo(ExpenseReport::class);
    }
 
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
 
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
 
    // ── Helpers ──────────────────────────────────────────────
 
    /**
     * Hitung anggaran_baru — return float.
     * Assign hasilnya di controller:
     *   $revision->anggaran_baru = $revision->hitungAnggaranBaru();
     */
    public function hitungAnggaranBaru(): float
    {
        $sisa = (float) $this->anggaran_awal - (float) $this->realisasi;
 
        return $this->jenis_perubahan === 'tambahan'
            ? $sisa - (float) $this->nominal_perubahan
            : $sisa + (float) $this->nominal_perubahan;
    }
 
    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
}
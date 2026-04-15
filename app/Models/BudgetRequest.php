<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetRequest extends Model
{
     use HasFactory, SoftDeletes;
 
    protected $guarded = [];
 
    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'estimasi_biaya'    => 'decimal:2',
        'total'             => 'decimal:2',
        'qty'               => 'decimal:2',
        'branch_manager_at' => 'datetime',
        'finance_at'        => 'datetime',
    ];
 
    public function user()
    {
        return $this->belongsTo(User::class);
    }
 
    public function branchManager()
    {
        return $this->belongsTo(User::class, 'branch_manager_id');
    }
 
    public function finance()
    {
        return $this->belongsTo(User::class, 'finance_id');
    }
 
    public function verification()
    {
        return $this->hasOne(BudgetVerification::class);
    }
 
    public function expenseReport()
    {
        return $this->hasOne(ExpenseReport::class);
    }
 
    public function revision()
    {
        return $this->hasOne(BudgetRevision::class);
    }
 
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
 
    // ── Helpers ──────────────────────────────────────────────
    public function isRab(): bool      { return $this->jenis === 'rab'; }
    public function isLuarRab(): bool  { return $this->jenis === 'luar_rab'; }
    public function isDraft(): bool    { return $this->status === 'draft'; }
    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isMendesak(): bool { return $this->urgensi === 'mendesak'; }
 
    /**
     * Generate nomor form otomatis.
     * RAB:      FR-RAB/DIVISI/BULAN/TAHUN/URUTAN
     * Luar RAB: FR-LRAB/DIVISI/BULAN/TAHUN/URUTAN
     */
    public static function generateNomorForm(string $jenis, string $divisi): string
    {
        $prefix  = $jenis === 'rab' ? 'FR-RAB' : 'FR-LRAB';
        $divKode = strtoupper(str_replace(' ', '-', $divisi));
        $bulan   = now()->format('m');
        $tahun   = now()->format('Y');
 
        $last   = static::where('nomor_form', 'like', "{$prefix}/{$divKode}/{$bulan}/{$tahun}/%")
                        ->count();
        $urutan = str_pad($last + 1, 3, '0', STR_PAD_LEFT);
 
        return "{$prefix}/{$divKode}/{$bulan}/{$tahun}/{$urutan}";
    }
}

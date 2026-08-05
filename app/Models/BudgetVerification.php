<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class BudgetVerification extends Model
{
    use HasFactory;
      protected $guarded = [];

    protected $casts = [
        'doc_form_lengkap'       => 'boolean',
        'doc_surat_justifikasi'  => 'boolean',
        'doc_estimasi_vendor'    => 'boolean',
        'doc_spesifikasi_teknis' => 'boolean',
        'nominal_rekomendasi'    => 'decimal:2',
        'verified_at'            => 'datetime',
    ];

    public function budgetRequest()
    {
        return $this->belongsTo(BudgetRequest::class);
    }

    public function finance()
    {
        return $this->belongsTo(User::class, 'finance_id');
    }

    // ── Helpers ──────────────────────────────────────────────
    public function allDocsComplete(): bool
    {
        return $this->doc_form_lengkap
            && $this->doc_surat_justifikasi
            && $this->doc_estimasi_vendor
            && $this->doc_spesifikasi_teknis;
    }
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'finance_id');
    }
}




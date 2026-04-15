<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashBook extends Model
{
    use HasFactory;
     protected $guarded = [];
 
    protected $casts = [
        'jumlah_uang' => 'decimal:2',
        'tanggal'     => 'date',
        'verified_at' => 'datetime',
    ];
 
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
 
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
 
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
 
    // ── Helpers ──────────────────────────────────────────────
    public function isMasuk(): bool  { return $this->type === 'masuk'; }
    public function isKeluar(): bool { return $this->type === 'keluar'; }

}

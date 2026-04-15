<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
       use HasFactory, SoftDeletes;
 
    protected $guarded = [];
 
    protected $casts = [
        'nominal'      => 'decimal:2',
        'payment_date' => 'date',
        'verified_at'  => 'datetime',
    ];
 
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
 
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
 
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
 
    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }
 
    public function budgetRequest()
    {
        return $this->belongsTo(BudgetRequest::class);
    }
 
    public function cashBook()
    {
        return $this->hasOne(CashBook::class);
    }
 
    // ── Helpers ──────────────────────────────────────────────
    public function isMasuk(): bool    { return $this->payment_type === 'masuk'; }
    public function isKeluar(): bool   { return $this->payment_type === 'keluar'; }
    public function isVerified(): bool { return $this->status === 'verified'; }
}

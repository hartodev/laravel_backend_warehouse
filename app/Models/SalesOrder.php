<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesOrder extends Model
{
     use HasFactory, SoftDeletes;
 
    protected $guarded = [];
 
    protected $casts = [
        'order_date'      => 'date',
        'due_date'        => 'date',
        'approved_at'     => 'datetime',
        'subtotal'        => 'decimal:2',
        'tax_percent'     => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount'    => 'decimal:2',
    ];
 
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
 
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
 
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
 
    public function items()
    {
        return $this->hasMany(SalesOrderItem::class);
    }
 
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
 
    // ── Helpers ──────────────────────────────────────────────
    public function isDraft(): bool     { return $this->status === 'draft'; }
    public function isConfirmed(): bool { return $this->status === 'confirmed'; }
    public function isCompleted(): bool { return $this->status === 'completed'; }
 
    public function isPaid(): bool
    {
        return $this->payments()
                    ->where('status', 'verified')
                    ->sum('nominal') >= $this->total_amount;
    }
}

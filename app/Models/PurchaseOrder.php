<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
     use HasFactory, SoftDeletes;
 
    protected $guarded = [];
 
    protected $casts = [
        'order_date'      => 'date',
        'expected_date'   => 'date',
        'received_date'   => 'date',
        'approved_at'     => 'datetime',
        'subtotal'        => 'decimal:2',
        'tax_percent'     => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount'    => 'decimal:2',
    ];
 
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
 
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
        return $this->hasMany(PurchaseOrderItem::class);
    }
 
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
 
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
 
    // ── Helpers ──────────────────────────────────────────────
    public function isDraft(): bool    { return $this->status === 'draft'; }
    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isPartial(): bool  { return $this->status === 'partial'; }
    public function isReceived(): bool { return $this->status === 'received'; }
}

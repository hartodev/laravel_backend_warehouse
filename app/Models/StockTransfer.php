<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockTransfer extends Model
{
     use HasFactory, SoftDeletes;
 
    protected $guarded = [];
 
    protected $casts = [
        'transfer_date'    => 'date',
        'expected_arrival' => 'date',
        'approved_at'      => 'datetime',
        'received_at'      => 'datetime',
    ];
 
    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }
 
    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }
 
    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
 
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
 
    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
 
    public function items()
    {
        return $this->hasMany(StockTransferItem::class);
    }
 
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
 
    // ── Helpers ──────────────────────────────────────────────
    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isApproved(): bool  { return $this->status === 'approved'; }
    public function isInTransit(): bool { return $this->status === 'in_transit'; }
    public function isReceived(): bool  { return $this->status === 'received'; }
}

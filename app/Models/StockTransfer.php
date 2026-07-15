<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockTransfer extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];


    protected function casts(): array
    {
        return [
            'transfer_date'           => 'date',
            'expected_arrival'        => 'date',
            'approved_at'             => 'datetime',
            'received_at'             => 'datetime',
            'cancelled_at'            => 'datetime',
            'discrepancy_reported_at' => 'datetime',
            'resolved_at'             => 'datetime',
        ];
    }

    // -------------------------------------------------------
    // SCOPES
    // -------------------------------------------------------


    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
    public function scopeInTransit($query)
    {
        return $query->where('status', 'in_transit');
    }
    public function scopeReceived($query)
    {
        return $query->where('status', 'received');
    }
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
    public function scopeDiscrepancy($query)
    {
        return $query->where('status', 'discrepancy');
    }

    // -------------------------------------------------------
    // RELATIONS
    // -------------------------------------------------------

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

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function discrepancyReportedBy()
    {
        return $this->belongsTo(User::class, 'discrepancy_reported_by');
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function items()
    {
        return $this->hasMany(StockTransferItem::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'reference_id')
            ->where('reference_type', 'stock_transfer');
    }
}




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
            'transfer_date'    => 'date',
            'expected_arrival' => 'date',
            'approved_at'      => 'datetime',
            'received_at'      => 'datetime',
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

    // -------------------------------------------------------
    // RELATIONS
    // -------------------------------------------------------

    /** Gudang pengirim */
    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    /** Gudang penerima */
    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    /** User yang meminta transfer */
    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /** User yang meng-approve transfer */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /** User yang menerima barang di gudang tujuan */
    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /** Item-item produk dalam transfer ini */
    public function items()
    {
        return $this->hasMany(StockTransferItem::class);
    }

    /** Pergerakan stok yang dipicu transfer ini */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'reference_id')
            ->where('reference_type', 'stock_transfer');
    }
}




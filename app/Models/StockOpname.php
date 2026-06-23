<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Category;

class StockOpname extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'opname_date'  => 'date',
            'started_at'   => 'datetime',
            'completed_at' => 'datetime',
            'approved_at'  => 'datetime',
        ];
    }

    // -------------------------------------------------------
    // SCOPES
    // -------------------------------------------------------

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }
    public function scopePendingApproval($query)
    {
        return $query->where('status', 'pending_approval');
    }
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // -------------------------------------------------------
    // RELATIONS
    // -------------------------------------------------------

    /** Gudang yang di-opname */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /** User yang membuat opname */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** User yang meng-approve opname */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /** Semua item produk dalam opname ini */
    public function items()
    {
        return $this->hasMany(StockOpnameItem::class);
    }

    /** Item yang memiliki selisih (fisik ≠ sistem) */
    public function deviatedItems()
    {
        return $this->hasMany(StockOpnameItem::class)
            ->whereColumn('physical_stock', '!=', 'system_stock');
    }

    /** Pergerakan stok adjustment yang dipicu opname ini */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'reference_id')
            ->where('reference_type', 'stock_opname');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

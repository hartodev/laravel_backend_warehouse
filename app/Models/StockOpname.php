<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockOpname extends Model
{
    use HasFactory, SoftDeletes;
 
    protected $guarded = [];
 
    protected $casts = [
        'opname_date'  => 'date',
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
        'approved_at'  => 'datetime',
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
        return $this->hasMany(StockOpnameItem::class);
    }
 
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
 
    // ── Helpers ──────────────────────────────────────────────
    public function totalDifference(): int
    {
        return $this->items()->sum('difference');
    }
 
    public function hasDiscrepancy(): bool
    {
        return $this->items()->where('difference', '!=', 0)->exists();
    }
}

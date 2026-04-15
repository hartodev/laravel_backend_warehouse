<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpnameItem extends Model
{
    use HasFactory;
       protected $guarded = [];
 
    protected $casts = [
        'system_stock'   => 'integer',
        'physical_stock' => 'integer',
        'difference'     => 'integer',
    ];
 
    public function stockOpname()
    {
        return $this->belongsTo(StockOpname::class);
    }
 
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
 
    // ── Helpers ──────────────────────────────────────────────
    public function hasDifference(): bool { return $this->difference !== 0; }
    public function isOverstock(): bool   { return $this->difference > 0; }
    public function isShortage(): bool    { return $this->difference < 0; }
}

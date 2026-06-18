<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpnameItem extends Model
{
    use HasFactory;
    protected $guarded = [];


    protected function casts(): array
    {
        return [
            'system_stock'   => 'integer',
            'physical_stock' => 'integer',
        ];
    }

    // -------------------------------------------------------
    // ACCESSOR
    // -------------------------------------------------------

    /**
     * Selisih = physical_stock - system_stock.
     * Jika DB support generated column → nilai dari DB.
     * Jika tidak (SQLite saat testing) → dihitung di sini.
     */
    public function getDifferenceAttribute(): int
    {
        if (array_key_exists('difference', $this->attributes)) {
            return (int) $this->attributes['difference'];
        }

        return $this->physical_stock - $this->system_stock;
    }

    // -------------------------------------------------------
    // HELPERS
    // -------------------------------------------------------

    /** Ada selisih antara stok fisik dan sistem */
    public function hasDeviation(): bool
    {
        return $this->difference !== 0;
    }

    /** Stok fisik lebih banyak dari sistem (kelebihan) */
    public function isOverstock(): bool
    {
        return $this->difference > 0;
    }

    /** Stok fisik lebih sedikit dari sistem (kekurangan) */
    public function isShortage(): bool
    {
        return $this->difference < 0;
    }

    // -------------------------------------------------------
    // RELATIONS
    // -------------------------------------------------------

    /** Opname induk */
    public function stockOpname()
    {
        return $this->belongsTo(StockOpname::class);
    }

    /** Produk yang di-opname */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

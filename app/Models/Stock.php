<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected function casts(): array
    {
        return ['quantity' => 'integer'];
    }

    // -------------------------------------------------------
    // HELPERS
    // -------------------------------------------------------

    /** Cek apakah stok di bawah minimum yang ditentukan produk */
    public function isBelowMinStock(): bool
    {
        return $this->quantity < $this->product->min_stock;
    }

    // -------------------------------------------------------
    // RELATIONS
    // -------------------------------------------------------

    /** Gudang tempat stok ini berada */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /** Produk yang distok */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function reduceStock(int $qty): void
    {
        $this->decrement('quantity', $qty);
    }

    public function addStock(int $qty): void
    {
        $this->increment('quantity', $qty);
    }
}



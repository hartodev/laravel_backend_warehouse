<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;
        protected $guarded = [];
 
    protected $casts = [
        'quantity' => 'integer',
    ];
 
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
 
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
 
    // ── Helpers ──────────────────────────────────────────────
    public function addStock(int $qty): static
    {
        $this->quantity += $qty;
        $this->save();
        return $this;
    }
 
    public function reduceStock(int $qty): static
    {
        if ($this->quantity < $qty) {
            throw new \RuntimeException(
                "Stok tidak cukup untuk produk ID {$this->product_id} " .
                "di gudang ID {$this->warehouse_id}. " .
                "Tersedia: {$this->quantity}, diminta: {$qty}"
            );
        }
        $this->quantity -= $qty;
        $this->save();
        return $this;
    }
 
    public function isLow(): bool
    {
        return $this->quantity <= ($this->product->min_stock ?? 0);
    }
}

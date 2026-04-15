<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;
      protected $guarded = [];
 
    protected $casts = [
        'quantity_ordered'  => 'integer',
        'quantity_received' => 'integer',
        'unit_price'        => 'decimal:2',
        'discount_percent'  => 'decimal:2',
        'subtotal'          => 'decimal:2',
    ];
 
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
 
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
 
    // ── Helpers ──────────────────────────────────────────────
    public function remainingQty(): int
    {
        return $this->quantity_ordered - $this->quantity_received;
    }
 
    public function isFullyReceived(): bool
    {
        return $this->quantity_received >= $this->quantity_ordered;
    }
}

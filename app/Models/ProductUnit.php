<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUnit extends Model
{
    use HasFactory;
      protected $guarded = [];
 
    protected $casts = [
        'conversion_value' => 'decimal:4',
        'is_purchase_unit' => 'boolean',
        'is_sell_unit'     => 'boolean',
    ];
 
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

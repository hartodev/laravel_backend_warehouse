<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrderItem extends Model
{
    use HasFactory;
     protected $guarded = [];
 
    protected $casts = [
        'qty'   => 'integer',
        'harga' => 'decimal:2',
        'total' => 'decimal:2',
    ];
 
    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }
 
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestItem extends Model
{
    use HasFactory;
     protected $guarded = [];
 
    protected $casts = [
        'quantity'          => 'integer',
        'approved_quantity' => 'integer',
    ];
 
    public function request()
    {
        return $this->belongsTo(Request::class);
    }
 
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
 
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}

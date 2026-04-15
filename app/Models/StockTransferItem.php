<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransferItem extends Model
{
    use HasFactory;
     
    protected $guarded = [];
 
    protected $casts = [
        'quantity_requested' => 'integer',
        'quantity_sent'      => 'integer',
        'quantity_received'  => 'integer',
    ];
 
    public function stockTransfer()
    {
        return $this->belongsTo(StockTransfer::class);
    }
 
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

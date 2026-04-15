<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;
      protected $guarded = [];
 
    protected $casts = [
        'quantity'        => 'integer',
        'quantity_before' => 'integer',
        'quantity_after'  => 'integer',
    ];
 
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
 
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
 
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
 
    public function request()
    {
        return $this->belongsTo(Request::class);
    }
 
    public function requestItem()
    {
        return $this->belongsTo(RequestItem::class);
    }
 
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
 
    public function stockTransfer()
    {
        return $this->belongsTo(StockTransfer::class);
    }
 
    public function stockOpname()
    {
        return $this->belongsTo(StockOpname::class);
    }
}

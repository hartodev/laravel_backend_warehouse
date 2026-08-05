<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function transfersOut()
    {
        return $this->hasMany(StockTransfer::class, 'from_warehouse_id');
    }

    public function transfersIn()
    {
        return $this->hasMany(StockTransfer::class, 'to_warehouse_id');
    }

    public function stockOpnames()
    {
        return $this->hasMany(StockOpname::class);
    }

    public function stockReports()
    {
        return $this->hasMany(StockReport::class);
    }

    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class);
    }

    public function barcodeLogs()
    {
        return $this->hasMany(BarcodeLog::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'stocks')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    public function productSubmissions()
    {
        return $this->hasMany(ProductSubmission::class, 'initial_warehouse_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}

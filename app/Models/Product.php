<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Supplier;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price'  => 'decimal:2',
        'is_active'      => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function units()
    {
        return $this->hasMany(ProductUnit::class);
    }

    public function submissions()
    {
        return $this->hasMany(ProductSubmission::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function requestItems()
    {
        return $this->hasMany(RequestItem::class);
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function salesOrderItems()
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function stockTransferItems()
    {
        return $this->hasMany(StockTransferItem::class);
    }

    public function stockOpnameItems()
    {
        return $this->hasMany(StockOpnameItem::class);
    }

    public function stockReports()
    {
        return $this->hasMany(StockReport::class);
    }

    public function barcodeLogs()
    {
        return $this->hasMany(BarcodeLog::class);
    }

    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'stocks')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    // ── Helpers ──────────────────────────────────────────────
    public function totalStock(): int
    {
        return $this->stocks()->sum('quantity');
    }

    public function stockInWarehouse(int $warehouseId): int
    {
        return $this->stocks()
                    ->where('warehouse_id', $warehouseId)
                    ->value('quantity') ?? 0;
    }

    public function isLowStock(): bool
    {
        return $this->totalStock() <= $this->min_stock;
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}

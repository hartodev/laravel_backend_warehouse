<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $admin_id
 * @property int $category_id
 * @property string $name
 * @property string|null $sku
 * @property string|null $barcode
 * @property string|null $unit
 * @property int|float $initial_stock
 * @property int|null $initial_warehouse_id
 * @property float $purchase_price
 * @property float $selling_price
 * @property string|null $description
 * @property string $status
 * @property array|null $change_data
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property string|null $reject_reason
 * @property int|null $product_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */

class ProductSubmission extends Model
{
    use HasFactory;
      protected $guarded = [];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price'  => 'decimal:2',
        'approved_at'    => 'datetime',
         'change_data' => 'array',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function initialWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'initial_warehouse_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // ── Helpers ──────────────────────────────────────────────
    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isRejected(): bool { return $this->status === 'rejected'; }
}

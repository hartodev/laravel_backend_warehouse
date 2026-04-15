<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSubmission extends Model
{
    use HasFactory;
      protected $guarded = [];
 
    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price'  => 'decimal:2',
        'approved_at'    => 'datetime',
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestItem extends Model
{
    use HasFactory;
    protected $guarded = [];


    protected function casts(): array
    {
        return [
            'quantity'          => 'integer',
            'approved_quantity' => 'integer',
            'external_price'    => 'decimal:2',
        ];
    }

    // -------------------------------------------------------
    // RELATIONS
    // -------------------------------------------------------

    /** Permintaan stok induk */
    public function request()
    {
        return $this->belongsTo(\App\Models\Request::class, 'request_id');
    }

    /** Produk yang diminta */


    public function isExternal(): bool
    {
        return $this->product_id === null;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}



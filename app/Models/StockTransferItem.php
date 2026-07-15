<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransferItem extends Model
{
    use HasFactory;

    protected $guarded = [];


    protected function casts(): array
    {
        return [
            'quantity_requested' => 'integer',
            'quantity_sent'      => 'integer',
            'quantity_received'  => 'integer',
            'is_matched'         => 'boolean',
        ];
    }

    // -------------------------------------------------------
    // RELATIONS
    // -------------------------------------------------------

    /** Transfer stok induk */
    public function stockTransfer()
    {
        return $this->belongsTo(StockTransfer::class);
    }

    /** Produk yang ditransfer */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}





<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockReport extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $guarded = [];


    protected function casts(): array
    {
        return [
            'period_date'   => 'date',
            'generated_at'  => 'datetime',
            'total_value'   => 'decimal:2',
            'opening_stock' => 'integer',
            'stock_in'      => 'integer',
            'stock_out'     => 'integer',
            'transfer_in'   => 'integer',
            'transfer_out'  => 'integer',
            'adjustment'    => 'integer',
            'closing_stock' => 'integer',
        ];
    }

    // -------------------------------------------------------
    // SCOPES
    // -------------------------------------------------------

    public function scopeDaily($query)
    {
        return $query->where('period_type', 'daily');
    }
    public function scopeMonthly($query)
    {
        return $query->where('period_type', 'monthly');
    }

    public function scopeForPeriod($query, string $type, string $date)
    {
        return $query->where('period_type', $type)->where('period_date', $date);
    }

    // -------------------------------------------------------
    // RELATIONS
    // -------------------------------------------------------

    /** Gudang laporan ini */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /** Produk laporan ini */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

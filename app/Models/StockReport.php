<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockReport extends Model
{
    use HasFactory;
     public $timestamps = false;
 
    protected $guarded = [];
 
    protected $casts = [
        'period_date'  => 'date',
        'generated_at' => 'datetime',
        'total_value'  => 'decimal:2',
    ];
 
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
 
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

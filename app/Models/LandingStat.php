<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class LandingStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'is_static',
        'static_value',
        'target',
        'suffix',
        'decimal_places',
        'bar_percentage',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_static'      => 'boolean',
        'is_active'      => 'boolean',
        'target'         => 'decimal:2',
        'decimal_places' => 'integer',
        'bar_percentage' => 'integer',
        'order'          => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order')->orderBy('id');
    }
}

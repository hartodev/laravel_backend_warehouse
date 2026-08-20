<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LandingSolution extends Model
{
    protected $fillable = [
        'title', 'description', 'icon', 'color', 'size',
        'visual_type', 'chart_data', 'order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order'     => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order')->orderBy('id');
    }

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(LandingSolutionInventoryItem::class)->orderBy('order');
    }

    /**
     * Ubah "40,65,45,80,55,90,70" jadi array [40,65,45,80,55,90,70] untuk dipakai di mini bar chart.
     */
    public function getChartBarsAttribute(): array
    {
        if (blank($this->chart_data)) {
            return [];
        }

        return array_map(
            fn ($value) => (int) trim($value),
            explode(',', $this->chart_data)
        );
    }
}

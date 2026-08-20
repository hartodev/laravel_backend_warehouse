<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandingSolutionInventoryItem extends Model
{
    protected $fillable = ['landing_solution_id', 'name', 'stock', 'color', 'order'];

    protected $casts = [
        'order' => 'integer',
    ];

    public function solution(): BelongsTo
    {
        return $this->belongsTo(LandingSolution::class, 'landing_solution_id');
    }
}

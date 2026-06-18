<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetRequestItem extends Model
{
    use HasFactory;


    protected $guarded = [];


    protected function casts(): array
    {
        return [
            'qty'            => 'decimal:2',
            'estimasi_biaya' => 'decimal:2',
            'total'          => 'decimal:2',
        ];
    }

    // -------------------------------------------------------
    // RELATIONS
    // -------------------------------------------------------

    /** Pengajuan anggaran induk */
    public function budgetRequest()
    {
        return $this->belongsTo(BudgetRequest::class);
    }
}

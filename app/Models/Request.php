<?php


/////versi baru
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Request extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'approved_at'  => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    // -------------------------------------------------------
    // SCOPES
    // -------------------------------------------------------

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // -------------------------------------------------------
    // STATUS HELPER METHODS  ← tambahan ini yang kurang
    // -------------------------------------------------------

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    // -------------------------------------------------------
    // RELATIONS
    // -------------------------------------------------------

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(RequestItem::class, 'request_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'reference_id')
            ->where('reference_type', 'request');
    }

    // -------------------------------------------------------
    // BOOT
    // -------------------------------------------------------

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($request) {
            $request->request_number = 'REQ-' . now()->format('Ymd') . '-' . str_pad(
                static::whereDate('created_at', now())->count() + 1,
                4,
                '0',
                STR_PAD_LEFT
            );
        });
    }
}




////versi lama
// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

// class Request extends Model
// {
//     use HasFactory, SoftDeletes;

//     protected $guarded = [];

//     protected function casts(): array
//     {
//         return [
//             'approved_at'  => 'datetime',
//             'completed_at' => 'datetime',
//         ];
//     }

//     // -------------------------------------------------------
//     // SCOPES
//     // -------------------------------------------------------

//     public function scopePending($query)
//     {
//         return $query->where('status', 'pending');
//     }
//     public function scopeApproved($query)
//     {
//         return $query->where('status', 'approved');
//     }
//     public function scopeRejected($query)
//     {
//         return $query->where('status', 'rejected');
//     }
//     public function scopeProcessing($query)
//     {
//         return $query->where('status', 'processing');
//     }
//     public function scopeCompleted($query)
//     {
//         return $query->where('status', 'completed');
//     }

//     // -------------------------------------------------------
//     // RELATIONS
//     // -------------------------------------------------------

//     /** User yang membuat permintaan ini */
//     public function user()
//     {
//         return $this->belongsTo(User::class);
//     }

//     /** Gudang tujuan permintaan stok */
//     public function warehouse()
//     {
//         return $this->belongsTo(Warehouse::class);
//     }

//     /** User yang meng-approve permintaan ini */
//     public function approvedBy()
//     {
//         return $this->belongsTo(User::class, 'approved_by');
//     }

//     /** Item-item produk dalam permintaan ini */
//     public function items()
//     {
//         return $this->hasMany(RequestItem::class, 'request_id');
//     }

//     /** Pergerakan stok yang dipicu oleh permintaan ini */
//     public function stockMovements()
//     {
//         return $this->hasMany(StockMovement::class, 'reference_id')
//             ->where('reference_type', 'request');
//     }


//     protected static function boot()
//     {
//         parent::boot();

//         static::creating(function ($request) {
//             $request->request_number = 'REQ-' . now()->format('Ymd') . '-' . str_pad(
//                 static::whereDate('created_at', now())->count() + 1,
//                 4,
//                 '0',
//                 STR_PAD_LEFT
//             );
//         });
//     }
// }

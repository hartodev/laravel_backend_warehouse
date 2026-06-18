<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;
       public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Static helper — catat aktivitas dari mana saja.
     *
     * Contoh:
     *   ActivityLog::record('create', 'Product', $product->id, 'Membuat produk baru');
     *   ActivityLog::record('update', 'Product', $product->id, null, $oldData, $newData);
     */
    public static function record(
        string  $activity,
        string  $module,
        ?int    $subjectId   = null,
        ?string $description = null,
        ?array  $oldValues   = null,
        ?array  $newValues   = null
    ): void {
        if (! auth()->check()) return;

        static::create([
            'user_id'     => auth()->id(),
            'activity'    => $activity,
            'module'      => $module,
            'subject_id'  => $subjectId,
            'description' => $description,
            'old_values'  => $oldValues,
            'new_values'  => $newValues,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);
    }
}





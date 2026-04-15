<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Request extends Model
{
     use HasFactory, SoftDeletes;
 
    protected $guarded = [];
 
    protected $casts = [
        'approved_at'  => 'datetime',
        'completed_at' => 'datetime',
    ];
 
    public function user()
    {
        return $this->belongsTo(User::class);
    }
 
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
 
    public function items()
    {
        return $this->hasMany(RequestItem::class);
    }
 
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
 
    // ── Helpers ──────────────────────────────────────────────
    public function isPending(): bool    { return $this->status === 'pending'; }
    public function isApproved(): bool   { return $this->status === 'approved'; }
    public function isRejected(): bool   { return $this->status === 'rejected'; }
    public function isProcessing(): bool { return $this->status === 'processing'; }
    public function isCompleted(): bool  { return $this->status === 'completed'; }
}

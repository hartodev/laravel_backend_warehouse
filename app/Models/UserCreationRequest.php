<?php
// app/Models/UserCreationRequest.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCreationRequest extends Model
{
    protected $guarded = [];

    protected $hidden = ['password'];

    // ============================================================
    // RELATIONS
    // ============================================================

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    // ============================================================
    // SCOPES
    // ============================================================

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    // ];

    protected $guarded = [];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // ============================================================
    // SCOPES
    // ============================================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    // ============================================================
    // HELPERS
    // ============================================================

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    // ============================================================
    // RELATIONS
    // ============================================================

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

public function requests()
{
    return $this->hasMany(RequestItem::class, 'user_id');
}

public function approvedRequests()
{
    return $this->hasMany(RequestItem::class, 'approved_by');
}
    public function productSubmissions()
    {
        return $this->hasMany(ProductSubmission::class, 'admin_id');
    }

    public function createdPurchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'created_by');
    }

    public function approvedPurchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'approved_by');
    }

    public function createdSalesOrders()
    {
        return $this->hasMany(SalesOrder::class, 'created_by');
    }

    public function stockTransfersRequested()
    {
        return $this->hasMany(StockTransfer::class, 'requested_by');
    }

    public function stockTransfersReceived()
    {
        return $this->hasMany(StockTransfer::class, 'received_by');
    }

    public function stockOpnames()
    {
        return $this->hasMany(StockOpname::class, 'created_by');
    }

    public function budgetRequests()
    {
        return $this->hasMany(BudgetRequest::class, 'user_id');
    }

    // Chat sebagai user_one atau user_two
    public function chatsAsOne()
    {
        return $this->hasMany(Chat::class, 'user_one_id');
    }

    public function chatsAsTwo()
    {
        return $this->hasMany(Chat::class, 'user_two_id');
    }

    /**
     * Semua percakapan user ini (baik sebagai user_one maupun user_two)
     */
    public function chats()
    {
        return Chat::where('user_one_id', $this->id)
            ->orWhere('user_two_id', $this->id);
    }

    public function sentMessages()
    {
        return $this->hasMany(ChatMessage::class, 'sender_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'created_by');
    }

    public function cashBooks()
    {
        return $this->hasMany(CashBook::class, 'created_by');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'created_by');
    }

    // ── Kirim ke semua user dengan role tertentu (mis. semua admin) ──
    public static function sendToRole(string $role, string $type, string $title, string $body, array $data = []): void
    {
        $userIds = User::where('role', $role)
            ->where('is_active', true)
            ->pluck('id');

        foreach ($userIds as $userId) {
            static::send($userId, $type, $title, $body, $data);
        }
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);   // ✅
    }

    public function request_items()
    {
        return $this->belongsTo(RequestItem::class);
    }
}

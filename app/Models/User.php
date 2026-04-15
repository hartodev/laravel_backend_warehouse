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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
     protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
    ];
 
    // ── Role Helpers ─────────────────────────────────────────
    public function isSuperAdmin(): bool { return $this->role === 'super_admin'; }
    public function isAdmin(): bool      { return $this->role === 'admin'; }
    public function isUser(): bool       { return $this->role === 'user'; }
 
    // ── Relations ─────────────────────────────────────────────
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }
 
    public function requests()
    {
        return $this->hasMany(Request::class);
    }
 
    public function approvedRequests()
    {
        return $this->hasMany(Request::class, 'approved_by');
    }
 
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'created_by');
    }
 
    public function productSubmissions()
    {
        return $this->hasMany(ProductSubmission::class, 'admin_id');
    }
 
    public function approvedProductSubmissions()
    {
        return $this->hasMany(ProductSubmission::class, 'approved_by');
    }
 
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'created_by');
    }
 
    public function approvedPurchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'approved_by');
    }
 
    public function stockTransfersRequested()
    {
        return $this->hasMany(StockTransfer::class, 'requested_by');
    }
 
    public function stockTransfersApproved()
    {
        return $this->hasMany(StockTransfer::class, 'approved_by');
    }
 
    public function stockTransfersReceived()
    {
        return $this->hasMany(StockTransfer::class, 'received_by');
    }
 
    public function stockOpnames()
    {
        return $this->hasMany(StockOpname::class, 'created_by');
    }
 
    public function approvedStockOpnames()
    {
        return $this->hasMany(StockOpname::class, 'approved_by');
    }
 
    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class, 'created_by');
    }
 
    public function approvedSalesOrders()
    {
        return $this->hasMany(SalesOrder::class, 'approved_by');
    }
 
    public function payments()
    {
        return $this->hasMany(Payment::class, 'created_by');
    }
 
    public function verifiedPayments()
    {
        return $this->hasMany(Payment::class, 'verified_by');
    }
 
    public function cashBooks()
    {
        return $this->hasMany(CashBook::class, 'created_by');
    }
 
    public function verifiedCashBooks()
    {
        return $this->hasMany(CashBook::class, 'verified_by');
    }
 
    public function budgetRequests()
    {
        return $this->hasMany(BudgetRequest::class);
    }
 
    public function managedBudgetRequests()
    {
        return $this->hasMany(BudgetRequest::class, 'branch_manager_id');
    }
 
    public function financeBudgetRequests()
    {
        return $this->hasMany(BudgetRequest::class, 'finance_id');
    }
 
    public function budgetVerifications()
    {
        return $this->hasMany(BudgetVerification::class, 'finance_id');
    }
 
    public function expenseReports()
    {
        return $this->hasMany(ExpenseReport::class, 'submitted_by');
    }
 
    public function verifiedExpenseReports()
    {
        return $this->hasMany(ExpenseReport::class, 'verified_by');
    }
 
    public function budgetRevisions()
    {
        return $this->hasMany(BudgetRevision::class, 'created_by');
    }
 
    public function approvedBudgetRevisions()
    {
        return $this->hasMany(BudgetRevision::class, 'approved_by');
    }
 
    public function sentChats()
    {
        return $this->hasMany(Chat::class, 'sender_id');
    }
 
    public function receivedChats()
    {
        return $this->hasMany(Chat::class, 'receiver_id');
    }
 
    public function chatMessages()
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
 
    public function barcodeLogs()
    {
        return $this->hasMany(BarcodeLog::class);
    }
}

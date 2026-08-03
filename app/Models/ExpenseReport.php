<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseReport extends Model
{
    use HasFactory;
  protected $guarded = [];

    protected $casts = [
        'tanggal_transaksi'   => 'date',
        'nominal_realisasi'   => 'float',
        'selisih'             => 'float',
        'lamp_invoice'        => 'boolean',
        'lamp_bukti_transfer' => 'boolean',
        'lamp_kartu_garansi'  => 'boolean',
        'lamp_serah_terima'   => 'boolean',
        'attachment_files'    => 'array',
        'verified_at'         => 'datetime',
    ];

    public function budgetRequest()
    {
        return $this->belongsTo(BudgetRequest::class);
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function revision()
    {
        return $this->hasOne(BudgetRevision::class);
    }

    // // ── Helpers ──────────────────────────────────────────────
    // public function calculateSelisih(): void
    // {
    //     $estimasi      = (float) ($this->budgetRequest?->estimasi_biaya ?? 0);
    //     $this->selisih = $estimasi - $this->nominal_realisasi;
    // }

    public function isOverBudget(): bool
    {
        return $this->selisih < 0;
    }

    public function calculateSelisih(): void
    {
        $estimasi      = (float) ($this->budgetRequest?->total_estimasi ?? 0);
        $this->selisih = $estimasi - $this->nominal_realisasi;
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatMessage extends Model
{
   use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function markAsRead(): void
    {
        if (! $this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
     protected $guarded = [];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at');
    }

    public function latestMessage()
    {
        return $this->hasOne(ChatMessage::class)->latestOfMany();
    }

    public function unreadCount(int $userId): int
    {
        return $this->messages()
                    ->where('sender_id', '!=', $userId)
                    ->where('is_read', false)
                    ->count();
    }

    public function userOne()
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }
    public function userTwo()
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $name
 * @property string|null $image
 * @property string|null $description
 */

class Category extends Model
{
      use HasFactory, SoftDeletes;

    // protected $guarded = [];

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'icon',
        'image',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function productSubmissions()
    {
        return $this->hasMany(ProductSubmission::class);
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashBook extends Model
{
    use HasFactory;
     protected $guarded = [];

    protected $casts = [
        'jumlah_uang' => 'decimal:2',
        'tanggal'     => 'date',
        'verified_at' => 'datetime',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // ── Helpers ──────────────────────────────────────────────
    public function isMasuk(): bool  { return $this->type === 'masuk'; }
    public function isKeluar(): bool { return $this->type === 'keluar'; }

}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class BudgetVerification extends Model
{
    use HasFactory;
      protected $guarded = [];

    protected $casts = [
        'doc_form_lengkap'       => 'boolean',
        'doc_surat_justifikasi'  => 'boolean',
        'doc_estimasi_vendor'    => 'boolean',
        'doc_spesifikasi_teknis' => 'boolean',
        'nominal_rekomendasi'    => 'decimal:2',
        'verified_at'            => 'datetime',
    ];

    public function budgetRequest()
    {
        return $this->belongsTo(BudgetRequest::class);
    }

    public function finance()
    {
        return $this->belongsTo(User::class, 'finance_id');
    }

    // ── Helpers ──────────────────────────────────────────────
    public function allDocsComplete(): bool
    {
        return $this->doc_form_lengkap
            && $this->doc_surat_justifikasi
            && $this->doc_estimasi_vendor
            && $this->doc_spesifikasi_teknis;
    }
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'finance_id');
    }
}




<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Services\CashBookService;

class BudgetRevision extends Model
{
    use HasFactory;
      protected $guarded = [];

    protected $casts = [
        'anggaran_awal'     => 'float',
        'realisasi'         => 'float',
        'nominal_perubahan' => 'float',
        'anggaran_baru'     => 'decimal:2',
        'approved_at'       => 'datetime',
    ];

    public function budgetRequest()
    {
        return $this->belongsTo(BudgetRequest::class);
    }

    public function expenseReport()
    {
        return $this->belongsTo(ExpenseReport::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ── Helpers ──────────────────────────────────────────────

    /**
     * Hitung anggaran_baru — return float.
     * Assign hasilnya di controller:
     *   $revision->anggaran_baru = $revision->hitungAnggaranBaru();
     */
    public function hitungAnggaranBaru(): float
    {
        $sisa = (float) $this->anggaran_awal - (float) $this->realisasi;

        return $this->jenis_perubahan === 'tambahan'
            ? $sisa - (float) $this->nominal_perubahan
            : $sisa + (float) $this->nominal_perubahan;
    }

    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }

    public function danaMencukupi(): bool
    {
        if ($this->jenis_perubahan !== 'tambahan') {
            return true; // pengurangan tidak butuh tambahan dana
        }

        return (float) $this->nominal_perubahan <= CashBookService::saldoKas();
    }

    public function evaluateAndApply(): bool
    {
        if (! $this->danaMencukupi()) {
            $this->status = 'pending';
            $this->save();
            return false;
        }

        DB::transaction(function () {
            $this->applyToBudget();
            $this->status      = 'approved_revisi';
            $this->approved_at = now();
            $this->save();
        });

        return true;
    }


    public function applyToBudget(): void
    {
        $budgetRequest = $this->budgetRequest;

        if (! $budgetRequest) {
            return;
        }

        $budgetRequest->update([
            'total_estimasi' => $this->anggaran_baru,
            'status'         => 'approved_revisi',
        ]);

        if ($this->jenis_perubahan === 'tambahan') {
            CashBookService::record([
                'tanggal'           => now()->toDateString(),
                'keterangan'        => "Alokasi Tambahan Revisi RAB #{$budgetRequest->nomor_form} — {$this->alasan_revisi}",
                'jenis'             => 'alokasi_revisi',
                'jumlah_uang'       => $this->nominal_perubahan,
                'pihak'             => $budgetRequest->divisi,
                'tipe'              => 'masuk',
                'budget_request_id' => $budgetRequest->id,
                'created_by'        => $this->created_by,
                'catatan'           => "Revisi anggaran #{$this->id}",
            ]);
        }
    }
}




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
<?php
// app/Models/BudgetRequest.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BudgetRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'tanggal_pengajuan'  => 'date',
        'branch_manager_at'  => 'datetime',
        'finance_at'         => 'datetime',
        'total_estimasi'     => 'decimal:2',
        'total_realisasi'    => 'decimal:2',
    ];

    // ─────────────────────────────────────────────────────────────
    // Relasi
    // ─────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BudgetRequestItem::class);
    }

    /** Admin yang melakukan review pertama */
    public function adminApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_approver_id');
    }

    /** Super admin yang melakukan approval final */
    public function superAdminApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'super_admin_approver_id');
    }

    public function branchManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'branch_manager_id');
    }

    public function finance(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finance_id');
    }

    public function verification(): HasOne
    {
        return $this->hasOne(BudgetVerification::class);
    }

    public function expenseReport(): HasOne
    {
        return $this->hasOne(ExpenseReport::class);
    }

    public function revision(): HasOne
    {
        return $this->hasOne(BudgetRevision::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function cashBooks(): HasMany
    {
        return $this->hasMany(CashBook::class);
    }

    // ─────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────

    public function isRab(): bool      { return $this->jenis === 'rab'; }
    public function isLuarRab(): bool  { return $this->jenis === 'luar_rab'; }
    public function isDraft(): bool    { return $this->status === 'draft'; }
    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isMendesak(): bool { return $this->urgensi === 'mendesak'; }

    public function recalculateTotal(): void
    {
        $this->update(['total_estimasi' => $this->items()->sum('total')]);
    }

    public function getSisaAnggaranAttribute(): float
    {
        return (float) $this->total_estimasi - (float) ($this->total_realisasi ?? 0);
    }

    public function getIsUrgentAttribute(): bool
    {
        return $this->urgensi === 'mendesak';
    }

    /**
     * Generate nomor form unik
     * Format: RAB/DIV/2024/001 atau LRAB/DIV/2024/001
     */
    public static function generateNomorForm(string $jenis, string $divisi): string
    {
        $prefix   = $jenis === 'rab' ? 'RAB' : 'LRAB';
        $divKode  = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $divisi), 0, 4));
        $year     = now()->format('Y');
        $month    = now()->format('m');

        $lastCount = static::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('jenis', $jenis)
            ->count();

        $seq = str_pad($lastCount + 1, 3, '0', STR_PAD_LEFT);

        return "{$prefix}/{$divKode}/{$year}{$month}/{$seq}";
    }

    public function budgetVerifications()
{
    return $this->hasMany(BudgetVerification::class);
}

public function budgetRevisions()
{
    return $this->hasMany(BudgetRevision::class);
}
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarcodeLog extends Model
{
    use HasFactory;
      public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'is_found'   => 'boolean',
        'scanned_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
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





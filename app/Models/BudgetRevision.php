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
        // Anggaran baru = anggaran awal (total RAB saat ini) ± nominal perubahan.
        // tambahan -> naik, pengurangan -> turun. Realisasi tidak dikurangkan
        // karena nilai ini disimpan kembali sebagai total_estimasi RAB.
        $awal    = (float) $this->anggaran_awal;
        $nominal = (float) $this->nominal_perubahan;

        return $this->jenis_perubahan === 'tambahan'
            ? $awal + $nominal
            : $awal - $nominal;
    }

    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }

    public function danaMencukupi(): bool
    {
        if ($this->jenis_perubahan !== 'tambahan') {
            return true; // pengurangan tidak butuh tambahan dana
        }

        // RAB yang baru disetujui admin (menunggu Super Admin) belum mengalokasikan
        // dana ke kas, jadi revisi hanya mengubah nilai RAB — tidak perlu cek saldo kas.
        if ($this->budgetRequest?->status === 'pending_sa') {
            return true;
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

        // RAB masih menunggu Super Admin: ubah nilai saja. Status tetap pending_sa
        // (jangan loncati approval Super Admin) dan belum ada catatan kas.
        if ($budgetRequest->status === 'pending_sa') {
            $budgetRequest->update(['total_estimasi' => $this->anggaran_baru]);
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

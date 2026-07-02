<?php

namespace App\Services;

use App\Models\BudgetRequest;
use App\Models\BudgetRevision;
use App\Models\ExpenseReport;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Service terpusat untuk Laporan Pertanggungjawaban (ExpenseReport).
 *
 * Menggabungkan 3 hal yang tadinya manual/terpisah:
 *  1. Catat cash_books (dulu: BudgetRequestController::realisasi())
 *  2. Update total_realisasi di RAB
 *  3. Kalau nominal realisasi > sisa anggaran -> OTOMATIS ajukan
 *     BudgetRevision (tambahan). Kalau saldo kas cukup, revisi
 *     langsung diterapkan & laporan langsung final. Kalau saldo
 *     kas belum cukup, laporan nyangkut 'pending_revisi' sampai
 *     revisi itu disetujui manual oleh Admin/SA.
 */
class ExpenseReportService
{
    /**
     * @throws ValidationException jika RAB belum berstatus disetujui
     */
    public static function createFromRequest(BudgetRequest $budgetRequest, array $data, array $attachmentPaths = []): ExpenseReport
    {
        if (! in_array($budgetRequest->status, ['approved', 'approved_revisi'])) {
            throw ValidationException::withMessages([
                'budget_request_id' => 'RAB ini belum berstatus disetujui, belum bisa dibuatkan laporan pertanggungjawaban.',
            ]);
        }

        return DB::transaction(function () use ($budgetRequest, $data, $attachmentPaths) {
            $sisaAnggaran = $budgetRequest->total_estimasi - ($budgetRequest->total_realisasi ?? 0);
            $nominal      = (float) $data['nominal_realisasi'];
            $kekurangan   = round($nominal - $sisaAnggaran, 2);

            $er = ExpenseReport::create([
                'budget_request_id'   => $budgetRequest->id,
                'submitted_by'        => auth()->id(),
                'nomor_invoice'       => $data['nomor_invoice'] ?? null,
                'nama_vendor'         => $data['nama_vendor'] ?? null,
                'tanggal_transaksi'   => $data['tanggal_transaksi'],
                'nominal_realisasi'   => $nominal,
                'selisih'             => $budgetRequest->total_estimasi - $nominal,
                'lamp_invoice'        => $data['lamp_invoice'] ?? false,
                'lamp_bukti_transfer' => $data['lamp_bukti_transfer'] ?? false,
                'lamp_kartu_garansi'  => $data['lamp_kartu_garansi'] ?? false,
                'lamp_serah_terima'   => $data['lamp_serah_terima'] ?? false,
                'lamp_lainnya'        => $data['lamp_lainnya'] ?? null,
                'attachment_files'    => $attachmentPaths,
                'catatan'             => $data['catatan'] ?? null,
                'status'              => $kekurangan > 0 ? 'pending_revisi' : 'submitted',
            ]);

            if ($kekurangan > 0) {
                $revision = new BudgetRevision([
                    'budget_request_id' => $budgetRequest->id,
                    'expense_report_id' => $er->id,
                    'akun_terdampak'    => $budgetRequest->nama_akun ?? $budgetRequest->divisi,
                    'kode_akun'         => $budgetRequest->kode_akun,
                    'anggaran_awal'     => $budgetRequest->total_estimasi,
                    'realisasi'         => $budgetRequest->total_realisasi ?? 0,
                    'jenis_perubahan'   => 'tambahan',
                    'nominal_perubahan' => $kekurangan,
                    'alasan_revisi'     => "Otomatis: nominal realisasi Laporan Pertanggungjawaban #{$er->id} " .
                        '(Rp ' . number_format($nominal, 0, ',', '.') . ') melebihi sisa anggaran ' .
                        '(Rp ' . number_format($sisaAnggaran, 0, ',', '.') . ').',
                ]);
                $revision->created_by    = auth()->id();
                $revision->status        = 'pending';
                $revision->anggaran_baru = $revision->hitungAnggaranBaru();
                $revision->save();

                // Coba terapkan otomatis (cek saldo kas cukup atau tidak)
                $applied = $revision->evaluateAndApply();

                if ($applied) {
                    // Saldo cukup -> revisi langsung approved_revisi,
                    // laporan langsung difinalisasi juga.
                    self::finalizeRealisasi($er->fresh());
                }
                // Kalau belum applied: revisi tetap 'pending', laporan
                // tetap 'pending_revisi' sampai di-approve manual
                // (lihat BudgetRevisionController::approve()).
            } else {
                self::finalizeRealisasi($er);
            }

            return $er->fresh(['budgetRequest', 'revision']);
        });
    }

    /**
     * Catat realisasi ke cash_books + tambah total_realisasi RAB +
     * ubah status laporan jadi 'submitted'. Dipanggil baik langsung
     * (kalau anggaran cukup) maupun belakangan setelah revisi terkait
     * disetujui manual.
     */
    public static function finalizeRealisasi(ExpenseReport $er): void
    {
        if (in_array($er->status, ['submitted', 'verified'])) {
            return; // sudah difinalisasi, jangan dobel catat
        }

        $budgetRequest = $er->budgetRequest()->first();

        CashBookService::record([
            'tanggal'           => $er->tanggal_transaksi,
            'keterangan'        => "Realisasi RAB #{$budgetRequest->nomor_form}" .
                ($er->nama_vendor ? " — {$er->nama_vendor}" : ''),
            'jenis'             => 'realisasi_rab',
            'jumlah_uang'       => $er->nominal_realisasi,
            'pihak'             => $er->nama_vendor ?? $budgetRequest->divisi,
            'tipe'              => 'keluar',
            'budget_request_id' => $budgetRequest->id,
            'created_by'        => $er->submitted_by,
            'catatan'           => "Laporan Pertanggungjawaban #{$er->id}" .
                ($er->nomor_invoice ? " — Invoice: {$er->nomor_invoice}" : ''),
        ]);

        $budgetRequest->increment('total_realisasi', $er->nominal_realisasi);

        $er->update([
            'status'  => 'submitted',
            'selisih' => $budgetRequest->fresh()->total_estimasi - $er->nominal_realisasi,
        ]);
    }
}

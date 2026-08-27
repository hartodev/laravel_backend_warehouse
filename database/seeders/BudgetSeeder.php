<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BudgetSeeder extends Seeder
{
    /**
     * Seed budget_requests, budget_request_items, budget_verifications,
     * expense_reports, budget_revisions.
     * Butuh users.
     */
    public function run(): void
    {
        $userIds = DB::table('users')->pluck('id')->all();
        $branchManagerIds = DB::table('users')->whereIn('role', ['admin', 'super_admin'])->pluck('id')->all();
        $financeIds = DB::table('users')->whereIn('role', ['admin', 'super_admin'])->pluck('id')->all();

        $divisions = ['Gudang', 'Operasional', 'Keuangan', 'HRD', 'IT', 'Marketing'];
        $statuses = ['draft', 'pending', 'pending_sa', 'approved', 'approved_revisi', 'ditunda', 'ditolak'];

        foreach (range(1, 18) as $i) {
            $status = fake()->randomElement($statuses);
            $jenis = fake()->randomElement(['rab', 'luar_rab']);
            $tanggalPengajuan = now()->subDays(fake()->numberBetween(1, 90));

            $isBranchManagerStage = ! in_array($status, ['draft']);
            $isFinanceStage = in_array($status, ['pending_sa', 'approved', 'approved_revisi', 'ditunda', 'ditolak']);
            $isApproved = in_array($status, ['approved', 'approved_revisi']);

            $totalEstimasi = fake()->numberBetween(500000, 50000000);
            $totalRealisasi = $isApproved ? (int) ($totalEstimasi * fake()->randomFloat(2, 0.7, 1)) : 0;

            $budgetRequestId = DB::table('budget_requests')->insertGetId([
                'nomor_form' => 'BR-' . date('Ym', $tanggalPengajuan->timestamp) . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'user_id' => fake()->randomElement($userIds),
                'divisi' => fake()->randomElement($divisions),
                'tanggal_pengajuan' => $tanggalPengajuan->toDateString(),
                'jenis' => $jenis,
                'kode_akun' => $jenis === 'rab' ? fake()->bothify('AKN-###') : null,
                'nama_akun' => $jenis === 'rab' ? fake('id_ID')->words(2, true) : null,
                'alasan_luar_rab' => $jenis === 'luar_rab' ? fake('id_ID')->sentence() : null,
                'urgensi' => $jenis === 'luar_rab' ? fake()->randomElement(['normal', 'mendesak']) : 'normal',
                'dampak_jika_tidak' => $jenis === 'luar_rab' ? fake('id_ID')->sentence() : null,
                'sumber_dana' => $jenis === 'luar_rab' ? fake()->randomElement(['realokasi', 'tambahan', 'lainnya']) : null,
                'total_estimasi' => $totalEstimasi,
                'total_realisasi' => $totalRealisasi,
                'keterangan' => fake()->boolean(40) ? fake('id_ID')->sentence() : null,
                'status' => $status,
                'branch_manager_id' => $isBranchManagerStage ? fake()->randomElement($branchManagerIds) : null,
                'branch_manager_at' => $isBranchManagerStage ? $tanggalPengajuan->copy()->addDay() : null,
                'catatan_branch_manager' => $isBranchManagerStage && fake()->boolean(30) ? fake('id_ID')->sentence() : null,
                'finance_id' => $isFinanceStage ? fake()->randomElement($financeIds) : null,
                'finance_at' => $isFinanceStage ? $tanggalPengajuan->copy()->addDays(2) : null,
                'submitted_at' => $status !== 'draft' ? $tanggalPengajuan : null,
                'created_at' => $tanggalPengajuan,
                'updated_at' => now(),
            ]);

            $this->seedBudgetRequestItems($budgetRequestId);

            if ($isFinanceStage) {
                $this->seedBudgetVerification($budgetRequestId, $financeIds, $tanggalPengajuan);
            }

            if ($isApproved) {
                $expenseReportId = $this->seedExpenseReport($budgetRequestId, $userIds, $financeIds, $totalRealisasi, $tanggalPengajuan);

                if (fake()->boolean(25)) {
                    $this->seedBudgetRevision($budgetRequestId, $expenseReportId, $userIds, $branchManagerIds, $totalEstimasi, $totalRealisasi);
                }
            }
        }
    }

    protected function seedBudgetRequestItems(int $budgetRequestId): void
    {
        $rows = [];
        foreach (range(1, fake()->numberBetween(1, 4)) as $i) {
            $qty = fake()->numberBetween(1, 20);
            $estimasiBiaya = fake()->numberBetween(50000, 5000000);

            $rows[] = [
                'budget_request_id' => $budgetRequestId,
                'nama_item' => ucfirst(fake('id_ID')->words(3, true)),
                'qty' => $qty,
                'satuan' => fake()->randomElement(['pcs', 'unit', 'paket', 'set']),
                'estimasi_biaya' => $estimasiBiaya,
                'total' => $qty * $estimasiBiaya,
                'keterangan' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('budget_request_items')->insert($rows);
    }

    protected function seedBudgetVerification(int $budgetRequestId, array $financeIds, $tanggalPengajuan): void
    {
        DB::table('budget_verifications')->insert([
            'budget_request_id' => $budgetRequestId,
            'finance_id' => fake()->randomElement($financeIds),
            'doc_form_lengkap' => fake()->boolean(90),
            'doc_surat_justifikasi' => fake()->boolean(70),
            'doc_estimasi_vendor' => fake()->boolean(60),
            'doc_spesifikasi_teknis' => fake()->boolean(50),
            'doc_lainnya' => fake()->boolean(20) ? fake('id_ID')->sentence() : null,
            'cek_anggaran' => fake('id_ID')->sentence(),
            'analisa_cashflow' => fake('id_ID')->sentence(),
            'rekomendasi' => fake()->randomElement(['setuju', 'tunda', 'tolak']),
            'nominal_rekomendasi' => fake()->numberBetween(500000, 50000000),
            'catatan_finance' => fake()->boolean(30) ? fake('id_ID')->sentence() : null,
            'verified_at' => $tanggalPengajuan->copy()->addDays(2),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function seedExpenseReport(
        int $budgetRequestId,
        array $userIds,
        array $financeIds,
        int $totalRealisasi,
        $tanggalPengajuan
    ): int {
        $status = fake()->randomElement(['draft', 'submitted', 'pending_revisi', 'verified', 'rejected']);
        $isVerified = $status === 'verified';

        return DB::table('expense_reports')->insertGetId([
            'budget_request_id' => $budgetRequestId,
            'submitted_by' => fake()->randomElement($userIds),
            'verified_by' => $isVerified ? fake()->randomElement($financeIds) : null,
            'nomor_invoice' => fake()->bothify('INV-#####'),
            'nama_vendor' => fake('id_ID')->company(),
            'tanggal_transaksi' => $tanggalPengajuan->copy()->addDays(5)->toDateString(),
            'nominal_realisasi' => $totalRealisasi ?: fake()->numberBetween(500000, 40000000),
            'selisih' => fake()->numberBetween(-500000, 500000),
            'lamp_invoice' => fake()->boolean(80),
            'lamp_bukti_transfer' => fake()->boolean(70),
            'lamp_kartu_garansi' => fake()->boolean(30),
            'lamp_serah_terima' => fake()->boolean(40),
            'lamp_lainnya' => null,
            'attachment_files' => json_encode(['invoice.pdf', 'bukti-transfer.jpg']),
            'status' => $status,
            'catatan' => fake()->boolean(30) ? fake('id_ID')->sentence() : null,
            'verified_at' => $isVerified ? $tanggalPengajuan->copy()->addDays(7) : null,
            'created_at' => $tanggalPengajuan->copy()->addDays(5),
            'updated_at' => now(),
        ]);
    }

    protected function seedBudgetRevision(
        int $budgetRequestId,
        int $expenseReportId,
        array $userIds,
        array $approverIds,
        int $anggaranAwal,
        int $realisasi
    ): void {
        $jenisPerubahan = fake()->randomElement(['tambahan', 'pengurangan']);
        $nominalPerubahan = fake()->numberBetween(100000, 5000000);
        $anggaranBaru = $jenisPerubahan === 'tambahan' ? $anggaranAwal + $nominalPerubahan : max(0, $anggaranAwal - $nominalPerubahan);
        $status = fake()->randomElement(['pending', 'approved', 'approved_revisi', 'ditunda', 'ditolak']);
        $isApproved = in_array($status, ['approved', 'approved_revisi']);

        DB::table('budget_revisions')->insert([
            'budget_request_id' => $budgetRequestId,
            'expense_report_id' => $expenseReportId,
            'created_by' => fake()->randomElement($userIds),
            'approved_by' => $isApproved ? fake()->randomElement($approverIds) : null,
            'akun_terdampak' => fake('id_ID')->words(2, true),
            'kode_akun' => fake()->bothify('AKN-###'),
            'anggaran_awal' => $anggaranAwal,
            'realisasi' => $realisasi,
            'jenis_perubahan' => $jenisPerubahan,
            'nominal_perubahan' => $nominalPerubahan,
            'anggaran_baru' => $anggaranBaru,
            'alasan_revisi' => fake('id_ID')->sentence(),
            'status' => $status,
            'catatan_approver' => $isApproved && fake()->boolean(30) ? fake('id_ID')->sentence() : null,
            'approved_at' => $isApproved ? now()->subDays(fake()->numberBetween(1, 10)) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    /**
     * Seed payments + cash_books.
     * Butuh users, purchase_orders, sales_orders, budget_requests sudah ada.
     */
    public function run(): void
    {
        $creatorIds = DB::table('users')->whereIn('role', ['admin', 'staff', 'super_admin'])->pluck('id')->all();
        $verifierIds = DB::table('users')->whereIn('role', ['admin', 'super_admin'])->pluck('id')->all();
        $poIds = DB::table('purchase_orders')->pluck('id')->all();
        $soIds = DB::table('sales_orders')->pluck('id')->all();
        $budgetRequestIds = DB::table('budget_requests')->pluck('id')->all();

        $paymentMethods = ['cash', 'transfer', 'cek', 'giro'];
        $paymentIds = [];

        foreach (range(1, 30) as $i) {
            $paymentType = fake()->randomElement(['masuk', 'keluar']);
            $status = fake()->randomElement(['pending', 'verified', 'cancelled']);
            $paymentDate = now()->subDays(fake()->numberBetween(1, 60));

            // Sumber pembayaran: salah satu dari PO / SO / Budget Request / tidak ada (kwitansi bebas)
            $source = fake()->randomElement(['po', 'so', 'budget', 'none']);
            $nominal = fake()->numberBetween(100000, 20000000);

            $paymentId = DB::table('payments')->insertGetId([
                'payment_number' => 'PAY-' . date('Ym', $paymentDate->timestamp) . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'created_by' => fake()->randomElement($creatorIds),
                'verified_by' => $status === 'verified' ? fake()->randomElement($verifierIds) : null,
                'purchase_order_id' => $source === 'po' && ! empty($poIds) ? fake()->randomElement($poIds) : null,
                'sales_order_id' => $source === 'so' && ! empty($soIds) ? fake()->randomElement($soIds) : null,
                'budget_request_id' => $source === 'budget' && ! empty($budgetRequestIds) ? fake()->randomElement($budgetRequestIds) : null,
                'payment_type' => $paymentType,
                'payment_method' => fake()->randomElement($paymentMethods),
                'nama_pengirim' => $paymentType === 'masuk' ? fake('id_ID')->name() : null,
                'bank_pengirim' => $paymentType === 'masuk' ? fake()->randomElement(['BCA', 'Mandiri', 'BNI']) : null,
                'nama_penerima' => $paymentType === 'keluar' ? fake('id_ID')->name() : null,
                'bank_penerima' => $paymentType === 'keluar' ? fake()->randomElement(['BCA', 'Mandiri', 'BNI']) : null,
                'no_rekening_tujuan' => fake()->numerify('##########'),
                'diterima_dari' => $paymentType === 'masuk' ? fake('id_ID')->name() : null,
                'nominal' => $nominal,
                'untuk_pembayaran' => fake('id_ID')->sentence(),
                'terbilang' => $this->terbilangDummy($nominal),
                'status' => $status,
                'bukti_file' => fake()->boolean(60) ? 'payments/bukti-' . $i . '.jpg' : null,
                'payment_date' => $paymentDate->toDateString(),
                'verified_at' => $status === 'verified' ? $paymentDate->copy()->addDay() : null,
                'keterangan' => fake()->boolean(30) ? fake('id_ID')->sentence() : null,
                'created_at' => $paymentDate,
                'updated_at' => now(),
            ]);

            $paymentIds[] = $paymentId;
        }

        $this->seedCashBooks($creatorIds, $verifierIds, $budgetRequestIds, $paymentIds);
    }

    protected function seedCashBooks(array $creatorIds, array $verifierIds, array $budgetRequestIds, array $paymentIds): void
    {
        $rows = [];
        foreach (range(1, 25) as $i) {
            $type = fake()->randomElement(['masuk', 'keluar']);
            $tanggal = now()->subDays(fake()->numberBetween(1, 60));
            $isVerified = fake()->boolean(70);
            $jumlah = fake()->numberBetween(50000, 15000000);

            $rows[] = [
                'no_bukti' => 'CB-' . date('Ym', $tanggal->timestamp) . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'budget_request_id' => fake()->boolean(30) && ! empty($budgetRequestIds) ? fake()->randomElement($budgetRequestIds) : null,
                'payment_id' => fake()->boolean(50) && ! empty($paymentIds) ? fake()->randomElement($paymentIds) : null,
                'created_by' => fake()->randomElement($creatorIds),
                'verified_by' => $isVerified ? fake()->randomElement($verifierIds) : null,
                'jenis' => fake()->randomElement(['operasional', 'pembelian', 'penjualan', 'lainnya']),
                'tipe' => $type,
                'type' => $type,
                'pihak' => fake('id_ID')->name(),
                'jumlah_uang' => $jumlah,
                'terbilang' => $this->terbilangDummy($jumlah),
                'catatan' => fake()->boolean(30) ? fake('id_ID')->sentence() : null,
                'keterangan' => fake()->boolean(30) ? fake('id_ID')->sentence() : null,
                'tanggal' => $tanggal->toDateString(),
                'verified_at' => $isVerified ? $tanggal->copy()->addDay() : null,
                'created_at' => $tanggal,
                'updated_at' => now(),
            ];
        }

        DB::table('cash_books')->insert($rows);
    }

    /**
     * Terbilang sederhana untuk keperluan seeding (bukan konversi asli angka ke teks).
     */
    protected function terbilangDummy(int $nominal): string
    {
        return 'Sejumlah Rp ' . number_format($nominal, 0, ',', '.') . ' (data dummy untuk testing)';
    }
}

<?php

namespace App\Services;

use App\Models\CashBook;

/**
 * Service terpusat untuk mencatat transaksi ke Buku Kas (cash_books).
 * Dipakai oleh BudgetRequestController (alokasi & realisasi RAB) dan
 * BudgetRevision (alokasi tambahan dari revisi anggaran), supaya logic
 * generate no_bukti & terbilang tidak duplikat di banyak tempat.
 */
class CashBookService
{
    /**
     * Catat satu entri Buku Kas. Field no_bukti, terbilang, dan pihak
     * akan diisi otomatis jika tidak disediakan di $data.
     */
    public static function record(array $data): CashBook
    {
        $data['no_bukti']  = $data['no_bukti']  ?? self::generateNoBukti();
        $data['terbilang'] = $data['terbilang'] ?? (self::terbilang((float) $data['jumlah_uang']) . ' Rupiah');
        $data['pihak']     = $data['pihak']     ?? '-';

        return CashBook::create($data);
    }

    // ─────────────────────────────────────────────────────────────
    // Generate nomor bukti transaksi: CB/YYYYMMDD/0001
    // lockForUpdate() agar aman dari race condition saat ada
    // beberapa transaksi tercatat hampir bersamaan. Method ini
    // WAJIB dipanggil di dalam DB::transaction() oleh pemanggilnya.
    // ─────────────────────────────────────────────────────────────
    public static function generateNoBukti(): string
    {
        $today = now()->format('Ymd');

        $count = CashBook::whereDate('created_at', now())
            ->lockForUpdate()
            ->count();

        return 'CB/' . $today . '/' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    // ─────────────────────────────────────────────────────────────
    // Saldo kas keseluruhan: total masuk - total keluar
    // Dipakai sementara sebagai dasar pengecekan "dana mencukupi"
    // untuk revisi anggaran, sebelum sistem pagu anggaran tahunan
    // per divisi/akun dibuat.
    // ─────────────────────────────────────────────────────────────
    public static function saldoKas(): float
    {
        $masuk  = (float) CashBook::where('tipe', 'masuk')->sum('jumlah_uang');
        $keluar = (float) CashBook::where('tipe', 'keluar')->sum('jumlah_uang');

        return $masuk - $keluar;
    }

    // ─────────────────────────────────────────────────────────────
    // Konversi angka jadi terbilang (Bahasa Indonesia)
    // Contoh: 1650000 -> "Satu Juta Enam Ratus Lima Puluh Ribu"
    // ─────────────────────────────────────────────────────────────
    public static function terbilang(float $angka): string
    {
        $angka = (int) round($angka);

        $satuan = [
            '', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima',
            'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh',
            'Sebelas',
        ];

        if ($angka < 0) {
            return 'Minus ' . self::terbilang(abs($angka));
        }

        if ($angka < 12) {
            return $satuan[$angka];
        }

        if ($angka < 20) {
            return self::terbilang($angka - 10) . ' Belas';
        }

        if ($angka < 100) {
            return trim(self::terbilang(intdiv($angka, 10)) . ' Puluh ' . self::terbilang($angka % 10));
        }

        if ($angka < 200) {
            return trim('Seratus ' . self::terbilang($angka - 100));
        }

        if ($angka < 1000) {
            return trim(self::terbilang(intdiv($angka, 100)) . ' Ratus ' . self::terbilang($angka % 100));
        }

        if ($angka < 2000) {
            return trim('Seribu ' . self::terbilang($angka - 1000));
        }

        if ($angka < 1000000) {
            return trim(self::terbilang(intdiv($angka, 1000)) . ' Ribu ' . self::terbilang($angka % 1000));
        }

        if ($angka < 1000000000) {
            return trim(self::terbilang(intdiv($angka, 1000000)) . ' Juta ' . self::terbilang($angka % 1000000));
        }

        if ($angka < 1000000000000) {
            return trim(self::terbilang(intdiv($angka, 1000000000)) . ' Miliar ' . self::terbilang($angka % 1000000000));
        }

        return trim(self::terbilang(intdiv($angka, 1000000000000)) . ' Triliun ' . self::terbilang($angka % 1000000000000));
    }
}





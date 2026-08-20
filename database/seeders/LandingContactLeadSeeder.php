<?php

namespace Database\Seeders;

use App\Models\LandingContactLead;
use Illuminate\Database\Seeder;

class LandingContactLeadSeeder extends Seeder
{
    /**
     * Data contoh untuk testing tampilan admin (index, filter status, detail).
     * Beda dengan seeder konten landing lain, lead biasanya datang dari submit
     * form asli di halaman publik — jadi seeder ini murni buat kebutuhan testing/demo,
     * bukan data wajib produksi. Aman dihapus/skip kalau tidak diperlukan.
     */
    public function run(): void
    {
        $leads = [
            [
                'name'       => 'Andi Setiawan',
                'email'      => 'andi.setiawan@mitralogistik.co.id',
                'phone'      => '081234567890',
                'company'    => 'PT Mitra Logistik',
                'message'    => 'Halo, kami tertarik dengan StockFlow untuk 3 gudang kami di Jakarta dan Surabaya. Bisa dijadwalkan demo minggu ini?',
                'status'     => LandingContactLead::STATUS_NEW,
                'source'     => 'cta_contact_sales',
                'created_at' => now()->subHours(3),
            ],
            [
                'name'       => 'Sri Wahyuni',
                'email'      => 'sri.wahyuni@tokoindomaju.com',
                'phone'      => '081298765432',
                'company'    => 'Toko Indo Maju',
                'message'    => 'Mau tanya, apakah StockFlow cocok untuk toko retail kecil dengan 2 cabang? Berapa estimasi biayanya?',
                'status'     => LandingContactLead::STATUS_NEW,
                'source'     => 'cta_contact_sales',
                'created_at' => now()->subDay(),
            ],
            [
                'name'       => 'Budi Hartono',
                'email'      => 'budi.hartono@suryaelektronik.id',
                'phone'      => null,
                'company'    => 'Surya Elektronik',
                'message'    => 'Kami sedang evaluasi beberapa vendor WMS. Boleh minta company profile dan pricing detail StockFlow?',
                'status'     => LandingContactLead::STATUS_CONTACTED,
                'source'     => 'cta_contact_sales',
                'admin_note' => 'Sudah dihubungi via telepon, dikirimkan proposal & pricing. Menunggu keputusan internal mereka, follow up lagi minggu depan.',
                'handled_at' => now()->subHours(20),
                'created_at' => now()->subDays(2),
            ],
            [
                'name'       => 'Rina Wijaya',
                'email'      => 'rina.wijaya@fulfillment.tokopedia.com',
                'phone'      => '087711223344',
                'company'    => 'Tokopedia Fulfillment',
                'message'    => 'Kami butuh integrasi API untuk sinkronisasi stok multi-gudang. Apakah StockFlow menyediakan dokumentasi API publik?',
                'status'     => LandingContactLead::STATUS_CONTACTED,
                'source'     => 'cta_contact_sales',
                'admin_note' => 'Diarahkan ke tim technical untuk pembahasan API. Meeting dijadwalkan.',
                'handled_at' => now()->subDay(),
                'created_at' => now()->subDays(3),
            ],
            [
                'name'       => 'Dewi Anggraini',
                'email'      => 'dewi.anggraini@gudangsentosa.co.id',
                'phone'      => '082199887766',
                'company'    => 'Gudang Sentosa',
                'message'    => 'Terima kasih atas demo minggu lalu, kami sudah putuskan untuk lanjut berlangganan paket Business.',
                'status'     => LandingContactLead::STATUS_CLOSED,
                'source'     => 'cta_contact_sales',
                'admin_note' => 'Deal closed — upgrade ke paket Business, onboarding dijadwalkan minggu depan.',
                'handled_at' => now()->subDays(4),
                'created_at' => now()->subDays(7),
            ],
            [
                'name'       => 'Fajar Nugroho',
                'email'      => 'fajar.n@gmail.com',
                'phone'      => null,
                'company'    => null,
                'message'    => 'Cuma mau tanya-tanya dulu, apakah ada versi trial gratis tanpa kartu kredit?',
                'status'     => LandingContactLead::STATUS_CLOSED,
                'source'     => 'cta_contact_sales',
                'admin_note' => 'Sudah dijawab via email, diarahkan untuk daftar trial 14 hari langsung dari landing page.',
                'handled_at' => now()->subDays(6),
                'created_at' => now()->subDays(8),
            ],
        ];

        foreach ($leads as $lead) {
            // created_at bukan bagian dari $fillable di model (sengaja, biar
            // tidak bisa dimanipulasi lewat form biasa), jadi di-set terpisah
            // pakai forceFill supaya tanggal contoh datanya bervariasi.
            $createdAt = $lead['created_at'] ?? null;
            unset($lead['created_at']);

            $record = LandingContactLead::updateOrCreate(
                ['email' => $lead['email'], 'message' => $lead['message']],
                $lead
            );

            if ($createdAt) {
                $record->forceFill(['created_at' => $createdAt])->save();
            }
        }
    }
}

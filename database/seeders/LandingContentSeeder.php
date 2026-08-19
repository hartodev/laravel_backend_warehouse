<?php

namespace Database\Seeders;

use App\Models\LandingFaq;
use App\Models\LandingFeature;
use App\Models\LandingStat;
use App\Models\LandingTestimonial;
use Illuminate\Database\Seeder;

class LandingContentSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Stats ----
        $stats = [
            ['label' => 'Inventory Accuracy', 'target' => 99.9,  'suffix' => '%', 'decimal_places' => 1, 'is_static' => false, 'static_value' => null, 'bar_percentage' => 100, 'order' => 1],
            ['label' => 'Transactions',       'target' => 10000, 'suffix' => '+', 'decimal_places' => 0, 'is_static' => false, 'static_value' => null, 'bar_percentage' => 80,  'order' => 2],
            ['label' => 'Companies',          'target' => 500,   'suffix' => '+', 'decimal_places' => 0, 'is_static' => false, 'static_value' => null, 'bar_percentage' => 65,  'order' => 3],
            ['label' => 'Monitoring',         'target' => null,  'suffix' => null,'decimal_places' => 0, 'is_static' => true,  'static_value' => '24/7', 'bar_percentage' => 100, 'order' => 4],
        ];
        foreach ($stats as $s) {
            LandingStat::updateOrCreate(['label' => $s['label']], $s);
        }

        // ---- Testimonials ----
        $testimonials = [
            [
                'name' => 'Budi Hartono', 'role' => 'Logistics Director · Astra Group',
                'initials' => 'BH', 'avatar_color' => 'blue',
                'quote' => 'StockFlow mengubah cara kami mengelola gudang secara fundamental. Akurasi stok kami naik dari 78% ke 99.7% hanya dalam 3 bulan implementasi. Luar biasa!',
                'rating' => 5, 'is_featured' => false, 'order' => 1,
            ],
            [
                'name' => 'Siti Rahayu', 'role' => 'Supply Chain Manager · Indomaret',
                'initials' => 'SR', 'avatar_color' => 'cyan',
                'quote' => 'ROI positif terasa dalam 2 bulan pertama. Tim kami menghemat 4 jam per hari yang sebelumnya dihabiskan untuk update spreadsheet manual. Sangat direkomendasikan!',
                'rating' => 5, 'is_featured' => true, 'order' => 2,
            ],
            [
                'name' => 'Ahmad Wijaya', 'role' => 'COO · Mitra Gudang Indonesia',
                'initials' => 'AW', 'avatar_color' => 'purple',
                'quote' => 'Dashboard real-time StockFlow memberikan visibilitas penuh atas 8 gudang kami di seluruh Indonesia. Support tim sangat responsif dan profesional!',
                'rating' => 5, 'is_featured' => false, 'order' => 3,
            ],
        ];
        foreach ($testimonials as $t) {
            LandingTestimonial::updateOrCreate(['name' => $t['name'], 'role' => $t['role']], $t);
        }

        // ---- FAQ ----
        $faqs = [
            ['question' => 'Berapa lama proses implementasi StockFlow?', 'answer' => 'Proses implementasi StockFlow rata-rata membutuhkan 3-7 hari kerja tergantung kompleksitas bisnis Anda. Tim onboarding kami akan memandu setiap langkah, mulai dari migrasi data, konfigurasi sistem, hingga pelatihan tim.', 'order' => 1],
            ['question' => 'Apakah StockFlow bisa diintegrasikan dengan sistem yang sudah ada?', 'answer' => 'Ya, StockFlow menyediakan REST API yang terdokumentasi dengan baik dan sudah terintegrasi dengan 50+ platform populer seperti Shopee, Tokopedia, Lazada, SAP, Oracle, dan berbagai sistem akuntansi. Custom integration juga tersedia.', 'order' => 2],
            ['question' => 'Bagaimana keamanan data bisnis saya di StockFlow?', 'answer' => 'Data Anda dilindungi dengan enkripsi AES-256 end-to-end, backup otomatis setiap jam, dan infrastruktur ISO 27001. Server berlokasi di Indonesia dengan uptime SLA 99.9%. Kami juga menyediakan audit log lengkap untuk semua aktivitas.', 'order' => 3],
            ['question' => 'Apakah tersedia versi mobile untuk operator gudang?', 'answer' => 'Ya, StockFlow memiliki aplikasi mobile untuk iOS dan Android yang dirancang khusus untuk operator gudang. Fitur meliputi scan barcode, receive barang, picking, packing, dan stock count yang bisa digunakan secara offline.', 'order' => 4],
            ['question' => 'Berapa biaya langganan StockFlow?', 'answer' => 'StockFlow menawarkan berbagai paket yang fleksibel mulai dari Starter (Rp 500K/bulan) untuk bisnis kecil hingga Enterprise dengan harga custom untuk kebutuhan besar. Semua paket termasuk support 24/7 dan unlimited training. Trial gratis 14 hari tersedia.', 'order' => 5],
            ['question' => 'Apakah bisa mengelola multiple gudang dalam satu akun?', 'answer' => 'Tentu! Fitur Multi-Warehouse StockFlow memungkinkan Anda mengelola puluhan gudang di berbagai lokasi dari satu dashboard terpusat. Tersedia fitur transfer antar gudang, konsolidasi laporan, dan manajemen stok terpusat yang sangat mudah digunakan.', 'order' => 6],
            ['question' => 'Bagaimana proses migrasi data dari sistem lama?', 'answer' => 'Tim migrasi kami berpengalaman menangani data dari berbagai format: Excel, CSV, database SQL, maupun sistem WMS lainnya. Proses migrasi dilakukan secara bertahap dengan validasi di setiap tahap untuk memastikan tidak ada data yang hilang atau terduplikasi.', 'order' => 7],
            ['question' => 'Apakah ada dukungan pelatihan untuk tim kami?', 'answer' => 'Ya! Setiap paket termasuk unlimited training session, dokumentasi lengkap, video tutorial, dan akses ke knowledge base kami. Kami juga menyediakan dedicated account manager untuk paket Business ke atas yang siap membantu tim Anda beradaptasi.', 'order' => 8],
        ];
        foreach ($faqs as $f) {
            LandingFaq::updateOrCreate(['question' => $f['question']], $f);
        }

        // ---- Features ----
        $features = [
            ['icon' => 'zap', 'title' => 'Real-time Sync', 'description' => 'Data tersinkronisasi secara instan di semua perangkat dan lokasi.', 'color' => 'blue', 'order' => 1],
            ['icon' => 'cloud', 'title' => 'Cloud-Based', 'description' => 'Akses dari mana saja tanpa perlu instalasi server lokal yang rumit.', 'color' => 'cyan', 'order' => 2],
            ['icon' => 'smartphone', 'title' => 'Mobile App', 'description' => 'Aplikasi mobile untuk operator gudang yang bekerja di lapangan.', 'color' => 'purple', 'order' => 3],
            ['icon' => 'download', 'title' => 'Export Data', 'description' => 'Export laporan ke Excel, PDF, CSV dengan satu klik mudah.', 'color' => 'green', 'order' => 4],
            ['icon' => 'brain-circuit', 'title' => 'AI Insights', 'description' => 'Prediksi kebutuhan stok berdasarkan pola historis penjualan.', 'color' => 'blue', 'order' => 5],
            ['icon' => 'link', 'title' => 'API Integration', 'description' => 'Integrasi dengan marketplace, ERP, dan sistem akuntansi populer.', 'color' => 'orange', 'order' => 6],
            ['icon' => 'git-merge', 'title' => 'Auto Reorder', 'description' => 'Order otomatis saat stok mencapai batas minimum yang ditentukan.', 'color' => 'cyan', 'order' => 7],
            ['icon' => 'layers', 'title' => 'Batch Tracking', 'description' => 'Kelola batch number dan lot tracking untuk produk perishable.', 'color' => 'purple', 'order' => 8],
            ['icon' => 'map-pin', 'title' => 'Slot Locator', 'description' => 'Temukan lokasi barang di gudang dengan sistem slot yang terstruktur.', 'color' => 'green', 'order' => 9],
            ['icon' => 'repeat', 'title' => 'Stock Opname', 'description' => 'Proses stock opname digital yang efisien dan terdokumentasi dengan baik.', 'color' => 'blue', 'order' => 10],
            ['icon' => 'truck', 'title' => 'Delivery Track', 'description' => 'Pantau status pengiriman barang keluar secara real-time.', 'color' => 'cyan', 'order' => 11],
            ['icon' => 'tag', 'title' => 'Price Management', 'description' => 'Kelola harga beli, jual, dan margin keuntungan setiap produk.', 'color' => 'orange', 'order' => 12],
            ['icon' => 'calendar-check', 'title' => 'Expiry Alert', 'description' => 'Notifikasi otomatis untuk produk yang mendekati tanggal kadaluarsa.', 'color' => 'purple', 'order' => 13],
            ['icon' => 'lock', 'title' => 'Data Security', 'description' => 'Enkripsi end-to-end dan backup otomatis untuk keamanan data bisnis.', 'color' => 'green', 'order' => 14],
            ['icon' => 'headphones', 'title' => '24/7 Support', 'description' => 'Tim support siap membantu Anda kapanpun dibutuhkan tanpa biaya tambahan.', 'color' => 'blue', 'order' => 15],
            ['icon' => 'trending-up', 'title' => 'Growth Analytics', 'description' => 'Pantau pertumbuhan bisnis dan identifikasi peluang optimasi inventori.', 'color' => 'cyan', 'order' => 16],
        ];
        foreach ($features as $f) {
            LandingFeature::updateOrCreate(['title' => $f['title']], $f);
        }
    }
}

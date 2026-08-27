<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LandingPageSeeder extends Seeder
{
    /**
     * Seed semua tabel landing_* (konten CMS untuk halaman marketing).
     * Sebagian besar konten diisi manual (bukan full random) karena ini
     * konten tampilan yang perlu terlihat masuk akal, ditambah beberapa
     * variasi dari Faker.
     */
    public function run(): void
    {
        $this->seedContactLeads();
        $this->seedBenefits();
        $this->seedWorkflowSteps();
        $this->seedHero();
        $this->seedHeroHighlights();
        $this->seedSectionHeaders();
        $this->seedDashboardStats();
        $this->seedDashboardActivities();
        $this->seedDashboardProducts();
        $this->seedCtaFeatures();
        $this->seedSolutions();
        $this->seedLandingStats();
        $this->seedTestimonials();
        $this->seedFaqs();
        $this->seedFeatures();
    }

    protected function seedContactLeads(): void
    {
        $handlerIds = DB::table('users')->whereIn('role', ['admin', 'super_admin'])->pluck('id')->all();
        $rows = [];

        foreach (range(1, 15) as $i) {
            $status = fake()->randomElement(['new', 'contacted', 'closed']);
            $isHandled = $status !== 'new';

            $rows[] = [
                'name' => fake('id_ID')->name(),
                'email' => fake()->unique()->safeEmail(),
                'phone' => fake('id_ID')->phoneNumber(),
                'company' => fake('id_ID')->company(),
                'message' => fake('id_ID')->sentence(15),
                'status' => $status,
                'source' => fake()->randomElement(['cta_contact_sales', 'cta_book_demo', 'footer_form']),
                'admin_note' => $isHandled ? fake('id_ID')->sentence() : null,
                'handled_at' => $isHandled ? now()->subDays(fake()->numberBetween(0, 15)) : null,
                'handled_by' => $isHandled && ! empty($handlerIds) ? fake()->randomElement($handlerIds) : null,
                'created_at' => now()->subDays(fake()->numberBetween(0, 30)),
                'updated_at' => now(),
            ];
        }

        DB::table('landing_contact_leads')->insert($rows);
    }

    protected function seedBenefits(): void
    {
        $benefits = [
            ['title' => 'Inventory Accuracy', 'is_static' => false, 'target' => 99.9, 'suffix' => '%', 'decimal_places' => 1, 'bar_percentage' => 99, 'icon' => 'shield-check', 'is_featured' => true],
            ['title' => 'Produk Terkelola', 'is_static' => false, 'target' => 10000, 'suffix' => '+', 'decimal_places' => 0, 'bar_percentage' => 85, 'icon' => 'package', 'is_featured' => false],
            ['title' => 'Dukungan Pelanggan', 'is_static' => true, 'static_value' => '24 Jam', 'decimal_places' => 0, 'bar_percentage' => 100, 'icon' => 'headset', 'is_featured' => false],
            ['title' => 'Waktu Setup', 'is_static' => true, 'static_value' => '< 1 Hari', 'decimal_places' => 0, 'bar_percentage' => 90, 'icon' => 'zap', 'is_featured' => false],
        ];

        $rows = [];
        foreach ($benefits as $order => $b) {
            $rows[] = array_merge([
                'description' => fake('id_ID')->sentence(12),
                'target' => null,
                'static_value' => null,
                'suffix' => null,
                'decimal_places' => 0,
                'order' => $order,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ], $b);
        }

        DB::table('landing_benefits')->insert($rows);
    }

    protected function seedWorkflowSteps(): void
    {
        $steps = [
            ['title' => 'Catat Barang Masuk', 'icon' => 'package-check', 'color' => 'blue'],
            ['title' => 'Pantau Stok Real-Time', 'icon' => 'activity', 'color' => 'cyan'],
            ['title' => 'Kelola Purchase Order', 'icon' => 'file-text', 'color' => 'purple'],
            ['title' => 'Analisa & Laporan', 'icon' => 'bar-chart-3', 'color' => 'green'],
        ];

        $rows = [];
        foreach ($steps as $order => $s) {
            $rows[] = array_merge($s, [
                'description' => fake('id_ID')->sentence(10),
                'order' => $order,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('landing_workflow_steps')->insert($rows);
    }

    protected function seedHero(): void
    {
        DB::table('landing_heroes')->insert([
            'badge_text' => 'Smart Warehouse Management System',
            'title_line_1' => 'Kelola Gudang',
            'title_line_1_highlight' => 'Lebih Cepat.',
            'title_line_2' => 'Pantau Stok',
            'title_line_2_highlight' => 'Secara Real-Time.',
            'title_line_3' => 'Semua Dalam',
            'title_line_3_highlight' => 'Satu Dashboard.',
            'subtitle' => fake('id_ID')->sentence(20),
            'cta_primary_text' => 'Start Free',
            'cta_primary_url' => '#',
            'cta_secondary_text' => 'Book Demo',
            'cta_secondary_url' => '#',
            'trust_count' => '500+',
            'trust_text' => 'perusahaan mempercayai StockFlow',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function seedHeroHighlights(): void
    {
        $highlights = [
            ['title' => '+125 Barang Masuk', 'subtitle' => 'Hari ini · 14:30', 'icon' => 'trending-up', 'color' => 'blue'],
            ['title' => '-24 Barang Keluar', 'subtitle' => 'Hari ini · 13:10', 'icon' => 'trending-down', 'color' => 'cyan'],
            ['title' => 'PO Disetujui', 'subtitle' => 'Baru saja', 'icon' => 'check-circle', 'color' => 'purple'],
        ];

        $rows = [];
        foreach ($highlights as $order => $h) {
            $rows[] = array_merge($h, [
                'order' => $order,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('landing_hero_highlights')->insert($rows);
    }

    protected function seedSectionHeaders(): void
    {
        $sections = [
            'dashboard' => 'Dashboard yang Memudahkan',
            'solution' => 'Solusi Lengkap untuk Bisnis Anda',
            'contact' => 'Hubungi Tim Kami',
        ];

        $rows = [];
        foreach ($sections as $key => $titleNormal) {
            $rows[] = [
                'section_key' => $key,
                'badge' => ucfirst($key),
                'title_normal' => $titleNormal,
                'title_gradient' => 'Untuk Tim Anda',
                'subtitle' => fake('id_ID')->sentence(15),
                'button_primary_text' => $key === 'contact' ? 'Hubungi Sales' : null,
                'button_primary_url' => $key === 'contact' ? '#' : null,
                'button_secondary_text' => $key === 'contact' ? 'Book Demo' : null,
                'button_secondary_url' => $key === 'contact' ? '#' : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('landing_section_headers')->insert($rows);
    }

    protected function seedDashboardStats(): void
    {
        $stats = [
            ['label' => 'Total Produk', 'value' => '12,847', 'trend_text' => '+12.5% bulan ini', 'trend_direction' => 'up', 'icon' => 'package', 'color' => 'blue'],
            ['label' => 'Nilai Inventori', 'value' => 'Rp 4.2M', 'trend_text' => '+8.1% bulan ini', 'trend_direction' => 'up', 'icon' => 'wallet', 'color' => 'cyan'],
            ['label' => 'Stok Menipis', 'value' => '24', 'trend_text' => '-3.4% bulan ini', 'trend_direction' => 'down', 'icon' => 'alert-triangle', 'color' => 'purple'],
        ];

        $rows = [];
        foreach ($stats as $order => $s) {
            $rows[] = array_merge($s, [
                'order' => $order,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('landing_dashboard_stats')->insert($rows);
    }

    protected function seedDashboardActivities(): void
    {
        $rows = [];
        foreach (range(1, 6) as $order) {
            $rows[] = [
                'title' => 'Barang Masuk #PO-' . fake()->numberBetween(1000, 9999),
                'time_text' => fake()->numberBetween(1, 59) . ' menit lalu',
                'icon' => fake()->randomElement(['arrow-down-to-line', 'arrow-up-from-line', 'check', 'x']),
                'color' => fake()->randomElement(['blue', 'green', 'purple']),
                'value_text' => fake()->randomElement(['+48', '-24', '✓', '!']),
                'value_color' => fake()->randomElement(['green', 'red', 'blue']),
                'order' => $order,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('landing_dashboard_activities')->insert($rows);
    }

    protected function seedDashboardProducts(): void
    {
        $rows = [];
        foreach (range(1, 5) as $i) {
            $stock = fake()->numberBetween(0, 300);
            $status = $stock === 0 ? 'critical' : ($stock < 20 ? 'low' : 'normal');

            $rows[] = [
                'name' => ucfirst(fake('id_ID')->words(2, true)),
                'sku' => 'LPT-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'stock' => $stock,
                'status' => $status,
                'order' => $i,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('landing_dashboard_products')->insert($rows);
    }

    protected function seedCtaFeatures(): void
    {
        $features = ['Setup dalam 1 hari', 'Tanpa biaya tersembunyi', 'Support 24/7', 'Free trial 14 hari'];

        $rows = [];
        foreach ($features as $order => $text) {
            $rows[] = [
                'text' => $text,
                'icon' => 'check-circle-2',
                'order' => $order,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('landing_cta_features')->insert($rows);
    }

    protected function seedSolutions(): void
    {
        $solutions = [
            ['title' => 'Manajemen Stok', 'icon' => 'boxes', 'color' => 'blue', 'size' => 'lg', 'visual_type' => 'inventory'],
            ['title' => 'Analitik Penjualan', 'icon' => 'bar-chart-3', 'color' => 'cyan', 'size' => 'md', 'visual_type' => 'chart', 'chart_data' => '40,65,45,80,55,90,70'],
            ['title' => 'Purchase Order Otomatis', 'icon' => 'file-text', 'color' => 'purple', 'size' => 'sm', 'visual_type' => 'none'],
        ];

        foreach ($solutions as $order => $s) {
            $solutionId = DB::table('landing_solutions')->insertGetId(array_merge($s, [
                'description' => fake('id_ID')->sentence(15),
                'chart_data' => $s['chart_data'] ?? null,
                'order' => $order,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            if ($s['visual_type'] === 'inventory') {
                $items = [];
                foreach (range(1, 4) as $i) {
                    $items[] = [
                        'landing_solution_id' => $solutionId,
                        'name' => 'SKU-' . str_pad($i, 3, '0', STR_PAD_LEFT) . ' · ' . ucfirst(fake('id_ID')->word()),
                        'stock' => (string) fake()->numberBetween(10, 300),
                        'color' => fake()->randomElement(['blue', 'green', 'yellow']),
                        'order' => $i,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                DB::table('landing_solution_inventory_items')->insert($items);
            }
        }
    }

    protected function seedLandingStats(): void
    {
        $stats = [
            ['label' => 'Inventory Accuracy', 'target' => 99.90, 'suffix' => '%', 'decimal_places' => 1, 'is_static' => false],
            ['label' => 'Produk Terkelola', 'target' => 10000, 'suffix' => '+', 'decimal_places' => 0, 'is_static' => false],
            ['label' => 'Perusahaan Aktif', 'target' => 500, 'suffix' => '+', 'decimal_places' => 0, 'is_static' => false],
            ['label' => 'Dukungan', 'is_static' => true, 'static_value' => '24/7', 'suffix' => null, 'target' => null, 'decimal_places' => 0],
        ];

        $rows = [];
        foreach ($stats as $order => $s) {
            $rows[] = array_merge([
                'static_value' => null,
                'bar_percentage' => 100,
                'order' => $order,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ], $s);
        }

        DB::table('landing_stats')->insert($rows);
    }

    protected function seedTestimonials(): void
    {
        $rows = [];
        foreach (range(1, 6) as $i) {
            $name = fake('id_ID')->name();
            $initials = collect(explode(' ', $name))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');

            $rows[] = [
                'name' => $name,
                'role' => fake('id_ID')->jobTitle() . ' · ' . fake('id_ID')->company(),
                'initials' => mb_strtoupper($initials),
                'avatar_color' => fake()->randomElement(['blue', 'cyan', 'purple']),
                'quote' => fake('id_ID')->sentence(20),
                'rating' => fake()->numberBetween(4, 5),
                'is_featured' => $i === 3,
                'order' => $i,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('landing_testimonials')->insert($rows);
    }

    protected function seedFaqs(): void
    {
        $rows = [];
        foreach (range(1, 8) as $order) {
            $rows[] = [
                'question' => fake('id_ID')->sentence(8) . '?',
                'answer' => fake('id_ID')->paragraph(3),
                'order' => $order,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('landing_faqs')->insert($rows);
    }

    protected function seedFeatures(): void
    {
        $features = [
            ['icon' => 'zap', 'title' => 'Cepat & Real-Time', 'color' => 'blue'],
            ['icon' => 'cloud', 'title' => 'Berbasis Cloud', 'color' => 'cyan'],
            ['icon' => 'shield', 'title' => 'Aman & Terenkripsi', 'color' => 'purple'],
            ['icon' => 'bar-chart-3', 'title' => 'Laporan Lengkap', 'color' => 'green'],
        ];

        $rows = [];
        foreach ($features as $order => $f) {
            $rows[] = array_merge($f, [
                'description' => fake('id_ID')->sentence(12),
                'order' => $order,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('landing_features')->insert($rows);
    }
}

<?php

namespace Database\Seeders;

use App\Models\LandingHero;
use App\Models\LandingHeroHighlight;
use Illuminate\Database\Seeder;

class LandingHeroSeeder extends Seeder
{
    public function run(): void
    {
        LandingHero::updateOrCreate(['id' => 1], [
            'badge_text'             => 'Smart Warehouse Management System',
            'title_line_1'           => 'Kelola Gudang',
            'title_line_1_highlight' => 'Lebih Cepat.',
            'title_line_2'           => 'Pantau Stok',
            'title_line_2_highlight' => 'Secara Real-Time.',
            'title_line_3'           => 'Semua Dalam',
            'title_line_3_highlight' => 'Satu Dashboard.',
            'subtitle'               => 'Satu platform modern untuk mengelola stok barang, supplier, transaksi barang masuk dan keluar, laporan, serta aktivitas pengguna secara real-time.',
            'cta_primary_text'       => 'Start Free',
            'cta_primary_url'        => '#',
            'cta_secondary_text'     => 'Book Demo',
            'cta_secondary_url'      => '#',
            'trust_count'            => '500+',
            'trust_text'             => 'perusahaan mempercayai StockFlow',
        ]);

        $highlights = [
            ['title' => '+125 Barang Masuk', 'subtitle' => 'Hari ini · 14:30', 'icon' => 'trending-up', 'color' => 'green', 'order' => 1],
            ['title' => 'Stock Updated', 'subtitle' => 'Real-time sync', 'icon' => 'refresh-cw', 'color' => 'blue', 'order' => 2],
            ['title' => 'Inventory Synced', 'subtitle' => '99.9% accuracy', 'icon' => 'check-circle-2', 'color' => 'cyan', 'order' => 3],
            ['title' => 'Supplier Added', 'subtitle' => 'PT. Mitra Logistik', 'icon' => 'user-plus', 'color' => 'purple', 'order' => 4],
        ];

        foreach ($highlights as $highlight) {
            LandingHeroHighlight::updateOrCreate(['title' => $highlight['title']], $highlight);
        }
    }
}

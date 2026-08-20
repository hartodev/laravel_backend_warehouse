<?php

namespace Database\Seeders;

use App\Models\LandingDashboardActivity;
use App\Models\LandingDashboardProduct;
use App\Models\LandingDashboardStat;
use Illuminate\Database\Seeder;

class LandingDashboardSeeder extends Seeder
{
    public function run(): void
    {
        $stats = [
            ['label' => 'Total Produk', 'value' => '12,847', 'trend_text' => '+12.5% bulan ini', 'trend_direction' => 'up', 'icon' => 'package', 'color' => 'blue', 'order' => 1],
            ['label' => 'Transaksi Hari Ini', 'value' => '348', 'trend_text' => '+8.2% dari kemarin', 'trend_direction' => 'up', 'icon' => 'activity', 'color' => 'green', 'order' => 2],
            ['label' => 'Nilai Stok', 'value' => 'Rp 4.2M', 'trend_text' => '+5.1% minggu ini', 'trend_direction' => 'up', 'icon' => 'dollar-sign', 'color' => 'purple', 'order' => 3],
            ['label' => 'Stok Rendah', 'value' => '23', 'trend_text' => 'Butuh perhatian', 'trend_direction' => 'down', 'icon' => 'alert-triangle', 'color' => 'orange', 'order' => 4],
        ];

        foreach ($stats as $stat) {
            LandingDashboardStat::updateOrCreate(['label' => $stat['label']], $stat);
        }

        $activities = [
            ['title' => 'Barang Masuk #PO-2847', 'time_text' => '2 menit lalu', 'icon' => 'arrow-down-to-line', 'color' => 'green', 'value_text' => '+48', 'value_color' => 'green', 'order' => 1],
            ['title' => 'Barang Keluar #SO-1293', 'time_text' => '15 menit lalu', 'icon' => 'arrow-up-from-line', 'color' => 'blue', 'value_text' => '-24', 'value_color' => 'orange', 'order' => 2],
            ['title' => 'Stock Opname #OP-091', 'time_text' => '1 jam lalu', 'icon' => 'refresh-cw', 'color' => 'purple', 'value_text' => '✓', 'value_color' => 'purple', 'order' => 3],
            ['title' => 'Stok Rendah: SKU-0091', 'time_text' => '3 jam lalu', 'icon' => 'alert-circle', 'color' => 'orange', 'value_text' => '!', 'value_color' => 'orange', 'order' => 4],
        ];

        foreach ($activities as $activity) {
            LandingDashboardActivity::updateOrCreate(['title' => $activity['title']], $activity);
        }

        $products = [
            ['name' => 'Laptop Asus X15', 'sku' => 'LPT-001', 'stock' => 248, 'status' => 'normal', 'order' => 1],
            ['name' => 'Monitor Dell 24"', 'sku' => 'MNT-002', 'stock' => 132, 'status' => 'normal', 'order' => 2],
            ['name' => 'Keyboard Mech', 'sku' => 'KBD-003', 'stock' => 12, 'status' => 'low', 'order' => 3],
            ['name' => 'Headset Sony', 'sku' => 'AUD-004', 'stock' => 3, 'status' => 'critical', 'order' => 4],
        ];

        foreach ($products as $product) {
            LandingDashboardProduct::updateOrCreate(['sku' => $product['sku']], $product);
        }
    }
}

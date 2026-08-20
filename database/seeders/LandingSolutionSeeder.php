<?php

namespace Database\Seeders;

use App\Models\LandingSolution;
use Illuminate\Database\Seeder;

class LandingSolutionSeeder extends Seeder
{
    public function run(): void
    {
        $solutions = [
            [
                'title' => 'Inventory Management', 'icon' => 'package', 'color' => 'blue', 'size' => 'lg',
                'visual_type' => 'inventory', 'order' => 1,
                'description' => 'Kelola ribuan SKU dengan tracking real-time, batch management, dan expiry monitoring otomatis.',
                'inventory' => [
                    ['name' => 'SKU-001 · Laptop', 'stock' => '248', 'color' => 'blue'],
                    ['name' => 'SKU-002 · Monitor', 'stock' => '132', 'color' => 'green'],
                    ['name' => 'SKU-003 · Keyboard', 'stock' => '12', 'color' => 'yellow'],
                    ['name' => 'SKU-004 · Mouse', 'stock' => '89', 'color' => 'green'],
                ],
            ],
            ['title' => 'Supplier Management', 'icon' => 'users', 'color' => 'cyan', 'size' => 'sm', 'visual_type' => 'none', 'order' => 2, 'description' => 'Kelola data supplier, PO, dan riwayat transaksi dengan mudah.'],
            ['title' => 'Warehouse', 'icon' => 'warehouse', 'color' => 'purple', 'size' => 'sm', 'visual_type' => 'none', 'order' => 3, 'description' => 'Mapping area gudang, zone management, dan slot optimization.'],
            ['title' => 'Purchase Order', 'icon' => 'shopping-cart', 'color' => 'green', 'size' => 'sm', 'visual_type' => 'none', 'order' => 4, 'description' => 'Buat dan track PO dari request hingga barang diterima.'],
            ['title' => 'Sales Order', 'icon' => 'send', 'color' => 'blue', 'size' => 'sm', 'visual_type' => 'none', 'order' => 5, 'description' => 'Proses SO, picking, packing, dan pengiriman dalam satu alur.'],
            [
                'title' => 'Reports & Analytics', 'icon' => 'bar-chart-3', 'color' => 'orange', 'size' => 'md',
                'visual_type' => 'chart', 'chart_data' => '40,65,45,80,55,90,70', 'order' => 6,
                'description' => 'Laporan komprehensif dengan visualisasi data yang mudah dipahami dan dapat diexport ke berbagai format.',
            ],
            ['title' => 'Barcode Scanner', 'icon' => 'scan-barcode', 'color' => 'cyan', 'size' => 'sm', 'visual_type' => 'none', 'order' => 7, 'description' => 'Scan barcode/QR untuk proses yang lebih cepat dan akurat.'],
            ['title' => 'Activity Log', 'icon' => 'activity', 'color' => 'purple', 'size' => 'sm', 'visual_type' => 'none', 'order' => 8, 'description' => 'Rekam jejak semua aktivitas pengguna dan transaksi sistem.'],
            ['title' => 'Notification', 'icon' => 'bell', 'color' => 'green', 'size' => 'sm', 'visual_type' => 'none', 'order' => 9, 'description' => 'Alert otomatis untuk stok rendah, PO jatuh tempo, dan anomali.'],
            ['title' => 'Analytics', 'icon' => 'pie-chart', 'color' => 'blue', 'size' => 'sm', 'visual_type' => 'none', 'order' => 10, 'description' => 'Dashboard analitik mendalam dengan insight bisnis berbasis AI.'],
            ['title' => 'Role Permission', 'icon' => 'shield-check', 'color' => 'orange', 'size' => 'sm', 'visual_type' => 'none', 'order' => 11, 'description' => 'Kontrol akses granular untuk setiap pengguna dan departemen.'],
            ['title' => 'Multi Warehouse', 'icon' => 'git-branch', 'color' => 'cyan', 'size' => 'sm', 'visual_type' => 'none', 'order' => 12, 'description' => 'Kelola banyak gudang di berbagai lokasi dalam satu platform.'],
        ];

        foreach ($solutions as $data) {
            $inventory = $data['inventory'] ?? null;
            unset($data['inventory']);

            $solution = LandingSolution::updateOrCreate(['title' => $data['title']], $data);

            if ($inventory) {
                $solution->inventoryItems()->delete();

                foreach ($inventory as $i => $item) {
                    $solution->inventoryItems()->create([...$item, 'order' => $i]);
                }
            }
        }
    }
}

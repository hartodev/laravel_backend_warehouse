<?php

namespace Database\Seeders;

use App\Models\LandingWorkflowStep;
use Illuminate\Database\Seeder;

class LandingWorkflowStepSeeder extends Seeder
{
    public function run(): void
    {
        $steps = [
            [
                'title'       => 'Barang Datang',
                'description' => 'Supplier mengantarkan barang ke gudang dan dilakukan pengecekan awal',
                'icon'        => 'package-check',
                'color'       => 'blue',
                'order'       => 1,
            ],
            [
                'title'       => 'Scan Barcode',
                'description' => 'Setiap item di-scan untuk identifikasi dan pencatatan otomatis',
                'icon'        => 'scan-barcode',
                'color'       => 'cyan',
                'order'       => 2,
            ],
            [
                'title'       => 'Masuk Gudang',
                'description' => 'Sistem menentukan lokasi penyimpanan optimal secara otomatis',
                'icon'        => 'warehouse',
                'color'       => 'purple',
                'order'       => 3,
            ],
            [
                'title'       => 'Update Stock',
                'description' => 'Stok ter-update real-time dan notifikasi dikirim ke seluruh tim',
                'icon'        => 'refresh-cw',
                'color'       => 'green',
                'order'       => 4,
            ],
            [
                'title'       => 'Penjualan',
                'description' => 'SO masuk, picking dilakukan otomatis berdasarkan lokasi optimal',
                'icon'        => 'shopping-cart',
                'color'       => 'orange',
                'order'       => 5,
            ],
            [
                'title'       => 'Barang Keluar',
                'description' => 'Barang dikirim, stok berkurang otomatis, status pengiriman tercatat',
                'icon'        => 'truck',
                'color'       => 'red',
                'order'       => 6,
            ],
            [
                'title'       => 'Laporan',
                'description' => 'Laporan lengkap tersedia otomatis untuk analisis dan pengambilan keputusan',
                'icon'        => 'bar-chart-3',
                'color'       => 'blue',
                'order'       => 7,
            ],
        ];

        foreach ($steps as $step) {
            LandingWorkflowStep::updateOrCreate(['title' => $step['title']], $step);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\LandingBenefit;
use Illuminate\Database\Seeder;

class LandingBenefitSeeder extends Seeder
{
    public function run(): void
    {
        $benefits = [
            [
                'title'          => 'Lebih Cepat',
                'description'    => 'Proses gudang berjalan 40% lebih cepat dibanding metode manual konvensional.',
                'is_static'      => false,
                'target'         => 40,
                'suffix'         => '%',
                'decimal_places' => 0,
                'bar_percentage' => 40,
                'icon'           => 'zap',
                'is_featured'    => false,
                'order'          => 1,
            ],
            [
                'title'          => 'Mengurangi Human Error',
                'description'    => 'Otomasi proses memangkas kesalahan manusia hingga 70% secara signifikan.',
                'is_static'      => false,
                'target'         => 70,
                'suffix'         => '%',
                'decimal_places' => 0,
                'bar_percentage' => 70,
                'icon'           => 'shield-check',
                'is_featured'    => false,
                'order'          => 2,
            ],
            [
                'title'          => 'Inventory Accuracy',
                'description'    => 'Akurasi inventori mencapai 99.9% dengan sistem tracking dan validasi berlapis.',
                'is_static'      => false,
                'target'         => 99.9,
                'suffix'         => '%',
                'decimal_places' => 1,
                'bar_percentage' => 100,
                'icon'           => 'target',
                'is_featured'    => true,
                'order'          => 3,
            ],
            [
                'title'          => 'Monitoring',
                'description'    => 'Pantau kondisi gudang 24 jam non-stop dengan alert otomatis.',
                'is_static'      => true,
                'static_value'   => '24 Jam',
                'bar_percentage' => 100,
                'icon'           => 'monitor',
                'is_featured'    => false,
                'order'          => 4,
            ],
        ];

        foreach ($benefits as $benefit) {
            LandingBenefit::updateOrCreate(['title' => $benefit['title']], $benefit);
        }
    }
}

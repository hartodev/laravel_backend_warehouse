<?php

namespace Database\Seeders;

use App\Models\LandingSectionHeader;
use Illuminate\Database\Seeder;

class LandingSectionHeaderSeeder extends Seeder
{
    public function run(): void
    {
        $headers = [
            [
                'section_key'    => 'dashboard',
                'badge'          => 'Dashboard',
                'title_normal'   => 'Dashboard Premium',
                'title_gradient' => 'Untuk Semua Kebutuhan',
                'subtitle'       => 'Visualisasikan data gudang Anda dengan tampilan yang cantik dan informatif.',
            ],
            [
                'section_key'    => 'solution',
                'badge'          => 'Solusi',
                'title_normal'   => 'Semua Yang Anda Butuhkan',
                'title_gradient' => 'Dalam Satu Platform',
                'subtitle'       => 'StockFlow hadir dengan fitur lengkap untuk mengotomasi seluruh proses gudang Anda.',
            ],
            [
                'section_key'           => 'contact',
                'badge'                 => 'Mulai Sekarang',
                'title_normal'          => 'Siap Transformasi',
                'title_gradient'        => 'Gudang Anda?',
                'subtitle'              => 'Bergabung dengan 500+ perusahaan yang sudah merasakan manfaat StockFlow. Coba gratis selama 14 hari, tanpa kartu kredit.',
                'button_primary_text'   => 'Start Free Trial',
                'button_primary_url'    => '#',
                'button_secondary_text' => 'Contact Sales',
                'button_secondary_url'  => '#',
            ],
        ];

        foreach ($headers as $header) {
            LandingSectionHeader::updateOrCreate(['section_key' => $header['section_key']], $header);
        }
    }
}

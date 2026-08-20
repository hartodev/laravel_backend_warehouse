<?php

namespace Database\Seeders;

use App\Models\LandingCtaFeature;
use Illuminate\Database\Seeder;

class LandingCtaFeatureSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            ['text' => 'Setup dalam 1 hari', 'icon' => 'check-circle-2', 'order' => 1],
            ['text' => 'Tanpa kontrak panjang', 'icon' => 'check-circle-2', 'order' => 2],
            ['text' => 'Support 24/7', 'icon' => 'check-circle-2', 'order' => 3],
            ['text' => 'Migrasi data gratis', 'icon' => 'check-circle-2', 'order' => 4],
        ];

        foreach ($features as $feature) {
            LandingCtaFeature::updateOrCreate(['text' => $feature['text']], $feature);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LandingPageSeeder extends Seeder
{
    /**
     * Jalankan semua seeder yang mengisi konten landing page:
     * Stats, Testimonials, FAQ, Features (dari LandingContentSeeder),
     * Benefits, Workflow Steps, dan contoh data Contact Leads (buat testing admin).
     */
    public function run(): void
    {
        $this->call([
            LandingContentSeeder::class,      // stats, features, testimonials, faqs
            LandingBenefitSeeder::class,      // 40% / 70% / 99.9% / 24 Jam
            LandingWorkflowStepSeeder::class, // 7 langkah workflow
            LandingContactLeadSeeder::class,  // contoh data leads (opsional, buat testing)
        ]);
    }
}

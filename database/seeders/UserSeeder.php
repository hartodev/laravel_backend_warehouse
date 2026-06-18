<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Super Admin ────────────────────────────────
        User::factory()->superAdmin()->create([
            'name' => 'Super Admin Warehouse',
            'email' => 'superadmin@warehouse.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // ── Admin ──────────────────────────────────────
        User::factory()->admin()->create([
            'name' => 'Admin Warehouse',
            'email' => 'admin@warehouse.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // ── User Dummy ────────────────────────────────
        User::factory()->user()->count(10)->create();
    }
}

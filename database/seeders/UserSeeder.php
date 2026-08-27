<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    protected \Faker\Generator $faker;
    protected \Faker\Generator $fakerId;

    public function __construct()
    {
        $this->faker = Faker::create();
        $this->fakerId = Faker::create('id_ID');
    }

    /**
     * Seed users + user_profiles.
     * Butuh warehouses & suppliers sudah ada (MasterDataSeeder jalan duluan)
     * karena users.warehouse_id & users.supplier_id adalah FK.
     */
    public function run(): void
    {
        $warehouseIds = DB::table('warehouses')->pluck('id')->all();
        $supplierIds = DB::table('suppliers')->pluck('id')->all();

        $fixedUsers = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@test.com',
                'role' => 'super_admin',
                'warehouse_id' => null,
                'supplier_id' => null,
            ],
            [
                'name' => 'Admin Utama',
                'email' => 'admin@test.com',
                'role' => 'admin',
                'warehouse_id' => null,
                'supplier_id' => null,
            ],
            [
                'name' => 'Staff Gudang Jakarta',
                'email' => 'staff@test.com',
                'role' => 'staff',
                'warehouse_id' => $warehouseIds[0] ?? null,
                'supplier_id' => null,
            ],
            [
                'name' => 'Kepala Gudang Jakarta',
                'email' => 'warehouse@test.com',
                'role' => 'warehouse_keeper',
                'warehouse_id' => $warehouseIds[0] ?? null,
                'supplier_id' => null,
            ],
            [
                'name' => 'Akun Supplier',
                'email' => 'supplier@test.com',
                'role' => 'supplier',
                'warehouse_id' => null,
                'supplier_id' => $supplierIds[0] ?? null,
            ],
            [
                'name' => 'Akun Partner',
                'email' => 'partner@test.com',
                'role' => 'partner',
                'warehouse_id' => null,
                'supplier_id' => null,
            ],
        ];

        $rows = [];
        foreach ($fixedUsers as $u) {
            $rows[] = array_merge($u, [
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'is_active' => true,
                'phone' => $this->fakerId->phoneNumber(),
                'remember_token' => Str::random(60),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Tambahan user acak: staff & warehouse_keeper per gudang, plus beberapa supplier & partner
        foreach ($warehouseIds as $warehouseId) {
            foreach (range(1, 2) as $i) {
                $rows[] = [
                    'name' => $this->fakerId->name(),
                    'email' => $this->faker->unique()->safeEmail(),
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                    'role' => $this->faker->randomElement(['staff', 'warehouse_keeper']),
                    'is_active' => $this->faker->boolean(90),
                    'warehouse_id' => $warehouseId,
                    'supplier_id' => null,
                    'phone' => $this->fakerId->phoneNumber(),
                    'remember_token' => Str::random(60),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        foreach ($supplierIds as $supplierId) {
            if ($this->faker->boolean(60)) {
                $rows[] = [
                    'name' => $this->fakerId->name(),
                    'email' => $this->faker->unique()->safeEmail(),
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                    'role' => 'supplier',
                    'is_active' => true,
                    'warehouse_id' => null,
                    'supplier_id' => $supplierId,
                    'phone' => $this->fakerId->phoneNumber(),
                    'remember_token' => Str::random(60),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // 'user' biasa & 'partner' tambahan
        foreach (range(1, 5) as $i) {
            $rows[] = [
                'name' => $this->fakerId->name(),
                'email' => $this->faker->unique()->safeEmail(),
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => $this->faker->randomElement(['user', 'partner']),
                'is_active' => $this->faker->boolean(85),
                'warehouse_id' => null,
                'supplier_id' => null,
                'phone' => $this->fakerId->phoneNumber(),
                'remember_token' => Str::random(60),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('users')->insert($rows);

        $this->seedUserProfiles();
    }

    protected function seedUserProfiles(): void
    {
        $userIds = DB::table('users')->pluck('id')->all();

        // Bukan semua user wajib punya profile, mirip kondisi real
        $withProfile = $this->faker->randomElements($userIds, (int) (count($userIds) * 0.7));

        $rows = [];
        foreach ($withProfile as $userId) {
            $rows[] = [
                'user_id' => $userId,
                'phone' => $this->fakerId->phoneNumber(),
                'address' => $this->fakerId->address(),
                'city' => $this->fakerId->city(),
                'province' => $this->fakerId->state(),
                'photo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('user_profiles')->insert($rows);
    }
}
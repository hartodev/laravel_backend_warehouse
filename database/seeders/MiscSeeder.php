<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MiscSeeder extends Seeder
{
    /**
     * Seed product_submissions & user_creation_requests.
     */
    public function run(): void
    {
        $this->seedProductSubmissions();
        $this->seedUserCreationRequests();
    }

    protected function seedProductSubmissions(): void
    {
        $adminIds = DB::table('users')->whereIn('role', ['admin', 'staff', 'super_admin'])->pluck('id')->all();
        $categoryIds = DB::table('categories')->pluck('id')->all();
        $warehouseIds = DB::table('warehouses')->pluck('id')->all();
        $productIds = DB::table('products')->pluck('id')->all();

        $rows = [];
        foreach (range(1, 12) as $i) {
            $status = fake()->randomElement(['pending', 'approved', 'rejected']);
            $isApproved = $status === 'approved';

            $rows[] = [
                'admin_id' => fake()->randomElement($adminIds),
                'category_id' => fake()->randomElement($categoryIds),
                'name' => ucfirst(fake('id_ID')->words(3, true)),
                'sku' => 'NEW-SKU-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'barcode' => fake()->boolean(70) ? fake()->ean13() : null,
                'unit' => fake()->randomElement(['pcs', 'box', 'unit']),
                'initial_stock' => fake()->numberBetween(0, 100),
                'initial_warehouse_id' => fake()->randomElement($warehouseIds),
                'purchase_price' => fake()->numberBetween(5000, 2000000),
                'selling_price' => fake()->numberBetween(6000, 2500000),
                'description' => fake('id_ID')->sentence(),
                'status' => $status,
                'change_data' => fake()->boolean(20) ? json_encode(['name' => 'Perubahan nama produk']) : null,
                'approved_by' => $status !== 'pending' ? fake()->randomElement($adminIds) : null,
                'approved_at' => $status !== 'pending' ? now()->subDays(fake()->numberBetween(1, 20)) : null,
                'reject_reason' => $status === 'rejected' ? fake('id_ID')->sentence() : null,
                'product_id' => $isApproved && ! empty($productIds) ? fake()->randomElement($productIds) : null,
                'created_at' => now()->subDays(fake()->numberBetween(1, 30)),
                'updated_at' => now(),
            ];
        }

        DB::table('product_submissions')->insert($rows);
    }

    protected function seedUserCreationRequests(): void
    {
        $requesterIds = DB::table('users')->whereIn('role', ['admin', 'super_admin'])->pluck('id')->all();
        $approverIds = DB::table('users')->where('role', 'super_admin')->pluck('id')->all();
        $createdUserIds = DB::table('users')->pluck('id')->all();
        $divisions = ['Gudang', 'Operasional', 'Keuangan', 'HRD', 'IT'];

        $rows = [];
        foreach (range(1, 10) as $i) {
            $status = fake()->randomElement(['pending', 'approved', 'rejected']);

            $rows[] = [
                'name' => fake('id_ID')->name(),
                'email' => fake()->unique()->safeEmail(),
                'phone' => fake('id_ID')->phoneNumber(),
                'address' => fake('id_ID')->address(),
                'password' => Hash::make('password'),
                'role' => fake()->randomElement(['user', 'admin']),
                'status' => $status,
                'requested_by' => fake()->randomElement($requesterIds),
                'approved_by' => $status !== 'pending' ? fake()->randomElement($approverIds) : null,
                'created_user_id' => $status === 'approved' ? fake()->randomElement($createdUserIds) : null,
                'reject_reason' => $status === 'rejected' ? fake('id_ID')->sentence() : null,
                'division' => fake()->randomElement($divisions),
                'reason' => fake('id_ID')->sentence(),
                'created_at' => now()->subDays(fake()->numberBetween(1, 30)),
                'updated_at' => now(),
            ];
        }

        DB::table('user_creation_requests')->insert($rows);
    }
}

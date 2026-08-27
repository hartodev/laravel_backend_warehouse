<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RequestSeeder extends Seeder
{
    /**
     * Seed requests + request_items.
     * Butuh users, warehouses, products.
     */
    public function run(): void
    {
        $userIds = DB::table('users')->pluck('id')->all();
        $adminApproverIds = DB::table('users')->whereIn('role', ['admin', 'super_admin'])->pluck('id')->all();
        $warehouseIds = DB::table('warehouses')->pluck('id')->all();
        $productIds = DB::table('products')->pluck('id')->all();

        $statuses = ['pending', 'pending_superadmin', 'approved', 'rejected', 'processing', 'completed'];
        $purposes = ['maintenance', 'distribution', 'return', 'other'];

        foreach (range(1, 25) as $i) {
            $status = fake()->randomElement($statuses);
            $isApproved = in_array($status, ['approved', 'processing', 'completed']);
            $isRejected = $status === 'rejected';

            $requestId = DB::table('requests')->insertGetId([
                'request_number' => 'REQ-' . date('Ym') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'user_id' => fake()->randomElement($userIds),
                'warehouse_id' => fake()->randomElement($warehouseIds),
                'purpose' => fake()->randomElement($purposes),
                'status' => $status,
                'admin_verified_by' => fake()->boolean(70) ? fake()->randomElement($adminApproverIds) : null,
                'admin_verified_at' => fake()->boolean(70) ? now()->subDays(fake()->numberBetween(1, 20)) : null,
                'approved_by' => $isApproved ? fake()->randomElement($adminApproverIds) : null,
                'approved_at' => $isApproved ? now()->subDays(fake()->numberBetween(0, 15)) : null,
                'completed_at' => $status === 'completed' ? now()->subDays(fake()->numberBetween(0, 10)) : null,
                'reject_reason' => $isRejected ? fake('id_ID')->sentence() : null,
                'note' => fake()->boolean(50) ? fake('id_ID')->sentence() : null,
                'admin_note' => fake()->boolean(30) ? fake('id_ID')->sentence() : null,
                'created_at' => now()->subDays(fake()->numberBetween(1, 30)),
                'updated_at' => now(),
            ]);

            $items = fake()->randomElements($productIds, fake()->numberBetween(1, 4));
            $itemRows = [];
            foreach ($items as $productId) {
                $qty = fake()->numberBetween(1, 20);
                $itemRows[] = [
                    'request_id' => $requestId,
                    'product_id' => $productId,
                    'external_name' => null,
                    'external_spec' => null,
                    'external_link' => null,
                    'external_price' => null,
                    'quantity' => $qty,
                    'approved_quantity' => $isApproved ? $qty : null,
                    'note' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Sesekali ada item eksternal (barang di luar katalog produk)
            if (fake()->boolean(20)) {
                $itemRows[] = [
                    'request_id' => $requestId,
                    'product_id' => null,
                    'external_name' => ucfirst(fake('id_ID')->words(3, true)),
                    'external_spec' => fake('id_ID')->sentence(),
                    'external_link' => fake()->url(),
                    'external_price' => fake()->numberBetween(10000, 2000000),
                    'quantity' => fake()->numberBetween(1, 5),
                    'approved_quantity' => null,
                    'note' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            DB::table('request_items')->insert($itemRows);
        }
    }
}

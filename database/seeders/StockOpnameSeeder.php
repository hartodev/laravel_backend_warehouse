<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockOpnameSeeder extends Seeder
{
    /**
     * Seed stock_opnames + stock_opname_items.
     * PENTING: kolom `difference` di stock_opname_items adalah generated/stored
     * column (physical_stock - system_stock) sehingga TIDAK BOLEH diisi manual
     * lewat insert — MySQL yang akan menghitungnya otomatis.
     */
    public function run(): void
    {
        $warehouseIds = DB::table('warehouses')->pluck('id')->all();
        $categoryIds = DB::table('categories')->pluck('id')->all();
        $userIds = DB::table('users')->whereIn('role', ['admin', 'staff', 'warehouse_keeper', 'super_admin'])->pluck('id')->all();
        $productIds = DB::table('products')->pluck('id')->all();

        $statuses = ['draft', 'in_progress', 'pending_approval', 'approved', 'cancelled'];
        $scopes = ['all', 'category', 'manual'];

        foreach (range(1, 12) as $i) {
            $status = fake()->randomElement($statuses);
            $scope = fake()->randomElement($scopes);
            $opnameDate = now()->subDays(fake()->numberBetween(1, 60));

            $started = in_array($status, ['in_progress', 'pending_approval', 'approved']);
            $completed = in_array($status, ['pending_approval', 'approved']);
            $approved = $status === 'approved';

            $opnameId = DB::table('stock_opnames')->insertGetId([
                'opname_number' => 'SO-' . date('Ym', $opnameDate->timestamp) . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'warehouse_id' => fake()->randomElement($warehouseIds),
                'created_by' => fake()->randomElement($userIds),
                'approved_by' => $approved ? fake()->randomElement($userIds) : null,
                'status' => $status,
                'opname_date' => $opnameDate->toDateString(),
                'scope' => $scope,
                'category_id' => $scope === 'category' ? fake()->randomElement($categoryIds) : null,
                'started_at' => $started ? $opnameDate->copy()->addHours(1) : null,
                'completed_at' => $completed ? $opnameDate->copy()->addHours(5) : null,
                'approved_at' => $approved ? $opnameDate->copy()->addDay() : null,
                'notes' => fake()->boolean(30) ? fake('id_ID')->sentence() : null,
                'reject_reason' => $status === 'cancelled' ? fake('id_ID')->sentence() : null,
                'created_at' => $opnameDate,
                'updated_at' => now(),
            ]);

            $items = fake()->randomElements($productIds, fake()->numberBetween(3, 8));
            $itemRows = [];
            foreach ($items as $productId) {
                $systemStock = fake()->numberBetween(0, 500);
                $hasBeenCounted = in_array($status, ['pending_approval', 'approved']);
                $physicalStock = $hasBeenCounted
                    ? max(0, $systemStock + fake()->numberBetween(-10, 10))
                    : null;

                $itemRows[] = [
                    'stock_opname_id' => $opnameId,
                    'product_id' => $productId,
                    'system_stock' => $systemStock,
                    'physical_stock' => $physicalStock,
                    // 'difference' TIDAK disertakan — kolom generated otomatis oleh DB
                    'notes' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            DB::table('stock_opname_items')->insert($itemRows);
        }
    }
}

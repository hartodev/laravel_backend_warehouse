<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SalesOrderSeeder extends Seeder
{
    /**
     * Seed sales_orders + sales_order_items.
     * Butuh warehouses, users, products.
     */
    public function run(): void
    {
        $warehouseIds = DB::table('warehouses')->pluck('id')->all();
        $creatorIds = DB::table('users')->whereIn('role', ['admin', 'staff', 'super_admin'])->pluck('id')->all();
        $approverIds = DB::table('users')->whereIn('role', ['admin', 'super_admin'])->pluck('id')->all();
        $productIds = DB::table('products')->pluck('id')->all();

        $statuses = ['draft', 'confirmed', 'processing', 'shipped', 'completed', 'cancelled'];
        $paymentMethods = ['cash', 'transfer', 'credit'];

        foreach (range(1, 20) as $i) {
            $status = fake()->randomElement($statuses);
            $orderDate = now()->subDays(fake()->numberBetween(1, 60));
            $isApproved = in_array($status, ['confirmed', 'processing', 'shipped', 'completed']);

            $soId = DB::table('sales_orders')->insertGetId([
                'so_number' => 'SO-' . date('Ym', $orderDate->timestamp) . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'reference_number' => fake()->boolean(30) ? fake()->bothify('REF-####??') : null,
                'warehouse_id' => fake()->randomElement($warehouseIds),
                'created_by' => fake()->randomElement($creatorIds),
                'approved_by' => $isApproved ? fake()->randomElement($approverIds) : null,
                'customer_name' => fake('id_ID')->name(),
                'customer_phone' => fake('id_ID')->phoneNumber(),
                'customer_address' => fake('id_ID')->address(),
                'payment_method' => fake()->randomElement($paymentMethods),
                'status' => $status,
                'order_date' => $orderDate->toDateString(),
                'due_date' => $orderDate->copy()->addDays(fake()->numberBetween(7, 30))->toDateString(),
                'notes' => fake()->boolean(30) ? fake('id_ID')->sentence() : null,
                'subtotal' => 0,
                'tax_percent' => 11,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 0,
                'approved_at' => $isApproved ? $orderDate->copy()->addDay() : null,
                'created_at' => $orderDate,
                'updated_at' => now(),
            ]);

            $items = fake()->randomElements($productIds, fake()->numberBetween(1, 5));
            $subtotal = 0;
            $itemRows = [];

            foreach ($items as $productId) {
                $qty = fake()->numberBetween(1, 30);
                $price = fake()->numberBetween(10000, 3500000);
                $discountPercent = fake()->randomElement([0, 0, 5, 10]);
                $lineTotal = $qty * $price * (1 - $discountPercent / 100);
                $subtotal += $lineTotal;

                $itemRows[] = [
                    'sales_order_id' => $soId,
                    'product_id' => $productId,
                    'description' => null,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'discount_percent' => $discountPercent,
                    'subtotal' => $lineTotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            DB::table('sales_order_items')->insert($itemRows);

            $taxAmount = round($subtotal * 0.11, 2);

            DB::table('sales_orders')->where('id', $soId)->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $subtotal + $taxAmount,
            ]);
        }
    }
}

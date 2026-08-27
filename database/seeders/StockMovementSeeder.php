<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockMovementSeeder extends Seeder
{
    /**
     * Seed stock_movements, stock_reports, barcode_logs.
     * Butuh products, warehouses, users, purchase_orders, requests, request_items,
     * stock_transfers sudah ada.
     */
    public function run(): void
    {
        $this->seedStockMovements();
        $this->seedStockReports();
        $this->seedBarcodeLogs();
    }

    protected function seedStockMovements(): void
    {
        $productIds = DB::table('products')->pluck('id')->all();
        $warehouseIds = DB::table('warehouses')->pluck('id')->all();
        $userIds = DB::table('users')->whereIn('role', ['admin', 'staff', 'warehouse_keeper', 'super_admin'])->pluck('id')->all();
        $poIds = DB::table('purchase_orders')->pluck('id')->all();
        $requestIds = DB::table('requests')->pluck('id')->all();
        $transferIds = DB::table('stock_transfers')->pluck('id')->all();

        $types = ['in', 'out', 'transfer_in', 'transfer_out', 'adjustment'];
        $rows = [];

        foreach (range(1, 60) as $i) {
            $type = fake()->randomElement($types);
            $quantity = fake()->numberBetween(1, 100);
            $quantityBefore = fake()->numberBetween(0, 500);

            $signedQty = in_array($type, ['in', 'transfer_in']) ? $quantity
                : (in_array($type, ['out', 'transfer_out']) ? -$quantity
                : fake()->randomElement([1, -1]) * $quantity);

            $referenceType = match ($type) {
                'in' => 'purchase_order',
                'out' => 'request',
                'transfer_in', 'transfer_out' => 'stock_transfer',
                default => 'manual',
            };

            $rows[] = [
                'product_id' => fake()->randomElement($productIds),
                'warehouse_id' => fake()->randomElement($warehouseIds),
                'purchase_order_id' => $type === 'in' && ! empty($poIds) ? fake()->randomElement($poIds) : null,
                'type' => $type,
                'quantity' => $signedQty,
                'quantity_before' => $quantityBefore,
                'quantity_after' => max(0, $quantityBefore + $signedQty),
                'request_id' => $type === 'out' && ! empty($requestIds) ? fake()->randomElement($requestIds) : null,
                'request_item_id' => null,
                'reference_type' => $referenceType,
                'reference_id' => null,
                'stock_transfer_id' => in_array($type, ['transfer_in', 'transfer_out']) && ! empty($transferIds)
                    ? fake()->randomElement($transferIds)
                    : null,
                'created_by' => fake()->randomElement($userIds),
                'note' => fake()->boolean(30) ? fake('id_ID')->sentence() : null,
                'created_at' => now()->subDays(fake()->numberBetween(0, 60)),
                'updated_at' => now(),
            ];
        }

        DB::table('stock_movements')->insert($rows);
    }

    protected function seedStockReports(): void
    {
        $warehouseIds = DB::table('warehouses')->pluck('id')->all();
        $productIds = DB::table('products')->pluck('id')->all();

        $rows = [];
        foreach ($warehouseIds as $warehouseId) {
            $sampledProducts = fake()->randomElements($productIds, min(10, count($productIds)));

            foreach ($sampledProducts as $productId) {
                foreach (['daily', 'monthly'] as $periodType) {
                    $periodDate = $periodType === 'daily'
                        ? now()->subDays(fake()->numberBetween(0, 30))->toDateString()
                        : now()->subMonths(fake()->numberBetween(0, 6))->startOfMonth()->toDateString();

                    $opening = fake()->numberBetween(0, 300);
                    $in = fake()->numberBetween(0, 100);
                    $out = fake()->numberBetween(0, 80);
                    $transferIn = fake()->numberBetween(0, 30);
                    $transferOut = fake()->numberBetween(0, 30);
                    $adjustment = fake()->numberBetween(-10, 10);
                    $closing = max(0, $opening + $in - $out + $transferIn - $transferOut + $adjustment);

                    $rows[] = [
                        'warehouse_id' => $warehouseId,
                        'product_id' => $productId,
                        'period_type' => $periodType,
                        'period_date' => $periodDate,
                        'opening_stock' => $opening,
                        'stock_in' => $in,
                        'stock_out' => $out,
                        'transfer_in' => $transferIn,
                        'transfer_out' => $transferOut,
                        'adjustment' => $adjustment,
                        'closing_stock' => $closing,
                        'total_value' => $closing * fake()->numberBetween(5000, 500000),
                        'generated_at' => now(),
                    ];
                }
            }
        }

        // Hindari duplikat kombinasi unik (warehouse, product, period_type, period_date)
        $unique = [];
        foreach ($rows as $row) {
            $key = $row['warehouse_id'] . '-' . $row['product_id'] . '-' . $row['period_type'] . '-' . $row['period_date'];
            $unique[$key] = $row;
        }

        DB::table('stock_reports')->insert(array_values($unique));
    }

    protected function seedBarcodeLogs(): void
    {
        $userIds = DB::table('users')->whereIn('role', ['admin', 'staff', 'warehouse_keeper', 'super_admin'])->pluck('id')->all();
        $warehouseIds = DB::table('warehouses')->pluck('id')->all();
        $productIds = DB::table('products')->pluck('id')->all();
        $scanTypes = ['stock_in', 'stock_out', 'transfer', 'check', 'purchase'];

        $rows = [];
        foreach (range(1, 40) as $i) {
            $isFound = fake()->boolean(85);

            $rows[] = [
                'user_id' => fake()->randomElement($userIds),
                'warehouse_id' => fake()->randomElement($warehouseIds),
                'product_id' => $isFound ? fake()->randomElement($productIds) : null,
                'barcode_value' => fake()->ean13(),
                'scan_type' => fake()->randomElement($scanTypes),
                'is_found' => $isFound,
                'device_info' => fake()->randomElement(['Android 13 - Zebra TC21', 'iOS 17 - iPhone 12', 'Web Scanner']),
                'scanned_at' => now()->subDays(fake()->numberBetween(0, 30)),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('barcode_logs')->insert($rows);
    }
}

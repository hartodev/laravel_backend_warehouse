<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockTransferSeeder extends Seeder
{
    /**
     * Seed stock_transfers + stock_transfer_items.
     * Butuh warehouses, users, products.
     */
    public function run(): void
    {
        $warehouseIds = DB::table('warehouses')->pluck('id')->all();
        $userIds = DB::table('users')->whereIn('role', ['admin', 'staff', 'warehouse_keeper', 'super_admin'])->pluck('id')->all();
        $productIds = DB::table('products')->pluck('id')->all();

        $statuses = [
            'pending_confirmation',
            'pending_approval',
            'approved',
            'in_transit',
            'received',
            'rejected',
            'cancelled',
            'discrepancy',
        ];

        foreach (range(1, 15) as $i) {
            [$fromWarehouse, $toWarehouse] = fake()->randomElements($warehouseIds, 2);
            $status = fake()->randomElement($statuses);
            $transferDate = now()->subDays(fake()->numberBetween(1, 45));

            $isConfirmed = in_array($status, ['pending_approval', 'approved', 'in_transit', 'received', 'discrepancy']);
            $isApproved = in_array($status, ['approved', 'in_transit', 'received', 'discrepancy']);
            $isSent = in_array($status, ['in_transit', 'received', 'discrepancy']);
            $isReceived = in_array($status, ['received', 'discrepancy']);
            $isCancelled = $status === 'cancelled';
            $isDiscrepancy = $status === 'discrepancy';

            $transferId = DB::table('stock_transfers')->insertGetId([
                'transfer_number' => 'TRF-' . date('Ym', $transferDate->timestamp) . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'from_warehouse_id' => $fromWarehouse,
                'to_warehouse_id' => $toWarehouse,
                'requested_by' => fake()->randomElement($userIds),
                'confirmed_by' => $isConfirmed ? fake()->randomElement($userIds) : null,
                'confirmed_at' => $isConfirmed ? $transferDate->copy()->addHours(3) : null,
                'approved_by' => $isApproved ? fake()->randomElement($userIds) : null,
                'received_by' => $isReceived ? fake()->randomElement($userIds) : null,
                'status' => $status,
                'transfer_date' => $transferDate->toDateString(),
                'expected_arrival' => $transferDate->copy()->addDays(fake()->numberBetween(1, 5))->toDateString(),
                'approved_at' => $isApproved ? $transferDate->copy()->addDay() : null,
                'received_at' => $isReceived ? $transferDate->copy()->addDays(3) : null,
                'reject_reason' => $status === 'rejected' ? fake('id_ID')->sentence() : null,
                'notes' => fake()->boolean(30) ? fake('id_ID')->sentence() : null,
                'sent_at' => $isSent ? $transferDate->copy()->addDays(2) : null,
                'sent_by' => $isSent ? fake()->randomElement($userIds) : null,
                'cancelled_by' => $isCancelled ? fake()->randomElement($userIds) : null,
                'cancelled_at' => $isCancelled ? $transferDate->copy()->addDay() : null,
                'cancel_reason' => $isCancelled ? fake('id_ID')->sentence() : null,
                'shipment_attachment' => $isSent ? 'shipments/dummy-' . $i . '.jpg' : null,
                'discrepancy_notes' => $isDiscrepancy ? fake('id_ID')->sentence() : null,
                'discrepancy_reported_by' => $isDiscrepancy ? fake()->randomElement($userIds) : null,
                'discrepancy_reported_at' => $isDiscrepancy ? $transferDate->copy()->addDays(3) : null,
                'resolved_by' => $isDiscrepancy && fake()->boolean(50) ? fake()->randomElement($userIds) : null,
                'resolved_at' => $isDiscrepancy && fake()->boolean(50) ? $transferDate->copy()->addDays(4) : null,
                'resolution_notes' => $isDiscrepancy && fake()->boolean(50) ? fake('id_ID')->sentence() : null,
                'created_at' => $transferDate,
                'updated_at' => now(),
            ]);

            $items = fake()->randomElements($productIds, fake()->numberBetween(1, 4));
            $itemRows = [];
            foreach ($items as $productId) {
                $qtyRequested = fake()->numberBetween(5, 50);
                $qtySent = $isSent ? $qtyRequested : 0;
                $qtyReceived = $isReceived ? ($isDiscrepancy ? $qtySent - fake()->numberBetween(1, 5) : $qtySent) : 0;

                $itemRows[] = [
                    'stock_transfer_id' => $transferId,
                    'product_id' => $productId,
                    'quantity_requested' => $qtyRequested,
                    'quantity_sent' => $qtySent,
                    'quantity_received' => max($qtyReceived, 0),
                    'is_matched' => $isReceived ? ! $isDiscrepancy : null,
                    'notes' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            DB::table('stock_transfer_items')->insert($itemRows);
        }
    }
}

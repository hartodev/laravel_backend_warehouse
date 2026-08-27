<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseOrderSeeder extends Seeder
{
    /**
     * Seed purchase_orders + purchase_order_items.
     * Butuh suppliers, warehouses, users, products.
     */
    public function run(): void
    {
        $supplierIds = DB::table('suppliers')->pluck('id')->all();
        $warehouseIds = DB::table('warehouses')->pluck('id')->all();
        $creatorIds = DB::table('users')->whereIn('role', ['admin', 'staff', 'super_admin'])->pluck('id')->all();
        $approverIds = DB::table('users')->whereIn('role', ['admin', 'super_admin'])->pluck('id')->all();
        $productIds = DB::table('products')->pluck('id')->all();

        $statuses = ['draft', 'pending', 'approved', 'partial', 'received', 'cancelled'];
        $paymentTerms = ['cod', 'net_7', 'net_14', 'net_30', 'net_60'];
        $paymentMethods = ['cash', 'transfer', 'credit'];

        foreach (range(1, 20) as $i) {
            $status = fake()->randomElement($statuses);
            $orderDate = now()->subDays(fake()->numberBetween(1, 60));

            $poId = DB::table('purchase_orders')->insertGetId([
                'po_number' => 'PO-' . date('Ym', $orderDate->timestamp) . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'supplier_id' => fake()->randomElement($supplierIds),
                'warehouse_id' => fake()->randomElement($warehouseIds),
                'created_by' => fake()->randomElement($creatorIds),
                'approved_by' => in_array($status, ['approved', 'partial', 'received']) ? fake()->randomElement($approverIds) : null,
                'status' => $status,
                'order_date' => $orderDate->toDateString(),
                'expected_date' => $orderDate->copy()->addDays(fake()->numberBetween(3, 14))->toDateString(),
                'received_date' => $status === 'received' ? $orderDate->copy()->addDays(fake()->numberBetween(3, 20))->toDateString() : null,
                'payment_term' => fake()->randomElement($paymentTerms),
                'payment_method' => fake()->randomElement($paymentMethods),
                'subtotal' => 0, // diisi ulang di bawah setelah item dihitung
                'tax_percent' => 11,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 0,
                'notes' => fake()->boolean(40) ? fake('id_ID')->sentence() : null,
                'reject_reason' => $status === 'cancelled' ? fake('id_ID')->sentence() : null,
                'approved_at' => in_array($status, ['approved', 'partial', 'received']) ? $orderDate->copy()->addDay() : null,
                'received_at' => $status === 'received' ? $orderDate->copy()->addDays(5) : null,
                'created_at' => $orderDate,
                'updated_at' => now(),
            ]);

            $items = fake()->randomElements($productIds, fake()->numberBetween(2, 5));
            $subtotal = 0;
            $itemRows = [];

            foreach ($items as $productId) {
                $qty = fake()->numberBetween(5, 100);
                $price = fake()->numberBetween(5000, 3000000);
                $discountPercent = fake()->randomElement([0, 0, 0, 5, 10]);
                $lineTotal = $qty * $price * (1 - $discountPercent / 100);
                $subtotal += $lineTotal;

                $qtyReceived = match ($status) {
                    'received' => $qty,
                    'partial' => (int) ($qty * fake()->randomFloat(2, 0.3, 0.8)),
                    default => 0,
                };

                $itemRows[] = [
                    'purchase_order_id' => $poId,
                    'product_id' => $productId,
                    'quantity_ordered' => $qty,
                    'quantity_received' => $qtyReceived,
                    'unit_price' => $price,
                    'discount_percent' => $discountPercent,
                    'subtotal' => $lineTotal,
                    'notes' => null,
                    // kolom duplikat yang ditambah migration belakangan, diisi supaya konsisten
                    'quantity' => $qty,
                    'price' => $price,
                    'total' => $lineTotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            DB::table('purchase_order_items')->insert($itemRows);

            $taxAmount = round($subtotal * 0.11, 2);
            $totalAmount = $subtotal + $taxAmount;

            DB::table('purchase_orders')->where('id', $poId)->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MasterDataSeeder extends Seeder
{
    /**
     * Seed warehouses, suppliers, categories, products, product_units, stocks.
     * Ini adalah data "master" yang dibutuhkan oleh hampir semua modul lain,
     * jadi harus dijalankan paling awal.
     */
    public function run(): void
    {
        $this->seedWarehouses();
        $this->seedSuppliers();
        $this->seedCategories();
        $this->seedProducts();
        $this->seedProductUnits();
        $this->seedStocks();
    }

    protected function seedWarehouses(): void
    {
        $cities = ['Jakarta', 'Bandung', 'Surabaya', 'Semarang', 'Yogyakarta'];

        $rows = [];
        foreach (range(1, 5) as $i) {
            $city = $cities[$i - 1];
            $rows[] = [
                'name' => "Gudang {$city}",
                'code' => 'WH-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'location' => "Kawasan Industri {$city}",
                'photo' => null,
                'address' => fake('id_ID')->streetAddress(),
                'city' => $city,
                'province' => fake('id_ID')->state(),
                'postal_code' => fake()->postcode(),
                'pic_name' => fake('id_ID')->name(),
                'pic_phone' => fake('id_ID')->phoneNumber(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('warehouses')->insert($rows);
    }

    protected function seedSuppliers(): void
    {
        $rows = [];
        foreach (range(1, 8) as $i) {
            $name = fake('id_ID')->company();
            $rows[] = [
                'name' => $name,
                'code' => 'SUP-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'contact_person' => fake('id_ID')->name(),
                'phone' => fake('id_ID')->phoneNumber(),
                'email' => fake()->unique()->safeEmail(),
                'address' => fake('id_ID')->address(),
                'city' => fake('id_ID')->city(),
                'province' => fake('id_ID')->state(),
                'npwp' => fake()->numerify('##.###.###.#-###.###'),
                'bank_name' => fake()->randomElement(['BCA', 'Mandiri', 'BNI', 'BRI']),
                'bank_account' => fake()->numerify('##########'),
                'bank_account_name' => $name,
                'logo' => null,
                'notes' => fake()->boolean(30) ? fake('id_ID')->sentence() : null,
                'is_active' => fake()->boolean(90),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('suppliers')->insert($rows);
    }

    protected function seedCategories(): void
    {
        $tree = [
            'Elektronik' => ['Laptop', 'Aksesoris Komputer', 'Peralatan Jaringan'],
            'Alat Tulis Kantor' => ['Kertas & Cetak', 'Perlengkapan Meja'],
            'Peralatan Gudang' => ['Rak & Penyimpanan', 'Alat Angkut'],
            'Bahan Baku' => [],
        ];

        foreach ($tree as $parentName => $children) {
            $parentId = DB::table('categories')->insertGetId([
                'parent_id' => null,
                'name' => $parentName,
                'slug' => Str::slug($parentName),
                'icon' => 'folder',
                'image' => null,
                'code' => 'CAT-' . Str::upper(Str::random(4)),
                'description' => fake('id_ID')->sentence(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($children as $childName) {
                DB::table('categories')->insert([
                    'parent_id' => $parentId,
                    'name' => $childName,
                    'slug' => Str::slug($childName),
                    'icon' => 'folder-open',
                    'image' => null,
                    'code' => 'CAT-' . Str::upper(Str::random(4)),
                    'description' => fake('id_ID')->sentence(),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    protected function seedProducts(): void
    {
        $categoryIds = DB::table('categories')->pluck('id')->all();
        $supplierIds = DB::table('suppliers')->pluck('id')->all();
        $units = ['pcs', 'box', 'karton', 'unit', 'lusin'];

        $rows = [];
        foreach (range(1, 40) as $i) {
            $purchasePrice = fake()->numberBetween(5000, 5000000);
            $rows[] = [
                'category_id' => fake()->randomElement($categoryIds),
                'supplier_id' => fake()->boolean(80) ? fake()->randomElement($supplierIds) : null,
                'name' => ucfirst(fake('id_ID')->words(3, true)),
                'sku' => 'SKU-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'barcode' => fake()->unique()->ean13(),
                'unit' => fake()->randomElement($units),
                'min_stock' => fake()->numberBetween(5, 50),
                'purchase_price' => $purchasePrice,
                'selling_price' => $purchasePrice * fake()->randomFloat(2, 1.1, 1.5),
                'photo' => null,
                'description' => fake('id_ID')->sentence(10),
                'is_active' => fake()->boolean(90),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('products')->insert($rows);
    }

    protected function seedProductUnits(): void
    {
        $productIds = DB::table('products')->pluck('id')->all();
        $rows = [];

        foreach ($productIds as $productId) {
            // Satuan dasar (selalu ada, dipakai untuk beli & jual)
            $rows[] = [
                'product_id' => $productId,
                'unit_name' => 'pcs',
                'conversion_value' => 1,
                'is_purchase_unit' => true,
                'is_sell_unit' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Sebagian produk juga punya satuan besar (karton/box)
            if (fake()->boolean(40)) {
                $rows[] = [
                    'product_id' => $productId,
                    'unit_name' => fake()->randomElement(['karton', 'box', 'lusin']),
                    'conversion_value' => fake()->randomElement([6, 10, 12, 24]),
                    'is_purchase_unit' => true,
                    'is_sell_unit' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('product_units')->insert($rows);
    }

    protected function seedStocks(): void
    {
        $warehouseIds = DB::table('warehouses')->pluck('id')->all();
        $productIds = DB::table('products')->pluck('id')->all();

        $rows = [];
        foreach ($warehouseIds as $warehouseId) {
            // Setiap gudang punya sebagian besar (bukan wajib semua) produk
            $productsInWarehouse = fake()->randomElements(
                $productIds,
                (int) (count($productIds) * 0.7)
            );

            foreach ($productsInWarehouse as $productId) {
                $rows[] = [
                    'warehouse_id' => $warehouseId,
                    'product_id' => $productId,
                    'quantity' => fake()->numberBetween(0, 500),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('stocks')->insert($rows);
    }
}

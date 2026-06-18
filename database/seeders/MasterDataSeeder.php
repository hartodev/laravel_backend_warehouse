<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {


        /*
        |--------------------------------------------------------------------------
        | USER PROFILES
        |--------------------------------------------------------------------------
        */
        DB::table('user_profiles')->insert([
            [
                'user_id' => 1,
                'phone' => '081111111111',
                'address' => 'Jl. Malioboro No. 1',
                'city' => 'Yogyakarta',
                'province' => 'DI Yogyakarta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'phone' => '082222222222',
                'address' => 'Jl. Kaliurang KM 5',
                'city' => 'Sleman',
                'province' => 'DI Yogyakarta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'phone' => '083333333333',
                'address' => 'Jl. Magelang KM 7',
                'city' => 'Sleman',
                'province' => 'DI Yogyakarta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | WAREHOUSES
        |--------------------------------------------------------------------------
        */
        DB::table('warehouses')->insert([
            [
                'id' => 1,
                'name' => 'Gudang Pusat',
                'code' => 'GDG-001',
                'address' => 'Jl. Ringroad Utara',
                'city' => 'Yogyakarta',
                'province' => 'DI Yogyakarta',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Gudang Cabang Sleman',
                'code' => 'GDG-002',
                'address' => 'Jl. Kaliurang',
                'city' => 'Sleman',
                'province' => 'DI Yogyakarta',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | SUPPLIERS
        |--------------------------------------------------------------------------
        */
        DB::table('suppliers')->insert([
            [
                'id' => 1,
                'name' => 'PT Sumber Teknik',
                'code' => 'SUP-001',
                'contact_person' => 'Budi',
                'phone' => '081234567890',
                'email' => 'sales@sumberteknik.test',
                'city' => 'Yogyakarta',
                'province' => 'DI Yogyakarta',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'CV Maju Jaya',
                'code' => 'SUP-002',
                'contact_person' => 'Andi',
                'phone' => '081234567891',
                'email' => 'info@majujaya.test',
                'city' => 'Sleman',
                'province' => 'DI Yogyakarta',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | CATEGORIES
        |--------------------------------------------------------------------------
        */
        DB::table('categories')->insert([
            [
                'id' => 1,
                'name' => 'Elektrikal',
                'code' => 'CAT-EL',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Mekanikal',
                'code' => 'CAT-MK',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Tools',
                'code' => 'CAT-TL',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | PRODUCTS
        |--------------------------------------------------------------------------
        */
        DB::table('products')->insert([
            [
                'id' => 1,
                'category_id' => 1,
                'name' => 'Kabel NYA 1.5mm',
                'sku' => 'SKU-001',
                'barcode' => '899001',
                'unit' => 'pcs',
                'min_stock' => 10,
                'purchase_price' => 12000,
                'selling_price' => 15000,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'category_id' => 2,
                'name' => 'Bearing 6205',
                'sku' => 'SKU-002',
                'barcode' => '899002',
                'unit' => 'pcs',
                'min_stock' => 5,
                'purchase_price' => 45000,
                'selling_price' => 55000,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'category_id' => 3,
                'name' => 'Kunci Inggris 12"',
                'sku' => 'SKU-003',
                'barcode' => '899003',
                'unit' => 'pcs',
                'min_stock' => 3,
                'purchase_price' => 75000,
                'selling_price' => 90000,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | PRODUCT UNITS
        |--------------------------------------------------------------------------
        */
        DB::table('product_units')->insert([
            [
                'product_id' => 1,
                'unit_name' => 'roll',
                'conversion_value' => 100,
                'is_purchase_unit' => true,
                'is_sell_unit' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 2,
                'unit_name' => 'box',
                'conversion_value' => 10,
                'is_purchase_unit' => true,
                'is_sell_unit' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 3,
                'unit_name' => 'set',
                'conversion_value' => 5,
                'is_purchase_unit' => true,
                'is_sell_unit' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | STOCKS
        |--------------------------------------------------------------------------
        */
        DB::table('stocks')->insert([
            [
                'warehouse_id' => 1,
                'product_id' => 1,
                'quantity' => 150,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'warehouse_id' => 1,
                'product_id' => 2,
                'quantity' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'warehouse_id' => 2,
                'product_id' => 3,
                'quantity' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

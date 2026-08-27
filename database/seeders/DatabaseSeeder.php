<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed seluruh database untuk keperluan testing.
     *
     * Urutan di bawah ini SENGAJA diatur mengikuti dependensi foreign key
     * antar tabel (mis. products butuh categories & suppliers lebih dulu,
     * payments butuh budget_requests lebih dulu, dst). Jangan diacak urutannya
     * kecuali kamu tahu persis dependensinya tidak berubah.
     */
    public function run(): void
    {
        $this->call([
            // 1. Master data (tanpa dependensi user)
            MasterDataSeeder::class,   // warehouses, suppliers, categories, products, product_units, stocks

            // 2. Users (butuh warehouses & suppliers utk warehouse_id / supplier_id)
            UserSeeder::class,         // users, user_profiles

            // 3. Modul operasional gudang
            RequestSeeder::class,          // requests, request_items
            PurchaseOrderSeeder::class,    // purchase_orders, purchase_order_items
            StockTransferSeeder::class,    // stock_transfers, stock_transfer_items
            StockOpnameSeeder::class,      // stock_opnames, stock_opname_items
            StockMovementSeeder::class,    // stock_movements, stock_reports, barcode_logs

            // 4. Penjualan
            SalesOrderSeeder::class,       // sales_orders, sales_order_items

            // 5. Keuangan (budget dulu, baru payments yg mereferensikan budget_requests)
            BudgetSeeder::class,           // budget_requests, budget_request_items, budget_verifications, expense_reports, budget_revisions
            PaymentSeeder::class,          // payments, cash_books

            // 6. Modul tambahan / approval
            MiscSeeder::class,             // product_submissions, user_creation_requests

            // 7. Komunikasi & log
            CommunicationSeeder::class,    // chats, chat_messages, notifications, activity_logs

            // 8. Konten landing page / marketing (tidak bergantung modul lain)
            LandingPageSeeder::class,      // semua tabel landing_*
        ]);
    }
}

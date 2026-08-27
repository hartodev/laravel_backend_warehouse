# Seeder untuk Testing

Seeder ini dibuat berdasarkan seluruh migration project (users, warehouses,
suppliers, products, requests, purchase orders, stock transfers, stock
opnames, sales orders, budget/finance, chat, notifikasi, activity log, sampai
konten landing page). Semua data memakai **Faker** (`fake()->...`) dengan
locale Indonesia (`fake('id_ID')`) untuk nama, alamat, kota, dll.

## Cara pakai

1. Salin seluruh file di folder ini ke `database/seeders/` pada project
   Laravel kamu (timpa `DatabaseSeeder.php` yang sudah ada).
2. Pastikan package `fakerphp/faker` ada di `composer.json` (biasanya sudah
   ada sebagai dev dependency bawaan `laravel/framework`). Kalau di-deploy ke
   environment yang men-strip dev dependencies, jalankan:
   ```bash
   composer require fakerphp/faker --dev
   ```
3. Jalankan migration dari awal (biar skema fresh), lalu seed:
   ```bash
   php artisan migrate:fresh --seed
   ```
   atau kalau migration sudah jalan dan tabel sudah ada tapi kosong:
   ```bash
   php artisan db:seed
   ```

## Urutan seeding (penting!)

`DatabaseSeeder.php` memanggil seeder lain secara berurutan sesuai dependensi
foreign key. **Jangan diacak urutannya** kecuali kamu paham betul rantai FK-nya:

1. `MasterDataSeeder` — warehouses, suppliers, categories, products,
   product_units, stocks
2. `UserSeeder` — users (butuh warehouse_id/supplier_id), user_profiles
3. `RequestSeeder` — requests, request_items
4. `PurchaseOrderSeeder` — purchase_orders, purchase_order_items
5. `StockTransferSeeder` — stock_transfers, stock_transfer_items
6. `StockOpnameSeeder` — stock_opnames, stock_opname_items
7. `StockMovementSeeder` — stock_movements, stock_reports, barcode_logs
8. `SalesOrderSeeder` — sales_orders, sales_order_items
9. `BudgetSeeder` — budget_requests, budget_request_items,
   budget_verifications, expense_reports, budget_revisions
10. `PaymentSeeder` — payments, cash_books
11. `MiscSeeder` — product_submissions, user_creation_requests
12. `CommunicationSeeder` — chats, chat_messages, notifications,
    activity_logs
13. `LandingPageSeeder` — semua tabel `landing_*` (konten marketing)

## Akun login yang dibuat (password sama semua: `password`)

| Email                  | Role             |
|-------------------------|------------------|
| superadmin@test.com     | super_admin      |
| admin@test.com          | admin            |
| staff@test.com          | staff            |
| warehouse@test.com      | warehouse_keeper |
| supplier@test.com       | supplier         |
| partner@test.com        | partner          |

Selain itu ada user tambahan acak per gudang & per supplier untuk variasi data.

## Catatan teknis

- **`stock_opname_items.difference`** adalah *generated/stored column*
  (`physical_stock - system_stock`) di migration `2026_04_10_100016`.
  Seeder **sengaja tidak mengisi kolom ini** — biarkan MySQL yang menghitung
  otomatis. Jangan tambahkan `difference` ke array insert manapun.
- Beberapa tabel punya kolom "duplikat" akibat migration tambahan belakangan
  (misalnya `purchase_order_items` punya `quantity_ordered` **dan**
  `quantity`, `unit_price` **dan** `price`, `subtotal` **dan** `total`; begitu
  juga `cash_books` punya `type` **dan** `tipe`). Seeder mengisi kedua-duanya
  dengan nilai yang sama supaya konsisten, apa pun kolom yang dipakai
  controller/model kamu.
- Role `finance` / `branch_manager` tidak ada di enum `users.role` (yang ada:
  `super_admin, admin, user, staff, warehouse_keeper, supplier, partner`),
  jadi approval budget di seeder ini memakai user `admin`/`super_admin`
  sebagai pemeran approver. Sesuaikan kalau project kamu punya role khusus
  yang belum tercermin di migration terbaru.
- Faker `unique()` (dipakai untuk email & barcode) di-reset otomatis tiap kali
  seeder dijalankan ulang dalam proses baru, tapi kalau kamu jalankan
  `db:seed` berkali-kali di request yang sama tanpa `migrate:fresh`, bisa
  kena unique constraint karena data lama masih ada. Disarankan selalu pakai
  `migrate:fresh --seed` untuk testing.

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Kolom status di-migrate sebagai ENUM('pending','approved','rejected').
        // Untuk MySQL, menambah opsi enum baru butuh MODIFY COLUMN langsung
        // (Schema::table biasa tidak bisa ubah daftar enum tanpa doctrine/dbal).
        DB::statement("ALTER TABLE product_submissions MODIFY status ENUM('pending','pending_sa','approved','rejected') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Turunkan submission yang masih pending_sa jadi pending dulu,
        // supaya rollback enum tidak menabrak baris yang memakai nilai baru.
        DB::table('product_submissions')->where('status', 'pending_sa')->update(['status' => 'pending']);

        DB::statement("ALTER TABLE product_submissions MODIFY status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'");
    }
};

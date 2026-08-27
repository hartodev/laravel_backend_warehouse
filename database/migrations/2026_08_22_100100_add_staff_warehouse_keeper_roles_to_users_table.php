<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * ⚠️ CEK DULU sebelum migrate: jalankan
 *     SHOW COLUMNS FROM users LIKE 'role';
 * dan pastikan daftar enum di bawah ini match dengan yang ASLI ada sekarang
 * (aku asumsikan 'super_admin','admin','user' berdasarkan validasi role yang
 * dipakai berulang di Web\Superadmin\UserController — kalau ternyata beda,
 * sesuaikan daftar di up()/down() sebelum migrate).
 */
return new class extends Migration
{
   public function up(): void
{
    DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin','admin','user','staff','warehouse_keeper','supplier','partner') NOT NULL DEFAULT 'user'");
}

public function down(): void
{
    DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin','admin','user') NOT NULL DEFAULT 'user'");
}
};
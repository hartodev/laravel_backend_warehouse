<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * ⚠️ CEK DULU enum role users yang AKTUAL sebelum migrate
 *     (SHOW COLUMNS FROM users LIKE 'role';)
 * Migration ini mengasumsikan kamu sudah menjalankan migration sebelumnya
 * yang menambah 'staff' & 'warehouse_keeper'. Kalau belum / enum-nya beda,
 * sesuaikan daftar di bawah dulu.
 */
return new class extends Migration
{
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->foreignId('supplier_id')
            ->nullable()
            ->after('warehouse_id')
            ->constrained('suppliers')
            ->nullOnDelete();
    });

    DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin','admin','user','staff','warehouse_keeper','supplier','partner') NOT NULL DEFAULT 'user'");
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropConstrainedForeignId('supplier_id');
    });

    DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin','admin','user','staff','warehouse_keeper') NOT NULL DEFAULT 'user'");
}
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_creation_requests', function (Blueprint $table) {
            $table->string('division', 100)->nullable()->after('role');
            $table->text('reason')->nullable()->after('division');
        });

        // Perluas enum role — 'user' lama dipertahankan supaya data existing tidak rusak,
        // 'staff' & 'warehouse_keeper' ditambah sebagai role baru yang bisa diajukan admin.

    DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin','admin','user','staff','warehouse_keeper','supplier','partner') NOT NULL DEFAULT 'user'");   }

    public function down(): void
    {
        Schema::table('user_creation_requests', function (Blueprint $table) {
            $table->dropColumn(['division', 'reason']);
        });

        DB::statement("ALTER TABLE user_creation_requests MODIFY role ENUM('user','admin') NOT NULL DEFAULT 'user'");
    }
};
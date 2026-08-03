<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
    {
        // Tambah status baru: pending_superadmin (antara pending & approved)
        DB::statement("ALTER TABLE requests MODIFY status ENUM(
            'pending',
            'pending_superadmin',
            'approved',
            'rejected',
            'processing',
            'completed'
        ) DEFAULT 'pending'");

        Schema::table('requests', function (Blueprint $table) {
            // Jejak approval Admin (tahap 1), terpisah dari approved_by (Super Admin, final)
            $table->foreignId('admin_verified_by')->nullable()->after('user_id')
                  ->constrained('users')->nullOnDelete();
            $table->timestamp('admin_verified_at')->nullable()->after('admin_verified_by');
            $table->text('admin_note')->nullable()->after('note');
        });
    }

    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('admin_verified_by');
            $table->dropColumn(['admin_verified_at', 'admin_note']);
        });

        DB::statement("ALTER TABLE requests MODIFY status ENUM(
            'pending',
            'approved',
            'rejected',
            'processing',
            'completed'
        ) DEFAULT 'pending'");
    }
};

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
        Schema::table('budget_requests', function (Blueprint $table) {
            // Dana yang sudah terealisasi (dipakai)
            $table->decimal('total_realisasi', 15, 2)->default(0)->after('total_estimasi');
        });

        // Update enum status — MySQL tidak support ALTER ENUM langsung,
        // gunakan DB::statement
        \DB::statement("ALTER TABLE budget_requests MODIFY COLUMN status ENUM(
            'draft',
            'pending',
            'pending_sa',
            'approved',
            'approved_revisi',
            'ditunda',
            'ditolak'
        ) NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        Schema::table('budget_requests', function (Blueprint $table) {
            $table->dropColumn('total_realisasi');
        });

        \DB::statement("ALTER TABLE budget_requests MODIFY COLUMN status ENUM(
            'draft',
            'pending',
            'pending_finance',
            'approved',
            'approved_revisi',
            'ditunda',
            'ditolak'
        ) NOT NULL DEFAULT 'draft'");
    }
};
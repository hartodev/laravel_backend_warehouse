<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Perbaiki enum status supaya sesuai state machine yang dipakai controller:
        // pending_confirmation -> pending_approval -> approved -> in_transit -> received/discrepancy
        // (juga tetap dukung rejected & cancelled)
        DB::statement("
            ALTER TABLE stock_transfers
            MODIFY COLUMN status ENUM(
                'pending_confirmation',
                'pending_approval',
                'approved',
                'in_transit',
                'received',
                'rejected',
                'cancelled',
                'discrepancy'
            ) NOT NULL DEFAULT 'pending_confirmation'
        ");

        // 2) Tambah semua kolom yang dipakai StockTransferController tapi belum ada
        Schema::table('stock_transfers', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_transfers', 'confirmed_by')) {
                $table->foreignId('confirmed_by')->nullable()->after('requested_by')
                    ->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('stock_transfers', 'confirmed_at')) {
                $table->timestamp('confirmed_at')->nullable()->after('confirmed_by');
            }
            if (!Schema::hasColumn('stock_transfers', 'cancelled_by')) {
                $table->foreignId('cancelled_by')->nullable()
                    ->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('stock_transfers', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable();
            }
            if (!Schema::hasColumn('stock_transfers', 'cancel_reason')) {
                $table->text('cancel_reason')->nullable();
            }
            if (!Schema::hasColumn('stock_transfers', 'shipment_attachment')) {
                $table->string('shipment_attachment')->nullable();
            }
            if (!Schema::hasColumn('stock_transfers', 'discrepancy_notes')) {
                $table->text('discrepancy_notes')->nullable();
            }
            if (!Schema::hasColumn('stock_transfers', 'discrepancy_reported_by')) {
                $table->foreignId('discrepancy_reported_by')->nullable()
                    ->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('stock_transfers', 'discrepancy_reported_at')) {
                $table->timestamp('discrepancy_reported_at')->nullable();
            }
            if (!Schema::hasColumn('stock_transfers', 'resolved_by')) {
                $table->foreignId('resolved_by')->nullable()
                    ->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('stock_transfers', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable();
            }
            if (!Schema::hasColumn('stock_transfers', 'resolution_notes')) {
                $table->text('resolution_notes')->nullable();
            }
        });

        // 3) Tambah is_matched di stock_transfer_items (dipakai checklist())
        Schema::table('stock_transfer_items', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_transfer_items', 'is_matched')) {
                $table->boolean('is_matched')->nullable()->after('quantity_received');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_transfers', function (Blueprint $table) {
            $cols = [
                'confirmed_by', 'confirmed_at', 'cancelled_by', 'cancelled_at', 'cancel_reason',
                'shipment_attachment', 'discrepancy_notes', 'discrepancy_reported_by',
                'discrepancy_reported_at', 'resolved_by', 'resolved_at', 'resolution_notes',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('stock_transfers', $col)) {
                    // drop foreign key dulu kalau ada, baru drop column
                    if (in_array($col, ['confirmed_by', 'cancelled_by', 'discrepancy_reported_by', 'resolved_by'])) {
                        $table->dropConstrainedForeignId($col);
                    } else {
                        $table->dropColumn($col);
                    }
                }
            }
        });

        Schema::table('stock_transfer_items', function (Blueprint $table) {
            if (Schema::hasColumn('stock_transfer_items', 'is_matched')) {
                $table->dropColumn('is_matched');
            }
        });

        DB::statement("
            ALTER TABLE stock_transfers
            MODIFY COLUMN status ENUM(
                'pending', 'approved', 'in_transit', 'received', 'rejected', 'cancelled'
            ) NOT NULL DEFAULT 'pending'
        ");
    }
};


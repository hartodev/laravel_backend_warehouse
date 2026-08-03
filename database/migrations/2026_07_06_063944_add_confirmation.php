<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// Additive di atas struktur asli — tidak mengubah data existing.
// Kolom pakai unsignedBigInteger (bukan foreignId) supaya konsisten
// dengan gaya sent_by/received_by yang sudah ada di project ini.

return new class extends Migration
{
    public function up(): void
    {
        // 1) Perluas enum status: tambah pending_approval & discrepancy.
        //    'pending' tetap dipakai sebagai "menunggu konfirmasi Admin A"
        //    (bukan diganti, biar data lama tidak perlu migrasi ulang).
        DB::statement("ALTER TABLE stock_transfers MODIFY COLUMN status ENUM(
            'pending',
            'pending_approval',
            'approved',
            'in_transit',
            'received',
            'rejected',
            'cancelled',
            'discrepancy'
        ) DEFAULT 'pending'");

        Schema::table('stock_transfers', function (Blueprint $table) {
            // Konfirmasi lanjut oleh Admin Gudang A (pending -> pending_approval)
            if (!Schema::hasColumn('stock_transfers', 'confirmed_by')) {
                $table->unsignedBigInteger('confirmed_by')->nullable()->after('requested_by');
            }
            if (!Schema::hasColumn('stock_transfers', 'confirmed_at')) {
                $table->timestamp('confirmed_at')->nullable()->after('confirmed_by');
            }

            // Pembatalan oleh Admin Gudang A (terpisah dari reject_reason superadmin)
            if (!Schema::hasColumn('stock_transfers', 'cancelled_by')) {
                $table->unsignedBigInteger('cancelled_by')->nullable();
            }
            if (!Schema::hasColumn('stock_transfers', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable();
            }
            if (!Schema::hasColumn('stock_transfers', 'cancel_reason')) {
                $table->text('cancel_reason')->nullable();
            }

            // Lampiran bukti pengiriman (wajib saat Admin A send)
            if (!Schema::hasColumn('stock_transfers', 'shipment_attachment')) {
                $table->string('shipment_attachment')->nullable();
            }

            // Discrepancy — dilaporkan Admin Gudang B saat checklist
            if (!Schema::hasColumn('stock_transfers', 'discrepancy_notes')) {
                $table->text('discrepancy_notes')->nullable();
            }
            if (!Schema::hasColumn('stock_transfers', 'discrepancy_reported_by')) {
                $table->unsignedBigInteger('discrepancy_reported_by')->nullable();
            }
            if (!Schema::hasColumn('stock_transfers', 'discrepancy_reported_at')) {
                $table->timestamp('discrepancy_reported_at')->nullable();
            }

            // Resolusi discrepancy oleh Superadmin
            if (!Schema::hasColumn('stock_transfers', 'resolved_by')) {
                $table->unsignedBigInteger('resolved_by')->nullable();
            }
            if (!Schema::hasColumn('stock_transfers', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable();
            }
            if (!Schema::hasColumn('stock_transfers', 'resolution_notes')) {
                $table->text('resolution_notes')->nullable();
            }
        });

        // Tandai per-item cocok/tidak saat checklist Admin B
        Schema::table('stock_transfer_items', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_transfer_items', 'is_matched')) {
                $table->boolean('is_matched')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('stock_transfers', 'confirmed_by') ? 'confirmed_by' : null,
                Schema::hasColumn('stock_transfers', 'confirmed_at') ? 'confirmed_at' : null,
                Schema::hasColumn('stock_transfers', 'cancelled_by') ? 'cancelled_by' : null,
                Schema::hasColumn('stock_transfers', 'cancelled_at') ? 'cancelled_at' : null,
                Schema::hasColumn('stock_transfers', 'cancel_reason') ? 'cancel_reason' : null,
                Schema::hasColumn('stock_transfers', 'shipment_attachment') ? 'shipment_attachment' : null,
                Schema::hasColumn('stock_transfers', 'discrepancy_notes') ? 'discrepancy_notes' : null,
                Schema::hasColumn('stock_transfers', 'discrepancy_reported_by') ? 'discrepancy_reported_by' : null,
                Schema::hasColumn('stock_transfers', 'discrepancy_reported_at') ? 'discrepancy_reported_at' : null,
                Schema::hasColumn('stock_transfers', 'resolved_by') ? 'resolved_by' : null,
                Schema::hasColumn('stock_transfers', 'resolved_at') ? 'resolved_at' : null,
                Schema::hasColumn('stock_transfers', 'resolution_notes') ? 'resolution_notes' : null,
            ]));
        });

        Schema::table('stock_transfer_items', function (Blueprint $table) {
            if (Schema::hasColumn('stock_transfer_items', 'is_matched')) {
                $table->dropColumn('is_matched');
            }
        });

        DB::statement("ALTER TABLE stock_transfers MODIFY COLUMN status ENUM(
            'pending', 'approved', 'in_transit', 'received', 'rejected', 'cancelled'
        ) DEFAULT 'pending'");
    }
};


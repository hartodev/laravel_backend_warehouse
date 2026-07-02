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
    Schema::table('stock_transfers', function (Blueprint $table) {
        if (!Schema::hasColumn('stock_transfers', 'sent_at')) {
            $table->timestamp('sent_at')->nullable();
        }
        if (!Schema::hasColumn('stock_transfers', 'sent_by')) {
            $table->unsignedBigInteger('sent_by')->nullable();
        }
        if (!Schema::hasColumn('stock_transfers', 'received_at')) {
            $table->timestamp('received_at')->nullable();
        }
        if (!Schema::hasColumn('stock_transfers', 'received_by')) {
            $table->unsignedBigInteger('received_by')->nullable();
        }
    });
}

public function down(): void
{
    Schema::table('stock_transfers', function (Blueprint $table) {
        $table->dropColumn(array_filter([
            Schema::hasColumn('stock_transfers', 'sent_at') ? 'sent_at' : null,
            Schema::hasColumn('stock_transfers', 'sent_by') ? 'sent_by' : null,
            Schema::hasColumn('stock_transfers', 'received_at') ? 'received_at' : null,
            Schema::hasColumn('stock_transfers', 'received_by') ? 'received_by' : null,
        ]));
    });
}
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->foreign('cancelled_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('discrepancy_reported_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('resolved_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->dropForeign(['cancelled_by']);
            $table->dropForeign(['discrepancy_reported_by']);
            $table->dropForeign(['resolved_by']);
        });
    }
};

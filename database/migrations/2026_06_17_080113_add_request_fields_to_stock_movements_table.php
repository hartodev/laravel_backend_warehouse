<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->unsignedBigInteger('request_id')->nullable()->after('quantity_after');
            $table->unsignedBigInteger('request_item_id')->nullable()->after('request_id');

            $table->foreign('request_id')
                  ->references('id')->on('requests')
                  ->nullOnDelete();

            $table->foreign('request_item_id')
                  ->references('id')->on('request_items')
                  ->nullOnDelete();

            $table->index('request_id');
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['request_id']);
            $table->dropForeign(['request_item_id']);
            $table->dropColumn(['request_id', 'request_item_id']);
        });
    }
};




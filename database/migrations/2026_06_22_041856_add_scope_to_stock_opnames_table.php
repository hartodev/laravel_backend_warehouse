<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->enum('scope', ['all', 'category', 'manual'])
                  ->default('all')
                  ->after('opname_date');
            $table->foreignId('category_id')
                  ->nullable()
                  ->after('scope')
                  ->constrained('categories')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['scope', 'category_id']);
        });
    }
};


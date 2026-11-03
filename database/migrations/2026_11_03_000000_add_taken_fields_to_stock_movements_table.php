<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            if (! Schema::hasColumn('stock_movements', 'taken_by_name')) {
                $table->string('taken_by_name')->nullable();
            }
            if (! Schema::hasColumn('stock_movements', 'taken_by_division')) {
                $table->string('taken_by_division')->nullable();
            }
            if (! Schema::hasColumn('stock_movements', 'taken_date')) {
                $table->date('taken_date')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            foreach (['taken_by_name', 'taken_by_division', 'taken_date'] as $col) {
                if (Schema::hasColumn('stock_movements', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

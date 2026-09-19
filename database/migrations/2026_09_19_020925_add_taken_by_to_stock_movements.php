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
    Schema::table('stock_movements', function (Blueprint $table) {
        $table->string('taken_by_name')->nullable()->after('note');
        $table->string('taken_by_division')->nullable()->after('taken_by_name');
    });
}

public function down(): void
{
    Schema::table('stock_movements', function (Blueprint $table) {
        $table->dropColumn(['taken_by_name', 'taken_by_division']);
    });
}
};
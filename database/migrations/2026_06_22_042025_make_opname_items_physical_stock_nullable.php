<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_opname_items', function (Blueprint $table) {
            // physical_stock dan difference boleh null (belum diisi)
            $table->integer('physical_stock')->nullable()->change();
            $table->integer('difference')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('stock_opname_items', function (Blueprint $table) {
            $table->integer('physical_stock')->nullable(false)->default(0)->change();
            $table->integer('difference')->nullable(false)->default(0)->change();
        });
    }
};
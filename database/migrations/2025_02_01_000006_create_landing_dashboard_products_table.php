<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_dashboard_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');   // contoh: "Laptop Asus X15"
            $table->string('sku');    // contoh: "LPT-001"
            $table->unsignedInteger('stock');
            $table->enum('status', ['normal', 'low', 'critical'])->default('normal');
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_dashboard_products');
    }
};

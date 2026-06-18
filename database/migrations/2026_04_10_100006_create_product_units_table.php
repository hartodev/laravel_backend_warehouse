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
        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('unit_name', 50);
            // conversion_value: berapa unit dasar dalam 1 satuan ini
            // contoh: 1 karton = 12 pcs → conversion_value = 12
            $table->decimal('conversion_value', 10, 4);
            $table->boolean('is_purchase_unit')->default(false);
            $table->boolean('is_sell_unit')->default(false);
            $table->timestamps();

            $table->unique(['product_id', 'unit_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_units');
    }
};

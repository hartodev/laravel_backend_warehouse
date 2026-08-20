<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_solution_inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_solution_id')->constrained()->cascadeOnDelete();
            $table->string('name');  // contoh: "SKU-001 · Laptop"
            $table->string('stock'); // contoh: "248" (string biar bisa "12" dsb tanpa masalah)
            $table->string('color')->default('blue'); // dipakai di class "inv-stock {color}": blue/green/yellow
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_solution_inventory_items');
    }
};

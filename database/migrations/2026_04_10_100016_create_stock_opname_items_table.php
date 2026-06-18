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
        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->integer('system_stock');   // stok sistem saat opname dibuat
            $table->integer('physical_stock'); // stok hasil hitung fisik
            // FIX: difference dihitung di DB agar tidak mungkin salah
            $table->integer('difference')->storedAs('physical_stock - system_stock');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['stock_opname_id', 'product_id']);
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
    }
};

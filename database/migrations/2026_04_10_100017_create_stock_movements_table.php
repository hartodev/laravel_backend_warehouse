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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('warehouse_id')->constrained()->restrictOnDelete();
            $table->enum('type', ['in', 'out', 'transfer_in', 'transfer_out', 'adjustment']);
            $table->integer('quantity'); // positif = masuk, negatif = keluar
            $table->integer('quantity_before');
            $table->integer('quantity_after');

            // FIX: Polymorphic yang lebih bersih — sumber pergerakan stok
            // reference_type: 'request', 'purchase_order', 'stock_transfer', 'stock_opname', 'manual'
            $table->string('reference_type', 50)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('product_id');
            $table->index('warehouse_id');
            $table->index('type');
            $table->index('created_at');
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};




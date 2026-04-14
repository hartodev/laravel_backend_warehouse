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
            $table->enum('type', ['in', 'out', 'transfer', 'adjustment']);
            $table->integer('quantity');
            $table->integer('quantity_before');
            $table->integer('quantity_after');
 
            // Sumber pergerakan stok (nullable, salah satu diisi)
            $table->foreignId('request_id')
                  ->nullable()->constrained('requests')->nullOnDelete();
            $table->foreignId('request_item_id')
                  ->nullable()->constrained('request_items')->nullOnDelete();
            $table->foreignId('purchase_order_id')
                  ->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->foreignId('stock_transfer_id')
                  ->nullable()->constrained('stock_transfers')->nullOnDelete();
            $table->foreignId('stock_opname_id')
                  ->nullable()->constrained('stock_opnames')->nullOnDelete();
 
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();
 
            $table->index('product_id');
            $table->index('warehouse_id');
            $table->index('type');
            $table->index('created_at');
            $table->index('request_id');
            $table->index('purchase_order_id');
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

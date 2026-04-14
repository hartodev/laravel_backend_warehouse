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
        Schema::create('barcode_logs', function (Blueprint $table) {
            $table->id();
             $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('barcode_value', 200);
            $table->enum('scan_type', ['stock_in', 'stock_out', 'transfer', 'check', 'purchase']);
            $table->boolean('is_found')->default(true);
            $table->string('device_info')->nullable();
            $table->timestamp('scanned_at')->useCurrent();
 
            $table->index('user_id');
            $table->index('barcode_value');
            $table->index('scanned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barcode_logs');
    }
};

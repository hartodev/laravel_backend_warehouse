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
        Schema::create('stock_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->enum('period_type', ['daily', 'monthly']);
            $table->date('period_date');
            $table->integer('opening_stock');
            $table->integer('stock_in');
            $table->integer('stock_out');
            $table->integer('transfer_in');
            $table->integer('transfer_out');
            $table->integer('adjustment');
            $table->integer('closing_stock');
            $table->decimal('total_value', 18, 2)->default(0);
            $table->timestamp('generated_at')->useCurrent();

            $table->unique(['warehouse_id', 'product_id', 'period_type', 'period_date'], 'stock_reports_unique');
            $table->index(['period_type', 'period_date']);
            $table->index('warehouse_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_reports');
    }
};

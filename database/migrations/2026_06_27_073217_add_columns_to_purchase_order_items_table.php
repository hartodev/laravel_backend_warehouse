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
        Schema::table('purchase_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_order_items', 'quantity')) {
                $table->integer('quantity')->default(0);
            }
            if (!Schema::hasColumn('purchase_order_items', 'price')) {
                $table->decimal('price', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('purchase_order_items', 'total')) {
                $table->decimal('total', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('purchase_order_items', 'quantity_received')) {
                $table->integer('quantity_received')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('purchase_order_items', 'quantity') ? 'quantity' : null,
                Schema::hasColumn('purchase_order_items', 'price') ? 'price' : null,
                Schema::hasColumn('purchase_order_items', 'total') ? 'total' : null,
                Schema::hasColumn('purchase_order_items', 'quantity_received') ? 'quantity_received' : null,
            ]));
        });
    }
};
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
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_orders', 'payment_method')) {
                $table->enum('payment_method', ['cash', 'transfer', 'credit'])->nullable()->after('notes');
            }
            if (!Schema::hasColumn('purchase_orders', 'tax_percent')) {
                $table->decimal('tax_percent', 5, 2)->default(0)->after('payment_method');
            }
            if (!Schema::hasColumn('purchase_orders', 'tax_amount')) {
                $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_percent');
            }
            if (!Schema::hasColumn('purchase_orders', 'discount_amount')) {
                $table->decimal('discount_amount', 15, 2)->default(0)->after('tax_amount');
            }
            if (!Schema::hasColumn('purchase_orders', 'subtotal')) {
                $table->decimal('subtotal', 15, 2)->default(0)->after('discount_amount');
            }
            if (!Schema::hasColumn('purchase_orders', 'total_amount')) {
                $table->decimal('total_amount', 15, 2)->default(0)->after('subtotal');
            }
            if (!Schema::hasColumn('purchase_orders', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('total_amount');
            }
            if (!Schema::hasColumn('purchase_orders', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('purchase_orders', 'received_at')) {
                $table->timestamp('received_at')->nullable()->after('approved_at');
            }
            if (!Schema::hasColumn('purchase_orders', 'reject_reason')) {
                $table->text('reject_reason')->nullable()->after('received_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('purchase_orders', 'payment_method') ? 'payment_method' : null,
                Schema::hasColumn('purchase_orders', 'tax_percent') ? 'tax_percent' : null,
                Schema::hasColumn('purchase_orders', 'tax_amount') ? 'tax_amount' : null,
                Schema::hasColumn('purchase_orders', 'discount_amount') ? 'discount_amount' : null,
                Schema::hasColumn('purchase_orders', 'subtotal') ? 'subtotal' : null,
                Schema::hasColumn('purchase_orders', 'total_amount') ? 'total_amount' : null,
                Schema::hasColumn('purchase_orders', 'approved_by') ? 'approved_by' : null,
                Schema::hasColumn('purchase_orders', 'approved_at') ? 'approved_at' : null,
                Schema::hasColumn('purchase_orders', 'received_at') ? 'received_at' : null,
                Schema::hasColumn('purchase_orders', 'reject_reason') ? 'reject_reason' : null,
            ]));
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->timestamp('supplier_confirmed_at')->nullable()->after('approved_at');
            $table->unsignedBigInteger('supplier_confirmed_by')->nullable()->after('supplier_confirmed_at');
            $table->timestamp('shipped_at')->nullable()->after('supplier_confirmed_by');
            $table->unsignedBigInteger('shipped_by')->nullable()->after('shipped_at');
            $table->text('supplier_note')->nullable()->after('shipped_by');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['supplier_confirmed_at', 'supplier_confirmed_by', 'shipped_at', 'shipped_by', 'supplier_note']);
        });
    }
};

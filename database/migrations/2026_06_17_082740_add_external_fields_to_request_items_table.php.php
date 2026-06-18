<?php
// database/migrations/xxxx_xx_xx_add_external_fields_to_request_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('request_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->change();
            $table->string('external_name')->nullable()->after('product_id');
            $table->text('external_spec')->nullable()->after('external_name');
            $table->string('external_link', 500)->nullable()->after('external_spec');
            $table->decimal('external_price', 15, 2)->nullable()->after('external_link');
        });
    }

    public function down(): void
    {
        Schema::table('request_items', function (Blueprint $table) {
            $table->dropColumn(['external_name', 'external_spec', 'external_link', 'external_price']);
            $table->foreignId('product_id')->nullable(false)->change();
        });
    }
};

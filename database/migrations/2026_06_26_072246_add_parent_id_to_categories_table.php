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
    Schema::table('categories', function (Blueprint $table) {
        $table->unsignedBigInteger('parent_id')->nullable()->after('id');
        $table->string('slug', 120)->nullable()->after('name');
        $table->string('icon', 50)->nullable()->after('slug');
        $table->string('image')->nullable()->after('icon');

        $table->foreign('parent_id')->references('id')->on('categories')->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('categories', function (Blueprint $table) {
        $table->dropForeign(['parent_id']);
        $table->dropColumn(['parent_id', 'slug', 'icon', 'image', 'is_active']);
    });
}
};

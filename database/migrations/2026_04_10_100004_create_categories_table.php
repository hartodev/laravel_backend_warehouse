<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // FIX: Pisah unique & nullable — jika code boleh kosong, jangan unique
            // Jika code harus unik saat diisi, gunakan partial unique index via DB::statement
            $table->string('code', 50)->nullable()->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
        });

        // // Partial unique index: hanya enforce unique jika code tidak NULL
        // DB::statement('CREATE UNIQUE INDEX categories_code_unique ON categories (code) WHERE code IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            // SEBELUM: dropColumn(['parent_id', 'slug', 'icon', 'image', 'is_active']);
            // SESUDAH: hapus 'is_active' — bukan kolom milik migration ini.
            $table->dropColumn(['parent_id', 'slug', 'icon', 'image']);
        });
    }
};


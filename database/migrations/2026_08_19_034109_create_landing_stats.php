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
        Schema::create('landing_stats', function (Blueprint $table) {
            $table->id();
            $table->string('label');                    // "Inventory Accuracy"
            $table->decimal('target', 10, 2)->nullable(); // 99.90 / 10000.00 / 500.00 (dipakai kalau bukan static)
            $table->string('suffix', 10)->nullable();     // "%", "+", ""
            $table->unsignedTinyInteger('decimal_places')->default(0); // 1 = tampil 99.9%, 0 = 500+
            $table->boolean('is_static')->default(false); // true kalau nilainya teks tetap (mis. "24/7")
            $table->string('static_value')->nullable();   // "24/7"
            $table->unsignedTinyInteger('bar_percentage')->default(100); // lebar progress bar di bawah angka
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_stats');
    }
};

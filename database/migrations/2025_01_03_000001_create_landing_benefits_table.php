<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_benefits', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');

            // Sama seperti landing_stats: is_static=true -> tampilkan static_value apa adanya (mis. "24 Jam")
            // is_static=false -> animasi counter dari 0 -> target dengan suffix & decimal_places
            $table->boolean('is_static')->default(false);
            $table->string('static_value')->nullable();

            $table->decimal('target', 10, 2)->nullable();
            $table->string('suffix')->nullable();
            $table->unsignedTinyInteger('decimal_places')->default(0);

            // Lebar progress bar di bawah angka (0-100), dipakai untuk --target-width
            $table->unsignedTinyInteger('bar_percentage')->default(0);

            $table->string('icon'); // nama icon lucide, contoh: "zap", "shield-check"

            // Kartu yang disorot / background beda (seperti kartu 99.9% Inventory Accuracy)
            $table->boolean('is_featured')->default(false);

            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_benefits');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_hero_highlights', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // contoh: "+125 Barang Masuk"
            $table->string('subtitle'); // contoh: "Hari ini · 14:30"
            $table->string('icon'); // nama icon lucide, contoh: "trending-up"
            $table->string('color')->default('blue'); // dipakai di class "fc-icon {color}"
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_hero_highlights');
    }
};

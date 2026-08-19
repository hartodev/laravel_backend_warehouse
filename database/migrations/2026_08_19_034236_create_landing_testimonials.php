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
        Schema::create('landing_testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // "Budi Hartono"
            $table->string('role');                 // "Logistics Director · Astra Group"
            $table->string('initials', 4);          // "BH"  (avatar tanpa foto)
            $table->string('avatar_color', 20)->default('blue'); // blue | cyan | purple
            $table->text('quote');
            $table->unsignedTinyInteger('rating')->default(5); // 1-5
            $table->boolean('is_featured')->default(false);    // kartu tengah yang di-highlight
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_testimonials');
    }
};

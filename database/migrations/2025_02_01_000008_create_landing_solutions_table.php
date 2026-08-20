<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_solutions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('icon');
            $table->string('color')->default('blue'); // dipakai di class "bento-icon {color}"

            // Ukuran kartu di bento grid: sm (1 kolom), md (2 kolom + mini chart), lg (2 kolom + mini inventory)
            $table->enum('size', ['sm', 'md', 'lg'])->default('sm');

            // Visual tambahan opsional:
            // - "none"      : kartu polos (judul + deskripsi saja) → biasanya size sm
            // - "inventory" : tampilkan daftar mini inventory (relasi ke landing_solution_inventory_items) → biasanya size lg
            // - "chart"     : tampilkan mini bar chart dari kolom chart_data → biasanya size md
            $table->enum('visual_type', ['none', 'inventory', 'chart'])->default('none');

            // Untuk visual_type=chart: daftar tinggi bar dipisah koma, contoh: "40,65,45,80,55,90,70" (dalam persen)
            $table->string('chart_data')->nullable();

            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_solutions');
    }
};

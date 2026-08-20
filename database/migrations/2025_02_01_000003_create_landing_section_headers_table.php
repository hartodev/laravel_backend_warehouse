<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_section_headers', function (Blueprint $table) {
            $table->id();
            // Kunci unik section: "dashboard", "solution", "contact" (bisa ditambah "problem", "workflow", dst di masa depan)
            $table->string('section_key')->unique();
            $table->string('badge');
            $table->string('title_normal');    // baris judul biasa
            $table->string('title_gradient');  // baris judul yang di-highlight gradient biru
            $table->text('subtitle')->nullable();

            // Tombol CTA opsional — hanya dipakai section yang punya tombol (misalnya "contact")
            $table->string('button_primary_text')->nullable();
            $table->string('button_primary_url')->nullable();
            $table->string('button_secondary_text')->nullable();
            $table->string('button_secondary_url')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_section_headers');
    }
};

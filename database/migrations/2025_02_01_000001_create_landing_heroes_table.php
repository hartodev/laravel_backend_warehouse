<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_heroes', function (Blueprint $table) {
            $table->id();
            $table->string('badge_text')->default('Smart Warehouse Management System');

            // 3 baris judul, masing2 punya bagian normal + bagian gradient (yang di-highlight biru)
            $table->string('title_line_1')->default('Kelola Gudang');
            $table->string('title_line_1_highlight')->default('Lebih Cepat.');
            $table->string('title_line_2')->default('Pantau Stok');
            $table->string('title_line_2_highlight')->default('Secara Real-Time.');
            $table->string('title_line_3')->default('Semua Dalam');
            $table->string('title_line_3_highlight')->default('Satu Dashboard.');

            $table->text('subtitle')->nullable();

            $table->string('cta_primary_text')->default('Start Free');
            $table->string('cta_primary_url')->default('#');
            $table->string('cta_secondary_text')->default('Book Demo');
            $table->string('cta_secondary_url')->default('#');

            $table->string('trust_count')->default('500+');
            $table->string('trust_text')->default('perusahaan mempercayai StockFlow');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_heroes');
    }
};

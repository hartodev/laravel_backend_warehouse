<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_dashboard_stats', function (Blueprint $table) {
            $table->id();
            $table->string('label');         // contoh: "Total Produk"
            $table->string('value');         // contoh: "12,847" atau "Rp 4.2M" — string biar fleksibel formatnya
            $table->string('trend_text');    // contoh: "+12.5% bulan ini"
            $table->enum('trend_direction', ['up', 'down'])->default('up');
            $table->string('icon');          // nama icon lucide, contoh: "package"
            $table->string('color')->default('blue'); // dipakai di class "dp-stat-icon {color}"
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_dashboard_stats');
    }
};

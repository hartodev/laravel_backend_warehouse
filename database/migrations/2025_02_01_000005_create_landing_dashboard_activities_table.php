<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_dashboard_activities', function (Blueprint $table) {
            $table->id();
            $table->string('title');       // contoh: "Barang Masuk #PO-2847"
            $table->string('time_text');   // contoh: "2 menit lalu"
            $table->string('icon');        // nama icon lucide, contoh: "arrow-down-to-line"
            $table->string('color')->default('blue'); // dipakai di class "dp-act-icon {color}"
            $table->string('value_text');  // contoh: "+48", "-24", "✓", "!"
            $table->string('value_color')->default('green'); // dipakai di class "dp-act-val {color}"
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_dashboard_activities');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_cta_features', function (Blueprint $table) {
            $table->id();
            $table->string('text');                       // contoh: "Setup dalam 1 hari"
            $table->string('icon')->default('check-circle-2');
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_cta_features');
    }
};

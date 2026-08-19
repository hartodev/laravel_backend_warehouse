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
        Schema::create('landing_features', function (Blueprint $table) {
            $table->id();
            $table->string('icon');        // slug lucide icon, mis. "zap", "cloud"
            $table->string('title');
            $table->text('description');
            $table->string('color', 20)->default('blue'); // blue | cyan | purple | green | orange
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_features');
    }};

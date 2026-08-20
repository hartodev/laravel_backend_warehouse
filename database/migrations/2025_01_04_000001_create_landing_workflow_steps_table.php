<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('icon'); // nama icon lucide, contoh: "package-check"
            $table->string('color')->default('blue'); // dipakai di class "wf-icon {color}"
            $table->unsignedInteger('order')->default(0); // juga jadi dasar nomor "01, 02, ..."
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_workflow_steps');
    }
};

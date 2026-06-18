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
        Schema::create('budget_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_request_id')->constrained()->cascadeOnDelete();
            $table->string('nama_item');
            $table->decimal('qty', 10, 2)->nullable();
            $table->string('satuan', 50)->nullable();
            $table->decimal('estimasi_biaya', 15, 2); // harga satuan
            $table->decimal('total', 15, 2);          // qty * estimasi_biaya
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index('budget_request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_request_items');
    }
};

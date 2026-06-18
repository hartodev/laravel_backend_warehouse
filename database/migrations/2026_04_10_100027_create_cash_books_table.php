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
        Schema::create('cash_books', function (Blueprint $table) {
            $table->id();
            $table->string('no_bukti', 100)->unique();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type', ['masuk', 'keluar']);
            $table->string('pihak'); // diterima dari / dibayarkan kepada
            $table->decimal('jumlah_uang', 15, 2);
            $table->string('terbilang');
            $table->text('keterangan')->nullable();
            $table->date('tanggal');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('tanggal');
            $table->index('payment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_books');
    }
};

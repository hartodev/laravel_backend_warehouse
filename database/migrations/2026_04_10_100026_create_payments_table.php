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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number', 100)->unique();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();

            // Referensi sumber pembayaran (salah satu diisi)
            $table->foreignId('purchase_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sales_order_id')->nullable()->constrained()->nullOnDelete();
            // budget_request_id FK akan ditambah di migration budget (setelah tabel tersebut ada)
            $table->unsignedBigInteger('budget_request_id')->nullable();

            $table->enum('payment_type', ['masuk', 'keluar']);
            $table->enum('payment_method', ['cash', 'transfer', 'cek', 'giro']);

            // Data transfer
            $table->string('nama_pengirim')->nullable();
            $table->string('bank_pengirim')->nullable();
            $table->string('nama_penerima')->nullable();
            $table->string('bank_penerima')->nullable();
            $table->string('no_rekening_tujuan')->nullable();

            // Data kwitansi
            $table->string('diterima_dari')->nullable();
            $table->decimal('nominal', 15, 2);
            $table->text('untuk_pembayaran')->nullable();
            $table->string('terbilang')->nullable();

            $table->enum('status', ['pending', 'verified', 'cancelled'])->default('pending');
            $table->string('bukti_file')->nullable();
            $table->date('payment_date');
            $table->timestamp('verified_at')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('payment_type');
            $table->index('payment_date');
            $table->index('status');
            $table->index('purchase_order_id');
            $table->index('sales_order_id');
            $table->index('budget_request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

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
        Schema::create('expense_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('submitted_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nomor_invoice', 150)->nullable();
            $table->string('nama_vendor', 200)->nullable();
            $table->date('tanggal_transaksi');
            $table->decimal('nominal_realisasi', 15, 2);
            // FIX: selisih sebagai generated column agar tidak bisa salah input
            // (membutuhkan join dengan budget_request untuk nominal_rekomendasi,
            //  jadi biarkan dihitung di aplikasi dan simpan hasilnya)
            $table->decimal('selisih', 15, 2)->default(0);

            // Checklist lampiran
            $table->boolean('lamp_invoice')->default(false);
            $table->boolean('lamp_bukti_transfer')->default(false);
            $table->boolean('lamp_kartu_garansi')->default(false);
            $table->boolean('lamp_serah_terima')->default(false);
            $table->text('lamp_lainnya')->nullable();

            $table->json('attachment_files')->nullable();
            $table->enum('status', ['draft', 'submitted', 'verified', 'rejected'])->default('draft');
            $table->text('catatan')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index('budget_request_id');
            $table->index('status');
            $table->index('tanggal_transaksi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_reports');
    }
};

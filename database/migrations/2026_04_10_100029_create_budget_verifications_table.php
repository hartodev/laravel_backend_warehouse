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
        Schema::create('budget_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('finance_id')->constrained('users')->restrictOnDelete();

            // Checklist kelengkapan dokumen
            $table->boolean('doc_form_lengkap')->default(false);
            $table->boolean('doc_surat_justifikasi')->default(false);
            $table->boolean('doc_estimasi_vendor')->default(false);
            $table->boolean('doc_spesifikasi_teknis')->default(false);
            $table->text('doc_lainnya')->nullable();

            // Analisa finance
            $table->text('cek_anggaran')->nullable();
            $table->text('analisa_cashflow')->nullable();
            $table->enum('rekomendasi', ['setuju', 'tunda', 'tolak']);
            $table->decimal('nominal_rekomendasi', 15, 2)->nullable();
            $table->text('catatan_finance')->nullable();

            $table->timestamp('verified_at')->useCurrent();
            $table->timestamps();

            $table->index('budget_request_id');
            $table->index('finance_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_verifications');
    }
};

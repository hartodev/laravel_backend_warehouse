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
        Schema::create('budget_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_request_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('expense_report_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('akun_terdampak', 200);
            $table->string('kode_akun', 50)->nullable();
            $table->decimal('anggaran_awal', 15, 2);
            $table->decimal('realisasi', 15, 2);
            $table->enum('jenis_perubahan', ['tambahan', 'pengurangan']);
            $table->decimal('nominal_perubahan', 15, 2);
            $table->decimal('anggaran_baru', 15, 2);
            $table->text('alasan_revisi');
            $table->enum('status', [
                'pending',
                'approved',
                'approved_revisi',
                'ditunda',
                'ditolak',
            ])->default('pending');
            $table->text('catatan_approver')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('budget_request_id');
            $table->index('expense_report_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_revisions');
    }
};

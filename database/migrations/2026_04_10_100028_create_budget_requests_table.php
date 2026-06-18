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
        Schema::create('budget_requests', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_form', 150)->unique();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('divisi', 100);
            $table->date('tanggal_pengajuan');
            $table->enum('jenis', ['rab', 'luar_rab']);

            // Khusus RAB
            $table->string('kode_akun', 50)->nullable();
            $table->string('nama_akun', 100)->nullable();

            // Khusus luar RAB
            $table->text('alasan_luar_rab')->nullable();
            $table->enum('urgensi', ['normal', 'mendesak'])->default('normal');
            $table->text('dampak_jika_tidak')->nullable();
            $table->enum('sumber_dana', ['realokasi', 'tambahan', 'lainnya'])->nullable();

            // Total agregat dari budget_request_items
            $table->decimal('total_estimasi', 15, 2)->default(0);

            $table->text('keterangan')->nullable();

            // Status & approval chain
            $table->enum('status', [
                'draft',
                'pending',
                'pending_finance',
                'approved',
                'approved_revisi',
                'ditunda',
                'ditolak',
            ])->default('draft');

            $table->foreignId('branch_manager_id')
                ->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('branch_manager_at')->nullable();
            $table->text('catatan_branch_manager')->nullable();

            $table->foreignId('finance_id')
                ->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('finance_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('jenis');
            $table->index('status');
            $table->index('tanggal_pengajuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_requests');
    }
};

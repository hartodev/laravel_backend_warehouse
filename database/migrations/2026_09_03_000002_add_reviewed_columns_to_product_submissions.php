<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_submissions', function (Blueprint $table) {
            // Kolom ini sudah dipakai di ProductSubmission model (relasi reviewedBy())
            // dan di view superadmin.product_submission.*, tapi belum pernah
            // benar-benar di-migrate. Tanpa ini, ->update(['reviewed_by' => ...])
            // di Superadmin\ProductSubmissionController akan gagal SQL "Unknown column".
            $table->foreignId('reviewed_by')->nullable()->after('is_urgent')->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            $table->text('review_note')->nullable()->after('reviewed_at');
        });
    }

    public function down(): void
    {
        Schema::table('product_submissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn(['reviewed_at', 'review_note']);
        });
    }
};

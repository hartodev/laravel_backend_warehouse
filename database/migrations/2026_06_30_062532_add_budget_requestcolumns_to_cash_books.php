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
        Schema::table('cash_books', function (Blueprint $table) {
            if (! Schema::hasColumn('cash_books', 'budget_request_id')) {
                $table->foreignId('budget_request_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('budget_requests')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('cash_books', 'jenis')) {
                $table->string('jenis', 50)->nullable()->after('budget_request_id');
            }

            if (! Schema::hasColumn('cash_books', 'tipe')) {
                $table->enum('tipe', ['masuk', 'keluar'])->nullable()->after('jenis');
            }

            if (! Schema::hasColumn('cash_books', 'created_by')) {
                $table->foreignId('created_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('cash_books', 'catatan')) {
                $table->text('catatan')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('cash_books', function (Blueprint $table) {
            if (Schema::hasColumn('cash_books', 'budget_request_id')) {
                $table->dropConstrainedForeignId('budget_request_id');
            }
            if (Schema::hasColumn('cash_books', 'created_by')) {
                $table->dropConstrainedForeignId('created_by');
            }
            $table->dropColumn(['jenis', 'tipe', 'catatan']);
        });
    }
};
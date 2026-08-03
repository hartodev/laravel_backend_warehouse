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
        DB::statement("ALTER TABLE expense_reports MODIFY COLUMN status ENUM('draft','submitted','pending_revisi','verified','rejected') DEFAULT 'draft'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE expense_reports MODIFY COLUMN status ENUM('draft','submitted','verified','rejected') DEFAULT 'draft'");
    }
};


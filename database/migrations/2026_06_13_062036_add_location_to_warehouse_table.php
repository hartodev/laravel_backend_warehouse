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
          Schema::table('warehouses', function (Blueprint $table) {
        $table->string('location')->after('code');
        $table->string('photo')->nullable();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouses', function (Blueprint $table) {
            //
        });
    }
};

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
        Schema::table('products', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('photo');
        });
    }
};
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
        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('budget_request_id')
                ->references('id')
                ->on('budget_requests')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['budget_request_id']);
        });
    }
};


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
        Schema::create('sales_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            // FIX: Standardisasi ke bahasa Inggris agar konsisten dengan tabel lain
            $table->string('description')->nullable(); // deskripsi khusus item ini
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();

            $table->unique(['sales_order_id', 'product_id']);
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_order_items');
    }
};





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
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('so_number', 100)->unique();
            $table->string('reference_number', 100)->nullable(); // no referensi eksternal
            $table->foreignId('warehouse_id')->constrained()->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone', 20)->nullable();
            $table->text('customer_address')->nullable();
            $table->enum('payment_method', ['cash', 'transfer', 'credit'])->default('cash');
            $table->enum('status', [
                'draft',
                'confirmed',
                'processing',
                'shipped',
                'completed',
                'cancelled',
            ])->default('draft');
            $table->date('order_date');
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('order_date');
            $table->index('warehouse_id');
            $table->index('customer_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};
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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('activity', 100);         // create, update, delete, login, dll
            $table->string('module', 100)->nullable(); // Product, PurchaseOrder, dll
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->text('description')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('user_id');
            $table->index(['module', 'subject_id']);
            $table->index('activity');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 100)->nullable(); // class notifikasi
            $table->string('title');
            $table->text('body');
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_read']);
            $table->index('type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->restrictOnDelete();
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('chat_id');
            $table->index(['chat_id', 'is_read']); // untuk query pesan belum dibaca per chat
            $table->index('sender_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};


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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            // CONVENTION: user_one_id SELALU user dengan ID lebih kecil
            $table->foreignId('user_one_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_two_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            // Dengan konvensi di atas, unique ini sudah benar (tidak bisa duplikat)
            $table->unique(['user_one_id', 'user_two_id']);
            $table->index('user_one_id');
            $table->index('user_two_id');
            $table->index('last_message_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
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
        Schema::create('barcode_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('barcode_value', 200);
            $table->enum('scan_type', ['stock_in', 'stock_out', 'transfer', 'check', 'purchase']);
            $table->boolean('is_found')->default(true);
            $table->string('device_info')->nullable();
            $table->timestamp('scanned_at')->useCurrent();

            $table->index('user_id');
            $table->index('barcode_value');
            $table->index('scanned_at');
            $table->index('is_found');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barcode_logs');
    }
};
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
        Schema::create('stock_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->enum('period_type', ['daily', 'monthly']);
            $table->date('period_date');
            $table->integer('opening_stock')->default(0);
            $table->integer('stock_in')->default(0);
            $table->integer('stock_out')->default(0);
            $table->integer('transfer_in')->default(0);
            $table->integer('transfer_out')->default(0);
            $table->integer('adjustment')->default(0);
            $table->integer('closing_stock')->default(0);
            $table->decimal('total_value', 18, 2)->default(0);
            $table->timestamp('generated_at')->useCurrent();

            $table->unique(
                ['warehouse_id', 'product_id', 'period_type', 'period_date'],
                'stock_reports_unique'
            );
            $table->index(['period_type', 'period_date']);
            $table->index('warehouse_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_reports');
    }
};
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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('warehouse_id')->constrained()->restrictOnDelete();
            $table->enum('type', ['in', 'out', 'transfer_in', 'transfer_out', 'adjustment']);
            $table->integer('quantity'); // positif = masuk, negatif = keluar
            $table->integer('quantity_before');
            $table->integer('quantity_after');

            // FIX: Polymorphic yang lebih bersih — sumber pergerakan stok
            // reference_type: 'request', 'purchase_order', 'stock_transfer', 'stock_opname', 'manual'
            $table->string('reference_type', 50)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('product_id');
            $table->index('warehouse_id');
            $table->index('type');
            $table->index('created_at');
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};


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
        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->integer('system_stock');   // stok sistem saat opname dibuat
            $table->integer('physical_stock'); // stok hasil hitung fisik
            // FIX: difference dihitung di DB agar tidak mungkin salah
            $table->integer('difference')->storedAs('physical_stock - system_stock');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['stock_opname_id', 'product_id']);
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
    }
};
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
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->string('opname_number', 100)->unique();
            $table->foreignId('warehouse_id')->constrained()->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', [
                'draft',
                'in_progress',
                'pending_approval',
                'approved',
                'cancelled',
            ])->default('draft');
            $table->date('opname_date');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('reject_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('warehouse_id');
            $table->index('status');
            $table->index('opname_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
    }
};
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
        Schema::create('stock_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_transfer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->integer('quantity_requested');
            $table->integer('quantity_sent')->default(0);
            $table->integer('quantity_received')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['stock_transfer_id', 'product_id']);
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_items');
    }
};





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
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_number', 100)->unique();
            $table->foreignId('from_warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->foreignId('to_warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', [
                'pending',
                'approved',
                'in_transit',
                'received',
                'rejected',
                'cancelled',
            ])->default('pending');
            $table->date('transfer_date');
            $table->date('expected_arrival')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->text('reject_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('from_warehouse_id');
            $table->index('to_warehouse_id');
            $table->index('status');
            $table->index('transfer_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfers');
    }
};
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
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->integer('quantity_ordered');
            $table->integer('quantity_received')->default(0);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('subtotal', 15, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['purchase_order_id', 'product_id']);
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number', 100)->unique();
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete();
            $table->foreignId('warehouse_id')->constrained()->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', [
                'draft',
                'pending',
                'approved',
                'partial',
                'received',
                'cancelled',
            ])->default('draft');
            $table->date('order_date');
            $table->date('expected_date')->nullable();
            $table->date('received_date')->nullable();
            // FIX: Tambah payment_term sebagai enum agar terstandarisasi
            $table->enum('payment_term', ['cod', 'net_7', 'net_14', 'net_30', 'net_60'])->default('cod');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->text('reject_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('supplier_id');
            $table->index('warehouse_id');
            $table->index('status');
            $table->index('order_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
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
        Schema::create('request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->integer('quantity');
            $table->integer('approved_quantity')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['request_id', 'product_id']);
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_items');
    }
};



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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number', 100)->unique(); // FIX: tambah nomor referensi unik
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete(); // FIX: tambah warehouse tujuan
            $table->enum('purpose', ['maintenance', 'distribution', 'return', 'other'])
                ->default('distribution');
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'processing',
                'completed',
            ])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('reject_reason')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('status');
            $table->index('created_at');
            $table->index('warehouse_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};


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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(0);
            $table->timestamps();

            // Setiap kombinasi warehouse + product hanya boleh ada 1 row
            $table->unique(['warehouse_id', 'product_id']);
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
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
        Schema::create('product_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('name');
            // FIX: sku di submission nullable, tapi wajib divalidasi sebelum approve di aplikasi
            $table->string('sku', 100)->nullable();
            $table->string('barcode', 100)->nullable();
            $table->string('unit', 50);
            $table->integer('initial_stock')->default(0);
            $table->foreignId('initial_warehouse_id')
                ->nullable()
                ->constrained('warehouses')
                ->nullOnDelete();
            $table->decimal('purchase_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('reject_reason')->nullable();
            // Terisi saat submission di-approve dan product dibuat
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index('admin_id');
            $table->index('status');
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_submissions');
    }
};
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
        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('unit_name', 50);
            // conversion_value: berapa unit dasar dalam 1 satuan ini
            // contoh: 1 karton = 12 pcs → conversion_value = 12
            $table->decimal('conversion_value', 10, 4);
            $table->boolean('is_purchase_unit')->default(false);
            $table->boolean('is_sell_unit')->default(false);
            $table->timestamps();

            $table->unique(['product_id', 'unit_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_units');
    }
};
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('sku', 100)->unique();
            $table->string('barcode', 100)->nullable()->unique();
            $table->string('unit', 50); // satuan dasar produk
            $table->integer('min_stock')->default(0);
            $table->decimal('purchase_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('category_id');
            $table->index('is_active');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // FIX: Pisah unique & nullable — jika code boleh kosong, jangan unique
            // Jika code harus unik saat diisi, gunakan partial unique index via DB::statement
            $table->string('code', 50)->nullable()->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
        });

        // // Partial unique index: hanya enforce unique jika code tidak NULL
        // DB::statement('CREATE UNIQUE INDEX categories_code_unique ON categories (code) WHERE code IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            // SEBELUM: dropColumn(['parent_id', 'slug', 'icon', 'image', 'is_active']);
            // SESUDAH: hapus 'is_active' — bukan kolom milik migration ini.
            $table->dropColumn(['parent_id', 'slug', 'icon', 'image']);
        });
    }
};

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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 50)->unique();
            $table->string('contact_person')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('npwp', 30)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account', 50)->nullable();
            $table->string('bank_account_name')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 50)->unique();
            // FIX: Pisah lokasi agar bisa di-filter/laporan
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('pic_name')->nullable();
            $table->string('pic_phone', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
            $table->index('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};




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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
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
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};
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
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['super_admin', 'admin', 'user'])->default('user');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index('role');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};














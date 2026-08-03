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
        Schema::table('barcode_logs', function (Blueprint $table) {
            $table->timestamps(); // tambah created_at + updated_at
        });
    }

    public function down(): void
    {
        Schema::table('barcode_logs', function (Blueprint $table) {
            $table->dropTimestamps();
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
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->unsignedBigInteger('stock_transfer_id')->nullable()->after('reference_id');
            $table->index('stock_transfer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropIndex(['stock_transfer_id']);
            $table->dropColumn(['stock_transfer_id']);
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
    Schema::table('stock_transfers', function (Blueprint $table) {
        if (!Schema::hasColumn('stock_transfers', 'sent_at')) {
            $table->timestamp('sent_at')->nullable();
        }
        if (!Schema::hasColumn('stock_transfers', 'sent_by')) {
            $table->unsignedBigInteger('sent_by')->nullable();
        }
        if (!Schema::hasColumn('stock_transfers', 'received_at')) {
            $table->timestamp('received_at')->nullable();
        }
        if (!Schema::hasColumn('stock_transfers', 'received_by')) {
            $table->unsignedBigInteger('received_by')->nullable();
        }
    });
}

public function down(): void
{
    Schema::table('stock_transfers', function (Blueprint $table) {
        $table->dropColumn(array_filter([
            Schema::hasColumn('stock_transfers', 'sent_at') ? 'sent_at' : null,
            Schema::hasColumn('stock_transfers', 'sent_by') ? 'sent_by' : null,
            Schema::hasColumn('stock_transfers', 'received_at') ? 'received_at' : null,
            Schema::hasColumn('stock_transfers', 'received_by') ? 'received_by' : null,
        ]));
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
    Schema::table('categories', function (Blueprint $table) {
        $table->unsignedBigInteger('parent_id')->nullable()->after('id');
        $table->string('slug', 120)->nullable()->after('name');
        $table->string('icon', 50)->nullable()->after('slug');
        $table->string('image')->nullable()->after('icon');

        $table->foreign('parent_id')->references('id')->on('categories')->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('categories', function (Blueprint $table) {
        $table->dropForeign(['parent_id']);
        $table->dropColumn(['parent_id', 'slug', 'icon', 'image', 'is_active']);
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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('warehouse_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->String('phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropColumn(['warehouse_id', 'phone']);
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
            $table->foreignId('supplier_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
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
    public function up()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('npwp');
        });
    }

    public function down()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('logo');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_submissions', function (Blueprint $table) {
            $table->json('change_data')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('product_submissions', function (Blueprint $table) {
            $table->dropColumn('change_data');
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
        Schema::table('stock_opnames', function (Blueprint $table) {
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_opnames', function (Blueprint $table) {
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
    public function up(): void
    {
        Schema::table('stock_opname_items', function (Blueprint $table) {
            // physical_stock dan difference boleh null (belum diisi)
            $table->integer('physical_stock')->nullable()->change();
            $table->integer('difference')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('stock_opname_items', function (Blueprint $table) {
            $table->integer('physical_stock')->nullable(false)->default(0)->change();
            $table->integer('difference')->nullable(false)->default(0)->change();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->enum('scope', ['all', 'category', 'manual'])
                  ->default('all')
                  ->after('opname_date');
            $table->foreignId('category_id')
                  ->nullable()
                  ->after('scope')
                  ->constrained('categories')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['scope', 'category_id']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreignId('purchase_order_id')
                ->nullable()
                ->after('warehouse_id')
                ->constrained('purchase_orders')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['purchase_order_id']);
            $table->dropColumn('purchase_order_id');
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
        Schema::table('barcode_logs', function (Blueprint $table) {
            $table->timestamps(); // tambah created_at + updated_at
        });
    }

    public function down(): void
    {
        Schema::table('barcode_logs', function (Blueprint $table) {
            $table->dropTimestamps();
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
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->unsignedBigInteger('stock_transfer_id')->nullable()->after('reference_id');
            $table->index('stock_transfer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropIndex(['stock_transfer_id']);
            $table->dropColumn(['stock_transfer_id']);
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
    Schema::table('stock_transfers', function (Blueprint $table) {
        if (!Schema::hasColumn('stock_transfers', 'sent_at')) {
            $table->timestamp('sent_at')->nullable();
        }
        if (!Schema::hasColumn('stock_transfers', 'sent_by')) {
            $table->unsignedBigInteger('sent_by')->nullable();
        }
        if (!Schema::hasColumn('stock_transfers', 'received_at')) {
            $table->timestamp('received_at')->nullable();
        }
        if (!Schema::hasColumn('stock_transfers', 'received_by')) {
            $table->unsignedBigInteger('received_by')->nullable();
        }
    });
}

public function down(): void
{
    Schema::table('stock_transfers', function (Blueprint $table) {
        $table->dropColumn(array_filter([
            Schema::hasColumn('stock_transfers', 'sent_at') ? 'sent_at' : null,
            Schema::hasColumn('stock_transfers', 'sent_by') ? 'sent_by' : null,
            Schema::hasColumn('stock_transfers', 'received_at') ? 'received_at' : null,
            Schema::hasColumn('stock_transfers', 'received_by') ? 'received_by' : null,
        ]));
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
    Schema::table('categories', function (Blueprint $table) {
        $table->unsignedBigInteger('parent_id')->nullable()->after('id');
        $table->string('slug', 120)->nullable()->after('name');
        $table->string('icon', 50)->nullable()->after('slug');
        $table->string('image')->nullable()->after('icon');

        $table->foreign('parent_id')->references('id')->on('categories')->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('categories', function (Blueprint $table) {
        $table->dropForeign(['parent_id']);
        $table->dropColumn(['parent_id', 'slug', 'icon', 'image', 'is_active']);
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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('warehouse_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->String('phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropColumn(['warehouse_id', 'phone']);
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
            $table->foreignId('supplier_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
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
    public function up()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('npwp');
        });
    }

    public function down()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('logo');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_submissions', function (Blueprint $table) {
            $table->json('change_data')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('product_submissions', function (Blueprint $table) {
            $table->dropColumn('change_data');
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
        Schema::table('stock_opnames', function (Blueprint $table) {
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_opnames', function (Blueprint $table) {
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
    public function up(): void
    {
        Schema::table('stock_opname_items', function (Blueprint $table) {
            // physical_stock dan difference boleh null (belum diisi)
            $table->integer('physical_stock')->nullable()->change();
            $table->integer('difference')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('stock_opname_items', function (Blueprint $table) {
            $table->integer('physical_stock')->nullable(false)->default(0)->change();
            $table->integer('difference')->nullable(false)->default(0)->change();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->enum('scope', ['all', 'category', 'manual'])
                  ->default('all')
                  ->after('opname_date');
            $table->foreignId('category_id')
                  ->nullable()
                  ->after('scope')
                  ->constrained('categories')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['scope', 'category_id']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreignId('purchase_order_id')
                ->nullable()
                ->after('warehouse_id')
                ->constrained('purchase_orders')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['purchase_order_id']);
            $table->dropColumn('purchase_order_id');
        });
    }
};







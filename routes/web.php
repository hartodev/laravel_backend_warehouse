<?php

// ============================================================
//  routes/web.php
//  VERSI RAPI — dikelompokkan: PUBLIC | SUPERADMIN | ADMIN
//  Catatan: tidak ada logika/nama/path route yang diubah.
//           Hanya urutan, indentasi, dan komentar section.
// ============================================================

use App\Http\Controllers\Web\Auth\AuthWebController;
use App\Http\Controllers\Web\Superadmin\ActivityLogController;
use App\Http\Controllers\Web\Admin\ProductUnitController;
use App\Http\Controllers\Web\Admin\SupplierController as AdminSupplierController;
use App\Http\Controllers\Web\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Web\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Web\Admin\PurchaseOrderController as AdminPurchaseOrderController;
use App\Http\Controllers\Web\Admin\StockOpnameController as AdminStockOpnameController;
use App\Http\Controllers\Web\Admin\StockController as AdminStockController;
use App\Http\Controllers\Web\Admin\StockMovementController as AdminStockMovementController;
use App\Http\Controllers\Web\Admin\StockTransferController as AdminStockTransferController;
use App\Http\Controllers\Web\Admin\StockReportController as AdminStockReportController;
use App\Http\Controllers\Web\Admin\ProductSubmissionController as AdminProductSubmissionController;
use App\Http\Controllers\Web\Superadmin\BarcodeController;
use App\Http\Controllers\Web\Superadmin\BudgetRequestController;
use App\Http\Controllers\Web\Superadmin\CashBookController;
use App\Http\Controllers\Web\Superadmin\CategoryController;
use App\Http\Controllers\Web\Superadmin\DashboardController;
use App\Http\Controllers\Web\Superadmin\ExpenseReportController;
use App\Http\Controllers\Web\Superadmin\PaymentController;
use App\Http\Controllers\Web\Superadmin\ProductController;
use App\Http\Controllers\Web\Superadmin\ProductSubmissionController;
use App\Http\Controllers\Web\Superadmin\PurchaseOrderController;
use App\Http\Controllers\Web\Superadmin\RequestController;
use App\Http\Controllers\Web\Superadmin\SalesOrderController;
use App\Http\Controllers\Web\Superadmin\StockController;
use App\Http\Controllers\Web\Superadmin\StockMovementController;
use App\Http\Controllers\Web\Superadmin\StockOpnameController;
use App\Http\Controllers\Web\Superadmin\StockReportController;
use App\Http\Controllers\Web\Superadmin\StockTransferController;
use App\Http\Controllers\Web\Superadmin\SupplierController;
use App\Http\Controllers\Web\Superadmin\UserController;
use App\Http\Controllers\Web\Superadmin\UserCreationRequestController;
use App\Http\Controllers\Web\Superadmin\WarehouseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LandingFaqController;
use App\Http\Controllers\Admin\LandingFeatureController;
use App\Http\Controllers\Admin\LandingStatController;
use App\Http\Controllers\Admin\LandingTestimonialController;

// ────────────────────────────────────────────────────────────
//  PUBLIC / AUTH ROUTES
// ────────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('frontend.landing');
})->name('home');

Route::resource('landing-stats', LandingStatController::class)->except(['show']);
Route::resource('landing-testimonials', LandingTestimonialController::class)->except(['show']);
Route::resource('landing-faqs', LandingFaqController::class)->except(['show']);
Route::resource('landing-features', LandingFeatureController::class)->except(['show']);


Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthWebController::class, 'login']);

Route::get('/register', [AuthWebController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthWebController::class, 'register']);


// Route::middleware('guest')->group(function () {
//     Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
//     Route::post('/login', [AuthWebController::class, 'login']);

//     Route::get('/register', [AuthWebController::class, 'showRegister'])->name('register');
//     Route::post('/register', [AuthWebController::class, 'register']);
// });

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');
});

// User Creation Requests (Pengajuan User Baru) — di luar prefix superadmin/admin
Route::prefix('user-requests')
    ->name('user-requests.')
    ->controller(UserCreationRequestController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::patch('/{userRequest}', 'updateRole')->name('update-role');
        Route::post('/{userRequest}/approve', 'approve')->name('approve');
        Route::post('/{userRequest}/reject', 'reject')->name('reject');
    });


// ══════════════════════════════════════════════════════════════
//  SUPERADMIN WEB PANEL
//  Semua route di bawah ini berprefix nama 'superadmin.'
//  karena berada di dalam Route::prefix('superadmin')->group()
// ══════════════════════════════════════════════════════════════
Route::prefix('superadmin')
    ->name('superadmin.')
    ->middleware(['auth', 'role:super_admin'])
    ->group(function () {

        // ── Dashboard ───────────────────────────────────────
        // NB: sebelumnya ditulis manual ->name('superadmin.dashboard').
        // Sekarang grup sudah punya ->name('superadmin.'), jadi cukup 'dashboard'
        // (hasil akhir tetap sama persis: superadmin.dashboard)
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // ── Master: Categories ──────────────────────────────
        Route::resource('categories', CategoryController::class);

        // ── Master: Suppliers ───────────────────────────────
        Route::resource('suppliers', SupplierController::class);
        Route::patch('suppliers/{supplier}/toggle-active', [SupplierController::class, 'toggleActive'])->name('suppliers.toggle-active');

        // ── Master: Products ─────────────────────────────────
        Route::resource('products', ProductController::class);
         Route::patch('products/{product}/toggle-active', [ProductController::class, 'toggleActive'])
            ->name('products.toggle-active');

        // ── Master: Users ─────────────────────────────────────
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::patch('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

        // ── Warehouses ─────────────────────────────────────────
        Route::resource('warehouses', WarehouseController::class);
        Route::patch('warehouses/{warehouse}/toggle-active', [WarehouseController::class, 'toggleActive'])->name('warehouses.toggle-active');

        // ── Barcodes ───────────────────────────────────────────
        Route::prefix('barcodes')
            ->name('barcodes.')
            ->controller(BarcodeController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/scan', 'scan')->name('scan');
                Route::post('/scan', 'doScan')->name('do-scan');
            });

        // ── Stocks ─────────────────────────────────────────────
        Route::prefix('stocks')
            ->name('stocks.')
            ->controller(StockController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/low-stock', 'lowStock')->name('low-stock');
                Route::get('/warehouse/{warehouse}', 'byWarehouse')->name('by-warehouse');
            });

        // ── Stock Movements ─────────────────────────────────────
        Route::resource('stock-movements', StockMovementController::class)->only(['index', 'create', 'store', 'show']);

        // ── Stock Opnames ─────────────────────────────────────
        Route::resource('stock-opnames', StockOpnameController::class);
        Route::prefix('stock-opnames/{stockOpname}')
            ->name('stock-opnames.')
            ->controller(StockOpnameController::class)
            ->group(function () {
                Route::post('/start', 'start')->name('start');
                Route::post('/complete', 'complete')->name('complete');
                Route::post('/approve', 'approve')->name('approve');
                Route::post('/reject', 'reject')->name('reject');
            });

        // ── Stock Transfers ────────────────────────────────────
        Route::resource('stock-transfers', StockTransferController::class)
            ->only(['index', 'create', 'store', 'show']);

        Route::prefix('stock-transfers/{stockTransfer}')
            ->name('stock-transfers.')
            ->controller(StockTransferController::class)
            ->group(function () {
                Route::post('/approve', 'approve')->name('approve');
                Route::post('/reject', 'reject')->name('reject');
                Route::post('/resolve-discrepancy', 'resolveDiscrepancy')->name('resolve-discrepancy');
            });

        // ── Stock Reports ──────────────────────────────────────
        Route::prefix('stock-reports')
            ->name('stock-reports.')
            ->controller(StockReportController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/summary', 'summary')->name('summary');
                Route::get('/warehouse/{warehouse}', 'byWarehouse')->name('by-warehouse');
            });

        // ── Purchase Orders ─────────────────────────────────────
        Route::resource('purchase-orders', PurchaseOrderController::class);
        Route::prefix('purchase-orders/{purchaseOrder}')
            ->name('purchase-orders.')
            ->controller(PurchaseOrderController::class)
            ->group(function () {
                Route::post('/approve', 'approve')->name('approve');
                Route::post('/reject', 'reject')->name('reject');
                Route::post('/receive', 'receive')->name('receive');
            });

        // ── Sales Orders ────────────────────────────────────────
        Route::resource('sales-orders', SalesOrderController::class);

        // ── Payments ────────────────────────────────────────────
        Route::resource('payments', PaymentController::class);
        Route::post('payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');

        // ── Cash Books ──────────────────────────────────────────
        Route::resource('cash-books', CashBookController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);

        // ── Budget Requests ─── ★ HANYA SATU BLOK — tidak diulang
        Route::resource('budget-requests', BudgetRequestController::class);
        Route::prefix('budget-requests/{budgetRequest}')
            ->name('budget-requests.')
            ->controller(BudgetRequestController::class)
            ->group(function () {
                Route::post('/submit', 'submit')->name('submit');
                Route::post('/approve', 'approve')->name('approve');
                Route::post('/reject', 'reject')->name('reject');
                Route::post('/realisasi', 'realisasi')->name('realisasi');
            });

        Route::resource('budget-verifications', \App\Http\Controllers\Web\Superadmin\BudgetVerificationController::class);
        Route::resource('budget-revisions', \App\Http\Controllers\Web\Superadmin\BudgetRevisionController::class);
        Route::post('budget-revisions/{budgetRevision}/approve', [\App\Http\Controllers\Web\Superadmin\BudgetRevisionController::class, 'approve'])->name('budget-revisions.approve');
        Route::post('budget-revisions/{budgetRevision}/reject', [\App\Http\Controllers\Web\Superadmin\BudgetRevisionController::class, 'reject'])->name('budget-revisions.reject');

        // ── Expense Reports ─────────────────────────────────────
        Route::resource('expense-reports', ExpenseReportController::class);
        Route::post('expense-reports/{expenseReport}/verify', [ExpenseReportController::class, 'verify'])->name('expense-reports.verify');

        // ── Item Requests (Request Barang) — versi 1 (approve/reject/complete) ──
        Route::prefix('requests')
            ->name('requests.')
            ->controller(RequestController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{itemRequest}', 'show')->name('show');
                Route::post('/{itemRequest}/approve', 'approve')->name('approve');
                Route::post('/{itemRequest}/reject', 'reject')->name('reject');
                Route::post('/{itemRequest}/complete', 'complete')->name('complete');
            });

        // ── Item Requests (Request Barang) — versi 2 tahap approval (approve-final) ──
        // NB: blok ini menimpa nama route 'index' dan 'show' dari blok di atas
        // karena nama route sama persis (requests.index / requests.show).
        // Tetap dipertahankan apa adanya sesuai file asli.
        Route::prefix('requests')
            ->name('requests.')
            ->controller(\App\Http\Controllers\Web\Superadmin\RequestController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{request}', 'show')->name('show');
                Route::post('/{request}/approve-final', 'approveFinal')->name('approveFinal');
                Route::post('/{request}/reject', 'reject')->name('reject');
            });

        // ── Product Submissions ─────────────────────────────────
        Route::prefix('product-submissions')
            ->name('product-submissions.')
            ->controller(ProductSubmissionController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{productSubmission}', 'show')->name('show');
                Route::post('/{productSubmission}/approve', 'approve')->name('approve');
                Route::post('/{productSubmission}/reject', 'reject')->name('reject');
            });

        // ── Activity Logs ────────────────────────────────────────
        Route::prefix('activity-logs')
            ->name('activity-logs.')
            ->controller(ActivityLogController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{activityLog}', 'show')->name('show');
            });
    });

// ──────────────────────────────────────────────────────────────
//  REFERENSI NAMA ROUTE LENGKAP (semua berprefix 'superadmin.')
// ──────────────────────────────────────────────────────────────
//
//  BUDGET REQUESTS (resource + submit/approve/reject/realisasi)
//    superadmin.budget_requests.index    | .create  | .store
//    superadmin.budget-requests.show     | .edit    | .update | .destroy
//    superadmin.budget-requests.submit   | .approve | .reject | .realisasi
//
//  BUDGET REVISIONS (resource + approve/reject)
//    superadmin.budget-revisions.*
//    superadmin.budget-revisions.approve | .reject
//
//  BUDGET VERIFICATIONS (index, create, store, show, edit, update)
//    superadmin.budget-verifications.*
//
//  Semua nama route di grup ini SEKARANG benar-benar diprefix 'superadmin.'
//  lewat ->name('superadmin.') pada grup (sebelumnya prefix('superadmin')
//  cuma mem-prefix URL, bukan nama route — sudah diperbaiki).
// ──────────────────────────────────────────────────────────────


// ══════════════════════════════════════════════════════════════
//  ADMIN PANEL
//  Sekarang dibungkus penuh: URL diprefix '/admin', nama route
//  diprefix 'admin.', dan dijaga middleware auth + role.
//  Role yang diizinkan: admin & super_admin.
//
//  PENTING — dampak perubahan ini pada kode lain di project:
//   • URL berubah, mis. "/suppliers" → "/admin/suppliers"
//   • Nama route berubah, mis. route('suppliers.index')
//     → route('admin.suppliers.index')
//   Semua pemanggilan route()/redirect()/<a href> di Blade atau
//   controller yang memakai nama route lama (suppliers.index,
//   products.units.store, purchase-orders.show, dst — tanpa
//   prefix admin.) HARUS diupdate menyesuaikan nama baru ini,
//   kalau tidak akan error "Route not defined".
// ══════════════════════════════════════════════════════════════
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin,super_admin'])
    ->group(function () {

      Route::get('/dashboard', [\App\Http\Controllers\Web\Admin\AdminDashboardController::class, 'index'])->name('dashboard');
        // ── Master: Suppliers / Categories / Products ────────
        // NB: sekarang pakai controller khusus dari namespace
        // App\Http\Controllers\Web\Admin\... (bukan lagi berbagi
        // class dengan section SUPERADMIN). File-file controller
        // di bawah ini HARUS ada / dibuat dulu di folder
        // app/Http/Controllers/Web/Admin/, kalau belum ada akan
        // muncul error "Class not found":
        //   - Admin\SupplierController
        //   - Admin\CategoryController
        //   - Admin\ProductController
        //   - Admin\ProductUnitController (sudah ada, dipakai di bawah)
        //   - Admin\PurchaseOrderController
        //   - Admin\StockOpnameController
        //   - Admin\StockController
        //   - Admin\StockMovementController
        Route::resource('suppliers', AdminSupplierController::class)->except('show');
        Route::resource('categories', AdminCategoryController::class)->except('show');
        Route::resource('products', AdminProductController::class);

        Route::post('products/{product}/units', [ProductUnitController::class, 'store'])
            ->name('products.units.store');
        Route::put('products/{product}/units/{unit}', [ProductUnitController::class, 'update'])
            ->name('products.units.update');
        Route::delete('products/{product}/units/{unit}', [ProductUnitController::class, 'destroy'])
            ->name('products.units.destroy');

        // ── Purchase Order ────────────────────────────────────
        Route::prefix('purchase-orders')
            ->name('purchase-orders.')
            ->controller(AdminPurchaseOrderController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('{po}', 'show')->name('show');
                Route::post('{po}/receive', 'receive')->name('receive');
            });

        // ── Stock Opname ──────────────────────────────────────
        Route::prefix('stock-opnames')
            ->name('stock-opnames.')
            ->controller(AdminStockOpnameController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('products-for-scope', 'productsForScope')->name('products-for-scope');
                Route::get('{opname}', 'show')->name('show');
                Route::post('{opname}/start', 'start')->name('start');
                Route::post('{opname}/save-progress', 'saveProgress')->name('save-progress'); // POST, bukan PATCH (form biasa)
                Route::post('{opname}/complete', 'complete')->name('complete');
            });

        // ── Stock (list + input manual) ────────────────────────
        Route::prefix('stocks')
            ->name('stocks.')
            ->controller(AdminStockController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('manual-in', 'manualIn')->name('manual-in');
            });

        // ── Stock Movement (riwayat, read-only) ────────────────
        // Route::get('stock-movements', [AdminStockMovementController::class, 'index'])->name('stock-movements.index');
        Route::resource('product-units', ProductUnitController::class)->except('show');
        Route::resource('warehouses', WarehouseController::class);
        Route::resource('sales-orders', SalesOrderController::class)->only(['index','create','store','show']);
        Route::patch('sales-orders/{salesOrder}/approve', [SalesOrderController::class, 'approve'])->name('sales-orders.approve');
        Route::patch('sales-orders/{salesOrder}/reject', [SalesOrderController::class, 'reject'])->name('sales-orders.reject');
        Route::resource('user-requests', UserCreationRequestController::class)
            ->only(['index', 'create', 'store', 'show', 'destroy']);
        Route::resource('stock-transfers', AdminStockTransferController::class)->only(['index', 'create', 'store', 'show']);
                    // ── Laporan Stok ─────────────────────────────────────────
        Route::get('stock-reports', [AdminStockReportController::class, 'index'])
            ->name('stock-reports.index');
        Route::prefix('product-submissions')
            ->name('product-submissions.')
            ->controller(AdminProductSubmissionController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('{submission}', 'show')->name('show');
                Route::patch('{submission}/approve', 'approve')->name('approve');
                Route::patch('{submission}/reject', 'reject')->name('reject');
            });

    });

/*
Catatan penting soal urutan route:
- Route::get('products-for-scope', ...) HARUS didaftarkan SEBELUM
  Route::get('{opname}', ...) — kalau tidak, Laravel akan menganggap
  "products-for-scope" sebagai value {opname} dan salah route.
  Di atas sudah diurutkan dengan benar.
*/




<?php

// ============================================================
//  routes/web.php  — SuperAdmin Web Routes
//  VERSI BERSIH — duplikat budget-requests dihapus
// ============================================================
use App\Http\Controllers\Web\Auth\AuthWebController;
use App\Http\Controllers\Web\ActivityLogController;
use App\Http\Controllers\Web\BarcodeController;
use App\Http\Controllers\Web\BudgetRequestController;
use App\Http\Controllers\Web\BudgetRevisionController;
use App\Http\Controllers\Web\BudgetVerificationController;
use App\Http\Controllers\Web\CashBookController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ExpenseReportController;
use App\Http\Controllers\Web\PaymentController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\ProductSubmissionController;
use App\Http\Controllers\Web\PurchaseOrderController;
use App\Http\Controllers\Web\RequestController;
use App\Http\Controllers\Web\SalesOrderController;
use App\Http\Controllers\Web\StockController;
use App\Http\Controllers\Web\StockMovementController;
use App\Http\Controllers\Web\StockOpnameController;
use App\Http\Controllers\Web\StockReportController;
use App\Http\Controllers\Web\StockTransferController;
use App\Http\Controllers\Web\SupplierController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthWebController::class, 'login']);

    Route::get('/register', [AuthWebController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthWebController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');
});

// ──────────────────────────────────────────────────────────────
//  SUPERADMIN WEB PANEL
//  Semua route di sini otomatis berprefix nama 'superadmin.'
//  karena berada di dalam Route::prefix('superadmin')->group()
// ──────────────────────────────────────────────────────────────
Route::prefix('superadmin')
    ->middleware(['auth', 'role:super_admin'])
    ->group(function () {

    // ── Dashboard ──────────────────────────────────────────
    Route::get('/', [DashboardController::class, 'index'])->name('superadmin.dashboard');

        // ── Master: Categories ─────────────────────────────────
        Route::resource('categories', CategoryController::class);

        // ── Master: Suppliers ──────────────────────────────────
        Route::resource('suppliers', SupplierController::class);
    Route::patch('suppliers/{supplier}/toggle-active', [SupplierController::class, 'toggleActive'])->name('suppliers.toggle-active');

        // ── Master: Products ───────────────────────────────────
        Route::resource('products', ProductController::class);
    Route::patch('products/{product}/toggle-active', [ProductController::class, 'toggleActive'])->name('products.toggle-active');

        // ── Master: Users ──────────────────────────────────────
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

    // ── Stock Movements ────────────────────────────────────
    Route::resource('stock-movements', StockMovementController::class)->only(['index', 'create', 'store', 'show']);

        // ── Stock Opnames ──────────────────────────────────────
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
        Route::resource('stock-transfers', StockTransferController::class);
    Route::prefix('stock-transfers/{stockTransfer}')
        ->name('stock-transfers.')
        ->controller(StockTransferController::class)
        ->group(function () {
            Route::post('/approve', 'approve')->name('approve');
        Route::post('/reject', 'reject')->name('reject');
        Route::post('/send', 'send')->name('send');
            Route::post('/receive', 'receive')->name('receive');
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

        // ── Purchase Orders ────────────────────────────────────
        Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::prefix('purchase-orders/{purchaseOrder}')
        ->name('purchase-orders.')
        ->controller(PurchaseOrderController::class)
        ->group(function () {
            Route::post('/approve', 'approve')->name('approve');
        Route::post('/reject', 'reject')->name('reject');
            Route::post('/receive', 'receive')->name('receive');
        });

        // ── Sales Orders ───────────────────────────────────────
        Route::resource('sales-orders', SalesOrderController::class);

        // ── Payments ───────────────────────────────────────────
        Route::resource('payments', PaymentController::class);
    Route::post('payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');

    // ── Cash Books ─────────────────────────────────────────
    Route::resource('cash-books', CashBookController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);

    // ────────────────────────────────────────────────────────
    // ── Budget Requests ─── ★ HANYA SATU BLOK — tidak diulang
    // ────────────────────────────────────────────────────────
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

    Route::resource('budget-verifications', \App\Http\Controllers\Web\BudgetVerificationController::class);
    Route::resource('budget-revisions', \App\Http\Controllers\Web\BudgetRevisionController::class);
    Route::post('budget-revisions/{budgetRevision}/approve', [\App\Http\Controllers\Web\BudgetRevisionController::class, 'approve'])->name('budget-revisions.approve');
    Route::post('budget-revisions/{budgetRevision}/reject', [\App\Http\Controllers\Web\BudgetRevisionController::class, 'reject'])->name('budget-revisions.reject');
        // ── Expense Reports ────────────────────────────────────
        Route::resource('expense-reports', ExpenseReportController::class);
    Route::post('expense-reports/{expenseReport}/verify', [ExpenseReportController::class, 'verify'])->name('expense-reports.verify');

    // ── Item Requests (Request Barang) ─────────────────────
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

    // ── Item Requests (Request Barang) — 2 tahap approval ──
    Route::prefix('requests')
        ->name('requests.')
        ->controller(\App\Http\Controllers\Web\RequestController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{request}', 'show')->name('show');
            Route::post('/{request}/approve-final', 'approveFinal')->name('approveFinal');
            Route::post('/{request}/reject', 'reject')->name('reject');
        });

    // ── Product Submissions ────────────────────────────────
    Route::prefix('product-submissions')
        ->name('product-submissions.')
        ->controller(ProductSubmissionController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{productSubmission}', 'show')->name('show');
            Route::post('/{productSubmission}/approve', 'approve')->name('approve');
            Route::post('/{productSubmission}/reject', 'reject')->name('reject');
        });

    // ── Activity Logs ──────────────────────────────────────
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
//    superadmin.budget-requests.index    | .create  | .store
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
//  (route lain tidak berubah dari versi sebelumnya)
// ──────────────────────────────────────────────────────────────

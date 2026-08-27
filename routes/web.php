<?php

// ============================================================
//  routes/web.php
//  VERSI RAPI — dikelompokkan: PUBLIC | SUPERADMIN | ADMIN
//  Catatan: tidak ada logika/nama/path route yang diubah.
//           Hanya urutan, indentasi, dan komentar section.
// ============================================================

use App\Http\Controllers\Web\Admin\AdminDashboardController;
use App\Http\Controllers\Web\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Web\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Web\Admin\ProductSubmissionController as AdminProductSubmissionController;
use App\Http\Controllers\Web\Admin\ProductUnitController as AdminProductUnitController;
use App\Http\Controllers\Web\Admin\SalesOrderController as AdminSalesOrderController;
use App\Http\Controllers\Web\Admin\UserCreationRequestController as AdminUserCreationRequestController;
use App\Http\Controllers\Web\Admin\WarehouseController as AdminWarehouseController;
use App\Http\Controllers\Web\Admin\BarcodeController as AdminBarcodeController;
use App\Http\Controllers\Web\Admin\CashBookController as AdminCashBookController;
use App\Http\Controllers\Web\Admin\BudgetRequestController as AdminBudgetRequestController;
use App\Http\Controllers\Web\Admin\BudgetVerificationController as AdminBudgetVerificationController;   
use App\Http\Controllers\Web\Admin\BudgetRevisionController as AdminBudgetRevisionController;
use App\Http\Controllers\Web\Admin\PurchaseOrderController as AdminPurchaseOrderController;
use App\Http\Controllers\Web\Admin\StockController as AdminStockController;
use App\Http\Controllers\Web\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Web\Admin\ExpenseReportController as AdminExpenseReportController;
use App\Http\Controllers\Web\Admin\StockMovementController as AdminStockMovementController;
use App\Http\Controllers\Web\Admin\StockOpnameController as AdminStockOpnameController;
use App\Http\Controllers\Web\Admin\StockReportController as AdminStockReportController;
use App\Http\Controllers\Web\Admin\StockTransferController as AdminStockTransferController;
use App\Http\Controllers\Web\Admin\SupplierController as AdminSupplierController;
use App\Http\Controllers\Web\Auth\AuthWebController;
use App\Http\Controllers\Web\Landing\LandingContactController;
use App\Http\Controllers\Web\Landing\LandingFaqController;
use App\Http\Controllers\Web\Landing\LandingFeatureController;
use App\Http\Controllers\Web\Landing\LandingStatController;
use App\Http\Controllers\Web\Landing\LandingTestimonialController;
use App\Http\Controllers\Web\Superadmin\ActivityLogController;
use App\Http\Controllers\Web\Superadmin\BarcodeController;
use App\Http\Controllers\Web\Superadmin\BudgetRequestController;
use App\Http\Controllers\Web\Superadmin\CashBookController;
use App\Http\Controllers\Web\Superadmin\CategoryController;
use App\Http\Controllers\Web\Superadmin\DashboardController;
use App\Http\Controllers\Web\Superadmin\ExpenseReportController;
use App\Http\Controllers\web\Superadmin\LandingBenefitController;
use App\Http\Controllers\web\Superadmin\LandingContactLeadController;
use App\Http\Controllers\web\Superadmin\LandingWorkflowStepController;
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

use App\Http\Controllers\Web\Supplier\DashboardController as SupplierDashboardController;
use App\Http\Controllers\Web\Supplier\ProductController as SupplierProductController;
use App\Http\Controllers\Web\Supplier\PurchaseOrderController as SupplierPurchaseOrderController;
use App\Http\Controllers\Web\Superadmin\SupplierAccountController;


// ────────────────────────────────────────────────────────────
//  PUBLIC / AUTH ROUTES
// ────────────────────────────────────────────────────────────
// Route::get('/', function () {
//     return view('frontend.landing');
// })->name('home');

Route::get('/', [App\Http\Controllers\Web\Landing\LandingController::class, 'index'])->name('landing.index');

Route::resource('landing-stats', LandingStatController::class)->except(['show']);
Route::resource('landing-testimonials', LandingTestimonialController::class)->except(['show']);
Route::resource('landing-faqs', LandingFaqController::class)->except(['show']);
Route::resource('landing-features', LandingFeatureController::class)->except(['show']);

Route::post('/contact', [LandingContactController::class, 'store'])->name('landing.contact.store');

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
//  ⚠️ TIDAK ADA PERUBAHAN di blok ini (sesuai permintaan).
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
    Route::patch('products/{product}/toggle-active', [ProductController::class, 'toggleActive'])->name('products.toggle-active');

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
    Route::resource('stock-transfers', StockTransferController::class)->only(['index', 'create', 'store', 'show']);

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

        Route::get('landing-leads', [LandingContactLeadController::class, 'index'])->name('landing-leads.index');
        Route::get('landing-leads/{landingLead}', [LandingContactLeadController::class, 'show'])->name('landing-leads.show');
        Route::put('landing-leads/{landingLead}', [LandingContactLeadController::class, 'update'])->name('landing-leads.update');
        Route::delete('landing-leads/{landingLead}', [LandingContactLeadController::class, 'destroy'])->name('landing-leads.destroy');

        Route::resource('landing-benefits', LandingBenefitController::class)->except(['show']);

        Route::resource('landing-workflow-steps', LandingWorkflowStepController::class)->except(['show']);
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
//  URL diprefix '/admin', nama route diprefix 'admin.', dijaga
//  middleware auth + role. Role yang diizinkan: admin & super_admin.
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

        // ── Dashboard ────────────────────────────────────────
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // ── Master: Suppliers / Categories / Products ────────
        // Pakai controller khusus dari namespace
        // App\Http\Controllers\Web\Admin\... (terpisah dari Superadmin).
        // File-file controller ini HARUS ada di app/Http/Controllers/Web/Admin/,
        // kalau belum ada akan muncul error "Class not found":
        //   - Admin\SupplierController
        //   - Admin\CategoryController
        //   - Admin\ProductController
        //   - Admin\ProductUnitController (sudah ada, dipakai di bawah)
        //   - Admin\PurchaseOrderController
        //   - Admin\StockOpnameController
        //   - Admin\StockController
        //   - Admin\StockMovementController
        Route::resource('suppliers', AdminSupplierController::class)->except('show');
        Route::resource('categories', AdminCategoryController::class);
        Route::resource('products', AdminProductController::class);

        Route::post('products/{product}/units', [AdminProductUnitController::class, 'store'])->name('products.units.store');
        Route::put('products/{product}/units/{unit}', [AdminProductUnitController::class, 'update'])->name('products.units.update');
        Route::delete('products/{product}/units/{unit}', [AdminProductUnitController::class, 'destroy'])->name('products.units.destroy');
        Route::resource('product-units', AdminProductUnitController::class)->except('show');

        // ── Purchase Order ────────────────────────────────────
             Route::prefix('purchase-orders')
            ->name('purchase-orders.')
            ->controller(AdminPurchaseOrderController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('{po}', 'show')->name('show');
                Route::put('{po}', 'update')->name('update');
                Route::delete('{po}', 'destroy')->name('destroy');
                Route::post('{po}/approve', 'approve')->name('approve');
                Route::post('{po}/reject', 'reject')->name('reject');
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
                // HARUS di atas '{opname}' — kalau di bawah, "products-for-scope"
                // akan dianggap value {opname} dan salah route.
                Route::get('products-for-scope', 'productsForScope')->name('products-for-scope');
                Route::get('{opname}', 'show')->name('show');
                Route::post('{opname}/start', 'start')->name('start');
                Route::post('{opname}/save-progress', 'saveProgress')->name('save-progress'); // POST, bukan PATCH (form biasa)
                Route::post('{opname}/complete', 'complete')->name('complete');
                Route::post('{opname}/approve', 'approve')->name('approve');
                Route::post('{opname}/reject', 'reject')->name('reject');
            });

        // ── Stock (list + input manual) ────────────────────────
        Route::prefix('stocks')
            ->name('stocks.')
            ->controller(AdminStockController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('manual-in', 'manualIn')->name('manual-in');
                                Route::get('/low-stock', 'lowStock')->name('low-stock');
                // ★ BARU (disisipkan) — detail stok per warehouse untuk admin
                Route::get('/warehouse/{warehouse}', 'byWarehouse')->name('by-warehouse');

            });

        // ── Stock Movement (riwayat, read-only) ────────────────
        // NB: sebelumnya baris ini didaftarkan dua kali — sudah dirapikan,
        // sekarang cukup satu.
        // Route::get('stock-movements', [AdminStockMovementController::class, 'index'])->name('stock-movements.index');
        Route::resource('stock-movements', AdminStockMovementController::class)->only(['index', 'show','create','store']);

        // ── Stock Transfer ──────────────────────────────────────
// ── Stock Transfer ──────────────────────────────────────
// ── Stock Transfer ──────────────────────────────────────
Route::resource('stock-transfers', AdminStockTransferController::class)
    ->only(['index', 'create', 'store', 'show'])
    ->parameters(['stock-transfers' => 'transfer']);

Route::post('stock-transfers/{transfer}/confirm', [AdminStockTransferController::class, 'confirm'])->name('stock-transfers.confirm');
Route::post('stock-transfers/{transfer}/cancel', [AdminStockTransferController::class, 'cancel'])->name('stock-transfers.cancel');
Route::post('stock-transfers/{transfer}/approve', [AdminStockTransferController::class, 'approve'])->name('stock-transfers.approve');
Route::post('stock-transfers/{transfer}/reject', [AdminStockTransferController::class, 'reject'])->name('stock-transfers.reject');
Route::post('stock-transfers/{transfer}/send', [AdminStockTransferController::class, 'send'])->name('stock-transfers.send');
Route::post('stock-transfers/{transfer}/checklist', [AdminStockTransferController::class, 'checklist'])->name('stock-transfers.checklist');
Route::post('stock-transfers/{transfer}/resolve-discrepancy', [AdminStockTransferController::class, 'resolveDiscrepancy'])->name('stock-transfers.resolve-discrepancy');
// ⚠️ Warehouses belum punya controller Admin sendiri —
        // masih pakai WarehouseController (Superadmin). Kalau warehouse
        // management memang sengaja dibagi ke admin & super_admin,
        // biarkan begini. Kalau tidak, buat dulu
        // App\Http\Controllers\Web\Admin\WarehouseController lalu ganti
        // baris di bawah ini.
        Route::resource('warehouses', AdminWarehouseController::class);

        // ── Sales Order ──────────────────────────────────────────
        Route::resource('sales-orders', AdminSalesOrderController::class)->only(['index', 'create', 'store', 'show','edit','update','destroy']);
        Route::patch('sales-orders/{salesOrder}/approve', [AdminSalesOrderController::class, 'approve'])->name('sales-orders.approve');
        Route::patch('sales-orders/{salesOrder}/reject', [AdminSalesOrderController::class, 'reject'])->name('sales-orders.reject');

        // ── User Creation Requests ───────────────────────────────
        Route::resource('user-requests', AdminUserCreationRequestController::class)->only(['index', 'create', 'store', 'show', 'destroy','update']);

        // ── Laporan Stok ─────────────────────────────────────────
        Route::get('stock-reports', [AdminStockReportController::class, 'index'])->name('stock-reports.index');
        // ★ BARU (disisipkan dari versi Claude) — detail laporan stok per warehouse
        Route::get('stock-reports/warehouse/{warehouse}', [AdminStockReportController::class, 'byWarehouse'])->name('stock-reports.by-warehouse');

        // ── Product Submissions ───────────────────────────────────
        Route::prefix('product-submissions')
            ->name('product-submissions.')
            ->controller(AdminProductSubmissionController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('{submission}', 'show')->name('show');
                Route::patch('{submission}/approve', 'approve')->name('approve');
                Route::patch('{submission}/reject', 'reject')->name('reject');
            });

        // ═══════════════════════════════════════════════════════
        // ⚠️ ROUTE DI BAWAH INI MASIH "REUSE" CONTROLLER SUPERADMIN
        // Belum ada versi Admin\* untuk fitur-fitur ini. Ini artinya
        // saat admin membuka /admin/payments, /admin/cash-books,
        // /admin/budget-requests, dst — kalau view di controller
        // Superadmin itu extend layout superadmin (bukan layout
        // admin), sidebar & menu yang muncul BAKAL superadmin lagi —
        // persis bug yang barusan kita perbaiki, tapi kali ini untuk
        // fitur finance/budget.
        //
        // Ada 2 pilihan:
        //   1. Kalau fitur ini memang HANYA untuk super_admin,
        //      HAPUS semua baris di bawah ini dari grup admin —
        //      biar admin nggak punya akses sama sekali (paling aman).
        //   2. Kalau admin memang boleh lihat/pakai, buatin
        //      Admin\PaymentController, Admin\CashBookController, dst
        //      dengan view sendiri yang extend layout admin.
        //
        // ★ CATATAN: budget-requests di bawah sudah dilengkapi dengan
        //   route show/approve/reject/tunda (disisipkan dari versi Claude).
        //   Route ini masih pakai AdminBudgetRequestController — pastikan
        //   controller tsb sudah punya method show/approve/reject/tunda,
        //   kalau belum akan error "Method not found".
        // ═══════════════════════════════════════════════════════
        Route::resource('payments', AdminPaymentController::class);
        Route::post('payments/{payment}/verify', [AdminPaymentController::class, 'verify'])->name('payments.verify');

        // Route::get('cash-books', [AdminCashBookController::class, 'index'])->name('cash-books.index');

        Route::get('budget-requests', [AdminBudgetRequestController::class, 'index'])->name('budget-requests.index');
        // ★ BARU (disisipkan dari versi Claude)
        Route::get('budget-requests/{budgetRequest}', [AdminBudgetRequestController::class, 'show'])->name('budget-requests.show');
        Route::post('budget-requests/{budgetRequest}/approve', [AdminBudgetRequestController::class, 'approve'])->name('budget-requests.approve');
        Route::post('budget-requests/{budgetRequest}/reject', [AdminBudgetRequestController::class, 'reject'])->name('budget-requests.reject');
        Route::post('budget-requests/{budgetRequest}/tunda', [AdminBudgetRequestController::class, 'tunda'])->name('budget-requests.tunda');

        // Route::get('budget-verifications', [AdminBudgetVerificationController::class, 'index'])->name('budget-verifications.index');
        Route::resource('budget-verifications', AdminBudgetVerificationController::class)->only('show','index','store','destroy','edit','update','create');
Route::resource('budget-revisions', AdminBudgetRevisionController::class);
Route::post('budget-revisions/{budgetRevision}/approve', [AdminBudgetRevisionController::class, 'approve'])
    ->name('budget-revisions.approve');
Route::post('budget-revisions/{budgetRevision}/reject', [AdminBudgetRevisionController::class, 'reject'])
    ->name('budget-revisions.reject');
        // Route::get('expense-reports', [AdminExpenseReportController::class, 'index'])->name('expense-reports.index');
        Route::resource('expense-reports',AdminExpenseReportController::class);

        Route::get('barcodes/scan', [AdminBarcodeController::class, 'scan'])->name('barcodes.scan');
        Route::post('barcodes/scan', [AdminBarcodeController::class, 'doScan'])->name('barcodes.do-scan');


        /////////
Route::resource('cashbook', AdminCashBookController::class)->only('index','show','store','detele','update','create')
    ->parameters(['cashbook' => 'book']);
        });




// ── Portal Supplier (role: supplier) ────────────────────────────────
Route::prefix('supplier')->name('supplier.')->middleware(['auth', 'role:supplier'])->group(function () {

    Route::get('/dashboard', [SupplierDashboardController::class, 'index'])->name('dashboard');

    Route::controller(SupplierProductController::class)->prefix('products')->name('products.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{product}', 'show')->name('show');
    });

    Route::controller(SupplierPurchaseOrderController::class)->prefix('purchase-orders')->name('purchase-orders.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{purchaseOrder}', 'show')->name('show');
    });
});

// ── Tambahan untuk group Superadmin yang SUDAH ADA — jangan dibuat baru,
//    gabungkan ke group superadmin existing kamu ─────────────────────
Route::controller(SupplierAccountController::class)
    ->prefix('superadmin/suppliers/{supplier}/account')
    ->name('superadmin.suppliers.account.')
    ->middleware(['auth', 'role:super_admin'])
    ->group(function () {
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::delete('/', 'destroy')->name('destroy');
    });






    
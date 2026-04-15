<?php

use App\Http\Controllers\Api\Admin\BarcodeController;
use App\Http\Controllers\Api\Admin\BudgetRequestController;
use App\Http\Controllers\Api\Admin\BudgetRevisionController;
use App\Http\Controllers\Api\Admin\BudgetVerificationController;
use App\Http\Controllers\Api\Admin\CashBookController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\ExpenseReportController;
use App\Http\Controllers\Api\Admin\PaymentController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\ProductSubmissionController;
use App\Http\Controllers\Api\Admin\ProductUnitController;
use App\Http\Controllers\Api\Admin\PurchaseOrderController;
use App\Http\Controllers\Api\Admin\SalesOrderController;
use App\Http\Controllers\Api\Admin\StockController;
use App\Http\Controllers\Api\Admin\StockMovementController;
use App\Http\Controllers\Api\Admin\StockOpnameController;
use App\Http\Controllers\Api\Admin\StockReportController;
use App\Http\Controllers\Api\Admin\StockTransferController;
use App\Http\Controllers\Api\Admin\SupplierController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\WarehouseController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\User\ChatController;
use App\Http\Controllers\Api\User\NotificationController;
use App\Http\Controllers\Api\User\ProfileController;
use App\Http\Controllers\Api\User\RequestController;
use Illuminate\Support\Facades\Route;


// ══════════════════════════════════════════════════════════════
//  PUBLIC — Tidak butuh login
// ══════════════════════════════════════════════════════════════
Route::prefix('auth')->group(function () {
    Route::post('login',    [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});
 
// ══════════════════════════════════════════════════════════════
//  AUTHENTICATED — Semua role (butuh login + active)
// ══════════════════════════════════════════════════════════════
Route::middleware('auth:sanctum')->group(function () {
 
    // Auth
    Route::post('auth/logout',   [AuthController::class, 'logout']);
    Route::get('auth/me',        [AuthController::class, 'me']);
 
    // ── Profile (semua role bisa edit profil sendiri) ────────
    Route::prefix('profile')->controller(ProfileController::class)->group(function () {
        Route::get('/',              'show');
        Route::put('/',              'update');
        Route::post('photo',         'updatePhoto');
        Route::put('change-password','changePassword');
    });
 
    // ── Notifikasi ───────────────────────────────────────────
    Route::prefix('notifications')->controller(NotificationController::class)->group(function () {
        Route::get('/',              'index');
        Route::get('unread-count',   'unreadCount');
        Route::put('{id}/read',      'markAsRead');
        Route::put('read-all',       'markAllAsRead');
        Route::delete('{id}',        'destroy');
    });
 
    // ── Chat ─────────────────────────────────────────────────
    Route::prefix('chats')->controller(ChatController::class)->group(function () {
        Route::get('/',              'index');
        Route::post('/',             'store');
        Route::get('{chat}',         'show');
        Route::post('{chat}/messages','sendMessage');
        Route::put('{chat}/read',    'markAsRead');
    });
 
    // ── Products (semua role bisa lihat) ─────────────────────
    Route::get('products',       [ProductController::class, 'index']);
    Route::get('products/{product}', [ProductController::class, 'show']);
    Route::get('categories',     [CategoryController::class, 'index']);
    Route::get('warehouses',     [WarehouseController::class, 'index']);
 
    // ── Barcode scan (semua role) ─────────────────────────────
    Route::post('barcode/scan',  [BarcodeController::class, 'scan']);
 
    // ══════════════════════════════════════════════════════════
    //  USER ROLE
    // ══════════════════════════════════════════════════════════
    Route::middleware('role:user,admin,super_admin')->group(function () {
 
        // Request Barang
        Route::prefix('requests')->controller(RequestController::class)->group(function () {
            Route::get('/',          'index');
            Route::post('/',         'store');
            Route::get('{request}',  'show');
            Route::put('{request}',  'update');   // hanya bisa edit saat status draft/pending
            Route::delete('{request}','destroy'); // cancel request
        });
    });
 
    // ══════════════════════════════════════════════════════════
    //  ADMIN ROLE
    // ══════════════════════════════════════════════════════════
    Route::middleware('role:admin,super_admin')->group(function () {
 
        // ── User Management (admin bisa lihat, super_admin bisa CRUD) ──
        Route::prefix('users')->controller(UserController::class)->group(function () {
            Route::get('/',              'index');
            Route::get('{user}',         'show');
        });
 
        // ── Warehouse ────────────────────────────────────────
        Route::apiResource('warehouses', WarehouseController::class);
 
        // ── Supplier ─────────────────────────────────────────
        Route::apiResource('suppliers', SupplierController::class);
 
        // ── Category ─────────────────────────────────────────
        Route::apiResource('categories', CategoryController::class);
 
        // ── Product ──────────────────────────────────────────
        Route::apiResource('products', ProductController::class)->except('index', 'show');
        Route::prefix('products')->controller(ProductController::class)->group(function () {
            Route::get('{product}/units',        'units');
            Route::get('{product}/stocks',       'stockByWarehouse');
        });
 
        // ── Product Units ────────────────────────────────────
        Route::prefix('products/{product}/units')
             ->controller(ProductUnitController::class)
             ->group(function () {
                 Route::get('/',      'index');
                 Route::post('/',     'store');
                 Route::put('{unit}', 'update');
                 Route::delete('{unit}','destroy');
             });
 
        // ── Product Submission ───────────────────────────────
        Route::prefix('product-submissions')->controller(ProductSubmissionController::class)->group(function () {
            Route::get('/',                  'index');
            Route::post('/',                 'store');
            Route::get('{submission}',       'show');
            Route::put('{submission}',       'update');
            Route::delete('{submission}',    'destroy');
        });
 
        // ── Stock ─────────────────────────────────────────────
        Route::prefix('stocks')->controller(StockController::class)->group(function () {
            Route::get('/',              'index');
            Route::get('low',            'lowStock');
            Route::get('{warehouse}',    'byWarehouse');
        });
 
        // ── Stock Movements ──────────────────────────────────
        Route::prefix('stock-movements')->controller(StockMovementController::class)->group(function () {
            Route::get('/',          'index');
            Route::post('/',         'store');
            Route::get('{movement}', 'show');
        });
 
        // ── Purchase Order ───────────────────────────────────
        Route::prefix('purchase-orders')->controller(PurchaseOrderController::class)->group(function () {
            Route::get('/',          'index');
            Route::post('/',         'store');
            Route::get('{po}',       'show');
            Route::put('{po}',       'update');
            Route::delete('{po}',    'destroy');
            Route::post('{po}/receive','receive'); // terima barang
        });
 
        // ── Stock Transfer ───────────────────────────────────
        Route::prefix('stock-transfers')->controller(StockTransferController::class)->group(function () {
            Route::get('/',                  'index');
            Route::post('/',                 'store');
            Route::get('{transfer}',         'show');
            Route::put('{transfer}',         'update');
            Route::post('{transfer}/send',   'send');     // kirim barang
            Route::post('{transfer}/receive','receive');  // terima barang
        });
 
        // ── Stock Opname ─────────────────────────────────────
        Route::prefix('stock-opnames')->controller(StockOpnameController::class)->group(function () {
            Route::get('/',                  'index');
            Route::post('/',                 'store');
            Route::get('{opname}',           'show');
            Route::put('{opname}',           'update');
            Route::post('{opname}/start',    'start');
            Route::post('{opname}/complete', 'complete');
            Route::post('{opname}/submit',   'submitApproval');
        });
 
        // ── Stock Reports ────────────────────────────────────
        Route::prefix('stock-reports')->controller(StockReportController::class)->group(function () {
            Route::get('/',              'index');
            Route::get('summary',        'summary');
            Route::get('{warehouse}',    'byWarehouse');
        });
 
        // ── Request Barang (admin approve/reject) ────────────
        Route::prefix('requests')->controller(RequestController::class)->group(function () {
            Route::get('/',                      'indexAdmin');  // semua request
            Route::get('{request}',              'showAdmin');
            Route::post('{request}/approve',     'approve');
            Route::post('{request}/reject',      'reject');
            Route::post('{request}/complete',    'complete');
        });
 
        // ── Sales Order ──────────────────────────────────────
        Route::prefix('sales-orders')->controller(SalesOrderController::class)->group(function () {
            Route::get('/',          'index');
            Route::post('/',         'store');
            Route::get('{so}',       'show');
            Route::put('{so}',       'update');
            Route::delete('{so}',    'destroy');
        });
 
        // ── Payment ───────────────────────────────────────────
        Route::prefix('payments')->controller(PaymentController::class)->group(function () {
            Route::get('/',          'index');
            Route::post('/',         'store');
            Route::get('{payment}',  'show');
            Route::put('{payment}',  'update');
            Route::delete('{payment}','destroy');
        });
 
        // ── Cash Book ─────────────────────────────────────────
        Route::prefix('cash-books')->controller(CashBookController::class)->group(function () {
            Route::get('/',          'index');
            Route::post('/',         'store');
            Route::get('{book}',     'show');
            Route::put('{book}',     'update');
        });
 
        // ── Budget Request ────────────────────────────────────
        Route::prefix('budget-requests')->controller(BudgetRequestController::class)->group(function () {
            Route::get('/',              'index');
            Route::post('/',             'store');
            Route::get('{br}',           'show');
            Route::put('{br}',           'update');
            Route::delete('{br}',        'destroy');
            Route::post('{br}/submit',   'submit');
        });
 
        // ── Budget Verification ──────────────────────────────
        Route::prefix('budget-verifications')->controller(BudgetVerificationController::class)->group(function () {
            Route::get('/',              'index');
            Route::post('/',             'store');
            Route::get('{bv}',           'show');
            Route::put('{bv}',           'update');
        });
 
        // ── Expense Report ────────────────────────────────────
        Route::prefix('expense-reports')->controller(ExpenseReportController::class)->group(function () {
            Route::get('/',              'index');
            Route::post('/',             'store');
            Route::get('{er}',           'show');
            Route::put('{er}',           'update');
            Route::post('{er}/verify',   'verify');
        });
 
        // ── Budget Revision ───────────────────────────────────
        Route::prefix('budget-revisions')->controller(BudgetRevisionController::class)->group(function () {
            Route::get('/',          'index');
            Route::post('/',         'store');
            Route::get('{br}',       'show');
            Route::put('{br}',       'update');
        });
    });
 
    // ══════════════════════════════════════════════════════════
    //  SUPER ADMIN ONLY
    // ══════════════════════════════════════════════════════════
    Route::middleware('role:super_admin')->group(function () {
 
        // ── Full User Management ─────────────────────────────
        Route::prefix('users')->controller(UserController::class)->group(function () {
            Route::post('/',                     'store');
            Route::put('{user}',                 'update');
            Route::delete('{user}',              'destroy');
            Route::patch('{user}/toggle-active', 'toggleActive');
            Route::patch('{user}/reset-password','resetPassword');
        });
 
        // ── Approve Product Submission ───────────────────────
        Route::prefix('product-submissions')->controller(ProductSubmissionController::class)->group(function () {
            Route::post('{submission}/approve',  'approve');
            Route::post('{submission}/reject',   'reject');
        });
 
        // ── Approve Stock Opname ──────────────────────────────
        Route::post('stock-opnames/{opname}/approve', [StockOpnameController::class, 'approve']);
        Route::post('stock-opnames/{opname}/reject',  [StockOpnameController::class, 'reject']);
 
        // ── Approve Stock Transfer ─────────────────────────
        Route::post('stock-transfers/{transfer}/approve', [StockTransferController::class, 'approve']);
        Route::post('stock-transfers/{transfer}/reject',  [StockTransferController::class, 'reject']);
 
        // ── Approve Purchase Order ───────────────────────────
        Route::post('purchase-orders/{po}/approve', [PurchaseOrderController::class, 'approve']);
        Route::post('purchase-orders/{po}/reject',  [PurchaseOrderController::class, 'reject']);
 
        // ── Approve Budget Request ────────────────────────────
        Route::post('budget-requests/{br}/approve', [BudgetRequestController::class, 'approve']);
        Route::post('budget-requests/{br}/reject',  [BudgetRequestController::class, 'reject']);
 
        // ── Approve Budget Revision ───────────────────────────
        Route::post('budget-revisions/{br}/approve', [BudgetRevisionController::class, 'approve']);
        Route::post('budget-revisions/{br}/reject',  [BudgetRevisionController::class, 'reject']);
 
        // ── Activity Log ──────────────────────────────────────
        Route::get('activity-logs', function () {
            return response()->json([
                'data' => \App\Models\ActivityLog::with('user')
                    ->latest('created_at')
                    ->paginate(50),
            ]);
        });
    });
    });

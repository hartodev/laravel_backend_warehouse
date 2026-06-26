<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BudgetRequest;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\StockOpname;
use App\Models\StockTransfer;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Stats Utama ───────────────────────────────────────
        $stats = [
            'total_products'   => Product::where('is_active', true)->count(),
            'total_warehouses' => Warehouse::where('is_active', true)->count(),
            'total_suppliers'  => Supplier::where('is_active', true)->count(),
            'total_users'      => User::where('is_active', true)->count(),
        ];

        // ── Stok Menipis ──────────────────────────────────────
        $lowStocks = Stock::with(['product:id,name,sku,unit,min_stock', 'warehouse:id,name'])
            ->whereHas('product', fn($q) => $q->whereColumn('stocks.quantity', '<=', 'products.min_stock'))
            ->orderBy('quantity')
            ->limit(10)
            ->get();

        // ── PO Pending ────────────────────────────────────────
        $pendingPOs = PurchaseOrder::with(['supplier:id,name', 'warehouse:id,name'])
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        // ── Pengajuan Anggaran Pending ────────────────────────
        $pendingBudgets = BudgetRequest::with('user:id,name')
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        // ── Transfer Pending/In-Transit ───────────────────────
        $activeTransfers = StockTransfer::with(['fromWarehouse:id,name', 'toWarehouse:id,name'])
            ->whereIn('status', ['pending', 'approved', 'in_transit'])
            ->latest()
            ->limit(5)
            ->get();

        // ── Opname Pending Approval ───────────────────────────
        $pendingOpnames = StockOpname::with('warehouse:id,name')
            ->where('status', 'pending_approval')
            ->latest()
            ->limit(5)
            ->get();

        // ── Pergerakan Stok 7 Hari Terakhir (Chart) ──────────
        $movementChart = StockMovement::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw("SUM(CASE WHEN type='in' THEN quantity ELSE 0 END) as total_in"),
            DB::raw("SUM(CASE WHEN type='out' THEN quantity ELSE 0 END) as total_out")
        )
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ── Nilai Stok per Gudang ─────────────────────────────
        $stockValueByWarehouse = Warehouse::withSum(
            [
                'stocks as stock_value' => fn($q) => $q->join('products', 'stocks.product_id', '=', 'products.id')
                    ->select(DB::raw('SUM(stocks.quantity * products.purchase_price)'))
            ],
            'stock_value'
        )
            ->where('is_active', true)
            ->get(['id', 'name', 'stock_value']);

        // ── Statistik Keuangan Bulan Ini ──────────────────────
        $monthlyFinance = [
            'total_po'  => PurchaseOrder::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total_amount'),
            'total_so'  => SalesOrder::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total_amount'),
            'pending_budget' => BudgetRequest::where('status', 'pending')->sum('total_estimasi'),
        ];

        return view('superadmin.dashboard', compact(
            'stats',
            'lowStocks',
            'pendingPOs',
            'pendingBudgets',
            'activeTransfers',
            'pendingOpnames',
            'movementChart',
            'stockValueByWarehouse',
            'monthlyFinance'
        ));
    }
}

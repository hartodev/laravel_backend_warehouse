<?php

namespace App\Http\Controllers\Web\Stocker;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

/**
 * Dashboard Stocker (role: warehouse_keeper) — READ ONLY,
 * hanya data gudang milik user login (users.warehouse_id).
 */
class DashboardController extends Controller
{
    public function index()
    {
        $wid = auth()->user()->warehouse_id;
        abort_if(! $wid, 403, 'Akun Anda belum ditempatkan di gudang manapun. Hubungi administrator.');

        $warehouse = Warehouse::findOrFail($wid);

        $base = fn () => Stock::query()
            ->join('products', 'products.id', '=', 'stocks.product_id')
            ->where('stocks.warehouse_id', $wid)
            ->where('products.is_active', true);

        $stats = [
            'total_items' => $base()->count(),
            'total_qty'   => (int) $base()->sum('stocks.quantity'),
            'low_stock'   => $base()->whereColumn('stocks.quantity', '<=', 'products.min_stock')->count(),
            'total_value' => (float) $base()->sum(DB::raw('stocks.quantity * products.purchase_price')),
        ];

        $lowStocks = $base()
            ->whereColumn('stocks.quantity', '<=', 'products.min_stock')
            ->orderBy('stocks.quantity')
            ->orderBy('products.name')
            ->limit(10)
            ->get(['products.name as product_name', 'stocks.quantity', 'products.min_stock', 'products.unit']);

        return view('stocker.dashboard', compact('warehouse', 'stats', 'lowStocks'));
    }
}

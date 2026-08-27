<?php

namespace App\Http\Controllers\Web\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PurchaseOrder;
use Illuminate\View\View;

/**
 * Panel BARU untuk role 'supplier'. Semua data di-scope otomatis ke
 * auth()->user()->supplier_id — supplier tidak bisa lihat data supplier lain.
 *
 * File ini berdiri sendiri, tidak mengubah controller Admin/Superadmin yang
 * sudah ada untuk modul Product/PurchaseOrder.
 */
class DashboardController extends Controller
{
    public function index(): View
    {
        $supplierId = auth()->user()->supplier_id;

        $stats = [
            'total_products' => Product::where('supplier_id', $supplierId)->count(),
            'po_pending'     => PurchaseOrder::where('supplier_id', $supplierId)->where('status', 'pending')->count(),
            'po_approved'    => PurchaseOrder::where('supplier_id', $supplierId)->where('status', 'approved')->count(),
            'po_received'    => PurchaseOrder::where('supplier_id', $supplierId)->where('status', 'received')->count(),
        ];

        $recentPOs = PurchaseOrder::where('supplier_id', $supplierId)
            ->with('warehouse:id,name')
            ->latest()
            ->limit(5)
            ->get();

        return view('supplier.dashboard', compact('stats', 'recentPOs'));
    }
}
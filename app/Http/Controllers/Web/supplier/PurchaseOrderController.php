<?php

namespace App\Http\Controllers\Web\Supplier;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Read-only. Approve/reject/receive PO tetap wewenang Admin/Superadmin
 * (PurchaseOrderController yang sudah ada) — supplier cuma memantau status.
 */
class PurchaseOrderController extends Controller
{
    public function index(Request $request): View
    {
        $supplierId = auth()->user()->supplier_id;

        $pos = PurchaseOrder::where('supplier_id', $supplierId)
            ->with('warehouse:id,name')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('supplier.purchase_orders.index', compact('pos'));
    }

    public function show(PurchaseOrder $purchaseOrder): View
    {
        abort_unless((int) $purchaseOrder->supplier_id === (int) auth()->user()->supplier_id, 403);

        $purchaseOrder->load(['warehouse:id,name', 'items.product:id,name,sku,unit']);

        return view('supplier.purchase_orders.show', compact('purchaseOrder'));
    }
}
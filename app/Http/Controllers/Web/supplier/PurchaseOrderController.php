<?php

namespace App\Http\Controllers\Web\Supplier;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Services\SupplierPurchaseOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Supplier memantau PO, lalu mengonfirmasi dan menandai dikirim.
 * Approve/reject/receive PO tetap wewenang Admin/Superadmin.
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

    public function confirm(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        abort_unless((int) $purchaseOrder->supplier_id === (int) auth()->user()->supplier_id, 403);
        $request->validate(['note' => 'nullable|string|max:500']);

        [$ok, $msg] = SupplierPurchaseOrderService::confirm($purchaseOrder, auth()->user(), $request->note);

        return back()->with($ok ? 'success' : 'error', $msg);
    }

    public function ship(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        abort_unless((int) $purchaseOrder->supplier_id === (int) auth()->user()->supplier_id, 403);
        $request->validate([
            'note'          => 'nullable|string|max:500',
            'expected_date' => 'nullable|date|after_or_equal:today',
        ]);

        [$ok, $msg] = SupplierPurchaseOrderService::ship($purchaseOrder, auth()->user(), $request->note, $request->expected_date);

        return back()->with($ok ? 'success' : 'error', $msg);
    }
}

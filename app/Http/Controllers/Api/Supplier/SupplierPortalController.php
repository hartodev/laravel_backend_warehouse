<?php

namespace App\Http\Controllers\Api\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\SupplierPurchaseOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API portal supplier (role: supplier) untuk aplikasi mobile.
 * Data di-scope ke auth()->user()->supplier_id
 * (sama persis dengan Web\Supplier\*Controller).
 *
 * Simpan di: app/Http/Controllers/Api/Supplier/SupplierPortalController.php
 */
class SupplierPortalController extends Controller
{
    private function supplierId(): ?int
    {
        return auth()->user()->supplier_id;
    }

    // GET /api/supplier-portal/dashboard
    public function dashboard(): JsonResponse
    {
        $sid = $this->supplierId();

        $count = fn (string $status) => PurchaseOrder::where('supplier_id', $sid)
            ->where('status', $status)->count();

        $recent = PurchaseOrder::where('supplier_id', $sid)
            ->with('warehouse:id,name')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($po) => $this->poSummary($po));

        return response()->json([
            'success' => true,
            'data' => [
                'supplier_name' => optional(Supplier::find($sid))->name,
                'stats' => [
                    'total_products' => Product::where('supplier_id', $sid)->count(),
                    'po_pending'     => $count('pending'),
                    'po_approved'    => $count('approved'),
                    'po_received'    => $count('received'),
                ],
                'recent_pos' => $recent,
            ],
        ]);
    }

    // GET /api/supplier-portal/products?search=&page=
    public function products(Request $request): JsonResponse
    {
        $sid = $this->supplierId();

        $paginator = Product::where('supplier_id', $sid)
            ->with('category:id,name')
            ->when($request->search, fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%");
            }))
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $paginator->getCollection()->map(fn ($p) => $this->productRow($p))->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'total'        => $paginator->total(),
            ],
        ]);
    }

    // GET /api/supplier-portal/products/{product}
    public function productShow(Product $product): JsonResponse
    {
        abort_unless((int) $product->supplier_id === (int) $this->supplierId(), 403);
        $product->load('category:id,name');

        return response()->json(['success' => true, 'data' => $this->productRow($product)]);
    }

    // GET /api/supplier-portal/purchase-orders?status=&page=
    public function purchaseOrders(Request $request): JsonResponse
    {
        $sid = $this->supplierId();

        $paginator = PurchaseOrder::where('supplier_id', $sid)
            ->with('warehouse:id,name')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $paginator->getCollection()->map(fn ($po) => $this->poSummary($po))->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'total'        => $paginator->total(),
            ],
        ]);
    }

    // GET /api/supplier-portal/purchase-orders/{purchaseOrder}
    public function purchaseOrderShow(PurchaseOrder $purchaseOrder): JsonResponse
    {
        abort_unless((int) $purchaseOrder->supplier_id === (int) $this->supplierId(), 403);

        $purchaseOrder->load(['warehouse:id,name', 'items.product:id,name,sku,unit']);

        $data = $this->poSummary($purchaseOrder);
        $data['expected_date'] = optional($purchaseOrder->expected_date)->toDateString();
        $data['items'] = $purchaseOrder->items->map(fn ($i) => [
            'id'                => $i->id,
            'product_name'      => $i->product->name ?? '-',
            'product_sku'       => $i->product->sku ?? '-',
            'quantity_ordered'  => (int) $i->quantity_ordered,
            'quantity_received' => (int) $i->quantity_received,
            'unit_price'        => (float) $i->unit_price,
            'subtotal'          => (float) $i->subtotal,
        ])->values();

        return response()->json(['success' => true, 'data' => $data]);
    }

    // POST /api/supplier-portal/purchase-orders/{po}/confirm   body: note?
    public function confirmPurchaseOrder(Request $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        abort_unless((int) $purchaseOrder->supplier_id === (int) $this->supplierId(), 403);
        $request->validate(['note' => 'nullable|string|max:500']);

        [$ok, $msg] = SupplierPurchaseOrderService::confirm($purchaseOrder, auth()->user(), $request->note);

        return response()->json(['success' => $ok, 'message' => $msg, 'data' => $this->poSummary($purchaseOrder->fresh()->load('warehouse:id,name'))], $ok ? 200 : 422);
    }

    // POST /api/supplier-portal/purchase-orders/{po}/ship   body: note?, expected_date?
    public function shipPurchaseOrder(Request $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        abort_unless((int) $purchaseOrder->supplier_id === (int) $this->supplierId(), 403);
        $request->validate([
            'note'          => 'nullable|string|max:500',
            'expected_date' => 'nullable|date|after_or_equal:today',
        ]);

        [$ok, $msg] = SupplierPurchaseOrderService::ship($purchaseOrder, auth()->user(), $request->note, $request->expected_date);

        return response()->json(['success' => $ok, 'message' => $msg, 'data' => $this->poSummary($purchaseOrder->fresh()->load('warehouse:id,name'))], $ok ? 200 : 422);
    }

    private function poSummary(PurchaseOrder $po): array
    {
        return [
            'id'             => $po->id,
            'po_number'      => $po->po_number,
            'status'         => $po->status,
            'warehouse_name' => $po->warehouse->name ?? '-',
            'order_date'     => optional($po->order_date)->toDateString(),
            'total_amount'   => (float) $po->total_amount,
            'supplier_confirmed_at' => optional($po->supplier_confirmed_at)->toIso8601String(),
            'shipped_at'            => optional($po->shipped_at)->toIso8601String(),
            'supplier_note'         => $po->supplier_note,
        ];
    }

    private function productRow(Product $p): array
    {
        return [
            'id'             => $p->id,
            'name'           => $p->name,
            'sku'            => $p->sku,
            'category_name'  => $p->category->name ?? '-',
            'barcode'        => $p->barcode,
            'unit'           => $p->unit,
            'purchase_price' => (float) $p->purchase_price,
            'selling_price'  => (float) $p->selling_price,
            'is_active'      => (bool) $p->is_active,
        ];
    }
}

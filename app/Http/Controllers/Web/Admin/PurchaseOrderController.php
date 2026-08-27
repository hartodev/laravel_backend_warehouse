<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    // ── GET /admin/purchase-orders ────────────────────────────
    public function index(Request $request): View
    {
        $pos = PurchaseOrder::with(['supplier:id,name,code', 'warehouse:id,name,code', 'createdBy:id,name'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->supplier_id, fn($q) => $q->where('supplier_id', $request->supplier_id))
            ->when($request->search, fn($q) => $q->where('po_number', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.purchase-orders.index', compact('pos', 'suppliers'));
    }

    // ── GET /admin/purchase-orders/create ─────────────────────
    public function create(): View
    {
        $suppliers  = Supplier::where('is_active', true)->orderBy('name')->get(['id', 'name', 'code']);
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get(['id', 'name', 'code']);
        $products   = Product::where('is_active', true)->orderBy('name')->get(['id', 'name', 'sku', 'unit', 'purchase_price']);

        return view('admin.purchase-orders.create', compact('suppliers', 'warehouses', 'products'));
    }

    // ── POST /admin/purchase-orders ───────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'supplier_id'              => 'required|exists:suppliers,id',
            'warehouse_id'             => 'required|exists:warehouses,id',
            'order_date'               => 'required|date',
            'expected_date'            => 'nullable|date|after_or_equal:order_date',
            'payment_term'             => 'nullable|string|max:100',
            'tax_percent'              => 'nullable|numeric|min:0|max:100',
            'discount_amount'          => 'nullable|numeric|min:0',
            'notes'                    => 'nullable|string',
            'items'                    => 'required|array|min:1',
            'items.*.product_id'       => 'required|exists:products,id',
            'items.*.quantity_ordered' => 'required|integer|min:1',
            'items.*.unit_price'       => 'required|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::transaction(function () use ($request, &$po) {
            $count    = PurchaseOrder::whereYear('created_at', now()->year)->count() + 1;
            $poNumber = 'PO/' . now()->format('Y') . '/' . str_pad($count, 4, '0', STR_PAD_LEFT);

            $subtotal = 0;
            foreach ($request->items as $item) {
                $discount  = $item['discount_percent'] ?? 0;
                $subtotal += $item['quantity_ordered'] * $item['unit_price'] * (1 - $discount / 100);
            }

            $taxPercent     = $request->tax_percent ?? 0;
            $taxAmount      = $subtotal * ($taxPercent / 100);
            $discountAmount = $request->discount_amount ?? 0;
            $totalAmount    = $subtotal + $taxAmount - $discountAmount;

            $po = PurchaseOrder::create([
                'po_number'       => $poNumber,
                'supplier_id'     => $request->supplier_id,
                'warehouse_id'    => $request->warehouse_id,
                'created_by'      => auth()->id(),
                'status'          => 'pending',
                'order_date'      => $request->order_date,
                'expected_date'   => $request->expected_date,
                'subtotal'        => $subtotal,
                'tax_percent'     => $taxPercent,
                'tax_amount'      => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount'    => $totalAmount,
                'payment_term'    => $request->payment_term,
                'notes'           => $request->notes,
            ]);

            foreach ($request->items as $item) {
                $discount     = $item['discount_percent'] ?? 0;
                $itemSubtotal = $item['quantity_ordered'] * $item['unit_price'] * (1 - $discount / 100);

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id'        => $item['product_id'],
                    'quantity_ordered'  => $item['quantity_ordered'],
                    'quantity_received' => 0,
                    'unit_price'        => $item['unit_price'],
                    'discount_percent'  => $discount,
                    'subtotal'          => $itemSubtotal,
                ]);
            }
        });

        return redirect()->route('admin.purchase-orders.show', $po)
            ->with('success', 'Purchase Order berhasil dibuat, menunggu persetujuan Super Admin.');
    }

    // ── GET /admin/purchase-orders/{po} ───────────────────────
    public function show(PurchaseOrder $po): View
    {
        $po->load(['supplier', 'warehouse:id,name,code', 'createdBy:id,name', 'approvedBy:id,name', 'items.product:id,name,sku,unit']);

        return view('admin.purchase-orders.show', compact('po'));
    }

    // ── PUT /admin/purchase-orders/{po} ───────────────────────
    public function update(Request $request, PurchaseOrder $po): RedirectResponse
    {
        if (! in_array($po->status, ['draft', 'pending'])) {
            return back()->with('error', 'PO yang sudah diproses tidak dapat diubah.');
        }

        $po->update($request->only('expected_date', 'payment_term', 'notes', 'tax_percent', 'discount_amount'));

        return redirect()->route('admin.purchase-orders.show', $po)
            ->with('success', 'Purchase Order berhasil diupdate.');
    }

    // ── DELETE /admin/purchase-orders/{po} ────────────────────
    public function destroy(PurchaseOrder $po): RedirectResponse
    {
        if (! in_array($po->status, ['draft', 'pending'])) {
            return back()->with('error', 'Hanya PO draft/pending yang dapat dihapus.');
        }

        $po->items()->delete();
        $po->delete();

        return redirect()->route('admin.purchase-orders.index')
            ->with('success', 'Purchase Order berhasil dihapus.');
    }

    // ── POST /admin/purchase-orders/{po}/approve ──────────────
    public function approve(PurchaseOrder $po): RedirectResponse
    {
        if ($po->status !== 'pending') {
            return back()->with('error', 'Hanya PO pending yang dapat disetujui.');
        }

        $po->update(['status' => 'approved', 'approved_by' => auth()->id(), 'approved_at' => now()]);

        return redirect()->route('admin.purchase-orders.show', $po)
            ->with('success', 'Purchase Order disetujui.');
    }

    // ── POST /admin/purchase-orders/{po}/reject ───────────────
    public function reject(Request $request, PurchaseOrder $po): RedirectResponse
    {
        if ($po->status !== 'pending') {
            return back()->with('error', 'Hanya PO pending yang dapat ditolak.');
        }

        $validator = Validator::make($request->all(), [
            'reject_reason' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $po->update(['status' => 'cancelled', 'reject_reason' => $request->reject_reason]);

        return redirect()->route('admin.purchase-orders.show', $po)
            ->with('success', 'Purchase Order ditolak.');
    }

    // ── POST /admin/purchase-orders/{po}/receive ──────────────
    public function receive(Request $request, PurchaseOrder $po): RedirectResponse
    {
        if ($po->status !== 'approved' && $po->status !== 'partial') {
            return back()->with('error', 'Hanya PO yang sudah disetujui yang dapat diterima barangnya.');
        }

        $validator = Validator::make($request->all(), [
            'items'                            => 'required|array|min:1',
            'items.*.purchase_order_item_id'   => 'required|exists:purchase_order_items,id',
            'items.*.quantity_received'        => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::transaction(function () use ($request, $po) {
            foreach ($request->items as $item) {
                if (empty($item['quantity_received'])) continue;

                $poItem = PurchaseOrderItem::find($item['purchase_order_item_id']);
                if (! $poItem || $poItem->purchase_order_id !== $po->id) continue;

                $qtyReceived = min($item['quantity_received'], $poItem->remainingQty());
                if ($qtyReceived <= 0) continue;

                $poItem->increment('quantity_received', $qtyReceived);

                $stock = Stock::firstOrCreate(
                    ['warehouse_id' => $po->warehouse_id, 'product_id' => $poItem->product_id],
                    ['quantity' => 0]
                );

                $before = $stock->quantity;
                $stock->addStock($qtyReceived);

                StockMovement::create([
                    'product_id'      => $poItem->product_id,
                    'warehouse_id'    => $po->warehouse_id,
                    'type'            => 'in',
                    'quantity'        => $qtyReceived,
                    'quantity_before' => $before,
                    'quantity_after'  => $stock->quantity,
                    'reference_type'  => 'purchase_order',
                    'reference_id'    => $po->id,
                    'created_by'      => auth()->id(),
                    'note'            => "Penerimaan barang PO #{$po->po_number}",
                ]);
            }

            $allReceived = $po->items()->whereColumn('quantity_received', '<', 'quantity_ordered')->doesntExist();
            $po->update([
                'status'        => $allReceived ? 'received' : 'partial',
                'received_date' => $allReceived ? now() : null,
            ]);
        });

        return redirect()->route('admin.purchase-orders.show', $po)
            ->with('success', 'Penerimaan barang berhasil dicatat.');
    }
}

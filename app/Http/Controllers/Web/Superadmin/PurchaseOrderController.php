<?php

namespace App\Http\Controllers\Web\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $pos = PurchaseOrder::with(['supplier:id,name', 'warehouse:id,name', 'createdBy:id,name'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->supplier_id, fn($q) => $q->where('supplier_id', $request->supplier_id))
            ->when($request->warehouse_id, fn($q) => $q->where('warehouse_id', $request->warehouse_id))
            ->when($request->date_from, fn($q) => $q->whereDate('order_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('order_date', '<=', $request->date_to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $suppliers  = Supplier::where('is_active', true)->get(['id', 'name']);
        $warehouses = Warehouse::where('is_active', true)->get(['id', 'name']);

        return view('superadmin.purchase_order.index', compact('pos', 'suppliers', 'warehouses'));
    }

    public function create()
    {
        $suppliers  = Supplier::where('is_active', true)->get(['id', 'name']);
        $warehouses = Warehouse::where('is_active', true)->get(['id', 'name']);
        $products   = Product::where('is_active', true)->get(['id', 'name', 'sku', 'unit', 'purchase_price']);

        return view('superadmin.purchase_order.create', compact('suppliers', 'warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id'          => 'required|exists:suppliers,id',
            'warehouse_id'         => 'required|exists:warehouses,id',
            'order_date'           => 'required|date',
            'expected_date'        => 'nullable|date|after_or_equal:order_date',
            'payment_method'       => 'nullable|in:cash,transfer,credit',
            'discount_amount'      => 'nullable|numeric|min:0',
            'tax_percent'          => 'nullable|numeric|min:0|max:100',
            'notes'                => 'nullable|string',
            'items'                => 'required|array|min:1',
            'items.*.product_id'   => 'required|exists:products,id',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.price'        => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, &$po) {
            $count  = PurchaseOrder::whereYear('created_at', now()->year)->count() + 1;
            $number = 'PO/' . now()->format('Y') . '/' . str_pad($count, 4, '0', STR_PAD_LEFT);

            $subtotal       = collect($request->items)->sum(fn($i) => $i['quantity'] * $i['price']);
            $taxAmount      = $subtotal * (($request->tax_percent ?? 0) / 100);
            $discountAmount = $request->discount_amount ?? 0;
            $totalAmount    = $subtotal + $taxAmount - $discountAmount;

            $po = PurchaseOrder::create([
                'po_number'       => $number,
                'supplier_id'     => $request->supplier_id,
                'warehouse_id'    => $request->warehouse_id,
                'created_by'      => auth()->id(),
                'order_date'      => $request->order_date,
                'expected_date'   => $request->expected_date,
                'payment_method'  => $request->payment_method,
                'status'          => 'pending',
                'subtotal'        => $subtotal,
                'tax_percent'     => $request->tax_percent ?? 0,
                'tax_amount'      => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount'    => $totalAmount,
                'notes'           => $request->notes,
            ]);

            foreach ($request->items as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id'        => $item['product_id'],
                    'quantity_ordered'  => $item['quantity'],   // kolom DB
                    'unit_price'        => $item['price'],      // kolom DB, form kirim 'price'
                    'subtotal'          => $item['quantity'] * $item['price'],
                ]);
            }
        });

        return redirect()->route('superadmin.purchase-orders.show', $po)
            ->with('success', 'Purchase Order berhasil dibuat.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load([
            'supplier',
            'warehouse:id,name',
            'createdBy:id,name',
            'approvedBy:id,name',
            'items.product:id,name,sku,unit',
        ]);

        return view('superadmin.purchase_order.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        if (!in_array($purchaseOrder->status, ['draft', 'pending'])) {
            return back()->with('error', 'PO yang sudah diproses tidak dapat diubah.');
        }

        $suppliers  = Supplier::where('is_active', true)->get(['id', 'name']);
        $warehouses = Warehouse::where('is_active', true)->get(['id', 'name']);
        $products   = Product::where('is_active', true)->get(['id', 'name', 'sku', 'unit', 'purchase_price']);

        return view('superadmin.purchase_order.edit', compact('purchaseOrder', 'suppliers', 'warehouses', 'products'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (!in_array($purchaseOrder->status, ['draft', 'pending'])) {
            return back()->with('error', 'PO yang sudah diproses tidak dapat diubah.');
        }

        $request->validate([
            'expected_date'   => 'nullable|date',
            'payment_method'  => 'nullable|in:cash,transfer,credit',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes'           => 'nullable|string',
        ]);

        $purchaseOrder->update($request->only('expected_date', 'payment_method', 'discount_amount', 'notes'));

        return redirect()->route('superadmin.purchase-orders.show', $purchaseOrder)
            ->with('success', 'PO berhasil diupdate.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
            return back()->with('error', 'Hanya PO draft yang dapat dihapus.');
        }

        $purchaseOrder->items()->delete();
        $purchaseOrder->delete();

        return redirect()->route('superadmin.purchase-orders.index')
            ->with('success', 'Purchase Order berhasil dihapus.');
    }

    public function approve(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'pending') {
            return back()->with('error', 'Hanya PO pending yang dapat disetujui.');
        }

        $purchaseOrder->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        NotificationService::purchaseOrder($purchaseOrder->fresh(), 'approved', auth()->id());

        return back()->with('success', 'PO disetujui.');
    }

    public function reject(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'pending') {
            return back()->with('error', 'Hanya PO pending yang dapat ditolak.');
        }

        $request->validate(['reject_reason' => 'required|string']);

        $purchaseOrder->update([
            'status'        => 'cancelled',
            'reject_reason' => $request->reject_reason,
        ]);

        NotificationService::purchaseOrder($purchaseOrder->fresh(), 'rejected', auth()->id());

        return back()->with('success', 'PO ditolak.');
    }

    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (! in_array($purchaseOrder->status, ['approved', 'partial'])) {
            return back()->with('error', 'Hanya PO approved/partial yang dapat diterima.');
        }

        // Tanpa input item = terima seluruh sisa; dengan input = penerimaan parsial
        $input = collect($request->input('items', []))->keyBy('purchase_order_item_id');

        DB::transaction(function () use ($purchaseOrder, $input) {
            foreach ($purchaseOrder->items as $item) {
                $remaining = max(0, $item->quantity_ordered - $item->quantity_received);
                $want      = $input->isEmpty() ? $remaining : (int) ($input[$item->id]['quantity_received'] ?? 0);
                $qty       = min($want, $remaining);
                if ($qty <= 0) continue;

                $item->increment('quantity_received', $qty);

                $stock = Stock::firstOrCreate(
                    ['warehouse_id' => $purchaseOrder->warehouse_id, 'product_id' => $item->product_id],
                    ['quantity' => 0]
                );
                $before = $stock->quantity;
                $stock->addStock($qty);

                StockMovement::create([
                    'product_id'      => $item->product_id,
                    'warehouse_id'    => $purchaseOrder->warehouse_id,
                    'type'            => 'in',
                    'quantity'        => $qty,
                    'quantity_before' => $before,
                    'quantity_after'  => $stock->quantity,
                    'reference_type'  => 'purchase_order',
                    'reference_id'    => $purchaseOrder->id,
                    'created_by'      => auth()->id(),
                    'note'            => "Penerimaan barang dari PO #{$purchaseOrder->po_number}",
                ]);
            }

            $allReceived = $purchaseOrder->items()->whereColumn('quantity_received', '<', 'quantity_ordered')->doesntExist();
            $purchaseOrder->update([
                'status'        => $allReceived ? 'received' : 'partial',
                'received_date' => $allReceived ? now() : null,
            ]);
        });

        $purchaseOrder->refresh();
        NotificationService::purchaseOrder($purchaseOrder, $purchaseOrder->status === 'received' ? 'received' : 'partial', auth()->id());

        return back()->with('success', 'Penerimaan barang dicatat dan stok diperbarui.');
    }
}

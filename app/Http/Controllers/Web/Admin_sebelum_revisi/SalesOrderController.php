<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class SalesOrderController extends Controller
{
    // ── GET /admin/sales-orders ─────────────────────────────────
    public function index(Request $request): View
    {
        $salesOrders = SalesOrder::with(['warehouse:id,name,code', 'createdBy:id,name'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->warehouse_id, fn($q) => $q->where('warehouse_id', $request->warehouse_id))
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('so_number', 'like', "%{$request->search}%")
                  ->orWhere('customer_name', 'like', "%{$request->search}%");
            }))
            ->when($request->date_from, fn($q) => $q->whereDate('order_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('order_date', '<=', $request->date_to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('Admin.sales_order.index', compact('salesOrders', 'warehouses'));
    }

    // ── GET /admin/sales-orders/create ──────────────────────────
    public function create(): View
    {
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $products = Product::where('is_active', true)->orderBy('name')->get(['id', 'name', 'sku', 'unit', 'selling_price']);

        return view('Admin.sales_order.create', compact('warehouses', 'products'));
    }

    // ── POST /admin/sales-orders ─────────────────────────────────
    // NB: field item form Blade (Admin/sales_order/create.blade.php) pakai
    // nama 'quantity'/'price' (bukan 'qty'/'harga' seperti di Api/Admin),
    // jadi validasi & mapping kolom di sini disesuaikan ke form yang sudah
    // ada, sementara business logic (nomor SO, hitung subtotal/pajak/diskon,
    // status awal draft) tetap ikut Api/Admin.
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'warehouse_id'          => 'required|exists:warehouses,id',
            'customer_name'         => 'required|string|max:255',
            'customer_address'      => 'nullable|string',
            'payment_method'        => 'required|in:cash,transfer,credit',
            'order_date'            => 'required|date',
            'due_date'              => 'nullable|date|after_or_equal:order_date',
            'notes'                 => 'nullable|string',
            'tax_percent'           => 'nullable|numeric|min:0|max:100',
            'discount_amount'       => 'nullable|numeric|min:0',
            'items'                 => 'required|array|min:1',
            'items.*.product_id'    => 'required|exists:products,id',
            'items.*.quantity'      => 'required|integer|min:1',
            'items.*.price'         => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $salesOrder = null;

        DB::transaction(function () use ($request, &$salesOrder) {
            $count    = SalesOrder::whereYear('created_at', now()->year)->count() + 1;
            $soNumber = 'SO/' . now()->format('Y') . '/' . str_pad($count, 4, '0', STR_PAD_LEFT);

            $subtotal       = collect($request->items)->sum(fn($i) => $i['quantity'] * $i['price']);
            $taxPercent     = $request->tax_percent ?? 0;
            $taxAmount      = $subtotal * ($taxPercent / 100);
            $discountAmount = $request->discount_amount ?? 0;
            $totalAmount    = $subtotal + $taxAmount - $discountAmount;

            $salesOrder = SalesOrder::create([
                'so_number'        => $soNumber,
                'warehouse_id'     => $request->warehouse_id,
                'created_by'       => auth()->id(),
                'customer_name'    => $request->customer_name,
                'customer_address' => $request->customer_address,
                'payment_method'   => $request->payment_method,
                'status'           => 'draft',
                'order_date'       => $request->order_date,
                'due_date'         => $request->due_date,
                'notes'            => $request->notes,
                'subtotal'         => $subtotal,
                'tax_percent'      => $taxPercent,
                'tax_amount'       => $taxAmount,
                'discount_amount'  => $discountAmount,
                'total_amount'     => $totalAmount,
            ]);

            foreach ($request->items as $item) {
                SalesOrderItem::create([
                    'sales_order_id'   => $salesOrder->id,
                    'product_id'       => $item['product_id'],
                    'quantity'         => $item['quantity'],
                    'unit_price'       => $item['price'],
                    'discount_percent' => 0,
                    'subtotal'         => $item['quantity'] * $item['price'],
                ]);
            }
        });

        return redirect()
            ->route('admin.sales-orders.show', $salesOrder)
            ->with('success', 'Sales Order berhasil dibuat.');
    }

    // ── GET /admin/sales-orders/{salesOrder} ────────────────────
    public function show(SalesOrder $salesOrder): View
    {
        $salesOrder->load([
            'warehouse:id,name,code',
            'createdBy:id,name',
            'approvedBy:id,name',
            'items.product:id,name,sku,unit',
            'payments',
        ]);

        return view('Admin.sales_order.show', compact('salesOrder'));
    }

    // ── GET /admin/sales-orders/{salesOrder}/edit ───────────────
    public function edit(SalesOrder $salesOrder): RedirectResponse|View
    {
        if (! in_array($salesOrder->status, ['draft', 'confirmed'])) {
            return back()->with('error', 'SO yang sudah diproses tidak dapat diubah.');
        }

        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $products = Product::where('is_active', true)->orderBy('name')->get(['id', 'name', 'sku', 'unit', 'selling_price']);

        return view('Admin.sales_order.edit', compact('salesOrder', 'warehouses', 'products'));
    }

    // ── PUT /admin/sales-orders/{salesOrder} ────────────────────
    // NB: hanya field header yang bisa diubah (items tidak diedit di sini),
    // konsisten dengan Api/Admin::update() yang juga tidak menyentuh items.
    public function update(Request $request, SalesOrder $salesOrder): RedirectResponse
    {
        if (! in_array($salesOrder->status, ['draft', 'confirmed'])) {
            return back()->with('error', 'SO yang sudah diproses tidak dapat diubah.');
        }

        $validator = Validator::make($request->all(), [
            'customer_name'    => 'required|string|max:255',
            'customer_address' => 'nullable|string',
            'payment_method'   => 'required|in:cash,transfer,credit',
            'order_date'       => 'required|date',
            'due_date'         => 'nullable|date|after_or_equal:order_date',
            'notes'            => 'nullable|string',
            'discount_amount'  => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $discountAmount = $request->discount_amount ?? 0;
        $totalAmount    = $salesOrder->subtotal + $salesOrder->tax_amount - $discountAmount;

        $salesOrder->update([
            'customer_name'    => $request->customer_name,
            'customer_address' => $request->customer_address,
            'payment_method'   => $request->payment_method,
            'order_date'       => $request->order_date,
            'due_date'         => $request->due_date,
            'notes'            => $request->notes,
            'discount_amount'  => $discountAmount,
            'total_amount'     => $totalAmount,
        ]);

        return redirect()->route('admin.sales-orders.show', $salesOrder)->with('success', 'Sales Order berhasil diupdate.');
    }

    // ── DELETE /admin/sales-orders/{salesOrder} ─────────────────
    public function destroy(SalesOrder $salesOrder): RedirectResponse
    {
        if ($salesOrder->status !== 'draft') {
            return back()->with('error', 'Hanya SO draft yang dapat dihapus.');
        }

        $salesOrder->items()->delete();
        $salesOrder->delete();

        return redirect()->route('admin.sales-orders.index')->with('success', 'Sales Order berhasil dihapus.');
    }
}
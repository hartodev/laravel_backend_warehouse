<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Warehouse;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesOrderController extends Controller
{
    public function index(Request $request)
    {
        $sos = SalesOrder::with(['warehouse:id,name', 'createdBy:id,name'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->warehouse_id, fn($q) => $q->where('warehouse_id', $request->warehouse_id))
            ->when(
                $request->search,
                fn($q) => $q->where(function ($q) use ($request) {
                    $q->where('customer_name', 'like', "%{$request->search}%")->orWhere('so_number', 'like', "%{$request->search}%");
                }),
            )
            ->when($request->date_from, fn($q) => $q->whereDate('order_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('order_date', '<=', $request->date_to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $warehouses = Warehouse::where('is_active', true)->get(['id', 'name']);

        return view('superadmin.sales_order.index', compact('sos', 'warehouses'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('is_active', true)->get(['id', 'name']);
        $products = Product::where('is_active', true)->get(['id', 'name', 'sku', 'unit', 'selling_price']);

        return view('superadmin.sales_order.create', compact('warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'customer_name' => 'required|string|max:255',
            'customer_address' => 'nullable|string',
            'payment_method' => 'nullable|in:cash,transfer,credit',
            'order_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:order_date',
            'keterangan' => 'nullable|string', // ← sesuai nama field di form
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_percent' => 'nullable|numeric|min:0|max:100',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1', // ← sesuai form
            'items.*.harga' => 'required|numeric|min:0', // ← sesuai form
            'items.*.deskripsi' => 'nullable|string', // ← sesuai form
        ]);

        DB::transaction(function () use ($request, &$so) {
            $count = SalesOrder::whereYear('created_at', now()->year)->count() + 1;
            $soNumber = 'SO/' . now()->format('Y') . '/' . str_pad($count, 4, '0', STR_PAD_LEFT);

            $subtotal = collect($request->items)->sum(fn($i) => $i['qty'] * $i['harga']);
            $taxAmount = $subtotal * (($request->tax_percent ?? 0) / 100);
            $discountAmount = $request->discount_amount ?? 0;
            $totalAmount = $subtotal + $taxAmount - $discountAmount;

            $so = SalesOrder::create([
                'so_number' => $soNumber,
                'warehouse_id' => $request->warehouse_id,
                'created_by' => auth()->id(),
                'customer_name' => $request->customer_name,
                'customer_address' => $request->customer_address,
                'payment_method' => $request->payment_method,
                'status' => 'draft',
                'order_date' => $request->order_date,
                'due_date' => $request->due_date,
                'notes' => $request->keterangan, // ← form: keterangan → DB: notes
                'subtotal' => $subtotal,
                'tax_percent' => $request->tax_percent ?? 0,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
            ]);
            foreach ($request->items as $item) {
                SalesOrderItem::create([
                    'sales_order_id' => $so->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'], // ← kolom DB: qty
                    'harga' => $item['harga'], // ← kolom DB: harga
                    'total' => $item['qty'] * $item['harga'], // ← kolom DB: total
                    'notes' => $item['deskripsi'] ?? null,
                ]);
            }
        });

        return redirect()->route('superadmin.sales-orders.show', $so)->with('success', 'Sales Order berhasil dibuat.');
    }

    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load(['warehouse:id,name', 'createdBy:id,name', 'items.product:id,name,sku,unit']);

        return view('superadmin.sales_order.show', compact('salesOrder'));
    }

    public function edit(SalesOrder $salesOrder)
    {
        if (!in_array($salesOrder->status, ['draft', 'confirmed'])) {
            return back()->with('error', 'SO yang sudah diproses tidak dapat diubah.');
        }

        $warehouses = Warehouse::where('is_active', true)->get(['id', 'name']);
        $products = Product::where('is_active', true)->get(['id', 'name', 'sku', 'unit', 'selling_price']);

        return view('superadmin.sales_order.edit', compact('salesOrder', 'warehouses', 'products'));
    }

    public function update(Request $request, SalesOrder $salesOrder)
    {
        if (!in_array($salesOrder->status, ['draft', 'confirmed'])) {
            return back()->with('error', 'SO yang sudah diproses tidak dapat diubah.');
        }

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_address' => 'nullable|string',
            'payment_method' => 'nullable|in:cash,transfer,credit',
            'order_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:order_date',
            'keterangan' => 'nullable|string',
            'discount_amount' => 'nullable|numeric|min:0',
        ]);

        $salesOrder->update([
            'customer_name' => $request->customer_name,
            'customer_address' => $request->customer_address,
            'payment_method' => $request->payment_method,
            'order_date' => $request->order_date,
            'due_date' => $request->due_date,
            'notes' => $request->keterangan, // ← form: keterangan → DB: notes
            'discount_amount' => $request->discount_amount ?? 0,
        ]);

        return redirect()->route('superadmin.sales-orders.show', $salesOrder)->with('success', 'Sales Order berhasil diupdate.');
    }

    public function destroy(SalesOrder $salesOrder)
    {
        if ($salesOrder->status !== 'draft') {
            return back()->with('error', 'Hanya SO draft yang dapat dihapus.');
        }

        $salesOrder->items()->delete();
        $salesOrder->delete();

        return redirect()->route('superadmin.sales-orders.index')->with('success', 'Sales Order berhasil dihapus.');
    }
}

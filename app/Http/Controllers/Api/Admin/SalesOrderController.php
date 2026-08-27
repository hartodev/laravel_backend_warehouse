<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SalesOrderController extends Controller
{
   public function index(Request $request): JsonResponse
    {
        $sos = SalesOrder::with(['warehouse:id,name,code', 'createdBy:id,name'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->warehouse_id, fn($q) => $q->where('warehouse_id', $request->warehouse_id))
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('so_number', 'like', "%{$request->search}%")
                  ->orWhere('customer_name', 'like', "%{$request->search}%");
            }))
            ->when($request->date_from, fn($q) => $q->whereDate('order_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('order_date', '<=', $request->date_to))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json(['success' => true, 'data' => $sos]);
    }

    public function show(SalesOrder $so): JsonResponse
    {
        $so->load(['warehouse:id,name,code', 'createdBy:id,name', 'approvedBy:id,name', 'items.product:id,name,sku,unit', 'payments']);

        return response()->json(['success' => true, 'data' => $so]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'warehouse_id'          => 'required|exists:warehouses,id',
            'customer_name'         => 'required|string|max:255',
            'customer_address'      => 'nullable|string',
            'payment_method'        => 'required|in:cash,transfer,credit',
            'order_date'            => 'required|date',
            'due_date'              => 'nullable|date|after_or_equal:order_date',
            'keterangan'            => 'nullable|string',
            'tax_percent'           => 'nullable|numeric|min:0|max:100',
            'discount_amount'       => 'nullable|numeric|min:0',
            'items'                 => 'required|array|min:1',
            'items.*.product_id'    => 'required|exists:products,id',
            'items.*.qty'           => 'required|integer|min:1',
            'items.*.harga'         => 'required|numeric|min:0',
            'items.*.deskripsi'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        DB::transaction(function () use ($request, &$so) {
            $count    = SalesOrder::whereYear('created_at', now()->year)->count() + 1;
            $soNumber = 'SO/' . now()->format('Y') . '/' . str_pad($count, 4, '0', STR_PAD_LEFT);

            $subtotal = collect($request->items)->sum(fn($i) => $i['qty'] * $i['harga']);
            $taxPercent     = $request->tax_percent ?? 0;
            $taxAmount      = $subtotal * ($taxPercent / 100);
            $discountAmount = $request->discount_amount ?? 0;
            $totalAmount    = $subtotal + $taxAmount - $discountAmount;

            $so = SalesOrder::create([
                'so_number'       => $soNumber,
                'warehouse_id'    => $request->warehouse_id,
                'created_by'      => auth()->id(),
                'customer_name'   => $request->customer_name,
                'customer_address'=> $request->customer_address,
                'payment_method'  => $request->payment_method,
                'status'          => 'draft',
                'order_date'      => $request->order_date,
                'due_date'        => $request->due_date,
                'keterangan'      => $request->keterangan,
                'subtotal'        => $subtotal,
                'tax_percent'     => $taxPercent,
                'tax_amount'      => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount'    => $totalAmount,
            ]);

            foreach ($request->items as $item) {
                SalesOrderItem::create([
                    'sales_order_id' => $so->id,
                    'product_id'     => $item['product_id'],
                    'deskripsi'      => $item['deskripsi'] ?? null,
                    'qty'            => $item['qty'],
                    'harga'          => $item['harga'],
                    'total'          => $item['qty'] * $item['harga'],
                ]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Sales Order berhasil dibuat.', 'data' => $so->load(['items.product:id,name,sku'])], 201);
    }

    public function update(Request $request, SalesOrder $so): JsonResponse
    {
        if (! in_array($so->status, ['draft', 'confirmed'])) {
            return response()->json(['success' => false, 'message' => 'SO yang sudah diproses tidak dapat diubah.'], 422);
        }

        $so->update($request->only('customer_name', 'customer_address', 'payment_method', 'order_date', 'due_date', 'keterangan', 'discount_amount'));

        return response()->json(['success' => true, 'message' => 'Sales Order berhasil diupdate.', 'data' => $so->fresh()]);
    }

    public function destroy(SalesOrder $so): JsonResponse
    {
        if ($so->status !== 'draft') {
            return response()->json(['success' => false, 'message' => 'Hanya SO draft yang dapat dihapus.'], 422);
        }

        $so->items()->delete();
        $so->delete();

        return response()->json(['success' => true, 'message' => 'Sales Order berhasil dihapus.']);
    }
}
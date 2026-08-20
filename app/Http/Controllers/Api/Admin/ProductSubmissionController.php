<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductSubmission;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductSubmissionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $submissions = ProductSubmission::with(['admin:id,name,email', 'category:id,name', 'initialWarehouse:id,name'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json(['success' => true, 'data' => $submissions]);
    }

    public function show(ProductSubmission $submission): JsonResponse
    {
        $submission->load(['admin:id,name,email', 'category:id,name', 'initialWarehouse:id,name', 'approvedBy:id,name', 'product:id,name,sku']);

        return response()->json(['success' => true, 'data' => $submission]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_id'          => 'required|exists:categories,id',
            'name'                 => 'required|string|max:255',
            'sku'                  => 'nullable|string|max:100|unique:products,sku',
            'barcode'              => 'nullable|string|max:100|unique:products,barcode',
            'unit'                 => 'required|string|max:50',
            'initial_stock'        => 'nullable|integer|min:0',
            'initial_warehouse_id' => 'nullable|exists:warehouses,id',
            'purchase_price'       => 'nullable|numeric|min:0',
            'selling_price'        => 'nullable|numeric|min:0',
            'description'          => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        $submission = ProductSubmission::create([
            'admin_id'             => auth()->id(),
            'category_id'          => $request->category_id,
            'name'                 => $request->name,
            'sku'                  => $request->sku ? strtoupper($request->sku) : null,
            'barcode'              => $request->barcode,
            'unit'                 => $request->unit,
            'initial_stock'        => $request->initial_stock ?? 0,
            'initial_warehouse_id' => $request->initial_warehouse_id,
            'purchase_price'       => $request->purchase_price ?? 0,
            'selling_price'        => $request->selling_price ?? 0,
            'description'          => $request->description,
            'status'               => 'pending',
        ]);

        return response()->json(['success' => true, 'message' => 'Pengajuan produk berhasil dikirim.', 'data' => $submission], 201);
    }

    public function update(Request $request, ProductSubmission $submission): JsonResponse
    {
        if (! $submission->isPending()) {
            return response()->json(['success' => false, 'message' => 'Pengajuan yang sudah diproses tidak dapat diubah.'], 422);
        }

        $submission->update($request->only('category_id', 'name', 'sku', 'barcode', 'unit', 'initial_stock', 'initial_warehouse_id', 'purchase_price', 'selling_price', 'description'));

        return response()->json(['success' => true, 'message' => 'Pengajuan berhasil diupdate.', 'data' => $submission->fresh()]);
    }

    public function destroy(ProductSubmission $submission): JsonResponse
    {
        if (! $submission->isPending()) {
            return response()->json(['success' => false, 'message' => 'Hanya pengajuan pending yang dapat dihapus.'], 422);
        }

        $submission->delete();

        return response()->json(['success' => true, 'message' => 'Pengajuan berhasil dihapus.']);
    }

    // POST /api/product-submissions/{submission}/approve
    public function approve(Request $request, ProductSubmission $submission): JsonResponse
    {
        if (! $submission->isPending()) {
            return response()->json(['success' => false, 'message' => 'Pengajuan ini sudah diproses sebelumnya.'], 422);
        }

        DB::transaction(function () use ($submission) {
            // Buat produk baru
            $product = Product::create([
                'category_id'    => $submission->category_id,
                'name'           => $submission->name,
                'sku'            => $submission->sku ?? strtoupper('SKU-' . time()),
                'barcode'        => $submission->barcode,
                'unit'           => $submission->unit,
                'purchase_price' => $submission->purchase_price,
                'selling_price'  => $submission->selling_price,
                'description'    => $submission->description,
                'is_active'      => true,
            ]);

            // Buat stok awal jika ada warehouse & initial stock
            if ($submission->initial_warehouse_id && $submission->initial_stock > 0) {
                $stock = Stock::create([
                    'warehouse_id' => $submission->initial_warehouse_id,
                    'product_id'   => $product->id,
                    'quantity'     => $submission->initial_stock,
                ]);

                StockMovement::create([
                    'product_id'      => $product->id,
                    'warehouse_id'    => $submission->initial_warehouse_id,
                    'type'            => 'in',
                    'quantity'        => $submission->initial_stock,
                    'quantity_before' => 0,
                    'quantity_after'  => $submission->initial_stock,
                    'created_by'      => auth()->id(),
                    'note'            => 'Stok awal dari pengajuan produk #' . $submission->id,
                ]);
            }

            $submission->update([
                'status'      => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'product_id'  => $product->id,
            ]);
        });

        return response()->json(['success' => true, 'message' => 'Pengajuan produk disetujui dan produk berhasil dibuat.', 'data' => $submission->fresh()->load('product:id,name,sku')]);
    }

    // POST /api/product-submissions/{submission}/reject
    public function reject(Request $request, ProductSubmission $submission): JsonResponse
    {
        if (! $submission->isPending()) {
            return response()->json(['success' => false, 'message' => 'Pengajuan ini sudah diproses sebelumnya.'], 422);
        }

        $validator = Validator::make($request->all(), [
            'reject_reason' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Alasan penolakan wajib diisi.', 'errors' => $validator->errors()], 422);
        }

        $submission->update([
            'status'        => 'rejected',
            'approved_by'   => auth()->id(),
            'approved_at'   => now(),
            'reject_reason' => $request->reject_reason,
        ]);

        return response()->json(['success' => true, 'message' => 'Pengajuan produk ditolak.', 'data' => $submission->fresh()]);
    }
}



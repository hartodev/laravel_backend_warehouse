<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductSubmission;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductSubmissionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $submissions = ProductSubmission::with(['admin:id,name,email', 'category:id,name', 'initialWarehouse:id,name'])
            ->when(auth()->user()->role === 'admin', fn($q) => $q->where('admin_id', auth()->id()))
            ->when(auth()->user()->role === 'super_admin', fn($q) => $q->where(function ($q) {
                $q->whereNotNull('product_id')->orWhere('status', '!=', 'pending');
            }))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json(['success' => true, 'data' => $submissions]);
    }

    public function show(ProductSubmission $submission): JsonResponse
    {
        if (auth()->user()->role === 'admin' && $submission->admin_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Tidak berhak mengakses pengajuan ini.'], 403);
        }
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
            'is_urgent'            => 'nullable|boolean',
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
            'is_urgent'            => $request->boolean('is_urgent'),
        ]);

        return response()->json(['success' => true, 'message' => 'Pengajuan produk berhasil dibuat. Teruskan ke Super Admin setelah direview.', 'data' => $submission], 201);
    }

    public function update(Request $request, ProductSubmission $submission): JsonResponse
    {
        if ($submission->admin_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Hanya pembuat pengajuan yang dapat mengubahnya.'], 403);
        }

        if (! $submission->isPending()) {
            return response()->json(['success' => false, 'message' => 'Pengajuan yang sudah diproses tidak dapat diubah.'], 422);
        }

        $submission->update($request->only('category_id', 'name', 'sku', 'barcode', 'unit', 'initial_stock', 'initial_warehouse_id', 'purchase_price', 'selling_price', 'description'));

        return response()->json(['success' => true, 'message' => 'Pengajuan berhasil diupdate.', 'data' => $submission->fresh()]);
    }

    public function destroy(ProductSubmission $submission): JsonResponse
    {
        if ($submission->admin_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Hanya pembuat pengajuan yang dapat mengubahnya.'], 403);
        }

        if (! $submission->isPending()) {
            return response()->json(['success' => false, 'message' => 'Hanya pengajuan pending yang dapat dihapus.'], 422);
        }

        $submission->delete();

        return response()->json(['success' => true, 'message' => 'Pengajuan berhasil dihapus.']);
    }

    // POST /api/product-submissions/{submission}/forward  (admin pemilik pengajuan)
    public function forward(ProductSubmission $submission): JsonResponse
    {
        if ($submission->admin_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Hanya pembuat pengajuan yang dapat meneruskannya.'], 403);
        }
        if ($submission->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Hanya pengajuan berstatus pending yang dapat diteruskan.'], 422);
        }

        $submission->update([
            'status'      => 'pending_sa',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        NotificationService::productSubmission($submission->fresh(), 'forwarded', auth()->id());

        return response()->json(['success' => true, 'message' => 'Pengajuan diteruskan ke Super Admin.', 'data' => $submission->fresh()]);
    }

    // POST /api/product-submissions/{submission}/approve  (super admin)
    public function approve(Request $request, ProductSubmission $submission): JsonResponse
    {
        $isNew    = is_null($submission->product_id);
        $expected = $isNew ? 'pending_sa' : 'pending';

        if ($submission->status !== $expected) {
            return response()->json(['success' => false, 'message' => $isNew
                ? 'Pengajuan produk baru ini belum diteruskan oleh Admin, atau sudah diproses.'
                : 'Hanya pengajuan pending yang dapat disetujui.'], 422);
        }

        DB::transaction(function () use ($request, $submission, $isNew) {
            if ($isNew) {
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

                if ($submission->initial_warehouse_id && $submission->initial_stock > 0) {
                    Stock::create([
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

                $submission->product_id = $product->id;
            } else {
                // Reaktivasi produk lama + terapkan perubahan (sama seperti web)
                if ($submission->change_data) {
                    $submission->product->update($submission->change_data);
                }
                $submission->product->update(['is_active' => true]);
            }

            $submission->update([
                'status'      => 'approved',
                'product_id'  => $submission->product_id,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'review_note' => $request->review_note,
            ]);
        });

        NotificationService::productSubmission($submission->fresh(), 'approved', auth()->id());

        return response()->json(['success' => true, 'message' => 'Pengajuan disetujui.', 'data' => $submission->fresh()->load('product:id,name,sku')]);
    }

    // POST /api/product-submissions/{submission}/reject  (super admin)
    public function reject(Request $request, ProductSubmission $submission): JsonResponse
    {
        $expected = is_null($submission->product_id) ? 'pending_sa' : 'pending';

        if ($submission->status !== $expected) {
            return response()->json(['success' => false, 'message' => 'Pengajuan ini tidak dalam status yang dapat ditolak.'], 422);
        }

        // Terima review_note (web) maupun reject_reason (mobile lama)
        $reason = $request->review_note ?? $request->reject_reason;
        if (! $reason || mb_strlen($reason) > 500) {
            return response()->json(['success' => false, 'message' => 'Alasan penolakan wajib diisi (maks. 500 karakter).'], 422);
        }

        $submission->update([
            'status'        => 'rejected',
            'approved_by'   => auth()->id(),
            'approved_at'   => now(),
            'reviewed_by'   => auth()->id(),
            'reviewed_at'   => now(),
            'review_note'   => $reason,
            'reject_reason' => $reason,
        ]);

        NotificationService::productSubmission($submission->fresh(), 'rejected', auth()->id());

        return response()->json(['success' => true, 'message' => 'Pengajuan produk ditolak.', 'data' => $submission->fresh()]);
    }
}

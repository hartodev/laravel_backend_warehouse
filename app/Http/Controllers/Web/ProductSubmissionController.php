<?php
// ============================================================
//  ProductSubmissionController.php
//  app/Http/Controllers/Web/SuperAdmin/ProductSubmissionController.php
// ============================================================

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductSubmission;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $submissions = ProductSubmission::with([
            'product:id,name,sku,barcode,category_id',
            'product.category:id,name',
            'submittedBy:id,name',
            'reviewedBy:id,name',
        ])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) => $q->whereHas(
                'product',
                fn($q) =>
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('sku', 'like', "%{$request->search}%")
            ))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('superadmin.product_submission.index', compact('submissions'));
    }

    public function show(ProductSubmission $productSubmission)
    {
        $productSubmission->load([
            'product.category',
            'product.supplier',
            'submittedBy:id,name',
            'reviewedBy:id,name',
        ]);

        return view('superadmin.product_submission.show', compact('productSubmission'));
    }

    public function approve(Request $request, ProductSubmission $productSubmission)
    {
        if ($productSubmission->status !== 'pending') {
            return back()->with('error', 'Hanya submission pending yang dapat disetujui.');
        }

        DB::transaction(function () use ($request, $productSubmission) {
            // Terapkan perubahan ke produk utama jika submission berisi update data
            if ($productSubmission->change_data) {
                $productSubmission->product->update($productSubmission->change_data);
            }

            // Aktifkan produk jika sebelumnya draft/inactive
            $productSubmission->product->update(['is_active' => true]);

            $productSubmission->update([
                'status'      => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'review_note' => $request->review_note,
            ]);
        });

        return back()->with('success', 'Submission produk disetujui dan produk diaktifkan.');
    }

    public function reject(Request $request, ProductSubmission $productSubmission)
    {
        if ($productSubmission->status !== 'pending') {
            return back()->with('error', 'Hanya submission pending yang dapat ditolak.');
        }

        $request->validate(['review_note' => 'required|string|max:500']);

        $productSubmission->update([
            'status'      => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_note' => $request->review_note,
        ]);

        return back()->with('success', 'Submission produk ditolak.');
    }
}

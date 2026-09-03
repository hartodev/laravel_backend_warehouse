<?php


// ============================================================
//  ProductSubmissionController.php
//  app/Http/Controllers/Web/Superadmin/ProductSubmissionController.php
//
//  Menangani DUA jenis pengajuan dalam satu tabel product_submissions:
//   A) Pengajuan PRODUK BARU dari Admin:
//        product_id NULL sejak awal, admin_id terisi.
//        Alur: pending (dibuat admin) -> pending_sa (di-forward admin)
//              -> approved (Super Admin approve, Product BARU dibuat)
//              -> rejected
//   B) Pengajuan REAKTIVASI produk lama:
//        product_id SUDAH terisi sejak awal.
//        Alur: pending -> approved (produk existing diaktifkan,
//              change_data diterapkan bila ada) / rejected
//
//  Sebelumnya approve() di sini SELALU mengasumsikan product_id ada,
//  sehingga meledak "Call to a member function update() on null"
//  saat memproses pengajuan produk baru (kasus A).
// ============================================================

namespace App\Http\Controllers\Web\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductSubmission;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $submissions = ProductSubmission::with([
            'product:id,name,sku,barcode,category_id',
            'product.category:id,name',
            'category:id,name',      // ★ untuk kasus produk baru (product masih null)
            'admin:id,name',         // ★ pengaju sebenarnya (kasus A)
            'submittedBy:id,name',
            'reviewedBy:id,name',
        ])
            // ★ DITAMBAHKAN — sembunyikan pengajuan produk baru yang masih
            // 'pending' (belum di-forward admin). Reaktivasi tetap tampil
            // di status 'pending' seperti biasa karena tidak lewat tahap forward.
            ->where(function ($q) {
                $q->whereNotNull('product_id')      // reaktivasi: selalu tampil
                    ->orWhere('status', '!=', 'pending'); // produk baru: hanya kalau sudah lewat pending
            })
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->whereHas('product', fn($q) => $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('sku', 'like', "%{$request->search}%"))
                    ->orWhere('name', 'like', "%{$request->search}%"); // fallback nama dari submission sendiri
            }))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->orderByDesc('is_urgent')
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
            'category:id,name',
            'initialWarehouse:id,name',
            'admin:id,name',
            'submittedBy:id,name',
            'reviewedBy:id,name',
        ]);

        return view('superadmin.product_submission.show', compact('productSubmission'));
    }

    public function approve(Request $request, ProductSubmission $productSubmission)
    {
        $isNewProduct = is_null($productSubmission->product_id);
        $expectedStatus = $isNewProduct ? 'pending_sa' : 'pending';

        if ($productSubmission->status !== $expectedStatus) {
            return back()->with('error', $isNewProduct
                ? 'Pengajuan produk baru ini belum diteruskan oleh Admin, atau sudah diproses sebelumnya.'
                : 'Hanya submission pending yang dapat disetujui.');
        }

        DB::transaction(function () use ($request, $productSubmission, $isNewProduct) {
            if ($isNewProduct) {
                // ── Kasus A: buat Product baru dari data submission ──
                $product = Product::create([
                    'category_id'    => $productSubmission->category_id,
                    'name'           => $productSubmission->name,
                    'sku'            => $productSubmission->sku ?? strtoupper('SKU-' . time()),
                    'barcode'        => $productSubmission->barcode,
                    'unit'           => $productSubmission->unit,
                    'purchase_price' => $productSubmission->purchase_price,
                    'selling_price'  => $productSubmission->selling_price,
                    'description'    => $productSubmission->description,
                    'is_active'      => true,
                ]);

                if ($productSubmission->initial_warehouse_id && $productSubmission->initial_stock > 0) {
                    Stock::create([
                        'warehouse_id' => $productSubmission->initial_warehouse_id,
                        'product_id'   => $product->id,
                        'quantity'     => $productSubmission->initial_stock,
                    ]);

                    StockMovement::create([
                        'product_id'      => $product->id,
                        'warehouse_id'    => $productSubmission->initial_warehouse_id,
                        'type'            => 'in',
                        'quantity'        => $productSubmission->initial_stock,
                        'quantity_before' => 0,
                        'quantity_after'  => $productSubmission->initial_stock,
                        'created_by'      => auth()->id(),
                        'note'            => 'Stok awal dari pengajuan produk #' . $productSubmission->id,
                    ]);
                }

                $productSubmission->product_id = $product->id;
            } else {
                // ── Kasus B: reaktivasi + terapkan perubahan produk lama ──
                if ($productSubmission->change_data) {
                    $productSubmission->product->update($productSubmission->change_data);
                }
                $productSubmission->product->update(['is_active' => true]);
            }

            $productSubmission->update([
                'status'      => 'approved',
                'product_id'  => $productSubmission->product_id,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'review_note' => $request->review_note,
            ]);
        });

        return back()->with('success', $isNewProduct
            ? 'Pengajuan produk baru disetujui, produk berhasil dibuat.'
            : 'Submission produk disetujui dan produk diaktifkan.');
    }

    public function reject(Request $request, ProductSubmission $productSubmission)
    {
        $isNewProduct = is_null($productSubmission->product_id);
        $expectedStatus = $isNewProduct ? 'pending_sa' : 'pending';

        if ($productSubmission->status !== $expectedStatus) {
            return back()->with('error', 'Pengajuan ini tidak dalam status yang dapat ditolak.');
        }

        $request->validate(['review_note' => 'required|string|max:500']);

        $productSubmission->update([
            'status'        => 'rejected',
            'approved_by'   => auth()->id(),
            'approved_at'   => now(),
            'reviewed_by'   => auth()->id(),
            'reviewed_at'   => now(),
            'review_note'   => $request->review_note,
            'reject_reason' => $request->review_note,
        ]);

        return back()->with('success', 'Submission produk ditolak.');
    }
}

// // ============================================================
// //  ProductSubmissionController.php
// //  app/Http/Controllers/Web/SuperAdmin/ProductSubmissionController.php
// // ============================================================

// namespace App\Http\Controllers\Web\Superadmin;

// use App\Http\Controllers\Controller;
// use App\Models\Product;
// use App\Models\ProductSubmission;
// use App\Services\ImageService;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;

// class ProductSubmissionController extends Controller
// {
//     public function index(Request $request)
//     {
//         $submissions = ProductSubmission::with([
//             'product:id,name,sku,barcode,category_id',
//             'product.category:id,name',
//             'submittedBy:id,name',
//             'reviewedBy:id,name',
//         ])
//             ->when($request->status, fn($q) => $q->where('status', $request->status))
//             ->when($request->search, fn($q) => $q->whereHas(
//                 'product',
//                 fn($q) =>
//                 $q->where('name', 'like', "%{$request->search}%")
//                     ->orWhere('sku', 'like', "%{$request->search}%")
//             ))
//             ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
//             ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
//             ->latest()
//             ->paginate(15)
//             ->withQueryString();

//         return view('superadmin.product_submission.index', compact('submissions'));
//     }

//     public function show(ProductSubmission $productSubmission)
//     {
//         $productSubmission->load([
//             'product.category',
//             'product.supplier',
//             'submittedBy:id,name',
//             'reviewedBy:id,name',
//         ]);

//         return view('superadmin.product_submission.show', compact('productSubmission'));
//     }

//     public function approve(Request $request, ProductSubmission $productSubmission)
//     {
//         if ($productSubmission->status !== 'pending') {
//             return back()->with('error', 'Hanya submission pending yang dapat disetujui.');
//         }

//         DB::transaction(function () use ($request, $productSubmission) {
//             // Terapkan perubahan ke produk utama jika submission berisi update data
//             if ($productSubmission->change_data) {
//                 $productSubmission->product->update($productSubmission->change_data);
//             }

//             // Aktifkan produk jika sebelumnya draft/inactive
//             $productSubmission->product->update(['is_active' => true]);

//             $productSubmission->update([
//                 'status'      => 'approved',
//                 'reviewed_by' => auth()->id(),
//                 'reviewed_at' => now(),
//                 'review_note' => $request->review_note,
//             ]);
//         });

//         return back()->with('success', 'Submission produk disetujui dan produk diaktifkan.');
//     }

//     public function reject(Request $request, ProductSubmission $productSubmission)
//     {
//         if ($productSubmission->status !== 'pending') {
//             return back()->with('error', 'Hanya submission pending yang dapat ditolak.');
//         }

//         $request->validate(['review_note' => 'required|string|max:500']);

//         $productSubmission->update([
//             'status'      => 'rejected',
//             'reviewed_by' => auth()->id(),
//             'reviewed_at' => now(),
//             'review_note' => $request->review_note,
//         ]);

//         return back()->with('success', 'Submission produk ditolak.');
//     }
// }
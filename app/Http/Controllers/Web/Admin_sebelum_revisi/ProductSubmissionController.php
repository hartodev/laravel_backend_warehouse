<?php

namespace App\Http\Controllers\Web\Admin;

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
        $submissions = ProductSubmission::with(['category:id,name', 'initialWarehouse:id,name'])
            ->where('admin_id', auth()->id())
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('Admin.product_submission.index', compact('submissions'));
    }

    public function show(ProductSubmission $submission)
    {
        abort_unless($submission->admin_id === auth()->id(), 403);

        $submission->load(['category:id,name', 'initialWarehouse:id,name', 'approvedBy:id,name', 'product:id,name,sku']);

        return view('Admin.product_submission.show', compact('submission'));
    }

    /**
     * NB: approve/reject di sini disediakan untuk kelengkapan route,
     * tapi secara bisnis biasanya approval final produk baru adalah
     * kewenangan Superadmin. Kalau memang admin TIDAK boleh approve
     * pengajuannya sendiri, hapus 2 method di bawah + route terkait
     * di grup admin.* pada web.php.
     */
    public function approve(Request $request, ProductSubmission $submission)
    {
        abort_unless($submission->admin_id === auth()->id(), 403);

        if (!$submission->isPending()) {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        DB::transaction(function () use ($submission) {
            $product = Product::create([
                'category_id' => $submission->category_id,
                'name' => $submission->name,
                'sku' => $submission->sku ?? strtoupper('SKU-' . time()),
                'barcode' => $submission->barcode,
                'unit' => $submission->unit,
                'purchase_price' => $submission->purchase_price,
                'selling_price' => $submission->selling_price,
                'description' => $submission->description,
                'is_active' => true,
            ]);

            if ($submission->initial_warehouse_id && $submission->initial_stock > 0) {
                Stock::create([
                    'warehouse_id' => $submission->initial_warehouse_id,
                    'product_id' => $product->id,
                    'quantity' => $submission->initial_stock,
                ]);

                StockMovement::create([
                    'product_id' => $product->id,
                    'warehouse_id' => $submission->initial_warehouse_id,
                    'type' => 'in',
                    'quantity' => $submission->initial_stock,
                    'quantity_before' => 0,
                    'quantity_after' => $submission->initial_stock,
                    'created_by' => auth()->id(),
                    'note' => 'Stok awal dari pengajuan produk #' . $submission->id,
                ]);
            }

            $submission->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'product_id' => $product->id,
            ]);
        });

        return redirect()->route('admin.product-submissions.index')->with('success', 'Pengajuan produk disetujui.');
    }

    public function reject(Request $request, ProductSubmission $submission)
    {
        abort_unless($submission->admin_id === auth()->id(), 403);

        $request->validate(['reject_reason' => 'required|string']);

        if (!$submission->isPending()) {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $submission->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'reject_reason' => $request->reject_reason,
        ]);

        return back()->with('success', 'Pengajuan produk ditolak.');
    }
}
// namespace App\Http\Controllers\Web\Admin;

// use App\Http\Controllers\Controller;
// use App\Models\Product;
// use App\Models\ProductSubmission;
// use App\Models\Stock;
// use App\Models\StockMovement;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;

// class ProductSubmissionController extends Controller
// {
//     public function index(Request $request)
//     {
//         $submissions = ProductSubmission::with(['category:id,name', 'initialWarehouse:id,name'])
//             ->where('admin_id', auth()->id())
//             ->when($request->status, fn($q) => $q->where('status', $request->status))
//             ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
//             ->latest()
//             ->paginate(15)
//             ->withQueryString();

//         return view('Admin.product-submissions.index', compact('submissions'));
//     }

//     public function show(ProductSubmission $submission)
//     {
//         abort_unless($submission->admin_id === auth()->id(), 403);

//         $submission->load(['category:id,name', 'initialWarehouse:id,name', 'approvedBy:id,name', 'product:id,name,sku']);

//         return view('Admin.product-submissions.show', compact('submission'));
//     }

//     /**
//      * NB: approve/reject di sini disediakan untuk kelengkapan route,
//      * tapi secara bisnis biasanya approval final produk baru adalah
//      * kewenangan Superadmin. Kalau memang admin TIDAK boleh approve
//      * pengajuannya sendiri, hapus 2 method di bawah + route terkait
//      * di grup admin.* pada web.php.
//      */
//     public function approve(Request $request, ProductSubmission $submission)
//     {
//         abort_unless($submission->admin_id === auth()->id(), 403);

//         if (!$submission->isPending()) {
//             return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
//         }

//         DB::transaction(function () use ($submission) {
//             $product = Product::create([
//                 'category_id' => $submission->category_id,
//                 'name' => $submission->name,
//                 'sku' => $submission->sku ?? strtoupper('SKU-' . time()),
//                 'barcode' => $submission->barcode,
//                 'unit' => $submission->unit,
//                 'purchase_price' => $submission->purchase_price,
//                 'selling_price' => $submission->selling_price,
//                 'description' => $submission->description,
//                 'is_active' => true,
//             ]);

//             if ($submission->initial_warehouse_id && $submission->initial_stock > 0) {
//                 Stock::create([
//                     'warehouse_id' => $submission->initial_warehouse_id,
//                     'product_id' => $product->id,
//                     'quantity' => $submission->initial_stock,
//                 ]);

//                 StockMovement::create([
//                     'product_id' => $product->id,
//                     'warehouse_id' => $submission->initial_warehouse_id,
//                     'type' => 'in',
//                     'quantity' => $submission->initial_stock,
//                     'quantity_before' => 0,
//                     'quantity_after' => $submission->initial_stock,
//                     'created_by' => auth()->id(),
//                     'note' => 'Stok awal dari pengajuan produk #' . $submission->id,
//                 ]);
//             }

//             $submission->update([
//                 'status' => 'approved',
//                 'approved_by' => auth()->id(),
//                 'approved_at' => now(),
//                 'product_id' => $product->id,
//             ]);
//         });

//         return redirect()->route('admin.product-submissions.index')->with('success', 'Pengajuan produk disetujui.');
//     }

//     public function reject(Request $request, ProductSubmission $submission)
//     {
//         abort_unless($submission->admin_id === auth()->id(), 403);

//         $request->validate(['reject_reason' => 'required|string']);

//         if (!$submission->isPending()) {
//             return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
//         }

//         $submission->update([
//             'status' => 'rejected',
//             'approved_by' => auth()->id(),
//             'approved_at' => now(),
//             'reject_reason' => $request->reject_reason,
//         ]);

//         return back()->with('success', 'Pengajuan produk ditolak.');
//     }
// }
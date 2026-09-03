<?php
namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ProductSubmission;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Alur pengajuan produk baru oleh Admin:
 *   pending    -> admin forward()  -> pending_sa
 *   pending_sa -> Super Admin approve/reject (lihat Superadmin\ProductSubmissionController)
 *
 * Approve/reject FINAL sengaja TIDAK ada di sini — itu wewenang Super Admin
 * lewat halaman /superadmin/product-submissions, bukan lewat /admin/.
 */
class ProductSubmissionController extends Controller
{
    // ── GET /admin/product-submissions ─────────────────────────
    public function index(Request $request): View
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

    // ── GET /admin/product-submissions/create ───────────────────
    public function create(): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('Admin.product_submission.create', compact('categories', 'warehouses'));
    }

    // ── GET /admin/product-submissions/{submission}/edit ────────
    public function edit(ProductSubmission $submission): View|RedirectResponse
    {
        abort_unless($submission->admin_id === auth()->id(), 403);

        if ($submission->status !== 'pending') {
            return back()->with('error', 'Pengajuan yang sudah diteruskan/diproses tidak dapat diubah.');
        }

        $categories = Category::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('Admin.product_submission.edit', compact('submission', 'categories', 'warehouses'));
    }

    // ── GET /admin/product-submissions/{submission} ──────────────
    public function show(ProductSubmission $submission): View
    {
        abort_unless($submission->admin_id === auth()->id(), 403);

        $submission->load(['category:id,name', 'initialWarehouse:id,name', 'approvedBy:id,name', 'product:id,name,sku']);

        return view('Admin.product_submission.show', compact('submission'));
    }

    // ── POST /admin/product-submissions ──────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
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

        $submission = ProductSubmission::create([
            'admin_id'             => auth()->id(),
            'category_id'          => $validated['category_id'],
            'name'                 => $validated['name'],
            'sku'                  => $validated['sku'] ? strtoupper($validated['sku']) : null,
            'barcode'              => $validated['barcode'] ?? null,
            'unit'                 => $validated['unit'],
            'initial_stock'        => $validated['initial_stock'] ?? 0,
            'initial_warehouse_id' => $validated['initial_warehouse_id'] ?? null,
            'purchase_price'       => $validated['purchase_price'] ?? 0,
            'selling_price'        => $validated['selling_price'] ?? 0,
            'description'          => $validated['description'] ?? null,
            'status'               => 'pending',
            'is_urgent'            => $request->boolean('is_urgent'),
        ]);

        return redirect()->route('admin.product-submissions.show', $submission)
            ->with('success', 'Pengajuan produk berhasil dibuat. Silakan review lalu teruskan ke Super Admin.');
    }

    // ── PUT /admin/product-submissions/{submission} ───────────────
    public function update(Request $request, ProductSubmission $submission): RedirectResponse
    {
        abort_unless($submission->admin_id === auth()->id(), 403);

        if ($submission->status !== 'pending') {
            return back()->with('error', 'Pengajuan yang sudah diteruskan/diproses tidak dapat diubah.');
        }

        $validated = $request->validate([
            'category_id'          => 'required|exists:categories,id',
            'name'                 => 'required|string|max:255',
            'sku'                  => 'nullable|string|max:100|unique:products,sku,' . $submission->id . ',id',
            'barcode'              => 'nullable|string|max:100|unique:products,barcode,' . $submission->id . ',id',
            'unit'                 => 'required|string|max:50',
            'initial_stock'        => 'nullable|integer|min:0',
            'initial_warehouse_id' => 'nullable|exists:warehouses,id',
            'purchase_price'       => 'nullable|numeric|min:0',
            'selling_price'        => 'nullable|numeric|min:0',
            'description'          => 'nullable|string',
            'is_urgent'            => 'nullable|boolean',
        ]);

        $submission->update([
            ...$validated,
            'sku'       => $validated['sku'] ? strtoupper($validated['sku']) : null,
            'is_urgent' => $request->boolean('is_urgent'),
        ]);

        return redirect()->route('admin.product-submissions.show', $submission)
            ->with('success', 'Pengajuan berhasil diupdate.');
    }

    // ── DELETE /admin/product-submissions/{submission} ─────────────
    public function destroy(ProductSubmission $submission): RedirectResponse
    {
        abort_unless($submission->admin_id === auth()->id(), 403);

        if ($submission->status !== 'pending') {
            return back()->with('error', 'Hanya pengajuan berstatus pending (belum diteruskan) yang dapat dihapus.');
        }

        $submission->delete();

        return redirect()->route('admin.product-submissions.index')->with('success', 'Pengajuan berhasil dihapus.');
    }

    // ── PATCH /admin/product-submissions/{submission}/forward ──────
    // Satu-satunya aksi admin di alur ini: menandai sudah direview
    // dan meneruskan ke Super Admin. TIDAK membuat Product — itu
    // baru terjadi saat Super Admin approve() di sisi Superadmin.
    public function forward(ProductSubmission $submission): RedirectResponse
    {
        abort_unless($submission->admin_id === auth()->id(), 403);

        if ($submission->status !== 'pending') {
            return back()->with('error', 'Hanya pengajuan berstatus pending yang dapat diteruskan.');
        }

        $submission->update([
            'status'      => 'pending_sa',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.product-submissions.show', $submission)
            ->with('success', 'Pengajuan telah direview dan diteruskan ke Super Admin.');
    }
}

// namespace App\Http\Controllers\Web\Admin;

// use App\Http\Controllers\Controller;
// use App\Models\Category;
// use App\Models\Product;
// use App\Models\ProductSubmission;
// use App\Models\Stock;
// use App\Models\StockMovement;
// use App\Models\Warehouse;
// use Illuminate\Http\RedirectResponse;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Illuminate\View\View;

// class ProductSubmissionController extends Controller
// {
//     // ── GET /admin/product-submissions ─────────────────────────
//     public function index(Request $request): View
//     {
//         $submissions = ProductSubmission::with(['category:id,name', 'initialWarehouse:id,name'])
//             ->where('admin_id', auth()->id())
//             ->when($request->status, fn($q) => $q->where('status', $request->status))
//             ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
//             ->latest()
//             ->paginate(15)
//             ->withQueryString();

//         return view('Admin.product_submission.index', compact('submissions'));
//     }

//     // ── GET /admin/product-submissions/create ──────────────────
//     // ★ BARU — shortcut agar admin bisa mengajukan produk baru sendiri,
//     // tanpa harus menunggu request dari user terlebih dahulu.
//     public function create(): View
//     {
//         $categories = Category::where('is_active', true)->orderBy('name')->get(['id', 'name']);
//         $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get(['id', 'name']);

//         return view('Admin.product_submission.create', compact('categories', 'warehouses'));
//     }

//     // ── GET /admin/product-submissions/{submission}/edit ───────
//     // ★ BARU — supaya pengajuan yang masih pending bisa direvisi
//     // lewat form, bukan cuma lewat update() mentah.
//     public function edit(ProductSubmission $submission): View|RedirectResponse
//     {
//         abort_unless($submission->admin_id === auth()->id(), 403);

//         if (! $submission->isPending()) {
//             return back()->with('error', 'Pengajuan yang sudah diproses tidak dapat diubah.');
//         }

//         $categories = Category::where('is_active', true)->orderBy('name')->get(['id', 'name']);
//         $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get(['id', 'name']);

//         return view('Admin.product_submission.edit', compact('submission', 'categories', 'warehouses'));
//     }

//     // ── GET /admin/product-submissions/{submission} ─────────────
//     public function show(ProductSubmission $submission): View
//     {
//         abort_unless($submission->admin_id === auth()->id(), 403);

//         $submission->load(['category:id,name', 'initialWarehouse:id,name', 'approvedBy:id,name', 'product:id,name,sku']);

//         return view('Admin.product_submission.show', compact('submission'));
//     }

//     // ── POST /admin/product-submissions ─────────────────────────
//     public function store(Request $request): RedirectResponse
//     {
//         $validated = $request->validate([
//             'category_id'          => 'required|exists:categories,id',
//             'name'                 => 'required|string|max:255',
//             'sku'                  => 'nullable|string|max:100|unique:products,sku',
//             'barcode'              => 'nullable|string|max:100|unique:products,barcode',
//             'unit'                 => 'required|string|max:50',
//             'initial_stock'        => 'nullable|integer|min:0',
//             'initial_warehouse_id' => 'nullable|exists:warehouses,id',
//             'purchase_price'       => 'nullable|numeric|min:0',
//             'selling_price'        => 'nullable|numeric|min:0',
//             'description'          => 'nullable|string',
//             // ★ BARU — penanda pengajuan mendesak, supaya Super Admin
//             // bisa memprioritaskan tanpa harus menunggu antrean biasa.
//             'is_urgent'            => 'nullable|boolean',
//         ]);

//         $submission = ProductSubmission::create([
//             'admin_id'             => auth()->id(),
//             'category_id'          => $validated['category_id'],
//             'name'                 => $validated['name'],
//             'sku'                  => $validated['sku'] ? strtoupper($validated['sku']) : null,
//             'barcode'              => $validated['barcode'] ?? null,
//             'unit'                 => $validated['unit'],
//             'initial_stock'        => $validated['initial_stock'] ?? 0,
//             'initial_warehouse_id' => $validated['initial_warehouse_id'] ?? null,
//             'purchase_price'       => $validated['purchase_price'] ?? 0,
//             'selling_price'        => $validated['selling_price'] ?? 0,
//             'description'          => $validated['description'] ?? null,
//             'status'               => 'pending',
//             'is_urgent'            => $request->boolean('is_urgent'),
//         ]);

//         return redirect()->route('admin.product-submissions.show', $submission)
//             ->with('success', 'Pengajuan produk berhasil dikirim.');
//     }

//     // ── PUT /admin/product-submissions/{submission} ─────────────
//     public function update(Request $request, ProductSubmission $submission): RedirectResponse
//     {
//         abort_unless($submission->admin_id === auth()->id(), 403);

//         if (! $submission->isPending()) {
//             return back()->with('error', 'Pengajuan yang sudah diproses tidak dapat diubah.');
//         }

//         $validated = $request->validate([
//             'category_id'          => 'required|exists:categories,id',
//             'name'                 => 'required|string|max:255',
//             'sku'                  => 'nullable|string|max:100|unique:products,sku,' . $submission->id . ',id',
//             'barcode'              => 'nullable|string|max:100|unique:products,barcode,' . $submission->id . ',id',
//             'unit'                 => 'required|string|max:50',
//             'initial_stock'        => 'nullable|integer|min:0',
//             'initial_warehouse_id' => 'nullable|exists:warehouses,id',
//             'purchase_price'       => 'nullable|numeric|min:0',
//             'selling_price'        => 'nullable|numeric|min:0',
//             'description'          => 'nullable|string',
//             'is_urgent'            => 'nullable|boolean',
//         ]);

//         $submission->update([
//             ...$validated,
//             'sku'       => $validated['sku'] ? strtoupper($validated['sku']) : null,
//             'is_urgent' => $request->boolean('is_urgent'),
//         ]);

//         return redirect()->route('admin.product-submissions.show', $submission)
//             ->with('success', 'Pengajuan berhasil diupdate.');
//     }

//     // ── DELETE /admin/product-submissions/{submission} ──────────
//     public function destroy(ProductSubmission $submission): RedirectResponse
//     {
//         abort_unless($submission->admin_id === auth()->id(), 403);

//         if (! $submission->isPending()) {
//             return back()->with('error', 'Hanya pengajuan pending yang dapat dihapus.');
//         }

//         $submission->delete();

//         return redirect()->route('admin.product-submissions.index')->with('success', 'Pengajuan berhasil dihapus.');
//     }

//     // ── PATCH /admin/product-submissions/{submission}/approve ───
//     // ★ DIPERBAIKI — sebelumnya admin bisa approve pengajuannya sendiri
//     // (abort_unless hanya cek admin_id === auth()->id()). Sekarang dikunci
//     // khusus super_admin, supaya alur "admin ajukan cepat, super admin
//     // yang setujui" benar-benar berlaku, bukan self-approve.
//     public function approve(Request $request, ProductSubmission $submission): RedirectResponse
//     {
//         abort_unless(auth()->user()->role === 'super_admin', 403,
//             'Hanya Super Admin yang dapat menyetujui pengajuan produk.');

//         if (! $submission->isPending()) {
//             return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
//         }

//         DB::transaction(function () use ($submission) {
//             $product = Product::create([
//                 'category_id'    => $submission->category_id,
//                 'name'           => $submission->name,
//                 'sku'            => $submission->sku ?? strtoupper('SKU-' . time()),
//                 'barcode'        => $submission->barcode,
//                 'unit'           => $submission->unit,
//                 'purchase_price' => $submission->purchase_price,
//                 'selling_price'  => $submission->selling_price,
//                 'description'    => $submission->description,
//                 'is_active'      => true,
//             ]);

//             if ($submission->initial_warehouse_id && $submission->initial_stock > 0) {
//                 Stock::create([
//                     'warehouse_id' => $submission->initial_warehouse_id,
//                     'product_id'   => $product->id,
//                     'quantity'     => $submission->initial_stock,
//                 ]);

//                 StockMovement::create([
//                     'product_id'      => $product->id,
//                     'warehouse_id'    => $submission->initial_warehouse_id,
//                     'type'            => 'in',
//                     'quantity'        => $submission->initial_stock,
//                     'quantity_before' => 0,
//                     'quantity_after'  => $submission->initial_stock,
//                     'created_by'      => auth()->id(),
//                     'note'            => 'Stok awal dari pengajuan produk #' . $submission->id,
//                 ]);
//             }

//             $submission->update([
//                 'status'      => 'approved',
//                 'approved_by' => auth()->id(),
//                 'approved_at' => now(),
//                 'product_id'  => $product->id,
//             ]);
//         });

//         return redirect()->route('admin.product-submissions.index')->with('success', 'Pengajuan produk disetujui.');
//     }

//     // ── PATCH /admin/product-submissions/{submission}/reject ────
//     // ★ DIPERBAIKI — sama seperti approve(), dikunci khusus super_admin.
//     public function reject(Request $request, ProductSubmission $submission): RedirectResponse
//     {
//         abort_unless(auth()->user()->role === 'super_admin', 403,
//             'Hanya Super Admin yang dapat menolak pengajuan produk.');

//         $request->validate(['reject_reason' => 'required|string']);

//         if (! $submission->isPending()) {
//             return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
//         }

//         $submission->update([
//             'status'        => 'rejected',
//             'approved_by'   => auth()->id(),
//             'approved_at'   => now(),
//             'reject_reason' => $request->reject_reason,
//         ]);

//         return back()->with('success', 'Pengajuan produk ditolak.');
//     }
// }
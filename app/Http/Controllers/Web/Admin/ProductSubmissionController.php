<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductSubmission;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductSubmissionController extends Controller
{
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

    public function show(ProductSubmission $submission): View
    {
        abort_unless($submission->admin_id === auth()->id(), 403);

        $submission->load(['category:id,name', 'initialWarehouse:id,name', 'approvedBy:id,name', 'product:id,name,sku']);

        return view('Admin.product_submission.show', compact('submission'));
    }

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
        ]);

        return redirect()->route('admin.product-submissions.show', $submission)
            ->with('success', 'Pengajuan produk berhasil dikirim.');
    }

    public function update(Request $request, ProductSubmission $submission): RedirectResponse
    {
        abort_unless($submission->admin_id === auth()->id(), 403);

        if (! $submission->isPending()) {
            return back()->with('error', 'Pengajuan yang sudah diproses tidak dapat diubah.');
        }

        $submission->update($request->only('category_id', 'name', 'sku', 'barcode', 'unit', 'initial_stock', 'initial_warehouse_id', 'purchase_price', 'selling_price', 'description'));

        return redirect()->route('admin.product-submissions.show', $submission)
            ->with('success', 'Pengajuan berhasil diupdate.');
    }

    public function destroy(ProductSubmission $submission): RedirectResponse
    {
        abort_unless($submission->admin_id === auth()->id(), 403);

        if (! $submission->isPending()) {
            return back()->with('error', 'Hanya pengajuan pending yang dapat dihapus.');
        }

        $submission->delete();

        return redirect()->route('admin.product-submissions.index')->with('success', 'Pengajuan berhasil dihapus.');
    }

    /**
     * NB: approve/reject di sini disediakan untuk kelengkapan route,
     * tapi secara bisnis biasanya approval final produk baru adalah
     * kewenangan Superadmin. Kalau memang admin TIDAK boleh approve
     * pengajuannya sendiri, hapus 2 method di bawah + route terkait
     * di grup admin.* pada web.php.
     */
    public function approve(Request $request, ProductSubmission $submission): RedirectResponse
    {
        abort_unless($submission->admin_id === auth()->id(), 403);

        if (! $submission->isPending()) {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        DB::transaction(function () use ($submission) {
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

            $submission->update([
                'status'      => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'product_id'  => $product->id,
            ]);
        });

        return redirect()->route('admin.product-submissions.index')->with('success', 'Pengajuan produk disetujui.');
    }

    public function reject(Request $request, ProductSubmission $submission): RedirectResponse
    {
        abort_unless($submission->admin_id === auth()->id(), 403);

        $request->validate(['reject_reason' => 'required|string']);

        if (! $submission->isPending()) {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $submission->update([
            'status'        => 'rejected',
            'approved_by'   => auth()->id(),
            'approved_at'   => now(),
            'reject_reason' => $request->reject_reason,
        ]);

        return back()->with('success', 'Pengajuan produk ditolak.');
    }
}

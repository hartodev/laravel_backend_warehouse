<?php

namespace App\Http\Controllers\Web\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(protected ImageService $imageService)
    {
    }

    /**
     * Tampilkan daftar produk.
     */
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'supplier']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $products = $query->latest()->paginate(15)->withQueryString();

        $categories = Category::orderBy('name')->get();
        $suppliers  = Supplier::orderBy('name')->get();

        return view('superadmin.product.index', compact('products', 'categories', 'suppliers'));
    }

    /**
     * Tampilkan form tambah produk.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $suppliers  = Supplier::orderBy('name')->get();

        return view('superadmin.product.create', compact('categories', 'suppliers'));
    }

    /**
     * Simpan produk baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:150',
            'sku'            => 'required|string|max:50|unique:products,sku',
            'category_id'    => 'required|exists:categories,id',
            'supplier_id'    => 'nullable|exists:suppliers,id',
            'description'    => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price'  => 'required|numeric|min:0',
            'unit'           => 'required|string|max:20',
            'min_stock'      => 'nullable|integer|min:0',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'      => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $validated['image'] = $this->imageService->store($request->file('image'), 'products');
        }

        Product::create($validated);

        return redirect()
            ->route('products.index')
            ->with('success', "Produk '{$validated['name']}' berhasil ditambahkan.");
    }

    /**
     * Tampilkan detail produk.
     */
    public function show(Product $product): View
    {
        $product->load(['category', 'supplier', 'units']);

        return view('superadmin.product.show', compact('product'));
    }

    /**
     * Tampilkan form edit produk.
     */
    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();
        $suppliers  = Supplier::orderBy('name')->get();

        return view('superadmin.product.edit', compact('product', 'categories', 'suppliers'));
    }

    /**
     * Update produk.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:150',
            'sku'            => 'required|string|max:50|unique:products,sku,' . $product->id,
            'category_id'    => 'required|exists:categories,id',
            'supplier_id'    => 'nullable|exists:suppliers,id',
            'description'    => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price'  => 'required|numeric|min:0',
            'unit'           => 'required|string|max:20',
            'min_stock'      => 'nullable|integer|min:0',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'      => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            if ($product->image) {
                $this->imageService->delete($product->image);
            }
            $validated['image'] = $this->imageService->store($request->file('image'), 'products');
        }

        $product->update($validated);

        return redirect()
            ->route('products.index')
            ->with('success', "Produk '{$product->name}' berhasil diupdate.");
    }

    /**
     * Hapus produk.
     */
    public function destroy(Product $product): RedirectResponse
    {
        if ($product->image) {
            $this->imageService->delete($product->image);
        }

        $name = $product->name;
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', "Produk '{$name}' berhasil dihapus.");
    }

    /**
     * Toggle status aktif/nonaktif produk.
     */
    public function toggleActive(Product $product): RedirectResponse
    {
        $product->update(['is_active' => ! $product->is_active]);

        $status = $product->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()
            ->route('products.index')
            ->with('success', "Produk '{$product->name}' berhasil {$status}.");
    }
}

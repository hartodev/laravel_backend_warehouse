<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    // ── GET /admin/products ──────────────────────────────────
    public function index(Request $request): View
    {
        $products = Product::with('category:id,name,code')
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%")
                  ->orWhere('barcode', 'like', "%{$request->search}%");
            }))
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->filled('is_active'), fn($q) => $q->where('is_active', $request->boolean('is_active')))
            ->when($request->low_stock, fn($q) => $q->whereHas('stocks', function ($q) {
                $q->whereColumn('quantity', '<=', 'products.min_stock');
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('Admin.products.index', compact('products', 'categories'));
    }

    // ── GET /admin/products/create ───────────────────────────
    public function create(): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('Admin.products.create', compact('categories'));
    }

    // ── POST /admin/products ─────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id'    => 'required|exists:categories,id',
            'name'           => 'required|string|max:255',
            'sku'            => 'required|string|max:100|unique:products,sku',
            'barcode'        => 'nullable|string|max:100|unique:products,barcode',
            'unit'           => 'required|string|max:50',
            'min_stock'      => 'nullable|integer|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'selling_price'  => 'nullable|numeric|min:0',
            'description'    => 'nullable|string',
            'is_active'      => 'nullable|boolean',
            'photo'          => ImageService::rules(),
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = ImageService::upload($request->file('photo'), 'products');
        }

        Product::create([
            'category_id'    => $validated['category_id'],
            'name'           => $validated['name'],
            'sku'            => strtoupper($validated['sku']),
            'barcode'        => $validated['barcode'] ?? null,
            'unit'           => $validated['unit'],
            'min_stock'      => $validated['min_stock'] ?? 0,
            'purchase_price' => $validated['purchase_price'] ?? 0,
            'selling_price'  => $validated['selling_price'] ?? 0,
            'description'    => $validated['description'] ?? null,
            'is_active'      => $request->boolean('is_active', true),
            'photo'          => $photoPath,
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dibuat.');
    }

    // ── GET /admin/products/{product} ────────────────────────
    // Detail produk: info umum + stok per gudang + daftar unit
    public function show(Product $product): View
    {
        $product->load(['category:id,name,code', 'units'])
                ->load(['stocks.warehouse:id,name,code']);

        return view('Admin.products.show', compact('product'));
    }

    // ── GET /admin/products/{product}/edit ───────────────────
    public function edit(Product $product): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('Admin.products.edit', compact('product', 'categories'));
    }

    // ── PUT /admin/products/{product} ────────────────────────
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id'    => 'required|exists:categories,id',
            'name'           => 'required|string|max:255',
            'sku'            => ['required', 'string', 'max:100', Rule::unique('products')->ignore($product->id)],
            'barcode'        => ['nullable', 'string', 'max:100', Rule::unique('products')->ignore($product->id)],
            'unit'           => 'required|string|max:50',
            'min_stock'      => 'nullable|integer|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'selling_price'  => 'nullable|numeric|min:0',
            'description'    => 'nullable|string',
            'is_active'      => 'nullable|boolean',
            'photo'          => ImageService::rules(),
        ]);

        $data = [
            'category_id'    => $validated['category_id'],
            'name'           => $validated['name'],
            'sku'            => strtoupper($validated['sku']),
            'barcode'        => $validated['barcode'] ?? null,
            'unit'           => $validated['unit'],
            'min_stock'      => $validated['min_stock'] ?? 0,
            'purchase_price' => $validated['purchase_price'] ?? 0,
            'selling_price'  => $validated['selling_price'] ?? 0,
            'description'    => $validated['description'] ?? null,
            'is_active'      => $request->boolean('is_active'),
        ];

        if ($request->hasFile('photo')) {
            $data['photo'] = ImageService::upload($request->file('photo'), 'products', $product->photo);
        }

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diupdate.');
    }

    // ── DELETE /admin/products/{product} ─────────────────────
    public function destroy(Product $product): RedirectResponse
    {
        if ($product->stocks()->where('quantity', '>', 0)->exists()) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Produk tidak dapat dihapus karena masih memiliki stok.');
        }

        if ($product->photo) {
            ImageService::delete($product->photo);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    // ── GET /admin/products/{product}/units ───────────────────
    public function units(Product $product): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $product->units]);
    }

    // ── GET /admin/products/{product}/stocks ──────────────────
    public function stockByWarehouse(Product $product): JsonResponse
    {
        $stocks = $product->stocks()->with('warehouse:id,name,code')->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'product'     => $product->only('id', 'name', 'sku', 'unit', 'min_stock'),
                'total_stock' => $stocks->sum('quantity'),
                'is_low'      => $product->isLowStock(),
                'stocks'      => $stocks,
            ],
        ]);
    }
}
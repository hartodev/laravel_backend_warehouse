<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
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

        $categories = Category::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('superadmin.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('superadmin.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = ImageService::upload($request->file('photo'), 'products');
        }

        Product::create([
            'category_id'    => $request->category_id,
            'name'           => $request->name,
            'sku'            => strtoupper($request->sku),
            'barcode'        => $request->barcode,
            'unit'           => $request->unit,
            'min_stock'      => $request->min_stock ?? 0,
            'purchase_price' => $request->purchase_price ?? 0,
            'selling_price'  => $request->selling_price ?? 0,
            'description'    => $request->description,
            'is_active'      => $request->boolean('is_active', true),
            'photo'          => $photoPath,
        ]);

        return redirect()->route('products.index')->with('success', 'Produk berhasil dibuat.');
    }

    public function show(Product $product): View
    {
        $product->load(['category:id,name,code', 'units', 'stocks.warehouse:id,name,code'])
                ->loadCount('stocks');

        return view('superadmin.products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('superadmin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only('category_id', 'name', 'barcode', 'unit', 'min_stock', 'purchase_price', 'selling_price', 'description');
        $data['sku']       = strtoupper($request->sku);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('photo')) {
            $data['photo'] = ImageService::upload($request->file('photo'), 'products', $product->photo);
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diupdate.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->stocks()->where('quantity', '>', 0)->exists()) {
            return redirect()->back()->with('error', 'Produk tidak dapat dihapus karena masih memiliki stok.');
        }

        if ($product->photo) {
            ImageService::delete($product->photo);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}

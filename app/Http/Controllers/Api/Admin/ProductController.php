<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
   public function index(Request $request): JsonResponse
    {
        $products = Product::with('category:id,name,code')
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%")
                  ->orWhere('barcode', 'like', "%{$request->search}%");
            }))
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when(isset($request->is_active), fn($q) => $q->where('is_active', $request->boolean('is_active')))
            ->when($request->low_stock, fn($q) => $q->whereHas('stocks', function ($q) {
                $q->whereColumn('quantity', '<=', 'products.min_stock');
            }))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json(['success' => true, 'data' => $products]);
    }

    public function show(Product $product): JsonResponse
    {
        $product->load(['category:id,name,code', 'units'])
                ->loadCount('stocks');

        return response()->json(['success' => true, 'data' => $product]);
    }

    public function store(Request $request): JsonResponse
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
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = ImageService::upload($request->file('photo'), 'products');
        }

        $product = Product::create([
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

        return response()->json(['success' => true, 'message' => 'Produk berhasil dibuat.', 'data' => $product->load('category:id,name')], 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_id'    => 'sometimes|required|exists:categories,id',
            'name'           => 'sometimes|required|string|max:255',
            'sku'            => ['sometimes', 'required', 'string', 'max:100', Rule::unique('products')->ignore($product->id)],
            'barcode'        => ['nullable', 'string', 'max:100', Rule::unique('products')->ignore($product->id)],
            'unit'           => 'sometimes|required|string|max:50',
            'min_stock'      => 'nullable|integer|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'selling_price'  => 'nullable|numeric|min:0',
            'description'    => 'nullable|string',
            'is_active'      => 'nullable|boolean',
            'photo'          => ImageService::rules(),
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        $data = $request->only('category_id', 'name', 'barcode', 'unit', 'min_stock', 'purchase_price', 'selling_price', 'description');

        if ($request->has('sku')) $data['sku'] = strtoupper($request->sku);
        if ($request->has('is_active')) $data['is_active'] = $request->boolean('is_active');
        if ($request->hasFile('photo')) {
            $data['photo'] = ImageService::upload($request->file('photo'), 'products', $product->photo);
        }

        $product->update($data);

        return response()->json(['success' => true, 'message' => 'Produk berhasil diupdate.', 'data' => $product->fresh()->load('category:id,name')]);
    }

    public function destroy(Product $product): JsonResponse
    {
        if ($product->stocks()->where('quantity', '>', 0)->exists()) {
            return response()->json(['success' => false, 'message' => 'Produk tidak dapat dihapus karena masih memiliki stok.'], 422);
        }

        if ($product->photo) ImageService::delete($product->photo);

        $product->delete();

        return response()->json(['success' => true, 'message' => 'Produk berhasil dihapus.']);
    }

    // GET /api/products/{product}/units
    public function units(Product $product): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $product->units]);
    }

    // GET /api/products/{product}/stocks
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
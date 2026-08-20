<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
   public function index(Request $request): JsonResponse
    {
        $categories = Category::withCount('products')
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%");
            }))
            ->when(isset($request->is_active), fn($q) => $q->where('is_active', $request->boolean('is_active')))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json(['success' => true, 'data' => $categories]);
    }

    public function show(Category $category): JsonResponse
    {
        $category->loadCount('products');

        return response()->json(['success' => true, 'data' => $category]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'code'        => 'nullable|string|max:50|unique:categories,code',
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        $category = Category::create([
            'name'        => $request->name,
            'code'        => $request->code ? strtoupper($request->code) : null,
            'description' => $request->description,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return response()->json(['success' => true, 'message' => 'Kategori berhasil dibuat.', 'data' => $category], 201);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'sometimes|required|string|max:255',
            'code'        => ['nullable', 'string', 'max:50', Rule::unique('categories')->ignore($category->id)],
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        $data = $request->only('name', 'description');
        if ($request->has('code')) $data['code'] = $request->code ? strtoupper($request->code) : null;
        if ($request->has('is_active')) $data['is_active'] = $request->boolean('is_active');

        $category->update($data);

        return response()->json(['success' => true, 'message' => 'Kategori berhasil diupdate.', 'data' => $category->fresh()]);
    }

    public function destroy(Category $category): JsonResponse
    {
        if ($category->products()->exists()) {
            return response()->json(['success' => false, 'message' => 'Kategori tidak dapat dihapus karena masih memiliki produk.'], 422);
        }

        $category->delete();

        return response()->json(['success' => true, 'message' => 'Kategori berhasil dihapus.']);
    }
}



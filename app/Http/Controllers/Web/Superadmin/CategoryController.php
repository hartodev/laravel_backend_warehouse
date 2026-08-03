<?php
namespace App\Http\Controllers\Web\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::withCount('products')
            // TAMBAHAN: eager load 'parent' untuk mencegah N+1 saat Blade akses $cat->parent
            ->with('parent')
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('slug', 'like', "%{$request->search}%");
            }))
            ->when($request->has('is_active') && $request->is_active !== '', fn($q) => $q->where('is_active', $request->boolean('is_active')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('superadmin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = Category::whereNull('parent_id')->where('is_active', true)->get(['id', 'name']);
        return view('superadmin.categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:100|unique:categories,name',
            'slug'      => 'nullable|string|max:120|unique:categories,slug',
            'parent_id' => 'nullable|exists:categories,id',
            'icon'      => 'nullable|string|max:50',
            'image'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        // TAMBAHAN: bungkus dalam transaction agar tidak ada orphan file
        // jika create() gagal setelah upload berhasil.
        DB::transaction(function () use ($request) {
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = ImageService::upload($request->file('image'), 'categories');
            }

            Category::create([
                'name'      => $request->name,
                'slug'      => $request->slug ?? \Str::slug($request->name),
                'parent_id' => $request->parent_id,
                'icon'      => $request->icon,
                'image'     => $imagePath,
                'is_active' => $request->boolean('is_active', true),
            ]);
        });

        return redirect()->route('superadmin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function show(Category $category)
    {
        $category->loadCount('products')
                 ->load(['products:id,name,sku,selling_price,is_active,category_id' => fn($q) => $q->limit(20), 'parent:id,name', 'children:id,name,parent_id']);

        return view('superadmin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $parents = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->where('id', '!=', $category->id)
            ->get(['id', 'name']);

        return view('superadmin.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name'      => ['required', 'string', 'max:100', Rule::unique('categories')->ignore($category->id)],
            'slug'      => ['nullable', 'string', 'max:120', Rule::unique('categories')->ignore($category->id)],
            // TAMBAHAN: cegah kategori dijadikan parent dari dirinya sendiri
            'parent_id' => ['nullable', 'exists:categories,id', Rule::notIn([$category->id])],
            'icon'      => 'nullable|string|max:50',
            'image'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->only('name', 'parent_id', 'icon');
        $data['slug']      = $request->slug ?? \Str::slug($request->name);
        $data['is_active'] = $request->boolean('is_active', $category->is_active);

        if ($request->hasFile('image')) {
            $data['image'] = ImageService::upload($request->file('image'), 'categories', $category->image);
        }

        $category->update($data);

        return redirect()->route('superadmin.categories.index')
            ->with('success', 'Kategori berhasil diupdate.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki produk.');
        }

        if ($category->children()->count() > 0) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki sub-kategori.');
        }

        if ($category->image) {
            ImageService::delete($category->image);
        }

        $category->delete();

        return redirect()->route('superadmin.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}

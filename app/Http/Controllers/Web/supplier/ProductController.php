<?php

namespace App\Http\Controllers\Web\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Read-only. Create/update/delete produk tetap wewenang Admin/Superadmin
 * (Web\Admin\ProductController & Web\Superadmin\ProductController yang
 * sudah ada) — supplier cuma memantau.
 */
class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $supplierId = auth()->user()->supplier_id;

        $products = Product::where('supplier_id', $supplierId)
            ->with('category:id,name')
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%");
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('Supplier.products.index', compact('products'));
    }

    public function show(Product $product): View
    {
        // Cegah supplier A intip produk milik supplier B lewat tebak-tebak ID di URL.
        abort_unless((int) $product->supplier_id === (int) auth()->user()->supplier_id, 403);

        $product->load('category:id,name');

        return view('Supplier.products.show', compact('product'));
    }
}

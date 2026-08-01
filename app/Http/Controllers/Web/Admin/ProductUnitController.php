<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductUnit;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ProductUnitController extends Controller
{
    // ── POST /admin/products/{product}/units ─────────────────
    public function store(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'unit_name'        => 'required|string|max:50',
            'conversion_value' => 'required|numeric|min:0.0001',
            'is_purchase_unit' => 'nullable|boolean',
            'is_sell_unit'     => 'nullable|boolean',
        ]);

        if ($product->units()->where('unit_name', $validated['unit_name'])->exists()) {
            return redirect()->route('admin.products.show', $product)
                ->with('error', "Unit '{$validated['unit_name']}' sudah ada untuk produk ini.");
        }

        $product->units()->create([
            'unit_name'        => $validated['unit_name'],
            'conversion_value' => $validated['conversion_value'],
            'is_purchase_unit' => $request->boolean('is_purchase_unit'),
            'is_sell_unit'     => $request->boolean('is_sell_unit'),
        ]);

        return redirect()->route('admin.products.show', $product)
            ->with('success', 'Unit produk berhasil ditambahkan.');
    }

    // ── PUT /admin/products/{product}/units/{unit} ───────────
    public function update(Request $request, Product $product, ProductUnit $unit): RedirectResponse
    {
        if ($unit->product_id !== $product->id) {
            abort(404);
        }

        $validated = $request->validate([
            'unit_name'        => 'required|string|max:50',
            'conversion_value' => 'required|numeric|min:0.0001',
            'is_purchase_unit' => 'nullable|boolean',
            'is_sell_unit'     => 'nullable|boolean',
        ]);

        $unit->update([
            'unit_name'        => $validated['unit_name'],
            'conversion_value' => $validated['conversion_value'],
            'is_purchase_unit' => $request->boolean('is_purchase_unit'),
            'is_sell_unit'     => $request->boolean('is_sell_unit'),
        ]);

        return redirect()->route('admin.products.show', $product)
            ->with('success', 'Unit produk berhasil diupdate.');
    }

    // ── DELETE /admin/products/{product}/units/{unit} ────────
    public function destroy(Product $product, ProductUnit $unit): RedirectResponse
    {
        if ($unit->product_id !== $product->id) {
            abort(404);
        }

        $unit->delete();

        return redirect()->route('admin.products.show', $product)
            ->with('success', 'Unit produk berhasil dihapus.');
    }
}
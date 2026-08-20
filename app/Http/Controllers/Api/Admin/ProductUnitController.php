<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductUnit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductUnitController extends Controller
{
     public function index(Product $product): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $product->units]);
    }

    public function store(Request $request, Product $product): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'unit_name'        => 'required|string|max:50',
            'conversion_value' => 'required|numeric|min:0.0001',
            'is_purchase_unit' => 'nullable|boolean',
            'is_sell_unit'     => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        // Cek duplikat
        $exists = $product->units()->where('unit_name', $request->unit_name)->exists();
        if ($exists) {
            return response()->json(['success' => false, 'message' => "Unit '{$request->unit_name}' sudah ada untuk produk ini."], 422);
        }

        $unit = $product->units()->create([
            'unit_name'        => $request->unit_name,
            'conversion_value' => $request->conversion_value,
            'is_purchase_unit' => $request->boolean('is_purchase_unit'),
            'is_sell_unit'     => $request->boolean('is_sell_unit'),
        ]);

        return response()->json(['success' => true, 'message' => 'Unit produk berhasil ditambahkan.', 'data' => $unit], 201);
    }

    public function update(Request $request, Product $product, ProductUnit $unit): JsonResponse
    {
        // Pastikan unit milik produk ini
        if ($unit->product_id !== $product->id) {
            return response()->json(['success' => false, 'message' => 'Unit tidak ditemukan untuk produk ini.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'unit_name'        => 'sometimes|required|string|max:50',
            'conversion_value' => 'sometimes|required|numeric|min:0.0001',
            'is_purchase_unit' => 'nullable|boolean',
            'is_sell_unit'     => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        $unit->update($request->only('unit_name', 'conversion_value', 'is_purchase_unit', 'is_sell_unit'));

        return response()->json(['success' => true, 'message' => 'Unit produk berhasil diupdate.', 'data' => $unit->fresh()]);
    }

    public function destroy(Product $product, ProductUnit $unit): JsonResponse
    {
        if ($unit->product_id !== $product->id) {
            return response()->json(['success' => false, 'message' => 'Unit tidak ditemukan untuk produk ini.'], 404);
        }

        $unit->delete();

        return response()->json(['success' => true, 'message' => 'Unit produk berhasil dihapus.']);
    }
}


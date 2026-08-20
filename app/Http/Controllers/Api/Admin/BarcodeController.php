<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\BarcodeLog;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BarcodeController extends Controller
{
      // POST /api/barcode/scan
    public function scan(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'barcode_value' => 'required|string|max:200',
            'scan_type'     => 'required|in:stock_in,stock_out,transfer,check,purchase',
            'warehouse_id'  => 'nullable|exists:warehouses,id',
            'device_info'   => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        // Cari produk berdasarkan barcode atau SKU
        $product = Product::where('barcode', $request->barcode_value)
                           ->orWhere('sku', $request->barcode_value)
                           ->with('stocks')
                           ->first();

        $isFound = $product !== null;

        // Catat log scan
        BarcodeLog::create([
            'user_id'       => auth()->id(),
            'warehouse_id'  => $request->warehouse_id,
            'product_id'    => $product?->id,
            'barcode_value' => $request->barcode_value,
            'scan_type'     => $request->scan_type,
            'is_found'      => $isFound,
            'device_info'   => $request->device_info,
        ]);

        if (! $isFound) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan untuk barcode: ' . $request->barcode_value,
            ], 404);
        }

        // Ambil stok di gudang tertentu jika ada
        $stockInfo = null;
        if ($request->warehouse_id) {
            $stock = $product->stocks->where('warehouse_id', $request->warehouse_id)->first();
            $stockInfo = [
                'warehouse_id' => $request->warehouse_id,
                'quantity'     => $stock?->quantity ?? 0,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Produk ditemukan.',
            'data'    => [
                'product' => [
                    'id'             => $product->id,
                    'name'           => $product->name,
                    'sku'            => $product->sku,
                    'barcode'        => $product->barcode,
                    'unit'           => $product->unit,
                    'min_stock'      => $product->min_stock,
                    'purchase_price' => $product->purchase_price,
                    'selling_price'  => $product->selling_price,
                ],
                'stock'   => $stockInfo,
            ],
        ]);
    }
}



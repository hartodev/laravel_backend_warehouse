<?php

namespace App\Http\Controllers\Web\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\BarcodeLog;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    public function index(Request $request)
    {
        $logs = BarcodeLog::with(['user:id,name', 'product:id,name,sku', 'warehouse:id,name'])
            ->when($request->scan_type, fn($q) => $q->where('scan_type', $request->scan_type))
            ->when($request->warehouse_id, fn($q) => $q->where('warehouse_id', $request->warehouse_id))
            ->when($request->is_found !== null && $request->is_found !== '', fn($q) => $q->where('is_found', $request->is_found))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $warehouses = Warehouse::where('is_active', true)->get(['id', 'name']);

        return view('superadmin.barcode.index', compact('logs', 'warehouses'));
    }

    public function scan()
    {
        $warehouses = Warehouse::where('is_active', true)->get(['id', 'name']);
        return view('superadmin.barcode.scan', compact('warehouses'));
    }

    public function doScan(Request $request)
    {
        $request->validate([
            'barcode_value' => 'required|string|max:200',
            'scan_type'     => 'required|in:stock_in,stock_out,transfer,check,purchase',
            'warehouse_id'  => 'nullable|exists:warehouses,id',
        ]);

        $product = Product::where('barcode', $request->barcode_value)
                          ->orWhere('sku', $request->barcode_value)
                          ->with('stocks')
                          ->first();

        $isFound = $product !== null;

        BarcodeLog::create([
            'user_id'       => auth()->id(),
            'warehouse_id'  => $request->warehouse_id,
            'product_id'    => $product?->id,
            'barcode_value' => $request->barcode_value,
            'scan_type'     => $request->scan_type,
            'is_found'      => $isFound,
            'device_info'   => 'Web SuperAdmin',
        ]);

        if (!$isFound) {
            return back()->with('error', 'Produk tidak ditemukan untuk barcode: ' . $request->barcode_value);
        }

        $stockInfo = null;
        if ($request->warehouse_id) {
            $stock = $product->stocks->where('warehouse_id', $request->warehouse_id)->first();
            $stockInfo = ['warehouse_id' => $request->warehouse_id, 'quantity' => $stock?->quantity ?? 0];
        }

        return back()->with(['scan_result' => compact('product', 'stockInfo'), 'success' => 'Produk ditemukan.']);
    }
}

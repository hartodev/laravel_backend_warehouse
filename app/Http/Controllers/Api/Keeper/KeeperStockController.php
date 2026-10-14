<?php

namespace App\Http\Controllers\Api\Keeper;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Endpoint khusus role warehouse_keeper (stocker) — READ ONLY (hanya GET stok).
 * Semua query dikunci ke gudang milik user login (users.warehouse_id),
 * jadi keeper tidak bisa melihat stok gudang lain walaupun mengirim
 * warehouse_id manual lewat query string.
 */
class KeeperStockController extends Controller
{
    private function noWarehouse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Akun Anda belum ditempatkan di gudang manapun. Hubungi administrator.',
        ], 403);
    }

    // Pencarian multi-kata: "pow elbow" cocok dengan "Pow fit elbow AW1"
    private function applyWords($query, ?string $search, array $columns)
    {
        $words = preg_split('/\s+/', trim((string) $search), -1, PREG_SPLIT_NO_EMPTY);
        foreach ($words as $word) {
            $query->where(function ($q) use ($word, $columns) {
                foreach ($columns as $col) {
                    $q->orWhere($col, 'like', '%' . $word . '%');
                }
            });
        }
        return $query;
    }

    // GET /api/keeper/stocks?search=&low_only=1&page=1&per_page=15
    public function stocks(Request $request): JsonResponse
    {
        $wid = $request->user()->warehouse_id;
        if (! $wid) {
            return $this->noWarehouse();
        }

        $stocks = Stock::with(['product:id,name,sku,unit,min_stock', 'warehouse:id,name,code'])
            ->where('warehouse_id', $wid)
            ->whereHas('product', fn ($p) => $p->where('is_active', true))
            ->when($request->filled('search'), fn ($q) => $q->whereHas('product',
                fn ($p) => $this->applyWords($p, $request->search, ['name', 'sku'])))
            ->when($request->boolean('low_only'), fn ($q) => $q->whereHas('product',
                fn ($p) => $p->whereColumn('stocks.quantity', '<=', 'products.min_stock')))
            ->latest('updated_at')
            ->paginate(min((int) ($request->per_page ?: 15), 100));

        return response()->json(['success' => true, 'data' => $stocks]);
    }
}

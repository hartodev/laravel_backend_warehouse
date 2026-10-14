<?php

namespace App\Http\Controllers\Web\Stocker;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Http\Request;

/**
 * Daftar stok untuk stocker — READ ONLY dan dikunci ke gudang miliknya.
 * Tidak ada parameter warehouse_id dari request, jadi tidak bisa diakali lewat URL.
 */
class StockController extends Controller
{
    private function wid(): int
    {
        $wid = auth()->user()->warehouse_id;
        abort_if(! $wid, 403, 'Akun Anda belum ditempatkan di gudang manapun. Hubungi administrator.');
        return (int) $wid;
    }

    private function baseQuery(Request $request, int $wid)
    {
        $q = Stock::query()
            ->join('products', 'products.id', '=', 'stocks.product_id')
            ->where('stocks.warehouse_id', $wid)
            ->where('products.is_active', true);

        // Pencarian multi-kata: "pow elbow" cocok dengan "Pow fit elbow AW1"
        $words = preg_split('/\s+/', trim((string) $request->search), -1, PREG_SPLIT_NO_EMPTY);
        foreach ($words as $word) {
            $q->where(function ($w) use ($word) {
                $w->where('products.name', 'like', "%{$word}%")
                  ->orWhere('products.sku', 'like', "%{$word}%");
            });
        }

        return $q->select([
            'stocks.id',
            'stocks.quantity',
            'products.name as product_name',
            'products.sku',
            'products.unit',
            'products.min_stock',
        ]);
    }

    public function index(Request $request)
    {
        $wid = $this->wid();
        $stocks = $this->baseQuery($request, $wid)->orderBy('products.name')->paginate(20)->withQueryString();

        return view('stocker.stocks.index', [
            'stocks'    => $stocks,
            'warehouse' => Warehouse::findOrFail($wid),
            'lowOnly'   => false,
        ]);
    }

    public function lowStock(Request $request)
    {
        $wid = $this->wid();
        $stocks = $this->baseQuery($request, $wid)
            ->whereColumn('stocks.quantity', '<=', 'products.min_stock')
            ->orderBy('stocks.quantity')
            ->orderBy('products.name')
            ->paginate(20)
            ->withQueryString();

        return view('stocker.stocks.index', [
            'stocks'    => $stocks,
            'warehouse' => Warehouse::findOrFail($wid),
            'lowOnly'   => true,
        ]);
    }
}

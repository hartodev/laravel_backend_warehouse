<?php
namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    // ── GET /admin/stock-movements ─────────────────────────────
    public function index(Request $request): View
    {
        $movements = StockMovement::with([
                'product:id,name,sku,unit',
                'warehouse:id,name,code',
                'createdBy:id,name',
            ])
            ->when($request->warehouse_id, function ($q) use ($request) {
                return $q->where('warehouse_id', $request->warehouse_id);
            })
            ->when($request->product_id, function ($q) use ($request) {
                return $q->where('product_id', $request->product_id);
            })
            ->when($request->type, function ($q) use ($request) {
                return $q->where('type', $request->type);
            })
            ->when($request->date_from, function ($q) use ($request) {
                return $q->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($q) use ($request) {
                return $q->whereDate('created_at', '<=', $request->date_to);
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $warehouses = Warehouse::orderBy('name')->get(['id', 'name', 'code']);
        $products = Product::orderBy('name')->get(['id', 'name', 'sku']);

        // Siapkan data produk untuk pencarian di JS.
        // Sengaja pakai foreach biasa (bukan ->map(fn...)) dan json_encode
        // di sisi PHP, supaya file Blade tidak perlu menulis satu pun
        // tanda panah (->) atau arrow function untuk data ini.
        $productSearchList = [];
        foreach ($products as $product) {
            $productSearchList[] = [
                'id'   => $product->id,
                'name' => $product->name,
                'sku'  => $product->sku,
            ];
        }
        $productsJson = json_encode($productSearchList);

        return view('Admin.stock-movements.index', compact(
            'movements',
            'warehouses',
            'products',
            'productsJson'
        ));
    }

    // ── GET /admin/stock-movements/{movement} ──────────────────
    public function show(int $id): View
    {
        $movement = StockMovement::query()
            ->where('id', $id)
            ->with(['product:id,name,sku,unit', 'warehouse:id,name,code', 'createdBy:id,name'])
            ->firstOrFail();

        return view('Admin.stock-movements.show', compact('movement'));
    }

    // ── POST /admin/stock-movements — input stok manual (adjustment) ──
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id'        => 'required|exists:products,id',
            'warehouse_id'      => 'required|exists:warehouses,id',
            'type'              => 'required|in:in,out,adjustment',
            'adjustment_type'   => 'required_if:type,adjustment|in:in,out',
            'quantity'          => 'required|integer|min:1',
            'note'              => 'nullable|string',
            'taken_by_name'     => 'nullable|string|max:255|required_if:type,out',
            'taken_by_division' => 'nullable|string|max:255|required_if:type,out',
        ]);

        // Wajibkan juga saat adjustment dengan arah "keluar"
        if ($validated['type'] === 'adjustment' && $validated['adjustment_type'] === 'out') {
            $request->validate([
                'taken_by_name'     => 'required|string|max:255',
                'taken_by_division' => 'required|string|max:255',
            ]);
        }

        $movement = null;

        DB::transaction(function () use ($request, $validated, &$movement) {
            $stock = Stock::firstOrCreate(
                ['warehouse_id' => $validated['warehouse_id'], 'product_id' => $validated['product_id']],
                ['quantity' => 0]
            );

            $before = $stock->quantity;
            $isOut = $validated['type'] === 'out'
                || ($validated['type'] === 'adjustment' && $validated['adjustment_type'] === 'out');

            if ($isOut) {
                if ($stock->quantity < $validated['quantity']) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'quantity' => 'Stok tidak mencukupi. Sisa stok saat ini: ' . $stock->quantity,
                    ]);
                }
                $stock->reduceStock($validated['quantity']);
            } else {
                $stock->addStock($validated['quantity']);
            }

            $movement = StockMovement::create([
                'product_id'        => $validated['product_id'],
                'warehouse_id'      => $validated['warehouse_id'],
                'type'              => $validated['type'],
                'quantity'          => $validated['quantity'],
                'quantity_before'   => $before,
                'quantity_after'    => $stock->quantity,
                'created_by'        => auth()->id(),
                'note'              => $validated['note'] ?? null,
                'taken_by_name'     => $validated['taken_by_name'] ?? null,
                'taken_by_division' => $validated['taken_by_division'] ?? null,
            ]);
        });

        return redirect()->route('admin.stock-movements.index')
            ->with('success', 'Pergerakan stok berhasil dicatat.');
    }
}
// namespace App\Http\Controllers\Web\Admin;

// use App\Http\Controllers\Controller;
// use App\Models\Product;
// use App\Models\Stock;
// use App\Models\StockMovement;
// use App\Models\Warehouse;
// use Illuminate\Http\RedirectResponse;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Illuminate\View\View;

// class StockMovementController extends Controller
// {
//     // ── GET /admin/stock-movements ─────────────────────────────
//     public function index(Request $request): View
//     {
//         $movements = StockMovement::with([
//                 'product:id,name,sku,unit',
//                 'warehouse:id,name,code',
//                 'createdBy:id,name',
//             ])
//             ->when($request->warehouse_id, fn($q) => $q->where('warehouse_id', $request->warehouse_id))
//             ->when($request->product_id, fn($q) => $q->where('product_id', $request->product_id))
//             ->when($request->type, fn($q, $type) => $q->where('type', $type))
//             ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
//             ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
//             ->latest()
//             ->paginate(25)
//             ->withQueryString();

//         $warehouses = Warehouse::orderBy('name')->get(['id', 'name', 'code']);
//         $products = Product::orderBy('name')->get(['id', 'name', 'sku']);

//         return view('Admin.stock-movements.index', compact('movements', 'warehouses', 'products'));
//     }

//     // // ── GET /admin/stock-movements/{movement} ──────────────────
//     // public function show(StockMovement $movement): View
//     // {
//     //     $movement->load(['product:id,name,sku,unit', 'warehouse:id,name,code', 'createdBy:id,name']);

//     //     return view('Admin.stock-movements.show', compact('movement'));
//     // }




//     // ── GET /admin/stock-movements/{movement} ──────────────────
// public function show(int $id): View
// {
//     $movement = StockMovement::query()
//         ->where('id', $id)
//         ->with(['product:id,name,sku,unit', 'warehouse:id,name,code', 'createdBy:id,name'])
//         ->firstOrFail();

//     return view('Admin.stock-movements.show', compact('movement'));
// }
//     // ── POST /admin/stock-movements — input stok manual (adjustment) ──
// public function store(Request $request): RedirectResponse
// {
//     $validated = $request->validate([
//         'product_id'      => 'required|exists:products,id',
//         'warehouse_id'    => 'required|exists:warehouses,id',
//         'type'            => 'required|in:in,out,adjustment',
//         'adjustment_type' => 'required_if:type,adjustment|in:in,out',
//         'quantity'        => 'required|integer|min:1',
//         'note'            => 'nullable|string',
//     ]);

//     $movement = null;

//     DB::transaction(function () use ($request, $validated, &$movement) {
//         $stock = Stock::firstOrCreate(
//             ['warehouse_id' => $validated['warehouse_id'], 'product_id' => $validated['product_id']],
//             ['quantity' => 0]
//         );

//         $before = $stock->quantity;
//         $isOut = $validated['type'] === 'out'
//             || ($validated['type'] === 'adjustment' && $validated['adjustment_type'] === 'out');

//         if ($isOut) {
//             if ($stock->quantity < $validated['quantity']) {
//                 throw \Illuminate\Validation\ValidationException::withMessages([
//                     'quantity' => 'Stok tidak mencukupi. Sisa stok saat ini: ' . $stock->quantity,
//                 ]);
//             }
//             $stock->reduceStock($validated['quantity']);
//         } else {
//             $stock->addStock($validated['quantity']);
//         }

//         $movement = StockMovement::create([
//             'product_id'      => $validated['product_id'],
//             'warehouse_id'    => $validated['warehouse_id'],
//             'type'            => $validated['type'],
//             'quantity'        => $validated['quantity'],
//             'quantity_before' => $before,
//             'quantity_after'  => $stock->quantity,
//             'created_by'      => auth()->id(),
//             'note'            => $validated['note'] ?? null,
//         ]);
//     });

//     return redirect()->route('admin.stock-movements.index')
//         ->with('success', 'Pergerakan stok berhasil dicatat.');
// }}
<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
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
            ->when($request->warehouse_id, fn($q) => $q->where('warehouse_id', $request->warehouse_id))
            ->when($request->product_id, fn($q) => $q->where('product_id', $request->product_id))
            ->when($request->type, fn($q, $type) => $q->where('type', $type))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $warehouses = Warehouse::orderBy('name')->get(['id', 'name', 'code']);

        return view('Admin.stock-movements.index', compact('movements', 'warehouses'));
    }

    // ── GET /admin/stock-movements/{movement} ──────────────────
    public function show(StockMovement $movement): View
    {
        $movement->load(['product:id,name,sku,unit', 'warehouse:id,name,code', 'createdBy:id,name']);

        return view('Admin.stock-movements.show', compact('movement'));
    }

    // ── POST /admin/stock-movements — input stok manual (adjustment) ──
public function store(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'product_id'      => 'required|exists:products,id',
        'warehouse_id'    => 'required|exists:warehouses,id',
        'type'            => 'required|in:in,out,adjustment',
        'adjustment_type' => 'required_if:type,adjustment|in:in,out',
        'quantity'        => 'required|integer|min:1',
        'note'            => 'nullable|string',
    ]);

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
            'product_id'      => $validated['product_id'],
            'warehouse_id'    => $validated['warehouse_id'],
            'type'            => $validated['type'],
            'quantity'        => $validated['quantity'],
            'quantity_before' => $before,
            'quantity_after'  => $stock->quantity,
            'created_by'      => auth()->id(),
            'note'            => $validated['note'] ?? null,
        ]);
    });

    return redirect()->route('admin.stock-movements.index')
        ->with('success', 'Pergerakan stok berhasil dicatat.');
}}
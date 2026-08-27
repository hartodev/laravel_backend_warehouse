<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class StockOpnameController extends Controller
{
    // ── GET /admin/stock-opnames ──────────────────────────────
    public function index(Request $request): View
    {
        $opnames = StockOpname::with(['warehouse:id,name,code', 'createdBy:id,name'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->warehouse_id, fn($q) => $q->where('warehouse_id', $request->warehouse_id))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $warehouses = Warehouse::orderBy('name')->get(['id', 'name', 'code']);

        return view('Admin.stock-opnames.index', compact('opnames', 'warehouses'));
    }

    // ── GET /admin/stock-opnames/create ───────────────────────
    public function create(): View
    {
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get(['id', 'name', 'code']);
        $categories = Category::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('Admin.stock-opnames.create', compact('warehouses', 'categories'));
    }

    // ── POST /admin/stock-opnames ──────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'warehouse_id'  => 'required|exists:warehouses,id',
            'opname_date'   => 'required|date',
            'scope'         => 'required|in:all,category,manual',
            'category_id'   => 'required_if:scope,category|nullable|exists:categories,id',
            'product_ids'   => 'required_if:scope,manual|nullable|array|min:1',
            'product_ids.*' => 'exists:products,id',
            'notes'         => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $opname = DB::transaction(function () use ($request) {
                $opname = StockOpname::create([
                    'opname_number' => $this->generateOpnameNumber(),
                    'warehouse_id'  => $request->warehouse_id,
                    'created_by'    => auth()->id(),
                    'status'        => 'draft',
                    'opname_date'   => $request->opname_date,
                    'scope'         => $request->scope,
                    'category_id'   => $request->scope === 'category' ? $request->category_id : null,
                    'notes'         => $request->notes,
                ]);

                $stockQuery = Stock::where('warehouse_id', $request->warehouse_id)->where('quantity', '>=', 0);

                if ($request->scope === 'category') {
                    $stockQuery->whereHas('product', fn($q) => $q->where('category_id', $request->category_id));
                } elseif ($request->scope === 'manual') {
                    $stockQuery->whereIn('product_id', $request->product_ids);
                }

                $stocks = $stockQuery->get();

                if ($stocks->isEmpty()) {
                    throw new \RuntimeException('Tidak ada produk ditemukan di gudang ini untuk scope yang dipilih.');
                }

                foreach ($stocks as $stock) {
                    StockOpnameItem::create([
                        'stock_opname_id' => $opname->id,
                        'product_id'      => $stock->product_id,
                        'system_stock'    => $stock->quantity,
                        'physical_stock'  => null,
                        // 'difference'      => null,
                    ]);
                }

                return $opname;
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('admin.stock-opnames.show', $opname)
            ->with('success', 'Opname berhasil dibuat. Silakan isi hasil hitung fisik.');
    }

    // ── GET /admin/stock-opnames/{opname} ──────────────────────
    public function show(StockOpname $opname): View
    {
        $opname->load('items.product:id,name,sku,unit', 'warehouse:id,name,code', 'createdBy:id,name');

        return view('Admin.stock-opnames.show', compact('opname'));
    }

    // ── POST /admin/stock-opnames/{opname}/start ───────────────
    public function start(StockOpname $opname): RedirectResponse
    {
        if ($opname->status !== 'draft') {
            return back()->with('error', 'Hanya opname berstatus draft yang dapat dimulai.');
        }

        $opname->update(['status' => 'in_progress', 'started_at' => now()]);

        return back()->with('success', 'Opname dimulai. Silakan isi jumlah stok fisik.');
    }

    // ── PATCH /admin/stock-opnames/{opname}/save-progress ──────
    public function saveProgress(Request $request, StockOpname $opname): RedirectResponse
    {
        if (! in_array($opname->status, ['draft', 'in_progress'])) {
            return back()->with('error', 'Opname sudah tidak bisa diedit.');
        }

        $validator = Validator::make($request->all(), [
            'items'                  => 'required|array|min:1',
            'items.*.product_id'     => 'required|integer',
            'items.*.physical_stock' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::transaction(function () use ($request, $opname) {
            foreach ($request->items as $itemData) {
                if (! isset($itemData['physical_stock']) || $itemData['physical_stock'] === '') continue;

                $item = StockOpnameItem::where('stock_opname_id', $opname->id)
                    ->where('product_id', $itemData['product_id'])
                    ->first();
                if (! $item) continue;

                $item->update([
                    'physical_stock' => $itemData['physical_stock'],
                    // 'difference'     => $itemData['physical_stock'] - $item->system_stock,
                ]);
            }

            if ($opname->status === 'draft') {
                $opname->update(['status' => 'in_progress', 'started_at' => now()]);
            }
        });

        return back()->with('success', 'Progress disimpan.');
    }

    // ── POST /admin/stock-opnames/{opname}/complete ────────────
    public function complete(Request $request, StockOpname $opname): RedirectResponse
    {
        if ($opname->status !== 'in_progress') {
            return back()->with('error', 'Hanya opname in_progress yang dapat diselesaikan.');
        }

        $validator = Validator::make($request->all(), [
            'items'                  => 'required|array|min:1',
            'items.*.product_id'     => 'required|integer',
            'items.*.physical_stock' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::transaction(function () use ($request, $opname) {
            foreach ($request->items as $itemData) {
                $item = StockOpnameItem::where('stock_opname_id', $opname->id)
                    ->where('product_id', $itemData['product_id'])
                    ->first();
                if (! $item) continue;

                $item->update([
                    'physical_stock' => $itemData['physical_stock'],
                    // 'difference'     => $itemData['physical_stock'] - $item->system_stock,
                ]);
            }

            $opname->update(['status' => 'pending_approval', 'completed_at' => now()]);
        });

        return redirect()->route('admin.stock-opnames.show', $opname)
            ->with('success', 'Opname selesai dihitung. Menunggu persetujuan Super Admin.');
    }

    // ── POST /admin/stock-opnames/{opname}/approve ─────────────
    // Adjust stok berdasarkan difference
    public function approve(StockOpname $opname): RedirectResponse
    {
        if ($opname->status !== 'pending_approval') {
            return back()->with('error', 'Hanya opname pending_approval yang dapat disetujui.');
        }

        DB::transaction(function () use ($opname) {
            $items = StockOpnameItem::where('stock_opname_id', $opname->id)
                ->whereNotNull('physical_stock')
                ->get();

            foreach ($items as $item) {
                if ($item->difference === 0) continue;

                $stock = Stock::firstOrCreate(
                    ['product_id' => $item->product_id, 'warehouse_id' => $opname->warehouse_id],
                    ['quantity' => 0]
                );

                $before = $stock->quantity;
                $after  = $item->physical_stock;

                $stock->update(['quantity' => $after]);

                StockMovement::create([
                    'product_id'      => $item->product_id,
                    'warehouse_id'    => $opname->warehouse_id,
                    'type'            => 'adjustment',
                    'quantity'        => abs($item->difference),
                    'quantity_before' => $before,
                    'quantity_after'  => $after,
                    'reference_type'  => 'stock_opname',
                    'reference_id'    => $opname->id,
                    'created_by'      => auth()->id(),
                    'note'            => "Penyesuaian opname #{$opname->opname_number}",
                ]);
            }

            $opname->update([
                'status'      => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
        });

        return redirect()->route('admin.stock-opnames.show', $opname)
            ->with('success', 'Opname disetujui. Stok sudah disesuaikan.');
    }

    // ── POST /admin/stock-opnames/{opname}/reject ──────────────
    public function reject(Request $request, StockOpname $opname): RedirectResponse
    {
        if ($opname->status !== 'pending_approval') {
            return back()->with('error', 'Hanya opname pending_approval yang dapat ditolak.');
        }

        $request->validate(['reject_reason' => 'required|string|max:500']);

        $opname->update([
            'status'        => 'in_progress',
            'reject_reason' => $request->reject_reason,
            'completed_at'  => null,
        ]);

        return redirect()->route('admin.stock-opnames.show', $opname)
            ->with('success', 'Opname dikembalikan untuk diperbaiki.');
    }

    // ── GET /admin/products-for-opname?warehouse_id={id}&search={q} ──
    // Endpoint bantu: untuk scope=manual, load produk yang ada di gudang
    public function productsForOpname(Request $request): JsonResponse
    {
        $request->validate(['warehouse_id' => 'required|exists:warehouses,id']);

        $query = Stock::with('product:id,name,sku,category_id,unit')
            ->where('warehouse_id', $request->warehouse_id)
            ->where('quantity', '>=', 0)
            ->whereHas('product');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('product', fn($q) =>
            $q->where('name', 'like', "%{$s}%")
                ->orWhere('sku', 'like', "%{$s}%"));
        }

        if ($request->filled('category_id')) {
            $query->whereHas('product', fn($q) =>
            $q->where('category_id', $request->category_id));
        }

        $stocks = $query->get()->map(fn($s) => [
            'product_id'    => $s->product_id,
            'product_name'  => $s->product->name ?? '-',
            'product_sku'   => $s->product->sku ?? '-',
            'product_unit'  => $s->product->unit ?? 'pcs',
            'category_id'   => $s->product->category_id,
            'current_stock' => $s->quantity,
        ]);

        return response()->json(['success' => true, 'data' => $stocks]);
    }

    // ── GET /admin/stock-opnames/products-for-scope (dipakai fetch() di halaman create) ──
    public function productsForScope(Request $request)
    {
        $request->validate(['warehouse_id' => 'required|exists:warehouses,id']);

        $products = Stock::with('product:id,name,sku,category_id,unit')
            ->where('warehouse_id', $request->warehouse_id)
            ->where('quantity', '>=', 0)
            ->whereHas('product')
            ->when($request->category_id, fn($q) => $q->whereHas('product', fn($q2) => $q2->where('category_id', $request->category_id)))
            ->get()
            ->map(fn($s) => [
                'id'    => $s->product_id,
                'name'  => $s->product->name ?? '-',
                'sku'   => $s->product->sku ?? '-',
                'stock' => $s->quantity,
            ]);

        return response()->json(['data' => $products]);
    }

    private function generateOpnameNumber(): string
    {
        $year  = now()->format('Y');
        $month = now()->format('m');
        $last  = StockOpname::whereYear('created_at', $year)->whereMonth('created_at', $month)->count() + 1;

        return sprintf('OP/%s/%s/%04d', $year, $month, $last);
    }
}
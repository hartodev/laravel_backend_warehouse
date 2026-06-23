<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StockOpnameController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────
    // GET /api/stock-opnames
    // ─────────────────────────────────────────────────────────────────────
    public function index(Request $request): JsonResponse
    {
        $query = StockOpname::with(['warehouse:id,name', 'createdBy:id,name', 'approvedBy:id,name'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $data = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // POST /api/stock-opnames
    // Body: warehouse_id, opname_date, scope (all|category|manual),
    //       category_id (jika scope=category),
    //       product_ids (array, jika scope=manual),
    //       notes (optional)
    // ─────────────────────────────────────────────────────────────────────
    public function store(Request $request): JsonResponse
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
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

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

            // Generate worksheet items berdasarkan scope
            $stockQuery = Stock::with('product:id,name,sku,category_id')
                ->where('warehouse_id', $request->warehouse_id)
                ->where('quantity', '>=', 0);

            if ($request->scope === 'category') {
                $stockQuery->whereHas('product', function ($q) use ($request) {
                    $q->where('category_id', $request->category_id);
                });
            } elseif ($request->scope === 'manual') {
                $stockQuery->whereIn('product_id', $request->product_ids);
            }

            $stocks = $stockQuery->get();

            if ($stocks->isEmpty()) {
                throw new \Exception('Tidak ada produk ditemukan di gudang ini untuk scope yang dipilih.');
            }

            foreach ($stocks as $stock) {
                StockOpnameItem::create([
                    'stock_opname_id' => $opname->id,
                    'product_id'      => $stock->product_id,
                    'system_stock'    => $stock->quantity,
                    'physical_stock'  => null,
                    'difference'      => null,
                ]);
            }

            return $opname;
        });

        return response()->json([
            'success' => true,
            'message' => 'Opname berhasil dibuat. Worksheet sudah siap diisi.',
            'data'    => $opname->load('items.product:id,name,sku', 'warehouse:id,name'),
        ], 201);
    }

    // ─────────────────────────────────────────────────────────────────────
    // GET /api/stock-opnames/{id}
    // ─────────────────────────────────────────────────────────────────────
    public function show(StockOpname $opname): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $opname->load(
                'items.product:id,name,sku,unit',
                'warehouse:id,name',
                'createdBy:id,name',
                'approvedBy:id,name',
                'category:id,name'
            ),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // POST /api/stock-opnames/{id}/start
    // ─────────────────────────────────────────────────────────────────────
    public function start(StockOpname $opname): JsonResponse
    {
        if ($opname->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya opname berstatus draft yang dapat dimulai.',
            ], 422);
        }

        $opname->update([
            'status'     => 'in_progress',
            'started_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Opname dimulai. Silakan isi jumlah stok fisik.',
            'data'    => $opname->fresh()->load('items.product:id,name,sku,unit', 'warehouse:id,name'),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // PATCH /api/stock-opnames/{id}/save-progress
    // Simpan progress tanpa finalize — bisa dipanggil berkali-kali
    // Body: items: [{product_id, physical_stock}]
    // ─────────────────────────────────────────────────────────────────────
    public function saveProgress(Request $request, StockOpname $opname): JsonResponse
    {
        if (!in_array($opname->status, ['draft', 'in_progress'])) {
            return response()->json([
                'success' => false,
                'message' => 'Opname sudah tidak bisa diedit.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'items'                  => 'required|array|min:1',
            'items.*.product_id'     => 'required|integer',
            'items.*.physical_stock' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        DB::transaction(function () use ($request, $opname) {
            foreach ($request->items as $itemData) {
                if ($itemData['physical_stock'] === null) continue;

                $item = StockOpnameItem::where('stock_opname_id', $opname->id)
                    ->where('product_id', $itemData['product_id'])
                    ->first();

                if (!$item) continue;

                $item->update([
                    'physical_stock' => $itemData['physical_stock'],
                    'difference'     => $itemData['physical_stock'] - $item->system_stock,
                ]);
            }

            // Set ke in_progress jika masih draft
            if ($opname->status === 'draft') {
                $opname->update(['status' => 'in_progress', 'started_at' => now()]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Progress disimpan.',
            'data'    => $opname->fresh()->load('items.product:id,name,sku,unit', 'warehouse:id,name'),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // POST /api/stock-opnames/{id}/complete
    // Finalize — semua item wajib sudah diisi physical_stock
    // Body: items: [{product_id, physical_stock}]
    // ─────────────────────────────────────────────────────────────────────
    public function complete(Request $request, StockOpname $opname): JsonResponse
    {
        if ($opname->status !== 'in_progress') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya opname in_progress yang dapat diselesaikan.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'items'                  => 'required|array|min:1',
            'items.*.product_id'     => 'required|integer',
            'items.*.physical_stock' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        DB::transaction(function () use ($request, $opname) {
            foreach ($request->items as $itemData) {
                $item = StockOpnameItem::where('stock_opname_id', $opname->id)
                    ->where('product_id', $itemData['product_id'])
                    ->first();

                if (!$item) continue;

                $item->update([
                    'physical_stock' => $itemData['physical_stock'],
                    'difference'     => $itemData['physical_stock'] - $item->system_stock,
                ]);
            }

            $opname->update([
                'status'       => 'pending_approval',
                'completed_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Opname selesai dihitung. Menunggu persetujuan.',
            'data'    => $opname->fresh()->load('items.product:id,name,sku,unit', 'warehouse:id,name'),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // POST /api/stock-opnames/{id}/approve
    // Adjust stok berdasarkan difference
    // ─────────────────────────────────────────────────────────────────────
    public function approve(StockOpname $opname): JsonResponse
    {
        if ($opname->status !== 'pending_approval') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya opname pending_approval yang dapat disetujui.',
            ], 422);
        }

        DB::transaction(function () use ($opname) {
            $items = StockOpnameItem::where('stock_opname_id', $opname->id)
                ->whereNotNull('physical_stock')
                ->get();

            foreach ($items as $item) {
                if ($item->difference === 0) continue;

                // Ambil atau buat record stock
                $stock = Stock::firstOrCreate(
                    ['product_id' => $item->product_id, 'warehouse_id' => $opname->warehouse_id],
                    ['quantity' => 0]
                );

                $before = $stock->quantity;
                $after  = $item->physical_stock;

                $stock->update(['quantity' => $after]);

                StockMovement::create([
                    'product_id'     => $item->product_id,
                    'warehouse_id'   => $opname->warehouse_id,
                    'type'           => 'adjustment',
                    'quantity'       => abs($item->difference),
                    'quantity_before' => $before,
                    'quantity_after' => $after,
                    'reference_type' => 'stock_opname',
                    'reference_id'   => $opname->id,
                    'created_by'     => auth()->id(),
                    'note'           => "Penyesuaian opname #{$opname->opname_number}",
                ]);
            }

            $opname->update([
                'status'      => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Opname disetujui. Stok sudah disesuaikan.',
            'data'    => $opname->fresh()->load('items.product:id,name,sku', 'warehouse:id,name'),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // POST /api/stock-opnames/{id}/reject
    // ─────────────────────────────────────────────────────────────────────
    public function reject(Request $request, StockOpname $opname): JsonResponse
    {
        if ($opname->status !== 'pending_approval') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya opname pending_approval yang dapat ditolak.',
            ], 422);
        }

        $request->validate(['reject_reason' => 'required|string|max:500']);

        $opname->update([
            'status'        => 'in_progress',
            'reject_reason' => $request->reject_reason,
            'completed_at'  => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Opname dikembalikan untuk diperbaiki.',
            'data'    => $opname->fresh()->load('items.product:id,name,sku', 'warehouse:id,name'),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // GET /api/products-for-opname?warehouse_id={id}&search={q}
    // Endpoint bantu: untuk scope=manual, load produk yang ada di gudang
    // ─────────────────────────────────────────────────────────────────────
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
            'product_id'     => $s->product_id,
            'product_name'   => $s->product->name ?? '-',
            'product_sku'    => $s->product->sku ?? '-',
            'product_unit'   => $s->product->unit ?? 'pcs',
            'category_id'    => $s->product->category_id,
            'current_stock'  => $s->quantity,
        ]);

        return response()->json(['success' => true, 'data' => $stocks]);
    }

    // ─────────────────────────────────────────────────────────────────────
    private function generateOpnameNumber(): string
    {
        $year  = now()->format('Y');
        $month = now()->format('m');
        $last  = StockOpname::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count() + 1;
        return sprintf('OP/%s/%s/%04d', $year, $month, $last);
    }
}


////////code lama before 2206
// namespace App\Http\Controllers\Api\Admin;

// use App\Http\Controllers\Controller;
// use App\Models\Stock;
// use App\Models\StockMovement;
// use App\Models\StockOpname;
// use App\Models\StockOpnameItem;
// use Illuminate\Http\JsonResponse;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Validator;

// class StockOpnameController extends Controller
// {
//     public function index(Request $request): JsonResponse
//     {
//         $opnames = StockOpname::with(['warehouse:id,name,code', 'createdBy:id,name'])
//             ->when($request->status, fn($q) => $q->where('status', $request->status))
//             ->when($request->warehouse_id, fn($q) => $q->where('warehouse_id', $request->warehouse_id))
//             ->latest()
//             ->paginate($request->per_page ?? 15);

//         return response()->json(['success' => true, 'data' => $opnames]);
//     }

//     public function show(StockOpname $opname): JsonResponse
//     {
//         $opname->load(['warehouse:id,name,code', 'createdBy:id,name', 'approvedBy:id,name', 'items.product:id,name,sku,unit']);

//         return response()->json(['success' => true, 'data' => $opname]);
//     }

//     public function store(Request $request): JsonResponse
//     {
//         $validator = Validator::make($request->all(), [
//             'warehouse_id' => 'required|exists:warehouses,id',
//             'opname_date'  => 'required|date',
//             'notes'        => 'nullable|string',
//         ]);

//         if ($validator->fails()) {
//             return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
//         }

//         $count  = StockOpname::whereYear('created_at', now()->year)->count() + 1;
//         $number = 'OPN/' . now()->format('Y') . '/' . str_pad($count, 4, '0', STR_PAD_LEFT);

//         $opname = StockOpname::create([
//             'opname_number' => $number,
//             'warehouse_id'  => $request->warehouse_id,
//             'created_by'    => auth()->id(),
//             'status'        => 'draft',
//             'opname_date'   => $request->opname_date,
//             'notes'         => $request->notes,
//         ]);

//         return response()->json(['success' => true, 'message' => 'Stock opname berhasil dibuat.', 'data' => $opname], 201);
//     }

//     public function update(Request $request, StockOpname $opname): JsonResponse
//     {
//         if (! in_array($opname->status, ['draft', 'in_progress'])) {
//             return response()->json(['success' => false, 'message' => 'Opname yang sudah selesai tidak dapat diubah.'], 422);
//         }

//         $opname->update($request->only('opname_date', 'notes'));

//         return response()->json(['success' => true, 'message' => 'Opname berhasil diupdate.', 'data' => $opname->fresh()]);
//     }

//     // POST start — mulai opname, snapshot stok sistem
//     public function start(StockOpname $opname): JsonResponse
//     {
//         if ($opname->status !== 'draft') {
//             return response()->json(['success' => false, 'message' => 'Hanya opname draft yang dapat dimulai.'], 422);
//         }

//         DB::transaction(function () use ($opname) {
//             $stocks = Stock::with('product:id,name,sku')
//                            ->where('warehouse_id', $opname->warehouse_id)
//                            ->get();

//             foreach ($stocks as $stock) {
//                 StockOpnameItem::create([
//                     'stock_opname_id' => $opname->id,
//                     'product_id'      => $stock->product_id,
//                     'system_stock'    => $stock->quantity,
//                     'physical_stock'  => $stock->quantity, // default sama, nanti diubah
//                     'difference'      => 0,
//                 ]);
//             }

//             $opname->update(['status' => 'in_progress', 'started_at' => now()]);
//         });

//         return response()->json(['success' => true, 'message' => 'Opname dimulai. Silakan update stok fisik.', 'data' => $opname->fresh()->load('items.product:id,name,sku')]);
//     }

//     // POST complete — simpan hasil hitung fisik
//     public function complete(Request $request, StockOpname $opname): JsonResponse
//     {
//         if ($opname->status !== 'in_progress') {
//             return response()->json(['success' => false, 'message' => 'Hanya opname in_progress yang dapat diselesaikan.'], 422);
//         }

//         $validator = Validator::make($request->all(), [
//             'items'                  => 'required|array|min:1',
//             'items.*.product_id'     => 'required|exists:products,id',
//             'items.*.physical_stock' => 'required|integer|min:0',
//         ]);

//         if ($validator->fails()) {
//             return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
//         }

//         DB::transaction(function () use ($request, $opname) {
//             foreach ($request->items as $item) {
//                 $opnameItem = StockOpnameItem::where('stock_opname_id', $opname->id)
//                     ->where('product_id', $item['product_id'])
//                     ->first();

//                 if (! $opnameItem) continue;

//                 $opnameItem->update([
//                     'physical_stock' => $item['physical_stock'],
//                     'difference'     => $item['physical_stock'] - $opnameItem->system_stock,
//                 ]);
//             }

//             $opname->update(['status' => 'pending_approval', 'completed_at' => now()]);
//         });

//         return response()->json([
//             'success' => true,
//             'message' => 'Hasil opname berhasil disimpan. Menunggu persetujuan.',
//             'data'    => $opname->fresh()->load('items.product:id,name,sku'),
//         ]);
//     }

//     // POST submit
//     public function submitApproval(StockOpname $opname): JsonResponse
//     {
//         if ($opname->status !== 'in_progress') {
//             return response()->json(['success' => false, 'message' => 'Status tidak sesuai.'], 422);
//         }

//         $opname->update(['status' => 'pending_approval']);

//         return response()->json(['success' => true, 'message' => 'Opname diajukan untuk persetujuan.']);
//     }

//     // POST approve — setujui dan adjust stok
//     public function approve(StockOpname $opname): JsonResponse
//     {
//         if ($opname->status !== 'pending_approval') {
//             return response()->json(['success' => false, 'message' => 'Hanya opname pending_approval yang dapat disetujui.'], 422);
//         }

//         DB::transaction(function () use ($opname) {
//             foreach ($opname->items as $item) {
//                 if ($item->difference === 0) continue;

//                 $stock = Stock::where('warehouse_id', $opname->warehouse_id)
//                                ->where('product_id', $item->product_id)
//                                ->first();

//                 if (! $stock) continue;

//                 $before = $stock->quantity;
//                 $stock->update(['quantity' => $item->physical_stock]);

//                 StockMovement::create([
//                     'product_id'      => $item->product_id,
//                     'warehouse_id'    => $opname->warehouse_id,
//                     'type'            => 'adjustment',
//                     'quantity'        => abs($item->difference),
//                     'quantity_before' => $before,
//                     'quantity_after'  => $item->physical_stock,
//                     'reference_type'  => 'stock_opname',   // ← ganti ini
//                     'reference_id'    => $opname->id,       // ← ganti ini
//                     'created_by'      => auth()->id(),
//                     'note'            => "Penyesuaian stok dari opname #{$opname->opname_number}",
//                 ]);
//             }

//             $opname->update(['status' => 'approved', 'approved_by' => auth()->id(), 'approved_at' => now()]);
//         });

//         return response()->json(['success' => true, 'message' => 'Opname disetujui dan stok telah disesuaikan.', 'data' => $opname->fresh()]);
//     }

//     // POST reject
//     public function reject(Request $request, StockOpname $opname): JsonResponse
//     {
//         if ($opname->status !== 'pending_approval') {
//             return response()->json(['success' => false, 'message' => 'Hanya opname pending_approval yang dapat ditolak.'], 422);
//         }

//         $validator = Validator::make($request->all(), ['reject_reason' => 'required|string']);
//         if ($validator->fails()) {
//             return response()->json(['success' => false, 'message' => 'Alasan penolakan wajib diisi.', 'errors' => $validator->errors()], 422);
//         }

//         $opname->update(['status' => 'in_progress', 'reject_reason' => $request->reject_reason]);

//         return response()->json(['success' => true, 'message' => 'Opname dikembalikan untuk perbaikan.', 'data' => $opname->fresh()]);
//     }
// }

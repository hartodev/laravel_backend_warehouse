<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\Request as StockRequest;
use App\Models\RequestItem;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Services\NotificationService;

class RequestController extends Controller
{
    // GET — user lihat requestnya sendiri, admin lihat semua
    public function index(Request $request): JsonResponse
    {
        $query = StockRequest::with(['user:id,name', 'items.product:id,name,sku,unit', 'approvedBy:id,name'])
            ->when(! auth()->user()->isSuperAdmin() && ! auth()->user()->isAdmin(), fn($q) => $q->where('user_id', auth()->id()))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->purpose, fn($q) => $q->where('purpose', $request->purpose))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json(['success' => true, 'data' => $query]);
    }

    // Admin lihat semua request
    public function indexAdmin(Request $request): JsonResponse
    {
        $requests = StockRequest::with(['user:id,name', 'items.product:id,name,sku,unit', 'approvedBy:id,name'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->user_id, fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->purpose, fn($q) => $q->where('purpose', $request->purpose))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json(['success' => true, 'data' => $requests]);
    }

    public function show(StockRequest $request): JsonResponse
    {
        // User hanya bisa lihat miliknya sendiri
        if (auth()->user()->isUser() && $request->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $request->load(['user:id,name', 'items.product:id,name,sku,unit,photo', 'approvedBy:id,name', 'stockMovements']);

        return response()->json(['success' => true, 'data' => $request]);
    }

    public function showAdmin(StockRequest $request): JsonResponse
    {
        $request->load(['user:id,name', 'items.product:id,name,sku,unit', 'approvedBy:id,name', 'stockMovements']);

        return response()->json(['success' => true, 'data' => $request]);
    }

    public function store(Request $httpRequest): JsonResponse
    {
        $validated = $httpRequest->validate([
            'purpose' => 'required|in:maintenance,distribution,return,other',
            'note'    => 'nullable|string',
            'items'   => 'required|array|min:1',

            // Barang gudang
            'items.*.product_id' => 'nullable|integer|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.note'       => 'nullable|string',

            // Barang dari luar
            'items.*.external_name'  => 'nullable|string|max:255',
            'items.*.external_spec'  => 'nullable|string',
            'items.*.external_link'  => 'nullable|string|max:500',
            'items.*.external_price' => 'nullable|numeric|min:0',
        ]);

        // Setiap item HARUS salah satu: punya product_id (gudang) ATAU
        // lengkap 4 field eksternal. Tidak boleh kosong semua / campur tanggung.
        foreach ($validated['items'] as $i => $item) {
            $hasProduct = ! empty($item['product_id']);
            $hasExternal = ! empty($item['external_name'])
                && ! empty($item['external_spec'])
                && ! empty($item['external_link'])
                && isset($item['external_price']);

            if (! $hasProduct && ! $hasExternal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal.',
                    'errors'  => [
                        "items.$i" => ['Barang harus dipilih dari gudang, atau diisi lengkap sebagai barang dari luar.'],
                    ],
                ], 422);
            }
        }

        $stockRequest = StockRequest::create([
            'user_id'        => $httpRequest->user()->id,
            'request_number' => 'REQ-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6)),
            'purpose'        => $validated['purpose'],
            'status'         => 'pending',
            'note'           => $validated['note'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            $stockRequest->items()->create([
                'product_id'     => $item['product_id'] ?? null,
                'quantity'       => $item['quantity'],
                'note'           => $item['note'] ?? null,
                'external_name'  => $item['external_name'] ?? null,
                'external_spec'  => $item['external_spec'] ?? null,
                'external_link'  => $item['external_link'] ?? null,
                'external_price' => $item['external_price'] ?? null,
            ]);
        }


        NotificationController::sendToRole(
            'admin',
            'request_created',
            'Request Baru Masuk',
            "{$httpRequest->user()->name} mengajukan permintaan baru #{$stockRequest->request_number}.",
            ['request_id' => $stockRequest->id]
        );

        return response()->json([
            'success' => true,
            'message' => 'Permintaan berhasil dibuat.',
            'data'    => $stockRequest->load('items.product'),
        ], 201);
    }

    public function update(Request $httpRequest, StockRequest $request): JsonResponse
    {
        if ($request->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        if (! $request->isPending()) {
            return response()->json(['success' => false, 'message' => 'Hanya permintaan pending yang dapat diubah.'], 422);
        }

        $request->update($httpRequest->only('purpose', 'note'));

        return response()->json([
            'success' => true,
            'message' => 'Permintaan berhasil diupdate.',
            'data'    => $request->fresh()->load('items.product:id,name,sku'),
        ]);
    }

    // Cancel / delete
    public function destroy(StockRequest $request): JsonResponse
    {
        if ($request->user_id !== auth()->id() && ! auth()->user()->isAdmin() && ! auth()->user()->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        if (! $request->isPending()) {
            return response()->json(['success' => false, 'message' => 'Hanya permintaan pending yang dapat dibatalkan.'], 422);
        }

        // Kalau bukan pemilik yang cancel (berarti admin/super_admin) → kasih tau pemilik
        if (auth()->id() !== $request->user_id) {
            NotificationController::send(
                $request->user_id,
                'request_cancelled',
                'Request Dibatalkan',
                "Request #{$request->request_number} Anda telah dibatalkan oleh admin.",
                ['request_id' => $request->id]
            );
        }

        $request->items()->delete();
        $request->delete();

        return response()->json(['success' => true, 'message' => 'Permintaan berhasil dibatalkan.']);
    }

    // Admin: approve
    public function approve(Request $httpRequest, StockRequest $request): JsonResponse
    {
        if (! $request->isPending()) {
            return response()->json(['success' => false, 'message' => 'Hanya permintaan pending yang dapat disetujui.'], 422);
        }

        $validator = Validator::make($httpRequest->all(), [
            'warehouse_id'               => 'required|exists:warehouses,id',
            'items'                      => 'required|array',
            'items.*.request_item_id'    => 'required|exists:request_items,id',
            'items.*.approved_quantity'  => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        // ── Validasi stok DULU, sebelum ada perubahan apapun ───────────────
        $stockErrors = [];
        $stockMap = [];

        foreach ($httpRequest->items as $item) {
            $approvedQty = $item['approved_quantity'];
            if ($approvedQty <= 0) {
                continue;
            }

            $requestItem = RequestItem::find($item['request_item_id']);
            if (! $requestItem || $requestItem->request_id !== $request->id) {
                $stockErrors[] = "Item #{$item['request_item_id']} tidak valid untuk permintaan ini.";
                continue;
            }

            if (! $requestItem->product_id) {
                continue;
            }

            $stock = Stock::where('warehouse_id', $httpRequest->warehouse_id)
                ->where('product_id', $requestItem->product_id)
                ->first();

            if (! $stock) {
                $namaProduk = $requestItem->product->name ?? "produk #{$requestItem->product_id}";
                $stockErrors[] = "Stok '{$namaProduk}' tidak ditemukan di gudang yang dipilih (mungkin belum pernah di-stock-in di gudang ini).";
                continue;
            }

            if ($stock->quantity < $approvedQty) {
                $namaProduk = $requestItem->product->name ?? "produk #{$requestItem->product_id}";
                $stockErrors[] = "Stok '{$namaProduk}' tidak cukup (tersedia {$stock->quantity}, diminta {$approvedQty}).";
                continue;
            }

            $stockMap[$requestItem->id] = $stock;
        }

        if (! empty($stockErrors)) {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan tidak dapat disetujui karena masalah stok.',
                'errors'  => $stockErrors,
            ], 422);
        }

        DB::transaction(function () use ($httpRequest, $request, $stockMap) {
            $totalApproved = 0;

            foreach ($httpRequest->items as $item) {
                $requestItem = RequestItem::find($item['request_item_id']);
                if (! $requestItem || $requestItem->request_id !== $request->id) {
                    continue;
                }

                $approvedQty = $item['approved_quantity'];
                $requestItem->update(['approved_quantity' => $approvedQty]);

                if ($approvedQty <= 0 || ! isset($stockMap[$requestItem->id])) {
                    continue;
                }

                $stock = $stockMap[$requestItem->id];
                $before = $stock->quantity;
                $stock->reduceStock($approvedQty);

                StockMovement::create([
                    'product_id'      => $requestItem->product_id,
                    'warehouse_id'    => $httpRequest->warehouse_id,
                    'type'            => 'out',
                    'quantity'        => $approvedQty,
                    'quantity_before' => $before,
                    'quantity_after'  => $stock->quantity,
                    'request_id'      => $request->id,
                    'request_item_id' => $requestItem->id,
                    'created_by'      => auth()->id(),
                    'note'            => "Pengeluaran untuk permintaan #{$request->request_number}",
                ]);

                $totalApproved += $approvedQty;
            }

            $request->update([
                'status'      => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            ActivityLog::record(
                'approve',
                'Request',
                $request->id,
                "Permintaan barang #{$request->request_number} disetujui — {$totalApproved} unit dikeluarkan dari gudang #{$httpRequest->warehouse_id}",
                ['status' => 'pending'],
                ['status' => 'approved', 'warehouse_id' => $httpRequest->warehouse_id, 'total_approved_qty' => $totalApproved]
            );

            NotificationController::send(
                $request->user_id,
                'request_approved',
                'Request Disetujui',
                "Request #{$request->request_number} Anda telah disetujui.",
                ['request_id' => $request->id]
            );
        });

        return response()->json([
            'success' => true,
            'message' => 'Permintaan disetujui dan stok telah dikeluarkan.',
            'data'    => $request->fresh()->load('items.product:id,name,sku'),
        ]);
    }
    // Admin: reject
    public function reject(Request $httpRequest, StockRequest $request): JsonResponse
    {
        if (! $request->isPending()) {
            return response()->json(['success' => false, 'message' => 'Hanya permintaan pending yang dapat ditolak.'], 422);
        }

        $validator = Validator::make($httpRequest->all(), ['reject_reason' => 'required|string']);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Alasan penolakan wajib diisi.', 'errors' => $validator->errors()], 422);
        }

        $request->update([
            'status'        => 'rejected',
            'approved_by'   => auth()->id(),
            'approved_at'   => now(),
            'reject_reason' => $httpRequest->reject_reason,
        ]);

        ActivityLog::record(
            'reject',
            'Request',
            $request->id,
            "Permintaan barang #{$request->request_number} ditolak — alasan: {$httpRequest->reject_reason}",
            ['status' => 'pending'],
            ['status' => 'rejected', 'reject_reason' => $httpRequest->reject_reason]
        );

        NotificationController::send(
            $request->user_id,
            'request_rejected',
            'Request Ditolak',
            "Request #{$request->request_number} ditolak. Alasan: {$httpRequest->reject_reason}",
            ['request_id' => $request->id, 'reason' => $httpRequest->reject_reason]
        );

        return response()->json(['success' => true, 'message' => 'Permintaan ditolak.', 'data' => $request->fresh()]);
    }

    // Admin: complete — stok sudah dikurangi saat approve, cukup update status
    public function complete(Request $httpRequest, StockRequest $request): JsonResponse
    {
        if (! $request->isApproved()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya permintaan yang sudah disetujui yang dapat diselesaikan.',
            ], 422);
        }

        $request->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);

        ActivityLog::record(
            'complete',
            'Request',
            $request->id,
            "Permintaan barang #{$request->request_number} diselesaikan",
            ['status' => 'approved'],
            ['status' => 'completed', 'completed_at' => now()->toIso8601String()]
        );
        NotificationController::send(
            $request->user_id,
            'request_completed',
            'Request Selesai',
            "Request #{$request->request_number} telah selesai diproses.",
            ['request_id' => $request->id]
        );
        return response()->json([
            'success' => true,
            'message' => 'Permintaan berhasil diselesaikan.',
            'data'    => $request->fresh()->load('items.product:id,name,sku'),
        ]);
    }
}

// namespace App\Http\Controllers\Api\User;

// use App\Http\Controllers\Controller;
// use App\Models\Request as StockRequest;
// use App\Models\RequestItem;
// use App\Models\Stock;
// use App\Models\StockMovement;
// use Illuminate\Http\JsonResponse;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Validator;
// use App\Models\Request as RequestModel;
// use App\Models\ActivityLog;

// class RequestController extends Controller
// {
//      // GET — user lihat requestnya sendiri, admin lihat semua
//     public function index(Request $request): JsonResponse
//     {
//         $query = StockRequest::with(['user:id,name', 'items.product:id,name,sku,unit', 'approvedBy:id,name'])
//             ->when(! auth()->user()->isSuperAdmin() && ! auth()->user()->isAdmin(), fn($q) => $q->where('user_id', auth()->id()))
//             ->when($request->status, fn($q) => $q->where('status', $request->status))
//             ->when($request->purpose, fn($q) => $q->where('purpose', $request->purpose))
//             ->latest()
//             ->paginate($request->per_page ?? 15);

//         return response()->json(['success' => true, 'data' => $query]);
//     }

//     // Admin lihat semua request
//     public function indexAdmin(Request $request): JsonResponse
//     {
//         $requests = StockRequest::with(['user:id,name', 'items.product:id,name,sku,unit', 'approvedBy:id,name'])
//             ->when($request->status, fn($q) => $q->where('status', $request->status))
//             ->when($request->user_id, fn($q) => $q->where('user_id', $request->user_id))
//             ->when($request->purpose, fn($q) => $q->where('purpose', $request->purpose))
//             ->latest()
//             ->paginate($request->per_page ?? 15);

//         return response()->json(['success' => true, 'data' => $requests]);
//     }

//     public function show(StockRequest $request): JsonResponse
//     {
//         // User hanya bisa lihat miliknya sendiri
//         if (auth()->user()->isUser() && $request->user_id !== auth()->id()) {
//             return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
//         }

//         $request->load(['user:id,name', 'items.product:id,name,sku,unit,photo', 'approvedBy:id,name', 'stockMovements']);

//         return response()->json(['success' => true, 'data' => $request]);
//     }

//     public function showAdmin(StockRequest $request): JsonResponse
//     {
//         $request->load(['user:id,name', 'items.product:id,name,sku,unit', 'approvedBy:id,name', 'stockMovements']);

//         return response()->json(['success' => true, 'data' => $request]);
//     }


//     // public function store(Request $httpRequest): JsonResponse
//     // {
//     //     $validator = Validator::make($httpRequest->all(), [
//     //         'purpose'            => 'required|in:maintenance,distribution,return,other',
//     //         'note'               => 'nullable|string',
//     //         'items'              => 'required|array|min:1',
//     //         'items.*.product_id' => 'required|exists:products,id',
//     //         'items.*.quantity'   => 'required|integer|min:1',
//     //         'items.*.note'       => 'nullable|string',

//     //     ]);

//     //     if ($validator->fails()) {
//     //         return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
//     //     }

//     //     $productIds = array_column($httpRequest->items, 'product_id');
//     //     if (count($productIds) !== count(array_unique($productIds))) {
//     //         return response()->json(['success' => false, 'message' => 'Produk yang sama tidak boleh duplikat.'], 422);
//     //     }

//     //     DB::transaction(function () use ($httpRequest, &$stockRequest) {
//     //         $stockRequest = StockRequest::create([
//     //             'user_id' => auth()->id(),
//     //             'purpose' => $httpRequest->purpose,
//     //             'status'  => 'pending',
//     //             'note'    => $httpRequest->note,
//     //         ]);

//     //         foreach ($httpRequest->items as $item) {
//     //             RequestItem::create([
//     //                 'request_id' => $stockRequest->id,
//     //                 'product_id' => $item['product_id'],
//     //                 'quantity'   => $item['quantity'],
//     //                 'note'       => $item['note'] ?? null,
//     //             ]);
//     //         }

//     //         // ── Activity log: user membuat request ────────────────
//     //         ActivityLog::record(
//     //             'create',
//     //             'Request',
//     //             $stockRequest->id,
//     //             "Permintaan barang #{$stockRequest->request_number} dibuat oleh " . auth()->user()->name,
//     //             null,
//     //             ['status' => 'pending', 'purpose' => $stockRequest->purpose, 'items_count' => count($httpRequest->items)]
//     //         );
//     //     });

//     //     return response()->json([
//     //         'success' => true,
//     //         'message' => 'Permintaan barang berhasil dikirim.',
//     //         'data'    => $stockRequest->load('items.product:id,name,sku'),
//     //     ], 201);
//     // }


//     public function store(Request $httpRequest)
//     {
//         $validated = $httpRequest->validate([
//             'purpose' => 'required|in:maintenance,distribution,return,other',
//             'note'    => 'nullable|string',
//             'items'   => 'required|array|min:1',

//             // Barang gudang (TIDAK diubah)
//             'items.*.product_id' => 'nullable|integer|exists:products,id',
//             'items.*.quantity'   => 'required|integer|min:1',
//             'items.*.note'       => 'nullable|string',

//             // Barang dari luar
//             'items.*.external_name'  => 'nullable|string|max:255',
//             'items.*.external_spec'  => 'nullable|string',
//             'items.*.external_link'  => 'nullable|string|max:500',
//             'items.*.external_price' => 'nullable|numeric|min:0',
//         ]);

//         // Setiap item HARUS salah satu: punya product_id (gudang) ATAU
//         // lengkap 4 field eksternal. Tidak boleh kosong semua / campur tanggung.
//         foreach ($validated['items'] as $i => $item) {
//             $hasProduct = !empty($item['product_id']);
//             $hasExternal = !empty($item['external_name'])
//                 && !empty($item['external_spec'])
//                 && !empty($item['external_link'])
//                 && isset($item['external_price']);

//             if (!$hasProduct && !$hasExternal) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Validasi gagal.',
//                     'errors'  => [
//                         "items.$i" => ['Barang harus dipilih dari gudang, atau diisi lengkap sebagai barang dari luar.'],
//                     ],
//                 ], 422);
//             }
//         }

//         $stockRequest = \App\Models\Request::create([
//             'user_id'        => $httpRequest->user()->id,
//             'request_number' => 'REQ-' . now()->format('Ymd') . '-' . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(6)),
//             'purpose'        => $validated['purpose'],
//             'status'         => 'pending',
//             'note'           => $validated['note'] ?? null,
//         ]);

//         foreach ($validated['items'] as $item) {
//             $stockRequest->items()->create([
//                 'product_id'      => $item['product_id'] ?? null,
//                 'quantity'        => $item['quantity'],
//                 'note'            => $item['note'] ?? null,
//                 'external_name'   => $item['external_name'] ?? null,
//                 'external_spec'   => $item['external_spec'] ?? null,
//                 'external_link'   => $item['external_link'] ?? null,
//                 'external_price'  => $item['external_price'] ?? null,
//             ]);
//         }

//         return response()->json([
//             'success' => true,
//             'message' => 'Permintaan berhasil dibuat.',
//             'data'    => $stockRequest->load('items.product'),
//         ], 201);
//     }


//     public function update(Request $httpRequest, StockRequest $request): JsonResponse
//     {
//         if ($request->user_id !== auth()->id()) {
//             return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
//         }

//         if (! $request->isPending()) {
//             return response()->json(['success' => false, 'message' => 'Hanya permintaan pending yang dapat diubah.'], 422);
//         }

//         $request->update($httpRequest->only('purpose', 'note'));

//         return response()->json(['success' => true, 'message' => 'Permintaan berhasil diupdate.', 'data' => $request->fresh()->load('items.product:id,name,sku')]);
//     }

//     // Cancel / delete
//     public function destroy(StockRequest $request): JsonResponse
//     {
//         if ($request->user_id !== auth()->id() && ! auth()->user()->isAdmin() && ! auth()->user()->isSuperAdmin()) {
//             return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
//         }

//         if (! $request->isPending()) {
//             return response()->json(['success' => false, 'message' => 'Hanya permintaan pending yang dapat dibatalkan.'], 422);
//         }

//         $request->items()->delete();
//         $request->delete();

//         return response()->json(['success' => true, 'message' => 'Permintaan berhasil dibatalkan.']);
//     }

//     // Admin: approve
//     // public function approve(Request $httpRequest, StockRequest $request): JsonResponse
//     // {
//     //     if (! $request->isPending()) {
//     //         return response()->json(['success' => false, 'message' => 'Hanya permintaan pending yang dapat disetujui.'], 422);
//     //     }

//     //     $validator = Validator::make($httpRequest->all(), [
//     //         'warehouse_id'                => 'required|exists:warehouses,id',
//     //         'items'                       => 'required|array',
//     //         'items.*.request_item_id'     => 'required|exists:request_items,id',
//     //         'items.*.approved_quantity'   => 'required|integer|min:0',
//     //     ]);

//     //     if ($validator->fails()) {
//     //         return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
//     //     }

//     //     DB::transaction(function () use ($httpRequest, $request) {
//     //         foreach ($httpRequest->items as $item) {
//     //             $requestItem = RequestItem::find($item['request_item_id']);
//     //             if (! $requestItem || $requestItem->request_id !== $request->id) continue;

//     //             $approvedQty = $item['approved_quantity'];
//     //             $requestItem->update(['approved_quantity' => $approvedQty]);

//     //             if ($approvedQty > 0) {
//     //                 $stock = Stock::where('warehouse_id', $httpRequest->warehouse_id)
//     //                                ->where('product_id', $requestItem->product_id)
//     //                                ->first();

//     //                 if ($stock) {
//     //                     $before = $stock->quantity;
//     //                     $stock->reduceStock($approvedQty);

//     //                     StockMovement::create([
//     //                         'product_id'      => $requestItem->product_id,
//     //                         'warehouse_id'    => $httpRequest->warehouse_id,
//     //                         'type'            => 'out',
//     //                         'quantity'        => $approvedQty,
//     //                         'quantity_before' => $before,
//     //                         'quantity_after'  => $stock->quantity,
//     //                         'request_id'      => $request->id,
//     //                         'request_item_id' => $requestItem->id,
//     //                         'created_by'      => auth()->id(),
//     //                         'note'            => "Pengeluaran untuk permintaan #{$request->id}",
//     //                     ]);
//     //                 }
//     //             }
//     //         }

//     //         $request->update([
//     //             'status'      => 'approved',
//     //             'approved_by' => auth()->id(),
//     //             'approved_at' => now(),
//     //         ]);

//     //         ActivityLog::record(
//     //             'approve',
//     //             'Request',
//     //             $request->id,
//     //             "Permintaan barang #{$request->request_number} disetujui — {$totalApproved} unit dikeluarkan dari gudang #{$httpRequest->warehouse_id}",
//     //             ['status' => 'pending'],
//     //             ['status' => 'approved', 'warehouse_id' => $httpRequest->warehouse_id, 'total_approved_qty' => $totalApproved]
//     //         );
//     //     });

//     //     return response()->json(['success' => true, 'message' => 'Permintaan disetujui dan stok telah dikeluarkan.', 'data' => $request->fresh()->load('items.product:id,name,sku')]);
//     // }




//     public function approve(Request $httpRequest, StockRequest $request): JsonResponse
//     {
//         if (!$request->isPending()) {
//             return response()->json(['success' => false, 'message' => 'Hanya permintaan pending yang dapat disetujui.'], 422);
//         }

//         $validator = Validator::make($httpRequest->all(), [
//             'warehouse_id'              => 'required|exists:warehouses,id',
//             'items'                     => 'required|array',
//             'items.*.request_item_id'   => 'required|exists:request_items,id',
//             'items.*.approved_quantity' => 'required|integer|min:0',
//         ]);

//         if ($validator->fails()) {
//             return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
//         }

//         DB::transaction(function () use ($httpRequest, $request) {
//             $totalApproved = 0; // ← FIX: inisialisasi di sini

//             foreach ($httpRequest->items as $item) {
//                 $requestItem = RequestItem::find($item['request_item_id']);
//                 if (!$requestItem || $requestItem->request_id !== $request->id) continue;

//                 $approvedQty = $item['approved_quantity'];
//                 $requestItem->update(['approved_quantity' => $approvedQty]);

//                 if ($approvedQty > 0) {
//                     $stock = Stock::where('warehouse_id', $httpRequest->warehouse_id)
//                         ->where('product_id', $requestItem->product_id)
//                         ->first();

//                     if ($stock) {
//                         $before = $stock->quantity;
//                         $stock->reduceStock($approvedQty);

//                         StockMovement::create([
//                             'product_id'      => $requestItem->product_id,
//                             'warehouse_id'    => $httpRequest->warehouse_id,
//                             'type'            => 'out',
//                             'quantity'        => $approvedQty,
//                             'quantity_before' => $before,
//                             'quantity_after'  => $stock->quantity,
//                             'request_id'      => $request->id,
//                             'request_item_id' => $requestItem->id,
//                             'created_by'      => auth()->id(),
//                             'note'            => "Pengeluaran untuk permintaan #{$request->request_number}",
//                         ]);


//                         $request->update([
//                 'status'      => 'approved',
//                 'approved_by' => auth()->id(),
//                 'approved_at' => now(),
//             ]);

//             ActivityLog::record(
//                 'approve',
//                 'Request',
//                 $request->id,
//                 "Permintaan barang #{$request->request_number} disetujui — {$totalApproved} unit dikeluarkan dari gudang #{$httpRequest->warehouse_id}",
//                 ['status' => 'pending'],
//                 ['status' => 'approved', 'warehouse_id' => $httpRequest->warehouse_id, 'total_approved_qty' => $totalApproved]
//             );

//             \App\Models\Notification::create([
//                 'user_id' => $request->user_id,   // user yang buat request
//                 'type'    => 'request_approved',
//                 'title'   => 'Request Disetujui',
//                 'body'    => "Request #{$request->request_number} Anda telah disetujui.",
//                 'data'    => ['request_id' => $request->id],
//             ]);
//         });

//         return response()->json([
//             'success' => true,
//             'message' => 'Permintaan disetujui dan stok telah dikeluarkan.',
//             'data'    => $request->fresh()->load('items.product:id,name,sku'),
//         ]);
//     }




//     // Admin: reject
//     public function reject(Request $httpRequest, StockRequest $request): JsonResponse
//     {
//         if (! $request->isPending()) {
//             return response()->json(['success' => false, 'message' => 'Hanya permintaan pending yang dapat ditolak.'], 422);
//         }

//         $validator = Validator::make($httpRequest->all(), ['reject_reason' => 'required|string']);
//         if ($validator->fails()) {
//             return response()->json(['success' => false, 'message' => 'Alasan penolakan wajib diisi.', 'errors' => $validator->errors()], 422);
//         }

//         $request->update([
//             'status'        => 'rejected',
//             'approved_by'   => auth()->id(),
//             'approved_at'   => now(),
//             'reject_reason' => $httpRequest->reject_reason,
//         ]);

//         // ── Activity log: admin reject request ────────────────────
//         ActivityLog::record(
//             'reject',
//             'Request',
//             $request->id,
//             "Permintaan barang #{$request->request_number} ditolak — alasan: {$httpRequest->reject_reason}",
//             ['status' => 'pending'],
//             ['status' => 'rejected', 'reject_reason' => $httpRequest->reject_reason]
//         );

//         \App\Models\Notification::create([
//             'user_id' => $request->user_id,
//             'type'    => 'request_rejected',
//             'title'   => 'Request Ditolak',
//             'body'    => "Request #{$request->request_number} ditolak. Alasan: {$httpRequest->reject_reason}",
//             'data'    => ['request_id' => $request->id, 'reason' => $httpRequest->reject_reason],
//         ]);

//         return response()->json(['success' => true, 'message' => 'Permintaan ditolak.', 'data' => $request->fresh()]);
//     }
//     // Di RequestController@complete
//     // Admin: complete — stok sudah dikurangi saat approve, cukup update status
//     public function complete(Request $httpRequest, StockRequest $request): JsonResponse
//     {
//         if (!$request->isApproved()) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Hanya permintaan yang sudah disetujui yang dapat diselesaikan.',
//             ], 422);
//         }

//         $request->update([
//             'status'       => 'completed',
//             'completed_at' => now(),
//         ]);

//         // ── Activity log: request selesai ─────────────────────────
//         ActivityLog::record(
//             'complete',
//             'Request',
//             $request->id,
//             "Permintaan barang #{$request->request_number} diselesaikan",
//             ['status' => 'approved'],
//             ['status' => 'completed', 'completed_at' => now()->toIso8601String()]
//         );


//         \App\Models\Notification::create([
//             'user_id' => $request->user_id,
//             'type'    => 'request_completed',
//             'title'   => 'Request Selesai',
//             'body'    => "Request #{$request->request_number} telah selesai diproses.",
//             'data'    => ['request_id' => $request->id],
//         ]);

//         return response()->json([
//             'success' => true,
//             'message' => 'Permintaan berhasil diselesaikan.',
//             'data'    => $request->fresh()->load('items.product:id,name,sku'),
//         ]);
//     }
// }

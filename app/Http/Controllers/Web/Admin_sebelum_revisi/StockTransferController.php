<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class StockTransferController extends Controller
{
    private function isSuperadmin($user): bool
    {
        return in_array($user->role, ['superadmin', 'super_admin']);
    }

    // ── GET /admin/stock-transfers ─────────────────────────────
    // NB: label & warna status sudah dihitung sendiri di dalam view
    // (lihat @php block di Admin/stock_transfer/index.blade.php),
    // jadi controller cukup kirim data transfer-nya saja.
    public function index(Request $request): View
    {
        $transfers = StockTransfer::with(['fromWarehouse:id,name', 'toWarehouse:id,name', 'requestedBy:id,name'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('Admin.stock_transfer.index', compact('transfers'));
    }

    // ── GET /admin/stock-transfers/create ──────────────────────
    public function create(): View
    {
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get(['id', 'name', 'sku', 'unit']);

        return view('Admin.stock_transfer.create', compact('warehouses', 'products'));
    }

    // ── POST /admin/stock-transfers ────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if ($this->isSuperadmin($user)) {
            return back()->with('error', 'Superadmin tidak membuat request transfer.');
        }
        if ((int) $user->warehouse_id !== (int) $request->from_warehouse_id) {
            return back()->with('error', 'Gudang asal harus sesuai gudang Anda sendiri.')->withInput();
        }

        $validator = Validator::make($request->all(), [
            'from_warehouse_id'          => 'required|exists:warehouses,id',
            'to_warehouse_id'            => 'required|exists:warehouses,id|different:from_warehouse_id',
            'transfer_date'              => 'required|date',
            'expected_arrival'           => 'nullable|date|after_or_equal:transfer_date',
            'notes'                      => 'nullable|string',
            'items'                      => 'required|array|min:1',
            'items.*.product_id'         => 'required|exists:products,id',
            'items.*.quantity_requested' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $transfer = null;
        DB::transaction(function () use ($request, &$transfer) {
            $count  = StockTransfer::whereYear('created_at', now()->year)->count() + 1;
            $number = 'TRF/' . now()->format('Y') . '/' . str_pad($count, 4, '0', STR_PAD_LEFT);

            $transfer = StockTransfer::create([
                'transfer_number'   => $number,
                'from_warehouse_id' => $request->from_warehouse_id,
                'to_warehouse_id'   => $request->to_warehouse_id,
                'requested_by'      => $user->id,
                'status'            => 'pending_confirmation',
                'transfer_date'     => $request->transfer_date,
                'expected_arrival'  => $request->expected_arrival,
                'notes'             => $request->notes,
            ]);

            foreach ($request->items as $item) {
                StockTransferItem::create([
                    'stock_transfer_id'  => $transfer->id,
                    'product_id'         => $item['product_id'],
                    'quantity_requested' => $item['quantity_requested'],
                    'quantity_sent'      => 0,
                    'quantity_received'  => 0,
                ]);
            }
        });

        return redirect()
            ->route('admin.stock-transfers.show', $transfer)
            ->with('success', 'Request transfer dibuat. Silakan konfirmasi untuk melanjutkan.');
    }

    // ── GET /admin/stock-transfers/{stockTransfer} ─────────────
    public function show(StockTransfer $transfer): View
    {
        $transfer->load([
            'fromWarehouse:id,name',
            'toWarehouse:id,name',
            'requestedBy:id,name',
            'confirmedBy:id,name',
            'approvedBy:id,name',
            'cancelledBy:id,name',
            'receivedBy:id,name',
            'discrepancyReportedBy:id,name',
            'resolvedBy:id,name',
            'items.product:id,name,sku,unit',
        ]);

        return view('Admin.stock_transfer.show', compact('transfer'));
    }

    // ── POST /admin/stock-transfers/{stockTransfer}/confirm ────
    // Admin Gudang A lanjutkan request ke approval superadmin
    public function confirm(StockTransfer $transfer): RedirectResponse
    {
        $user = auth()->user();

        if ($transfer->status !== 'pending_confirmation') {
            return back()->with('error', 'Hanya transfer pending_confirmation yang bisa dikonfirmasi.');
        }
        if ((int) $transfer->requested_by !== (int) $user->id) {
            return back()->with('error', 'Hanya pembuat request yang bisa konfirmasi.');
        }

        $transfer->update([
            'status'       => 'pending_approval',
            'confirmed_by' => $user->id,
            'confirmed_at' => now(),
        ]);

        return back()->with('success', 'Transfer dikonfirmasi, menunggu approval superadmin.');
    }

    // ── POST /admin/stock-transfers/{stockTransfer}/cancel ─────
    // Admin Gudang A batal (wajib alasan)
    public function cancel(Request $request, StockTransfer $transfer): RedirectResponse
    {
        $user = auth()->user();

        if ($transfer->status !== 'pending_confirmation') {
            return back()->with('error', 'Hanya transfer pending_confirmation yang bisa dibatalkan di tahap ini.');
        }
        if ((int) $transfer->requested_by !== (int) $user->id) {
            return back()->with('error', 'Hanya pembuat request yang bisa membatalkan.');
        }

        $validator = Validator::make($request->all(), ['cancel_reason' => 'required|string']);
        if ($validator->fails()) {
            return back()->withErrors($validator)->with('error', 'Alasan pembatalan wajib diisi.');
        }

        $transfer->update([
            'status'        => 'cancelled',
            'cancelled_by'  => $user->id,
            'cancelled_at'  => now(),
            'cancel_reason' => $request->cancel_reason,
        ]);

        return redirect()->route('admin.stock-transfers.index')->with('success', 'Transfer dibatalkan.');
    }

    // ── POST /admin/stock-transfers/{stockTransfer}/send ────────
    // Admin Gudang A kirim barang + lampiran wajib
    public function send(Request $request, StockTransfer $transfer): RedirectResponse
    {
        $user = auth()->user();

        if ($transfer->status !== 'approved') {
            return back()->with('error', 'Hanya transfer yang sudah disetujui yang dapat dikirim.');
        }
        if ((int) $user->warehouse_id !== (int) $transfer->from_warehouse_id) {
            return back()->with('error', 'Hanya admin gudang asal yang bisa mengirim.');
        }

        // Form Blade mengirim 'items' sebagai array langsung (bukan JSON string
        // seperti request multipart di API), jadi tidak perlu json_decode di sini.
        $validator = Validator::make($request->all(), [
            'items'                           => 'required|array|min:1',
            'items.*.stock_transfer_item_id'  => 'required|exists:stock_transfer_items,id',
            'items.*.quantity_sent'           => 'required|integer|min:1',
            'attachment'                      => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $path = $request->file('attachment')->store('transfer-shipments', 'public');

        try {
            DB::transaction(function () use ($request, $transfer, $user, $path) {
                foreach ($request->items as $item) {
                    $transferItem = StockTransferItem::find($item['stock_transfer_item_id']);
                    if (! $transferItem || $transferItem->stock_transfer_id !== $transfer->id) continue;

                    $qtySent = min($item['quantity_sent'], $transferItem->quantity_requested);

                    $stock = Stock::where('warehouse_id', $transfer->from_warehouse_id)
                        ->where('product_id', $transferItem->product_id)
                        ->first();

                    if (! $stock) {
                        throw new \RuntimeException(
                            "Stok untuk produk \"{$transferItem->product->name}\" tidak ditemukan di gudang asal."
                        );
                    }

                    if ($stock->quantity < $qtySent) {
                        throw new \RuntimeException(
                            "Stok \"{$transferItem->product->name}\" tidak cukup. Tersedia: {$stock->quantity}, diminta kirim: {$qtySent}."
                        );
                    }

                    $before = $stock->quantity;
                    $stock->reduceStock($qtySent);

                    StockMovement::create([
                        'product_id'      => $transferItem->product_id,
                        'warehouse_id'    => $transfer->from_warehouse_id,
                        'type'            => 'transfer_out',
                        'quantity'        => $qtySent,
                        'quantity_before' => $before,
                        'quantity_after'  => $stock->quantity,
                        'reference_type'  => 'stock_transfer',
                        'reference_id'    => $transfer->id,
                        'created_by'      => $user->id,
                        'note'            => "Pengiriman transfer #{$transfer->transfer_number}",
                    ]);

                    $transferItem->update(['quantity_sent' => $qtySent]);
                }

                $transfer->update([
                    'status'              => 'in_transit',
                    'sent_at'             => now(),
                    'sent_by'             => $user->id,
                    'shipment_attachment' => $path,
                ]);
            });
        } catch (\RuntimeException $e) {
            Storage::disk('public')->delete($path);
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('admin.stock-transfers.show', $transfer)->with('success', 'Barang berhasil dikirim.');
    }

    // ── POST /admin/stock-transfers/{stockTransfer}/checklist ──
    // Admin Gudang B validasi penerimaan
    public function checklist(Request $request, StockTransfer $transfer): RedirectResponse
    {
        $user = auth()->user();

        if ($transfer->status !== 'in_transit') {
            return back()->with('error', 'Hanya transfer in_transit yang dapat divalidasi.');
        }
        if ((int) $user->warehouse_id !== (int) $transfer->to_warehouse_id) {
            return back()->with('error', 'Hanya admin gudang tujuan yang bisa checklist.');
        }

        $validator = Validator::make($request->all(), [
            'items'                          => 'required|array|min:1',
            'items.*.stock_transfer_item_id' => 'required|exists:stock_transfer_items,id',
            'items.*.quantity_received'      => 'required|integer|min:0',
            'discrepancy_notes'              => 'required_if:has_discrepancy,true|nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $hasDiscrepancy = false;

        DB::transaction(function () use ($request, $transfer, $user, &$hasDiscrepancy) {
            foreach ($request->items as $item) {
                $transferItem = StockTransferItem::find($item['stock_transfer_item_id']);
                if (! $transferItem || $transferItem->stock_transfer_id !== $transfer->id) continue;

                $qtyReceived = $item['quantity_received'];
                $isMatched   = $qtyReceived === $transferItem->quantity_sent;
                if (! $isMatched) $hasDiscrepancy = true;

                $stock = Stock::firstOrCreate(
                    ['warehouse_id' => $transfer->to_warehouse_id, 'product_id' => $transferItem->product_id],
                    ['quantity' => 0]
                );

                $before = $stock->quantity;
                if ($qtyReceived > 0) $stock->addStock($qtyReceived);

                StockMovement::create([
                    'product_id'      => $transferItem->product_id,
                    'warehouse_id'    => $transfer->to_warehouse_id,
                    'type'            => 'transfer_in',
                    'quantity'        => $qtyReceived,
                    'quantity_before' => $before,
                    'quantity_after'  => $stock->quantity,
                    'reference_type'  => 'stock_transfer',
                    'reference_id'    => $transfer->id,
                    'created_by'      => $user->id,
                    'note'            => "Penerimaan transfer #{$transfer->transfer_number}",
                ]);

                $transferItem->update([
                    'quantity_received' => $qtyReceived,
                    'is_matched'        => $isMatched,
                ]);
            }

            if ($hasDiscrepancy) {
                $transfer->update([
                    'status'                  => 'discrepancy',
                    'discrepancy_notes'       => $request->discrepancy_notes,
                    'discrepancy_reported_by' => $user->id,
                    'discrepancy_reported_at' => now(),
                ]);
            } else {
                $transfer->update([
                    'status'      => 'received',
                    'received_by' => $user->id,
                    'received_at' => now(),
                ]);
            }
        });

        $msg = $hasDiscrepancy
            ? 'Ada selisih barang, menunggu resolusi superadmin.'
            : 'Barang diterima sesuai, transfer selesai.';

        return redirect()->route('admin.stock-transfers.show', $transfer)->with('success', $msg);
    }
}

// namespace App\Http\Controllers\Web\Admin;

// use App\Http\Controllers\Controller;
// use App\Models\Product;
// use App\Models\Stock;
// use App\Models\StockMovement;
// use App\Models\StockTransfer;
// use App\Models\StockTransferItem;
// use App\Models\Warehouse;
// use Illuminate\Contracts\View\View;
// use Illuminate\Http\RedirectResponse;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\Validator;

// class StockTransferController extends Controller
// {
//     private function isSuperadmin($user): bool
//     {
//         return in_array($user->role, ['superadmin', 'super_admin']);
//     }

//     // ── GET /admin/stock-transfers ───────────────────────────────
//     public function index(Request $request): View
//     {
//         $transfers = StockTransfer::with(['fromWarehouse:id,name,code', 'toWarehouse:id,name,code', 'requestedBy:id,name'])
//             ->when($request->status, fn($q) => $q->where('status', $request->status))
//             ->latest()
//             ->paginate(15)
//             ->withQueryString();

//         return view('admin.stock-transfers.index', compact('transfers'));
//     }

//     // ── GET /admin/stock-transfers/create ────────────────────────
//     public function create(): View
//     {
//         $user = auth()->user();
//         $warehouses = Warehouse::where('is_active', true)->get();
//         $products = Product::where('is_active', true)->orderBy('name')->get();

//         return view('admin.stock-transfers.create', compact('warehouses', 'products', 'user'));
//     }

//     // ── POST /admin/stock-transfers ──────────────────────────────
//     public function store(Request $request): RedirectResponse
//     {
//         $user = auth()->user();

//         if ($this->isSuperadmin($user)) {
//             return back()->with('error', 'Superadmin tidak membuat request transfer.');
//         }
//         if ((int) $user->warehouse_id !== (int) $request->from_warehouse_id) {
//             return back()->with('error', 'Gudang asal harus sesuai gudang Anda sendiri.')->withInput();
//         }

//         $validator = Validator::make($request->all(), [
//             'from_warehouse_id'          => 'required|exists:warehouses,id',
//             'to_warehouse_id'            => 'required|exists:warehouses,id|different:from_warehouse_id',
//             'transfer_date'              => 'required|date',
//             'expected_arrival'           => 'nullable|date|after_or_equal:transfer_date',
//             'notes'                      => 'nullable|string',
//             'items'                      => 'required|array|min:1',
//             'items.*.product_id'         => 'required|exists:products,id',
//             'items.*.quantity_requested' => 'required|integer|min:1',
//         ]);

//         if ($validator->fails()) {
//             return back()->withErrors($validator)->withInput();
//         }

//         $transfer = null;
//         DB::transaction(function () use ($request, &$transfer) {
//             $count  = StockTransfer::whereYear('created_at', now()->year)->count() + 1;
//             $number = 'TRF/' . now()->format('Y') . '/' . str_pad($count, 4, '0', STR_PAD_LEFT);

//             $transfer = StockTransfer::create([
//                 'transfer_number'   => $number,
//                 'from_warehouse_id' => $request->from_warehouse_id,
//                 'to_warehouse_id'   => $request->to_warehouse_id,
//                 'requested_by'      => auth()->id(),
//                 'status'            => 'pending_confirmation',
//                 'transfer_date'     => $request->transfer_date,
//                 'expected_arrival'  => $request->expected_arrival,
//                 'notes'             => $request->notes,
//             ]);

//             foreach ($request->items as $item) {
//                 StockTransferItem::create([
//                     'stock_transfer_id'  => $transfer->id,
//                     'product_id'         => $item['product_id'],
//                     'quantity_requested' => $item['quantity_requested'],
//                     'quantity_sent'      => 0,
//                     'quantity_received'  => 0,
//                 ]);
//             }
//         });

//         return redirect()
//             ->route('admin.stock-transfers.show', $transfer)
//             ->with('success', 'Request transfer dibuat. Silakan konfirmasi untuk melanjutkan.');
//     }

//     // ── GET /admin/stock-transfers/{transfer} ────────────────────
//     public function show(StockTransfer $transfer): View
//     {
//         $transfer->load([
//             'fromWarehouse:id,name,code',
//             'toWarehouse:id,name,code',
//             'requestedBy:id,name',
//             'confirmedBy:id,name',
//             'approvedBy:id,name',
//             'cancelledBy:id,name',
//             'receivedBy:id,name',
//             'discrepancyReportedBy:id,name',
//             'resolvedBy:id,name',
//             'items.product:id,name,sku,unit',
//         ]);

//         return view('admin.stock-transfers.show', compact('transfer'));
//     }

//     // ── POST /admin/stock-transfers/{transfer}/confirm ───────────
//     public function confirm(StockTransfer $transfer): RedirectResponse
//     {
//         $user = auth()->user();

//         if ($transfer->status !== 'pending_confirmation') {
//             return back()->with('error', 'Hanya transfer pending_confirmation yang bisa dikonfirmasi.');
//         }
//         if ((int) $transfer->requested_by !== (int) $user->id) {
//             return back()->with('error', 'Hanya pembuat request yang bisa konfirmasi.');
//         }

//         $transfer->update([
//             'status'       => 'pending_approval',
//             'confirmed_by' => $user->id,
//             'confirmed_at' => now(),
//         ]);

//         return back()->with('success', 'Transfer dikonfirmasi, menunggu approval superadmin.');
//     }

//     // ── POST /admin/stock-transfers/{transfer}/cancel ────────────
//     public function cancel(Request $request, StockTransfer $transfer): RedirectResponse
//     {
//         $user = auth()->user();

//         if ($transfer->status !== 'pending_confirmation') {
//             return back()->with('error', 'Hanya transfer pending_confirmation yang bisa dibatalkan di tahap ini.');
//         }
//         if ((int) $transfer->requested_by !== (int) $user->id) {
//             return back()->with('error', 'Hanya pembuat request yang bisa membatalkan.');
//         }

//         $validator = Validator::make($request->all(), ['cancel_reason' => 'required|string']);
//         if ($validator->fails()) {
//             return back()->withErrors($validator)->with('error', 'Alasan pembatalan wajib diisi.');
//         }

//         $transfer->update([
//             'status'        => 'cancelled',
//             'cancelled_by'  => $user->id,
//             'cancelled_at'  => now(),
//             'cancel_reason' => $request->cancel_reason,
//         ]);

//         return redirect()->route('admin.stock-transfers.index')->with('success', 'Transfer dibatalkan.');
//     }

//     // ── POST /admin/stock-transfers/{transfer}/send ──────────────
//     public function send(Request $request, StockTransfer $transfer): RedirectResponse
//     {
//         $user = auth()->user();

//         if ($transfer->status !== 'approved') {
//             return back()->with('error', 'Hanya transfer yang sudah disetujui yang dapat dikirim.');
//         }
//         if ((int) $user->warehouse_id !== (int) $transfer->from_warehouse_id) {
//             return back()->with('error', 'Hanya admin gudang asal yang bisa mengirim.');
//         }

//         // Form Blade mengirim items sebagai array langsung (bukan JSON string
//         // seperti di API multipart) — jadi tidak perlu json_decode di sini.
//         $validator = Validator::make($request->all(), [
//             'items'                          => 'required|array|min:1',
//             'items.*.stock_transfer_item_id' => 'required|exists:stock_transfer_items,id',
//             'items.*.quantity_sent'          => 'required|integer|min:1',
//             'attachment'                     => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
//         ]);

//         if ($validator->fails()) {
//             return back()->withErrors($validator)->withInput();
//         }

//         $path = $request->file('attachment')->store('transfer-shipments', 'public');

//         try {
//             DB::transaction(function () use ($request, $transfer, $user, $path) {
//                 foreach ($request->items as $item) {
//                     $transferItem = StockTransferItem::find($item['stock_transfer_item_id']);
//                     if (! $transferItem || $transferItem->stock_transfer_id !== $transfer->id) continue;

//                     $qtySent = min($item['quantity_sent'], $transferItem->quantity_requested);

//                     $stock = Stock::where('warehouse_id', $transfer->from_warehouse_id)
//                         ->where('product_id', $transferItem->product_id)
//                         ->first();

//                     if (! $stock) {
//                         throw new \RuntimeException("Stok untuk produk \"{$transferItem->product->name}\" tidak ditemukan di gudang asal.");
//                     }
//                     if ($stock->quantity < $qtySent) {
//                         throw new \RuntimeException("Stok \"{$transferItem->product->name}\" tidak cukup. Tersedia: {$stock->quantity}, diminta kirim: {$qtySent}.");
//                     }

//                     $before = $stock->quantity;
//                     $stock->reduceStock($qtySent);

//                     StockMovement::create([
//                         'product_id'      => $transferItem->product_id,
//                         'warehouse_id'    => $transfer->from_warehouse_id,
//                         'type'            => 'transfer_out',
//                         'quantity'        => $qtySent,
//                         'quantity_before' => $before,
//                         'quantity_after'  => $stock->quantity,
//                         'reference_type'  => 'stock_transfer',
//                         'reference_id'    => $transfer->id,
//                         'created_by'      => $user->id,
//                         'note'            => "Pengiriman transfer #{$transfer->transfer_number}",
//                     ]);

//                     $transferItem->update(['quantity_sent' => $qtySent]);
//                 }

//                 $transfer->update([
//                     'status'              => 'in_transit',
//                     'sent_at'             => now(),
//                     'sent_by'             => $user->id,
//                     'shipment_attachment' => $path,
//                 ]);
//             });
//         } catch (\RuntimeException $e) {
//             Storage::disk('public')->delete($path);
//             return back()->with('error', $e->getMessage())->withInput();
//         }

//         return redirect()->route('admin.stock-transfers.show', $transfer)->with('success', 'Barang berhasil dikirim.');
//     }

//     // ── POST /admin/stock-transfers/{transfer}/checklist ─────────
//     public function checklist(Request $request, StockTransfer $transfer): RedirectResponse
//     {
//         $user = auth()->user();

//         if ($transfer->status !== 'in_transit') {
//             return back()->with('error', 'Hanya transfer in_transit yang dapat divalidasi.');
//         }
//         if ((int) $user->warehouse_id !== (int) $transfer->to_warehouse_id) {
//             return back()->with('error', 'Hanya admin gudang tujuan yang bisa checklist.');
//         }

//         $validator = Validator::make($request->all(), [
//             'items'                          => 'required|array|min:1',
//             'items.*.stock_transfer_item_id' => 'required|exists:stock_transfer_items,id',
//             'items.*.quantity_received'      => 'required|integer|min:0',
//             'discrepancy_notes'              => 'required_if:has_discrepancy,true|nullable|string',
//         ]);

//         if ($validator->fails()) {
//             return back()->withErrors($validator)->withInput();
//         }

//         $hasDiscrepancy = false;

//         DB::transaction(function () use ($request, $transfer, $user, &$hasDiscrepancy) {
//             foreach ($request->items as $item) {
//                 $transferItem = StockTransferItem::find($item['stock_transfer_item_id']);
//                 if (! $transferItem || $transferItem->stock_transfer_id !== $transfer->id) continue;

//                 $qtyReceived = $item['quantity_received'];
//                 $isMatched   = $qtyReceived === $transferItem->quantity_sent;
//                 if (! $isMatched) $hasDiscrepancy = true;

//                 $stock = Stock::firstOrCreate(
//                     ['warehouse_id' => $transfer->to_warehouse_id, 'product_id' => $transferItem->product_id],
//                     ['quantity' => 0]
//                 );

//                 $before = $stock->quantity;
//                 if ($qtyReceived > 0) $stock->addStock($qtyReceived);

//                 StockMovement::create([
//                     'product_id'      => $transferItem->product_id,
//                     'warehouse_id'    => $transfer->to_warehouse_id,
//                     'type'            => 'transfer_in',
//                     'quantity'        => $qtyReceived,
//                     'quantity_before' => $before,
//                     'quantity_after'  => $stock->quantity,
//                     'reference_type'  => 'stock_transfer',
//                     'reference_id'    => $transfer->id,
//                     'created_by'      => $user->id,
//                     'note'            => "Penerimaan transfer #{$transfer->transfer_number}",
//                 ]);

//                 $transferItem->update([
//                     'quantity_received' => $qtyReceived,
//                     'is_matched'        => $isMatched,
//                 ]);
//             }

//             if ($hasDiscrepancy) {
//                 $transfer->update([
//                     'status'                  => 'discrepancy',
//                     'discrepancy_notes'       => $request->discrepancy_notes,
//                     'discrepancy_reported_by' => $user->id,
//                     'discrepancy_reported_at' => now(),
//                 ]);
//             } else {
//                 $transfer->update([
//                     'status'      => 'received',
//                     'received_by' => $user->id,
//                     'received_at' => now(),
//                 ]);
//             }
//         });

//         $msg = $hasDiscrepancy
//             ? 'Ada selisih barang, menunggu resolusi superadmin.'
//             : 'Barang diterima sesuai, transfer selesai.';

//         return redirect()->route('admin.stock-transfers.show', $transfer)->with('success', $msg);
//     }
// }
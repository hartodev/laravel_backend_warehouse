<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Request as StockRequest;
use App\Models\RequestItem;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Http\Controllers\Api\User\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    public function index(Request $request)
    {
        $requests = StockRequest::with(['user:id,name', 'items.product:id,name,sku,unit', 'adminVerifiedBy:id,name'])
            ->when($request->status, fn($q) => $q->where('status', $request->status), fn($q) => $q->where('status', 'pending_superadmin'))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $warehouses = Warehouse::orderBy('name')->get(['id', 'name']);

        return view('superadmin.request.index', compact('requests', 'warehouses'));
    }

    public function show(StockRequest $request)
    {
        $request->load(['user:id,name', 'items.product:id,name,sku,unit', 'adminVerifiedBy:id,name', 'approvedBy:id,name']);
        $warehouses = Warehouse::orderBy('name')->get(['id', 'name']);

        return view('superadmin.request.show', compact('request', 'warehouses'));
    }

    // Approve final — potong stok di sini
    public function approveFinal(Request $httpRequest, StockRequest $request)
    {
        if (! $request->isPendingSuperadmin()) {
            return back()->with('error', 'Hanya permintaan yang sudah diverifikasi Admin yang dapat disetujui final.');
        }

        $httpRequest->validate([
            'warehouse_id'               => 'required|exists:warehouses,id',
            'items'                      => 'required|array',
            'items.*.request_item_id'    => 'required|exists:request_items,id',
            'items.*.approved_quantity'  => 'required|integer|min:0',
        ]);

        $stockErrors = [];
        $stockMap = [];

        foreach ($httpRequest->items as $item) {
            $approvedQty = (int) $item['approved_quantity'];
            if ($approvedQty <= 0) continue;

            $requestItem = RequestItem::find($item['request_item_id']);
            if (! $requestItem || $requestItem->request_id !== $request->id || ! $requestItem->product_id) {
                continue;
            }

            $stock = Stock::where('warehouse_id', $httpRequest->warehouse_id)
                ->where('product_id', $requestItem->product_id)
                ->first();

            $namaProduk = $requestItem->product->name ?? "produk #{$requestItem->product_id}";

            if (! $stock) {
                $stockErrors[] = "Stok '{$namaProduk}' tidak ditemukan di gudang yang dipilih.";
                continue;
            }
            if ($stock->quantity < $approvedQty) {
                $stockErrors[] = "Stok '{$namaProduk}' tidak cukup (tersedia {$stock->quantity}, diminta {$approvedQty}).";
                continue;
            }

            $stockMap[$requestItem->id] = $stock;
        }

        if (! empty($stockErrors)) {
            return back()->with('error', implode(' ', $stockErrors))->withInput();
        }

        DB::transaction(function () use ($httpRequest, $request, $stockMap) {
            $totalApproved = 0;

            foreach ($httpRequest->items as $item) {
                $requestItem = RequestItem::find($item['request_item_id']);
                if (! $requestItem || $requestItem->request_id !== $request->id) continue;

                $approvedQty = (int) $item['approved_quantity'];
                $requestItem->update(['approved_quantity' => $approvedQty]);

                if ($approvedQty <= 0 || ! isset($stockMap[$requestItem->id])) continue;

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
                'approve_final',
                'Request',
                $request->id,
                "Permintaan barang #{$request->request_number} disetujui final oleh Super Admin — {$totalApproved} unit dikeluarkan",
                ['status' => 'pending_superadmin'],
                ['status' => 'approved', 'warehouse_id' => $httpRequest->warehouse_id, 'total_approved_qty' => $totalApproved]
            );

            NotificationController::send(
                $request->user_id,
                'request_approved',
                'Request Disetujui',
                "Request #{$request->request_number} Anda telah disetujui final dan stok telah dikeluarkan.",
                ['request_id' => $request->id]
            );
        });

        return redirect()->route('requests.index')->with('success', 'Permintaan disetujui final dan stok telah dikeluarkan.');
    }

    public function reject(Request $httpRequest, StockRequest $request)
    {
        if (! in_array($request->status, ['pending', 'pending_superadmin'])) {
            return back()->with('error', 'Permintaan ini tidak dapat ditolak.');
        }

        $httpRequest->validate(['reject_reason' => 'required|string']);

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
            "Permintaan barang #{$request->request_number} ditolak Super Admin — alasan: {$httpRequest->reject_reason}",
            ['status' => $request->getOriginal('status')],
            ['status' => 'rejected', 'reject_reason' => $httpRequest->reject_reason]
        );

        NotificationController::send(
            $request->user_id,
            'request_rejected',
            'Request Ditolak',
            "Request #{$request->request_number} ditolak. Alasan: {$httpRequest->reject_reason}",
            ['request_id' => $request->id, 'reason' => $httpRequest->reject_reason]
        );

        return redirect()->route('requests.index')->with('success', 'Permintaan ditolak.');
    }
}



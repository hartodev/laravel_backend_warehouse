<?php

namespace App\Http\Controllers\Web\Admin;

use App\Exceptions\StockTransferException;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use App\Services\StockTransferService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Panel web BARU untuk role 'admin' (admin gudang).
 * Scope-nya setara Api\Admin\StockTransferController (dipakai Android) —
 * BUKAN setara Web\Superadmin (yang punya wewenang approve/reject/resolve).
 *
 * File ini BERDIRI SENDIRI: tidak mengubah, tidak dipakai oleh, dan tidak
 * dipakai oleh Api\Admin\StockTransferController maupun
 * Web\Superadmin\StockTransferController yang sudah ada. Logic di
 * StockTransferService (lihat docblock-nya) sudah disamakan manual dengan
 * Api\Admin per hari ini dibuat.
 */
class StockTransferController extends Controller
{
    public function __construct(protected StockTransferService $service)
    {
    }

    public function index(Request $request): View
    {
        $user = auth()->user();

        $transfers = StockTransfer::with(['fromWarehouse:id,name,code', 'toWarehouse:id,name,code', 'requestedBy:id,name'])
            ->where(function ($q) use ($user) {
                $q->where('from_warehouse_id', $user->warehouse_id)
                  ->orWhere('to_warehouse_id', $user->warehouse_id);
            })
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('Admin.stock_transfer.index', compact('transfers'));
    }

    public function show(StockTransfer $stockTransfer): View
    {
        $stockTransfer->load([
            'fromWarehouse:id,name,code',
            'toWarehouse:id,name,code',
            'requestedBy:id,name',
            'confirmedBy:id,name',
            'approvedBy:id,name',
            'cancelledBy:id,name',
            'receivedBy:id,name',
            'discrepancyReportedBy:id,name',
            'resolvedBy:id,name',
            'items.product:id,name,sku,unit',
        ]);

        return view('Admin.stock_transfer.show', compact('stockTransfer'));
    }

    public function create(): View
    {
        $user = auth()->user();

        $warehouses = Warehouse::where('is_active', true)
            ->where('id', '!=', $user->warehouse_id)
            ->get(['id', 'name']);

        $products = Product::where('is_active', true)->get(['id', 'name', 'sku', 'unit']);

        return view('Admin.stock_transfer.create', compact('warehouses', 'products'));
    }

    // ── 1. STORE — dari gudang milik user yang login ────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'to_warehouse_id'            => 'required|exists:warehouses,id',
            'transfer_date'              => 'required|date',
            'expected_arrival'           => 'nullable|date|after_or_equal:transfer_date',
            'notes'                      => 'nullable|string',
            'items'                      => 'required|array|min:1',
            'items.*.product_id'         => 'required|exists:products,id',
            'items.*.quantity_requested' => 'required|integer|min:1',
        ]);

        // Gudang asal dipaksa dari akun yang login, bukan input form —
        // supaya admin tidak bisa mengajukan transfer atas nama gudang lain.
        $validated['from_warehouse_id'] = auth()->user()->warehouse_id;

        try {
            $transfer = $this->service->createRequest($validated, auth()->user());
        } catch (StockTransferException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('admin.stock-transfers.show', $transfer)
            ->with('success', 'Request transfer dibuat, silakan konfirmasi untuk lanjut.');
    }

    // ── 2a. CONFIRM ───────────────────────────────────────────────────────
    public function confirm(StockTransfer $stockTransfer): RedirectResponse
    {
        try {
            $this->service->confirm($stockTransfer, auth()->user());
        } catch (StockTransferException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Transfer dikonfirmasi, menunggu approval superadmin.');
    }

    // ── 2b. CANCEL ────────────────────────────────────────────────────────
    public function cancel(Request $request, StockTransfer $stockTransfer): RedirectResponse
    {
        $request->validate(['cancel_reason' => 'required|string']);

        try {
            $this->service->cancel($stockTransfer, auth()->user(), $request->cancel_reason);
        } catch (StockTransferException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.stock-transfers.index')->with('success', 'Transfer dibatalkan.');
    }

    // ── 4. SEND — hanya admin gudang asal ────────────────────────────────
    public function send(Request $request, StockTransfer $stockTransfer): RedirectResponse
    {
        $validated = $request->validate([
            'items'                          => 'required|array|min:1',
            'items.*.stock_transfer_item_id' => 'required|exists:stock_transfer_items,id',
            'items.*.quantity_sent'          => 'required|integer|min:1',
            'attachment'                     => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $path = $request->file('attachment')->store('transfer-shipments', 'public');

        try {
            $this->service->send($stockTransfer, auth()->user(), $validated['items'], $path);
        } catch (StockTransferException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.stock-transfers.show', $stockTransfer)
            ->with('success', 'Barang berhasil dikirim.');
    }

    // ── 5. CHECKLIST — hanya admin gudang tujuan ─────────────────────────
    public function checklist(Request $request, StockTransfer $stockTransfer): RedirectResponse
    {
        $validated = $request->validate([
            'items'                          => 'required|array|min:1',
            'items.*.stock_transfer_item_id' => 'required|exists:stock_transfer_items,id',
            'items.*.quantity_received'      => 'required|integer|min:0',
            'discrepancy_notes'              => 'required_if:has_discrepancy,true|nullable|string',
        ]);

        try {
            $result = $this->service->checklist(
                $stockTransfer,
                auth()->user(),
                $validated['items'],
                $validated['discrepancy_notes'] ?? null
            );
        } catch (StockTransferException $e) {
            return back()->with('error', $e->getMessage());
        }

        $msg = $result['has_discrepancy']
            ? 'Ada selisih barang, menunggu resolusi superadmin.'
            : 'Barang diterima sesuai, transfer selesai.';

        return redirect()->route('admin.stock-transfers.show', $stockTransfer)->with('success', $msg);
    }
}

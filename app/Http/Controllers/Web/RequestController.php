<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ItemRequest;
use App\Models\RequestItem;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index(Request $request)
    {
        $requests = RequestItem::with(['user:id,name', 'warehouse:id,name', 'product:id,name,sku'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->warehouse_id, fn($q) => $q->where('warehouse_id', $request->warehouse_id))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('superadmin.request.index', compact('requests'));
    }

    public function show(ItemRequest $itemRequest)
    {
        $itemRequest->load(['user:id,name', 'warehouse:id,name', 'product:id,name,sku,unit', 'approvedBy:id,name']);
        return view('superadmin.request.show', compact('itemRequest'));
    }

    public function approve(Request $request, ItemRequest $itemRequest)
    {
        if ($itemRequest->status !== 'pending') {
            return back()->with('error', 'Hanya request pending yang dapat disetujui.');
        }

        $itemRequest->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'catatan'     => $request->catatan,
        ]);

        return back()->with('success', 'Request barang disetujui.');
    }

    public function reject(Request $request, ItemRequest $itemRequest)
    {
        $request->validate(['reject_reason' => 'required|string']);

        $itemRequest->update([
            'status'        => 'rejected',
            'approved_by'   => auth()->id(),
            'approved_at'   => now(),
            'reject_reason' => $request->reject_reason,
        ]);

        return back()->with('success', 'Request barang ditolak.');
    }

    public function complete(ItemRequest $itemRequest)
    {
        if ($itemRequest->status !== 'approved') {
            return back()->with('error', 'Hanya request approved yang dapat diselesaikan.');
        }

        $itemRequest->update(['status' => 'completed', 'completed_at' => now()]);

        return back()->with('success', 'Request barang selesai.');
    }
}

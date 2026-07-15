@extends('layouts.app')
@section('title', $stockTransfer->transfer_number)
@section('breadcrumb')
<a href="{{ route('stock-transfers.index') }}" class="hover:text-primary-700">Transfer Stok</a>
<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-700 font-medium">{{ $stockTransfer->transfer_number }}</span>
@endsection

@section('content')
<div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-3 flex-wrap">
            <h1 class="text-xl font-bold text-gray-900">{{ $stockTransfer->transfer_number }}</h1>
            <x-status-badge :status="$stockTransfer->status" />
        </div>
        <p class="text-sm text-gray-500 mt-0.5">Diajukan oleh: <strong>{{ $stockTransfer->requestedBy->name ?? '—' }}</strong> · {{ $stockTransfer->created_at->isoFormat('D MMM Y, HH:mm') }}</p>
    </div>
    <div class="flex flex-wrap gap-2">
    @if($stockTransfer->status === 'pending_approval')
    <form method="POST" action="{{ route('stock-transfers.approve', $stockTransfer) }}" class="inline">@csrf<button class="btn-success btn">Setujui</button></form>
    <button onclick="document.getElementById('reject-modal').classList.remove('hidden')" class="btn-danger btn">Tolak</button>
@elseif($stockTransfer->status === 'discrepancy')
    <button onclick="document.getElementById('resolve-modal').classList.remove('hidden')" class="btn-primary btn">Resolusi Selisih</button>
@endif
    </div>
</div>

{{-- Info tambahan sesuai status --}}
@if($stockTransfer->status === 'cancelled' && $stockTransfer->cancel_reason)
<div class="card p-4 mb-5 bg-red-50 border-red-100">
    <p class="text-sm font-semibold text-red-700 mb-1">Dibatalkan oleh Admin Gudang Asal</p>
    <p class="text-sm text-red-600">{{ $stockTransfer->cancel_reason }}</p>
    <p class="text-xs text-red-400 mt-1">Oleh {{ $stockTransfer->cancelledBy->name ?? '—' }} · {{ $stockTransfer->cancelled_at?->isoFormat('D MMM Y, HH:mm') }}</p>
</div>
@endif

@if($stockTransfer->status === 'rejected' && $stockTransfer->reject_reason)
<div class="card p-4 mb-5 bg-red-50 border-red-100">
    <p class="text-sm font-semibold text-red-700 mb-1">Alasan Penolakan</p>
    <p class="text-sm text-red-600">{{ $stockTransfer->reject_reason }}</p>
</div>
@endif

@if($stockTransfer->status === 'discrepancy' && $stockTransfer->discrepancy_notes)
<div class="card p-4 mb-5 bg-yellow-50 border-yellow-100">
    <p class="text-sm font-semibold text-yellow-700 mb-1">Selisih Dilaporkan Admin Gudang Tujuan</p>
    <p class="text-sm text-yellow-700">{{ $stockTransfer->discrepancy_notes }}</p>
    <p class="text-xs text-yellow-500 mt-1">Oleh {{ $stockTransfer->discrepancyReportedBy->name ?? '—' }} · {{ $stockTransfer->discrepancy_reported_at?->isoFormat('D MMM Y, HH:mm') }}</p>
</div>
@endif

@if($stockTransfer->resolution_notes)
<div class="card p-4 mb-5 bg-blue-50 border-blue-100">
    <p class="text-sm font-semibold text-blue-700 mb-1">Resolusi Superadmin</p>
    <p class="text-sm text-blue-600">{{ $stockTransfer->resolution_notes }}</p>
    <p class="text-xs text-blue-400 mt-1">Oleh {{ $stockTransfer->resolvedBy->name ?? '—' }} · {{ $stockTransfer->resolved_at?->isoFormat('D MMM Y, HH:mm') }}</p>
</div>
@endif

@if($stockTransfer->shipment_attachment)
<div class="card p-4 mb-5">
    <p class="text-sm font-semibold text-gray-700 mb-2">Lampiran Bukti Pengiriman</p>
    <a href="{{ Storage::url($stockTransfer->shipment_attachment) }}" target="_blank" class="btn btn-secondary btn-sm">
        Lihat Lampiran
    </a>
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
    <div class="card p-5">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Dari Gudang</p>
        <p class="font-bold text-gray-900">{{ $stockTransfer->fromWarehouse->name ?? '—' }}</p>
        <p class="text-sm text-gray-500">{{ $stockTransfer->fromWarehouse->code ?? '' }}</p>
    </div>
    <div class="card p-5 flex flex-col items-center justify-center text-gray-300">
        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        <p class="text-xs text-gray-400 mt-1">Transfer</p>
    </div>
    <div class="card p-5">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Ke Gudang</p>
        <p class="font-bold text-gray-900">{{ $stockTransfer->toWarehouse->name ?? '—' }}</p>
        <p class="text-sm text-gray-500">{{ $stockTransfer->toWarehouse->code ?? '' }}</p>
    </div>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5 text-sm">
    <div class="card p-4"><p class="text-gray-400 text-xs mb-1">Tgl. Transfer</p><p class="font-medium">{{ \Carbon\Carbon::parse($stockTransfer->transfer_date)->isoFormat('D MMM Y') }}</p></div>
    <div class="card p-4"><p class="text-gray-400 text-xs mb-1">Est. Tiba</p><p class="font-medium">{{ $stockTransfer->expected_arrival ? \Carbon\Carbon::parse($stockTransfer->expected_arrival)->isoFormat('D MMM Y') : '—' }}</p></div>
    <div class="card p-4"><p class="text-gray-400 text-xs mb-1">Dikirim</p><p class="font-medium">{{ $stockTransfer->sent_at ? \Carbon\Carbon::parse($stockTransfer->sent_at)->isoFormat('D MMM Y') : '—' }}</p></div>
    <div class="card p-4"><p class="text-gray-400 text-xs mb-1">Diterima</p><p class="font-medium">{{ $stockTransfer->received_at ? \Carbon\Carbon::parse($stockTransfer->received_at)->isoFormat('D MMM Y') : '—' }}</p></div>
</div>

<div class="card">
    <div class="card-header"><h3 class="font-semibold text-gray-900">Item Transfer</h3></div>
    <div class="table-wrap rounded-none border-0">
        <table class="data-table">
            <thead><tr><th>#</th><th>Produk</th><th>SKU</th><th class="text-right">Diminta</th><th class="text-right">Dikirim</th><th class="text-right">Diterima</th><th class="text-center">Cocok?</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($stockTransfer->items as $i => $item)
                <tr>
                    <td class="text-gray-400 text-xs">{{ $i+1 }}</td>
                    <td class="font-medium">{{ $item->product->name ?? '—' }}</td>
                    <td class="font-mono text-xs text-gray-500">{{ $item->product->sku ?? '—' }}</td>
                    <td class="text-right">{{ number_format($item->quantity_requested) }}</td>
                    <td class="text-right {{ $item->quantity_sent < $item->quantity_requested ? 'text-yellow-600' : '' }}">{{ number_format($item->quantity_sent) }}</td>
                    <td class="text-right {{ $item->quantity_received < $item->quantity_sent ? 'text-yellow-600' : 'text-green-700' }}">{{ number_format($item->quantity_received) }}</td>
                    <td class="text-center">
                        @if(is_null($item->is_matched))
                            <span class="text-gray-300">—</span>
                        @elseif($item->is_matched)
                            <span class="text-green-600">✓</span>
                        @else
                            <span class="text-red-600">✗</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-8 text-gray-400">Tidak ada item</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($stockTransfer->notes)
    <div class="card-body border-t text-sm text-gray-600"><strong>Catatan:</strong> {{ $stockTransfer->notes }}</div>
    @endif
</div>

{{-- Reject Modal --}}
<div id="reject-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
        <div class="px-6 py-5 border-b"><h3 class="font-semibold">Tolak Transfer</h3></div>
        <form method="POST" action="{{ route('stock-transfers.reject', $stockTransfer) }}">
            @csrf
            <div class="px-6 py-4 space-y-3">
                <textarea name="reject_reason" rows="3" required class="form-textarea" placeholder="Alasan penolakan..."></textarea>
            </div>
            <div class="px-6 py-4 border-t flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('reject-modal').classList.add('hidden')" class="btn-secondary btn">Batal</button>
                <button type="submit" class="btn-danger btn">Tolak</button>
            </div>
        </form>
    </div>
</div>

{{-- Resolve Discrepancy Modal --}}
<div id="resolve-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
        <div class="px-6 py-5 border-b"><h3 class="font-semibold">Resolusi Selisih Barang</h3></div>
        <form method="POST" action="{{ route('stock-transfers.resolve-discrepancy', $stockTransfer) }}">
            @csrf
            <div class="px-6 py-4 space-y-3">
                <div>
                    <label class="form-label">Keputusan</label>
                    <div class="flex gap-4 mt-1">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="radio" name="resolution" value="accept" required>
                            Terima selisih (tandai selesai)
                        </label>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="radio" name="resolution" value="cancel" required>
                            Batalkan transfer
                        </label>
                    </div>
                </div>
                <div>
                    <label class="form-label">Catatan Resolusi <span class="text-red-500">*</span></label>
                    <textarea name="notes" rows="3" required class="form-textarea" placeholder="Jelaskan keputusan..."></textarea>
                </div>
            </div>
            <div class="px-6 py-4 border-t flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('resolve-modal').classList.add('hidden')" class="btn-secondary btn">Batal</button>
                <button type="submit" class="btn-primary btn">Simpan Resolusi</button>
            </div>
        </form>
    </div>
</div>
@endsection



@extends('layouts.app')
@section('title', $purchaseOrder->po_number)
@section('breadcrumb')
    <a href="{{ route('purchase-orders.index') }}" class="hover:text-primary-700">Purchase Order</a>
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-700 font-medium">{{ $purchaseOrder->po_number }}</span>
@endsection

@section('content')
<div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-3 flex-wrap">
            <h1 class="text-xl font-bold text-gray-900">{{ $purchaseOrder->po_number }}</h1>
            <x-status-badge :status="$purchaseOrder->status" />
        </div>
        <p class="text-sm text-gray-500 mt-0.5">
            Dibuat oleh: <strong>{{ $purchaseOrder->createdBy->name ?? '—' }}</strong> ·
            {{ $purchaseOrder->created_at->isoFormat('D MMMM Y, HH:mm') }}
        </p>
    </div>
    <div class="flex flex-wrap gap-2 print:hidden">
        @if($purchaseOrder->status === 'draft')
            <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="btn-secondary btn">Edit</a>
            <button onclick="document.getElementById('delete-po').classList.remove('hidden')" class="btn-danger btn">Hapus</button>
        @elseif($purchaseOrder->status === 'pending')
            <form method="POST" action="{{ route('purchase-orders.approve', $purchaseOrder) }}" class="inline">
                @csrf
                <button type="submit" class="btn-success btn">Setujui PO</button>
            </form>
            <button onclick="document.getElementById('reject-po').classList.remove('hidden')" class="btn-danger btn">Tolak</button>
        @elseif($purchaseOrder->status === 'approved')
            <form method="POST" action="{{ route('purchase-orders.receive', $purchaseOrder) }}" class="inline">
                @csrf
                <button type="submit" class="btn-primary btn">✓ Terima Semua Barang</button>
            </form>
        @endif
        <button onclick="window.print()" class="btn-secondary btn">🖨️ Print</button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
    {{-- Info supplier --}}
    <div class="card p-5">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3">Supplier</p>
        <p class="font-bold text-gray-900">{{ $purchaseOrder->supplier->name ?? '—' }}</p>
        @if($purchaseOrder->supplier?->phone)<p class="text-sm text-gray-500">{{ $purchaseOrder->supplier->phone }}</p>@endif
        @if($purchaseOrder->supplier?->address)<p class="text-sm text-gray-400 mt-1">{{ $purchaseOrder->supplier->address }}</p>@endif
    </div>
    {{-- Info gudang --}}
    <div class="card p-5">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3">Gudang Tujuan</p>
        <p class="font-bold text-gray-900">{{ $purchaseOrder->warehouse->name ?? '—' }}</p>
        <p class="text-sm text-gray-500">{{ $purchaseOrder->warehouse->location ?? '' }}</p>
    </div>
    {{-- Info tanggal --}}
    <div class="card p-5 space-y-2 text-sm">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Detail</p>
        <div class="flex justify-between"><span class="text-gray-500">Tgl. Order</span><span>{{ \Carbon\Carbon::parse($purchaseOrder->order_date)->isoFormat('D MMM Y') }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Tgl. Diharapkan</span><span>{{ $purchaseOrder->expected_date ? \Carbon\Carbon::parse($purchaseOrder->expected_date)->isoFormat('D MMM Y') : '—' }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Pembayaran</span><span>{{ $purchaseOrder->payment_method ? ucfirst($purchaseOrder->payment_method) : '—' }}</span></div>
        @if($purchaseOrder->approved_by)
        <div class="flex justify-between"><span class="text-gray-500">Disetujui</span><span>{{ $purchaseOrder->approvedBy->name }}</span></div>
        @endif
    </div>
</div>

{{-- Items --}}
<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-semibold text-gray-900">Item Produk</h3>
    </div>
    <div class="table-wrap rounded-none border-0">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Produk</th>
                    <th>SKU</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Harga Satuan</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($purchaseOrder->items as $i => $item)
                <tr>
                    <td class="text-gray-400 text-xs">{{ $i+1 }}</td>
                    <td class="font-medium">{{ $item->product->name ?? '—' }}</td>
                    <td class="font-mono text-xs text-gray-500">{{ $item->product->sku ?? '—' }}</td>
                    <td class="text-right">{{ number_format($item->quantity) }}</td>
                    <td class="text-right">Rp {{ number_format($item->price) }}</td>
                    <td class="text-right font-semibold">Rp {{ number_format($item->total) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="5" class="text-right px-4 py-3 text-sm text-gray-500">Subtotal</td>
                    <td class="text-right px-4 py-3 font-medium">Rp {{ number_format($purchaseOrder->subtotal) }}</td>
                </tr>
                @if($purchaseOrder->tax_amount > 0)
                <tr>
                    <td colspan="5" class="text-right px-4 py-3 text-sm text-gray-500">PPN ({{ $purchaseOrder->tax_percent }}%)</td>
                    <td class="text-right px-4 py-3">Rp {{ number_format($purchaseOrder->tax_amount) }}</td>
                </tr>
                @endif
                @if($purchaseOrder->discount_amount > 0)
                <tr>
                    <td colspan="5" class="text-right px-4 py-3 text-sm text-gray-500">Diskon</td>
                    <td class="text-right px-4 py-3 text-red-600">- Rp {{ number_format($purchaseOrder->discount_amount) }}</td>
                </tr>
                @endif
                <tr class="border-t-2 border-gray-200">
                    <td colspan="5" class="text-right px-4 py-3 font-bold">TOTAL</td>
                    <td class="text-right px-4 py-3 font-bold text-primary-700 text-base">Rp {{ number_format($purchaseOrder->total_amount) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@if($purchaseOrder->notes)
<div class="card p-5">
    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Catatan</p>
    <p class="text-sm text-gray-700">{{ $purchaseOrder->notes }}</p>
</div>
@endif

{{-- Modals --}}
<x-confirm-modal id="delete-po" title="Hapus PO?" :message="'PO '.$purchaseOrder->po_number.' akan dihapus permanen.'" :action="route('purchase-orders.destroy', $purchaseOrder)" method="DELETE" confirm-label="Hapus PO" />

<div id="reject-po" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
        <div class="px-6 py-5 border-b"><h3 class="font-semibold">Tolak Purchase Order</h3></div>
        <form method="POST" action="{{ route('purchase-orders.reject', $purchaseOrder) }}">
            @csrf
            <div class="px-6 py-4 space-y-3">
                <p class="text-sm text-gray-600">Berikan alasan penolakan PO ini.</p>
                <textarea name="reject_reason" rows="3" required class="form-textarea" placeholder="Alasan penolakan..."></textarea>
            </div>
            <div class="px-6 py-4 border-t flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('reject-po').classList.add('hidden')" class="btn-secondary btn">Batal</button>
                <button type="submit" class="btn-danger btn">Tolak PO</button>
            </div>
        </form>
    </div>
</div>
@endsection

{{-- sales_orders/show.blade.php --}}
@extends('layouts.app')
@section('title', $salesOrder->so_number)
@section('breadcrumb')
<a href="{{ route('sales-orders.index') }}" class="hover:text-primary-700">Sales Order</a>
<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-700 font-medium">{{ $salesOrder->so_number }}</span>
@endsection

@section('content')
<div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-3 flex-wrap">
            <h1 class="text-xl font-bold text-gray-900">{{ $salesOrder->so_number }}</h1>
            <x-status-badge :status="$salesOrder->status" />
        </div>
        <p class="text-sm text-gray-500 mt-0.5">Dibuat: <strong>{{ $salesOrder->createdBy->name ?? '—' }}</strong> · {{ $salesOrder->created_at->isoFormat('D MMMM Y') }}</p>
    </div>
    <div class="flex gap-2 print:hidden">
        @if($salesOrder->status === 'draft')
        <a href="{{ route('sales-orders.edit', $salesOrder) }}" class="btn-secondary btn">Edit</a>
        @endif
        <button onclick="window.print()" class="btn-secondary btn">🖨️ Print</button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
    <div class="card p-4 text-sm"><p class="text-xs text-gray-400 mb-2 font-semibold uppercase tracking-wider">Customer</p><p class="font-bold text-gray-900">{{ $salesOrder->customer_name }}</p><p class="text-gray-500 mt-1">{{ $salesOrder->customer_address ?? '—' }}</p></div>
    <div class="card p-4 text-sm"><p class="text-xs text-gray-400 mb-2 font-semibold uppercase tracking-wider">Gudang</p><p class="font-bold">{{ $salesOrder->warehouse->name ?? '—' }}</p></div>
    <div class="card p-4 text-sm space-y-1.5">
        <p class="text-xs text-gray-400 mb-1 font-semibold uppercase tracking-wider">Detail</p>
        <div class="flex justify-between"><span class="text-gray-500">Tgl. Order</span><span>{{ \Carbon\Carbon::parse($salesOrder->order_date)->isoFormat('D MMM Y') }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Jatuh Tempo</span><span>{{ $salesOrder->due_date ? \Carbon\Carbon::parse($salesOrder->due_date)->isoFormat('D MMM Y') : '—' }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Pembayaran</span><span>{{ $salesOrder->payment_method ? ucfirst($salesOrder->payment_method) : '—' }}</span></div>
    </div>
</div>

<div class="card mb-5">
    <div class="card-header"><h3 class="font-semibold">Item Produk</h3></div>
    <div class="table-wrap rounded-none border-0">
        <table class="data-table">
            <thead><tr><th>#</th><th>Produk</th><th>Deskripsi</th><th class="text-right">Qty</th><th class="text-right">Harga</th><th class="text-right">Subtotal</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($salesOrder->items as $i => $item)
                <tr>
                    <td class="text-gray-400 text-xs">{{ $i+1 }}</td>
                    <td class="font-medium">{{ $item->product->name ?? '—' }}</td>
                    <td class="text-gray-500">{{ $item->deskripsi ?? '—' }}</td>
                    <td class="text-right">{{ number_format($item->qty) }}</td>
                    <td class="text-right">Rp {{ number_format($item->harga) }}</td>
                    <td class="text-right font-semibold">Rp {{ number_format($item->total) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50">
                <tr><td colspan="5" class="text-right px-4 py-3 text-sm text-gray-500">Subtotal</td><td class="text-right px-4 py-3 font-medium">Rp {{ number_format($salesOrder->subtotal) }}</td></tr>
                @if($salesOrder->tax_amount > 0)<tr><td colspan="5" class="text-right px-4 py-3 text-sm text-gray-500">PPN ({{ $salesOrder->tax_percent }}%)</td><td class="text-right px-4 py-3">Rp {{ number_format($salesOrder->tax_amount) }}</td></tr>@endif
                @if($salesOrder->discount_amount > 0)<tr><td colspan="5" class="text-right px-4 py-3 text-sm text-gray-500">Diskon</td><td class="text-right px-4 py-3 text-red-600">- Rp {{ number_format($salesOrder->discount_amount) }}</td></tr>@endif
                <tr class="border-t-2 border-gray-200"><td colspan="5" class="text-right px-4 py-3 font-bold">TOTAL</td><td class="text-right px-4 py-3 font-bold text-primary-700 text-lg">Rp {{ number_format($salesOrder->total_amount) }}</td></tr>
            </tfoot>
        </table>
    </div>
    @if($salesOrder->keterangan)<div class="card-body border-t text-sm text-gray-600"><strong>Keterangan:</strong> {{ $salesOrder->keterangan }}</div>@endif
</div>
@endsection

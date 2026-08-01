@extends('layouts.admin')
@section('title', 'Detail Sales Order')
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900 font-mono">{{ $salesOrder->so_number }}</h1>
        <p class="text-sm text-gray-500">Dibuat {{ $salesOrder->created_at->format('d M Y, H:i') }}</p>
    </div>
    <x-status-badge :status="$salesOrder->status" />
</div>

@if(session('success'))
<div class="mb-4 p-3 rounded-lg bg-green-50 text-green-700 text-sm">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
    <div class="card p-4">
        <p class="text-xs text-gray-500 mb-1">Customer</p>
        <p class="text-sm font-semibold text-gray-900">{{ $salesOrder->customer_name }}</p>
    </div>
    <div class="card p-4">
        <p class="text-xs text-gray-500 mb-1">Gudang</p>
        <p class="text-sm font-semibold text-gray-900">{{ $salesOrder->warehouse->name ?? '-' }}</p>
    </div>
    <div class="card p-4">
        <p class="text-xs text-gray-500 mb-1">Total</p>
        <p class="text-sm font-semibold text-gray-900">Rp {{ number_format($salesOrder->total_amount) }}</p>
    </div>
</div>

<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-semibold text-gray-900">Item Pesanan</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Produk</th>
                    <th class="px-4 py-3 text-right">Qty</th>
                    <th class="px-4 py-3 text-right">Harga</th>
                    <th class="px-4 py-3 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($salesOrder->items as $item)
                <tr>
                    <td class="px-4 py-3">{{ $item->product->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-right">{{ $item->quantity }}</td>
                    <td class="px-4 py-3 text-right">Rp {{ number_format($item->price) }}</td>
                    <td class="px-4 py-3 text-right font-medium">Rp {{ number_format($item->quantity * $item->price) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-gray-400">Tidak ada item</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="bg-gray-50 font-semibold">
                    <td colspan="3" class="px-4 py-3 text-right">Total</td>
                    <td class="px-4 py-3 text-right">Rp {{ number_format($salesOrder->total_amount) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@if($salesOrder->notes)
<div class="card p-4 mb-5">
    <p class="text-xs text-gray-500 mb-1">Catatan</p>
    <p class="text-sm text-gray-700">{{ $salesOrder->notes }}</p>
</div>
@endif

<div class="flex justify-between items-center">
    <a href="{{ route('admin.sales-orders.index') }}" class="btn btn-secondary">← Kembali</a>

    @if($salesOrder->status === 'pending')
    <div class="flex gap-2">
        <form action="{{ route('admin.sales-orders.reject', $salesOrder) }}" method="POST"
            onsubmit="return confirm('Tolak sales order ini?')">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-danger">Tolak</button>
        </form>
        <form action="{{ route('admin.sales-orders.approve', $salesOrder) }}" method="POST">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-primary">Setujui</button>
        </form>
    </div>
    @endif
</div>
@endsection
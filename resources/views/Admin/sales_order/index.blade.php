@extends('layouts.admin')
@section('title', 'Sales Order')
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Sales Order</h1>
        <p class="text-sm text-gray-500">Daftar pesanan penjualan.</p>
    </div>
    <a href="{{ route('admin.sales-orders.create') }}" class="btn btn-primary">+ Buat SO</a>
</div>

@if(session('success'))
<div class="mb-4 p-3 rounded-lg bg-green-50 text-green-700 text-sm">{{ session('success') }}</div>
@endif

{{-- Filter --}}
<form method="GET" class="card mb-4 p-4 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs text-gray-500 mb-1">Cari No. SO</label>
        <input type="text" name="search" value="{{ request('search') }}"
            class="rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500"
            placeholder="SO-0001">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Status</label>
        <select name="status"
            class="rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
            <option value="">Semua</option>
            <option value="pending" @selected(request('status')==='pending' )>Pending</option>
            <option value="approved" @selected(request('status')==='approved' )>Approved</option>
            <option value="completed" @selected(request('status')==='completed' )>Completed</option>
            <option value="cancelled" @selected(request('status')==='cancelled' )>Cancelled</option>
        </select>
    </div>
    <button type="submit" class="btn btn-secondary">Filter</button>
</form>

<div class="card">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">No. SO</th>
                    <th class="px-4 py-3 text-left">Customer</th>
                    <th class="px-4 py-3 text-left">Gudang</th>
                    <th class="px-4 py-3 text-left">Tanggal</th>
                    <th class="px-4 py-3 text-right">Total</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($salesOrders as $so)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-primary-700">
                        <a href="{{ route('admin.sales-orders.show', $so) }}"
                            class="hover:underline">{{ $so->so_number }}</a>
                    </td>
                    <td class="px-4 py-3">{{ $so->customer_name }}</td>
                    <td class="px-4 py-3">{{ $so->warehouse->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $so->created_at->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($so->total_amount) }}</td>
                    <td class="px-4 py-3">
                        <x-status-badge :status="$so->status" />
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.sales-orders.show', $so) }}"
                            class="text-primary-700 hover:underline text-xs">Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">Belum ada sales order</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($salesOrders, 'links'))
    <div class="p-4">{{ $salesOrders->appends(request()->query())->links() }}</div>
    @endif
</div>
@endsection
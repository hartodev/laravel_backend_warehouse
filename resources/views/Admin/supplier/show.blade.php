@extends('layouts.admin')
@section('title', 'Detail Supplier')
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">{{ $supplier->name }}</h1>
        <p class="text-sm text-gray-500 font-mono">{{ $supplier->code ?? '-' }}</p>
    </div>
    <span class="badge {{ $supplier->is_active ? 'badge-success' : 'badge-secondary' }}">
        {{ $supplier->is_active ? 'Aktif' : 'Nonaktif' }}
    </span>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
    <div class="card p-4">
        <p class="text-xs text-gray-500 mb-1">No. Telepon</p>
        <p class="text-sm text-gray-900">{{ $supplier->phone ?? '-' }}</p>
    </div>
    <div class="card p-4">
        <p class="text-xs text-gray-500 mb-1">Email</p>
        <p class="text-sm text-gray-900">{{ $supplier->email ?? '-' }}</p>
    </div>
    <div class="card p-4 md:col-span-2">
        <p class="text-xs text-gray-500 mb-1">Alamat</p>
        <p class="text-sm text-gray-900">{{ $supplier->address ?? '-' }}</p>
    </div>
</div>

{{-- Riwayat PO dari supplier ini, jika dikirim dari controller --}}
@if(isset($purchaseOrders))
<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-semibold text-gray-900">Riwayat Purchase Order</h3>
    </div>
    <div class="overflow-x-auto max-h-96 overflow-y-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">No. PO</th>
                    <th class="px-4 py-3 text-left">Gudang</th>
                    <th class="px-4 py-3 text-left">Tanggal</th>
                    <th class="px-4 py-3 text-right">Total</th>
                    <th class="px-4 py-3 text-left">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($purchaseOrders as $po)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono">
                        <a href="{{ route('admin.purchase-orders.show', $po) }}"
                            class="text-primary-700 hover:underline">{{ $po->po_number }}</a>
                    </td>
                    <td class="px-4 py-3">{{ $po->warehouse->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $po->created_at->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-right font-medium">Rp {{ number_format($po->total_amount) }}</td>
                    <td class="px-4 py-3">
                        <x-status-badge :status="$po->status" />
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada transaksi PO</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="flex justify-between">
    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">← Kembali</a>
    <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-primary">Edit Supplier</a>
</div>
@endsection
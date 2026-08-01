@extends('layouts.admin')
@section('title', 'Detail Gudang')
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">{{ $warehouse->name }}</h1>
        <p class="text-sm text-gray-500 font-mono">{{ $warehouse->code ?? '-' }}</p>
    </div>
    <span class="badge {{ $warehouse->is_active ? 'badge-success' : 'badge-secondary' }}">
        {{ $warehouse->is_active ? 'Aktif' : 'Nonaktif' }}
    </span>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
    <div class="card p-4">
        <p class="text-xs text-gray-500 mb-1">Alamat</p>
        <p class="text-sm text-gray-900">{{ $warehouse->address ?? '-' }}</p>
    </div>
    <div class="card p-4">
        <p class="text-xs text-gray-500 mb-1">Telepon</p>
        <p class="text-sm text-gray-900">{{ $warehouse->phone ?? '-' }}</p>
    </div>
    <div class="card p-4">
        <p class="text-xs text-gray-500 mb-1">Penanggung Jawab</p>
        <p class="text-sm text-gray-900">{{ $warehouse->pic_name ?? '-' }}</p>
    </div>
    <div class="card p-4">
        <p class="text-xs text-gray-500 mb-1">Jumlah Produk Tersimpan</p>
        <p class="text-sm text-gray-900">{{ $warehouse->stocks_count ?? $warehouse->stocks->count() ?? 0 }} SKU</p>
    </div>
</div>

@if(isset($stocks))
<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-semibold text-gray-900">Stok di Gudang Ini</h3>
    </div>
    <div class="overflow-x-auto max-h-96 overflow-y-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Produk</th>
                    <th class="px-4 py-3 text-right">Qty</th>
                    <th class="px-4 py-3 text-left">Satuan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($stocks as $stock)
                <tr>
                    <td class="px-4 py-3">{{ $stock->product->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-right font-medium">{{ $stock->quantity }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $stock->product->unit ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-4 py-8 text-center text-gray-400">Belum ada stok</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="flex justify-between">
    <a href="{{ route('admin.warehouses.index') }}" class="btn btn-secondary">← Kembali</a>
    <a href="{{ route('admin.warehouses.edit', $warehouse) }}" class="btn btn-primary">Edit Gudang</a>
</div>
@endsection
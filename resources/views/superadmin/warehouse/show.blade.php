@extends('layouts.app')
@section('title', $warehouse->name)
@section('breadcrumb')
<a href="{{ route('superadmin.warehouses.index') }}" class="hover:text-primary-700">Gudang</a>
<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
</svg>
<span class="text-gray-700 font-medium">{{ $warehouse->name }}</span>
@endsection

@section('content')
<div class="flex items-start justify-between mb-6">
    <div>
        <div class="flex items-center gap-3">
            <h1 class="text-xl font-bold text-gray-900">{{ $warehouse->name }}</h1>
            <span class="font-mono text-sm bg-gray-100 text-gray-600 px-2 py-0.5 rounded">{{ $warehouse->code }}</span>
            <x-status-badge :status="$warehouse->is_active ? '1' : '0'" />
        </div>
        <p class="text-sm text-gray-500 mt-0.5">{{ $warehouse->location }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('superadmin.warehouses.edit', $warehouse) }}" class="btn-secondary btn">Edit</a>
        <a href="{{ route('superadmin.stocks.by-warehouse', $warehouse) }}" class="btn-primary btn">Lihat Stok</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
    {{-- Info card --}}
    <div class="lg:col-span-1 card p-5 space-y-4">
        @if($warehouse->photo)
        <img src="{{ Storage::url($warehouse->photo) }}" class="w-full h-40 object-cover rounded-lg">
        @endif
        <div>
            <p class="text-xs text-gray-400 mb-0.5">PIC</p>
            <p class="font-medium text-gray-800">{{ $warehouse->pic_name ?? '—' }}</p>
            @if($warehouse->pic_phone)<p class="text-sm text-gray-500">{{ $warehouse->pic_phone }}</p>@endif
        </div>
        <div>
            <p class="text-xs text-gray-400 mb-0.5">Dibuat</p>
            <p class="text-sm text-gray-700">{{ $warehouse->created_at->isoFormat('D MMMM Y') }}</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="lg:col-span-2 grid grid-cols-2 gap-4">
        <div class="stat-card">
            <div class="stat-icon bg-blue-50 text-blue-600">📦</div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($warehouse->stocks_count) }}</p>
                <p class="text-sm text-gray-500">Total Item Stok</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-green-50 text-green-600">✅</div>
            <div>
                <p class="text-2xl font-bold text-gray-900">
                    {{ $warehouse->stocks->where('quantity', '>', 0)->count() }}
                </p>
                <p class="text-sm text-gray-500">Item Tersedia</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-red-50 text-red-600">⚠️</div>
            <div>
                <p class="text-2xl font-bold text-gray-900">
                    {{ $warehouse->stocks->filter(fn($s) => $s->product && $s->quantity <= $s->product->min_stock)->count() }}
                </p>
                <p class="text-sm text-gray-500">Stok Menipis</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-purple-50 text-purple-600">💰</div>
            <div>
                <p class="text-2xl font-bold text-gray-900">
                    Rp
                    {{ number_format($warehouse->stocks->sum(fn($s) => $s->quantity * ($s->product->purchase_price ?? 0)) / 1000) }}K
                </p>
                <p class="text-sm text-gray-500">Estimasi Nilai Stok</p>
            </div>
        </div>
    </div>
</div>

{{-- Stock table --}}
<div class="card">
    <div class="card-header">
        <h3 class="font-semibold text-gray-900">Daftar Stok</h3>
        <a href="{{ route('superadmin.stocks.by-warehouse', $warehouse) }}"
            class="text-sm text-primary-700 hover:underline">Lihat semua →</a>
    </div>
    <div class="table-wrap rounded-none border-0">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>SKU</th>
                    <th>Satuan</th>
                    <th>Stok Min</th>
                    <th>Stok Saat Ini</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($warehouse->stocks->take(20) as $stock)
                <tr>
                    <td class="font-medium">{{ $stock->product->name ?? '—' }}</td>
                    <td class="font-mono text-xs text-gray-500">{{ $stock->product->sku ?? '—' }}</td>
                    <td>{{ $stock->product->unit ?? '—' }}</td>
                    <td>{{ $stock->product->min_stock ?? 0 }}</td>
                    <td
                        class="font-semibold {{ $stock->product && $stock->quantity <= $stock->product->min_stock ? 'text-red-600' : 'text-gray-900' }}">
                        {{ number_format($stock->quantity) }}
                    </td>
                    <td>
                        @if($stock->product && $stock->quantity <= $stock->product->min_stock)
                            <span class="badge badge-danger">Menipis</span>
                            @else
                            <span class="badge badge-success">Normal</span>
                            @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-8 text-gray-400">Belum ada stok di gudang ini</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

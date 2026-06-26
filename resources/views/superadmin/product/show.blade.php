{{-- ============================================================
  resources/views/superadmin/products/show.blade.php
============================================================ --}}
@extends('superadmin.layouts.app')
@section('title', $product->name)
@section('breadcrumb')
    <a href="{{ route('superadmin.products.index') }}" class="text-indigo-500 hover:underline">Produk</a>
    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-gray-700 dark:text-gray-200">{{ Str::limit($product->name, 30) }}</span>
@endsection

@section('content')
{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
    <div class="flex items-start gap-4">
        @if($product->image)
        <img src="{{ Storage::url($product->image) }}" class="w-16 h-16 rounded-xl object-cover border border-gray-100 shadow-sm">
        @else
        <div class="w-16 h-16 rounded-xl bg-indigo-50 flex items-center justify-center">
            <svg class="w-8 h-8 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
        </div>
        @endif
        <div>
            <div class="flex items-center gap-2 flex-wrap">
                <h1 class="page-title">{{ $product->name }}</h1>
                <span class="badge {{ $product->is_active ? 'badge-success' : 'badge-gray' }}">{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                @if($product->is_featured ?? false)<span class="badge badge-brand">⭐ Unggulan</span>@endif
            </div>
            <div class="flex items-center gap-3 mt-1">
                <span class="font-mono text-sm text-gray-400">{{ $product->sku }}</span>
                @if($product->barcode)<span class="text-gray-300">·</span><span class="font-mono text-sm text-gray-400">{{ $product->barcode }}</span>@endif
            </div>
        </div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('superadmin.products.edit', $product) }}" class="btn btn-secondary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit
        </a>
        <form method="POST" action="{{ route('superadmin.products.toggle-active', $product) }}">
            @csrf @method('PATCH')
            <button type="submit" class="btn {{ $product->is_active ? 'btn-warning' : 'btn-success' }}">
                {{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
            </button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    {{-- Left: Detail --}}
    <div class="lg:col-span-2 space-y-5">
        {{-- Pricing --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="stat-card">
                <div class="stat-icon bg-blue-50">💰</div>
                <div><p class="text-lg font-bold text-gray-900 dark:text-white">Rp {{ number_format($product->purchase_price) }}</p><p class="text-xs text-gray-500">Harga Beli</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-green-50">🏷️</div>
                <div><p class="text-lg font-bold text-green-700">Rp {{ number_format($product->selling_price) }}</p><p class="text-xs text-gray-500">Harga Jual</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-purple-50">📊</div>
                <div>
                    @php $margin = $product->purchase_price > 0 ? (($product->selling_price - $product->purchase_price) / $product->purchase_price * 100) : 0; @endphp
                    <p class="text-lg font-bold text-purple-700">{{ number_format($margin, 1) }}%</p>
                    <p class="text-xs text-gray-500">Margin</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-orange-50">📦</div>
                <div><p class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($product->min_stock) }}</p><p class="text-xs text-gray-500">Stok Min ({{ $product->unit }})</p></div>
            </div>
        </div>

        {{-- Info --}}
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-gray-900 dark:text-white">Detail Produk</h3></div>
            <div class="card-body grid grid-cols-2 gap-x-8 gap-y-4 text-sm">
                <div><p class="text-xs text-gray-400 mb-0.5">Kategori</p><p class="font-medium">{{ $product->category->name ?? '—' }}</p></div>
                <div><p class="text-xs text-gray-400 mb-0.5">Supplier</p><p class="font-medium">{{ $product->supplier->name ?? '—' }}</p></div>
                <div><p class="text-xs text-gray-400 mb-0.5">Satuan</p><p class="font-medium">{{ $product->unit }}</p></div>
                @if($product->weight)<div><p class="text-xs text-gray-400 mb-0.5">Berat</p><p class="font-medium">{{ $product->weight }}g</p></div>@endif
                @if($product->description)
                <div class="col-span-2"><p class="text-xs text-gray-400 mb-0.5">Deskripsi</p><p class="text-gray-700 dark:text-gray-300">{{ $product->description }}</p></div>
                @endif
            </div>
        </div>

        {{-- Stock per warehouse --}}
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900 dark:text-white">Stok di Semua Gudang</h3>
                <span class="badge badge-info">{{ $product->stocks->count() }} gudang</span>
            </div>
            <div class="table-wrap border-0 rounded-none">
                <table class="data-table">
                    <thead><tr><th>Gudang</th><th>Kode</th><th class="text-right">Stok</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse($product->stocks as $stock)
                        <tr>
                            <td class="font-medium">{{ $stock->warehouse->name ?? '—' }}</td>
                            <td class="font-mono text-xs text-gray-400">{{ $stock->warehouse->code ?? '' }}</td>
                            <td class="text-right font-bold {{ $stock->quantity <= $product->min_stock ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">
                                {{ number_format($stock->quantity) }}
                            </td>
                            <td>
                                @if($stock->quantity <= $product->min_stock)
                                    <span class="badge badge-danger">⚠ Menipis</span>
                                @elseif($stock->quantity == 0)
                                    <span class="badge badge-danger">Habis</span>
                                @else
                                    <span class="badge badge-success">Normal</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-6 text-gray-400 text-sm">Belum ada stok di gudang manapun</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Right: Sidebar info --}}
    <div class="space-y-5">
        @if($product->image)
        <div class="card p-2">
            <img src="{{ Storage::url($product->image) }}" class="w-full rounded-lg object-cover" style="max-height:220px">
        </div>
        @endif
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-gray-900 dark:text-white">Info Sistem</h3></div>
            <div class="card-body space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-400">Dibuat</span><span>{{ $product->created_at->isoFormat('D MMM Y') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Diupdate</span><span>{{ $product->updated_at->isoFormat('D MMM Y') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Total Stok</span><span class="font-bold text-primary-700">{{ number_format($product->stocks->sum('quantity')) }} {{ $product->unit }}</span></div>
            </div>
        </div>
    </div>
</div>
@endsection

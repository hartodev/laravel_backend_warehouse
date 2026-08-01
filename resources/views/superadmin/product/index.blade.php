{{-- ============================================================
  resources/views/superadmin/products/index.blade.php
============================================================ --}}
@extends('layouts.app')
@section('title','Produk')
@section('breadcrumb')
<span class="font-medium text-gray-700 dark:text-gray-200">Produk</span>
@endsection

@section('content')
{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="page-title">Manajemen Produk</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $products->total() }} produk terdaftar</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('superadmin.products.create') }}" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Produk
        </a>
    </div>
</div>

{{-- Filter --}}
<div class="card mb-5">
    <form method="GET" class="card-body">
        <div class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-48">
                <label class="form-label">Cari</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, SKU, barcode..."
                        class="form-input pl-9">
                </div>
            </div>
            <div class="w-44">
                <label class="form-label">Kategori</label>
                <select name="category_id" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-44">
                <label class="form-label">Supplier</label>
                <select name="supplier_id" class="form-select">
                    <option value="">Semua Supplier</option>
                    @foreach($suppliers as $sup)
                    <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>
                        {{ $sup->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-36">
                <label class="form-label">Status</label>
                <select name="is_active" class="form-select">
                    <option value="">Semua</option>
                    <option value="1" {{ request('is_active')==='1'?'selected':'' }}>Aktif</option>
                    <option value="0" {{ request('is_active')==='0'?'selected':'' }}>Nonaktif</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                    </svg>
                    Filter
                </button>
                <a href="{{ route('superadmin.products.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="card">
    <div class="table-wrap border-0 rounded-xl">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:48px">#</th>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>Supplier</th>
                    <th class="text-right">Hrg. Beli</th>
                    <th class="text-right">Hrg. Jual</th>
                    <th class="text-right">Stok Min</th>
                    <th>Status</th>
                    <th class="text-right" style="width:120px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $i => $p)
                <tr>
                    <td class="text-gray-400 text-xs">{{ $products->firstItem() + $i }}</td>
                    <td>
                        <div class="flex items-center gap-3">
                            @if($p->image)
                            <img src="{{ Storage::url($p->image) }}"
                                class="w-9 h-9 rounded-lg object-cover border border-gray-100 flex-shrink-0">
                            @else
                            <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-indigo-300" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                                </svg>
                            </div>
                            @endif
                            <div class="min-w-0">
                                <a href="{{ route('superadmin.products.show', $p) }}"
                                    class="font-medium text-indigo-600 hover:underline truncate block">{{ $p->name }}</a>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="font-mono text-xs text-gray-400">{{ $p->sku }}</span>
                                    @if($p->barcode)<span class="text-xs text-gray-300">·</span><span
                                        class="font-mono text-xs text-gray-400">{{ $p->barcode }}</span>@endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($p->category)
                        <span class="badge badge-brand">{{ $p->category->name }}</span>
                        @else<span class="text-gray-300">—</span>@endif
                    </td>
                    <td class="text-sm text-gray-600 dark:text-gray-300">{{ $p->supplier->name ?? '—' }}</td>
                    <td class="text-right font-medium text-sm">Rp {{ number_format($p->purchase_price) }}</td>
                    <td class="text-right font-medium text-sm text-green-700">Rp {{ number_format($p->selling_price) }}
                    </td>
                    <td class="text-right text-sm">{{ number_format($p->min_stock) }} {{ $p->unit }}</td>
                    <td>
                        <span class="badge {{ $p->is_active ? 'badge-success' : 'badge-gray' }}">
                            {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('superadmin.products.show', $p) }}" class="btn btn-secondary btn-xs"
                                title="Detail">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            <a href="{{ route('superadmin.products.edit', $p) }}" class="btn btn-secondary btn-xs"
                                title="Edit">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('products.toggle-active', $p) }}" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="btn btn-xs {{ $p->is_active ? 'btn-warning' : 'btn-success' }}"
                                    title="{{ $p->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                </button>
                            </form>
                            <button onclick="document.getElementById('del-p-{{ $p->id }}').classList.remove('hidden')"
                                class="btn btn-danger btn-xs" title="Hapus">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>

                        {{-- Delete modal --}}
                        <div id="del-p-{{ $p->id }}" class="hidden modal-backdrop">
                            <div class="modal-box">
                                <div class="p-6">
                                    <div
                                        class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-center font-bold text-gray-900 dark:text-white mb-1">Hapus Produk?
                                    </h3>
                                    <p class="text-center text-sm text-gray-500 mb-6">Produk
                                        <strong>{{ $p->name }}</strong> akan dihapus permanen dan tidak dapat
                                        dikembalikan.</p>
                                    <div class="flex gap-3">
                                        <button type="button"
                                            onclick="document.getElementById('del-p-{{ $p->id }}').classList.add('hidden')"
                                            class="btn btn-secondary flex-1 justify-center">Batal</button>
                                        <form method="POST" action="{{ route('products.destroy', $p) }}" class="flex-1">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-danger w-full justify-center">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-16">
                        <div class="flex flex-col items-center gap-3">
                            <div
                                class="w-16 h-16 rounded-full bg-gray-100 dark:bg-slate-700 flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                                </svg>
                            </div>
                            <p class="text-gray-400 text-sm">Belum ada produk</p>
                            <a href="{{ route('superadmin.products.create') }}" class="btn btn-primary btn-sm">+ Tambah
                                Produk</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($products->hasPages())
    <div class="card-body border-t border-gray-100 dark:border-slate-700">
        {{ $products->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
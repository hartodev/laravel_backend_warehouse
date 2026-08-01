{{-- suppliers/show.blade.php --}}
@extends('layouts.app')
@section('title', $supplier->name)
@section('breadcrumb')
<a href="{{ route('superadmin.suppliers.index') }}" class="text-indigo-500 hover:underline">Supplier</a>
<svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
</svg>
<span class="font-medium text-gray-700 dark:text-gray-200">{{ Str::limit($supplier->name,28) }}</span>
@endsection

@section('content')
<div class="flex items-start justify-between mb-6">
    <div class="flex items-center gap-4">
        @if($supplier->logo)
        <img src="{{ Storage::url($supplier->logo) }}" class="w-14 h-14 rounded-xl object-cover border shadow-sm">
        @else
        <div
            class="w-14 h-14 rounded-xl bg-purple-50 flex items-center justify-center text-2xl font-bold text-purple-200">
            {{ strtoupper(substr($supplier->name,0,1)) }}
        </div>
        @endif
        <div>
            <div class="flex items-center gap-2 flex-wrap">
                <h1 class="page-title">{{ $supplier->name }}</h1>
                <span
                    class="badge {{ $supplier->is_active ? 'badge-success' : 'badge-gray' }}">{{ $supplier->is_active ? 'Aktif' : 'Nonaktif' }}</span>
            </div>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ $supplier->city ?? '' }}{{ $supplier->city && $supplier->email ? ' · ' : '' }}{{ $supplier->email ?? '' }}
            </p>
        </div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('superadmin.suppliers.edit', $supplier) }}" class="btn btn-secondary">Edit</a>
        <form method="POST" action="{{ route('superadmin.suppliers.toggle-active', $supplier) }}">
            @csrf @method('PATCH')
            <button
                class="btn {{ $supplier->is_active ? 'btn-warning' : 'btn-success' }}">{{ $supplier->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 space-y-5">
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div class="stat-card">
                <div class="stat-icon bg-blue-50">📦</div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($supplier->products_count ?? 0) }}</p>
                    <p class="text-sm text-gray-500">Total Produk</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-green-50">💰</div>
                <div>
                    <p class="text-xl font-bold text-green-700">Rp {{ number_format($totalPurchased / 1000000, 1) }}M
                    </p>
                    <p class="text-sm text-gray-500">Total Pembelian</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-purple-50">📋</div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $supplier->purchaseOrders->count() }}
                    </p>
                    <p class="text-sm text-gray-500">Total PO</p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900 dark:text-white">Informasi Detail</h3>
            </div>
            <div class="card-body grid grid-cols-2 gap-x-8 gap-y-4 text-sm">
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Telepon</p>
                    <p class="font-medium">{{ $supplier->phone ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Kota</p>
                    <p class="font-medium">{{ $supplier->city ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Contact Person</p>
                    <p class="font-medium">{{ $supplier->contact_person ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">HP CP</p>
                    <p class="font-medium">{{ $supplier->contact_phone ?? '—' }}</p>
                </div>
                @if($supplier->bank_name)
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Bank</p>
                    <p class="font-medium">{{ $supplier->bank_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">No. Rekening</p>
                    <p class="font-mono font-medium">{{ $supplier->bank_account }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Atas Nama</p>
                    <p class="font-medium">{{ $supplier->bank_holder }}</p>
                </div>
                @endif
                @if($supplier->address)
                <div class="col-span-2">
                    <p class="text-xs text-gray-400 mb-0.5">Alamat</p>
                    <p>{{ $supplier->address }}</p>
                </div>
                @endif
                @if($supplier->notes)
                <div class="col-span-2">
                    <p class="text-xs text-gray-400 mb-0.5">Catatan</p>
                    <p>{{ $supplier->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900 dark:text-white">Produk dari Supplier Ini</h3>
            </div>
            <div class="table-wrap border-0 rounded-none">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>SKU</th>
                            <th class="text-right">Hrg. Beli</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($supplier->products as $p)
                        <tr>
                            <td><a href="{{ route('superadmin.products.show', $p) }}"
                                    class="font-medium text-indigo-600 hover:underline">{{ $p->name }}</a></td>
                            <td class="font-mono text-xs text-gray-400">{{ $p->sku }}</td>
                            <td class="text-right">Rp {{ number_format($p->selling_price) }}</td>
                            <td><span
                                    class="badge {{ $p->is_active ? 'badge-success' : 'badge-gray' }}">{{ $p->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-8 text-gray-400 text-sm">Belum ada produk dari
                                supplier ini</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card p-5 space-y-3 text-sm h-fit">
        <p class="font-semibold text-gray-900 dark:text-white mb-2">Info Sistem</p>
        <div class="flex justify-between"><span
                class="text-gray-400">Dibuat</span><span>{{ $supplier->created_at->isoFormat('D MMM Y') }}</span></div>
        <div class="flex justify-between"><span
                class="text-gray-400">Diupdate</span><span>{{ $supplier->updated_at->isoFormat('D MMM Y') }}</span>
        </div>
    </div>
</div>
@endsection
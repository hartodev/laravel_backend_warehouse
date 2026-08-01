{{-- ============================================================
  suppliers/index.blade.php
============================================================ --}}
@extends('layouts.app')
@section('title','Supplier')
@section('breadcrumb')<span class="font-medium text-gray-700 dark:text-gray-200">Supplier</span>@endsection

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="page-title">Manajemen Supplier</h1>
        <p class="text-sm text-gray-500">{{ $suppliers->total() }} supplier terdaftar</p>
    </div>
    <a href="{{ route('superadmin.suppliers.create') }}" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Supplier
    </a>
</div>

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
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nama, email, telepon..." class="form-input pl-9">
                </div>
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
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('superadmin.suppliers.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-wrap border-0 rounded-xl">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Supplier</th>
                    <th>Kontak</th>
                    <th>Kota</th>
                    <th>Bank</th>
                    <th class="text-right">Produk</th>
                    <th>Status</th>
                    <th class="text-right" style="width:120px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $i => $sup)
                <tr>
                    <td class="text-gray-400 text-xs">{{ $suppliers->firstItem() + $i }}</td>
                    <td>
                        <div class="flex items-center gap-3">
                            @if($sup->logo)
                            <img src="{{ Storage::url($sup->logo) }}"
                                class="w-8 h-8 rounded-lg object-cover border flex-shrink-0">
                            @else
                            <div
                                class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0 text-purple-300 font-bold text-sm">
                                {{ strtoupper(substr($sup->name,0,1)) }}
                            </div>
                            @endif
                            <div>
                                <a href="{{ route('superadmin.suppliers.show', $sup) }}"
                                    class="font-medium text-indigo-600 hover:underline">{{ $sup->name }}</a>
                                @if($sup->email)<p class="text-xs text-gray-400">{{ $sup->email }}</p>@endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="text-sm">{{ $sup->contact_person ?? $sup->phone ?? '—' }}</div>
                        @if($sup->contact_phone)<p class="text-xs text-gray-400">{{ $sup->contact_phone }}</p>@endif
                    </td>
                    <td class="text-sm text-gray-600 dark:text-gray-300">{{ $sup->city ?? '—' }}</td>
                    <td>
                        @if($sup->bank_name)
                        <p class="text-sm font-medium">{{ $sup->bank_name }}</p>
                        <p class="text-xs font-mono text-gray-400">{{ $sup->bank_account }}</p>
                        @else<span class="text-gray-300 text-sm">—</span>@endif
                    </td>
                    <td class="text-right font-semibold">{{ number_format($sup->products_count ?? 0) }}</td>
                    <td><span
                            class="badge {{ $sup->is_active ? 'badge-success' : 'badge-gray' }}">{{ $sup->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    </td>
                    <td class="text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('superadmin.suppliers.show', $sup) }}" class="btn btn-secondary btn-xs">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            <a href="{{ route('superadmin.suppliers.edit', $sup) }}" class="btn btn-secondary btn-xs">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('superadmin.suppliers.toggle-active', $sup) }}"
                                class="inline">
                                @csrf @method('PATCH')
                                <button class="btn btn-xs {{ $sup->is_active ? 'btn-warning' : 'btn-success' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                </button>
                            </form>
                            <button
                                onclick="document.getElementById('del-sup-{{ $sup->id }}').classList.remove('hidden')"
                                class="btn btn-danger btn-xs">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                        <div id="del-sup-{{ $sup->id }}" class="hidden modal-backdrop">
                            <div class="modal-box p-6">
                                <div
                                    class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <h3 class="text-center font-bold text-gray-900 dark:text-white mb-1">Hapus Supplier?
                                </h3>
                                <p class="text-center text-sm text-gray-500 mb-5"><strong>{{ $sup->name }}</strong> akan
                                    dihapus permanen.</p>
                                <div class="flex gap-3">
                                    <button
                                        onclick="document.getElementById('del-sup-{{ $sup->id }}').classList.add('hidden')"
                                        class="btn btn-secondary flex-1 justify-center">Batal</button>
                                    <form method="POST" action="{{ route('suppliers.destroy', $sup) }}" class="flex-1">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-danger w-full justify-center">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-16">
                        <div class="flex flex-col items-center gap-3">
                            <div
                                class="w-16 h-16 rounded-full bg-gray-100 dark:bg-slate-700 flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0" />
                                </svg>
                            </div>
                            <p class="text-gray-400 text-sm">Belum ada supplier</p>
                            <a href="{{ route('superadmin.suppliers.create') }}" class="btn btn-primary btn-sm">+ Tambah
                                Supplier</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($suppliers->hasPages())
    <div class="card-body border-t border-gray-100 dark:border-slate-700">{{ $suppliers->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
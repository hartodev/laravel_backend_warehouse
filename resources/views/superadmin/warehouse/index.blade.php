{{-- warehouse/index.blade.php --}}
@extends('layouts.app')
@section('title','Data Gudang')
@section('breadcrumb')<span class="text-gray-700 font-medium">Data Gudang</span>@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Data Gudang</h1>
        <p class="text-sm text-gray-500">{{ $warehouses->total() }} gudang</p>
    </div>
    <a href="{{ route('superadmin.warehouses.create') }}" class="btn btn-primary">+ Tambah Gudang</a>
</div>

<div class="card mb-5">
    <form method="GET" class="card-body flex flex-wrap gap-3 items-end">
        <div class="w-64">
            <label class="form-label">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, kode, atau lokasi..."
                class="form-input">
        </div>
        <div class="w-40">
            <label class="form-label">Status</label>
            <select name="is_active" class="form-select">
                <option value="">Semua</option>
                <option value="1" {{ request('is_active')==='1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ request('is_active')==='0' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('superadmin.warehouses.index') }}" class="btn btn-secondary">Reset</a>
    </form>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>Nama Gudang</th>
                <th>Kode</th>
                <th>Lokasi</th>
                <th class="text-right">Jumlah Stok</th>
                <th>Status</th>
                <th class="text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($warehouses as $warehouse)
            <tr>
                <td class="font-medium">{{ $warehouse->name }}</td>
                <td class="font-mono text-sm text-gray-500">{{ $warehouse->code ?? '—' }}</td>
                <td>{{ $warehouse->location ?? '—' }}</td>
                <td class="text-right font-semibold">{{ number_format($warehouse->stocks_count) }}</td>
                <td>
                    @if($warehouse->is_active)
                    <span class="badge badge-success">Aktif</span>
                    @else
                    <span class="badge badge-secondary">Nonaktif</span>
                    @endif
                </td>
                <td class="text-right">
                    <div class="flex items-center justify-end gap-1">
                        <a href="{{ route('superadmin.warehouses.show', $warehouse) }}"
                            class="btn btn-secondary btn-sm">Detail</a>
                        <a href="{{ route('superadmin.warehouses.edit', $warehouse) }}"
                            class="btn btn-primary btn-sm">Edit</a>
                        <button
                            onclick="window.dispatchEvent(new CustomEvent('open-modal-del-warehouse-{{ $warehouse->id }}'))"
                            class="btn btn-danger btn-sm">Hapus</button>
                    </div>

                    <x-confirm-modal id="del-warehouse-{{ $warehouse->id }}" title="Hapus Gudang?"
                        message="Gudang {{ $warehouse->name }} akan dihapus permanen dan tidak bisa dikembalikan."
                        :action="route('superadmin.warehouses.destroy', $warehouse)" method="DELETE" confirm-text="Ya, Hapus"
                        confirm-class="btn-danger" />
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-12 text-gray-400">Belum ada data gudang</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $warehouses->links() }}</div>
@endsection

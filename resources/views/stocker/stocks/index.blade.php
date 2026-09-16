@extends('layouts.stocker')
@section('title', 'Stok')
@section('page-title', 'Semua Stok')
@section('content')

<div class="admin-page-head">
    <h2>Semua Stok <span class="admin-badge admin-badge-success" style="font-size:.65rem;vertical-align:middle;">Read Only</span></h2>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('stocker.stocks.low-stock') }}" class="btn-outline"><i class="lucide-alert-triangle"></i> Stok
            Menipis</a>
    </div>
</div>

{{-- Halaman ini khusus untuk role stocker: hanya menampilkan data, tanpa
     kemampuan tambah/ubah/hapus stok. Semua tombol aksi admin (Input Stok
     Manual, Edit, Hapus) SENGAJA tidak ditampilkan di sini. --}}

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif

<form method="GET" class="admin-filter-bar">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / SKU produk..."
        class="admin-input" style="max-width:240px;">
    <select name="warehouse_id" class="admin-select" style="max-width:180px;">
        <option value="">Semua Gudang</option>
        @foreach($warehouses as $warehouse)
        <option value="{{ $warehouse->id }}" @selected(request('warehouse_id')==$warehouse->id)>{{ $warehouse->name }}
        </option>
        @endforeach
    </select>
    <label style="display:flex;align-items:center;gap:6px;">
        <input type="checkbox" name="low_stock" value="1" @checked(request('low_stock'))> Stok menipis saja
    </label>
    <button class="btn-outline">Filter</button>
</form>

<div class="admin-card admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>SKU</th>
                <th>Gudang</th>
                <th>Qty</th>
                <th>Satuan</th>
                <th>Min. Stok</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($stocks as $stock)
            <tr>
                <td>{{ $stock->product->name ?? '-' }}</td>
                <td class="cell-mono">{{ $stock->product->sku ?? '-' }}</td>
                <td class="cell-muted">{{ $stock->warehouse->name ?? '-' }}</td>
                <td class="cell-mono">{{ $stock->quantity }}</td>
                <td class="cell-muted">{{ $stock->product->unit ?? '-' }}</td>
                <td class="cell-mono cell-muted">{{ $stock->product->min_stock ?? '-' }}</td>
                <td>
                    @if(($stock->product->min_stock ?? null) !== null && $stock->quantity <= $stock->product->min_stock)
                        <span class="admin-badge admin-badge-danger">Menipis</span>
                        @else
                        <span class="admin-badge admin-badge-success">Aman</span>
                        @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="cell-empty">Belum ada data stok.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-pagination">{{ $stocks->appends(request()->query())->links() }}</div>

@endsection

@extends('layouts.admin')
@section('title', 'Laporan Stok')
@section('content')

<div class="admin-page-head"><h2>Laporan Stok</h2></div>

<form method="GET" class="admin-filter-bar">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / SKU produk..." class="admin-input" style="max-width:260px;">
    <select name="warehouse_id" class="admin-select" style="max-width:200px;" onchange="this.form.submit()">
        <option value="">Semua Gudang</option>
        @foreach($warehouses as $warehouse)
        <option value="{{ $warehouse->id }}" @selected(request('warehouse_id') == $warehouse->id)>{{ $warehouse->name }} ({{ $warehouse->code }})</option>
        @endforeach
    </select>
    <button class="btn-outline">Filter</button>
</form>

<div class="admin-card admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Gudang</th>
                <th>Produk</th>
                <th>SKU</th>
                <th>Qty</th>
                <th>Satuan</th>
                <th>Min. Stok</th>
                <th>Status</th>
                <th class="cell-actions">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($summary as $stock)
            <tr>
                <td class="cell-muted">{{ $stock->warehouse->name ?? '-' }}</td>
                <td>{{ $stock->product->name ?? '-' }}</td>
                <td class="cell-mono">{{ $stock->product->sku ?? '-' }}</td>
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
                <td class="cell-actions">
                    @if($stock->warehouse)
                    <a href="{{ route('admin.stock-reports.by-warehouse', $stock->warehouse) }}" class="admin-link">Histori Gudang</a>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="cell-empty">Belum ada data stok.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-pagination">{{ $summary->appends(request()->query())->links() }}</div>
@endsection

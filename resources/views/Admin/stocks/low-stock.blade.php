@extends('layouts.admin')
@section('title', 'Stok Menipis')
@section('content')

<div class="admin-page-head">
    <h2>Stok Menipis</h2>
</div>

<form method="GET" class="admin-filter-bar">
    <select name="warehouse_id" class="admin-select" style="max-width:200px;" onchange="this.form.submit()">
        <option value="">Semua Gudang</option>
        @foreach($warehouses ?? [] as $warehouse)
        <option value="{{ $warehouse->id }}" @selected(request('warehouse_id')==$warehouse->id)>{{ $warehouse->name }}
        </option>
        @endforeach
    </select>
</form>

<div class="admin-card admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>SKU</th>
                <th>Gudang</th>
                <th>Qty Saat Ini</th>
                <th>Min. Stok</th>
                <th>Satuan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($lowStocks as $stock)
            <tr>
                <td>{{ $stock->product->name ?? '-' }}</td>
                <td class="cell-mono">{{ $stock->product->sku ?? '-' }}</td>
                <td class="cell-muted">{{ $stock->warehouse->name ?? '-' }}</td>
                <td class="cell-mono"><span class="admin-badge admin-badge-danger">{{ $stock->quantity }}</span></td>
                <td class="cell-mono cell-muted">{{ $stock->product->min_stock ?? '-' }}</td>
                <td class="cell-muted">{{ $stock->product->unit ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="cell-empty">Tidak ada stok yang menipis saat ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-action-panel" style="margin-top:20px;">
    <a href="{{ route('admin.stocks.index') }}" class="btn-secondary">← Kembali</a>
</div>
@endsection
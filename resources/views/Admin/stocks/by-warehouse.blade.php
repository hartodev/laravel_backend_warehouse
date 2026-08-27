@extends('layouts.admin')
@section('title', 'Stok per Gudang')
@section('content')

<div class="admin-page-head">
    <h2>Stok — {{ $warehouse->name }} <span class="cell-mono cell-muted">({{ $warehouse->code }})</span></h2>
</div>

<div class="admin-detail-grid" style="margin-bottom:20px;max-width:320px;">
    <div class="admin-detail-item"><p class="admin-label">Total Nilai Stok</p><p><strong>Rp{{ number_format($totalValue, 0, ',', '.') }}</strong></p></div>
</div>

<form method="GET" class="admin-filter-bar">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / SKU produk..." class="admin-input" style="max-width:280px;">
    <button class="btn-outline">Filter</button>
</form>

<div class="admin-card admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>SKU</th>
                <th>Qty</th>
                <th>Satuan</th>
                <th>Harga Beli</th>
                <th>Harga Jual</th>
                <th>Min. Stok</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($stocks as $stock)
            <tr>
                <td>{{ $stock->product->name ?? '-' }}</td>
                <td class="cell-mono">{{ $stock->product->sku ?? '-' }}</td>
                <td class="cell-mono">{{ $stock->quantity }}</td>
                <td class="cell-muted">{{ $stock->product->unit ?? '-' }}</td>
                <td class="cell-mono">Rp{{ number_format($stock->product->purchase_price ?? 0, 0, ',', '.') }}</td>
                <td class="cell-mono">Rp{{ number_format($stock->product->selling_price ?? 0, 0, ',', '.') }}</td>
                <td class="cell-mono cell-muted">{{ $stock->product->min_stock ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="cell-empty">Belum ada stok di gudang ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-pagination">{{ $stocks->appends(request()->query())->links() }}</div>

<div class="admin-action-panel" style="margin-top:20px;">
    <a href="{{ route('admin.stocks.index') }}" class="btn-secondary">← Kembali</a>
</div>
@endsection

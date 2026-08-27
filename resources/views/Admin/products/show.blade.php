@extends('layouts.admin')
@section('title', 'Detail Produk')
@section('content')

<div class="admin-page-head">
    <h2>{{ $product->name }} <span class="cell-mono cell-muted">({{ $product->sku }})</span></h2>
    @if($product->is_active)
    <span class="admin-badge admin-badge-success">Aktif</span>
    @else
    <span class="admin-badge admin-badge-muted">Nonaktif</span>
    @endif
</div>

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif

<div class="admin-detail-grid" style="margin-bottom:20px;">
    @if($product->photo)
    <div class="admin-detail-item" style="grid-column:span 2;">
        <img src="{{ asset('storage/'.$product->photo) }}" alt="{{ $product->name }}"
            style="max-height:180px;border-radius:8px;">
    </div>
    @endif
    <div class="admin-detail-item">
        <p class="admin-label">Kategori</p>
        <p>{{ $product->category->name ?? '-' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Barcode</p>
        <p class="cell-mono">{{ $product->barcode ?? '-' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Satuan</p>
        <p>{{ $product->unit }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Stok Minimum</p>
        <p>{{ $product->min_stock }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Harga Beli</p>
        <p class="cell-mono">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Harga Jual</p>
        <p class="cell-mono">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</p>
    </div>
    <div class="admin-detail-item" style="grid-column:span 2;">
        <p class="admin-label">Deskripsi</p>
        <p>{{ $product->description ?: '-' }}</p>
    </div>
</div>

<div class="admin-card admin-table-wrap" style="margin-bottom:20px;">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Gudang</th>
                <th>Qty</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($product->stocks as $stock)
            <tr>
                <td>{{ $stock->warehouse->name ?? '-' }} <span
                        class="cell-muted">({{ $stock->warehouse->code ?? '-' }})</span></td>
                <td class="cell-mono">{{ $stock->quantity }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="2" class="cell-empty">Belum ada stok untuk produk ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($product->units && $product->units->count())
<div class="admin-card admin-table-wrap" style="margin-bottom:20px;">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Nama Unit</th>
                <th>Konversi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($product->units as $unit)
            <tr>
                <td>{{ $unit->name ?? '-' }}</td>
                <td class="cell-mono">{{ $unit->conversion ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<div class="admin-action-panel" style="display:flex;justify-content:space-between;">
    <a href="{{ route('admin.products.index') }}" class="btn-secondary">← Kembali</a>
    <a href="{{ route('admin.products.edit', $product) }}" class="btn-primary ripple">Edit</a>
</div>
@endsection
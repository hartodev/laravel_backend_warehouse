@extends('layouts.supplier')

@section('title', $product->name)

@section('content')
<h4 class="mb-3">{{ $product->name }}</h4>

<p><strong>SKU:</strong> {{ $product->sku }}</p>
<p><strong>Barcode:</strong> {{ $product->barcode ?? '-' }}</p>
<p><strong>Kategori:</strong> {{ $product->category->name ?? '-' }}</p>
<p><strong>Unit:</strong> {{ $product->unit }}</p>
<p><strong>Harga Beli (dari Anda):</strong> Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</p>
<p><strong>Harga Jual:</strong> Rp {{ number_format($product->selling_price, 0, ',', '.') }}</p>
<p><strong>Status:</strong> {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</p>

<a href="{{ route('supplier.products.index') }}" class="btn btn-link">&larr; Kembali</a>
@endsection@extends('layouts.supplier')

@section('title', $product->name)
@section('header', 'Detail Produk')

@section('content')

<a href="{{ route('supplier.products.index') }}" class="btn btn-ghost" style="margin-bottom:16px;">
    &larr; Kembali ke daftar produk
</a>

<div class="section-head">
    <div>
        <span class="eyebrow">Produk</span>
        <h2 style="font-size:20px;">{{ $product->name }}</h2>
    </div>
    @if ($product->is_active)
    <span class="stamp stamp-success" style="font-size:12px; padding:5px 14px;">Aktif</span>
    @else
    <span class="stamp stamp-muted" style="font-size:12px; padding:5px 14px;">Nonaktif</span>
    @endif
</div>

<div class="card">
    <div class="meta-grid" style="margin-bottom:0;">
        <div class="meta-item">
            <span class="label">SKU</span>
            <div class="value mono">{{ $product->sku }}</div>
        </div>
        <div class="meta-item">
            <span class="label">Barcode</span>
            <div class="value mono">{{ $product->barcode ?? '-' }}</div>
        </div>
        <div class="meta-item">
            <span class="label">Kategori</span>
            <div class="value">{{ $product->category->name ?? '-' }}</div>
        </div>
        <div class="meta-item">
            <span class="label">Unit</span>
            <div class="value">{{ $product->unit }}</div>
        </div>
        <div class="meta-item">
            <span class="label">Harga Beli (dari Anda)</span>
            <div class="value mono">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</div>
        </div>
        <div class="meta-item">
            <span class="label">Harga Jual</span>
            <div class="value mono">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</div>
        </div>
    </div>
</div>

@endsection
@extends('layouts.admin')
@section('title', 'Detail Pergerakan Stok')
@section('content')

@php
$typeBadge = ['in' => 'admin-badge-success', 'out' => 'admin-badge-danger', 'adjustment' => 'admin-badge-warning'];
$typeLabel = ['in' => 'Masuk', 'out' => 'Keluar', 'adjustment' => 'Penyesuaian'];
@endphp

<div class="admin-page-head">
    <h2>Pergerakan Stok #{{ $movement->id }}</h2>
    <span
        class="admin-badge {{ $typeBadge[$movement->type] ?? 'admin-badge-muted' }}">{{ $typeLabel[$movement->type] ?? $movement->type }}</span>
</div>

<div class="admin-detail-grid" style="margin-bottom:20px;">
    <div class="admin-detail-item">
        <p class="admin-label">Produk</p>
        <p>{{ $movement->product->name ?? '-' }} <span
                class="cell-mono cell-muted">({{ $movement->product->sku ?? '-' }})</span></p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Gudang</p>
        <p>{{ $movement->warehouse->name ?? '-' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Jumlah</p>
        <p class="cell-mono">{{ $movement->quantity }} {{ $movement->product->unit ?? '' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Sebelum → Sesudah</p>
        <p class="cell-mono">{{ $movement->quantity_before }} → {{ $movement->quantity_after }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Referensi</p>
        <p>{{ $movement->reference_type ?? '-' }} @if($movement->reference_id) #{{ $movement->reference_id }} @endif</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Dicatat Oleh</p>
        <p>{{ $movement->createdBy->name ?? '-' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Tanggal</p>
        <p>{{ $movement->created_at->format('d M Y H:i') }}</p>
    </div>
    @if($movement->note)
    <div class="admin-detail-item" style="grid-column:span 2;">
        <p class="admin-label">Catatan</p>
        <p>{{ $movement->note }}</p>
    </div>
    @endif
</div>

<div class="admin-action-panel">
    <a href="{{ route('admin.stock-movements.index') }}" class="btn-secondary">← Kembali</a>
</div>
@endsection
@extends('layouts.admin')
@section('title', 'Detail Gudang')
@section('content')

<div class="admin-page-head">
    <h2>{{ $warehouse->name }} <span class="cell-mono cell-muted">({{ $warehouse->code }})</span></h2>
    @if($warehouse->is_active)
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
    @if($warehouse->photo)
    <div class="admin-detail-item" style="grid-column:span 2;">
        <img src="{{ asset('storage/'.$warehouse->photo) }}" alt="{{ $warehouse->name }}" style="max-height:180px;border-radius:8px;">
    </div>
    @endif
    <div class="admin-detail-item">
        <p class="admin-label">Lokasi</p>
        <p>{{ $warehouse->location }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">PIC</p>
        <p>{{ $warehouse->pic_name ?? '-' }} @if($warehouse->pic_phone) · {{ $warehouse->pic_phone }} @endif</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Jumlah Item Stok</p>
        <p>{{ $warehouse->stocks_count }}</p>
    </div>
</div>

<div class="admin-card admin-table-wrap" style="margin-bottom:20px;">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>SKU</th>
                <th>Qty</th>
                <th>Satuan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($warehouse->stocks as $stock)
            <tr>
                <td>{{ $stock->product->name ?? '-' }}</td>
                <td class="cell-mono">{{ $stock->product->sku ?? '-' }}</td>
                <td class="cell-mono">{{ $stock->quantity }}</td>
                <td class="cell-muted">{{ $stock->product->unit ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="cell-empty">Belum ada stok di gudang ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-action-panel" style="display:flex;justify-content:space-between;">
    <a href="{{ route('admin.warehouses.index') }}" class="btn-secondary">← Kembali</a>
    <a href="{{ route('admin.warehouses.edit', $warehouse) }}" class="btn-primary ripple">Edit</a>
</div>
@endsection

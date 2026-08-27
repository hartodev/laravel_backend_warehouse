@extends('layouts.admin')
@section('title', 'Detail Kategori')
@section('content')

<div class="admin-page-head">
    <h2>{{ $category->name }}</h2>
    @if($category->is_active)
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
    <div class="admin-detail-item">
        <p class="admin-label">Kode</p>
        <p class="cell-mono">{{ $category->code ?? '-' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Jumlah Produk</p>
        <p>{{ $category->products_count }}</p>
    </div>
    <div class="admin-detail-item" style="grid-column:span 2;">
        <p class="admin-label">Deskripsi</p>
        <p>{{ $category->description ?: '-' }}</p>
    </div>
</div>

<div class="admin-action-panel" style="display:flex;justify-content:space-between;">
    <a href="{{ route('admin.categories.index') }}" class="btn-secondary">← Kembali</a>
    <a href="{{ route('admin.categories.edit', $category) }}" class="btn-primary ripple">Edit</a>
</div>
@endsection

@extends('layouts.admin')
@section('title', 'Detail Pengajuan Produk')
@section('content')

<div class="admin-page-head">
    <h2>{{ $submission->name }}</h2>
    @if($submission->status === 'approved')
    <span class="admin-badge admin-badge-success">Disetujui</span>
    @elseif($submission->status === 'rejected')
    <span class="admin-badge admin-badge-danger">Ditolak</span>
    @else
    <span class="admin-badge admin-badge-warning">Pending</span>
    @endif
</div>

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif
@if ($errors->any())
<div class="admin-alert admin-alert-error">
    <div>
        @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
        @endforeach
    </div>
</div>
@endif

<div class="admin-detail-grid" style="margin-bottom:20px;">
    <div class="admin-detail-item">
        <p class="admin-label">Kategori</p>
        <p>{{ $submission->category->name ?? '-' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">SKU</p>
        <p class="cell-mono">{{ $submission->sku ?? '-' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Barcode</p>
        <p class="cell-mono">{{ $submission->barcode ?? '-' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Satuan</p>
        <p>{{ $submission->unit }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Stok Awal</p>
        <p class="cell-mono">{{ $submission->initial_stock ?? 0 }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Gudang Awal</p>
        <p>{{ $submission->initialWarehouse->name ?? '-' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Harga Beli</p>
        <p class="cell-mono">Rp {{ number_format($submission->purchase_price, 0, ',', '.') }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Harga Jual</p>
        <p class="cell-mono">Rp {{ number_format($submission->selling_price, 0, ',', '.') }}</p>
    </div>
    <div class="admin-detail-item" style="grid-column:span 2;">
        <p class="admin-label">Deskripsi</p>
        <p>{{ $submission->description ?: '-' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Diproses Oleh</p>
        <p>{{ $submission->approvedBy->name ?? '-' }}</p>
    </div>
    @if($submission->status === 'approved')
    <div class="admin-detail-item">
        <p class="admin-label">Produk Terbentuk</p>
        <p>{{ $submission->product->name ?? '-' }} <span
                class="cell-mono cell-muted">({{ $submission->product->sku ?? '-' }})</span></p>
    </div>
    @endif
    @if($submission->status === 'rejected')
    <div class="admin-detail-item" style="grid-column:span 2;">
        <p class="admin-label">Alasan Penolakan</p>
        <p>{{ $submission->reject_reason ?: '-' }}</p>
    </div>
    @endif
</div>

@if($submission->status === 'pending')
<div class="admin-card admin-card-pad" style="margin-bottom:20px;">
    <h3 style="margin-top:0;">Review Pengajuan</h3>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <form action="{{ route('admin.product-submissions.approve', $submission) }}" method="POST"
            onsubmit="return confirm('Setujui pengajuan produk ini?');">
            @csrf
            <button type="submit" class="btn-primary ripple">Setujui</button>
        </form>
        <button type="button" class="btn-danger"
            onclick="document.getElementById('reject-form').style.display='flex'">Tolak</button>
    </div>

    <form id="reject-form" action="{{ route('admin.product-submissions.reject', $submission) }}" method="POST"
        style="display:none;gap:10px;margin-top:14px;align-items:flex-end;">
        @csrf
        <div style="flex:1;">
            <label class="admin-label">Alasan Penolakan</label>
            <textarea name="reject_reason" required class="admin-textarea"></textarea>
        </div>
        <button type="submit" class="btn-danger">Tolak</button>
    </form>
</div>
@endif

<div class="admin-action-panel">
    <a href="{{ route('admin.product-submissions.index') }}" class="btn-secondary">← Kembali</a>
</div>
@endsection
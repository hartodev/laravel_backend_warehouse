@extends('layouts.admin')
@section('title', 'Detail Laporan Belanja')
@section('content')

<div class="admin-page-head">
    <h2>{{ $er->nomor_invoice ?? 'LPJ #' . $er->id }}</h2>
    @if($er->status === 'verified')
    <span class="admin-badge admin-badge-success">Terverifikasi</span>
    @elseif($er->status === 'pending_revisi')
    <span class="admin-badge admin-badge-warning">Pending Revisi</span>
    @else
    <span class="admin-badge admin-badge-info">Submitted</span>
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
        <p class="admin-label">RAB Terkait</p>
        <p class="cell-mono">{{ $er->budgetRequest->nomor_form ?? '-' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Vendor</p>
        <p>{{ $er->nama_vendor ?? '-' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Tanggal Transaksi</p>
        <p>{{ optional($er->tanggal_transaksi)->format('d M Y') }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Nominal Realisasi</p>
        <p class="cell-mono">Rp {{ number_format($er->nominal_realisasi, 0, ',', '.') }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Selisih</p>
        <p class="cell-mono">Rp {{ number_format($er->selisih ?? 0, 0, ',', '.') }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Diajukan Oleh</p>
        <p>{{ $er->submittedBy->name ?? '-' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Diverifikasi Oleh</p>
        <p>{{ $er->verifiedBy->name ?? '-' }} @if($er->verified_at) · {{ $er->verified_at->format('d M Y H:i') }} @endif
        </p>
    </div>
    <div class="admin-detail-item" style="grid-column:span 2;">
        <p class="admin-label">Catatan</p>
        <p>{{ $er->catatan ?: '-' }}</p>
    </div>
</div>

<div class="admin-card admin-card-pad" style="margin-bottom:20px;">
    <h3 style="margin-top:0;">Kelengkapan Lampiran</h3>
    <div class="admin-detail-grid">
        <div class="admin-detail-item">
            <p class="admin-label">Invoice</p>
            <p>{{ $er->lamp_invoice ? 'Ya' : 'Tidak' }}</p>
        </div>
        <div class="admin-detail-item">
            <p class="admin-label">Bukti Transfer</p>
            <p>{{ $er->lamp_bukti_transfer ? 'Ya' : 'Tidak' }}</p>
        </div>
        <div class="admin-detail-item">
            <p class="admin-label">Kartu Garansi</p>
            <p>{{ $er->lamp_kartu_garansi ? 'Ya' : 'Tidak' }}</p>
        </div>
        <div class="admin-detail-item">
            <p class="admin-label">Serah Terima</p>
            <p>{{ $er->lamp_serah_terima ? 'Ya' : 'Tidak' }}</p>
        </div>
        <div class="admin-detail-item" style="grid-column:span 2;">
            <p class="admin-label">Lainnya</p>
            <p>{{ $er->lamp_lainnya ?: '-' }}</p>
        </div>
    </div>
</div>

@if($er->status === 'submitted')
<div class="admin-card admin-card-pad" style="margin-bottom:20px;">
    <h3 style="margin-top:0;">Verifikasi Laporan</h3>
    <form action="{{ route('admin.expense-reports.verify', $er) }}" method="POST"
        onsubmit="return confirm('Verifikasi laporan ini?');">
        @csrf
        <div style="margin-bottom:12px;">
            <label class="admin-label">Catatan (opsional)</label>
            <textarea name="catatan" class="admin-textarea"></textarea>
        </div>
        <button type="submit" class="btn-primary ripple">Verifikasi</button>
    </form>
</div>
@endif

<div class="admin-action-panel" style="display:flex;justify-content:space-between;">
    <a href="{{ route('admin.expense-reports.index') }}" class="btn-secondary">← Kembali</a>
    @if($er->status !== 'verified')
    <a href="{{ route('admin.expense-reports.edit', $er) }}" class="btn-primary ripple">Edit</a>
    @endif
</div>
@endsection
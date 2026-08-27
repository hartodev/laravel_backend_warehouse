@extends('layouts.admin')
@section('title', 'Detail Revisi Anggaran')
@section('content')

<div class="admin-page-head">
    <h2>Revisi · {{ $budgetRevision->akun_terdampak }}</h2>
    @if($budgetRevision->status === 'approved')
    <span class="admin-badge admin-badge-success">Approved</span>
    @elseif($budgetRevision->status === 'ditolak')
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

<div class="admin-card admin-card-pad" style="margin-bottom:20px;">
    <h3 style="margin-top:0;">Detail Revisi</h3>
    <div class="admin-detail-grid">
        <div class="admin-detail-item">
            <p class="admin-label">RAB Terkait</p>
            <p>{{ $budgetRevision->budgetRequest->nomor_form ?? '-' }}</p>
        </div>
        <div class="admin-detail-item">
            <p class="admin-label">Akun Terdampak</p>
            <p>{{ $budgetRevision->akun_terdampak }} @if($budgetRevision->kode_akun) ({{ $budgetRevision->kode_akun }})
                @endif</p>
        </div>
        <div class="admin-detail-item">
            <p class="admin-label">Jenis Perubahan</p>
            <p>
                @if($budgetRevision->jenis_perubahan === 'tambahan')
                <span class="admin-badge admin-badge-success">Tambahan</span>
                @else
                <span class="admin-badge admin-badge-danger">Pengurangan</span>
                @endif
            </p>
        </div>
        <div class="admin-detail-item">
            <p class="admin-label">Anggaran Awal</p>
            <p class="cell-mono">Rp {{ number_format($budgetRevision->anggaran_awal, 0, ',', '.') }}</p>
        </div>
        <div class="admin-detail-item">
            <p class="admin-label">Realisasi</p>
            <p class="cell-mono">Rp {{ number_format($budgetRevision->realisasi, 0, ',', '.') }}</p>
        </div>
        <div class="admin-detail-item">
            <p class="admin-label">Nominal Perubahan</p>
            <p class="cell-mono">Rp {{ number_format($budgetRevision->nominal_perubahan, 0, ',', '.') }}</p>
        </div>
        <div class="admin-detail-item">
            <p class="admin-label">Anggaran Baru</p>
            <p class="cell-mono">Rp {{ number_format($budgetRevision->anggaran_baru ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="admin-detail-item">
            <p class="admin-label">Diajukan Oleh</p>
            <p>{{ $budgetRevision->createdBy->name ?? '-' }}</p>
        </div>
        @if($budgetRevision->approvedBy)
        <div class="admin-detail-item">
            <p class="admin-label">Diproses Oleh</p>
            <p>{{ $budgetRevision->approvedBy->name ?? '-' }}</p>
        </div>
        @endif
        <div class="admin-detail-item" style="grid-column:span 2;">
            <p class="admin-label">Alasan Revisi</p>
            <p>{{ $budgetRevision->alasan_revisi }}</p>
        </div>
        @if($budgetRevision->catatan_approver)
        <div class="admin-detail-item" style="grid-column:span 2;">
            <p class="admin-label">Catatan Approver</p>
            <p>{{ $budgetRevision->catatan_approver }}</p>
        </div>
        @endif
    </div>
</div>

<div class="admin-action-panel" style="display:flex;justify-content:space-between;gap:8px;">
    <a href="{{ route('admin.budget-revisions.index') }}" class="btn-secondary">← Kembali</a>

    @if($budgetRevision->status === 'pending')
    <div style="display:flex;gap:8px;">
        <form action="{{ route('admin.budget-revisions.approve', $budgetRevision) }}" method="POST"
            onsubmit="return confirm('Setujui revisi ini?')">
            @csrf
            <button type="submit" class="btn-primary ripple">Setujui</button>
        </form>
        <form action="{{ route('admin.budget-revisions.reject', $budgetRevision) }}" method="POST"
            onsubmit="return confirm('Tolak revisi ini?')">
            @csrf
            <input type="hidden" name="catatan" value="Ditolak melalui halaman detail">
            <button type="submit" class="btn-danger ripple">Tolak</button>
        </form>
    </div>
    @endif
</div>
@endsection
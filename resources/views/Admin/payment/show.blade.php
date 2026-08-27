@extends('layouts.admin')
@section('title', 'Detail Pembayaran')
@section('content')

<div class="admin-page-head">
    <h2>{{ $payment->payment_number }}</h2>
    @if($payment->status === 'verified')
    <span class="admin-badge admin-badge-success">Terverifikasi</span>
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

<div class="admin-detail-grid" style="margin-bottom:20px;">
    <div class="admin-detail-item">
        <p class="admin-label">Jenis</p>
        <p>{{ $payment->payment_type === 'masuk' ? 'Masuk' : 'Keluar' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Metode</p>
        <p>{{ ucfirst($payment->payment_method) }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Nominal</p>
        <p class="cell-mono">Rp {{ number_format($payment->nominal, 0, ',', '.') }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Tanggal</p>
        <p>{{ optional($payment->payment_date)->format('d M Y') }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">PO Terkait</p>
        <p class="cell-mono">{{ $payment->purchaseOrder->po_number ?? '-' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">SO Terkait</p>
        <p class="cell-mono">{{ $payment->salesOrder->so_number ?? '-' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">RAB Terkait</p>
        <p class="cell-mono">{{ $payment->budgetRequest->nomor_form ?? '-' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Diterima Dari</p>
        <p>{{ $payment->diterima_dari ?? '-' }}</p>
    </div>
    <div class="admin-detail-item" style="grid-column:span 2;">
        <p class="admin-label">Untuk Pembayaran</p>
        <p>{{ $payment->untuk_pembayaran ?: '-' }}</p>
    </div>
    <div class="admin-detail-item" style="grid-column:span 2;">
        <p class="admin-label">Terbilang</p>
        <p>{{ $payment->terbilang ?: '-' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Pengirim</p>
        <p>{{ $payment->nama_pengirim ?? '-' }} @if($payment->bank_pengirim) · {{ $payment->bank_pengirim }} @endif</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Penerima</p>
        <p>{{ $payment->nama_penerima ?? '-' }} @if($payment->bank_penerima) · {{ $payment->bank_penerima }} @endif</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">No. Rekening Tujuan</p>
        <p class="cell-mono">{{ $payment->no_rekening_tujuan ?? '-' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Dibuat Oleh</p>
        <p>{{ $payment->createdBy->name ?? '-' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Diverifikasi Oleh</p>
        <p>{{ $payment->verifiedBy->name ?? '-' }} @if($payment->verified_at) ·
            {{ $payment->verified_at->format('d M Y H:i') }} @endif</p>
    </div>
    @if($payment->bukti_file)
    <div class="admin-detail-item">
        <p class="admin-label">Bukti Pembayaran</p>
        <p><a href="{{ asset('storage/'.$payment->bukti_file) }}" target="_blank" class="admin-link">Lihat File</a></p>
    </div>
    @endif
    <div class="admin-detail-item" style="grid-column:span 2;">
        <p class="admin-label">Keterangan</p>
        <p>{{ $payment->keterangan ?: '-' }}</p>
    </div>
</div>

@if($payment->status === 'pending')
<div class="admin-card admin-card-pad" style="margin-bottom:20px;">
    <h3 style="margin-top:0;">Verifikasi Pembayaran</h3>
    <form action="{{ route('admin.payments.verify', $payment) }}" method="POST"
        onsubmit="return confirm('Verifikasi pembayaran ini?');">
        @csrf
        <button type="submit" class="btn-primary ripple">Verifikasi</button>
    </form>
</div>
@endif

<div class="admin-action-panel" style="display:flex;justify-content:space-between;">
    <a href="{{ route('admin.payments.index') }}" class="btn-secondary">← Kembali</a>
    @if($payment->status !== 'verified')
    <a href="{{ route('admin.payments.edit', $payment) }}" class="btn-primary ripple">Edit</a>
    @endif
</div>
@endsection
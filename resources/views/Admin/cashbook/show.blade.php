@extends('layouts.admin')
@section('title', 'Detail Buku Kas')
@section('content')

<div class="admin-page-head">
    <h2>Detail Buku Kas — {{ $book->no_bukti }}</h2>
</div>

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif
@if ($errors->any())
<div class="admin-alert admin-alert-error">
    <ul style="margin:0; padding-left:1.2em;">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="admin-card" style="margin-bottom:16px;">
    <div class="admin-detail-grid">
        <div>
            <span class="cell-muted">No. Bukti</span>
            <p class="cell-mono">{{ $book->no_bukti }}</p>
        </div>
        <div>
            <span class="cell-muted">Tipe</span>
            <p>
                @if($book->type === 'masuk')
                <span class="admin-badge admin-badge-success">Masuk</span>
                @else
                <span class="admin-badge admin-badge-danger">Keluar</span>
                @endif
            </p>
        </div>
        <div>
            <span class="cell-muted">Tanggal</span>
            <p class="cell-muted">{{ optional($book->tanggal)->format('d M Y') ?? '-' }}</p>
        </div>
        <div>
            <span class="cell-muted">Jumlah</span>
            <p class="cell-mono">Rp {{ number_format($book->jumlah_uang, 0, ',', '.') }}</p>
        </div>
        <div>
            <span class="cell-muted">Pihak</span>
            <p>{{ $book->pihak }}</p>
        </div>
        <div>
            <span class="cell-muted">Dibuat Oleh</span>
            <p>{{ $book->createdBy->name ?? '-' }}</p>
        </div>
        <div>
            <span class="cell-muted">Status Verifikasi</span>
            <p>
                @if($book->verified_at)
                <span class="admin-badge admin-badge-success">Terverifikasi oleh
                    {{ $book->verifiedBy->name ?? '-' }}</span>
                @else
                <span class="admin-badge admin-badge-warning">Belum Diverifikasi</span>
                @endif
            </p>
        </div>
        @if($book->payment)
        <div>
            <span class="cell-muted">Terkait Pembayaran</span>
            <p class="cell-mono">{{ $book->payment->payment_number }}</p>
        </div>
        @endif
    </div>

    <p style="margin-top:12px;"><strong>Terbilang:</strong> {{ $book->terbilang }}</p>
    @if($book->keterangan)
    <p style="margin-top:8px;"><strong>Keterangan:</strong><br>{{ $book->keterangan }}</p>
    @endif
</div>

@if(!$book->verified_at)
<form method="POST" action="{{ route('admin.cashbook.update', $book) }}" class="admin-card">
    @csrf
    @method('PUT')

    <h3 class="admin-section-title">Edit Transaksi</h3>

    <div class="admin-form-group">
        <label for="pihak">Pihak</label>
        <input type="text" id="pihak" name="pihak" class="admin-input" value="{{ old('pihak', $book->pihak) }}">
    </div>

    <div class="admin-form-grid">
        <div class="admin-form-group">
            <label for="jumlah_uang">Jumlah</label>
            <input type="number" step="0.01" min="0" id="jumlah_uang" name="jumlah_uang" class="admin-input"
                value="{{ old('jumlah_uang', $book->jumlah_uang) }}">
        </div>
        <div class="admin-form-group">
            <label for="tanggal">Tanggal</label>
            <input type="date" id="tanggal" name="tanggal" class="admin-input"
                value="{{ old('tanggal', optional($book->tanggal)->format('Y-m-d')) }}">
        </div>
    </div>

    <div class="admin-form-group">
        <label for="terbilang">Terbilang</label>
        <input type="text" id="terbilang" name="terbilang" class="admin-input"
            value="{{ old('terbilang', $book->terbilang) }}">
    </div>

    <div class="admin-form-group">
        <label for="keterangan">Keterangan</label>
        <textarea id="keterangan" name="keterangan" class="admin-textarea"
            rows="3">{{ old('keterangan', $book->keterangan) }}</textarea>
    </div>

    <div class="admin-form-actions">
        <a href="{{ route('admin.cashbook.index') }}" class="btn-outline">Kembali</a>
        <button type="submit" class="btn-primary">Update</button>
    </div>
</form>
@else
<div class="admin-alert admin-alert-warning">
    Buku kas yang sudah diverifikasi tidak dapat diubah.
</div>
@endif
@endsection
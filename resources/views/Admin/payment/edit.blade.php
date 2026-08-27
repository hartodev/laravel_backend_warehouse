@extends('layouts.admin')
@section('title', 'Edit Pembayaran')
@section('content')

<div class="admin-page-head">
    <h2>Edit Pembayaran · {{ $payment->payment_number }}</h2>
</div>

@if ($errors->any())
<div class="admin-alert admin-alert-error">
    <div>
        @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
        @endforeach
    </div>
</div>
@endif

<form action="{{ route('admin.payments.update', $payment) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="admin-form-grid" style="grid-template-columns:repeat(2,1fr);margin-bottom:20px;">
        <div>
            <label class="admin-label">Nominal</label>
            <input type="number" step="0.01" name="nominal" value="{{ old('nominal', $payment->nominal) }}" required
                class="admin-input">
        </div>
        <div>
            <label class="admin-label">Tanggal Pembayaran</label>
            <input type="date" name="payment_date"
                value="{{ old('payment_date', optional($payment->payment_date)->format('Y-m-d')) }}" required
                class="admin-input">
        </div>
        <div>
            <label class="admin-label">Diterima Dari</label>
            <input type="text" name="diterima_dari" value="{{ old('diterima_dari', $payment->diterima_dari) }}"
                class="admin-input">
        </div>
        <div style="grid-column:span 2;">
            <label class="admin-label">Untuk Pembayaran</label>
            <textarea name="untuk_pembayaran"
                class="admin-textarea">{{ old('untuk_pembayaran', $payment->untuk_pembayaran) }}</textarea>
        </div>
        <div style="grid-column:span 2;">
            <label class="admin-label">Terbilang</label>
            <input type="text" name="terbilang" value="{{ old('terbilang', $payment->terbilang) }}" class="admin-input">
        </div>
        <div>
            <label class="admin-label">Nama Pengirim</label>
            <input type="text" name="nama_pengirim" value="{{ old('nama_pengirim', $payment->nama_pengirim) }}"
                class="admin-input">
        </div>
        <div>
            <label class="admin-label">Bank Pengirim</label>
            <input type="text" name="bank_pengirim" value="{{ old('bank_pengirim', $payment->bank_pengirim) }}"
                class="admin-input">
        </div>
        <div>
            <label class="admin-label">Nama Penerima</label>
            <input type="text" name="nama_penerima" value="{{ old('nama_penerima', $payment->nama_penerima) }}"
                class="admin-input">
        </div>
        <div>
            <label class="admin-label">Bank Penerima</label>
            <input type="text" name="bank_penerima" value="{{ old('bank_penerima', $payment->bank_penerima) }}"
                class="admin-input">
        </div>
        <div>
            <label class="admin-label">No. Rekening Tujuan</label>
            <input type="text" name="no_rekening_tujuan"
                value="{{ old('no_rekening_tujuan', $payment->no_rekening_tujuan) }}" class="admin-input">
        </div>
        <div>
            <label class="admin-label">Bukti Pembayaran</label>
            <input type="file" name="bukti_file" accept=".jpg,.jpeg,.png,.pdf" class="admin-input">
            @if($payment->bukti_file)
            <p class="cell-muted" style="margin-top:6px;">File saat ini: <a
                    href="{{ asset('storage/'.$payment->bukti_file) }}" target="_blank" class="admin-link">Lihat</a></p>
            @endif
        </div>
        <div style="grid-column:span 2;">
            <label class="admin-label">Keterangan</label>
            <textarea name="keterangan" class="admin-textarea">{{ old('keterangan', $payment->keterangan) }}</textarea>
        </div>
    </div>

    <div class="admin-form-actions" style="justify-content:flex-end;">
        <a href="{{ route('admin.payments.show', $payment) }}" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary ripple">Simpan Perubahan</button>
    </div>
</form>
@endsection
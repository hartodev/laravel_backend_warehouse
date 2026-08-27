@extends('layouts.admin')
@section('title', 'Catat Pembayaran')
@section('content')

<div class="admin-page-head">
    <h2>Catat Pembayaran</h2>
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

<form action="{{ route('admin.payments.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="admin-form-grid" style="grid-template-columns:repeat(2,1fr);margin-bottom:20px;">
        <div>
            <label class="admin-label">Jenis Pembayaran</label>
            <select name="payment_type" required class="admin-select">
                <option value="masuk" @selected(old('payment_type')=='masuk' )>Masuk</option>
                <option value="keluar" @selected(old('payment_type')=='keluar' )>Keluar</option>
            </select>
        </div>
        <div>
            <label class="admin-label">Metode Pembayaran</label>
            <select name="payment_method" required class="admin-select">
                <option value="cash" @selected(old('payment_method')=='cash' )>Cash</option>
                <option value="transfer" @selected(old('payment_method')=='transfer' )>Transfer</option>
                <option value="cek" @selected(old('payment_method')=='cek' )>Cek</option>
                <option value="giro" @selected(old('payment_method')=='giro' )>Giro</option>
            </select>
        </div>
        <div>
            <label class="admin-label">Nominal</label>
            <input type="number" step="0.01" name="nominal" value="{{ old('nominal') }}" required class="admin-input">
        </div>
        <div>
            <label class="admin-label">Tanggal Pembayaran</label>
            <input type="date" name="payment_date" value="{{ old('payment_date') }}" required class="admin-input">
        </div>
        <div>
            <label class="admin-label">ID Purchase Order (opsional)</label>
            <input type="number" name="purchase_order_id" value="{{ old('purchase_order_id') }}" class="admin-input">
        </div>
        <div>
            <label class="admin-label">ID Sales Order (opsional)</label>
            <input type="number" name="sales_order_id" value="{{ old('sales_order_id') }}" class="admin-input">
        </div>
        <div>
            <label class="admin-label">ID Pengajuan RAB (opsional)</label>
            <input type="number" name="budget_request_id" value="{{ old('budget_request_id') }}" class="admin-input">
        </div>
        <div>
            <label class="admin-label">Diterima Dari</label>
            <input type="text" name="diterima_dari" value="{{ old('diterima_dari') }}" class="admin-input">
        </div>
        <div style="grid-column:span 2;">
            <label class="admin-label">Untuk Pembayaran</label>
            <textarea name="untuk_pembayaran" class="admin-textarea">{{ old('untuk_pembayaran') }}</textarea>
        </div>
        <div style="grid-column:span 2;">
            <label class="admin-label">Terbilang</label>
            <input type="text" name="terbilang" value="{{ old('terbilang') }}" class="admin-input">
        </div>
        <div>
            <label class="admin-label">Nama Pengirim</label>
            <input type="text" name="nama_pengirim" value="{{ old('nama_pengirim') }}" class="admin-input">
        </div>
        <div>
            <label class="admin-label">Bank Pengirim</label>
            <input type="text" name="bank_pengirim" value="{{ old('bank_pengirim') }}" class="admin-input">
        </div>
        <div>
            <label class="admin-label">Nama Penerima</label>
            <input type="text" name="nama_penerima" value="{{ old('nama_penerima') }}" class="admin-input">
        </div>
        <div>
            <label class="admin-label">Bank Penerima</label>
            <input type="text" name="bank_penerima" value="{{ old('bank_penerima') }}" class="admin-input">
        </div>
        <div>
            <label class="admin-label">No. Rekening Tujuan</label>
            <input type="text" name="no_rekening_tujuan" value="{{ old('no_rekening_tujuan') }}" class="admin-input">
        </div>
        <div>
            <label class="admin-label">Bukti Pembayaran</label>
            <input type="file" name="bukti_file" accept=".jpg,.jpeg,.png,.pdf" class="admin-input">
        </div>
        <div style="grid-column:span 2;">
            <label class="admin-label">Keterangan</label>
            <textarea name="keterangan" class="admin-textarea">{{ old('keterangan') }}</textarea>
        </div>
    </div>

    <div class="admin-form-actions" style="justify-content:flex-end;">
        <a href="{{ route('admin.payments.index') }}" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary ripple">Simpan</button>
    </div>
</form>
@endsection
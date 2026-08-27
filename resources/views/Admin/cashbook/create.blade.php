@extends('layouts.admin')
@section('title', 'Catat Transaksi Buku Kas')
@section('content')

<div class="admin-page-head">
    <h2>Catat Transaksi Buku Kas</h2>
</div>

@if ($errors->any())
<div class="admin-alert admin-alert-error">
    <ul style="margin:0; padding-left:1.2em;">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('admin.cashbook.store') }}" class="admin-card">
    @csrf

    <div class="admin-form-grid">
        <div class="admin-form-group">
            <label for="type">Tipe <span class="required">*</span></label>
            <select id="type" name="type" class="admin-select" required>
                <option value="">Pilih Tipe</option>
                <option value="masuk" @selected(old('type')==='masuk' )>Masuk</option>
                <option value="keluar" @selected(old('type')==='keluar' )>Keluar</option>
            </select>
        </div>
        <div class="admin-form-group">
            <label for="tanggal">Tanggal <span class="required">*</span></label>
            <input type="date" id="tanggal" name="tanggal" class="admin-input"
                value="{{ old('tanggal', date('Y-m-d')) }}" required>
        </div>
    </div>

    <div class="admin-form-group">
        <label for="pihak">Pihak <span class="required">*</span></label>
        <input type="text" id="pihak" name="pihak" class="admin-input" value="{{ old('pihak') }}"
            placeholder="Nama vendor / penerima / pemberi dana" required>
    </div>

    <div class="admin-form-grid">
        <div class="admin-form-group">
            <label for="jumlah_uang">Jumlah <span class="required">*</span></label>
            <input type="number" step="0.01" min="0" id="jumlah_uang" name="jumlah_uang" class="admin-input"
                value="{{ old('jumlah_uang') }}" required>
        </div>
        <div class="admin-form-group">
            <label for="terbilang">Terbilang <span class="required">*</span></label>
            <input type="text" id="terbilang" name="terbilang" class="admin-input" value="{{ old('terbilang') }}"
                placeholder="mis. Lima juta rupiah" required>
        </div>
    </div>

    <div class="admin-form-group">
        <label for="keterangan">Keterangan</label>
        <textarea id="keterangan" name="keterangan" class="admin-textarea" rows="3">{{ old('keterangan') }}</textarea>
    </div>

    <div class="admin-form-actions">
        <a href="{{ route('admin.cashbook.index') }}" class="btn-outline">Batal</a>
        <button type="submit" class="btn-primary">Simpan</button>
    </div>
</form>
@endsection
@extends('layouts.admin')
@section('title', 'Edit Laporan Belanja')
@section('content')

<div class="admin-page-head">
    <h2>Edit Laporan · {{ $er->nomor_invoice ?? 'LPJ #' . $er->id }}</h2>
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

<form action="{{ route('admin.expense-reports.update', $er) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="admin-form-grid" style="grid-template-columns:repeat(2,1fr);margin-bottom:20px;">
        <div>
            <label class="admin-label">No. Invoice</label>
            <input type="text" name="nomor_invoice" value="{{ old('nomor_invoice', $er->nomor_invoice) }}"
                class="admin-input">
        </div>
        <div>
            <label class="admin-label">Nama Vendor</label>
            <input type="text" name="nama_vendor" value="{{ old('nama_vendor', $er->nama_vendor) }}"
                class="admin-input">
        </div>
        <div>
            <label class="admin-label">Tanggal Transaksi</label>
            <input type="date" name="tanggal_transaksi"
                value="{{ old('tanggal_transaksi', optional($er->tanggal_transaksi)->format('Y-m-d')) }}" required
                class="admin-input">
        </div>
        <div>
            <label class="admin-label">Nominal Realisasi</label>
            <input type="number" step="0.01" name="nominal_realisasi"
                value="{{ old('nominal_realisasi', $er->nominal_realisasi) }}" required class="admin-input">
        </div>
    </div>

    <div class="admin-card admin-card-pad" style="margin-bottom:20px;">
        <h3 style="margin-top:0;">Kelengkapan Lampiran</h3>
        <div class="admin-form-grid" style="grid-template-columns:repeat(2,1fr);">
            <div>
                <label class="admin-label">Invoice</label>
                <select name="lamp_invoice" class="admin-select">
                    <option value="1" @selected(old('lamp_invoice', $er->lamp_invoice)==1)>Ya</option>
                    <option value="0" @selected(old('lamp_invoice', $er->lamp_invoice)==0)>Tidak</option>
                </select>
            </div>
            <div>
                <label class="admin-label">Bukti Transfer</label>
                <select name="lamp_bukti_transfer" class="admin-select">
                    <option value="1" @selected(old('lamp_bukti_transfer', $er->lamp_bukti_transfer)==1)>Ya</option>
                    <option value="0" @selected(old('lamp_bukti_transfer', $er->lamp_bukti_transfer)==0)>Tidak</option>
                </select>
            </div>
            <div>
                <label class="admin-label">Kartu Garansi</label>
                <select name="lamp_kartu_garansi" class="admin-select">
                    <option value="1" @selected(old('lamp_kartu_garansi', $er->lamp_kartu_garansi)==1)>Ya</option>
                    <option value="0" @selected(old('lamp_kartu_garansi', $er->lamp_kartu_garansi)==0)>Tidak</option>
                </select>
            </div>
            <div>
                <label class="admin-label">Serah Terima</label>
                <select name="lamp_serah_terima" class="admin-select">
                    <option value="1" @selected(old('lamp_serah_terima', $er->lamp_serah_terima)==1)>Ya</option>
                    <option value="0" @selected(old('lamp_serah_terima', $er->lamp_serah_terima)==0)>Tidak</option>
                </select>
            </div>
            <div style="grid-column:span 2;">
                <label class="admin-label">Lampiran Lainnya</label>
                <input type="text" name="lamp_lainnya" value="{{ old('lamp_lainnya', $er->lamp_lainnya) }}"
                    class="admin-input">
            </div>
        </div>
    </div>

    <div class="admin-form-grid" style="margin-bottom:20px;">
        <div>
            <label class="admin-label">Catatan</label>
            <textarea name="catatan" class="admin-textarea">{{ old('catatan', $er->catatan) }}</textarea>
        </div>
    </div>

    <div class="admin-form-actions" style="justify-content:flex-end;">
        <a href="{{ route('admin.expense-reports.show', $er) }}" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary ripple">Simpan Perubahan</button>
    </div>
</form>
@endsection
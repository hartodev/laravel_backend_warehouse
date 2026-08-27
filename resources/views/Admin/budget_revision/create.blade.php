@extends('layouts.admin')
@section('title', 'Ajukan Revisi Anggaran')
@section('content')

<div class="admin-page-head">
    <h2>Ajukan Revisi Anggaran</h2>
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

<form action="{{ route('admin.budget-revisions.store') }}" method="POST" class="admin-card admin-card-pad">
    @csrf

    <div class="admin-form-grid" style="grid-template-columns:repeat(2,1fr);">
        <div class="admin-form-group">
            <label for="budget_request_id">ID RAB Terkait (opsional)</label>
            <input type="number" id="budget_request_id" name="budget_request_id" class="admin-input"
                value="{{ old('budget_request_id', request('budget_request_id')) }}">
        </div>
        <div class="admin-form-group">
            <label for="expense_report_id">ID Laporan Pengeluaran (opsional)</label>
            <input type="number" id="expense_report_id" name="expense_report_id" class="admin-input"
                value="{{ old('expense_report_id', request('expense_report_id')) }}">
        </div>
    </div>

    <div class="admin-form-grid" style="grid-template-columns:repeat(2,1fr);">
        <div class="admin-form-group">
            <label for="akun_terdampak">Akun Terdampak</label>
            <input type="text" id="akun_terdampak" name="akun_terdampak" class="admin-input" required
                value="{{ old('akun_terdampak') }}">
        </div>
        <div class="admin-form-group">
            <label for="kode_akun">Kode Akun (opsional)</label>
            <input type="text" id="kode_akun" name="kode_akun" class="admin-input" value="{{ old('kode_akun') }}">
        </div>
    </div>

    <div class="admin-form-grid" style="grid-template-columns:repeat(2,1fr);">
        <div class="admin-form-group">
            <label for="anggaran_awal">Anggaran Awal</label>
            <input type="number" step="0.01" min="0" id="anggaran_awal" name="anggaran_awal" class="admin-input"
                required value="{{ old('anggaran_awal') }}">
        </div>
        <div class="admin-form-group">
            <label for="realisasi">Realisasi</label>
            <input type="number" step="0.01" min="0" id="realisasi" name="realisasi" class="admin-input" required
                value="{{ old('realisasi') }}">
        </div>
    </div>

    <div class="admin-form-grid" style="grid-template-columns:repeat(2,1fr);">
        <div class="admin-form-group">
            <label for="jenis_perubahan">Jenis Perubahan</label>
            <select id="jenis_perubahan" name="jenis_perubahan" class="admin-select" required>
                <option value="">-- Pilih --</option>
                <option value="tambahan" @selected(old('jenis_perubahan')==='tambahan' )>Tambahan</option>
                <option value="pengurangan" @selected(old('jenis_perubahan')==='pengurangan' )>Pengurangan</option>
            </select>
        </div>
        <div class="admin-form-group">
            <label for="nominal_perubahan">Nominal Perubahan</label>
            <input type="number" step="0.01" min="0" id="nominal_perubahan" name="nominal_perubahan" class="admin-input"
                required value="{{ old('nominal_perubahan') }}">
        </div>
    </div>

    <div class="admin-form-group">
        <label for="alasan_revisi">Alasan Revisi</label>
        <textarea id="alasan_revisi" name="alasan_revisi" class="admin-textarea" rows="4"
            required>{{ old('alasan_revisi') }}</textarea>
    </div>

    <div class="admin-form-actions" style="justify-content:flex-end;">
        <a href="{{ route('admin.budget-revisions.index') }}" class="btn-outline">Batal</a>
        <button type="submit" class="btn-primary">Ajukan Revisi</button>
    </div>
</form>
@endsection
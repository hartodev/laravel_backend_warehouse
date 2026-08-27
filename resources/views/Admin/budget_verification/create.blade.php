@extends('layouts.admin')
@section('title', 'Verifikasi RAB Baru')
@section('content')

<div class="admin-page-head">
    <h2>Verifikasi RAB — {{ $budgetRequest->nomor_form }}</h2>
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

<div class="admin-card" style="margin-bottom:16px;">
    <div class="admin-detail-grid">
        <div>
            <span class="cell-muted">Nomor Form</span>
            <p class="cell-mono">{{ $budgetRequest->nomor_form }}</p>
        </div>
        <div>
            <span class="cell-muted">Total Estimasi</span>
            <p class="cell-mono">Rp {{ number_format($budgetRequest->total_estimasi ?? 0, 0, ',', '.') }}</p>
        </div>
        <div>
            <span class="cell-muted">Status</span>
            <p><span class="admin-badge admin-badge-warning">{{ ucfirst($budgetRequest->status) }}</span></p>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.budget-verifications.store') }}" class="admin-card">
    @csrf
    <input type="hidden" name="budget_request_id" value="{{ $budgetRequest->id }}">

    <h3 class="admin-section-title">Checklist Dokumen</h3>
    <div class="admin-form-grid">
        <label class="admin-checkbox">
            <input type="checkbox" name="doc_form_lengkap" value="1" @checked(old('doc_form_lengkap'))>
            Form Lengkap
        </label>
        <label class="admin-checkbox">
            <input type="checkbox" name="doc_surat_justifikasi" value="1" @checked(old('doc_surat_justifikasi'))>
            Surat Justifikasi
        </label>
        <label class="admin-checkbox">
            <input type="checkbox" name="doc_estimasi_vendor" value="1" @checked(old('doc_estimasi_vendor'))>
            Estimasi Vendor
        </label>
        <label class="admin-checkbox">
            <input type="checkbox" name="doc_spesifikasi_teknis" value="1" @checked(old('doc_spesifikasi_teknis'))>
            Spesifikasi Teknis
        </label>
    </div>

    <div class="admin-form-group">
        <label for="doc_lainnya">Dokumen Lainnya</label>
        <input type="text" id="doc_lainnya" name="doc_lainnya" class="admin-input" value="{{ old('doc_lainnya') }}">
    </div>

    <h3 class="admin-section-title">Analisa Finance</h3>
    <div class="admin-form-group">
        <label for="cek_anggaran">Cek Anggaran</label>
        <textarea id="cek_anggaran" name="cek_anggaran" class="admin-textarea"
            rows="3">{{ old('cek_anggaran') }}</textarea>
    </div>
    <div class="admin-form-group">
        <label for="analisa_cashflow">Analisa Cashflow</label>
        <textarea id="analisa_cashflow" name="analisa_cashflow" class="admin-textarea"
            rows="3">{{ old('analisa_cashflow') }}</textarea>
    </div>

    <h3 class="admin-section-title">Rekomendasi</h3>
    <div class="admin-form-grid">
        <div class="admin-form-group">
            <label for="rekomendasi">Rekomendasi <span class="required">*</span></label>
            <select id="rekomendasi" name="rekomendasi" class="admin-select" required>
                <option value="">Pilih Rekomendasi</option>
                <option value="setuju" @selected(old('rekomendasi')==='setuju' )>Setuju</option>
                <option value="tunda" @selected(old('rekomendasi')==='tunda' )>Tunda</option>
                <option value="tolak" @selected(old('rekomendasi')==='tolak' )>Tolak</option>
            </select>
        </div>
        <div class="admin-form-group">
            <label for="nominal_rekomendasi">Nominal Rekomendasi</label>
            <input type="number" step="0.01" min="0" id="nominal_rekomendasi" name="nominal_rekomendasi"
                class="admin-input" value="{{ old('nominal_rekomendasi') }}">
        </div>
    </div>

    <div class="admin-form-group">
        <label for="catatan_finance">Catatan Finance</label>
        <textarea id="catatan_finance" name="catatan_finance" class="admin-textarea"
            rows="3">{{ old('catatan_finance') }}</textarea>
    </div>

    <div class="admin-form-actions">
        <a href="{{ url()->previous() }}" class="btn-outline">Batal</a>
        <button type="submit" class="btn-primary">Simpan Verifikasi</button>
    </div>
</form>
@endsection
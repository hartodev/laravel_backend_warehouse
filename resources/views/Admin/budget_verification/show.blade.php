@extends('layouts.admin')
@section('title', 'Detail Verifikasi RAB')
@section('content')

<div class="admin-page-head">
    <h2>Detail Verifikasi — {{ $budgetVerification->budgetRequest->nomor_form ?? '-' }}</h2>
</div>

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
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
            <span class="cell-muted">RAB Terkait</span>
            <p class="cell-mono">{{ $budgetVerification->budgetRequest->nomor_form ?? '-' }}</p>
        </div>
        <div>
            <span class="cell-muted">Finance</span>
            <p>{{ $budgetVerification->finance->name ?? '-' }}</p>
        </div>
        <div>
            <span class="cell-muted">Waktu Verifikasi</span>
            <p class="cell-muted">{{ optional($budgetVerification->verified_at)->format('d M Y H:i') ?? '-' }}</p>
        </div>
        <div>
            <span class="cell-muted">Rekomendasi</span>
            <p>
                @if($budgetVerification->rekomendasi === 'setuju')
                <span class="admin-badge admin-badge-success">Setuju</span>
                @elseif($budgetVerification->rekomendasi === 'tunda')
                <span class="admin-badge admin-badge-warning">Tunda</span>
                @else
                <span class="admin-badge admin-badge-danger">Tolak</span>
                @endif
            </p>
        </div>
        <div>
            <span class="cell-muted">Nominal Rekomendasi</span>
            <p class="cell-mono">Rp {{ number_format($budgetVerification->nominal_rekomendasi ?? 0, 0, ',', '.') }}</p>
        </div>
    </div>
</div>

<div class="admin-card" style="margin-bottom:16px;">
    <h3 class="admin-section-title">Checklist Dokumen</h3>
    <div class="admin-form-grid">
        <span
            class="admin-badge {{ $budgetVerification->doc_form_lengkap ? 'admin-badge-success' : 'admin-badge-danger' }}">
            Form Lengkap: {{ $budgetVerification->doc_form_lengkap ? 'Ya' : 'Tidak' }}
        </span>
        <span
            class="admin-badge {{ $budgetVerification->doc_surat_justifikasi ? 'admin-badge-success' : 'admin-badge-danger' }}">
            Surat Justifikasi: {{ $budgetVerification->doc_surat_justifikasi ? 'Ya' : 'Tidak' }}
        </span>
        <span
            class="admin-badge {{ $budgetVerification->doc_estimasi_vendor ? 'admin-badge-success' : 'admin-badge-danger' }}">
            Estimasi Vendor: {{ $budgetVerification->doc_estimasi_vendor ? 'Ya' : 'Tidak' }}
        </span>
        <span
            class="admin-badge {{ $budgetVerification->doc_spesifikasi_teknis ? 'admin-badge-success' : 'admin-badge-danger' }}">
            Spesifikasi Teknis: {{ $budgetVerification->doc_spesifikasi_teknis ? 'Ya' : 'Tidak' }}
        </span>
    </div>
    @if($budgetVerification->doc_lainnya)
    <p class="cell-muted" style="margin-top:12px;">Dokumen lainnya: {{ $budgetVerification->doc_lainnya }}</p>
    @endif
</div>

<div class="admin-card" style="margin-bottom:16px;">
    <h3 class="admin-section-title">Analisa Finance</h3>
    <p><strong>Cek Anggaran:</strong><br>{{ $budgetVerification->cek_anggaran ?? '-' }}</p>
    <p style="margin-top:8px;"><strong>Analisa Cashflow:</strong><br>{{ $budgetVerification->analisa_cashflow ?? '-' }}
    </p>
    <p style="margin-top:8px;"><strong>Catatan Finance:</strong><br>{{ $budgetVerification->catatan_finance ?? '-' }}
    </p>
</div>

<form method="POST" action="{{ route('admin.budget-verifications.update', $budgetVerification) }}" class="admin-card">
    @csrf
    @method('PUT')

    <h3 class="admin-section-title">Update Verifikasi</h3>

    <div class="admin-form-grid">
        <label class="admin-checkbox">
            <input type="checkbox" name="doc_form_lengkap" value="1" @checked($budgetVerification->doc_form_lengkap)>
            Form Lengkap
        </label>
        <label class="admin-checkbox">
            <input type="checkbox" name="doc_surat_justifikasi" value="1"
                @checked($budgetVerification->doc_surat_justifikasi)>
            Surat Justifikasi
        </label>
        <label class="admin-checkbox">
            <input type="checkbox" name="doc_estimasi_vendor" value="1"
                @checked($budgetVerification->doc_estimasi_vendor)>
            Estimasi Vendor
        </label>
        <label class="admin-checkbox">
            <input type="checkbox" name="doc_spesifikasi_teknis" value="1"
                @checked($budgetVerification->doc_spesifikasi_teknis)>
            Spesifikasi Teknis
        </label>
    </div>

    <div class="admin-form-group">
        <label for="doc_lainnya">Dokumen Lainnya</label>
        <input type="text" id="doc_lainnya" name="doc_lainnya" class="admin-input"
            value="{{ old('doc_lainnya', $budgetVerification->doc_lainnya) }}">
    </div>

    <div class="admin-form-group">
        <label for="cek_anggaran">Cek Anggaran</label>
        <textarea id="cek_anggaran" name="cek_anggaran" class="admin-textarea"
            rows="3">{{ old('cek_anggaran', $budgetVerification->cek_anggaran) }}</textarea>
    </div>
    <div class="admin-form-group">
        <label for="analisa_cashflow">Analisa Cashflow</label>
        <textarea id="analisa_cashflow" name="analisa_cashflow" class="admin-textarea"
            rows="3">{{ old('analisa_cashflow', $budgetVerification->analisa_cashflow) }}</textarea>
    </div>

    <div class="admin-form-grid">
        <div class="admin-form-group">
            <label for="rekomendasi">Rekomendasi <span class="required">*</span></label>
            <select id="rekomendasi" name="rekomendasi" class="admin-select" required>
                <option value="setuju" @selected($budgetVerification->rekomendasi === 'setuju' )>Setuju</option>
                <option value="tunda" @selected($budgetVerification->rekomendasi === 'tunda' )>Tunda</option>
                <option value="tolak" @selected($budgetVerification->rekomendasi === 'tolak' )>Tolak</option>
            </select>
        </div>
        <div class="admin-form-group">
            <label for="nominal_rekomendasi">Nominal Rekomendasi</label>
            <input type="number" step="0.01" min="0" id="nominal_rekomendasi" name="nominal_rekomendasi"
                class="admin-input" value="{{ old('nominal_rekomendasi', $budgetVerification->nominal_rekomendasi) }}">
        </div>
    </div>

    <div class="admin-form-group">
        <label for="catatan_finance">Catatan Finance</label>
        <textarea id="catatan_finance" name="catatan_finance" class="admin-textarea"
            rows="3">{{ old('catatan_finance', $budgetVerification->catatan_finance) }}</textarea>
    </div>

    <div class="admin-form-actions">
        <a href="{{ route('admin.budget-verifications.index') }}" class="btn-outline">Kembali</a>
        <button type="submit" class="btn-primary">Update Verifikasi</button>
    </div>
</form>
@endsection
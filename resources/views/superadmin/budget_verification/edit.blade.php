{{-- resources/views/superadmin/budget_verification/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Verifikasi Anggaran')

@section('breadcrumb')
<a href="{{ route('superadmin.budget-verifications.index') }}" class="text-gray-500 hover:text-gray-700">Verifikasi
    Anggaran</a>
<span class="text-gray-400 mx-1">/</span>
<a href="{{ route('superadmin.budget-verifications.show', $budgetVerification) }}"
    class="text-gray-500 hover:text-gray-700">Detail</a>
<span class="text-gray-400 mx-1">/</span>
<span class="text-gray-700 font-medium">Edit</span>
@endsection

@section('content')
<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-900">Edit Verifikasi Anggaran</h1>
    <p class="text-sm text-gray-500 mt-0.5">
        Pengajuan:
        <a href="{{ route('superadmin.budget-requests.show', $budgetVerification->budgetRequest) }}"
            class="text-blue-600 hover:underline font-mono">
            {{ $budgetVerification->budgetRequest?->nomor_form ?? '-' }}
        </a>
    </p>
</div>

@if ($errors->any())
<div class="mb-5 rounded-md bg-red-50 border border-red-200 p-4">
    <ul class="text-sm text-red-700 list-disc list-inside space-y-1">
        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('superadmin.budget-verifications.update', $budgetVerification) }}">
    @csrf @method('PUT')

    {{-- Info Pengajuan (readonly) --}}
    @if ($budgetVerification->budgetRequest)
    <div class="card mb-5">
        <div class="card-header">
            <h2 class="font-semibold text-gray-800">Pengajuan Terkait</h2>
        </div>
        <div class="card-body">
            <div class="rounded-md bg-blue-50 border border-blue-200 p-4 text-sm text-blue-800">
                <div class="flex justify-between mb-1">
                    <span>Nomor Form:</span>
                    <strong class="font-mono">{{ $budgetVerification->budgetRequest->nomor_form }}</strong>
                </div>
                <div class="flex justify-between mb-1">
                    <span>Divisi:</span>
                    <strong>{{ $budgetVerification->budgetRequest->divisi }}</strong>
                </div>
                <div class="flex justify-between">
                    <span>Total Estimasi:</span>
                    <strong>Rp
                        {{ number_format($budgetVerification->budgetRequest->total_estimasi, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Checklist Dokumen --}}
    <div class="card mb-5">
        <div class="card-header">
            <h2 class="font-semibold text-gray-800">Kelengkapan Dokumen</h2>
        </div>
        <div class="card-body space-y-3">
            @php
            $checks = [
            'doc_form_lengkap' => 'Form pengajuan lengkap',
            'doc_surat_justifikasi' => 'Surat justifikasi terlampir',
            'doc_estimasi_vendor' => 'Estimasi vendor terlampir',
            'doc_spesifikasi_teknis' => 'Spesifikasi teknis tersedia',
            ];
            @endphp
            @foreach ($checks as $name => $label)
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="{{ $name }}" value="1" class="rounded border-gray-300 text-blue-600"
                    {{ old($name, $budgetVerification->$name) ? 'checked' : '' }}>
                <span class="text-sm text-gray-700">{{ $label }}</span>
            </label>
            @endforeach
            <div>
                <label class="form-label">Dokumen Lainnya</label>
                <textarea name="doc_lainnya" rows="2"
                    class="form-textarea">{{ old('doc_lainnya', $budgetVerification->doc_lainnya) }}</textarea>
            </div>
        </div>
    </div>

    {{-- Analisa Finance --}}
    <div class="card mb-5">
        <div class="card-header">
            <h2 class="font-semibold text-gray-800">Analisa Finance</h2>
        </div>
        <div class="card-body grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="form-label">Cek Anggaran</label>
                <textarea name="cek_anggaran" rows="3"
                    class="form-textarea">{{ old('cek_anggaran', $budgetVerification->cek_anggaran) }}</textarea>
            </div>
            <div class="sm:col-span-2">
                <label class="form-label">Analisa Cashflow</label>
                <textarea name="analisa_cashflow" rows="3"
                    class="form-textarea">{{ old('analisa_cashflow', $budgetVerification->analisa_cashflow) }}</textarea>
            </div>
            <div>
                <label class="form-label">Rekomendasi <span class="text-red-500">*</span></label>
                <select name="rekomendasi" class="form-select" required>
                    <option value="setuju"
                        {{ old('rekomendasi', $budgetVerification->rekomendasi) === 'setuju' ? 'selected' : '' }}>Setuju
                    </option>
                    <option value="tunda"
                        {{ old('rekomendasi', $budgetVerification->rekomendasi) === 'tunda'  ? 'selected' : '' }}>Tunda
                    </option>
                    <option value="tolak"
                        {{ old('rekomendasi', $budgetVerification->rekomendasi) === 'tolak'  ? 'selected' : '' }}>Tolak
                    </option>
                </select>
            </div>
            <div>
                <label class="form-label">Nominal Rekomendasi</label>
                <input type="number" step="0.01" min="0" name="nominal_rekomendasi"
                    value="{{ old('nominal_rekomendasi', $budgetVerification->nominal_rekomendasi) }}"
                    class="form-input">
            </div>
            <div class="sm:col-span-2">
                <label class="form-label">Catatan Finance</label>
                <textarea name="catatan_finance" rows="3"
                    class="form-textarea">{{ old('catatan_finance', $budgetVerification->catatan_finance) }}</textarea>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('superadmin.budget-verifications.show', $budgetVerification) }}"
            class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </div>
</form>
@endsection
{{-- budget_verifications/create.blade.php --}}
@extends('superadmin.layouts.app')
@section('title','Buat Verifikasi Anggaran')
@section('breadcrumb')
<a href="{{ route('superadmin.budget-verifications.index') }}" class="hover:text-primary-700">Verifikasi Anggaran</a>
<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-700 font-medium">Buat Baru</span>
@endsection

@section('content')
<div class="max-w-xl">
<div class="card">
    <div class="card-header"><h2 class="font-semibold text-gray-900">Form Verifikasi Anggaran</h2></div>
    <form method="POST" action="{{ route('superadmin.budget-verifications.store') }}">
    @csrf
    <div class="card-body space-y-4">
        <div>
            <label class="form-label">Pengajuan Anggaran <span class="text-red-500">*</span></label>
            <select name="budget_request_id" required class="form-select" x-data x-on:change="$refs.anggaran.value = ($event.target.selectedOptions[0]?.dataset.biaya ?? '')">
                <option value="">— Pilih —</option>
                @foreach($budgetRequests as $br)
                <option value="{{ $br->id }}" data-biaya="{{ $br->estimasi_biaya }}" {{ old('budget_request_id') == $br->id ? 'selected' : '' }}>
                    {{ $br->nomor_form }} — {{ $br->nama_item }}
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Anggaran (referensi)</label>
            <input type="text" x-ref="anggaran" class="form-input bg-gray-50" readonly placeholder="Pilih pengajuan dulu...">
        </div>
        <div>
            <label class="form-label">Jumlah Disetujui <span class="text-red-500">*</span></label>
            <input type="number" name="jumlah_disetujui" value="{{ old('jumlah_disetujui') }}" required min="0" class="form-input">
        </div>
        <div>
            <label class="form-label">Status <span class="text-red-500">*</span></label>
            <select name="status" required class="form-select">
                <option value="approved" {{ old('status','approved')==='approved'?'selected':'' }}>Approved (Penuh)</option>
                <option value="partial" {{ old('status')==='partial'?'selected':'' }}>Partial (Sebagian)</option>
                <option value="rejected" {{ old('status')==='rejected'?'selected':'' }}>Rejected (Ditolak)</option>
            </select>
        </div>
        <div>
            <label class="form-label">Catatan</label>
            <textarea name="catatan" rows="3" class="form-textarea">{{ old('catatan') }}</textarea>
        </div>
    </div>
    <div class="card-body border-t flex justify-end gap-3">
        <a href="{{ route('superadmin.budget-verifications.index') }}" class="btn-secondary btn">Batal</a>
        <button type="submit" class="btn-primary btn">Simpan Verifikasi</button>
    </div>
    </form>
</div>
</div>
@endsection

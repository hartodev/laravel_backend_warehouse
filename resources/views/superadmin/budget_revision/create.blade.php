{{-- budget_revisions/create.blade.php --}}
@extends('superadmin.layouts.app')
@section('title','Ajukan Revisi Anggaran')
@section('breadcrumb')
<a href="{{ route('superadmin.budget-revisions.index') }}" class="hover:text-primary-700">Revisi Anggaran</a>
<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-700 font-medium">Ajukan Baru</span>
@endsection

@section('content')
<div class="max-w-xl">
<div class="card" x-data="{ awal: 0, jenis: 'tambahan', nominal: 0, get baru() { return this.jenis==='tambahan' ? this.awal+this.nominal : this.awal-this.nominal; } }">
    <div class="card-header"><h2 class="font-semibold text-gray-900">Form Revisi Anggaran</h2></div>
    <form method="POST" action="{{ route('superadmin.budget-revisions.store') }}">
    @csrf
    <div class="card-body space-y-4">
        <div>
            <label class="form-label">Pengajuan Anggaran Terkait</label>
            <select name="budget_request_id" class="form-select">
                <option value="">— Opsional —</option>
                @foreach($budgetRequests as $br)
                <option value="{{ $br->id }}" {{ old('budget_request_id') == $br->id ? 'selected' : '' }}>{{ $br->nomor_form }} — {{ $br->nama_item }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="form-label">Kode Akun</label>
                <input type="text" name="kode_akun" value="{{ old('kode_akun') }}" class="form-input font-mono" placeholder="5-101">
            </div>
            <div>
                <label class="form-label">Akun Terdampak <span class="text-red-500">*</span></label>
                <input type="text" name="akun_terdampak" value="{{ old('akun_terdampak') }}" required class="form-input">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="form-label">Anggaran Awal <span class="text-red-500">*</span></label>
                <input type="number" name="anggaran_awal" x-model.number="awal" value="{{ old('anggaran_awal') }}" required min="0" class="form-input">
            </div>
            <div>
                <label class="form-label">Realisasi <span class="text-red-500">*</span></label>
                <input type="number" name="realisasi" value="{{ old('realisasi') }}" required min="0" class="form-input">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="form-label">Jenis Perubahan <span class="text-red-500">*</span></label>
                <select name="jenis_perubahan" x-model="jenis" required class="form-select">
                    <option value="tambahan">Tambahan</option>
                    <option value="pengurangan">Pengurangan</option>
                </select>
            </div>
            <div>
                <label class="form-label">Nominal Perubahan <span class="text-red-500">*</span></label>
                <input type="number" name="nominal_perubahan" x-model.number="nominal" value="{{ old('nominal_perubahan') }}" required min="0" class="form-input">
            </div>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm">
            <p class="text-blue-700">Anggaran Baru (Perkiraan):
                <strong class="text-blue-900" x-text="'Rp '+baru.toLocaleString('id-ID')"></strong>
            </p>
        </div>
        <div>
            <label class="form-label">Alasan Revisi <span class="text-red-500">*</span></label>
            <textarea name="alasan_revisi" rows="3" required class="form-textarea" placeholder="Jelaskan alasan perlu revisi anggaran...">{{ old('alasan_revisi') }}</textarea>
        </div>
    </div>
    <div class="card-body border-t flex justify-end gap-3">
        <a href="{{ route('superadmin.budget-revisions.index') }}" class="btn-secondary btn">Batal</a>
        <button type="submit" class="btn-primary btn">Ajukan Revisi</button>
    </div>
    </form>
</div>
</div>
@endsection

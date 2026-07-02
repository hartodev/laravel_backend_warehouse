{{-- resources/views/superadmin/budget_verification/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Buat Verifikasi Anggaran')

@section('breadcrumb')
    <a href="{{ route('budget-verifications.index') }}" class="text-gray-500 hover:text-gray-700">Verifikasi Anggaran</a>
    <span class="text-gray-400 mx-1">/</span>
    <span class="text-gray-700 font-medium">Buat Verifikasi</span>
@endsection

@section('content')
<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-900">Buat Verifikasi Anggaran</h1>
    <p class="text-sm text-gray-500 mt-0.5">Isi form verifikasi finance untuk pengajuan yang sudah disetujui</p>
</div>

@if ($errors->any())
    <div class="mb-5 rounded-md bg-red-50 border border-red-200 p-4">
        <ul class="text-sm text-red-700 list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('budget-verifications.store') }}">
    @csrf

    {{-- Pilih Pengajuan --}}
    <div class="card mb-5">
        <div class="card-header"><h2 class="font-semibold text-gray-800">Pilih Pengajuan Anggaran</h2></div>
        <div class="card-body">
            <div class="max-w-lg">
                <label class="form-label">Nomor Form Pengajuan <span class="text-red-500">*</span></label>
                <select name="budget_request_id" id="budget_request_id" class="form-select" required onchange="loadBudgetDetail(this)">
                    <option value="">-- Pilih Pengajuan --</option>
                    @foreach ($budgetRequests as $br)
                        <option value="{{ $br->id }}"
                            data-total="{{ $br->total_estimasi }}"
                            data-items="{{ $br->items->pluck('nama_item')->join(', ') }}"
                            {{ old('budget_request_id', request('budget_request_id')) == $br->id ? 'selected' : '' }}>
                            {{ $br->nomor_form }} — Rp {{ number_format($br->total_estimasi, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Info Box Pengajuan --}}
            <div id="br-info" class="mt-4 hidden rounded-md bg-blue-50 border border-blue-200 p-4 text-sm text-blue-800">
                <div class="flex justify-between mb-1">
                    <span>Total Estimasi:</span>
                    <strong id="br-total">-</strong>
                </div>
                <div>
                    <span>Item:</span>
                    <span id="br-items" class="ml-1 text-blue-700"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Checklist Dokumen --}}
    <div class="card mb-5">
        <div class="card-header"><h2 class="font-semibold text-gray-800">Kelengkapan Dokumen</h2></div>
        <div class="card-body space-y-3">
            @php
                $checks = [
                    'doc_form_lengkap'       => 'Form pengajuan lengkap',
                    'doc_surat_justifikasi'  => 'Surat justifikasi terlampir',
                    'doc_estimasi_vendor'    => 'Estimasi vendor terlampir',
                    'doc_spesifikasi_teknis' => 'Spesifikasi teknis tersedia',
                ];
            @endphp
            @foreach ($checks as $name => $label)
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="{{ $name }}" value="1" class="rounded border-gray-300 text-blue-600"
                    {{ old($name) ? 'checked' : '' }}>
                <span class="text-sm text-gray-700">{{ $label }}</span>
            </label>
            @endforeach
            <div>
                <label class="form-label">Dokumen Lainnya</label>
                <textarea name="doc_lainnya" rows="2" class="form-textarea" placeholder="Sebutkan dokumen lain jika ada...">{{ old('doc_lainnya') }}</textarea>
            </div>
        </div>
    </div>

    {{-- Analisa Finance --}}
    <div class="card mb-5">
        <div class="card-header"><h2 class="font-semibold text-gray-800">Analisa Finance</h2></div>
        <div class="card-body grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="form-label">Cek Anggaran</label>
                <textarea name="cek_anggaran" rows="3" class="form-textarea" placeholder="Pengecekan ketersediaan anggaran...">{{ old('cek_anggaran') }}</textarea>
            </div>
            <div class="sm:col-span-2">
                <label class="form-label">Analisa Cashflow</label>
                <textarea name="analisa_cashflow" rows="3" class="form-textarea" placeholder="Analisa dampak terhadap cashflow...">{{ old('analisa_cashflow') }}</textarea>
            </div>
            <div>
                <label class="form-label">Rekomendasi <span class="text-red-500">*</span></label>
                <select name="rekomendasi" class="form-select" required>
                    <option value="">-- Pilih --</option>
                    <option value="setuju" {{ old('rekomendasi') === 'setuju' ? 'selected' : '' }}>Setuju</option>
                    <option value="tunda"  {{ old('rekomendasi') === 'tunda'  ? 'selected' : '' }}>Tunda</option>
                    <option value="tolak"  {{ old('rekomendasi') === 'tolak'  ? 'selected' : '' }}>Tolak</option>
                </select>
            </div>
            <div>
                <label class="form-label">Nominal Rekomendasi</label>
                <input type="number" step="0.01" min="0" name="nominal_rekomendasi"
                    value="{{ old('nominal_rekomendasi') }}" class="form-input" placeholder="Kosongkan jika sama dengan estimasi">
            </div>
            <div class="sm:col-span-2">
                <label class="form-label">Catatan Finance</label>
                <textarea name="catatan_finance" rows="3" class="form-textarea" placeholder="Catatan tambahan dari finance...">{{ old('catatan_finance') }}</textarea>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('budget-verifications.index') }}" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Verifikasi</button>
    </div>
</form>

<script>
function loadBudgetDetail(sel) {
    const opt = sel.options[sel.selectedIndex];
    const info = document.getElementById('br-info');
    if (!opt.value) { info.classList.add('hidden'); return; }
    document.getElementById('br-total').textContent = 'Rp ' + parseInt(opt.dataset.total).toLocaleString('id-ID');
    document.getElementById('br-items').textContent = opt.dataset.items || '-';
    info.classList.remove('hidden');
}
// Init on load if old value exists
window.addEventListener('DOMContentLoaded', () => {
    const sel = document.getElementById('budget_request_id');
    if (sel.value) loadBudgetDetail(sel);
});
</script>
@endsection

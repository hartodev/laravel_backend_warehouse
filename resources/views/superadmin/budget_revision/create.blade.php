{{-- resources/views/superadmin/budget_revision/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Ajukan Revisi Anggaran')

@section('breadcrumb')
    <a href="{{ route('budget-revisions.index') }}" class="text-gray-500 hover:text-gray-700">Revisi Anggaran</a>
    <span class="text-gray-400 mx-1">/</span>
    <span class="text-gray-700 font-medium">Ajukan Revisi</span>
@endsection

@section('content')
<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-900">Ajukan Revisi Anggaran</h1>
    <p class="text-sm text-gray-500 mt-0.5">Isi form untuk mengajukan perubahan anggaran yang sudah disetujui</p>
</div>

@if ($errors->any())
    <div class="mb-5 rounded-md bg-red-50 border border-red-200 p-4">
        <ul class="text-sm text-red-700 list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('budget-revisions.store') }}">
    @csrf

    {{-- Pilih Pengajuan --}}
    <div class="card mb-5">
        <div class="card-header"><h2 class="font-semibold text-gray-800">Pengajuan Terkait (Opsional)</h2></div>
        <div class="card-body">
            <div class="max-w-lg">
                <label class="form-label">Nomor Form Pengajuan</label>
                <select name="budget_request_id" id="budget_request_id" class="form-select" onchange="loadBudgetDetail(this)">
                    <option value="">-- Tanpa pengajuan spesifik --</option>
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

    {{-- Detail Revisi --}}
    <div class="card mb-5">
        <div class="card-header"><h2 class="font-semibold text-gray-800">Detail Revisi</h2></div>
        <div class="card-body grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="form-label">Akun Terdampak <span class="text-red-500">*</span></label>
                <input type="text" name="akun_terdampak" value="{{ old('akun_terdampak') }}" class="form-input"
                    placeholder="Nama akun yang mengalami perubahan" required>
            </div>
            <div>
                <label class="form-label">Kode Akun</label>
                <input type="text" name="kode_akun" value="{{ old('kode_akun') }}" class="form-input font-mono" placeholder="Contoh: 5-001">
            </div>
            <div>
                <label class="form-label">Jenis Perubahan <span class="text-red-500">*</span></label>
                <select name="jenis_perubahan" class="form-select" required>
                    <option value="">-- Pilih --</option>
                    <option value="tambahan"    {{ old('jenis_perubahan') === 'tambahan'    ? 'selected' : '' }}>Tambahan</option>
                    <option value="pengurangan" {{ old('jenis_perubahan') === 'pengurangan' ? 'selected' : '' }}>Pengurangan</option>
                </select>
            </div>
            <div>
                <label class="form-label">Anggaran Awal <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="anggaran_awal"
                    value="{{ old('anggaran_awal') }}" class="form-input" required oninput="hitungBaru()">
            </div>
            <div>
                <label class="form-label">Realisasi <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="realisasi"
                    value="{{ old('realisasi') }}" class="form-input" required>
            </div>
            <div>
                <label class="form-label">Nominal Perubahan <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="nominal_perubahan"
                    value="{{ old('nominal_perubahan') }}" class="form-input" required oninput="hitungBaru()" id="nominal_perubahan">
            </div>
            <div>
                <label class="form-label">Anggaran Baru (estimasi)</label>
                <div id="anggaran-baru-preview" class="form-input bg-gray-50 text-gray-500 cursor-not-allowed">-</div>
            </div>
            <div class="sm:col-span-2">
                <label class="form-label">Alasan Revisi <span class="text-red-500">*</span></label>
                <textarea name="alasan_revisi" rows="4" class="form-textarea" required
                    placeholder="Jelaskan alasan revisi anggaran secara detail...">{{ old('alasan_revisi') }}</textarea>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('budget-revisions.index') }}" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">Ajukan Revisi</button>
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
    // prefill anggaran awal
    const awalInput = document.querySelector('[name="anggaran_awal"]');
    if (awalInput && !awalInput.value) {
        awalInput.value = opt.dataset.total;
        hitungBaru();
    }
}

function hitungBaru() {
    const awal    = parseFloat(document.querySelector('[name="anggaran_awal"]')?.value) || 0;
    const nominal = parseFloat(document.getElementById('nominal_perubahan')?.value) || 0;
    const jenis   = document.querySelector('[name="jenis_perubahan"]')?.value;
    let baru = jenis === 'pengurangan' ? awal - nominal : awal + nominal;
    document.getElementById('anggaran-baru-preview').textContent = 'Rp ' + baru.toLocaleString('id-ID');
}

document.querySelector('[name="jenis_perubahan"]')?.addEventListener('change', hitungBaru);

window.addEventListener('DOMContentLoaded', () => {
    const sel = document.getElementById('budget_request_id');
    if (sel.value) loadBudgetDetail(sel);
});
</script>
@endsection

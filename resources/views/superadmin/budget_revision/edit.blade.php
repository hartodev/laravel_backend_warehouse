{{-- resources/views/superadmin/budget_revision/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Revisi Anggaran')

@section('breadcrumb')
    <a href="{{ route('superadmin.budget-revisions.index') }}" class="text-gray-500 hover:text-gray-700">Revisi Anggaran</a>
    <span class="text-gray-400 mx-1">/</span>
    <a href="{{ route('superadmin.budget-revisions.show', $budgetRevision) }}"
        class="text-gray-500 hover:text-gray-700">Detail</a>
    <span class="text-gray-400 mx-1">/</span>
    <span class="text-gray-700 font-medium">Edit</span>
@endsection

@section('content')
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">Edit Revisi Anggaran</h1>
        <p class="text-sm text-gray-500 mt-0.5">Hanya revisi dengan status <strong>Pending</strong> yang dapat diubah</p>
    </div>

    @if ($errors->any())
        <div class="mb-5 rounded-md bg-red-50 border border-red-200 p-4">
            <ul class="text-sm text-red-700 list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('superadmin.budget-revisions.update', $budgetRevision) }}">
        @csrf @method('PUT')

        {{-- Info Pengajuan (readonly) --}}
        @if ($budgetRevision->budgetRequest)
            <div class="card mb-5">
                <div class="card-header">
                    <h2 class="font-semibold text-gray-800">Pengajuan Terkait</h2>
                </div>
                <div class="card-body">
                    <div class="rounded-md bg-blue-50 border border-blue-200 p-4 text-sm text-blue-800">
                        <div class="flex justify-between mb-1">
                            <span>Nomor Form:</span>
                            <strong class="font-mono">{{ $budgetRevision->budgetRequest->nomor_form }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span>Total Estimasi:</span>
                            <strong>Rp
                                {{ number_format($budgetRevision->budgetRequest->total_estimasi, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Detail Revisi --}}
        <div class="card mb-5">
            <div class="card-header">
                <h2 class="font-semibold text-gray-800">Detail Revisi</h2>
            </div>
            <div class="card-body grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="form-label">Akun Terdampak <span class="text-red-500">*</span></label>
                    <input type="text" name="akun_terdampak"
                        value="{{ old('akun_terdampak', $budgetRevision->akun_terdampak) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Kode Akun</label>
                    <input type="text" name="kode_akun" value="{{ old('kode_akun', $budgetRevision->kode_akun) }}"
                        class="form-input font-mono">
                </div>
                <div>
                    <label class="form-label">Jenis Perubahan <span class="text-red-500">*</span></label>
                    <select name="jenis_perubahan" class="form-select" required id="jenis_perubahan"
                        onchange="hitungBaru()">
                        <option value="tambahan"
                            {{ old('jenis_perubahan', $budgetRevision->jenis_perubahan) === 'tambahan' ? 'selected' : '' }}>
                            Tambahan</option>
                        <option value="pengurangan"
                            {{ old('jenis_perubahan', $budgetRevision->jenis_perubahan) === 'pengurangan' ? 'selected' : '' }}>
                            Pengurangan</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Anggaran Awal <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0" name="anggaran_awal" id="anggaran_awal"
                        value="{{ old('anggaran_awal', $budgetRevision->anggaran_awal) }}" class="form-input" required
                        oninput="hitungBaru()">
                </div>
                <div>
                    <label class="form-label">Realisasi <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0" name="realisasi"
                        value="{{ old('realisasi', $budgetRevision->realisasi) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Nominal Perubahan <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0" name="nominal_perubahan" id="nominal_perubahan"
                        value="{{ old('nominal_perubahan', $budgetRevision->nominal_perubahan) }}" class="form-input"
                        required oninput="hitungBaru()">
                </div>
                <div>
                    <label class="form-label">Anggaran Baru (estimasi)</label>
                    <div id="anggaran-baru-preview" class="form-input bg-gray-50 text-gray-500 cursor-not-allowed">
                        Rp {{ number_format($budgetRevision->anggaran_baru, 0, ',', '.') }}
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <label class="form-label">Alasan Revisi <span class="text-red-500">*</span></label>
                    <textarea name="alasan_revisi" rows="4" class="form-textarea" required>{{ old('alasan_revisi', $budgetRevision->alasan_revisi) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('superadmin.budget-revisions.show', $budgetRevision) }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>

    <script>
        function hitungBaru() {
            const awal = parseFloat(document.getElementById('anggaran_awal')?.value) || 0;
            const nominal = parseFloat(document.getElementById('nominal_perubahan')?.value) || 0;
            const jenis = document.getElementById('jenis_perubahan')?.value;
            let baru = jenis === 'pengurangan' ? awal - nominal : awal + nominal;
            document.getElementById('anggaran-baru-preview').textContent = 'Rp ' + baru.toLocaleString('id-ID');
        }
        window.addEventListener('DOMContentLoaded', hitungBaru);
    </script>
@endsection




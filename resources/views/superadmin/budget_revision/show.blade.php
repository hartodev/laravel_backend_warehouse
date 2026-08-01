{{-- resources/views/superadmin/budget_revision/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Detail Revisi Anggaran')

@section('breadcrumb')
<a href="{{ route('superadmin.budget_revisions.index') }}" class="text-gray-500 hover:text-gray-700">Revisi Anggaran</a>
<span class="text-gray-400 mx-1">/</span>
<span class="text-gray-700 font-medium">Detail</span>
@endsection

@section('content')
@if (session('success'))
<div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
    {{ session('success') }}</div>
@endif
@if (session('error'))
<div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">{{ session('error') }}
</div>
@endif

<div class="mb-6 flex flex-wrap items-start justify-between gap-3">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Detail Revisi Anggaran</h1>
        <p class="text-sm text-gray-500 mt-0.5">Diajukan oleh
            <strong>{{ $budgetRevision->createdBy?->name ?? '-' }}</strong>
        </p>
    </div>
    <div class="flex flex-wrap gap-2">
        @if ($budgetRevision->status === 'pending')
        <a href="{{ route('superadmin.budget-revisions.edit', $budgetRevision) }}"
            class="btn btn-secondary text-sm">Edit</a>
        <button type="button" onclick="document.getElementById('modal-approve').classList.remove('hidden')"
            class="btn btn-primary text-sm">Setujui</button>
        <button type="button" onclick="document.getElementById('modal-reject').classList.remove('hidden')"
            class="btn btn-danger text-sm">Tolak</button>
        @endif
        <a href="{{ route('superadmin.budget_revisions.index') }}" class="btn btn-secondary text-sm">← Kembali</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 space-y-5">

        {{-- Detail Revisi --}}
        <div class="card">
            <div class="card-header">
                <h2 class="font-semibold text-gray-800">Detail Revisi</h2>
            </div>
            <div class="card-body">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Akun Terdampak</dt>
                        <dd class="font-medium text-gray-800 mt-0.5">{{ $budgetRevision->akun_terdampak }}</dd>
                    </div>
                    @if ($budgetRevision->kode_akun)
                    <div>
                        <dt class="text-gray-500">Kode Akun</dt>
                        <dd class="font-medium text-gray-800 mt-0.5 font-mono">{{ $budgetRevision->kode_akun }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-gray-500">Jenis Perubahan</dt>
                        <dd class="mt-0.5">
                            @if ($budgetRevision->jenis_perubahan === 'tambahan')
                            <span
                                class="px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Tambahan</span>
                            @else
                            <span
                                class="px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">Pengurangan</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Realisasi</dt>
                        <dd class="font-medium text-gray-800 mt-0.5">Rp
                            {{ number_format($budgetRevision->realisasi, 0, ',', '.') }}</dd>
                    </div>
                </dl>

                {{-- Perubahan Anggaran Visual --}}
                <div class="mt-5 pt-5 border-t border-gray-100">
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div class="rounded-lg bg-gray-50 p-4">
                            <div class="text-xs text-gray-500 mb-1">Anggaran Awal</div>
                            <div class="font-bold text-gray-900">Rp
                                {{ number_format($budgetRevision->anggaran_awal, 0, ',', '.') }}</div>
                        </div>
                        <div
                            class="rounded-lg {{ $budgetRevision->jenis_perubahan === 'tambahan' ? 'bg-green-50' : 'bg-red-50' }} p-4">
                            <div
                                class="text-xs {{ $budgetRevision->jenis_perubahan === 'tambahan' ? 'text-green-600' : 'text-red-600' }} mb-1">
                                {{ $budgetRevision->jenis_perubahan === 'tambahan' ? '+ Tambahan' : '- Pengurangan' }}
                            </div>
                            <div
                                class="font-bold {{ $budgetRevision->jenis_perubahan === 'tambahan' ? 'text-green-700' : 'text-red-700' }}">
                                Rp {{ number_format($budgetRevision->nominal_perubahan, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="rounded-lg bg-blue-50 p-4">
                            <div class="text-xs text-blue-600 mb-1">Anggaran Baru</div>
                            <div class="font-bold text-blue-900">Rp
                                {{ number_format($budgetRevision->anggaran_baru, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-5 border-t border-gray-100 text-sm">
                    <dt class="text-gray-500 mb-1">Alasan Revisi</dt>
                    <dd class="text-gray-800 leading-relaxed">{{ $budgetRevision->alasan_revisi }}</dd>
                </div>
            </div>
        </div>

        {{-- Catatan Approver (jika sudah diproses) --}}
        @if ($budgetRevision->approvedBy)
        <div class="card">
            <div class="card-header">
                <h2 class="font-semibold text-gray-800">Hasil Review</h2>
            </div>
            <div class="card-body text-sm space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500">Direview oleh</span>
                    <span class="font-medium text-gray-800">{{ $budgetRevision->approvedBy->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Tanggal Review</span>
                    <span
                        class="text-gray-700">{{ \Carbon\Carbon::parse($budgetRevision->approved_at)->translatedFormat('d M Y, H:i') }}</span>
                </div>
                @if ($budgetRevision->catatan_approver)
                <div class="pt-2 border-t border-gray-100">
                    <dt class="text-gray-500 mb-1">Catatan</dt>
                    <dd class="text-gray-800">{{ $budgetRevision->catatan_approver }}</dd>
                </div>
                @endif
            </div>
        </div>
        @endif

    </div>

    {{-- Sidebar --}}
    <div class="space-y-5">

        <div class="card">
            <div class="card-header">
                <h2 class="font-semibold text-gray-800">Status</h2>
            </div>
            <div class="card-body space-y-3 text-sm">
                @php
                $rsMap = [
                'pending' => ['bg-yellow-100', 'text-yellow-700', 'Pending'],
                'approved' => ['bg-green-100', 'text-green-700', 'Approved'],
                'ditolak' => ['bg-red-100', 'text-red-700', 'Ditolak'],
                'ditunda' => ['bg-purple-100', 'text-purple-700', 'Ditunda'],
                'approved_revisi' => ['bg-teal-100', 'text-teal-700', 'Approved Revisi'],
                ];
                [$rsbg, $rsc, $rsl] = $rsMap[$budgetRevision->status] ?? [
                'bg-gray-100',
                'text-gray-600',
                $budgetRevision->status,
                ];
                @endphp
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Status</span>
                    <span class="px-2 py-0.5 rounded text-xs font-medium {{ $rsbg }} {{ $rsc }}">{{ $rsl }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Dibuat</span>
                    <span class="text-gray-700">{{ $budgetRevision->created_at->translatedFormat('d M Y, H:i') }}</span>
                </div>
            </div>
        </div>

        {{-- Link ke Pengajuan --}}
        @if ($budgetRevision->budgetRequest)
        <div class="card">
            <div class="card-header">
                <h2 class="font-semibold text-gray-800">Pengajuan Terkait</h2>
            </div>
            <div class="card-body text-sm space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-500">Nomor Form</span>
                    <a href="{{ route('superadmin.budget-requests.show', $budgetRevision->budgetRequest) }}"
                        class="font-mono text-blue-600 hover:underline text-xs">
                        {{ $budgetRevision->budgetRequest->nomor_form }}
                    </a>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Total Estimasi</span>
                    <span class="font-semibold text-gray-800">Rp
                        {{ number_format($budgetRevision->budgetRequest->total_estimasi, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

{{-- Modal Approve --}}
<div id="modal-approve" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Setujui Revisi</h3>
        <form method="POST" action="{{ route('budget-revisions.approve', $budgetRevision) }}">
            @csrf
            <div class="mb-4">
                <label class="form-label">Catatan (opsional)</label>
                <textarea name="catatan" rows="3" class="form-textarea" placeholder="Catatan persetujuan..."></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-approve').classList.add('hidden')"
                    class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">Setujui</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Reject --}}
<div id="modal-reject" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Tolak Revisi</h3>
        <form method="POST" action="{{ route('budget-revisions.reject', $budgetRevision) }}">
            @csrf
            <div class="mb-4">
                <label class="form-label">Catatan Penolakan <span class="text-red-500">*</span></label>
                <textarea name="catatan" rows="3" class="form-textarea" required
                    placeholder="Wajib diisi..."></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-reject').classList.add('hidden')"
                    class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-danger">Tolak</button>
            </div>
        </form>
    </div>
</div>
@endsection
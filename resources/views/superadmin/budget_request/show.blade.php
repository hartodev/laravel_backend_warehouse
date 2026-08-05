@extends('layouts.app')

@section('title', 'Detail Pengajuan — ' . $budgetRequest->nomor_form)
@section('breadcrumb')
<a href="{{ route('superadmin.budget-requests.index') }}" class="hover:text-primary-700">Pengajuan Anggaran</a>
<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
</svg>
<span class="text-gray-700 font-medium">{{ $budgetRequest->nomor_form }}</span>
@endsection

@section('content')
@php
    $sudahDiverifikasiSetuju = $budgetRequest->budgetVerifications()
        ->where('rekomendasi', 'setuju')
        ->exists();
    $verifikasiTerakhir = $budgetRequest->budgetVerifications()->latest()->first();
@endphp

<div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 mb-6">
    <div>
        <div class="flex items-center gap-3 flex-wrap">
            <h1 class="text-xl font-bold text-gray-900">{{ $budgetRequest->nomor_form }}</h1>
            @include('superadmin.budget_request._status_badge', ['status' => $budgetRequest->status])
            @if ($budgetRequest->urgensi === 'mendesak')
            <span class="badge badge-danger">Mendesak</span>
            @endif
        </div>
        <p class="text-sm text-gray-500 mt-1">
            Diajukan oleh <strong>{{ $budgetRequest->user->name ?? '—' }}</strong>
            · {{ \Carbon\Carbon::parse($budgetRequest->tanggal_pengajuan)->translatedFormat('d M Y') }}
        </p>
    </div>
    <a href="{{ route('superadmin.budget-requests.index') }}" class="btn btn-secondary">← Kembali</a>
</div>

@if (session('success'))
<div class="mb-5 rounded-md bg-green-50 border border-green-200 p-4 text-sm text-green-700">{{ session('success') }}</div>
@endif
@if (session('error'))
<div class="mb-5 rounded-md bg-red-50 border border-red-200 p-4 text-sm text-red-700">{{ session('error') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- LEFT: Detail --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Info Umum --}}
        <div class="card">
            <div class="card-header">
                <h2 class="font-semibold text-gray-800">Informasi Umum</h2>
            </div>
            <div class="card-body grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Divisi</p>
                    <p class="font-medium text-gray-900">{{ $budgetRequest->divisi }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Jenis</p>
                    <p class="font-medium text-gray-900">{{ $budgetRequest->jenis === 'rab' ? 'RAB' : 'Luar RAB' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Kode Akun</p>
                    <p class="font-medium text-gray-900">{{ $budgetRequest->kode_akun ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Nama Akun</p>
                    <p class="font-medium text-gray-900">{{ $budgetRequest->nama_akun ?? '—' }}</p>
                </div>
                @if ($budgetRequest->jenis === 'luar_rab')
                <div class="sm:col-span-2">
                    <p class="text-gray-500">Alasan Luar RAB</p>
                    <p class="text-gray-800">{{ $budgetRequest->alasan_luar_rab ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Sumber Dana</p>
                    <p class="font-medium text-gray-900">{{ $budgetRequest->sumber_dana ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Dampak Jika Tidak Direalisasi</p>
                    <p class="text-gray-800">{{ $budgetRequest->dampak_jika_tidak ?? '—' }}</p>
                </div>
                @endif
                @if ($budgetRequest->keterangan)
                <div class="sm:col-span-2">
                    <p class="text-gray-500">Keterangan</p>
                    <p class="text-gray-800">{{ $budgetRequest->keterangan }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Item Rincian --}}
        <div class="card">
            <div class="card-header">
                <h2 class="font-semibold text-gray-800">Rincian Item</h2>
            </div>
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wide">
                            <tr>
                                <th class="px-4 py-3 text-left">#</th>
                                <th class="px-4 py-3 text-left">Nama Item</th>
                                <th class="px-4 py-3 text-right">Qty</th>
                                <th class="px-4 py-3 text-left">Satuan</th>
                                <th class="px-4 py-3 text-right">Estimasi</th>
                                <th class="px-4 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($budgetRequest->items as $i => $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-400">{{ $i + 1 }}</td>
                                <td class="px-4 py-3 text-gray-800">
                                    {{ $item->nama_item }}
                                    @if ($item->keterangan)
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $item->keterangan }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right text-gray-600">{{ $item->qty ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $item->satuan ?? '-' }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">Rp {{ number_format($item->estimasi_biaya, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-400">Tidak ada item.</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="border-t-2 border-gray-200 bg-gray-50">
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-right font-semibold text-gray-700">Total Estimasi</td>
                                <td class="px-4 py-3 text-right font-bold text-gray-900">Rp {{ number_format($budgetRequest->total_estimasi, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Status Verifikasi --}}
        <div class="card">
            <div class="card-header">
                <h2 class="font-semibold text-gray-800">Status Verifikasi Anggaran</h2>
            </div>
            <div class="card-body text-sm">
                @if ($verifikasiTerakhir)
                <div class="flex items-center gap-3 mb-3">
                    @php
                        $rekMap = [
                            'setuju' => ['bg-green-100', 'text-green-700', 'Setuju'],
                            'tunda'  => ['bg-yellow-100', 'text-yellow-700', 'Tunda'],
                            'tolak'  => ['bg-red-100', 'text-red-700', 'Tolak'],
                        ];
                        [$rbg, $rc, $rl] = $rekMap[$verifikasiTerakhir->rekomendasi] ?? ['bg-gray-100', 'text-gray-600', $verifikasiTerakhir->rekomendasi];
                    @endphp
                    <span class="px-2 py-0.5 rounded text-xs font-medium {{ $rbg }} {{ $rc }}">{{ $rl }}</span>
                    <span class="text-gray-500">oleh {{ $verifikasiTerakhir->verifiedBy->name ?? '-' }}
                        · {{ \Carbon\Carbon::parse($verifikasiTerakhir->verified_at)->translatedFormat('d M Y') }}</span>
                    <a href="{{ route('superadmin.budget-verifications.show', $verifikasiTerakhir) }}"
                        class="ml-auto text-primary-700 hover:underline text-xs">Lihat Detail →</a>
                </div>
                @else
                <p class="text-gray-400">Pengajuan ini belum pernah diverifikasi.</p>
                @endif

                @if ($budgetRequest->status === 'pending_sa' && ! $sudahDiverifikasiSetuju)
                <div class="mt-3 rounded-md bg-yellow-50 border border-yellow-200 p-3 text-yellow-800 text-xs">
                    ⚠️ RAB ini belum bisa di-approve final karena belum ada verifikasi dengan rekomendasi
                    <strong>"Setuju"</strong>. Silakan lakukan
                    <a href="{{ route('superadmin.budget-verifications.create', ['budget_request_id' => $budgetRequest->id]) }}"
                        class="underline font-medium">Verifikasi Anggaran</a> terlebih dahulu.
                </div>
                @endif
            </div>
        </div>

        {{-- Realisasi (kalau sudah approved) --}}
        @if (in_array($budgetRequest->status, ['approved', 'approved_revisi']))
        <div class="card">
            <div class="card-header">
                <h2 class="font-semibold text-gray-800">Realisasi Dana</h2>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-3 gap-4 text-center mb-5">
                    <div class="rounded-lg bg-gray-50 p-4">
                        <p class="text-xs text-gray-500 mb-1">Total Anggaran</p>
                        <p class="font-bold text-gray-900">Rp {{ number_format($budgetRequest->total_estimasi, 0, ',', '.') }}</p>
                    </div>
                    <div class="rounded-lg bg-red-50 p-4">
                        <p class="text-xs text-red-600 mb-1">Sudah Terpakai</p>
                        <p class="font-bold text-red-700">Rp {{ number_format($budgetRequest->total_realisasi ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="rounded-lg bg-green-50 p-4">
                        <p class="text-xs text-green-600 mb-1">Sisa Anggaran</p>
                        <p class="font-bold text-green-700">Rp {{ number_format($sisaAnggaran, 0, ',', '.') }}</p>
                    </div>
                </div>

                @if ($sisaAnggaran > 0)
                <form method="POST" action="{{ route('superadmin.budget-requests.realisasi', $budgetRequest) }}"
                    class="border-t border-gray-100 pt-4 grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                    @csrf
                    <div>
                        <label class="form-label">Jumlah Realisasi</label>
                        <input type="number" name="jumlah_uang" min="1" max="{{ $sisaAnggaran }}" required class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="form-input">
                    </div>
                    <div class="sm:col-span-1">
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" required class="form-input" placeholder="Untuk apa dana dipakai">
                    </div>
                    <div class="sm:col-span-3">
                        <button type="submit" class="btn btn-primary btn-sm">Catat Realisasi</button>
                    </div>
                </form>
                @else
                <p class="text-sm text-gray-400 border-t border-gray-100 pt-4">Anggaran sudah terpakai sepenuhnya.</p>
                @endif

                @if ($budgetRequest->cashBooks->where('jenis', 'realisasi_rab')->count())
                <div class="mt-5 border-t border-gray-100 pt-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Riwayat Realisasi</p>
                    <div class="space-y-2 text-sm">
                        @foreach ($budgetRequest->cashBooks->where('jenis', 'realisasi_rab')->sortByDesc('tanggal') as $cb)
                        <div class="flex justify-between border-b border-gray-50 pb-2">
                            <div>
                                <p class="text-gray-800">{{ $cb->keterangan }}</p>
                                <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($cb->tanggal)->translatedFormat('d M Y') }}</p>
                            </div>
                            <span class="font-semibold text-red-600">- Rp {{ number_format($cb->jumlah_uang, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- RIGHT: Aksi & Approver --}}
    <div class="space-y-5">

        {{-- Aksi Approval --}}
        @if ($budgetRequest->status === 'pending_sa')
        <div class="card border-2 border-blue-200">
            <div class="card-header bg-blue-50">
                <h2 class="font-semibold text-blue-900">Aksi Persetujuan</h2>
            </div>
            <div class="card-body space-y-3">
                <form method="POST" action="{{ route('superadmin.budget-requests.approve', $budgetRequest) }}">
                    @csrf
                    <textarea name="catatan" rows="2" class="form-textarea mb-2" placeholder="Catatan (opsional)"></textarea>
                    <button type="submit" class="btn btn-primary w-full justify-center"
                        {{ $sudahDiverifikasiSetuju ? '' : 'disabled' }}
                        onclick="return confirm('Setujui pengajuan anggaran ini? Dana akan langsung dialokasikan ke buku kas.')">
                        ✓ Setujui &amp; Alokasikan Dana
                    </button>
                </form>
                <button type="button" onclick="document.getElementById('modal-reject-rab').classList.remove('hidden')"
                    class="btn btn-danger w-full justify-center">✕ Tolak Pengajuan</button>
                @if (! $sudahDiverifikasiSetuju)
                <p class="text-xs text-gray-500">Tombol setujui nonaktif sampai ada verifikasi "Setuju".</p>
                @endif
            </div>
        </div>
        @endif

        {{-- Ringkasan --}}
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-800">Ringkasan</h3>
            </div>
            <div class="card-body space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Status</span>
                    @include('superadmin.budget_request._status_badge', ['status' => $budgetRequest->status])
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Total Estimasi</span>
                    <span class="font-semibold text-gray-900">Rp {{ number_format($budgetRequest->total_estimasi, 0, ',', '.') }}</span>
                </div>
                @if ($budgetRequest->total_realisasi)
                <div class="flex justify-between">
                    <span class="text-gray-500">Total Realisasi</span>
                    <span class="font-semibold text-red-600">Rp {{ number_format($budgetRequest->total_realisasi, 0, ',', '.') }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Riwayat Approver --}}
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-800">Riwayat</h3>
            </div>
            <div class="card-body space-y-3 text-sm">
                <div>
                    <p class="text-gray-500">Diajukan Oleh</p>
                    <p class="font-medium text-gray-900">{{ $budgetRequest->user->name ?? '—' }}</p>
                    <p class="text-xs text-gray-400">{{ $budgetRequest->user->email ?? '' }}</p>
                </div>
                @if ($budgetRequest->superAdminApprover)
                <div class="border-t border-gray-100 pt-3">
                    <p class="text-gray-500">Diproses Super Admin</p>
                    <p class="font-medium text-gray-900">{{ $budgetRequest->superAdminApprover->name }}</p>
                    <p class="text-xs text-gray-400">{{ $budgetRequest->finance_at ? \Carbon\Carbon::parse($budgetRequest->finance_at)->translatedFormat('d M Y, H:i') : '' }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- @if (in_array($budgetRequest->status, ['draft', 'pending']))
        <div class="card">
            <div class="card-body space-y-2">
                <a href="{{ route('superadmin.budget-requests.edit', $budgetRequest) }}" class="btn btn-secondary w-full justify-center">Edit Pengajuan</a>
                @if ($budgetRequest->status === 'draft')
                <form method="POST" action="{{ route('superadmin.budget-requests.submit', $budgetRequest) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary w-full justify-center">Ajukan ke Admin</button>
                </form>
                <form method="POST" action="{{ route('superadmin.budget-requests.destroy', $budgetRequest) }}"
                    onsubmit="return confirm('Hapus pengajuan draft ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger w-full justify-center">Hapus</button>
                </form>
                @endif
            </div>
        </div>
        @endif --}}

 @if ($budgetRequest->status === 'draft')
<div class="card">
    <div class="card-body space-y-2">
        <a href="{{ route('superadmin.budget-requests.edit', $budgetRequest) }}" class="btn btn-secondary w-full justify-center">Edit Pengajuan</a>

        <form method="POST" action="{{ route('superadmin.budget-requests.submit', $budgetRequest) }}"
            onsubmit="return confirm('Ajukan RAB ini? Karena Anda Super Admin, RAB akan langsung disetujui dan dana dialokasikan.')">
            @csrf
            <button type="submit" class="btn btn-primary w-full justify-center">Ajukan &amp; Setujui</button>
        </form>

        <form method="POST" action="{{ route('superadmin.budget-requests.destroy', $budgetRequest) }}"
            onsubmit="return confirm('Hapus pengajuan draft ini?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger w-full justify-center">Hapus</button>
        </form>
    </div>
</div>
@endif

@if ($budgetRequest->status === 'pending_sa')
<div class="card">
    <div class="card-body space-y-2">
        <a href="{{ route('superadmin.budget-requests.edit', $budgetRequest) }}" class="btn btn-secondary w-full justify-center">Edit Pengajuan</a>
    </div>
</div>
@endif

@if ($budgetRequest->status === 'pending_sa')
<div class="card">
    <div class="card-body space-y-2">
        <a href="{{ route('superadmin.budget-requests.edit', $budgetRequest) }}" class="btn btn-secondary w-full justify-center">Edit Pengajuan</a>
    </div>
</div>
@endif
    </div>
</div>

{{-- Modal Tolak --}}
@if ($budgetRequest->status === 'pending_sa')
<div id="modal-reject-rab" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Tolak Pengajuan Anggaran</h3>
        <form method="POST" action="{{ route('superadmin.budget-requests.reject', $budgetRequest) }}">
            @csrf
            <label class="form-label">Alasan Penolakan <span class="text-red-500">*</span></label>
            <textarea name="reject_reason" rows="3" required class="form-textarea mb-4"
                placeholder="Jelaskan alasan penolakan..."></textarea>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-reject-rab').classList.add('hidden')"
                    class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-danger">Tolak Pengajuan</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

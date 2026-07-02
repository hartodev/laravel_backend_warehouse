@extends('layouts.app')

@section('title', 'Detail Pengajuan — ' . $budgetRequest->nomor_form)
@section('breadcrumb')
    <a href="{{ route('budget-requests.index') }}">Pengajuan Anggaran</a>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
    </svg>
    <span class="text-gray-700 dark:text-gray-300 font-medium">{{ $budgetRequest->nomor_form }}</span>
@endsection

@section('content')
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="page-title">{{ $budgetRequest->nomor_form }}</h1>
                    @include('superadmin.budget_request._status_badge', ['status' => $budgetRequest->status])
                    @if ($budgetRequest->urgensi === 'mendesak')
                        <span class="badge badge-danger">🚨 Mendesak</span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 mt-1">Diajukan oleh {{ $budgetRequest->user->name ?? '-' }} —
                    {{ $budgetRequest->divisi }}</p>
            </div>
            <a href="{{ route('budget-requests.index') }}" class="btn btn-secondary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>

        {{-- ★ ACTION PANEL — hanya muncul sesuai status --}}
        @if ($budgetRequest->status === 'pending_sa')
            <div class="card border-2 border-indigo-200">
                <div class="card-header bg-indigo-50 dark:bg-indigo-900/20">
                    <p class="font-semibold text-indigo-900 dark:text-indigo-300 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Menunggu Persetujuan Final Anda
                    </p>
                </div>
                <div class="card-body">
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                        Pengajuan ini sudah disetujui oleh admin
                        @if ($budgetRequest->adminApprover)
                            (<strong>{{ $budgetRequest->adminApprover->name }}</strong>)
                        @endif
                        dan menunggu approval final dari Anda. Setelah disetujui, dana sebesar
                        <strong>Rp {{ number_format($budgetRequest->total_estimasi, 0, ',', '.') }}</strong>
                        akan otomatis dialokasikan ke buku kas.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="button" onclick="document.getElementById('modal-approve').classList.remove('hidden')"
                            class="btn btn-success">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Setujui & Alokasikan Dana
                        </button>
                        <button type="button" onclick="document.getElementById('modal-reject').classList.remove('hidden')"
                            class="btn btn-danger">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Tolak
                        </button>
                    </div>
                </div>
            </div>
        @elseif($budgetRequest->status === 'approved')
            <div class="card border-2 border-emerald-200">
                <div class="card-header bg-emerald-50 dark:bg-emerald-900/20">
                    <p class="font-semibold text-emerald-900 dark:text-emerald-300 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Dana Telah Dialokasikan
                    </p>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                        <div>
                            <p class="text-xs text-gray-500">Total Anggaran</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">Rp
                                {{ number_format($budgetRequest->total_estimasi, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Sudah Terealisasi</p>
                            <p class="text-lg font-bold text-orange-600">Rp
                                {{ number_format($budgetRequest->total_realisasi, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Sisa Anggaran</p>
                            <p class="text-lg font-bold text-emerald-600">Rp
                                {{ number_format($sisaAnggaran, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    {{-- Progress bar realisasi --}}
                    @php
                        $persen = $budgetRequest->total_estimasi > 0
                            ? min(100, ($budgetRequest->total_realisasi / $budgetRequest->total_estimasi) * 100)
                            : 0;
                    @endphp
                    <div class="w-full bg-gray-200 dark:bg-slate-700 rounded-full h-2.5 mb-4">
                        <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ $persen }}%"></div>
                    </div>

                    @if ($sisaAnggaran > 0)
                        <button type="button" onclick="document.getElementById('modal-realisasi').classList.remove('hidden')"
                            class="btn btn-primary">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Catat Realisasi Dana
                        </button>
                    @else
                        <p class="text-sm text-gray-500 italic">Anggaran sudah terealisasi sepenuhnya.</p>
                    @endif
                </div>
            </div>
        @elseif($budgetRequest->status === 'pending')
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-800">
                Pengajuan ini masih menunggu review dari Admin. Belum dapat diproses oleh Super Admin.
            </div>
        @elseif($budgetRequest->status === 'ditolak')
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-800">
                Pengajuan ini telah ditolak.
                @if ($budgetRequest->catatan_branch_manager)
                    <br><strong>Catatan:</strong> {{ $budgetRequest->catatan_branch_manager }}
                @endif
            </div>
        @endif

        {{-- Info Pengajuan --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="card">
                    <div class="card-header">
                        <p class="font-semibold text-gray-800 dark:text-white">Informasi Pengajuan</p>
                    </div>
                    <div class="card-body">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div>
                                <dt class="text-gray-500">Divisi</dt>
                                <dd class="font-medium text-gray-900 dark:text-white">{{ $budgetRequest->divisi }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Tanggal Pengajuan</dt>
                                <dd class="font-medium text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($budgetRequest->tanggal_pengajuan)->translatedFormat('d F Y') }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Jenis</dt>
                                <dd class="font-medium text-gray-900 dark:text-white">
                                    {{ $budgetRequest->jenis === 'rab' ? 'RAB' : 'Luar RAB' }}</dd>
                            </div>
                            @if ($budgetRequest->jenis === 'rab')
                                <div>
                                    <dt class="text-gray-500">Akun</dt>
                                    <dd class="font-medium text-gray-900 dark:text-white">
                                        {{ $budgetRequest->kode_akun ?? '-' }} — {{ $budgetRequest->nama_akun ?? '-' }}
                                    </dd>
                                </div>
                            @else
                                <div class="sm:col-span-2">
                                    <dt class="text-gray-500">Alasan Luar RAB</dt>
                                    <dd class="font-medium text-gray-900 dark:text-white">
                                        {{ $budgetRequest->alasan_luar_rab ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500">Sumber Dana</dt>
                                    <dd class="font-medium text-gray-900 dark:text-white">
                                        {{ ucfirst($budgetRequest->sumber_dana ?? '-') }}</dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-gray-500">Dampak Jika Tidak Direalisasi</dt>
                                    <dd class="font-medium text-gray-900 dark:text-white">
                                        {{ $budgetRequest->dampak_jika_tidak ?? '-' }}</dd>
                                </div>
                            @endif
                            @if ($budgetRequest->keterangan)
                                <div class="sm:col-span-2">
                                    <dt class="text-gray-500">Keterangan</dt>
                                    <dd class="font-medium text-gray-900 dark:text-white">{{ $budgetRequest->keterangan }}
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>

                {{-- Rincian Item --}}
                <div class="card">
                    <div class="card-header">
                        <p class="font-semibold text-gray-800 dark:text-white">Rincian Item</p>
                    </div>
                    <div class="table-wrap" style="border:none;border-radius:0">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Nama Item</th>
                                    <th>Qty</th>
                                    <th>Satuan</th>
                                    <th>Estimasi Biaya</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($budgetRequest->items as $item)
                                    <tr>
                                        <td>{{ $item->nama_item }}
                                            @if ($item->keterangan)
                                                <p class="text-xs text-gray-400">{{ $item->keterangan }}</p>
                                            @endif
                                        </td>
                                        <td>{{ $item->qty ?? '-' }}</td>
                                        <td>{{ $item->satuan ?? '-' }}</td>
                                        <td>Rp {{ number_format($item->estimasi_biaya, 0, ',', '.') }}</td>
                                        <td class="font-medium">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50 dark:bg-slate-900">
                                    <td colspan="4" class="text-right font-semibold">Total Estimasi</td>
                                    <td class="font-bold text-indigo-600">Rp
                                        {{ number_format($budgetRequest->total_estimasi, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- Riwayat Realisasi (Cash Book) --}}
                @if ($budgetRequest->cashBooks->isNotEmpty())
                    <div class="card">
                        <div class="card-header">
                            <p class="font-semibold text-gray-800 dark:text-white">Riwayat Transaksi Dana</p>
                        </div>
                        <div class="table-wrap" style="border:none;border-radius:0">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Keterangan</th>
                                        <th>Tipe</th>
                                        <th class="text-right">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($budgetRequest->cashBooks->sortByDesc('tanggal') as $cb)
                                        <tr>
                                            <td class="text-xs text-gray-500">
                                                {{ \Carbon\Carbon::parse($cb->tanggal)->translatedFormat('d M Y') }}</td>
                                            <td>{{ $cb->keterangan }}</td>
                                            <td>
                                                @if ($cb->tipe === 'masuk')
                                                    <span class="badge badge-success">Alokasi</span>
                                                @else
                                                    <span class="badge badge-warning">Realisasi</span>
                                                @endif
                                            </td>
                                            <td class="text-right font-medium {{ $cb->tipe === 'masuk' ? 'text-emerald-600' : 'text-orange-600' }}">
                                                {{ $cb->tipe === 'masuk' ? '+' : '-' }}Rp
                                                {{ number_format($cb->jumlah_uang, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar — Approval Trail --}}
            <div class="space-y-6">
                <div class="card">
                    <div class="card-header">
                        <p class="font-semibold text-gray-800 dark:text-white">Alur Persetujuan</p>
                    </div>
                    <div class="card-body space-y-4">
                        {{-- User --}}
                        <div class="flex gap-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0 text-sm">
                                👤</div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Diajukan</p>
                                <p class="text-xs text-gray-500">{{ $budgetRequest->user->name ?? '-' }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ $budgetRequest->created_at->translatedFormat('d M Y, H:i') }}</p>
                            </div>
                        </div>

                        {{-- Admin --}}
                        <div class="flex gap-3">
                            <div
                                class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-sm
                                {{ $budgetRequest->adminApprover ? 'bg-emerald-100' : 'bg-gray-100' }}">
                                {{ $budgetRequest->adminApprover ? '✅' : '⏳' }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Review Admin</p>
                                @if ($budgetRequest->adminApprover)
                                    <p class="text-xs text-gray-500">{{ $budgetRequest->adminApprover->name }}</p>
                                    <p class="text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($budgetRequest->branch_manager_at)->translatedFormat('d M Y, H:i') }}
                                    </p>
                                    @if ($budgetRequest->catatan_branch_manager)
                                        <p class="text-xs text-gray-500 italic mt-1">"{{ $budgetRequest->catatan_branch_manager }}"</p>
                                    @endif
                                @else
                                    <p class="text-xs text-gray-400">Menunggu</p>
                                @endif
                            </div>
                        </div>

                        {{-- Super Admin --}}
                        <div class="flex gap-3">
                            <div
                                class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-sm
                                {{ $budgetRequest->superAdminApprover ? 'bg-emerald-100' : 'bg-gray-100' }}">
                                {{ $budgetRequest->superAdminApprover ? '✅' : '⏳' }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Approval Final (Super Admin)</p>
                                @if ($budgetRequest->superAdminApprover)
                                    <p class="text-xs text-gray-500">{{ $budgetRequest->superAdminApprover->name }}</p>
                                    <p class="text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($budgetRequest->finance_at)->translatedFormat('d M Y, H:i') }}
                                    </p>
                                @else
                                    <p class="text-xs text-gray-400">Menunggu</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if (in_array($budgetRequest->status, ['draft', 'pending']))
                    <div class="card">
                        <div class="card-body">
                            <a href="{{ route('budget-requests.edit', $budgetRequest) }}"
                                class="btn btn-secondary w-full justify-center mb-2">Edit Pengajuan</a>
                            @if ($budgetRequest->status === 'draft')
                                <form action="{{ route('budget-requests.destroy', $budgetRequest) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus pengajuan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-full justify-center">Hapus
                                        Pengajuan</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- Modal: Approve --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div id="modal-approve" class="modal-backdrop hidden">
        <div class="modal-box">
            <form action="{{ route('budget-requests.approve', $budgetRequest) }}" method="POST">
                @csrf
                <div class="p-5 border-b border-gray-100 dark:border-slate-700">
                    <p class="font-semibold text-gray-900 dark:text-white">Setujui Pengajuan Anggaran</p>
                </div>
                <div class="p-5">
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                        Dana sebesar <strong>Rp {{ number_format($budgetRequest->total_estimasi, 0, ',', '.') }}</strong>
                        akan dialokasikan ke buku kas setelah disetujui.
                    </p>
                    <label class="form-label">Catatan (opsional)</label>
                    <textarea name="catatan" rows="3" class="form-textarea" placeholder="Catatan persetujuan..."></textarea>
                </div>
                <div class="p-5 border-t border-gray-100 dark:border-slate-700 flex gap-2 justify-end">
                    <button type="button" onclick="document.getElementById('modal-approve').classList.add('hidden')"
                        class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-success">Ya, Setujui</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Reject --}}
    <div id="modal-reject" class="modal-backdrop hidden">
        <div class="modal-box">
            <form action="{{ route('budget-requests.reject', $budgetRequest) }}" method="POST">
                @csrf
                <div class="p-5 border-b border-gray-100 dark:border-slate-700">
                    <p class="font-semibold text-gray-900 dark:text-white">Tolak Pengajuan Anggaran</p>
                </div>
                <div class="p-5">
                    <label class="form-label">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <textarea name="reject_reason" rows="3" class="form-textarea" required
                        placeholder="Jelaskan alasan penolakan..."></textarea>
                </div>
                <div class="p-5 border-t border-gray-100 dark:border-slate-700 flex gap-2 justify-end">
                    <button type="button" onclick="document.getElementById('modal-reject').classList.add('hidden')"
                        class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Tolak</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Realisasi --}}
    @if ($budgetRequest->status === 'approved')
        <div id="modal-realisasi" class="modal-backdrop hidden">
            <div class="modal-box">
                <form action="{{ route('budget-requests.realisasi', $budgetRequest) }}" method="POST">
                    @csrf
                    <div class="p-5 border-b border-gray-100 dark:border-slate-700">
                        <p class="font-semibold text-gray-900 dark:text-white">Catat Realisasi Dana</p>
                        <p class="text-xs text-gray-500 mt-1">Sisa anggaran: Rp
                            {{ number_format($sisaAnggaran, 0, ',', '.') }}</p>
                    </div>
                    <div class="p-5 space-y-3">
                        <div>
                            <label class="form-label">Tanggal <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal" class="form-input" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div>
                            <label class="form-label">Jumlah (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="jumlah_uang" class="form-input" min="1" max="{{ $sisaAnggaran }}"
                                required placeholder="0">
                        </div>
                        <div>
                            <label class="form-label">Keterangan <span class="text-red-500">*</span></label>
                            <textarea name="keterangan" rows="2" class="form-textarea" required
                                placeholder="Untuk pembelian apa dana ini digunakan..."></textarea>
                        </div>
                    </div>
                    <div class="p-5 border-t border-gray-100 dark:border-slate-700 flex gap-2 justify-end">
                        <button type="button" onclick="document.getElementById('modal-realisasi').classList.add('hidden')"
                            class="btn btn-secondary">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Realisasi</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection

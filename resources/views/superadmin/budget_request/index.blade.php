@extends('layouts.app')

@section('title', 'Pengajuan Anggaran')
@section('breadcrumb')
<span class="text-gray-700 dark:text-gray-300 font-medium">Pengajuan Anggaran</span>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="page-title">Pengajuan Anggaran (RAB)</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola dan setujui pengajuan anggaran dari seluruh divisi.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('superadmin.budget-requests.create') }}" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat RAB
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card">
            <div class="stat-icon bg-orange-100 text-orange-600">⏳</div>
            <div>
                <p class="text-xs text-gray-500">Menunggu Admin</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $summary['menunggu_admin'] }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-blue-100 text-blue-600">📋</div>
            <div>
                <p class="text-xs text-gray-500">Menunggu Super Admin</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $summary['menunggu_sa'] }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-emerald-100 text-emerald-600">💰</div>
            <div>
                <p class="text-xs text-gray-500">Total Anggaran Disetujui</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white">Rp
                    {{ number_format($summary['total_anggaran'], 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-indigo-100 text-indigo-600">📊</div>
            <div>
                <p class="text-xs text-gray-500">Sisa Anggaran</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white">Rp
                    {{ number_format($summary['sisa_anggaran'], 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    @if ($summary['mendesak_pending'] > 0)
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3">
        <span class="text-xl">🚨</span>
        <p class="text-sm text-red-800">
            Ada <strong>{{ $summary['mendesak_pending'] }}</strong> pengajuan <strong>mendesak</strong> yang
            masih menunggu persetujuan.
        </p>
    </div>
    @endif

    {{-- Filter --}}
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('superadmin.budget-requests.index') }}"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        @foreach (['draft' => 'Draft', 'pending' => 'Menunggu Admin', 'pending_sa' => 'Menunggu Super
                        Admin', 'approved' => 'Disetujui', 'ditolak' => 'Ditolak', 'ditunda' => 'Ditunda'] as $key =>
                        $label)
                        <option value="{{ $key }}" @selected(request('status')==$key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Jenis</label>
                    <select name="jenis" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Jenis</option>
                        <option value="rab" @selected(request('jenis')=='rab' )>RAB</option>
                        <option value="luar_rab" @selected(request('jenis')=='luar_rab' )>Luar RAB</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Urgensi</label>
                    <select name="urgensi" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua</option>
                        <option value="normal" @selected(request('urgensi')=='normal' )>Normal</option>
                        <option value="mendesak" @selected(request('urgensi')=='mendesak' )>Mendesak</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Divisi</label>
                    <input type="text" name="divisi" value="{{ request('divisi') }}" class="form-input"
                        placeholder="Cari divisi...">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn btn-primary w-full">Filter</button>
                    @if (request()->anyFilled(['status', 'jenis', 'urgensi', 'divisi']))
                    <a href="{{ route('superadmin.budget-requests.index') }}" class="btn btn-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No. Form</th>
                    <th>Pengaju</th>
                    <th>Divisi</th>
                    <th>Jenis</th>
                    <th>Urgensi</th>
                    <th>Total Estimasi</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($brs as $br)
                <tr>
                    <td class="font-medium text-gray-900 dark:text-white">{{ $br->nomor_form }}</td>
                    <td>{{ $br->user->name ?? '-' }}</td>
                    <td>{{ $br->divisi }}</td>
                    <td>
                        <span class="badge badge-gray">{{ strtoupper($br->jenis) }}</span>
                    </td>
                    <td>
                        @if ($br->urgensi === 'mendesak')
                        <span class="badge badge-danger">Mendesak</span>
                        @else
                        <span class="badge badge-gray">Normal</span>
                        @endif
                    </td>
                    <td class="font-medium">Rp {{ number_format($br->total_estimasi, 0, ',', '.') }}</td>
                    <td>
                        @include('superadmin.budget_request._status_badge', ['status' => $br->status])
                    </td>
                    <td class="text-xs text-gray-500">
                        {{ \Carbon\Carbon::parse($br->tanggal_pengajuan)->translatedFormat('d M Y') }}</td>
                    <td class="text-right whitespace-nowrap">
                        <a href="{{ route('superadmin.budget-requests.show', $br) }}" class="btn btn-secondary btn-xs">
                            Detail
                        </a>
                        @if ($br->status === 'pending_sa')
                        <span
                            class="inline-flex items-center ml-1 px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-700 font-semibold">Perlu
                            Aksi</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-gray-400 py-8">
                        Tidak ada data pengajuan anggaran.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $brs->links() }}</div>
</div>
@endsection



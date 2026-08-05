{{-- resources/views/superadmin/budget_revision/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Revisi Anggaran')

@section('breadcrumb')
<span class="text-gray-700 font-medium">Revisi Anggaran</span>
@endsection

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Revisi Anggaran</h1>
        <p class="text-sm text-gray-500 mt-0.5">Daftar pengajuan revisi anggaran</p>
    </div>
    <a href="{{ route('superadmin.budget-revisions.create') }}" class="btn btn-primary text-sm">+ Ajukan Revisi</a>
</div>

@if (session('success'))
<div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
    {{ session('success') }}</div>
@endif
@if (session('error'))
<div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
@endif

{{-- Filter --}}
<div class="card mb-5">
    <div class="card-body">
        <form method="GET" action="{{ route('superadmin.budget-revisions.index') }}" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="pending" {{ request('status') === 'pending'         ? 'selected' : '' }}>Pending
                    </option>
                    <option value="approved" {{ request('status') === 'approved'        ? 'selected' : '' }}>Approved
                    </option>
                    <option value="approved_revisi" {{ request('status') === 'approved_revisi' ? 'selected' : '' }}>
                        Approved Revisi</option>
                    <option value="ditunda" {{ request('status') === 'ditunda'         ? 'selected' : '' }}>Ditunda
                    </option>
                    <option value="ditolak" {{ request('status') === 'ditolak'         ? 'selected' : '' }}>Ditolak
                    </option>
                </select>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('superadmin.budget-revisions.index') }}" class="btn btn-secondary text-sm">Reset</a>
                <button type="submit" class="btn btn-primary text-sm">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wide">
                    <tr>
                        <th class="px-4 py-3 text-left">Pengajuan Terkait</th>
                        <th class="px-4 py-3 text-left">Akun Terdampak</th>
                        <th class="px-4 py-3 text-center">Jenis</th>
                        <th class="px-4 py-3 text-right">Anggaran Awal</th>
                        <th class="px-4 py-3 text-right">Nominal Perubahan</th>
                        <th class="px-4 py-3 text-right">Anggaran Baru</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-left">Pengaju</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($revisions as $r)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            @if ($r->budgetRequest)
                            <a href="{{ route('superadmin.budget-requests.show', $r->budgetRequest) }}"
                                class="font-mono text-xs text-blue-600 hover:underline">
                                {{ $r->budgetRequest->nomor_form }}
                            </a>
                            @else
                            <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-800">{{ $r->akun_terdampak }}</td>
                        <td class="px-4 py-3 text-center">
                            @if ($r->jenis_perubahan === 'tambahan')
                            <span
                                class="px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Tambahan</span>
                            @else
                            <span
                                class="px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">Pengurangan</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right text-gray-600">Rp
                            {{ number_format($r->anggaran_awal, 0, ',', '.') }}</td>
                        <td
                            class="px-4 py-3 text-right font-semibold {{ $r->jenis_perubahan === 'tambahan' ? 'text-green-700' : 'text-red-700' }}">
                            {{ $r->jenis_perubahan === 'tambahan' ? '+' : '-' }}
                            Rp {{ number_format($r->nominal_perubahan, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900">Rp
                            {{ number_format($r->anggaran_baru, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            @php
                            $rsMap =
                            ['pending'=>['bg-yellow-100','text-yellow-700','Pending'],'approved'=>['bg-green-100','text-green-700','Approved'],'ditolak'=>['bg-red-100','text-red-700','Ditolak'],'ditunda'=>['bg-purple-100','text-purple-700','Ditunda'],'approved_revisi'=>['bg-teal-100','text-teal-700','Approved
                            Revisi']];
                            [$rsbg,$rsc,$rsl] = $rsMap[$r->status] ?? ['bg-gray-100','text-gray-600',$r->status];
                            @endphp
                            <span
                                class="px-2 py-0.5 rounded text-xs font-medium {{ $rsbg }} {{ $rsc }}">{{ $rsl }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $r->createdBy?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('superadmin.budget-revisions.show', $r) }}"
                                    class="text-blue-600 hover:text-blue-800 text-xs font-medium">Detail</a>
                                @if ($r->status === 'pending')
                                <a href="{{ route('superadmin.budget-revisions.edit', $r) }}"
                                    class="text-yellow-600 hover:text-yellow-800 text-xs font-medium">Edit</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-10 text-center text-gray-400 text-sm">Belum ada revisi anggaran.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($revisions->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $revisions->links() }}</div>
        @endif
    </div>
</div>
@endsection

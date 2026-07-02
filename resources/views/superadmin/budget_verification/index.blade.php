{{-- resources/views/superadmin/budget_verification/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Verifikasi Anggaran')

@section('breadcrumb')
    <span class="text-gray-700 font-medium">Verifikasi Anggaran</span>
@endsection

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Verifikasi Anggaran</h1>
        <p class="text-sm text-gray-500 mt-0.5">Daftar verifikasi finance atas pengajuan anggaran</p>
    </div>
    <a href="{{ route('budget-verifications.create') }}" class="btn btn-primary text-sm">+ Buat Verifikasi</a>
</div>

@if (session('success'))
    <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
@endif

{{-- Filter --}}
<div class="card mb-5">
    <div class="card-body">
        <form method="GET" action="{{ route('budget-verifications.index') }}" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="form-label">Rekomendasi</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="setuju"  {{ request('status') === 'setuju'  ? 'selected' : '' }}>Setuju</option>
                    <option value="tunda"   {{ request('status') === 'tunda'   ? 'selected' : '' }}>Tunda</option>
                    <option value="tolak"   {{ request('status') === 'tolak'   ? 'selected' : '' }}>Tolak</option>
                </select>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('budget-verifications.index') }}" class="btn btn-secondary text-sm">Reset</a>
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
                        <th class="px-4 py-3 text-left">No. Form</th>
                        <th class="px-4 py-3 text-right">Total Estimasi</th>
                        <th class="px-4 py-3 text-right">Nominal Direkomendasikan</th>
                        <th class="px-4 py-3 text-center">Rekomendasi</th>
                        <th class="px-4 py-3 text-left">Verifikator</th>
                        <th class="px-4 py-3 text-left">Tgl Verifikasi</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($verifications as $v)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <a href="{{ route('budget-requests.show', $v->budgetRequest) }}"
                                class="font-mono text-xs text-blue-600 hover:underline">
                                {{ $v->budgetRequest?->nomor_form ?? '-' }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-700">
                            Rp {{ number_format($v->budgetRequest?->total_estimasi ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-800">
                            {{ $v->nominal_rekomendasi ? 'Rp '.number_format($v->nominal_rekomendasi, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $rekMap = ['setuju'=>['bg-green-100','text-green-700','Setuju'],'tunda'=>['bg-yellow-100','text-yellow-700','Tunda'],'tolak'=>['bg-red-100','text-red-700','Tolak']];
                                [$rbg,$rc,$rl] = $rekMap[$v->rekomendasi] ?? ['bg-gray-100','text-gray-600',$v->rekomendasi];
                            @endphp
                            <span class="px-2 py-0.5 rounded text-xs font-medium {{ $rbg }} {{ $rc }}">{{ $rl }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $v->verifiedBy?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-500">
                            {{ \Carbon\Carbon::parse($v->verified_at)->translatedFormat('d M Y') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('budget-verifications.show', $v) }}"
                                    class="text-blue-600 hover:text-blue-800 text-xs font-medium">Detail</a>
                                <a href="{{ route('budget-verifications.edit', $v) }}"
                                    class="text-yellow-600 hover:text-yellow-800 text-xs font-medium">Edit</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-gray-400 text-sm">Belum ada verifikasi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($verifications->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $verifications->links() }}</div>
        @endif
    </div>
</div>
@endsection





{{-- resources/views/superadmin/budget_verification/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Detail Verifikasi Anggaran')

@section('breadcrumb')
<a href="{{ route('superadmin.budget-verifications.index') }}" class="text-gray-500 hover:text-gray-700">Verifikasi
    Anggaran</a>
<span class="text-gray-400 mx-1">/</span>
<span class="text-gray-700 font-medium">Detail</span>
@endsection

@section('content')
@if (session('success'))
<div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
    {{ session('success') }}</div>
@endif

<div class="mb-6 flex flex-wrap items-start justify-between gap-3">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Detail Verifikasi Anggaran</h1>
        <p class="text-sm text-gray-500 mt-0.5">
            Pengajuan:
            <a href="{{ route('superadmin.budget-requests.show', $budgetVerification->budgetRequest) }}"
                class="text-blue-600 hover:underline font-mono">
                {{ $budgetVerification->budgetRequest?->nomor_form ?? '-' }}
            </a>
        </p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('superadmin.budget-verifications.edit', $budgetVerification) }}"
            class="btn btn-secondary text-sm">Edit</a>
        <a href="{{ route('superadmin.budget-verifications.index') }}" class="btn btn-secondary text-sm">← Kembali</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 space-y-5">

        {{-- Dokumen --}}
        <div class="card">
            <div class="card-header">
                <h2 class="font-semibold text-gray-800">Kelengkapan Dokumen</h2>
            </div>
            <div class="card-body space-y-3 text-sm">
                @php
                $checks = [
                'doc_form_lengkap' => 'Form pengajuan lengkap',
                'doc_surat_justifikasi' => 'Surat justifikasi terlampir',
                'doc_estimasi_vendor' => 'Estimasi vendor terlampir',
                'doc_spesifikasi_teknis' => 'Spesifikasi teknis tersedia',
                ];
                @endphp
                @foreach ($checks as $field => $label)
                <div class="flex items-center gap-3">
                    @if ($budgetVerification->$field)
                    <span class="text-green-500">✓</span>
                    <span class="text-gray-800">{{ $label }}</span>
                    @else
                    <span class="text-gray-300">✗</span>
                    <span class="text-gray-400">{{ $label }}</span>
                    @endif
                </div>
                @endforeach
                @if ($budgetVerification->doc_lainnya)
                <div class="pt-2 border-t border-gray-100">
                    <span class="text-gray-500">Lainnya: </span>
                    <span class="text-gray-800">{{ $budgetVerification->doc_lainnya }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Analisa Finance --}}
        <div class="card">
            <div class="card-header">
                <h2 class="font-semibold text-gray-800">Analisa Finance</h2>
            </div>
            <div class="card-body space-y-4 text-sm">
                @if ($budgetVerification->cek_anggaran)
                <div>
                    <dt class="text-gray-500 mb-1">Cek Anggaran</dt>
                    <dd class="text-gray-800">{{ $budgetVerification->cek_anggaran }}</dd>
                </div>
                @endif
                @if ($budgetVerification->analisa_cashflow)
                <div>
                    <dt class="text-gray-500 mb-1">Analisa Cashflow</dt>
                    <dd class="text-gray-800">{{ $budgetVerification->analisa_cashflow }}</dd>
                </div>
                @endif
                @if ($budgetVerification->catatan_finance)
                <div>
                    <dt class="text-gray-500 mb-1">Catatan Finance</dt>
                    <dd class="text-gray-800">{{ $budgetVerification->catatan_finance }}</dd>
                </div>
                @endif
            </div>
        </div>

        {{-- Item Pengajuan --}}
        @if ($budgetVerification->budgetRequest?->items->count())
        <div class="card">
            <div class="card-header">
                <h2 class="font-semibold text-gray-800">Item Pengajuan</h2>
            </div>
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wide">
                            <tr>
                                <th class="px-4 py-3 text-left">#</th>
                                <th class="px-4 py-3 text-left">Nama Item</th>
                                <th class="px-4 py-3 text-right">Qty</th>
                                <th class="px-4 py-3 text-right">Estimasi</th>
                                <th class="px-4 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($budgetVerification->budgetRequest->items as $i => $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-400">{{ $i+1 }}</td>
                                <td class="px-4 py-3 text-gray-800">{{ $item->nama_item }}</td>
                                <td class="px-4 py-3 text-right text-gray-600">{{ $item->qty ?? '-' }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">Rp
                                    {{ number_format($item->estimasi_biaya, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-semibold">Rp
                                    {{ number_format($item->total, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-t-2 border-gray-200 bg-gray-50">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right font-semibold text-gray-700">Total</td>
                                <td class="px-4 py-3 text-right font-bold text-gray-900">
                                    Rp
                                    {{ number_format($budgetVerification->budgetRequest->total_estimasi, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        @endif

    </div>

    {{-- Sidebar --}}
    <div class="space-y-5">
        <div class="card">
            <div class="card-header">
                <h2 class="font-semibold text-gray-800">Hasil Verifikasi</h2>
            </div>
            <div class="card-body space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Rekomendasi</span>
                    @php
                    $rekMap =
                    ['setuju'=>['bg-green-100','text-green-700','Setuju'],'tunda'=>['bg-yellow-100','text-yellow-700','Tunda'],'tolak'=>['bg-red-100','text-red-700','Tolak']];
                    [$rbg,$rc,$rl] = $rekMap[$budgetVerification->rekomendasi] ??
                    ['bg-gray-100','text-gray-600',$budgetVerification->rekomendasi];
                    @endphp
                    <span class="px-2 py-0.5 rounded text-xs font-medium {{ $rbg }} {{ $rc }}">{{ $rl }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Total Estimasi</span>
                    <span class="font-semibold text-gray-800">
                        Rp {{ number_format($budgetVerification->budgetRequest?->total_estimasi ?? 0, 0, ',', '.') }}
                    </span>
                </div>
                @if ($budgetVerification->nominal_rekomendasi)
                <div class="flex justify-between">
                    <span class="text-gray-500">Nominal Direkomendasikan</span>
                    <span class="font-bold text-gray-900">Rp
                        {{ number_format($budgetVerification->nominal_rekomendasi, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-500">Verifikator</span>
                    <span class="font-medium text-gray-800">{{ $budgetVerification->verifiedBy?->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Tanggal</span>
                    <span
                        class="text-gray-700">{{ \Carbon\Carbon::parse($budgetVerification->verified_at)->translatedFormat('d M Y, H:i') }}</span>
                </div>
            </div>
        </div>

        {{-- Link ke Pengajuan --}}
        @if ($budgetVerification->budgetRequest)
        <div class="card">
            <div class="card-header">
                <h2 class="font-semibold text-gray-800">Pengajuan Terkait</h2>
            </div>
            <div class="card-body text-sm space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-500">Nomor Form</span>
                    <a href="{{ route('superadmin.budget-requests.show', $budgetVerification->budgetRequest) }}"
                        class="font-mono text-blue-600 hover:underline text-xs">
                        {{ $budgetVerification->budgetRequest->nomor_form }}
                    </a>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Divisi</span>
                    <span class="font-medium text-gray-800">{{ $budgetVerification->budgetRequest->divisi }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Jenis</span>
                    <span
                        class="uppercase font-medium text-gray-800">{{ $budgetVerification->budgetRequest->jenis }}</span>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection



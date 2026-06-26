{{-- ============================================================
     expense_reports/show.blade.php
============================================================ --}}
@extends('superadmin.layouts.app')
@section('title','Detail Laporan Pertanggungjawaban')
@section('breadcrumb')
<a href="{{ route('superadmin.expense-reports.index') }}" class="hover:text-primary-700">Lap. Pertanggungjawaban</a>
<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-700 font-medium">Detail</span>
@endsection

@section('content')
<div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-3 flex-wrap">
            <h1 class="text-xl font-bold text-gray-900">Lap. Pertanggungjawaban</h1>
            <x-status-badge :status="$expenseReport->status" />
        </div>
        <p class="text-sm text-gray-500 mt-0.5">Disubmit: <strong>{{ $expenseReport->submittedBy->name ?? '—' }}</strong> · {{ $expenseReport->created_at->isoFormat('D MMM Y') }}</p>
    </div>
    <div class="flex gap-2">
        @if($expenseReport->status === 'submitted')
        <a href="{{ route('superadmin.expense-reports.edit', $expenseReport) }}" class="btn-secondary btn">Edit</a>
        <form method="POST" action="{{ route('superadmin.expense-reports.verify', $expenseReport) }}" class="inline">@csrf<button class="btn-success btn">✓ Verifikasi</button></form>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
    <div class="card p-5 space-y-3 text-sm">
        <p class="font-semibold mb-2">Detail Realisasi</p>
        <div class="flex justify-between"><span class="text-gray-400">Pengajuan Anggaran</span><span class="font-mono text-primary-700">{{ $expenseReport->budgetRequest->nomor_form ?? '—' }}</span></div>
        <div class="flex justify-between"><span class="text-gray-400">Nama Item</span><span class="font-medium">{{ $expenseReport->budgetRequest->nama_item ?? '—' }}</span></div>
        <div class="flex justify-between"><span class="text-gray-400">Vendor</span><span>{{ $expenseReport->nama_vendor ?? '—' }}</span></div>
        <div class="flex justify-between"><span class="text-gray-400">No. Invoice</span><span class="font-mono">{{ $expenseReport->nomor_invoice ?? '—' }}</span></div>
        <div class="flex justify-between"><span class="text-gray-400">Tgl. Transaksi</span><span>{{ \Carbon\Carbon::parse($expenseReport->tanggal_transaksi)->isoFormat('D MMMM Y') }}</span></div>
    </div>
    <div class="card p-5 space-y-3 text-sm">
        <p class="font-semibold mb-2">Ringkasan Keuangan</p>
        <div class="flex justify-between"><span class="text-gray-400">Anggaran</span><span class="font-medium">Rp {{ number_format($expenseReport->budgetRequest->estimasi_biaya ?? 0) }}</span></div>
        <div class="flex justify-between"><span class="text-gray-400">Realisasi</span><span class="font-bold text-lg">Rp {{ number_format($expenseReport->nominal_realisasi) }}</span></div>
        <div class="flex justify-between border-t pt-3"><span class="text-gray-400">Selisih</span>
            <span class="font-bold {{ ($expenseReport->selisih ?? 0) > 0 ? 'text-red-600' : 'text-green-700' }}">
                {{ ($expenseReport->selisih ?? 0) > 0 ? '+' : '' }}Rp {{ number_format($expenseReport->selisih ?? 0) }}
                <span class="text-xs font-normal text-gray-400">{{ ($expenseReport->selisih ?? 0) > 0 ? '(melebihi)' : '(sisa)' }}</span>
            </span>
        </div>
    </div>
</div>

<div class="card p-5 mb-5">
    <p class="font-semibold mb-3">Kelengkapan Dokumen</p>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        @foreach(['lamp_invoice' => 'Invoice', 'lamp_bukti_transfer' => 'Bukti Transfer', 'lamp_kartu_garansi' => 'Kartu Garansi', 'lamp_serah_terima' => 'Serah Terima'] as $key => $label)
        <div class="flex items-center gap-2 text-sm">
            @if($expenseReport->$key)
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            @else
            <svg class="w-5 h-5 text-gray-300 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            @endif
            <span class="{{ $expenseReport->$key ? 'text-gray-800' : 'text-gray-400' }}">{{ $label }}</span>
        </div>
        @endforeach
    </div>
    @if($expenseReport->lamp_lainnya)<p class="text-sm text-gray-500 mt-2">Lainnya: {{ $expenseReport->lamp_lainnya }}</p>@endif
</div>

@if($expenseReport->catatan)
<div class="card p-5 mb-5"><p class="font-semibold mb-2">Catatan</p><p class="text-sm text-gray-700">{{ $expenseReport->catatan }}</p></div>
@endif

@if($expenseReport->verified_by)
<div class="card p-5 bg-green-50 border-green-200">
    <p class="font-semibold text-green-900">✓ Terverifikasi</p>
    <p class="text-sm text-green-700 mt-1">Oleh: {{ $expenseReport->verifiedBy->name }} · {{ \Carbon\Carbon::parse($expenseReport->verified_at)->isoFormat('D MMM Y, HH:mm') }}</p>
</div>
@endif
@endsection

{{-- resources/views/superadmin/expense_report/show.blade.php --}}
@extends('layouts.app')
@section('title','Detail Laporan Pertanggungjawaban')
@section('breadcrumb')
<a href="{{ route('superadmin.expense-reports.index') }}" class="hover:text-primary-700">Laporan Pertanggungjawaban</a>
<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
</svg>
<span class="text-gray-700 font-medium">Detail</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Laporan Pertanggungjawaban</h1>
        <p class="text-sm text-gray-500">Disubmit oleh {{ $expenseReport->submittedBy->name ?? '—' }} pada
            {{ $expenseReport->created_at?->isoFormat('D MMM Y, HH:mm') }}</p>
    </div>
    <span class="badge {{ match($expenseReport->status) {
    'verified' => 'badge-success',
    'pending_revisi' => 'badge-danger',
    default => 'badge-warning',
} }}">
        {{ ucfirst(str_replace('_', ' ', $expenseReport->status)) }}
    </span>
</div>

@if (session('success'))
<div class="mb-5 rounded-md bg-green-50 border border-green-200 p-4 text-sm text-green-700">{{ session('success') }}
</div>
@endif
@if (session('error'))
<div class="mb-5 rounded-md bg-red-50 border border-red-200 p-4 text-sm text-red-700">{{ session('error') }}</div>
@endif

<div class="card mb-5">
    <div class="card-header">
        <h2 class="font-semibold text-gray-800">Pengajuan Anggaran Terkait</h2>
    </div>
    <div class="card-body text-sm">
        <p class="font-mono text-primary-700">{{ $expenseReport->budgetRequest->nomor_form ?? '—' }}</p>
        <p class="text-gray-600 mt-1">
            Item: {{ $expenseReport->budgetRequest->items->pluck('nama_item')->join(', ') ?? '—' }}
        </p>
        <p class="text-gray-600">
            Total Anggaran: Rp {{ number_format($expenseReport->budgetRequest->total_estimasi ?? 0, 0, ',', '.') }}
        </p>
    </div>
</div>

<div class="card mb-5">
    <div class="card-header">
        <h2 class="font-semibold text-gray-800">Detail Transaksi</h2>
    </div>
    <div class="card-body grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        <div>
            <p class="text-gray-500">Nomor Invoice</p>
            <p class="font-medium text-gray-900">{{ $expenseReport->nomor_invoice ?? '—' }}</p>
        </div>
        <div>
            <p class="text-gray-500">Nama Vendor</p>
            <p class="font-medium text-gray-900">{{ $expenseReport->nama_vendor ?? '—' }}</p>
        </div>
        <div>
            <p class="text-gray-500">Tanggal Transaksi</p>
            <p class="font-medium text-gray-900">
                {{ \Carbon\Carbon::parse($expenseReport->tanggal_transaksi)->isoFormat('D MMM Y') }}</p>
        </div>
        <div>
            <p class="text-gray-500">Nominal Realisasi</p>
            <p class="font-semibold text-gray-900">Rp
                {{ number_format($expenseReport->nominal_realisasi, 0, ',', '.') }}</p>
        </div>
        <div>
            <p class="text-gray-500">Selisih (Anggaran - Realisasi)</p>
            <p
                class="font-semibold {{ $expenseReport->selisih < 0 ? 'text-red-600' : ($expenseReport->selisih > 0 ? 'text-green-600' : 'text-gray-700') }}">
                Rp {{ number_format($expenseReport->selisih, 0, ',', '.') }}
            </p>
        </div>
    </div>
</div>

<div class="card mb-5">
    <div class="card-header">
        <h2 class="font-semibold text-gray-800">Lampiran</h2>
    </div>
    <div class="card-body">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-sm mb-4">
            <span class="badge {{ $expenseReport->lamp_invoice ? 'badge-success' : 'badge-secondary' }}">Invoice
                {{ $expenseReport->lamp_invoice ? '✓' : '✗' }}</span>
            <span class="badge {{ $expenseReport->lamp_bukti_transfer ? 'badge-success' : 'badge-secondary' }}">Bukti
                Transfer {{ $expenseReport->lamp_bukti_transfer ? '✓' : '✗' }}</span>
            <span class="badge {{ $expenseReport->lamp_kartu_garansi ? 'badge-success' : 'badge-secondary' }}">Kartu
                Garansi {{ $expenseReport->lamp_kartu_garansi ? '✓' : '✗' }}</span>
            <span class="badge {{ $expenseReport->lamp_serah_terima ? 'badge-success' : 'badge-secondary' }}">Serah
                Terima {{ $expenseReport->lamp_serah_terima ? '✓' : '✗' }}</span>
        </div>

        @if ($expenseReport->lamp_lainnya)
        <p class="text-sm text-gray-600 mb-3">Lampiran lainnya: {{ $expenseReport->lamp_lainnya }}</p>
        @endif

        @if ($expenseReport->attachment_files)
        <ul class="text-sm text-primary-700 space-y-1">
            @foreach ($expenseReport->attachment_files as $path)
            <li><a href="{{ asset('storage/' . $path) }}" target="_blank" class="hover:underline">📎
                    {{ basename($path) }}</a></li>
            @endforeach
        </ul>
        @else
        <p class="text-sm text-gray-400">Tidak ada file lampiran terupload.</p>
        @endif
    </div>
</div>

@if ($expenseReport->catatan)
<div class="card mb-5">
    <div class="card-header">
        <h2 class="font-semibold text-gray-800">Catatan</h2>
    </div>
    <div class="card-body text-sm text-gray-700">{{ $expenseReport->catatan }}</div>
</div>
@endif
@if ($expenseReport->status === 'pending_revisi')
<div class="mb-5 rounded-md bg-orange-50 border border-orange-200 p-4 text-sm text-orange-700">
    ⚠️ Nominal realisasi melebihi sisa anggaran saat itu. Revisi anggaran tambahan otomatis diajukan dan sedang
    menunggu persetujuan — laporan ini akan otomatis final (tercatat di buku kas) setelah revisi disetujui.
</div>
@endif

@if ($expenseReport->revision)
<div class="card mb-5">
    <div class="card-header">
        <h2 class="font-semibold text-gray-800">Revisi Anggaran Terkait</h2>
    </div>
    <div class="card-body grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        <div>
            <p class="text-gray-500">Status Revisi</p>
            <p class="font-medium text-gray-900">{{ ucfirst($expenseReport->revision->status) }}</p>
        </div>
        <div>
            <p class="text-gray-500">Nominal Tambahan</p>
            <p class="font-medium text-gray-900">Rp
                {{ number_format($expenseReport->revision->nominal_perubahan, 0, ',', '.') }}</p>
        </div>
        <div class="sm:col-span-2">
            <p class="text-gray-500">Alasan</p>
            <p class="text-gray-700">{{ $expenseReport->revision->alasan_revisi }}</p>
        </div>
    </div>
</div>
@endif

@if ($expenseReport->status === 'verified')
<div class="card mb-5">
    <div class="card-header">
        <h2 class="font-semibold text-gray-800">Verifikasi</h2>
    </div>
    <div class="card-body grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        <div>
            <p class="text-gray-500">Diverifikasi Oleh</p>
            <p class="font-medium text-gray-900">{{ $expenseReport->verifiedBy->name ?? '—' }}</p>
        </div>
        <div>
            <p class="text-gray-500">Tanggal Verifikasi</p>
            <p class="font-medium text-gray-900">
                {{ $expenseReport->verified_at ? \Carbon\Carbon::parse($expenseReport->verified_at)->isoFormat('D MMM Y, HH:mm') : '—' }}
            </p>
        </div>
    </div>
</div>
@endif

<div class="flex justify-between">
    <a href="{{ route('superadmin.expense-reports.index') }}" class="btn-secondary btn">&larr; Kembali</a>

    <div class="flex gap-3">
        @if ($expenseReport->status !== 'verified')
        <a href="{{ route('superadmin.expense-reports.edit', $expenseReport) }}" class="btn-secondary btn">Edit</a>
        <form method="POST" action="{{ route('superadmin.expense-reports.verify', $expenseReport) }}" class="inline">
            @csrf
            <button type="submit" class="btn-primary btn"
                onclick="return confirm('Verifikasi laporan ini?')">Verifikasi</button>
        </form>
        @endif
    </div>
</div>
@endsection


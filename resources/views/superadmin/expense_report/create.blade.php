{{-- resources/views/superadmin/expense_report/create.blade.php --}}
@extends('layouts.app')
@section('title','Buat Laporan Pertanggungjawaban')
@section('breadcrumb')
<a href="{{ route('superadmin.expense-reports.index') }}" class="hover:text-primary-700">Laporan Pertanggungjawaban</a>
<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
</svg>
<span class="text-gray-700 font-medium">Buat Baru</span>
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <div class="card-header">
            <h2 class="font-semibold text-gray-900">Form Laporan Pertanggungjawaban</h2>
        </div>

        @if ($errors->any())
        <div class="card-body border-b">
            <div class="rounded-md bg-red-50 border border-red-200 p-4">
                <ul class="text-sm text-red-700 list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('superadmin.expense-reports.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="card-body space-y-4">

                <div>
                    <label class="form-label">Pengajuan Anggaran <span class="text-red-500">*</span></label>
                    <select name="budget_request_id" required class="form-select">
                        <option value="">— Pilih —</option>
                        @foreach ($budgetRequests as $br)
                        <option value="{{ $br->id }}" {{ old('budget_request_id') == $br->id ? 'selected' : '' }}>
                            {{ $br->nomor_form }} — {{ $br->items->pluck('nama_item')->join(', ') }} (Rp
                            {{ number_format($br->total_estimasi, 0, ',', '.') }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Nomor Invoice</label>
                        <input type="text" name="nomor_invoice" value="{{ old('nomor_invoice') }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Nama Vendor</label>
                        <input type="text" name="nama_vendor" value="{{ old('nama_vendor') }}" class="form-input">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Tanggal Transaksi <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_transaksi"
                            value="{{ old('tanggal_transaksi', now()->format('Y-m-d')) }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Nominal Realisasi <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" min="0" name="nominal_realisasi"
                            value="{{ old('nominal_realisasi') }}" class="form-input">
                    </div>
                </div>

                <div>
                    <label class="form-label mb-2 block">Lampiran yang Disertakan</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="lamp_invoice" value="1"
                                {{ old('lamp_invoice') ? 'checked' : '' }}>
                            Invoice
                        </label>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="lamp_bukti_transfer" value="1"
                                {{ old('lamp_bukti_transfer') ? 'checked' : '' }}>
                            Bukti Transfer
                        </label>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="lamp_kartu_garansi" value="1"
                                {{ old('lamp_kartu_garansi') ? 'checked' : '' }}>
                            Kartu Garansi
                        </label>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="lamp_serah_terima" value="1"
                                {{ old('lamp_serah_terima') ? 'checked' : '' }}>
                            Serah Terima
                        </label>
                    </div>
                </div>

                <div>
                    <label class="form-label">Lampiran Lainnya (keterangan)</label>
                    <input type="text" name="lamp_lainnya" value="{{ old('lamp_lainnya') }}" class="form-input"
                        placeholder="Opsional">
                </div>

                <div>
                    <label class="form-label">Upload File Lampiran</label>
                    <input type="file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf" class="form-input">
                    <p class="text-xs text-gray-400 mt-1">Bisa pilih lebih dari satu file. Maks 5MB per file (jpg, png,
                        pdf).</p>
                </div>

                <div>
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" rows="3" class="form-textarea">{{ old('catatan') }}</textarea>
                </div>
            </div>

            <div class="card-body border-t flex justify-end gap-3">
                <a href="{{ route('superadmin.expense-reports.index') }}" class="btn-secondary btn">Batal</a>
                <button type="submit" class="btn-primary btn">Kirim Laporan</button>
            </div>
        </form>
    </div>
</div>
@endsection



@extends('layouts.app')

@section('title', 'Detail Buku Kas')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Detail Buku Kas</h4>
            <small class="text-muted">{{ $cashBook->no_bukti }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('superadmin.cash-books.edit', $cashBook) }}" class="btn btn-warning btn-sm">
                <i class="bi bi-pencil-square me-1"></i> Edit
            </a>
            <a href="{{ route('superadmin.cash-books.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Alert --}}
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">

        {{-- Info Utama --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-3">
                    <i class="bi bi-journal-text text-primary"></i>
                    <span class="fw-semibold">Informasi Kas</span>
                    <span
                        class="ms-auto badge {{ $cashBook->type === 'masuk' ? 'bg-success' : 'bg-danger' }} text-uppercase">
                        {{ $cashBook->type === 'masuk' ? 'Kas Masuk' : 'Kas Keluar' }}
                    </span>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">

                        <dt class="col-sm-4 text-muted fw-normal">No. Bukti</dt>
                        <dd class="col-sm-8 fw-semibold font-monospace">{{ $cashBook->no_bukti }}</dd>

                        <dt class="col-sm-4 text-muted fw-normal">Tanggal</dt>
                        <dd class="col-sm-8">
                            {{ \Carbon\Carbon::parse($cashBook->tanggal)->translatedFormat('d F Y') }}
                        </dd>

                        <dt class="col-sm-4 text-muted fw-normal">Pihak</dt>
                        <dd class="col-sm-8">{{ $cashBook->pihak }}</dd>

                        <dt class="col-sm-4 text-muted fw-normal">Jumlah Uang</dt>
                        <dd class="col-sm-8">
                            <span
                                class="fs-5 fw-bold {{ $cashBook->type === 'masuk' ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($cashBook->jumlah_uang, 0, ',', '.') }}
                            </span>
                        </dd>

                        <dt class="col-sm-4 text-muted fw-normal">Terbilang</dt>
                        <dd class="col-sm-8 fst-italic text-capitalize">{{ $cashBook->terbilang }}</dd>

                        <dt class="col-sm-4 text-muted fw-normal">Keterangan</dt>
                        <dd class="col-sm-8">
                            {{ $cashBook->keterangan ?? '-' }}
                        </dd>

                    </dl>
                </div>
            </div>
        </div>

        {{-- Sidebar: Meta & Payment --}}
        <div class="col-lg-4 d-flex flex-column gap-3">

            {{-- Meta --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-3">
                    <i class="bi bi-info-circle text-secondary"></i>
                    <span class="fw-semibold">Meta Data</span>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">

                        <dt class="col-5 text-muted fw-normal small">Dibuat Oleh</dt>
                        <dd class="col-7 small">
                            {{ $cashBook->createdBy?->name ?? '-' }}
                        </dd>

                        <dt class="col-5 text-muted fw-normal small">Dibuat Pada</dt>
                        <dd class="col-7 small">
                            {{ $cashBook->created_at?->translatedFormat('d M Y, H:i') ?? '-' }}
                        </dd>

                        <dt class="col-5 text-muted fw-normal small">Diupdate</dt>
                        <dd class="col-7 small">
                            {{ $cashBook->updated_at?->translatedFormat('d M Y, H:i') ?? '-' }}
                        </dd>

                    </dl>
                </div>
            </div>

            {{-- Linked Payment --}}
            @if ($cashBook->payment)
            <div class="card shadow-sm border-0 border-start border-4 border-primary">
                <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-3">
                    <i class="bi bi-receipt text-primary"></i>
                    <span class="fw-semibold">Terkait Pembayaran</span>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted fw-normal small">No. Pembayaran</dt>
                        <dd class="col-7 small font-monospace fw-semibold">
                            {{ $cashBook->payment->payment_number }}
                        </dd>
                    </dl>
                </div>
            </div>
            @endif

        </div>
    </div>

    {{-- Print Button --}}
    <div class="mt-4 text-end">
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-printer me-1"></i> Cetak
        </button>
    </div>

</div>
@endsection

{{-- ============================================================
     cash_books/index.blade.php
============================================================ --}}
@extends('layouts.app')
@section('title','Buku Kas')
@section('breadcrumb')<span class="text-gray-700 font-medium">Buku Kas</span>@endsection

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Buku Kas</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $books->total() }} entri</p>
    </div>
    <a href="{{ route('superadmin.cash-books.create') }}" class="btn-primary btn">+ Tambah Entri</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
    <div class="stat-card">
        <div class="stat-icon bg-green-50 text-green-600">💰</div>
        <div>
            <p class="text-xl font-bold text-green-700">Rp {{ number_format($totalMasuk) }}</p>
            <p class="text-sm text-gray-500">Total Masuk</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-red-50 text-red-600">💸</div>
        <div>
            <p class="text-xl font-bold text-red-600">Rp {{ number_format($totalKeluar) }}</p>
            <p class="text-sm text-gray-500">Total Keluar</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-blue-50 text-blue-600">🏦</div>
        <div>
            <p class="text-xl font-bold text-primary-700">Rp {{ number_format($totalMasuk - $totalKeluar) }}</p>
            <p class="text-sm text-gray-500">Saldo Bersih</p>
        </div>
    </div>
</div>

<div class="card mb-5">
    <form method="GET" class="card-body flex flex-wrap gap-3 items-end">
        <div class="w-36"><label class="form-label">Tipe</label>
            <select name="type" class="form-select">
                <option value="">Semua</option>
                <option value="masuk" {{ request('type')==='masuk'?'selected':'' }}>Masuk</option>
                <option value="keluar" {{ request('type')==='keluar'?'selected':'' }}>Keluar</option>
            </select>
        </div>
        <div class="w-36"><label class="form-label">Dari</label><input type="date" name="date_from"
                value="{{ request('date_from') }}" class="form-input"></div>
        <div class="w-36"><label class="form-label">Sampai</label><input type="date" name="date_to"
                value="{{ request('date_to') }}" class="form-input"></div>
        <button type="submit" class="btn-primary btn">Filter</button>
        <a href="{{ route('superadmin.cash-books.index') }}" class="btn-secondary btn">Reset</a>
    </form>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>No. Bukti</th>
                <th>Tanggal</th>
                <th>Tipe</th>
                <th>Pihak</th>
                <th class="text-right">Jumlah</th>
                <th>Keterangan</th>
                <th class="text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($books as $b)
            <tr>
                <td><span class="font-mono text-xs font-medium">{{ $b->no_bukti }}</span></td>
                <td>{{ \Carbon\Carbon::parse($b->tanggal)->isoFormat('D MMM Y') }}</td>
                <td><span
                        class="badge {{ $b->type === 'masuk' ? 'badge-success' : 'badge-danger' }}">{{ ucfirst($b->type) }}</span>
                </td>
                <td class="max-w-xs truncate">{{ $b->pihak }}</td>
                <td class="text-right font-semibold {{ $b->type === 'masuk' ? 'text-green-700' : 'text-red-600' }}">
                    {{ $b->type === 'masuk' ? '+' : '-' }} Rp {{ number_format($b->jumlah_uang) }}
                </td>
                <td class="text-gray-500 max-w-xs truncate">{{ $b->keterangan ?? '—' }}</td>
                <td class="text-right"><a href="{{ route('superadmin.cash-books.show', $b) }}"
                        class="btn btn-secondary btn-sm">Detail</a></td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-12 text-gray-400">Belum ada entri buku kas</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $books->links() }}</div>
@endsection

{{-- resources/views/superadmin/cashbook/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold text-gray-800">Edit Entri Kas</h1>
        <a href="{{ route('superadmin.cash-books.show', $cashBook) }}"
            class="text-sm text-gray-600 hover:text-gray-900">&larr; Kembali</a>
    </div>

    @if ($errors->any())
    <div class="mb-4 rounded-md bg-red-50 border border-red-200 p-4">
        <ul class="text-sm text-red-700 list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('superadmin.cash-books.update', $cashBook) }}" method="POST"
        class="bg-white rounded-lg shadow p-6 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">No. Bukti</label>
            <input type="text" value="{{ $cashBook->no_bukti }}" disabled
                class="w-full rounded-md border-gray-200 bg-gray-100 text-gray-500 shadow-sm">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Transaksi</label>
            <input type="text" value="{{ $cashBook->type == 'masuk' ? 'Kas Masuk' : 'Kas Keluar' }}" disabled
                class="w-full rounded-md border-gray-200 bg-gray-100 text-gray-500 shadow-sm">
            <p class="text-xs text-gray-400 mt-1">Tipe transaksi tidak dapat diubah.</p>
        </div>

        <div>
            <label for="pihak" class="block text-sm font-medium text-gray-700 mb-1">Pihak</label>
            <input type="text" name="pihak" id="pihak" value="{{ old('pihak', $cashBook->pihak) }}"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div>
            <label for="jumlah_uang" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Uang</label>
            <input type="number" step="0.01" min="0" name="jumlah_uang" id="jumlah_uang"
                value="{{ old('jumlah_uang', $cashBook->jumlah_uang) }}"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div>
            <label for="terbilang" class="block text-sm font-medium text-gray-700 mb-1">Terbilang</label>
            <input type="text" name="terbilang" id="terbilang" value="{{ old('terbilang', $cashBook->terbilang) }}"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div>
            <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
            <input type="date" name="tanggal" id="tanggal"
                value="{{ old('tanggal', \Carbon\Carbon::parse($cashBook->tanggal)->format('Y-m-d')) }}"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div>
            <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
            <textarea name="keterangan" id="keterangan" rows="3"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('keterangan', $cashBook->keterangan) }}</textarea>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('superadmin.cash-books.show', $cashBook) }}"
                class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 text-sm hover:bg-gray-50">Batal</a>
            <button type="submit"
                class="px-4 py-2 rounded-md bg-indigo-600 text-white text-sm hover:bg-indigo-700">Update</button>
        </div>
    </form>
</div>
@endsection


{{-- resources/views/superadmin/cashbook/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold text-gray-800">Tambah Entri Kas</h1>
        <a href="{{ route('superadmin.cash-books.index') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr;
            Kembali</a>
    </div>

    @if ($errors->any())
    <div class="mb-4 rounded-md bg-red-50 border border-red-200 p-4">
        <ul class="text-sm text-red-700 list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('superadmin.cash-books.store') }}" method="POST"
        class="bg-white rounded-lg shadow p-6 space-y-5">
        @csrf

        <div>
            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Tipe Transaksi</label>
            <select name="type" id="type"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">-- Pilih Tipe --</option>
                <option value="masuk" {{ old('type') == 'masuk' ? 'selected' : '' }}>Kas Masuk</option>
                <option value="keluar" {{ old('type') == 'keluar' ? 'selected' : '' }}>Kas Keluar</option>
            </select>
        </div>

        <div>
            <label for="pihak" class="block text-sm font-medium text-gray-700 mb-1">Pihak</label>
            <input type="text" name="pihak" id="pihak" value="{{ old('pihak') }}"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Nama pihak terkait">
        </div>

        <div>
            <label for="jumlah_uang" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Uang</label>
            <input type="number" step="0.01" min="0" name="jumlah_uang" id="jumlah_uang"
                value="{{ old('jumlah_uang') }}"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="0">
        </div>

        <div>
            <label for="terbilang" class="block text-sm font-medium text-gray-700 mb-1">Terbilang</label>
            <input type="text" name="terbilang" id="terbilang" value="{{ old('terbilang') }}"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Contoh: Seratus ribu rupiah">
        </div>

        <div>
            <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
            <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal') }}"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div>
            <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
            <textarea name="keterangan" id="keterangan" rows="3"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Opsional">{{ old('keterangan') }}</textarea>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('superadmin.cash-books.index') }}"
                class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 text-sm hover:bg-gray-50">Batal</a>
            <button type="submit"
                class="px-4 py-2 rounded-md bg-indigo-600 text-white text-sm hover:bg-indigo-700">Simpan</button>
        </div>
    </form>
</div>
@endsection


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
    <a href="{{ route('superadmin.budget-verifications.create') }}" class="btn btn-primary text-sm">+ Buat
        Verifikasi</a>
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
        <form method="GET" action="{{ route('superadmin.budget-verifications.index') }}"
            class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="form-label">Rekomendasi</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="setuju" {{ request('status') === 'setuju'  ? 'selected' : '' }}>Setuju</option>
                    <option value="tunda" {{ request('status') === 'tunda'   ? 'selected' : '' }}>Tunda</option>
                    <option value="tolak" {{ request('status') === 'tolak'   ? 'selected' : '' }}>Tolak</option>
                </select>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('superadmin.budget-verifications.index') }}"
                    class="btn btn-secondary text-sm">Reset</a>
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
                            <a href="{{ route('superadmin.budget-requests.show', $v->budgetRequest) }}"
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
                            $rekMap =
                            ['setuju'=>['bg-green-100','text-green-700','Setuju'],'tunda'=>['bg-yellow-100','text-yellow-700','Tunda'],'tolak'=>['bg-red-100','text-red-700','Tolak']];
                            [$rbg,$rc,$rl] = $rekMap[$v->rekomendasi] ??
                            ['bg-gray-100','text-gray-600',$v->rekomendasi];
                            @endphp
                            <span class="px-2 py-0.5 rounded text-xs font-medium {{ $rbg }} {{ $rc }}">{{ $rl }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $v->verifiedBy?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-500">
                            {{ \Carbon\Carbon::parse($v->verified_at)->translatedFormat('d M Y') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('superadmin.budget-verifications.show', $v) }}"
                                    class="text-blue-600 hover:text-blue-800 text-xs font-medium">Detail</a>
                                <a href="{{ route('superadmin.budget-verifications.edit', $v) }}"
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

{{-- resources/views/superadmin/budget_verification/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Verifikasi Anggaran')

@section('breadcrumb')
<a href="{{ route('superadmin.budget-verifications.index') }}" class="text-gray-500 hover:text-gray-700">Verifikasi
    Anggaran</a>
<span class="text-gray-400 mx-1">/</span>
<a href="{{ route('superadmin.budget-verifications.show', $budgetVerification) }}"
    class="text-gray-500 hover:text-gray-700">Detail</a>
<span class="text-gray-400 mx-1">/</span>
<span class="text-gray-700 font-medium">Edit</span>
@endsection

@section('content')
<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-900">Edit Verifikasi Anggaran</h1>
    <p class="text-sm text-gray-500 mt-0.5">
        Pengajuan:
        <a href="{{ route('superadmin.budget-requests.show', $budgetVerification->budgetRequest) }}"
            class="text-blue-600 hover:underline font-mono">
            {{ $budgetVerification->budgetRequest?->nomor_form ?? '-' }}
        </a>
    </p>
</div>

@if ($errors->any())
<div class="mb-5 rounded-md bg-red-50 border border-red-200 p-4">
    <ul class="text-sm text-red-700 list-disc list-inside space-y-1">
        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('superadmin.budget-verifications.update', $budgetVerification) }}">
    @csrf @method('PUT')

    {{-- Info Pengajuan (readonly) --}}
    @if ($budgetVerification->budgetRequest)
    <div class="card mb-5">
        <div class="card-header">
            <h2 class="font-semibold text-gray-800">Pengajuan Terkait</h2>
        </div>
        <div class="card-body">
            <div class="rounded-md bg-blue-50 border border-blue-200 p-4 text-sm text-blue-800">
                <div class="flex justify-between mb-1">
                    <span>Nomor Form:</span>
                    <strong class="font-mono">{{ $budgetVerification->budgetRequest->nomor_form }}</strong>
                </div>
                <div class="flex justify-between mb-1">
                    <span>Divisi:</span>
                    <strong>{{ $budgetVerification->budgetRequest->divisi }}</strong>
                </div>
                <div class="flex justify-between">
                    <span>Total Estimasi:</span>
                    <strong>Rp
                        {{ number_format($budgetVerification->budgetRequest->total_estimasi, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Checklist Dokumen --}}
    <div class="card mb-5">
        <div class="card-header">
            <h2 class="font-semibold text-gray-800">Kelengkapan Dokumen</h2>
        </div>
        <div class="card-body space-y-3">
            @php
            $checks = [
            'doc_form_lengkap' => 'Form pengajuan lengkap',
            'doc_surat_justifikasi' => 'Surat justifikasi terlampir',
            'doc_estimasi_vendor' => 'Estimasi vendor terlampir',
            'doc_spesifikasi_teknis' => 'Spesifikasi teknis tersedia',
            ];
            @endphp
            @foreach ($checks as $name => $label)
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="{{ $name }}" value="1" class="rounded border-gray-300 text-blue-600"
                    {{ old($name, $budgetVerification->$name) ? 'checked' : '' }}>
                <span class="text-sm text-gray-700">{{ $label }}</span>
            </label>
            @endforeach
            <div>
                <label class="form-label">Dokumen Lainnya</label>
                <textarea name="doc_lainnya" rows="2"
                    class="form-textarea">{{ old('doc_lainnya', $budgetVerification->doc_lainnya) }}</textarea>
            </div>
        </div>
    </div>

    {{-- Analisa Finance --}}
    <div class="card mb-5">
        <div class="card-header">
            <h2 class="font-semibold text-gray-800">Analisa Finance</h2>
        </div>
        <div class="card-body grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="form-label">Cek Anggaran</label>
                <textarea name="cek_anggaran" rows="3"
                    class="form-textarea">{{ old('cek_anggaran', $budgetVerification->cek_anggaran) }}</textarea>
            </div>
            <div class="sm:col-span-2">
                <label class="form-label">Analisa Cashflow</label>
                <textarea name="analisa_cashflow" rows="3"
                    class="form-textarea">{{ old('analisa_cashflow', $budgetVerification->analisa_cashflow) }}</textarea>
            </div>
            <div>
                <label class="form-label">Rekomendasi <span class="text-red-500">*</span></label>
                <select name="rekomendasi" class="form-select" required>
                    <option value="setuju"
                        {{ old('rekomendasi', $budgetVerification->rekomendasi) === 'setuju' ? 'selected' : '' }}>Setuju
                    </option>
                    <option value="tunda"
                        {{ old('rekomendasi', $budgetVerification->rekomendasi) === 'tunda'  ? 'selected' : '' }}>Tunda
                    </option>
                    <option value="tolak"
                        {{ old('rekomendasi', $budgetVerification->rekomendasi) === 'tolak'  ? 'selected' : '' }}>Tolak
                    </option>
                </select>
            </div>
            <div>
                <label class="form-label">Nominal Rekomendasi</label>
                <input type="number" step="0.01" min="0" name="nominal_rekomendasi"
                    value="{{ old('nominal_rekomendasi', $budgetVerification->nominal_rekomendasi) }}"
                    class="form-input">
            </div>
            <div class="sm:col-span-2">
                <label class="form-label">Catatan Finance</label>
                <textarea name="catatan_finance" rows="3"
                    class="form-textarea">{{ old('catatan_finance', $budgetVerification->catatan_finance) }}</textarea>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('superadmin.budget-verifications.show', $budgetVerification) }}"
            class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </div>
</form>
@endsection


{{-- resources/views/superadmin/budget_verification/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Buat Verifikasi Anggaran')

@section('breadcrumb')
<a href="{{ route('superadmin.budget-verifications.index') }}" class="text-gray-500 hover:text-gray-700">Verifikasi
    Anggaran</a>
<span class="text-gray-400 mx-1">/</span>
<span class="text-gray-700 font-medium">Buat Verifikasi</span>
@endsection

@section('content')
<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-900">Buat Verifikasi Anggaran</h1>
    <p class="text-sm text-gray-500 mt-0.5">Isi form verifikasi finance untuk pengajuan yang sudah disetujui</p>
</div>

@if ($errors->any())
<div class="mb-5 rounded-md bg-red-50 border border-red-200 p-4">
    <ul class="text-sm text-red-700 list-disc list-inside space-y-1">
        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('superadmin.budget-verifications.store') }}">
    @csrf

    {{-- Pilih Pengajuan --}}
    <div class="card mb-5">
        <div class="card-header">
            <h2 class="font-semibold text-gray-800">Pilih Pengajuan Anggaran</h2>
        </div>
        <div class="card-body">
            <div class="max-w-lg">
                <label class="form-label">Nomor Form Pengajuan <span class="text-red-500">*</span></label>
                <select name="budget_request_id" id="budget_request_id" class="form-select" required
                    onchange="loadBudgetDetail(this)">
                    <option value="">-- Pilih Pengajuan --</option>
                    @foreach ($budgetRequests as $br)
                    <option value="{{ $br->id }}" data-total="{{ $br->total_estimasi }}"
                        data-items="{{ $br->items->pluck('nama_item')->join(', ') }}"
                        {{ old('budget_request_id', request('budget_request_id')) == $br->id ? 'selected' : '' }}>
                        {{ $br->nomor_form }} — Rp {{ number_format($br->total_estimasi, 0, ',', '.') }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Info Box Pengajuan --}}
            <div id="br-info"
                class="mt-4 hidden rounded-md bg-blue-50 border border-blue-200 p-4 text-sm text-blue-800">
                <div class="flex justify-between mb-1">
                    <span>Total Estimasi:</span>
                    <strong id="br-total">-</strong>
                </div>
                <div>
                    <span>Item:</span>
                    <span id="br-items" class="ml-1 text-blue-700"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Checklist Dokumen --}}
    <div class="card mb-5">
        <div class="card-header">
            <h2 class="font-semibold text-gray-800">Kelengkapan Dokumen</h2>
        </div>
        <div class="card-body space-y-3">
            @php
            $checks = [
            'doc_form_lengkap' => 'Form pengajuan lengkap',
            'doc_surat_justifikasi' => 'Surat justifikasi terlampir',
            'doc_estimasi_vendor' => 'Estimasi vendor terlampir',
            'doc_spesifikasi_teknis' => 'Spesifikasi teknis tersedia',
            ];
            @endphp
            @foreach ($checks as $name => $label)
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="{{ $name }}" value="1" class="rounded border-gray-300 text-blue-600"
                    {{ old($name) ? 'checked' : '' }}>
                <span class="text-sm text-gray-700">{{ $label }}</span>
            </label>
            @endforeach
            <div>
                <label class="form-label">Dokumen Lainnya</label>
                <textarea name="doc_lainnya" rows="2" class="form-textarea"
                    placeholder="Sebutkan dokumen lain jika ada...">{{ old('doc_lainnya') }}</textarea>
            </div>
        </div>
    </div>

    {{-- Analisa Finance --}}
    <div class="card mb-5">
        <div class="card-header">
            <h2 class="font-semibold text-gray-800">Analisa Finance</h2>
        </div>
        <div class="card-body grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="form-label">Cek Anggaran</label>
                <textarea name="cek_anggaran" rows="3" class="form-textarea"
                    placeholder="Pengecekan ketersediaan anggaran...">{{ old('cek_anggaran') }}</textarea>
            </div>
            <div class="sm:col-span-2">
                <label class="form-label">Analisa Cashflow</label>
                <textarea name="analisa_cashflow" rows="3" class="form-textarea"
                    placeholder="Analisa dampak terhadap cashflow...">{{ old('analisa_cashflow') }}</textarea>
            </div>
            <div>
                <label class="form-label">Rekomendasi <span class="text-red-500">*</span></label>
                <select name="rekomendasi" class="form-select" required>
                    <option value="">-- Pilih --</option>
                    <option value="setuju" {{ old('rekomendasi') === 'setuju' ? 'selected' : '' }}>Setuju</option>
                    <option value="tunda" {{ old('rekomendasi') === 'tunda'  ? 'selected' : '' }}>Tunda</option>
                    <option value="tolak" {{ old('rekomendasi') === 'tolak'  ? 'selected' : '' }}>Tolak</option>
                </select>
            </div>
            <div>
                <label class="form-label">Nominal Rekomendasi</label>
                <input type="number" step="0.01" min="0" name="nominal_rekomendasi"
                    value="{{ old('nominal_rekomendasi') }}" class="form-input"
                    placeholder="Kosongkan jika sama dengan estimasi">
            </div>
            <div class="sm:col-span-2">
                <label class="form-label">Catatan Finance</label>
                <textarea name="catatan_finance" rows="3" class="form-textarea"
                    placeholder="Catatan tambahan dari finance...">{{ old('catatan_finance') }}</textarea>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('superadmin.budget-verifications.index') }}" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Verifikasi</button>
    </div>
</form>

<script>
function loadBudgetDetail(sel) {
    const opt = sel.options[sel.selectedIndex];
    const info = document.getElementById('br-info');
    if (!opt.value) {
        info.classList.add('hidden');
        return;
    }
    document.getElementById('br-total').textContent = 'Rp ' + parseInt(opt.dataset.total).toLocaleString('id-ID');
    document.getElementById('br-items').textContent = opt.dataset.items || '-';
    info.classList.remove('hidden');
}
// Init on load if old value exists
window.addEventListener('DOMContentLoaded', () => {
    const sel = document.getElementById('budget_request_id');
    if (sel.value) loadBudgetDetail(sel);
});
</script>
@endsection

{{-- resources/views/superadmin/budget_revision/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Detail Revisi Anggaran')

@section('breadcrumb')
<a href="{{ route('superadmin.budget-revisions.index') }}" class="text-gray-500 hover:text-gray-700">Revisi Anggaran</a>
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
        <a href="{{ route('superadmin.budget-revisions.index') }}" class="btn btn-secondary text-sm">← Kembali</a>
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
        <form method="POST" action="{{ route('superadmin.budget-revisions.approve', $budgetRevision) }}">
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
        <form method="GET" action="{{ route('superadmin.budget_revisions.index') }}" class="flex flex-wrap gap-3 items-end">
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



{{-- resources/views/superadmin/budget_revision/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Ajukan Revisi Anggaran')

@section('breadcrumb')
<a href="{{ route('superadmin.budget-revisions.index') }}" class="text-gray-500 hover:text-gray-700">Revisi Anggaran</a>
<span class="text-gray-400 mx-1">/</span>
<span class="text-gray-700 font-medium">Ajukan Revisi</span>
@endsection

@section('content')
<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-900">Ajukan Revisi Anggaran</h1>
    <p class="text-sm text-gray-500 mt-0.5">Isi form untuk mengajukan perubahan anggaran yang sudah disetujui</p>
</div>

@if ($errors->any())
<div class="mb-5 rounded-md bg-red-50 border border-red-200 p-4">
    <ul class="text-sm text-red-700 list-disc list-inside space-y-1">
        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('superadmin.budget-revisions.store') }}">
    @csrf

    {{-- Pilih Pengajuan --}}
    <div class="card mb-5">
        <div class="card-header">
            <h2 class="font-semibold text-gray-800">Pengajuan Terkait (Opsional)</h2>
        </div>
        <div class="card-body">
            <div class="max-w-lg">
                <label class="form-label">Nomor Form Pengajuan</label>
                <select name="budget_request_id" id="budget_request_id" class="form-select"
                    onchange="loadBudgetDetail(this)">
                    <option value="">-- Tanpa pengajuan spesifik --</option>
                    @foreach ($budgetRequests as $br)
                    <option value="{{ $br->id }}" data-total="{{ $br->total_estimasi }}"
                        data-items="{{ $br->items->pluck('nama_item')->join(', ') }}"
                        {{ old('budget_request_id', request('budget_request_id')) == $br->id ? 'selected' : '' }}>
                        {{ $br->nomor_form }} — Rp {{ number_format($br->total_estimasi, 0, ',', '.') }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div id="br-info"
                class="mt-4 hidden rounded-md bg-blue-50 border border-blue-200 p-4 text-sm text-blue-800">
                <div class="flex justify-between mb-1">
                    <span>Total Estimasi:</span>
                    <strong id="br-total">-</strong>
                </div>
                <div>
                    <span>Item:</span>
                    <span id="br-items" class="ml-1 text-blue-700"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Revisi --}}
    <div class="card mb-5">
        <div class="card-header">
            <h2 class="font-semibold text-gray-800">Detail Revisi</h2>
        </div>
        <div class="card-body grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="form-label">Akun Terdampak <span class="text-red-500">*</span></label>
                <input type="text" name="akun_terdampak" value="{{ old('akun_terdampak') }}" class="form-input"
                    placeholder="Nama akun yang mengalami perubahan" required>
            </div>
            <div>
                <label class="form-label">Kode Akun</label>
                <input type="text" name="kode_akun" value="{{ old('kode_akun') }}" class="form-input font-mono"
                    placeholder="Contoh: 5-001">
            </div>
            <div>
                <label class="form-label">Jenis Perubahan <span class="text-red-500">*</span></label>
                <select name="jenis_perubahan" class="form-select" required>
                    <option value="">-- Pilih --</option>
                    <option value="tambahan" {{ old('jenis_perubahan') === 'tambahan'    ? 'selected' : '' }}>Tambahan
                    </option>
                    <option value="pengurangan" {{ old('jenis_perubahan') === 'pengurangan' ? 'selected' : '' }}>
                        Pengurangan</option>
                </select>
            </div>
            <div>
                <label class="form-label">Anggaran Awal <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="anggaran_awal" value="{{ old('anggaran_awal') }}"
                    class="form-input" required oninput="hitungBaru()">
            </div>
            <div>
                <label class="form-label">Realisasi <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="realisasi" value="{{ old('realisasi') }}"
                    class="form-input" required>
            </div>
            <div>
                <label class="form-label">Nominal Perubahan <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="nominal_perubahan" value="{{ old('nominal_perubahan') }}"
                    class="form-input" required oninput="hitungBaru()" id="nominal_perubahan">
            </div>
            <div>
                <label class="form-label">Anggaran Baru (estimasi)</label>
                <div id="anggaran-baru-preview" class="form-input bg-gray-50 text-gray-500 cursor-not-allowed">-</div>
            </div>
            <div class="sm:col-span-2">
                <label class="form-label">Alasan Revisi <span class="text-red-500">*</span></label>
                <textarea name="alasan_revisi" rows="4" class="form-textarea" required
                    placeholder="Jelaskan alasan revisi anggaran secara detail...">{{ old('alasan_revisi') }}</textarea>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('superadmin.budget-revisions.index') }}" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">Ajukan Revisi</button>
    </div>
</form>

<script>
function loadBudgetDetail(sel) {
    const opt = sel.options[sel.selectedIndex];
    const info = document.getElementById('br-info');
    if (!opt.value) {
        info.classList.add('hidden');
        return;
    }
    document.getElementById('br-total').textContent = 'Rp ' + parseInt(opt.dataset.total).toLocaleString('id-ID');
    document.getElementById('br-items').textContent = opt.dataset.items || '-';
    info.classList.remove('hidden');
    // prefill anggaran awal
    const awalInput = document.querySelector('[name="anggaran_awal"]');
    if (awalInput && !awalInput.value) {
        awalInput.value = opt.dataset.total;
        hitungBaru();
    }
}

function hitungBaru() {
    const awal = parseFloat(document.querySelector('[name="anggaran_awal"]')?.value) || 0;
    const nominal = parseFloat(document.getElementById('nominal_perubahan')?.value) || 0;
    const jenis = document.querySelector('[name="jenis_perubahan"]')?.value;
    let baru = jenis === 'pengurangan' ? awal - nominal : awal + nominal;
    document.getElementById('anggaran-baru-preview').textContent = 'Rp ' + baru.toLocaleString('id-ID');
}

document.querySelector('[name="jenis_perubahan"]')?.addEventListener('change', hitungBaru);

window.addEventListener('DOMContentLoaded', () => {
    const sel = document.getElementById('budget_request_id');
    if (sel.value) loadBudgetDetail(sel);
});
</script>
@endsection
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


@extends('layouts.app')

@section('title', 'Edit Pengajuan — ' . $budgetRequest->nomor_form)
@section('breadcrumb')
<a href="{{ route('superadmin.budget-requests.index') }}">Pengajuan Anggaran</a>
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
</svg>
<span class="text-gray-700 dark:text-gray-300 font-medium">Edit {{ $budgetRequest->nomor_form }}</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="budgetRequestEditForm()">

    <div>
        <h1 class="page-title">Edit Pengajuan — {{ $budgetRequest->nomor_form }}</h1>
        <p class="text-sm text-gray-500 mt-1">Hanya pengajuan berstatus Draft / Menunggu Admin yang dapat diubah.
        </p>
    </div>

    <form action="{{ route('superadmin.budget-requests.update', $budgetRequest) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card mb-6">
            <div class="card-header">
                <p class="font-semibold text-gray-800 dark:text-white">Informasi Umum</p>
            </div>
            <div class="card-body grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Divisi <span class="text-red-500">*</span></label>
                    <input type="text" name="divisi" class="form-input"
                        value="{{ old('divisi', $budgetRequest->divisi) }}" required>
                </div>
                <div>
                    <label class="form-label">Jenis</label>
                    <input type="text" class="form-input bg-gray-50 dark:bg-slate-800"
                        value="{{ $budgetRequest->jenis === 'rab' ? 'RAB' : 'Luar RAB' }}" readonly disabled>
                    <p class="text-xs text-gray-400 mt-1">Jenis tidak dapat diubah setelah dibuat.</p>
                </div>
                <div>
                    <label class="form-label">Urgensi</label>
                    <select name="urgensi" class="form-select">
                        <option value="normal" @selected($budgetRequest->urgensi == 'normal')>Normal</option>
                        <option value="mendesak" @selected($budgetRequest->urgensi == 'mendesak')>Mendesak</option>
                    </select>
                </div>
            </div>
        </div>

        @if ($budgetRequest->jenis === 'luar_rab')
        <div class="card mb-6">
            <div class="card-header">
                <p class="font-semibold text-gray-800 dark:text-white">Informasi Luar RAB</p>
            </div>
            <div class="card-body space-y-4">
                <div>
                    <label class="form-label">Alasan Luar RAB <span class="text-red-500">*</span></label>
                    <textarea name="alasan_luar_rab" rows="2"
                        class="form-textarea">{{ old('alasan_luar_rab', $budgetRequest->alasan_luar_rab) }}</textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Sumber Dana</label>
                        <select name="sumber_dana" class="form-select">
                            <option value="">- Pilih -</option>
                            <option value="realokasi" @selected($budgetRequest->sumber_dana == 'realokasi')>Realokasi
                            </option>
                            <option value="tambahan" @selected($budgetRequest->sumber_dana == 'tambahan')>Tambahan
                            </option>
                            <option value="lainnya" @selected($budgetRequest->sumber_dana == 'lainnya')>Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Dampak Jika Tidak Direalisasi</label>
                        <input type="text" name="dampak_jika_tidak" class="form-input"
                            value="{{ old('dampak_jika_tidak', $budgetRequest->dampak_jika_tidak) }}">
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Items --}}
        <div class="card mb-6">
            <div class="card-header">
                <p class="font-semibold text-gray-800 dark:text-white">Rincian Item</p>
                <button type="button" @click="addItem()" class="btn btn-secondary btn-sm">+ Tambah Item</button>
            </div>
            <div class="card-body space-y-4">
                <template x-for="(item, index) in items" :key="index">
                    <div class="border border-gray-200 dark:border-slate-700 rounded-lg p-4 relative">
                        <button type="button" x-show="items.length > 1" @click="removeItem(index)"
                            class="absolute top-3 right-3 text-red-500 hover:text-red-700">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="sm:col-span-2">
                                <label class="form-label">Nama Item <span class="text-red-500">*</span></label>
                                <input type="text" :name="`items[${index}][nama_item]`" class="form-input"
                                    x-model="item.nama_item" required>
                            </div>
                            <div>
                                <label class="form-label">Qty</label>
                                <input type="number" step="0.01" :name="`items[${index}][qty]`" class="form-input"
                                    x-model="item.qty" @input="calcTotal(index)">
                            </div>
                            <div>
                                <label class="form-label">Satuan</label>
                                <input type="text" :name="`items[${index}][satuan]`" class="form-input"
                                    x-model="item.satuan">
                            </div>
                            <div>
                                <label class="form-label">Estimasi Biaya (Rp) <span
                                        class="text-red-500">*</span></label>
                                <input type="number" :name="`items[${index}][estimasi_biaya]`" class="form-input"
                                    x-model="item.estimasi_biaya" required @input="calcTotal(index)">
                            </div>
                            <div>
                                <label class="form-label">Total</label>
                                <input type="text" class="form-input bg-gray-50 dark:bg-slate-800" readonly
                                    :value="formatRupiah(item.total)">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="form-label">Keterangan</label>
                                <input type="text" :name="`items[${index}][keterangan]`" class="form-input"
                                    x-model="item.keterangan">
                            </div>
                        </div>
                    </div>
                </template>

                <div class="flex justify-end pt-2 border-t border-gray-100 dark:border-slate-700">
                    <p class="font-semibold text-gray-900 dark:text-white">
                        Total Estimasi: <span class="text-indigo-600" x-text="formatRupiah(grandTotal())"></span>
                    </p>
                </div>
            </div>
        </div>

        <div class="card mb-6">
            <div class="card-body">
                <label class="form-label">Keterangan Tambahan</label>
                <textarea name="keterangan" rows="3"
                    class="form-textarea">{{ old('keterangan', $budgetRequest->keterangan) }}</textarea>
            </div>
        </div>

        <div class="flex gap-3 justify-end">
            <a href="{{ route('superadmin.budget-requests.show', $budgetRequest) }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>

<script>
function budgetRequestEditForm() {
    return {
        items: @json(
            $budgetRequest - > items - > map(fn($i) => [
                'nama_item' => $i - > nama_item,
                'qty' => $i - > qty,
                'satuan' => $i - > satuan,
                'estimasi_biaya' => $i - > estimasi_biaya,
                'keterangan' => $i - > keterangan,
                'total' => $i - > total,
            ])),
        addItem() {
            this.items.push({
                nama_item: '',
                qty: 1,
                satuan: '',
                estimasi_biaya: 0,
                keterangan: '',
                total: 0
            });
        },
        removeItem(index) {
            this.items.splice(index, 1);
        },
        calcTotal(index) {
            const item = this.items[index];
            const qty = parseFloat(item.qty) || 1;
            const harga = parseFloat(item.estimasi_biaya) || 0;
            item.total = qty * harga;
        },
        grandTotal() {
            return this.items.reduce((sum, item) => sum + (parseFloat(item.total) || 0), 0);
        },
        formatRupiah(value) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value || 0);
        }
    }
}
</script>
@endsection



@extends('layouts.app')

@section('title', 'Buat Pengajuan Anggaran')
@section('breadcrumb')
<a href="{{ route('superadmin.budget-requests.index') }}">Pengajuan Anggaran</a>
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
</svg>
<span class="text-gray-700 dark:text-gray-300 font-medium">Buat Baru</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="budgetRequestForm()">

    <div>
        <h1 class="page-title">Buat Pengajuan Anggaran</h1>
        <p class="text-sm text-gray-500 mt-1">Input manual pengajuan RAB sebagai Super Admin.</p>
    </div>

    <form action="{{ route('superadmin.budget-requests.store') }}" method="POST" @submit="return validateForm()">
        @csrf

        {{-- Info Umum --}}
        <div class="card mb-6">
            <div class="card-header">
                <p class="font-semibold text-gray-800 dark:text-white">Informasi Umum</p>
            </div>
            <div class="card-body grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Divisi <span class="text-red-500">*</span></label>
                    <input type="text" name="divisi" class="form-input" value="{{ old('divisi') }}" required>
                </div>
                <div>
                    <label class="form-label">Tanggal Pengajuan <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_pengajuan" class="form-input"
                        value="{{ old('tanggal_pengajuan', date('Y-m-d')) }}" required>
                </div>
                <div>
                    <label class="form-label">Jenis Pengajuan <span class="text-red-500">*</span></label>
                    <select name="jenis" x-model="jenis" class="form-select" required>
                        <option value="rab">RAB</option>
                        <option value="luar_rab">Luar RAB</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Urgensi</label>
                    <select name="urgensi" class="form-select">
                        <option value="normal">Normal</option>
                        <option value="mendesak">Mendesak</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- RAB-specific --}}
        <div class="card mb-6" x-show="jenis === 'rab'" x-cloak>
            <div class="card-header">
                <p class="font-semibold text-gray-800 dark:text-white">Akun Anggaran</p>
            </div>
            <div class="card-body grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Kode Akun</label>
                    <input type="text" name="kode_akun" class="form-input" value="{{ old('kode_akun') }}">
                </div>
                <div>
                    <label class="form-label">Nama Akun</label>
                    <input type="text" name="nama_akun" class="form-input" value="{{ old('nama_akun') }}">
                </div>
            </div>
        </div>

        {{-- Luar RAB specific --}}
        <div class="card mb-6" x-show="jenis === 'luar_rab'" x-cloak>
            <div class="card-header">
                <p class="font-semibold text-gray-800 dark:text-white">Informasi Luar RAB</p>
            </div>
            <div class="card-body space-y-4">
                <div>
                    <label class="form-label">Alasan Luar RAB <span class="text-red-500">*</span></label>
                    <textarea name="alasan_luar_rab" rows="2"
                        class="form-textarea">{{ old('alasan_luar_rab') }}</textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Sumber Dana</label>
                        <select name="sumber_dana" class="form-select">
                            <option value="">- Pilih -</option>
                            <option value="realokasi">Realokasi</option>
                            <option value="tambahan">Tambahan</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Dampak Jika Tidak Direalisasi</label>
                        <input type="text" name="dampak_jika_tidak" class="form-input">
                    </div>
                </div>
            </div>
        </div>

        {{-- Items --}}
        <div class="card mb-6">
            <div class="card-header">
                <p class="font-semibold text-gray-800 dark:text-white">Rincian Item</p>
                <button type="button" @click="addItem()" class="btn btn-secondary btn-sm">+ Tambah Item</button>
            </div>
            <div class="card-body space-y-4">
                <template x-for="(item, index) in items" :key="index">
                    <div class="border border-gray-200 dark:border-slate-700 rounded-lg p-4 relative">
                        <button type="button" x-show="items.length > 1" @click="removeItem(index)"
                            class="absolute top-3 right-3 text-red-500 hover:text-red-700">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="sm:col-span-2">
                                <label class="form-label">Nama Item <span class="text-red-500">*</span></label>
                                <input type="text" :name="`items[${index}][nama_item]`" class="form-input"
                                    x-model="item.nama_item" required>
                            </div>
                            <div>
                                <label class="form-label">Qty</label>
                                <input type="number" step="0.01" :name="`items[${index}][qty]`" class="form-input"
                                    x-model="item.qty" @input="calcTotal(index)">
                            </div>
                            <div>
                                <label class="form-label">Satuan</label>
                                <input type="text" :name="`items[${index}][satuan]`" class="form-input"
                                    x-model="item.satuan">
                            </div>
                            <div>
                                <label class="form-label">Estimasi Biaya (Rp) <span
                                        class="text-red-500">*</span></label>
                                <input type="number" :name="`items[${index}][estimasi_biaya]`" class="form-input"
                                    x-model="item.estimasi_biaya" required @input="calcTotal(index)">
                            </div>
                            <div>
                                <label class="form-label">Total</label>
                                <input type="text" class="form-input bg-gray-50 dark:bg-slate-800" readonly
                                    :value="formatRupiah(item.total)">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="form-label">Keterangan</label>
                                <input type="text" :name="`items[${index}][keterangan]`" class="form-input"
                                    x-model="item.keterangan">
                            </div>
                        </div>
                    </div>
                </template>

                <div class="flex justify-end pt-2 border-t border-gray-100 dark:border-slate-700">
                    <p class="font-semibold text-gray-900 dark:text-white">
                        Total Estimasi: <span class="text-indigo-600" x-text="formatRupiah(grandTotal())"></span>
                    </p>
                </div>
            </div>
        </div>

        <div class="card mb-6">
            <div class="card-body">
                <label class="form-label">Keterangan Tambahan</label>
                <textarea name="keterangan" rows="3" class="form-textarea">{{ old('keterangan') }}</textarea>
            </div>
        </div>

        <div class="flex gap-3 justify-end">
            <a href="{{ route('superadmin.budget-requests.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan sebagai Draft</button>
        </div>
    </form>
</div>

<script>
function budgetRequestForm() {
    return {
        jenis: 'rab',
        items: [{
            nama_item: '',
            qty: 1,
            satuan: '',
            estimasi_biaya: 0,
            keterangan: '',
            total: 0
        }],
        addItem() {
            this.items.push({
                nama_item: '',
                qty: 1,
                satuan: '',
                estimasi_biaya: 0,
                keterangan: '',
                total: 0
            });
        },
        removeItem(index) {
            this.items.splice(index, 1);
        },
        calcTotal(index) {
            const item = this.items[index];
            const qty = parseFloat(item.qty) || 1;
            const harga = parseFloat(item.estimasi_biaya) || 0;
            item.total = qty * harga;
        },
        grandTotal() {
            return this.items.reduce((sum, item) => sum + (parseFloat(item.total) || 0), 0);
        },
        formatRupiah(value) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value || 0);
        },
        validateForm() {
            return true;
        }
    }
}
</script>
@endsection


{{-- barcodes/scan.blade.php --}}
@extends('layouts.app')
@section('title', 'Scan Barcode')
@section('breadcrumb')
<a href="{{ route('superadmin.barcodes.index') }}" class="hover:text-primary-700">Barcode</a>
<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
</svg>
<span class="text-gray-700 font-medium">Scan</span>
@endsection

@section('content')
<div class="max-w-xl">
    @if(session('scan_result'))
    @php $result = session('scan_result'); $product = $result['product']; @endphp
    <div class="card mb-5 border-green-300 bg-green-50">
        <div class="card-header bg-green-100">
            <h3 class="font-semibold text-green-900">✓ Produk Ditemukan</h3>
        </div>
        <div class="card-body space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-gray-500">Nama</span><span
                    class="font-bold">{{ $product->name }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">SKU</span><span
                    class="font-mono">{{ $product->sku }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Barcode</span><span
                    class="font-mono">{{ $product->barcode }}</span></div>
            <div class="flex justify-between"><span
                    class="text-gray-500">Kategori</span><span>{{ $product->category->name ?? '—' }}</span></div>
            @if(!empty($result['stockInfo']))
            <div class="flex justify-between border-t pt-2 mt-2"><span class="text-gray-500">Stok di Gudang</span><span
                    class="font-bold text-lg text-primary-700">{{ number_format($result['stockInfo']['quantity']) }}</span>
            </div>
            @endif
        </div>
        <div class="card-body border-t">
            <a href="{{ route('superadmin.products.show', $product) }}" class="btn-primary btn btn-sm">Lihat Produk
                →</a>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h2 class="font-semibold text-gray-900">Scan Barcode Produk</h2>
        </div>
        <form method="POST" action="{{ route('superadmin.barcodes.do-scan') }}">
            @csrf
            <div class="card-body space-y-4">
                <div>
                    <label class="form-label">Nilai Barcode / SKU <span class="text-red-500">*</span></label>
                    <input type="text" name="barcode_value" id="barcodeInput" required autofocus
                        class="form-input font-mono text-lg @error('barcode_value') border-red-400 @enderror"
                        placeholder="Scan atau ketik barcode...">
                    @error('barcode_value')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Tipe Scan <span class="text-red-500">*</span></label>
                    <select name="scan_type" required class="form-select">
                        <option value="check" {{ old('scan_type','check')==='check'    ? 'selected' : '' }}>Check /
                            Pengecekan</option>
                        <option value="stock_in" {{ old('scan_type')==='stock_in' ? 'selected' : '' }}>Stok Masuk
                        </option>
                        <option value="stock_out" {{ old('scan_type')==='stock_out'? 'selected' : '' }}>Stok Keluar
                        </option>
                        <option value="transfer" {{ old('scan_type')==='transfer' ? 'selected' : '' }}>Transfer</option>
                        <option value="purchase" {{ old('scan_type')==='purchase' ? 'selected' : '' }}>Pembelian
                        </option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Gudang</label>
                    <select name="warehouse_id" class="form-select">
                        <option value="">— Opsional —</option>
                        @foreach($warehouses as $w)
                        <option value="{{ $w->id }}" {{ old('warehouse_id') == $w->id ? 'selected' : '' }}>
                            {{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="card-body border-t flex justify-end gap-3">
                <a href="{{ route('superadmin.barcodes.index') }}" class="btn-secondary btn">Log</a>
                <button type="submit" class="btn-primary btn">🔍 Scan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-focus barcode input
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('barcodeInput').focus();
});
</script>
@endpush



{{-- barcodes/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Barcode Log')
@section('breadcrumb')<span class="text-gray-700 font-medium">Barcode</span>@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Barcode Scan Log</h1>
        <p class="text-sm text-gray-500">{{ $logs->total() }} scan</p>
    </div>
    <a href="{{ route('superadmin.barcodes.scan') }}" class="btn-primary btn">📷 Scan Barcode</a>
</div>

<div class="card mb-5">
    <form method="GET" class="card-body flex flex-wrap gap-3 items-end">
        <div class="w-40">
            <label class="form-label">Tipe Scan</label>
            <select name="scan_type" class="form-select">
                <option value="">Semua</option>
                @foreach (['stock_in', 'stock_out', 'transfer', 'check', 'purchase'] as $t)
                <option value="{{ $t }}" {{ request('scan_type') === $t ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $t)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-48">
            <label class="form-label">Gudang</label>
            <select name="warehouse_id" class="form-select">
                <option value="">Semua</option>
                @foreach ($warehouses as $w)
                <option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>
                    {{ $w->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-36">
            <label class="form-label">Ditemukan</label>
            <select name="is_found" class="form-select">
                <option value="">Semua</option>
                <option value="1" {{ request('is_found') === '1' ? 'selected' : '' }}>Ditemukan</option>
                <option value="0" {{ request('is_found') === '0' ? 'selected' : '' }}>Tidak Ditemukan</option>
            </select>
        </div>
        <div class="w-36"><label class="form-label">Dari</label><input type="date" name="date_from"
                value="{{ request('date_from') }}" class="form-input"></div>
        <div class="w-36"><label class="form-label">Sampai</label><input type="date" name="date_to"
                value="{{ request('date_to') }}" class="form-input"></div>
        <button type="submit" class="btn-primary btn">Filter</button>
        <a href="{{ route('superadmin.barcodes.index') }}" class="btn-secondary btn">Reset</a>
    </form>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>Barcode</th>
                <th>Produk</th>
                <th>Tipe Scan</th>
                <th>Gudang</th>
                <th>User</th>
                <th>Waktu</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($logs as $log)
            <tr class="{{ !$log->is_found ? 'bg-red-50' : '' }}">
                <td class="font-mono text-sm">{{ $log->barcode_value }}</td>
                <td>
                    @if ($log->product)
                    {{ $log->product->name }}
                    @else
                    <span class="text-red-500 text-xs">Tidak ditemukan</span>
                    @endif
                </td>
                <td><span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $log->scan_type)) }}</span>
                </td>
                <td>{{ $log->warehouse->name ?? '—' }}</td>
                <td>{{ $log->user->name ?? '—' }}</td>
                <td class="text-xs text-gray-500">{{ $log->created_at?->isoFormat('D MMM, HH:mm') ?? '—' }}</td>
                <td>
                    @if ($log->is_found)
                    <span class="badge badge-success">✓ Ditemukan</span>
                    @else
                    <span class="badge badge-danger">✗ Tidak Ada</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-12 text-gray-400">Belum ada scan log</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $logs->links() }}</div>
@endsection

@extends('superadmin.layouts.app')
@section('title', 'Detail Activity Log')
@section('breadcrumb')
<a href="{{ route('superadmin.activity-logs.index') }}" class="hover:text-primary-700">Activity Log</a>
<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-700 font-medium">Detail</span>
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <div class="card-header">
            <h2 class="font-semibold text-gray-900">Detail Activity Log</h2>
            <span class="badge {{ str_contains($activityLog->action,'create') ? 'badge-success' : (str_contains($activityLog->action,'delete') ? 'badge-danger' : 'badge-info') }}">
                {{ $activityLog->action }}
            </span>
        </div>
        <div class="card-body space-y-4 text-sm">
            <div class="grid grid-cols-2 gap-4">
                <div><p class="text-xs text-gray-400 mb-1">User</p><p class="font-medium">{{ $activityLog->user->name ?? 'System' }}</p></div>
                <div><p class="text-xs text-gray-400 mb-1">Waktu</p><p>{{ $activityLog->created_at->isoFormat('D MMMM Y, HH:mm:ss') }}</p></div>
                <div><p class="text-xs text-gray-400 mb-1">Model</p><p class="font-mono">{{ $activityLog->model_type ?? '—' }}</p></div>
                <div><p class="text-xs text-gray-400 mb-1">Model ID</p><p class="font-mono">{{ $activityLog->model_id ?? '—' }}</p></div>
                <div><p class="text-xs text-gray-400 mb-1">IP Address</p><p class="font-mono">{{ $activityLog->ip_address ?? '—' }}</p></div>
                <div><p class="text-xs text-gray-400 mb-1">User Agent</p><p class="text-gray-500 text-xs truncate">{{ $activityLog->user_agent ?? '—' }}</p></div>
            </div>

            @if($activityLog->old_values)
            <div>
                <p class="text-xs text-gray-400 mb-2 font-semibold uppercase tracking-wider">Data Sebelum</p>
                <pre class="bg-red-50 border border-red-100 rounded-lg p-3 text-xs overflow-x-auto text-red-800">{{ json_encode(json_decode($activityLog->old_values), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
            @endif

            @if($activityLog->new_values)
            <div>
                <p class="text-xs text-gray-400 mb-2 font-semibold uppercase tracking-wider">Data Sesudah</p>
                <pre class="bg-green-50 border border-green-100 rounded-lg p-3 text-xs overflow-x-auto text-green-800">{{ json_encode(json_decode($activityLog->new_values), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
            @endif

            @if($activityLog->description)
            <div>
                <p class="text-xs text-gray-400 mb-1">Deskripsi</p>
                <p class="text-gray-700">{{ $activityLog->description }}</p>
            </div>
            @endif
        </div>
        <div class="card-body border-t">
            <a href="{{ route('superadmin.activity-logs.index') }}" class="btn-secondary btn">← Kembali</a>
        </div>
    </div>
</div>
@endsection
{{-- activity_logs/index.blade.php --}}
@extends('layouts.app')
@section('title','Activity Log')
@section('breadcrumb')<span class="text-gray-700 font-medium">Activity Log</span>@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Activity Log</h1>
        <p class="text-sm text-gray-500">{{ $logs->total() }} entri</p>
    </div>
</div>

<div class="card mb-5">
    <form method="GET" class="card-body flex flex-wrap gap-3 items-end">
        <div class="w-44"><label class="form-label">User</label>
            <select name="user_id" class="form-select">
                <option value="">Semua User</option>
                @foreach($users as $u)<option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                    {{ $u->name }}</option>@endforeach
            </select>
        </div>
        <div class="flex-1 min-w-40"><label class="form-label">Action</label><input type="text" name="action"
                value="{{ request('action') }}" placeholder="cth. create, update..." class="form-input"></div>
        <div class="flex-1 min-w-40"><label class="form-label">Model</label><input type="text" name="model_type"
                value="{{ request('model_type') }}" placeholder="cth. Product, StockOpname..." class="form-input"></div>
        <div class="w-36"><label class="form-label">Dari</label><input type="date" name="date_from"
                value="{{ request('date_from') }}" class="form-input"></div>
        <div class="w-36"><label class="form-label">Sampai</label><input type="date" name="date_to"
                value="{{ request('date_to') }}" class="form-input"></div>
        <button type="submit" class="btn-primary btn">Filter</button>
        <a href="{{ route('superadmin.activity-logs.index') }}" class="btn-secondary btn">Reset</a>
    </form>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>User</th>
                <th>Action</th>
                <th>Model</th>
                <th>ID</th>
                <th>IP Address</th>
                <th>Waktu</th>
                <th class="text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($logs as $log)
            <tr>
                <td class="font-medium">{{ $log->user->name ?? 'System' }}</td>
                <td><span
                        class="badge {{ str_contains($log->action,'create') ? 'badge-success' : (str_contains($log->action,'delete') ? 'badge-danger' : 'badge-info') }}">{{ $log->action }}</span>
                </td>
                <td class="font-mono text-xs">{{ class_basename($log->model_type ?? '') }}</td>
                <td class="font-mono text-xs text-gray-500">{{ $log->model_id ?? '—' }}</td>
                <td class="font-mono text-xs text-gray-400">{{ $log->ip_address ?? '—' }}</td>
                <td class="text-sm text-gray-500">{{ $log->created_at->isoFormat('D MMM, HH:mm') }}</td>
                <td class="text-right"><a href="{{ route('superadmin.activity-logs.show', $log) }}"
                        class="btn btn-secondary btn-sm">Detail</a></td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-12 text-gray-400">Belum ada activity log</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $logs->links() }}</div>
@endsection
{{-- barcodes/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Barcode Log')
@section('breadcrumb')<span class="text-gray-700 font-medium">Barcode</span>@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Barcode Scan Log</h1>
        <p class="text-sm text-gray-500">{{ $logs->total() }} scan</p>
    </div>
    <a href="{{ route('superadmin.barcodes.scan') }}" class="btn-primary btn">📷 Scan Barcode</a>
</div>

<div class="card mb-5">
    <form method="GET" class="card-body flex flex-wrap gap-3 items-end">
        <div class="w-40">
            <label class="form-label">Tipe Scan</label>
            <select name="scan_type" class="form-select">
                <option value="">Semua</option>
                @foreach (['stock_in', 'stock_out', 'transfer', 'check', 'purchase'] as $t)
                <option value="{{ $t }}" {{ request('scan_type') === $t ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $t)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-48">
            <label class="form-label">Gudang</label>
            <select name="warehouse_id" class="form-select">
                <option value="">Semua</option>
                @foreach ($warehouses as $w)
                <option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>
                    {{ $w->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-36">
            <label class="form-label">Ditemukan</label>
            <select name="is_found" class="form-select">
                <option value="">Semua</option>
                <option value="1" {{ request('is_found') === '1' ? 'selected' : '' }}>Ditemukan</option>
                <option value="0" {{ request('is_found') === '0' ? 'selected' : '' }}>Tidak Ditemukan</option>
            </select>
        </div>
        <div class="w-36"><label class="form-label">Dari</label><input type="date" name="date_from"
                value="{{ request('date_from') }}" class="form-input"></div>
        <div class="w-36"><label class="form-label">Sampai</label><input type="date" name="date_to"
                value="{{ request('date_to') }}" class="form-input"></div>
        <button type="submit" class="btn-primary btn">Filter</button>
        <a href="{{ route('superadmin.barcodes.index') }}" class="btn-secondary btn">Reset</a>
    </form>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>Barcode</th>
                <th>Produk</th>
                <th>Tipe Scan</th>
                <th>Gudang</th>
                <th>User</th>
                <th>Waktu</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($logs as $log)
            <tr class="{{ !$log->is_found ? 'bg-red-50' : '' }}">
                <td class="font-mono text-sm">{{ $log->barcode_value }}</td>
                <td>
                    @if ($log->product)
                    {{ $log->product->name }}
                    @else
                    <span class="text-red-500 text-xs">Tidak ditemukan</span>
                    @endif
                </td>
                <td><span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $log->scan_type)) }}</span>
                </td>
                <td>{{ $log->warehouse->name ?? '—' }}</td>
                <td>{{ $log->user->name ?? '—' }}</td>
                <td class="text-xs text-gray-500">{{ $log->created_at?->isoFormat('D MMM, HH:mm') ?? '—' }}</td>
                <td>
                    @if ($log->is_found)
                    <span class="badge badge-success">✓ Ditemukan</span>
                    @else
                    <span class="badge badge-danger">✗ Tidak Ada</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-12 text-gray-400">Belum ada scan log</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $logs->links() }}</div>
@endsection

@extends('superadmin.layouts.app')
@section('title', 'Detail Activity Log')
@section('breadcrumb')
<a href="{{ route('superadmin.activity-logs.index') }}" class="hover:text-primary-700">Activity Log</a>
<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-700 font-medium">Detail</span>
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <div class="card-header">
            <h2 class="font-semibold text-gray-900">Detail Activity Log</h2>
            <span class="badge {{ str_contains($activityLog->action,'create') ? 'badge-success' : (str_contains($activityLog->action,'delete') ? 'badge-danger' : 'badge-info') }}">
                {{ $activityLog->action }}
            </span>
        </div>
        <div class="card-body space-y-4 text-sm">
            <div class="grid grid-cols-2 gap-4">
                <div><p class="text-xs text-gray-400 mb-1">User</p><p class="font-medium">{{ $activityLog->user->name ?? 'System' }}</p></div>
                <div><p class="text-xs text-gray-400 mb-1">Waktu</p><p>{{ $activityLog->created_at->isoFormat('D MMMM Y, HH:mm:ss') }}</p></div>
                <div><p class="text-xs text-gray-400 mb-1">Model</p><p class="font-mono">{{ $activityLog->model_type ?? '—' }}</p></div>
                <div><p class="text-xs text-gray-400 mb-1">Model ID</p><p class="font-mono">{{ $activityLog->model_id ?? '—' }}</p></div>
                <div><p class="text-xs text-gray-400 mb-1">IP Address</p><p class="font-mono">{{ $activityLog->ip_address ?? '—' }}</p></div>
                <div><p class="text-xs text-gray-400 mb-1">User Agent</p><p class="text-gray-500 text-xs truncate">{{ $activityLog->user_agent ?? '—' }}</p></div>
            </div>

            @if($activityLog->old_values)
            <div>
                <p class="text-xs text-gray-400 mb-2 font-semibold uppercase tracking-wider">Data Sebelum</p>
                <pre class="bg-red-50 border border-red-100 rounded-lg p-3 text-xs overflow-x-auto text-red-800">{{ json_encode(json_decode($activityLog->old_values), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
            @endif

            @if($activityLog->new_values)
            <div>
                <p class="text-xs text-gray-400 mb-2 font-semibold uppercase tracking-wider">Data Sesudah</p>
                <pre class="bg-green-50 border border-green-100 rounded-lg p-3 text-xs overflow-x-auto text-green-800">{{ json_encode(json_decode($activityLog->new_values), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
            @endif

            @if($activityLog->description)
            <div>
                <p class="text-xs text-gray-400 mb-1">Deskripsi</p>
                <p class="text-gray-700">{{ $activityLog->description }}</p>
            </div>
            @endif
        </div>
        <div class="card-body border-t">
            <a href="{{ route('superadmin.activity-logs.index') }}" class="btn-secondary btn">← Kembali</a>
        </div>
    </div>
</div>
@endsection
{{-- activity_logs/index.blade.php --}}
@extends('layouts.app')
@section('title','Activity Log')
@section('breadcrumb')<span class="text-gray-700 font-medium">Activity Log</span>@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Activity Log</h1>
        <p class="text-sm text-gray-500">{{ $logs->total() }} entri</p>
    </div>
</div>

<div class="card mb-5">
    <form method="GET" class="card-body flex flex-wrap gap-3 items-end">
        <div class="w-44"><label class="form-label">User</label>
            <select name="user_id" class="form-select">
                <option value="">Semua User</option>
                @foreach($users as $u)<option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                    {{ $u->name }}</option>@endforeach
            </select>
        </div>
        <div class="flex-1 min-w-40"><label class="form-label">Action</label><input type="text" name="action"
                value="{{ request('action') }}" placeholder="cth. create, update..." class="form-input"></div>
        <div class="flex-1 min-w-40"><label class="form-label">Model</label><input type="text" name="model_type"
                value="{{ request('model_type') }}" placeholder="cth. Product, StockOpname..." class="form-input"></div>
        <div class="w-36"><label class="form-label">Dari</label><input type="date" name="date_from"
                value="{{ request('date_from') }}" class="form-input"></div>
        <div class="w-36"><label class="form-label">Sampai</label><input type="date" name="date_to"
                value="{{ request('date_to') }}" class="form-input"></div>
        <button type="submit" class="btn-primary btn">Filter</button>
        <a href="{{ route('superadmin.activity-logs.index') }}" class="btn-secondary btn">Reset</a>
    </form>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>User</th>
                <th>Action</th>
                <th>Model</th>
                <th>ID</th>
                <th>IP Address</th>
                <th>Waktu</th>
                <th class="text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($logs as $log)
            <tr>
                <td class="font-medium">{{ $log->user->name ?? 'System' }}</td>
                <td><span
                        class="badge {{ str_contains($log->action,'create') ? 'badge-success' : (str_contains($log->action,'delete') ? 'badge-danger' : 'badge-info') }}">{{ $log->action }}</span>
                </td>
                <td class="font-mono text-xs">{{ class_basename($log->model_type ?? '') }}</td>
                <td class="font-mono text-xs text-gray-500">{{ $log->model_id ?? '—' }}</td>
                <td class="font-mono text-xs text-gray-400">{{ $log->ip_address ?? '—' }}</td>
                <td class="text-sm text-gray-500">{{ $log->created_at->isoFormat('D MMM, HH:mm') }}</td>
                <td class="text-right"><a href="{{ route('superadmin.activity-logs.show', $log) }}"
                        class="btn btn-secondary btn-sm">Detail</a></td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-12 text-gray-400">Belum ada activity log</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $logs->links() }}</div>
@endsection



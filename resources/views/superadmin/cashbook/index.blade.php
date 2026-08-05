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


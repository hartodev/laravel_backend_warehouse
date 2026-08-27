@extends('layouts.admin')
@section('title', 'Buku Kas')
@section('content')

<div class="admin-page-head">
    <h2>Buku Kas</h2>
    <a href="{{ route('admin.cashbook.create') }}" class="btn-primary">+ Catat Transaksi</a>
</div>

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif

<div class="admin-summary-grid" style="margin-bottom:16px;">
    <div class="admin-card admin-summary-card">
        <span class="cell-muted">Total Masuk</span>
        <p class="cell-mono" style="color:var(--success, #16a34a); font-size:1.25rem;">
            Rp {{ number_format($totalMasuk ?? 0, 0, ',', '.') }}
        </p>
    </div>
    <div class="admin-card admin-summary-card">
        <span class="cell-muted">Total Keluar</span>
        <p class="cell-mono" style="color:var(--danger, #dc2626); font-size:1.25rem;">
            Rp {{ number_format($totalKeluar ?? 0, 0, ',', '.') }}
        </p>
    </div>
    <div class="admin-card admin-summary-card">
        <span class="cell-muted">Saldo</span>
        <p class="cell-mono" style="font-size:1.25rem;">
            Rp {{ number_format(($totalMasuk ?? 0) - ($totalKeluar ?? 0), 0, ',', '.') }}
        </p>
    </div>
</div>

<form method="GET" class="admin-filter-bar">
    <select name="type" class="admin-select" style="max-width:160px;">
        <option value="">Semua Tipe</option>
        <option value="masuk" @selected(request('type')==='masuk' )>Masuk</option>
        <option value="keluar" @selected(request('type')==='keluar' )>Keluar</option>
    </select>
    <input type="date" name="date_from" class="admin-input" value="{{ request('date_from') }}" style="max-width:170px;">
    <input type="date" name="date_to" class="admin-input" value="{{ request('date_to') }}" style="max-width:170px;">
    <button class="btn-outline">Filter</button>
</form>

<div class="admin-card admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>No. Bukti</th>
                <th>Tanggal</th>
                <th>Tipe</th>
                <th>Pihak</th>
                <th>Jumlah</th>
                <th>Dibuat Oleh</th>
                <th>Status</th>
                <th class="cell-actions">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($books as $book)
            <tr>
                <td class="cell-mono">{{ $book->no_bukti }}</td>
                <td class="cell-muted">{{ optional($book->tanggal)->format('d M Y') ?? '-' }}</td>
                <td>
                    @if($book->type === 'masuk')
                    <span class="admin-badge admin-badge-success">Masuk</span>
                    @else
                    <span class="admin-badge admin-badge-danger">Keluar</span>
                    @endif
                </td>
                <td>{{ $book->pihak }}</td>
                <td class="cell-mono">Rp {{ number_format($book->jumlah_uang, 0, ',', '.') }}</td>
                <td>{{ $book->createdBy->name ?? '-' }}</td>
                <td>
                    @if($book->verified_at)
                    <span class="admin-badge admin-badge-success">Terverifikasi</span>
                    @else
                    <span class="admin-badge admin-badge-warning">Belum Verifikasi</span>
                    @endif
                </td>
                <td class="cell-actions">
                    <a href="{{ route('admin.cashbook.show', $book) }}" class="admin-link">Detail</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="cell-empty">Belum ada transaksi buku kas.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-pagination">{{ $books->appends(request()->query())->links() }}</div>
@endsection
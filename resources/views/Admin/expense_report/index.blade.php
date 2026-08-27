@extends('layouts.admin')
@section('title', 'Laporan Belanja')
@section('content')

<div class="admin-page-head">
    <h2>Laporan Pertanggungjawaban</h2>
    <a href="{{ route('admin.expense-reports.create') }}" class="btn-primary ripple"><i class="lucide-plus"></i> Buat
        LPJ</a>
</div>

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif

<form method="GET" class="admin-filter-bar">
    <select name="status" class="admin-select" style="max-width:180px;">
        <option value="">Semua Status</option>
        <option value="submitted" @selected(request('status')==='submitted' )>Submitted</option>
        <option value="pending_revisi" @selected(request('status')==='pending_revisi' )>Pending Revisi</option>
        <option value="verified" @selected(request('status')==='verified' )>Terverifikasi</option>
    </select>
    <input type="date" name="date_from" value="{{ request('date_from') }}" class="admin-input" style="max-width:160px;">
    <input type="date" name="date_to" value="{{ request('date_to') }}" class="admin-input" style="max-width:160px;">
    <button class="btn-outline">Filter</button>
</form>

<div class="admin-card admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>RAB Terkait</th>
                <th>No. Invoice</th>
                <th>Vendor</th>
                <th>Tanggal Transaksi</th>
                <th>Nominal Realisasi</th>
                <th>Diajukan Oleh</th>
                <th>Status</th>
                <th class="cell-actions">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reports as $report)
            <tr>
                <td class="cell-mono">{{ $report->budgetRequest->nomor_form ?? '-' }}</td>
                <td class="cell-mono">{{ $report->nomor_invoice ?? '-' }}</td>
                <td class="cell-muted">{{ $report->nama_vendor ?? '-' }}</td>
                <td>{{ optional($report->tanggal_transaksi)->format('d M Y') }}</td>
                <td class="cell-mono">Rp {{ number_format($report->nominal_realisasi, 0, ',', '.') }}</td>
                <td class="cell-muted">{{ $report->submittedBy->name ?? '-' }}</td>
                <td>
                    @if($report->status === 'verified')
                    <span class="admin-badge admin-badge-success">Terverifikasi</span>
                    @elseif($report->status === 'pending_revisi')
                    <span class="admin-badge admin-badge-warning">Pending Revisi</span>
                    @else
                    <span class="admin-badge admin-badge-info">Submitted</span>
                    @endif
                </td>
                <td class="cell-actions">
                    <a href="{{ route('admin.expense-reports.show', $report) }}" class="admin-link">Detail</a>
                    @if($report->status !== 'verified')
                    <a href="{{ route('admin.expense-reports.edit', $report) }}" class="admin-link">Edit</a>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="cell-empty">Belum ada laporan belanja.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-pagination">{{ $reports->appends(request()->query())->links() }}</div>
@endsection
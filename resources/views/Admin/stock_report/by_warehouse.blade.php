@extends('layouts.admin')
@section('title', 'Histori Laporan Stok')
@section('content')

<div class="admin-page-head">
    <h2>Histori Laporan — {{ $warehouse->name }} <span class="cell-mono cell-muted">({{ $warehouse->code }})</span></h2>
</div>

<form method="GET" class="admin-filter-bar">
    <select name="period_type" class="admin-select" style="max-width:160px;">
        <option value="">Semua Periode</option>
        <option value="daily" @selected(request('period_type')==='daily')>Harian</option>
        <option value="weekly" @selected(request('period_type')==='weekly')>Mingguan</option>
        <option value="monthly" @selected(request('period_type')==='monthly')>Bulanan</option>
    </select>
    <input type="date" name="date_from" value="{{ request('date_from') }}" class="admin-input" style="max-width:150px;">
    <input type="date" name="date_to" value="{{ request('date_to') }}" class="admin-input" style="max-width:150px;">
    <button class="btn-outline">Filter</button>
</form>

{{-- NB: kolom di tabel ini hanya memuat field yang terkonfirmasi dari StockReportController
     (product, period_type, period_date). Jika model StockReport punya kolom lain
     (mis. opening_stock/closing_stock/nilai), tambahkan kolomnya di sini. --}}
<div class="admin-card admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Periode</th>
                <th>Tipe Periode</th>
                <th>Produk</th>
                <th>SKU</th>
                <th>Satuan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reports as $report)
            <tr>
                <td class="cell-muted">{{ \Illuminate\Support\Carbon::parse($report->period_date)->format('d M Y') }}</td>
                <td class="cell-muted">{{ ucfirst($report->period_type) }}</td>
                <td>{{ $report->product->name ?? '-' }}</td>
                <td class="cell-mono">{{ $report->product->sku ?? '-' }}</td>
                <td class="cell-muted">{{ $report->product->unit ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="cell-empty">Belum ada histori laporan untuk gudang ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-pagination">{{ $reports->appends(request()->query())->links() }}</div>

<div class="admin-action-panel" style="margin-top:20px;">
    <a href="{{ route('admin.stock-reports.index') }}" class="btn-secondary">← Kembali</a>
</div>
@endsection

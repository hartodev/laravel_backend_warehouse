@extends('layouts.admin')
@section('title', 'Transfer Stok')
@section('content')

<div class="admin-page-head">
    <h2>Transfer Stok</h2>
    <a href="{{ route('admin.stock-transfers.create') }}" class="btn-primary ripple"><i class="lucide-plus"></i> Buat
        Transfer</a>
</div>

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif

@php
// Dipindahkan ke sini (dari dalam @foreach langsung) supaya ekspresi
// Blade tetap sederhana dan tidak rawan "Malformed @foreach statement"
// akibat array literal panjang / line-break di tengah string.
$statusOptions = [
'pending_confirmation' => 'Menunggu Konfirmasi',
'pending_approval' => 'Menunggu Approval',
'approved' => 'Disetujui',
'in_transit' => 'Dalam Pengiriman',
'discrepancy' => 'Selisih',
'received' => 'Diterima',
'rejected' => 'Ditolak',
'cancelled' => 'Dibatalkan',
];

$badgeMap = [
'pending_confirmation' => 'admin-badge-muted',
'pending_approval' => 'admin-badge-warning',
'approved' => 'admin-badge-info',
'in_transit' => 'admin-badge-info',
'discrepancy' => 'admin-badge-danger',
'received' => 'admin-badge-success',
'rejected' => 'admin-badge-danger',
'cancelled' => 'admin-badge-muted',
];

// labelMap sama isinya dengan statusOptions, jadi cukup pakai satu variabel saja
$labelMap = $statusOptions;
@endphp

<form method="GET" class="admin-filter-bar">
    <select name="status" class="admin-select" style="max-width:220px;">
        <option value="">Semua Status</option>
        @foreach($statusOptions as $val => $label)
        <option value="{{ $val }}" @selected(request('status')===$val)>{{ $label }}</option>
        @endforeach
    </select>
    <button class="btn-outline">Filter</button>
</form>

<div class="admin-card admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>No. Transfer</th>
                <th>Dari</th>
                <th>Ke</th>
                <th>Diminta Oleh</th>
                <th>Tgl. Transfer</th>
                <th>Status</th>
                <th class="cell-actions">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transfers as $transfer)
            <tr>
                <td class="cell-mono">{{ $transfer->transfer_number }}</td>
                <td class="cell-muted">{{ $transfer->fromWarehouse->name ?? '-' }}</td>
                <td class="cell-muted">{{ $transfer->toWarehouse->name ?? '-' }}</td>
                <td class="cell-muted">{{ $transfer->requestedBy->name ?? '-' }}</td>
                <td class="cell-muted">
                    {{ \Illuminate\Support\Carbon::parse($transfer->transfer_date)->format('d M Y') }}</td>
                <td><span
                        class="admin-badge {{ $badgeMap[$transfer->status] ?? 'admin-badge-muted' }}">{{ $labelMap[$transfer->status] ?? ucfirst($transfer->status) }}</span>
                </td>
                <td class="cell-actions">
                    <a href="{{ route('admin.stock-transfers.show', $transfer) }}" class="admin-link">Detail</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="cell-empty">Belum ada transfer stok.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-pagination">{{ $transfers->appends(request()->query())->links() }}</div>
@endsection
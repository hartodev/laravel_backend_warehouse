@extends('layouts.admin')

@section('title', 'Transfer Stok')
@section('page-title', 'Transfer Stok Antar Gudang')

@php
$statusColor = [
'pending_confirmation' => 'secondary',
'pending_approval' => 'warning',
'approved' => 'info',
'in_transit' => 'primary',
'received' => 'success',
'discrepancy' => 'danger',
'rejected' => 'danger',
'cancelled' => 'secondary',
];
$statusLabel = [
'pending_confirmation' => 'Menunggu Konfirmasi',
'pending_approval' => 'Menunggu Approval',
'approved' => 'Disetujui',
'in_transit' => 'Dalam Perjalanan',
'received' => 'Diterima',
'discrepancy' => 'Selisih',
'rejected' => 'Ditolak',
'cancelled' => 'Dibatalkan',
];
@endphp

@section('content')
<div class="card-panel">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex gap-2">
            <a href="{{ route('admin.stock-transfers.index') }}"
                class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }}">Semua</a>
            @foreach ($statusLabel as $key => $label)
            <a href="{{ route('admin.stock-transfers.index', ['status' => $key]) }}"
                class="btn btn-sm {{ request('status') === $key ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $label }}</a>
            @endforeach
        </div>
        <a href="{{ route('admin.stock-transfers.create') }}" class="btn btn-primary btn-sm">
            <i class="lucide-plus"></i> Buat Request Transfer
        </a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>No. Transfer</th>
                    <th>Dari</th>
                    <th>Ke</th>
                    <th>Diminta Oleh</th>
                    <th>Tgl Transfer</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transfers as $transfer)
                <tr>
                    <td class="fw-medium">{{ $transfer->transfer_number }}</td>
                    <td>{{ $transfer->fromWarehouse->name ?? '-' }}</td>
                    <td>{{ $transfer->toWarehouse->name ?? '-' }}</td>
                    <td class="text-muted small">{{ $transfer->requestedBy->name ?? '-' }}</td>
                    <td class="text-muted small">
                        {{ \Illuminate\Support\Carbon::parse($transfer->transfer_date)->format('d M Y') }}</td>
                    <td>
                        <span
                            class="badge-status bg-{{ $statusColor[$transfer->status] ?? 'secondary' }}-subtle text-{{ $statusColor[$transfer->status] ?? 'secondary' }}">
                            {{ $statusLabel[$transfer->status] ?? $transfer->status }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.stock-transfers.show', $transfer) }}"
                            class="btn btn-sm btn-outline-secondary">
                            <i class="lucide-eye"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Belum ada transfer stok.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $transfers->links() }}</div>
</div>
@endsection
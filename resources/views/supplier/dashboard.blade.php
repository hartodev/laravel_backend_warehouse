@extends('layouts.supplier')

@section('title', 'Dashboard Supplier')
@section('header', 'Selamat datang kembali')
@section('subheader', 'Ringkasan produk dan purchase order Anda hari ini')

@section('content')

@php
$statusStyles = [
'draft' => 'stamp-muted',
'pending' => 'stamp-amber',
'approved' => 'stamp-info',
'partial' => 'stamp-amber',
'received' => 'stamp-success',
'cancelled' => 'stamp-danger',
];
@endphp

<div class="stat-grid">
    <div class="stat-tag">
        <span class="label">Total Produk</span>
        <div class="value">{{ $stats['total_products'] }}</div>
    </div>
    <div class="stat-tag">
        <span class="label">PO Pending</span>
        <div class="value">{{ $stats['po_pending'] }}</div>
    </div>
    <div class="stat-tag">
        <span class="label">PO Disetujui</span>
        <div class="value">{{ $stats['po_approved'] }}</div>
    </div>
    <div class="stat-tag">
        <span class="label">PO Diterima</span>
        <div class="value">{{ $stats['po_received'] }}</div>
    </div>
</div>

<div class="section-head">
    <div>
        <span class="eyebrow">Aktivitas terbaru</span>
        <h2>Purchase Order Terbaru</h2>
    </div>
    <a href="{{ route('supplier.purchase-orders.index') }}" class="btn btn-outline btn-sm">Lihat semua PO</a>
</div>

<div class="table-wrap">
    <table class="manifest">
        <thead>
            <tr>
                <th>No. PO</th>
                <th>Gudang Tujuan</th>
                <th>Tgl Order</th>
                <th>Status</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($recentPOs as $po)
            <tr>
                <td>
                    <a href="{{ route('supplier.purchase-orders.show', $po) }}" class="mono" style="font-weight:600;">
                        {{ $po->po_number }}
                    </a>
                </td>
                <td>{{ $po->warehouse->name ?? '-' }}</td>
                <td class="text-muted">{{ \Illuminate\Support\Carbon::parse($po->order_date)->format('d/m/Y') }}</td>
                <td><span
                        class="stamp {{ $statusStyles[$po->status] ?? 'stamp-muted' }}">{{ ucfirst($po->status) }}</span>
                </td>
                <td class="text-right mono">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5">
                    <div class="empty-state">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.5">
                            <rect x="3" y="7" width="13" height="11" rx="1.5" />
                            <path d="M16 11h3.3a1 1 0 0 1 .8.4l1.9 2.5v3a1 1 0 0 1-1 1H16" />
                            <circle cx="7.5" cy="19.5" r="1.5" />
                            <circle cx="17.5" cy="19.5" r="1.5" />
                        </svg>
                        <div class="title">Belum ada purchase order</div>
                        <div class="desc">PO baru akan langsung muncul di sini begitu gudang membuat pesanan untuk Anda.
                        </div>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
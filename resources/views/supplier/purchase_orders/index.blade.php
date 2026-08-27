@extends('layouts.supplier')

@section('title', 'Purchase Order')
@section('header', 'Purchase Order')
@section('subheader', 'Semua pesanan yang dibuat gudang untuk Anda')

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

<div class="section-head">
    <div>
        <span class="eyebrow">Filter</span>
        <h2>Cari berdasarkan status</h2>
    </div>
    <form method="GET" class="field-inline">
        <select name="status" class="select" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            @foreach (['draft', 'pending', 'approved', 'partial', 'received', 'cancelled'] as $status)
            <option value="{{ $status }}" @selected(request('status')===$status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </form>
</div>

<div class="table-wrap">
    <table class="manifest">
        <thead>
            <tr>
                <th>No. PO</th>
                <th>Gudang Tujuan</th>
                <th>Tgl Order</th>
                <th>Estimasi Tiba</th>
                <th>Status</th>
                <th class="text-right">Total</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pos as $po)
            <tr>
                <td class="mono" style="font-weight:600;">{{ $po->po_number }}</td>
                <td>{{ $po->warehouse->name ?? '-' }}</td>
                <td class="text-muted">{{ \Illuminate\Support\Carbon::parse($po->order_date)->format('d/m/Y') }}</td>
                <td class="text-muted">
                    {{ $po->expected_date ? \Illuminate\Support\Carbon::parse($po->expected_date)->format('d/m/Y') : '-' }}
                </td>
                <td><span
                        class="stamp {{ $statusStyles[$po->status] ?? 'stamp-muted' }}">{{ ucfirst($po->status) }}</span>
                </td>
                <td class="text-right mono">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</td>
                <td><a href="{{ route('supplier.purchase-orders.show', $po) }}"
                        class="btn btn-outline btn-sm">Detail</a></td>
            </tr>
            @empty
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.5">
                            <rect x="3" y="7" width="13" height="11" rx="1.5" />
                            <path d="M16 11h3.3a1 1 0 0 1 .8.4l1.9 2.5v3a1 1 0 0 1-1 1H16" />
                            <circle cx="7.5" cy="19.5" r="1.5" />
                            <circle cx="17.5" cy="19.5" r="1.5" />
                        </svg>
                        <div class="title">Belum ada PO{{ request('status') ? ' dengan status ini' : '' }}</div>
                        <div class="desc">
                            @if (request('status'))
                            Coba pilih status lain, atau lihat semua purchase order.
                            @else
                            PO baru akan muncul di sini begitu gudang membuat pesanan untuk Anda.
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination-wrap">
    {{ $pos->links() }}
</div>

@endsection
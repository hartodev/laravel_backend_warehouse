@extends('layouts.admin')
@section('title', 'Detail Sales Order')
@section('content')

@php
$badgeMap = ['draft'=>'admin-badge-muted','confirmed'=>'admin-badge-info','processed'=>'admin-badge-warning','completed'=>'admin-badge-success','cancelled'=>'admin-badge-danger'];
@endphp

<div class="admin-page-head">
    <h2>SO {{ $salesOrder->so_number }}</h2>
    <span class="admin-badge {{ $badgeMap[$salesOrder->status] ?? 'admin-badge-muted' }}">{{ ucfirst($salesOrder->status) }}</span>
</div>

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif

<div class="admin-detail-grid" style="margin-bottom:20px;">
    <div class="admin-detail-item"><p class="admin-label">Customer</p><p>{{ $salesOrder->customer_name }}</p></div>
    <div class="admin-detail-item"><p class="admin-label">Alamat</p><p>{{ $salesOrder->customer_address ?? '-' }}</p></div>
    <div class="admin-detail-item"><p class="admin-label">Gudang</p><p>{{ $salesOrder->warehouse->name ?? '-' }}</p></div>
    <div class="admin-detail-item"><p class="admin-label">Metode Pembayaran</p><p>{{ ucfirst($salesOrder->payment_method) }}</p></div>
    <div class="admin-detail-item"><p class="admin-label">Tanggal Order</p><p>{{ \Illuminate\Support\Carbon::parse($salesOrder->order_date)->format('d M Y') }}</p></div>
    <div class="admin-detail-item"><p class="admin-label">Jatuh Tempo</p><p>{{ $salesOrder->due_date ? \Illuminate\Support\Carbon::parse($salesOrder->due_date)->format('d M Y') : '-' }}</p></div>
    <div class="admin-detail-item"><p class="admin-label">Dibuat Oleh</p><p>{{ $salesOrder->createdBy->name ?? '-' }}</p></div>
    @if($salesOrder->approvedBy)
    <div class="admin-detail-item"><p class="admin-label">Disetujui Oleh</p><p>{{ $salesOrder->approvedBy->name }}</p></div>
    @endif
    @if($salesOrder->notes)
    <div class="admin-detail-item" style="grid-column:span 2;"><p class="admin-label">Catatan</p><p>{{ $salesOrder->notes }}</p></div>
    @endif
</div>

<div class="admin-card admin-table-wrap" style="margin-bottom:12px;">
    <table class="admin-table">
        <thead>
            <tr><th>Produk</th><th>Qty</th><th>Harga Satuan</th><th>Diskon</th><th>Subtotal</th></tr>
        </thead>
        <tbody>
            @foreach($salesOrder->items as $item)
            <tr>
                <td>{{ $item->product->name ?? '-' }} <span class="cell-muted cell-mono">({{ $item->product->sku ?? '-' }})</span></td>
                <td class="cell-mono">{{ $item->quantity }} {{ $item->product->unit ?? '' }}</td>
                <td class="cell-mono">Rp{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="cell-mono">{{ $item->discount_percent }}%</td>
                <td class="cell-mono">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="admin-detail-grid" style="margin-bottom:20px;max-width:320px;margin-left:auto;">
    <div class="admin-detail-item"><p class="admin-label">Subtotal</p><p>Rp{{ number_format($salesOrder->subtotal, 0, ',', '.') }}</p></div>
    <div class="admin-detail-item"><p class="admin-label">Pajak ({{ $salesOrder->tax_percent }}%)</p><p>Rp{{ number_format($salesOrder->tax_amount, 0, ',', '.') }}</p></div>
    <div class="admin-detail-item"><p class="admin-label">Diskon</p><p>Rp{{ number_format($salesOrder->discount_amount, 0, ',', '.') }}</p></div>
    <div class="admin-detail-item"><p class="admin-label"><strong>Total</strong></p><p><strong>Rp{{ number_format($salesOrder->total_amount, 0, ',', '.') }}</strong></p></div>
</div>

@if($salesOrder->payments->isNotEmpty())
<div class="admin-card admin-table-wrap" style="margin-bottom:20px;">
    <table class="admin-table">
        <thead><tr><th>Tanggal</th><th>Metode</th><th>Jumlah</th></tr></thead>
        <tbody>
            @foreach($salesOrder->payments as $payment)
            <tr>
                <td class="cell-muted">{{ $payment->created_at->format('d M Y') }}</td>
                <td class="cell-muted">{{ $payment->payment_method ?? '-' }}</td>
                <td class="cell-mono">Rp{{ number_format($payment->amount ?? 0, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<div class="admin-action-panel" style="display:flex;justify-content:space-between;">
    <a href="{{ route('admin.sales-orders.index') }}" class="btn-secondary">← Kembali</a>
    @if(in_array($salesOrder->status, ['draft','confirmed']))
    <a href="{{ route('admin.sales-orders.edit', $salesOrder) }}" class="btn-primary ripple">Edit</a>
    @endif
</div>
@endsection

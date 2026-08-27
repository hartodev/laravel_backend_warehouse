@extends('layouts.supplier')

@section('title', 'Detail PO ' . $purchaseOrder->po_number)
@section('header', 'Detail Purchase Order')

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

<a href="{{ route('supplier.purchase-orders.index') }}" class="btn btn-ghost" style="margin-bottom:16px;">
    &larr; Kembali ke daftar PO
</a>

<div class="section-head">
    <div>
        <span class="eyebrow">No. Purchase Order</span>
        <h2 class="mono" style="font-size:20px;">{{ $purchaseOrder->po_number }}</h2>
    </div>
    <span class="stamp {{ $statusStyles[$purchaseOrder->status] ?? 'stamp-muted' }}"
        style="font-size:12px; padding:5px 14px;">
        {{ ucfirst($purchaseOrder->status) }}
    </span>
</div>

<div class="meta-grid">
    <div class="meta-item">
        <span class="label">Gudang Tujuan</span>
        <div class="value">{{ $purchaseOrder->warehouse->name ?? '-' }}</div>
    </div>
    <div class="meta-item">
        <span class="label">Tanggal Order</span>
        <div class="value">{{ \Illuminate\Support\Carbon::parse($purchaseOrder->order_date)->format('d/m/Y') }}</div>
    </div>
    <div class="meta-item">
        <span class="label">Estimasi Tiba</span>
        <div class="value">
            {{ $purchaseOrder->expected_date ? \Illuminate\Support\Carbon::parse($purchaseOrder->expected_date)->format('d/m/Y') : '-' }}
        </div>
    </div>
</div>

<div class="section-head">
    <div>
        <span class="eyebrow">Rincian</span>
        <h2>Item Pesanan</h2>
    </div>
</div>

<div class="table-wrap">
    <table class="manifest">
        <thead>
            <tr>
                <th>Produk</th>
                <th class="text-right">Qty Dipesan</th>
                <th class="text-right">Qty Diterima</th>
                <th class="text-right">Harga Satuan</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchaseOrder->items as $item)
            <tr>
                <td>
                    {{ $item->product->name ?? '-' }}
                    <span class="text-muted mono" style="font-size:12px;">({{ $item->product->sku ?? '-' }})</span>
                </td>
                <td class="text-right mono">{{ $item->quantity_ordered }}</td>
                <td class="text-right mono">{{ $item->quantity_received }}</td>
                <td class="text-right mono">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="text-right mono" style="font-weight:600;">Rp
                    {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="total-line">
        <span class="label">Total Pesanan</span>
        <span class="value">Rp {{ number_format($purchaseOrder->total_amount, 0, ',', '.') }}</span>
    </div>
</div>

@endsection
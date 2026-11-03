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

@if (session('success'))
<div style="background:var(--success-soft);color:var(--success);padding:10px 14px;border-radius:8px;margin-bottom:14px;font-size:14px;">
    {{ session('success') }}
</div>
@endif
@if (session('error') || $errors->any())
<div style="background:#FBE4E0;color:#B3382C;padding:10px 14px;border-radius:8px;margin-bottom:14px;font-size:14px;">
    {{ session('error') ?? $errors->first() }}
</div>
@endif

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

@php
$canConfirm = $purchaseOrder->status === 'approved' && !$purchaseOrder->supplier_confirmed_at;
$canShip = in_array($purchaseOrder->status, ['approved', 'partial'])
&& $purchaseOrder->supplier_confirmed_at
&& ($purchaseOrder->status === 'partial' || !$purchaseOrder->shipped_at);
@endphp

<div class="section-head">
    <div>
        <span class="eyebrow">Tindakan Supplier</span>
        <h2>Status Pesanan</h2>
    </div>
</div>

<div class="meta-grid">
    <div class="meta-item">
        <span class="label">Disetujui</span>
        <div class="value">{{ $purchaseOrder->approved_at ? $purchaseOrder->approved_at->format('d/m/Y H:i') : 'Belum' }}</div>
    </div>
    <div class="meta-item">
        <span class="label">Dikonfirmasi Supplier</span>
        <div class="value">{{ $purchaseOrder->supplier_confirmed_at ? $purchaseOrder->supplier_confirmed_at->format('d/m/Y H:i') : 'Belum' }}</div>
    </div>
    <div class="meta-item">
        <span class="label">Dikirim</span>
        <div class="value">{{ $purchaseOrder->shipped_at ? $purchaseOrder->shipped_at->format('d/m/Y H:i') : 'Belum' }}</div>
    </div>
</div>

@if ($purchaseOrder->supplier_note)
<p class="text-muted" style="margin:8px 0 14px;font-size:13px;">Catatan Anda: {{ $purchaseOrder->supplier_note }}</p>
@endif

@if ($canConfirm)
<form method="POST" action="{{ route('supplier.purchase-orders.confirm', $purchaseOrder) }}" style="margin-bottom:20px;">
    @csrf
    <input type="text" name="note" maxlength="500" placeholder="Catatan (opsional)"
        style="width:100%;max-width:420px;padding:8px 10px;border:1px solid #ccc;border-radius:8px;margin-bottom:8px;display:block;">
    <button type="submit" class="btn btn-primary">Konfirmasi Pesanan</button>
</form>
@endif

@if ($canShip)
<form method="POST" action="{{ route('supplier.purchase-orders.ship', $purchaseOrder) }}" style="margin-bottom:20px;">
    @csrf
    <label class="text-muted" style="font-size:12px;display:block;">Estimasi tiba (opsional)</label>
    <input type="date" name="expected_date" min="{{ now()->toDateString() }}"
        style="padding:8px 10px;border:1px solid #ccc;border-radius:8px;margin-bottom:8px;display:block;">
    <input type="text" name="note" maxlength="500" placeholder="Catatan pengiriman, mis. nomor resi (opsional)"
        style="width:100%;max-width:420px;padding:8px 10px;border:1px solid #ccc;border-radius:8px;margin-bottom:8px;display:block;">
    <button type="submit" class="btn btn-primary"
        onclick="return confirm('Tandai PO ini sudah dikirim?')">Tandai Sudah Dikirim</button>
</form>
@endif

@if (!$canConfirm && !$canShip && $purchaseOrder->status === 'pending')
<p class="text-muted" style="font-size:13px;margin-bottom:20px;">PO masih menunggu persetujuan Super Admin.</p>
@endif

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
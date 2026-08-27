@extends('layouts.admin')
@section('title', 'Detail Purchase Order')
@section('content')

@php
$badgeMap = [
    'draft' => 'admin-badge-muted', 'pending' => 'admin-badge-warning',
    'approved' => 'admin-badge-info', 'partial' => 'admin-badge-warning',
    'received' => 'admin-badge-success', 'cancelled' => 'admin-badge-danger',
];
@endphp

<div class="admin-page-head">
    <h2>PO {{ $po->po_number }}</h2>
    <span class="admin-badge {{ $badgeMap[$po->status] ?? 'admin-badge-muted' }}">{{ ucfirst($po->status) }}</span>
</div>

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif
@if ($errors->any())
<div class="admin-alert admin-alert-error">
    @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
</div>
@endif

<div class="admin-detail-grid" style="margin-bottom:20px;">
    <div class="admin-detail-item"><p class="admin-label">Supplier</p><p>{{ $po->supplier->name ?? '-' }}</p></div>
    <div class="admin-detail-item"><p class="admin-label">Gudang</p><p>{{ $po->warehouse->name ?? '-' }}</p></div>
    <div class="admin-detail-item"><p class="admin-label">Dibuat Oleh</p><p>{{ $po->createdBy->name ?? '-' }}</p></div>
    <div class="admin-detail-item"><p class="admin-label">Tanggal Order</p><p>{{ \Illuminate\Support\Carbon::parse($po->order_date)->format('d M Y') }}</p></div>
    <div class="admin-detail-item"><p class="admin-label">Estimasi Tiba</p><p>{{ $po->expected_date ? \Illuminate\Support\Carbon::parse($po->expected_date)->format('d M Y') : '-' }}</p></div>
    <div class="admin-detail-item"><p class="admin-label">Termin</p><p>{{ $po->payment_term ?? '-' }}</p></div>
    @if($po->approved_by)
    <div class="admin-detail-item"><p class="admin-label">Disetujui Oleh</p><p>{{ $po->approvedBy->name ?? '-' }}</p></div>
    @endif
    @if($po->status === 'cancelled' && $po->reject_reason)
    <div class="admin-detail-item" style="grid-column:span 2;"><p class="admin-label">Alasan Ditolak</p><p>{{ $po->reject_reason }}</p></div>
    @endif
    @if($po->notes)
    <div class="admin-detail-item" style="grid-column:span 2;"><p class="admin-label">Catatan</p><p>{{ $po->notes }}</p></div>
    @endif
</div>

<div class="admin-card admin-table-wrap" style="margin-bottom:12px;">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Dipesan</th>
                <th>Diterima</th>
                <th>Harga Satuan</th>
                <th>Diskon</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($po->items as $item)
            <tr>
                <td>{{ $item->product->name ?? '-' }} <span class="cell-muted cell-mono">({{ $item->product->sku ?? '-' }})</span></td>
                <td class="cell-mono">{{ $item->quantity_ordered }} {{ $item->product->unit ?? '' }}</td>
                <td class="cell-mono">{{ $item->quantity_received }} {{ $item->product->unit ?? '' }}</td>
                <td class="cell-mono">Rp{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="cell-mono">{{ $item->discount_percent }}%</td>
                <td class="cell-mono">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="admin-detail-grid" style="margin-bottom:20px;max-width:320px;margin-left:auto;">
    <div class="admin-detail-item"><p class="admin-label">Subtotal</p><p>Rp{{ number_format($po->subtotal, 0, ',', '.') }}</p></div>
    <div class="admin-detail-item"><p class="admin-label">Pajak ({{ $po->tax_percent }}%)</p><p>Rp{{ number_format($po->tax_amount, 0, ',', '.') }}</p></div>
    <div class="admin-detail-item"><p class="admin-label">Diskon</p><p>Rp{{ number_format($po->discount_amount, 0, ',', '.') }}</p></div>
    <div class="admin-detail-item"><p class="admin-label"><strong>Total</strong></p><p><strong>Rp{{ number_format($po->total_amount, 0, ',', '.') }}</strong></p></div>
</div>

@if($po->status === 'pending')
<div class="admin-card" style="padding:16px;margin-bottom:20px;">
    <p class="admin-label" style="margin-bottom:10px;">Persetujuan</p>
    <div style="display:flex;gap:10px;">
        <form action="{{ route('admin.purchase-orders.approve', $po) }}" method="POST" onsubmit="return confirm('Setujui PO ini?');">
            @csrf
            <button type="submit" class="btn-primary ripple">Setujui</button>
        </form>
        <button type="button" class="btn-secondary" onclick="document.getElementById('reject-form').classList.toggle('hidden')">Tolak</button>
    </div>
    <form id="reject-form" action="{{ route('admin.purchase-orders.reject', $po) }}" method="POST" class="hidden" style="margin-top:12px;">
        @csrf
        <label class="admin-label">Alasan Penolakan</label>
        <textarea name="reject_reason" required class="admin-textarea"></textarea>
        <button type="submit" class="btn-primary ripple" style="margin-top:8px;">Kirim Penolakan</button>
    </form>
</div>
@endif

@if(in_array($po->status, ['approved', 'partial']))
<div class="admin-card" style="padding:16px;margin-bottom:20px;">
    <p class="admin-label" style="margin-bottom:10px;">Penerimaan Barang</p>
    <form action="{{ route('admin.purchase-orders.receive', $po) }}" method="POST">
        @csrf
        <table class="admin-table">
            <thead>
                <tr><th>Produk</th><th>Sisa</th><th>Qty Diterima</th></tr>
            </thead>
            <tbody>
                @foreach($po->items as $item)
                @php $remaining = $item->quantity_ordered - $item->quantity_received; @endphp
                @if($remaining > 0)
                <tr>
                    <td>{{ $item->product->name ?? '-' }}</td>
                    <td class="cell-mono">{{ $remaining }} {{ $item->product->unit ?? '' }}</td>
                    <td>
                        <input type="hidden" name="items[{{ $loop->index }}][purchase_order_item_id]" value="{{ $item->id }}">
                        <input type="number" min="0" max="{{ $remaining }}" name="items[{{ $loop->index }}][quantity_received]" class="admin-input" style="max-width:120px;">
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn-primary ripple" style="margin-top:12px;">Simpan Penerimaan</button>
    </form>
</div>
@endif

@if(in_array($po->status, ['draft', 'pending']))
<form action="{{ route('admin.purchase-orders.destroy', $po) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus PO ini?');">
    @csrf @method('DELETE')
    <button type="submit" class="btn-secondary text-danger">Hapus PO</button>
</form>
@endif

<div class="admin-action-panel" style="margin-top:20px;">
    <a href="{{ route('admin.purchase-orders.index') }}" class="btn-secondary">← Kembali</a>
</div>

<style>.hidden{display:none;}</style>
@endsection

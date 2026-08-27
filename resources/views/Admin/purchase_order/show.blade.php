@extends('layouts.admin')
@section('title', 'Detail Purchase Order')
@section('content')

<div class="admin-page-head">
    <h2>{{ $po->po_number }}</h2>
    @if($po->status === 'approved')
    <span class="admin-badge admin-badge-success">Disetujui</span>
    @elseif($po->status === 'pending')
    <span class="admin-badge admin-badge-warning">Pending</span>
    @elseif($po->status === 'received')
    <span class="admin-badge admin-badge-success">Diterima</span>
    @elseif($po->status === 'partial')
    <span class="admin-badge admin-badge-info">Sebagian Diterima</span>
    @elseif($po->status === 'cancelled')
    <span class="admin-badge admin-badge-danger">Dibatalkan</span>
    @else
    <span class="admin-badge admin-badge-muted">{{ ucfirst($po->status) }}</span>
    @endif
</div>

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif
@if ($errors->any())
<div class="admin-alert admin-alert-error">
    <div>
        @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
        @endforeach
    </div>
</div>
@endif

<div class="admin-detail-grid" style="margin-bottom:20px;">
    <div class="admin-detail-item">
        <p class="admin-label">Supplier</p>
        <p>{{ $po->supplier->name ?? '-' }} <span class="cell-muted">({{ $po->supplier->code ?? '-' }})</span></p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Gudang Tujuan</p>
        <p>{{ $po->warehouse->name ?? '-' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Tanggal Order</p>
        <p>{{ optional($po->order_date)->format('d M Y') }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Tanggal Diharapkan</p>
        <p>{{ optional($po->expected_date)->format('d M Y') ?? '-' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Dibuat Oleh</p>
        <p>{{ $po->createdBy->name ?? '-' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Disetujui Oleh</p>
        <p>{{ $po->approvedBy->name ?? '-' }}</p>
    </div>
    <div class="admin-detail-item" style="grid-column:span 2;">
        <p class="admin-label">Catatan</p>
        <p>{{ $po->notes ?: '-' }}</p>
    </div>
</div>

<div class="admin-card admin-table-wrap" style="margin-bottom:20px;">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Qty Order</th>
                <th>Qty Diterima</th>
                <th>Harga</th>
                <th>Diskon</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($po->items as $item)
            <tr>
                <td>{{ $item->product->name ?? '-' }} <span class="cell-muted">({{ $item->product->sku ?? '-' }})</span>
                </td>
                <td class="cell-mono">{{ $item->quantity_ordered }} {{ $item->product->unit ?? '' }}</td>
                <td class="cell-mono">{{ $item->quantity_received }}</td>
                <td class="cell-mono">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="cell-mono">{{ $item->discount_percent }}%</td>
                <td class="cell-mono">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="cell-empty">Tidak ada item.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-detail-grid" style="margin-bottom:20px;">
    <div class="admin-detail-item">
        <p class="admin-label">Subtotal</p>
        <p class="cell-mono">Rp {{ number_format($po->subtotal, 0, ',', '.') }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Pajak ({{ $po->tax_percent }}%)</p>
        <p class="cell-mono">Rp {{ number_format($po->tax_amount, 0, ',', '.') }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Diskon</p>
        <p class="cell-mono">Rp {{ number_format($po->discount_amount, 0, ',', '.') }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Total</p>
        <p class="cell-mono" style="font-weight:700;">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</p>
    </div>
</div>

@if($po->status === 'pending')
<div class="admin-card admin-card-pad" style="margin-bottom:20px;">
    <h3 style="margin-top:0;">Review PO</h3>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <form action="{{ route('admin.purchase-orders.approve', $po) }}" method="POST"
            onsubmit="return confirm('Setujui PO ini?');">
            @csrf
            <button type="submit" class="btn-primary ripple">Setujui</button>
        </form>
        <button type="button" class="btn-danger"
            onclick="document.getElementById('reject-form').style.display='flex'">Tolak</button>
    </div>
    <form id="reject-form" action="{{ route('admin.purchase-orders.reject', $po) }}" method="POST"
        style="display:none;gap:10px;margin-top:14px;align-items:flex-end;">
        @csrf
        <div style="flex:1;">
            <label class="admin-label">Alasan Penolakan</label>
            <textarea name="reject_reason" required class="admin-textarea"></textarea>
        </div>
        <button type="submit" class="btn-danger">Tolak</button>
    </form>
</div>
@endif

@if(in_array($po->status, ['approved', 'partial']))
<div class="admin-card admin-card-pad" style="margin-bottom:20px;">
    <h3 style="margin-top:0;">Penerimaan Barang</h3>
    <form action="{{ route('admin.purchase-orders.receive', $po) }}" method="POST">
        @csrf
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Sisa</th>
                        <th>Qty Diterima</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($po->items as $item)
                    @if($item->quantity_received < $item->quantity_ordered)
                        <tr>
                            <td>{{ $item->product->name ?? '-' }}</td>
                            <td class="cell-mono">{{ $item->quantity_ordered - $item->quantity_received }}</td>
                            <td>
                                <input type="hidden" name="items[{{ $loop->index }}][purchase_order_item_id]"
                                    value="{{ $item->id }}">
                                <input type="number" name="items[{{ $loop->index }}][quantity_received]" min="0"
                                    max="{{ $item->quantity_ordered - $item->quantity_received }}" class="admin-input"
                                    style="max-width:120px;">
                            </td>
                        </tr>
                        @endif
                        @endforeach
                </tbody>
            </table>
        </div>
        <div class="admin-form-actions" style="justify-content:flex-end;">
            <button type="submit" class="btn-primary ripple">Catat Penerimaan</button>
        </div>
    </form>
</div>
@endif

<div class="admin-action-panel">
    <a href="{{ route('admin.purchase-orders.index') }}" class="btn-secondary">← Kembali</a>
</div>
@endsection
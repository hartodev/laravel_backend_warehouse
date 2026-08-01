@extends('layouts.admin')

@section('title', $po->po_number)

@section('content')
    <div class="admin-page-head">
        <h2>{{ $po->po_number }}</h2>
        <a href="{{ route('admin.purchase-orders.index') }}" class="btn-ghost">← Kembali</a>
    </div>

    @if (session('success'))
        <div class="admin-alert admin-alert-success"><i data-lucide="check-circle"></i> {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="admin-alert admin-alert-error"><i data-lucide="alert-circle"></i> {{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="admin-alert admin-alert-error">
            <ul style="margin:0;padding-left:18px;">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="admin-card admin-card-pad" style="margin-bottom:20px;">
        <div class="admin-detail-grid">
            <div class="admin-detail-item">
                <div class="label">Status</div>
                <div class="value"><span class="admin-badge admin-badge-{{ $po->status }}">{{ ucfirst($po->status) }}</span></div>
            </div>
            <div class="admin-detail-item"><div class="label">Supplier</div><div class="value">{{ $po->supplier->name }}</div></div>
            <div class="admin-detail-item"><div class="label">Gudang</div><div class="value">{{ $po->warehouse->name }} ({{ $po->warehouse->code }})</div></div>
            <div class="admin-detail-item"><div class="label">Tanggal Order</div><div class="value">{{ \Carbon\Carbon::parse($po->order_date)->format('d M Y') }}</div></div>
            <div class="admin-detail-item"><div class="label">Estimasi Tiba</div><div class="value">{{ $po->expected_date ? \Carbon\Carbon::parse($po->expected_date)->format('d M Y') : '-' }}</div></div>
            <div class="admin-detail-item"><div class="label">Termin</div><div class="value">{{ $po->payment_term ?? '-' }}</div></div>
            <div class="admin-detail-item"><div class="label">Subtotal</div><div class="value">Rp {{ number_format($po->subtotal, 0, ',', '.') }}</div></div>
            <div class="admin-detail-item"><div class="label">Pajak</div><div class="value">Rp {{ number_format($po->tax_amount, 0, ',', '.') }} ({{ $po->tax_percent }}%)</div></div>
            <div class="admin-detail-item"><div class="label">Total</div><div class="value">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</div></div>
        </div>
        @if ($po->notes)
            <p class="text-muted" style="margin-top:16px;"><strong style="color:var(--text-primary);">Catatan:</strong> {{ $po->notes }}</p>
        @endif
        @if ($po->status === 'cancelled' && $po->reject_reason)
            <div class="admin-alert admin-alert-error" style="margin-top:16px;"><strong>Alasan ditolak:</strong>&nbsp;{{ $po->reject_reason }}</div>
        @endif
        @if ($po->status === 'pending')
            <div class="admin-alert admin-alert-warning" style="margin-top:16px;">Menunggu persetujuan Super Admin.</div>
        @endif
    </div>

    <div class="admin-card admin-table-wrap" style="margin-bottom:20px;">
        <table class="admin-table">
            <thead><tr><th>Produk</th><th>Qty Order</th><th>Qty Diterima</th><th>Harga Satuan</th><th>Subtotal</th></tr></thead>
            <tbody>
                @foreach ($po->items as $item)
                    <tr>
                        <td>{{ $item->product->name }} <span class="cell-muted">({{ $item->product->sku }})</span></td>
                        <td>{{ $item->quantity_ordered }} {{ $item->product->unit }}</td>
                        <td>{{ $item->quantity_received }} {{ $item->product->unit }}</td>
                        <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if (in_array($po->status, ['approved', 'partial']))
        <form method="POST" action="{{ route('admin.purchase-orders.receive', $po) }}" class="admin-action-panel">
            @csrf
            <h4>Terima Barang</h4>
            <div class="admin-table-wrap" style="border:1px solid var(--border);border-radius:var(--r-sm);margin-bottom:14px;">
                <table class="admin-table">
                    <thead><tr><th>Produk</th><th>Sisa Belum Diterima</th><th style="width:160px">Qty Diterima Sekarang</th></tr></thead>
                    <tbody>
                        @foreach ($po->items as $item)
                            @php $remaining = $item->quantity_ordered - $item->quantity_received; @endphp
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ $remaining }} {{ $item->product->unit }}</td>
                                <td>
                                    <input type="hidden" name="items[{{ $loop->index }}][purchase_order_item_id]" value="{{ $item->id }}">
                                    <input type="number" name="items[{{ $loop->index }}][quantity_received]" min="0" max="{{ $remaining }}"
                                           value="{{ $remaining }}" class="admin-input" {{ $remaining <= 0 ? 'disabled' : '' }}>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <button type="submit" class="btn-primary ripple">Simpan Penerimaan</button>
        </form>
    @endif

    @if ($po->status === 'received')
        <div class="admin-action-panel"><p class="text-success"><i data-lucide="check-circle"></i> Semua barang sudah diterima.</p></div>
    @endif
@endsection

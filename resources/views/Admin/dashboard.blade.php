@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')

<div class="admin-page-head">
    <h2>Dashboard</h2>
</div>

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif

<div class="admin-form-grid" style="grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:20px;">
    <div class="admin-card admin-card-pad">
        <p class="admin-label">Produk Aktif</p>
        <p style="font-size:26px;font-weight:700;margin:4px 0 0;">{{ $stats['total_products'] }}</p>
    </div>
    <div class="admin-card admin-card-pad">
        <p class="admin-label">Gudang Aktif</p>
        <p style="font-size:26px;font-weight:700;margin:4px 0 0;">{{ $stats['total_warehouses'] }}</p>
    </div>
    <div class="admin-card admin-card-pad">
        <p class="admin-label">Supplier Aktif</p>
        <p style="font-size:26px;font-weight:700;margin:4px 0 0;">{{ $stats['total_suppliers'] }}</p>
    </div>
</div>

<div class="admin-form-grid" style="grid-template-columns:repeat(2,1fr);gap:16px;margin-bottom:20px;">
    <div class="admin-card admin-card-pad">
        <p class="admin-label">Total PO Bulan Ini</p>
        <p style="font-size:20px;font-weight:700;margin:4px 0 0;">Rp
            {{ number_format($monthlyFinance['total_po'] ?? 0, 0, ',', '.') }}</p>
    </div>
    <div class="admin-card admin-card-pad">
        <p class="admin-label">Total SO Bulan Ini</p>
        <p style="font-size:20px;font-weight:700;margin:4px 0 0;">Rp
            {{ number_format($monthlyFinance['total_so'] ?? 0, 0, ',', '.') }}</p>
    </div>
</div>

<div class="admin-form-grid" style="grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
    <div class="admin-card admin-card-pad">
        <h3 style="margin-top:0;">Stok Menipis</h3>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Gudang</th>
                        <th>Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lowStocks as $stock)
                    <tr>
                        <td>{{ $stock->product->name ?? '-' }} <span
                                class="cell-muted">({{ $stock->product->sku ?? '-' }})</span></td>
                        <td class="cell-muted">{{ $stock->warehouse->name ?? '-' }}</td>
                        <td class="cell-mono">{{ $stock->quantity }} / {{ $stock->product->min_stock ?? 0 }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="cell-empty">Tidak ada stok menipis.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-card admin-card-pad">
        <h3 style="margin-top:0;">Nilai Stok per Gudang</h3>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Gudang</th>
                        <th>Nilai Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stockValueByWarehouse as $wh)
                    <tr>
                        <td>{{ $wh->name }}</td>
                        <td class="cell-mono">Rp {{ number_format($wh->stock_value ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="cell-empty">Belum ada data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="admin-form-grid" style="grid-template-columns:repeat(2,1fr);gap:16px;margin-bottom:20px;">
    <div class="admin-card admin-card-pad">
        <h3 style="margin-top:0;">PO Pending</h3>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>No. PO</th>
                        <th>Supplier</th>
                        <th>Gudang</th>
                        <th class="cell-actions">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pendingPOs as $po)
                    <tr>
                        <td class="cell-mono">{{ $po->po_number }}</td>
                        <td>{{ $po->supplier->name ?? '-' }}</td>
                        <td class="cell-muted">{{ $po->warehouse->name ?? '-' }}</td>
                        <td class="cell-actions"><a href="{{ route('admin.purchase-orders.show', $po) }}"
                                class="admin-link">Detail</a></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="cell-empty">Tidak ada PO pending.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-card admin-card-pad">
        <h3 style="margin-top:0;">SO Pending</h3>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>No. SO</th>
                        <th>Gudang</th>
                        <th class="cell-actions">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pendingSOs as $so)
                    <tr>
                        <td class="cell-mono">{{ $so->so_number }}</td>
                        <td class="cell-muted">{{ $so->warehouse->name ?? '-' }}</td>
                        <td class="cell-actions"><a href="{{ route('admin.sales-orders.show', $so) }}"
                                class="admin-link">Detail</a></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="cell-empty">Tidak ada SO pending.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="admin-form-grid" style="grid-template-columns:repeat(2,1fr);gap:16px;">
    <div class="admin-card admin-card-pad">
        <h3 style="margin-top:0;">Transfer Aktif</h3>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>No. Transfer</th>
                        <th>Dari → Ke</th>
                        <th>Status</th>
                        <th class="cell-actions">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activeTransfers as $t)
                    <tr>
                        <td class="cell-mono">{{ $t->transfer_number }}</td>
                        <td class="cell-muted">{{ $t->fromWarehouse->name ?? '-' }} → {{ $t->toWarehouse->name ?? '-' }}
                        </td>
                        <td><span class="admin-badge admin-badge-info">{{ $t->status }}</span></td>
                        <td class="cell-actions"><a href="{{ route('admin.stock-transfers.show', $t) }}"
                                class="admin-link">Detail</a></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="cell-empty">Tidak ada transfer aktif.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-card admin-card-pad">
        <h3 style="margin-top:0;">Opname Menunggu Persetujuan</h3>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>No. Opname</th>
                        <th>Gudang</th>
                        <th class="cell-actions">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pendingOpnames as $op)
                    <tr>
                        <td class="cell-mono">{{ $op->opname_number }}</td>
                        <td class="cell-muted">{{ $op->warehouse->name ?? '-' }}</td>
                        <td class="cell-actions"><a href="{{ route('admin.stock-opnames.show', $op) }}"
                                class="admin-link">Detail</a></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="cell-empty">Tidak ada opname pending.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
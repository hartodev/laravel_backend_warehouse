@extends('layouts.admin')
@section('title', 'Purchase Order')
@section('content')

<div class="admin-page-head">
    <h2>Purchase Order</h2>
    <a href="{{ route('admin.purchase-orders.create') }}" class="btn-primary ripple"><i class="lucide-plus"></i> Buat
        PO</a>
</div>

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif

<form method="GET" class="admin-filter-bar">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no. PO..." class="admin-input"
        style="max-width:220px;">
    <select name="supplier_id" class="admin-select" style="max-width:180px;">
        <option value="">Semua Supplier</option>
        @foreach ($suppliers as $supplier)
        <option value="{{ $supplier->id }}" @selected(request('supplier_id')==$supplier->id)>{{ $supplier->name }}
        </option>
        @endforeach
    </select>
    <select name="status" class="admin-select" style="max-width:180px;">
        <option value="">Semua Status</option>
        <option value="draft" @selected(request('status')==='draft' )>Draft</option>
        <option value="pending" @selected(request('status')==='pending' )>Pending</option>
        <option value="approved" @selected(request('status')==='approved' )>Disetujui</option>
        <option value="partial" @selected(request('status')==='partial' )>Sebagian Diterima</option>
        <option value="received" @selected(request('status')==='received' )>Diterima</option>
        <option value="cancelled" @selected(request('status')==='cancelled' )>Dibatalkan</option>
    </select>
    <button class="btn-outline">Filter</button>
</form>

<div class="admin-card admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>No. PO</th>
                <th>Supplier</th>
                <th>Gudang</th>
                <th>Total</th>
                <th>Dibuat Oleh</th>
                <th>Status</th>
                <th class="cell-actions">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pos as $po)
            <tr>
                <td class="cell-mono">{{ $po->po_number }}</td>
                <td>{{ $po->supplier->name ?? '-' }}</td>
                <td class="cell-muted">{{ $po->warehouse->name ?? '-' }}</td>
                <td class="cell-mono">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</td>
                <td class="cell-muted">{{ $po->createdBy->name ?? '-' }}</td>
                <td>
                    @if($po->status === 'approved')
                    <span class="admin-badge admin-badge-success">Disetujui</span>
                    @elseif($po->status === 'pending')
                    <span class="admin-badge admin-badge-warning">Pending</span>
                    @elseif($po->status === 'received')
                    <span class="admin-badge admin-badge-success">Diterima</span>
                    @elseif($po->status === 'partial')
                    <span class="admin-badge admin-badge-info">Sebagian</span>
                    @elseif($po->status === 'cancelled')
                    <span class="admin-badge admin-badge-danger">Dibatalkan</span>
                    @else
                    <span class="admin-badge admin-badge-muted">{{ ucfirst($po->status) }}</span>
                    @endif
                </td>
                <td class="cell-actions">
                    <a href="{{ route('admin.purchase-orders.show', $po) }}" class="admin-link">Detail</a>
                    @if(in_array($po->status, ['draft','pending']))
                    <form action="{{ route('admin.purchase-orders.destroy', $po) }}" method="POST"
                        style="display:inline;" onsubmit="return confirm('Hapus PO ini?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="admin-link text-danger"
                            style="background:none;border:none;cursor:pointer;">Hapus</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="cell-empty">Belum ada purchase order.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-pagination">{{ $pos->appends(request()->query())->links() }}</div>
@endsection
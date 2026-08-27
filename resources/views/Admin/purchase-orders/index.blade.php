@extends('layouts.admin')
@section('title', 'Purchase Order')
@section('content')

<div class="admin-page-head">
    <h2>Purchase Order</h2>
    <a href="{{ route('admin.purchase-orders.create') }}" class="btn-primary ripple"><i class="lucide-plus"></i> Buat PO</a>
</div>

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif

<form method="GET" class="admin-filter-bar">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no. PO..." class="admin-input" style="max-width:220px;">
    <select name="supplier_id" class="admin-select" style="max-width:200px;">
        <option value="">Semua Supplier</option>
        @foreach($suppliers as $supplier)
        <option value="{{ $supplier->id }}" @selected(request('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
        @endforeach
    </select>
    <select name="status" class="admin-select" style="max-width:180px;">
        <option value="">Semua Status</option>
        @foreach(['draft','pending','approved','partial','received','cancelled'] as $status)
        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
        @endforeach
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
                <th>Tgl. Order</th>
                <th>Total</th>
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
                <td class="cell-muted">{{ \Illuminate\Support\Carbon::parse($po->order_date)->format('d M Y') }}</td>
                <td class="cell-mono">Rp{{ number_format($po->total_amount, 0, ',', '.') }}</td>
                <td>
                    @php
                    $badgeMap = [
                        'draft' => 'admin-badge-muted', 'pending' => 'admin-badge-warning',
                        'approved' => 'admin-badge-info', 'partial' => 'admin-badge-warning',
                        'received' => 'admin-badge-success', 'cancelled' => 'admin-badge-danger',
                    ];
                    @endphp
                    <span class="admin-badge {{ $badgeMap[$po->status] ?? 'admin-badge-muted' }}">{{ ucfirst($po->status) }}</span>
                </td>
                <td class="cell-actions">
                    <a href="{{ route('admin.purchase-orders.show', $po) }}" class="admin-link">Detail</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="cell-empty">Belum ada purchase order.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-pagination">{{ $pos->appends(request()->query())->links() }}</div>
@endsection

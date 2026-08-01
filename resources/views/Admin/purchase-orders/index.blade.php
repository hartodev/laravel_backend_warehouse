@extends('layouts.admin')

@section('title', 'Purchase Order')

@section('content')
    <div class="admin-page-head">
        <h2>Purchase Order</h2>
        <a href="{{ route('admin.purchase-orders.create') }}" class="btn-primary ripple">
            <i data-lucide="plus"></i> Buat PO Baru
        </a>
    </div>

    @if (session('success'))
        <div class="admin-alert admin-alert-success"><i data-lucide="check-circle"></i> {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="admin-alert admin-alert-error"><i data-lucide="alert-circle"></i> {{ session('error') }}</div>
    @endif

    <form method="GET" class="admin-filter-bar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no. PO..." class="admin-input" style="max-width:220px;">
        <select name="supplier_id" onchange="this.form.submit()" class="admin-select" style="max-width:200px;">
            <option value="">Semua Supplier</option>
            @foreach ($suppliers as $sup)
                <option value="{{ $sup->id }}" @selected((string) request('supplier_id') === (string) $sup->id)>{{ $sup->name }}</option>
            @endforeach
        </select>
        <select name="status" onchange="this.form.submit()" class="admin-select" style="max-width:180px;">
            <option value="">Semua Status</option>
            @foreach (['pending','approved','partial','received','cancelled'] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
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
                        <td>{{ $po->warehouse->name ?? '-' }}</td>
                        <td>Rp {{ number_format($po->total_amount, 0, ',', '.') }}</td>
                        <td><span class="admin-badge admin-badge-{{ $po->status }}">{{ ucfirst($po->status) }}</span></td>
                        <td class="cell-actions">
                            <a href="{{ route('admin.purchase-orders.show', $po) }}" class="admin-link">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="cell-empty">Belum ada purchase order.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="admin-pagination">{{ $pos->links() }}</div>
@endsection

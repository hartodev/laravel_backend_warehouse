@extends('layouts.admin')
@section('title', 'Sales Order')
@section('content')

<div class="admin-page-head">
    <h2>Sales Order</h2>
    <a href="{{ route('admin.sales-orders.create') }}" class="btn-primary ripple"><i class="lucide-plus"></i> Buat SO</a>
</div>

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif

<form method="GET" class="admin-filter-bar">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no. SO / customer..." class="admin-input" style="max-width:240px;">
    <select name="warehouse_id" class="admin-select" style="max-width:180px;">
        <option value="">Semua Gudang</option>
        @foreach($warehouses as $warehouse)
        <option value="{{ $warehouse->id }}" @selected(request('warehouse_id') == $warehouse->id)>{{ $warehouse->name }}</option>
        @endforeach
    </select>
    <select name="status" class="admin-select" style="max-width:160px;">
        <option value="">Semua Status</option>
        @foreach(['draft','confirmed','processed','completed','cancelled'] as $status)
        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
        @endforeach
    </select>
    <input type="date" name="date_from" value="{{ request('date_from') }}" class="admin-input" style="max-width:150px;">
    <input type="date" name="date_to" value="{{ request('date_to') }}" class="admin-input" style="max-width:150px;">
    <button class="btn-outline">Filter</button>
</form>

<div class="admin-card admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>No. SO</th>
                <th>Customer</th>
                <th>Gudang</th>
                <th>Tgl. Order</th>
                <th>Total</th>
                <th>Status</th>
                <th class="cell-actions">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($salesOrders as $so)
            <tr>
                <td class="cell-mono">{{ $so->so_number }}</td>
                <td>{{ $so->customer_name }}</td>
                <td class="cell-muted">{{ $so->warehouse->name ?? '-' }}</td>
                <td class="cell-muted">{{ \Illuminate\Support\Carbon::parse($so->order_date)->format('d M Y') }}</td>
                <td class="cell-mono">Rp{{ number_format($so->total_amount, 0, ',', '.') }}</td>
                <td>
                    @php
                    $badgeMap = ['draft'=>'admin-badge-muted','confirmed'=>'admin-badge-info','processed'=>'admin-badge-warning','completed'=>'admin-badge-success','cancelled'=>'admin-badge-danger'];
                    @endphp
                    <span class="admin-badge {{ $badgeMap[$so->status] ?? 'admin-badge-muted' }}">{{ ucfirst($so->status) }}</span>
                </td>
                <td class="cell-actions">
                    <a href="{{ route('admin.sales-orders.show', $so) }}" class="admin-link">Detail</a>
                    @if(in_array($so->status, ['draft','confirmed']))
                    <a href="{{ route('admin.sales-orders.edit', $so) }}" class="admin-link">Edit</a>
                    @endif
                    @if($so->status === 'draft')
                    <form action="{{ route('admin.sales-orders.destroy', $so) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus SO ini?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="admin-link text-danger" style="background:none;border:none;cursor:pointer;">Hapus</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="cell-empty">Belum ada sales order.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-pagination">{{ $salesOrders->appends(request()->query())->links() }}</div>
@endsection

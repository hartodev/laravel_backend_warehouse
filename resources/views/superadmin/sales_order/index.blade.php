{{-- sales_orders/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Sales Order')
@section('breadcrumb')<span class="text-gray-700 font-medium">Sales Order</span>@endsection

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div><h1 class="text-xl font-bold text-gray-900">Sales Order</h1><p class="text-sm text-gray-500">{{ $sos->total() }} SO</p></div>
    <a href="{{ route('sales-orders.create') }}" class="btn-primary btn">+ Buat SO</a>
</div>

<div class="card mb-5">
    <form method="GET" class="card-body flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48"><label class="form-label">Cari</label><input type="text" name="search" value="{{ request('search') }}" placeholder="Nama customer, no. SO..." class="form-input"></div>
        <div class="w-36"><label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua</option>
                @foreach(['draft','confirmed','completed','cancelled'] as $s)
                <option value="{{ $s }}" {{ request('status')===$s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-48"><label class="form-label">Gudang</label>
            <select name="warehouse_id" class="form-select">
                <option value="">Semua</option>
                @foreach($warehouses as $w)<option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>@endforeach
            </select>
        </div>
        <div class="w-36"><label class="form-label">Dari</label><input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input"></div>
        <div class="w-36"><label class="form-label">Sampai</label><input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input"></div>
        <button type="submit" class="btn-primary btn">Filter</button>
        <a href="{{ route('sales-orders.index') }}" class="btn-secondary btn">Reset</a>
    </form>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>No. SO</th><th>Customer</th><th>Gudang</th><th>Tgl. Order</th><th class="text-right">Total</th><th>Pembayaran</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($sos as $so)
            <tr>
                <td><span class="font-mono text-sm font-medium text-primary-700">{{ $so->so_number }}</span></td>
                <td><p class="font-medium">{{ $so->customer_name }}</p><p class="text-xs text-gray-400 truncate max-w-40">{{ $so->customer_address }}</p></td>
                <td>{{ $so->warehouse->name ?? '—' }}</td>
                <td>{{ \Carbon\Carbon::parse($so->order_date)->isoFormat('D MMM Y') }}</td>
                <td class="text-right font-semibold">Rp {{ number_format($so->total_amount) }}</td>
                <td>{{ $so->payment_method ? ucfirst($so->payment_method) : '—' }}</td>
                <td><x-status-badge :status="$so->status" /></td>
                <td class="text-right">
                    <div class="flex items-center justify-end gap-1">
                        <a href="{{ route('sales-orders.show', $so) }}" class="btn btn-secondary btn-sm">Detail</a>
                        @if($so->status === 'draft')
                        <a href="{{ route('sales-orders.edit', $so) }}" class="btn btn-secondary btn-sm">Edit</a>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center py-12 text-gray-400">Belum ada Sales Order</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $sos->links() }}</div>
@endsection

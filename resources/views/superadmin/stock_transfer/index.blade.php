{{-- ============================================================
     stock_transfers/index.blade.php
============================================================ --}}
@extends('layouts.app')
@section('title', 'Transfer Stok')
@section('breadcrumb')<span class="text-gray-700 font-medium">Transfer Stok</span>@endsection

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Transfer Stok</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $transfers->total() }} transfer</p>
    </div>
    <a href="{{ route('stock-transfers.create') }}" class="btn-primary btn">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Buat Transfer
    </a>
</div>

<div class="card mb-5">
    <form method="GET" class="card-body flex flex-wrap gap-3 items-end">
        <div class="w-40">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua</option>
                @foreach(['pending','approved','rejected','in_transit','completed'] as $s)
                <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-48">
            <label class="form-label">Gudang</label>
            <select name="warehouse_id" class="form-select">
                <option value="">Semua</option>
                @foreach($warehouses as $w)
                <option value="{{ $w->id }}" {{ request('warehouse_id')==$w->id?'selected':'' }}>{{ $w->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary btn">Filter</button>
        <a href="{{ route('stock-transfers.index') }}" class="btn-secondary btn">Reset</a>
    </form>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>No. Transfer</th>
                <th>Dari Gudang</th>
                <th>Ke Gudang</th>
                <th>Tgl. Transfer</th>
                <th>Diajukan Oleh</th>
                <th>Status</th>
                <th class="text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($transfers as $t)
            <tr>
                <td><span class="font-mono text-sm font-medium text-primary-700">{{ $t->transfer_number }}</span></td>
                <td>{{ $t->fromWarehouse->name ?? '—' }}</td>
                <td>{{ $t->toWarehouse->name ?? '—' }}</td>
                <td>{{ \Carbon\Carbon::parse($t->transfer_date)->isoFormat('D MMM Y') }}</td>
                <td>{{ $t->requestedBy->name ?? '—' }}</td>
                <td><x-status-badge :status="$t->status" /></td>
                <td class="text-right">
                    <div class="flex items-center justify-end gap-1">
                        <a href="{{ route('stock-transfers.show', $t) }}" class="btn btn-secondary btn-sm">Detail</a>
                        @if($t->status === 'pending')
                            <form method="POST" action="{{ route('stock-transfers.approve', $t) }}" class="inline">
                                @csrf <button class="btn btn-success btn-sm">Setujui</button>
                            </form>
                        @elseif($t->status === 'approved')
                            <form method="POST" action="{{ route('stock-transfers.send', $t) }}" class="inline">
                                @csrf <button class="btn btn-primary btn-sm">Kirim</button>
                            </form>
                        @elseif($t->status === 'in_transit')
                            <form method="POST" action="{{ route('stock-transfers.receive', $t) }}" class="inline">
                                @csrf <button class="btn btn-success btn-sm">Terima</button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center py-12 text-gray-400">Belum ada data transfer stok</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $transfers->links() }}</div>
@endsection

@extends('layouts.admin')
@section('title', 'Stock Opname')
@section('content')

<div class="admin-page-head">
    <h2>Stock Opname</h2>
    <a href="{{ route('admin.stock-opnames.create') }}" class="btn-primary ripple"><i class="lucide-plus"></i> Buat
        Opname</a>
</div>

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif

<form method="GET" class="admin-filter-bar">
    <select name="warehouse_id" class="admin-select" style="max-width:180px;">
        <option value="">Semua Gudang</option>
        @foreach($warehouses as $warehouse)
        <option value="{{ $warehouse->id }}" @selected(request('warehouse_id')==$warehouse->id)>{{ $warehouse->name }}
        </option>
        @endforeach
    </select>
    <select name="status" class="admin-select" style="max-width:180px;">
        <option value="">Semua Status</option>
        @foreach(['draft'=>'Draft','in_progress'=>'Sedang Berjalan','pending_approval'=>'Menunggu
        Persetujuan','approved'=>'Disetujui'] as $val => $label)
        <option value="{{ $val }}" @selected(request('status')===$val)>{{ $label }}</option>
        @endforeach
    </select>
    <button class="btn-outline">Filter</button>
</form>

<div class="admin-card admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>No. Opname</th>
                <th>Gudang</th>
                <th>Tgl. Opname</th>
                <th>Scope</th>
                <th>Dibuat Oleh</th>
                <th>Status</th>
                <th class="cell-actions">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($opnames as $opname)
            <tr>
                <td class="cell-mono">{{ $opname->opname_number }}</td>
                <td class="cell-muted">{{ $opname->warehouse->name ?? '-' }}</td>
                <td class="cell-muted">{{ \Illuminate\Support\Carbon::parse($opname->opname_date)->format('d M Y') }}
                </td>
                <td class="cell-muted">{{ ucfirst($opname->scope) }}</td>
                <td class="cell-muted">{{ $opname->createdBy->name ?? '-' }}</td>
                <td>
                    @php
                    $badgeMap =
                    ['draft'=>'admin-badge-muted','in_progress'=>'admin-badge-warning','pending_approval'=>'admin-badge-info','approved'=>'admin-badge-success'];
                    $labelMap = ['draft'=>'Draft','in_progress'=>'Sedang Berjalan','pending_approval'=>'Menunggu
                    Persetujuan','approved'=>'Disetujui'];
                    @endphp
                    <span
                        class="admin-badge {{ $badgeMap[$opname->status] ?? 'admin-badge-muted' }}">{{ $labelMap[$opname->status] ?? ucfirst($opname->status) }}</span>
                </td>
                <td class="cell-actions">
                    <a href="{{ route('admin.stock-opnames.show', $opname) }}" class="admin-link">Detail</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="cell-empty">Belum ada stock opname.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-pagination">{{ $opnames->appends(request()->query())->links() }}</div>
@endsection
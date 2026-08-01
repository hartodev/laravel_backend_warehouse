@extends('layouts.admin')

@section('title', 'Stock Opname')

@section('content')
    <div class="admin-page-head">
        <h2>Stock Opname</h2>
        <a href="{{ route('admin.stock-opnames.create') }}" class="btn-primary ripple">
            <i data-lucide="plus"></i> Opname Baru
        </a>
    </div>

    @if (session('success'))
        <div class="admin-alert admin-alert-success"><i data-lucide="check-circle"></i> {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="admin-alert admin-alert-error"><i data-lucide="alert-circle"></i> {{ session('error') }}</div>
    @endif

    <form method="GET" class="admin-filter-bar">
        <select name="status" onchange="this.form.submit()" class="admin-select" style="max-width:200px;">
            <option value="">Semua Status</option>
            @foreach (['draft','in_progress','pending_approval','approved'] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucwords(str_replace('_', ' ', $s)) }}</option>
            @endforeach
        </select>
        <select name="warehouse_id" onchange="this.form.submit()" class="admin-select" style="max-width:200px;">
            <option value="">Semua Gudang</option>
            @foreach ($warehouses as $wh)
                <option value="{{ $wh->id }}" @selected((string) request('warehouse_id') === (string) $wh->id)>{{ $wh->name }}</option>
            @endforeach
        </select>
    </form>

    <div class="admin-card admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>No. Opname</th>
                    <th>Gudang</th>
                    <th>Tgl Opname</th>
                    <th>Scope</th>
                    <th>Status</th>
                    <th>Dibuat Oleh</th>
                    <th class="cell-actions">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($opnames as $o)
                    <tr>
                        <td class="cell-mono">{{ $o->opname_number }}</td>
                        <td>{{ $o->warehouse->name ?? '-' }}</td>
                        <td class="cell-muted">{{ \Carbon\Carbon::parse($o->opname_date)->format('d M Y') }}</td>
                        <td class="cell-muted">{{ ucfirst($o->scope) }}</td>
                        <td><span class="admin-badge admin-badge-{{ $o->status }}">{{ ucwords(str_replace('_', ' ', $o->status)) }}</span></td>
                        <td>{{ $o->createdBy->name ?? '-' }}</td>
                        <td class="cell-actions">
                            <a href="{{ route('admin.stock-opnames.show', $o) }}" class="admin-link">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="cell-empty">Belum ada data opname.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="admin-pagination">{{ $opnames->links() }}</div>
@endsection

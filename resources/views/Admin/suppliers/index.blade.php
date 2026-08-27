@extends('layouts.admin')
@section('title', 'Supplier')
@section('content')

<div class="admin-page-head">
    <h2>Supplier</h2>
    <a href="{{ route('admin.suppliers.create') }}" class="btn-primary ripple"><i class="lucide-plus"></i> Tambah Supplier</a>
</div>

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif

<form method="GET" class="admin-filter-bar">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / kode / email..." class="admin-input" style="max-width:280px;">
    <select name="is_active" class="admin-select" style="max-width:160px;">
        <option value="">Semua Status</option>
        <option value="1" @selected(request('is_active')==='1')>Aktif</option>
        <option value="0" @selected(request('is_active')==='0')>Nonaktif</option>
    </select>
    <button class="btn-outline">Filter</button>
</form>

<div class="admin-card admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Kontak</th>
                <th>PO Terkait</th>
                <th>Status</th>
                <th class="cell-actions">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($suppliers as $supplier)
            <tr>
                <td class="cell-mono">{{ $supplier->code }}</td>
                <td>{{ $supplier->name }}</td>
                <td class="cell-muted">{{ $supplier->contact_person ?? '-' }}<br>{{ $supplier->phone ?? '' }}</td>
                <td>{{ $supplier->purchase_orders_count }}</td>
                <td>
                    @if($supplier->is_active)
                    <span class="admin-badge admin-badge-success">Aktif</span>
                    @else
                    <span class="admin-badge admin-badge-muted">Nonaktif</span>
                    @endif
                </td>
                <td class="cell-actions">
                    <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="admin-link">Edit</a>
                    <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus supplier ini?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="admin-link text-danger" style="background:none;border:none;cursor:pointer;">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="cell-empty">Belum ada supplier.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-pagination">{{ $suppliers->appends(request()->query())->links() }}</div>
@endsection

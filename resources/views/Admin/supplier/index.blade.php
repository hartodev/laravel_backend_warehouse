@extends('layouts.admin')

@section('title', 'Supplier')

@section('content')
<div class="admin-page-head">
    <h2>Supplier</h2>
    <a href="{{ route('admin.suppliers.create') }}" class="btn-primary ripple">
        <i data-lucide="plus"></i> Tambah Supplier
    </a>
</div>

@if (session('success'))
<div class="admin-alert admin-alert-success"><i data-lucide="check-circle"></i> {{ session('success') }}</div>
@endif
@if (session('error'))
<div class="admin-alert admin-alert-error"><i data-lucide="alert-circle"></i> {{ session('error') }}</div>
@endif

<form method="GET" class="admin-filter-bar">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, kode, atau email..."
        class="admin-input" style="max-width:280px;">
    <select name="is_active" class="admin-select" style="max-width:160px;">
        <option value="">Semua Status</option>
        <option value="1" @selected(request('is_active')==='1' )>Aktif</option>
        <option value="0" @selected(request('is_active')==='0' )>Nonaktif</option>
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
                <th>Total PO</th>
                <th>Status</th>
                <th class="cell-actions">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($suppliers as $supplier)
            <tr>
                <td class="cell-mono">{{ $supplier->code }}</td>
                <td>
                    <div style="font-weight:600;">{{ $supplier->name }}</div>
                    <div class="cell-muted">{{ $supplier->email }}</div>
                </td>
                <td>
                    {{ $supplier->contact_person ?? '-' }}
                    <div class="cell-muted">{{ $supplier->phone }}</div>
                </td>
                <td>{{ $supplier->purchase_orders_count }}</td>
                <td>
                    <span class="admin-badge {{ $supplier->is_active ? 'admin-badge-success' : 'admin-badge-muted' }}">
                        {{ $supplier->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td class="cell-actions">
                    <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="admin-link">Edit</a>
                    <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST"
                        style="display:inline" onsubmit="return confirm('Hapus supplier {{ $supplier->name }}?')">
                        @csrf @method('DELETE')
                        <button class="admin-link admin-link-danger"
                            style="background:none;border:none;cursor:pointer;">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="cell-empty">Belum ada supplier.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-pagination">{{ $suppliers->links() }}</div>
@endsection
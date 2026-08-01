@extends('layouts.admin')

@section('title', 'Kategori')

@section('content')
<div class="admin-page-head">
    <h2>Kategori Produk</h2>
    <a href="{{ route('admin.categories.create') }}" class="btn-primary ripple">
        <i data-lucide="plus"></i> Tambah Kategori
    </a>
</div>

@if (session('success'))
<div class="admin-alert admin-alert-success"><i data-lucide="check-circle"></i> {{ session('success') }}</div>
@endif
@if (session('error'))
<div class="admin-alert admin-alert-error"><i data-lucide="alert-circle"></i> {{ session('error') }}</div>
@endif

<form method="GET" class="admin-filter-bar">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode..."
        class="admin-input" style="max-width:280px;">
    <button class="btn-outline">Filter</button>
</form>

<div class="admin-card admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Jumlah Produk</th>
                <th>Status</th>
                <th class="cell-actions">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $category)
            <tr>
                <td class="cell-mono">{{ $category->code ?? '-' }}</td>
                <td style="font-weight:600;">{{ $category->name }}</td>
                <td>{{ $category->products_count }}</td>
                <td>
                    <span class="admin-badge {{ $category->is_active ? 'admin-badge-success' : 'admin-badge-muted' }}">
                        {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td class="cell-actions">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="admin-link">Edit</a>
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                        style="display:inline" onsubmit="return confirm('Hapus kategori {{ $category->name }}?')">
                        @csrf @method('DELETE')
                        <button class="admin-link admin-link-danger"
                            style="background:none;border:none;cursor:pointer;">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="cell-empty">Belum ada kategori.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-pagination">{{ $categories->links() }}</div>
@endsection
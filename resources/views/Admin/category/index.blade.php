@extends('layouts.admin')
@section('title', 'Kategori')
@section('content')

<div class="admin-page-head">
    <h2>Kategori Produk</h2>
    <a href="{{ route('admin.categories.create') }}" class="btn-primary ripple"><i class="lucide-plus"></i> Tambah
        Kategori</a>
</div>

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif

<form method="GET" class="admin-filter-bar">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / kode..."
        class="admin-input" style="max-width:260px;">
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
                <th>Nama</th>
                <th>Kode</th>
                <th>Jumlah Produk</th>
                <th>Status</th>
                <th class="cell-actions">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $category)
            <tr>
                <td>{{ $category->name }}</td>
                <td class="cell-mono">{{ $category->code ?? '-' }}</td>
                <td>{{ $category->products_count }}</td>
                <td>
                    @if($category->is_active)
                    <span class="admin-badge admin-badge-success">Aktif</span>
                    @else
                    <span class="admin-badge admin-badge-muted">Nonaktif</span>
                    @endif
                </td>
                <td class="cell-actions">
                    <a href="{{ route('admin.categories.index', $category) }}" class="admin-link">Detail</a>
                    <a href="{{ route('admin.categories.edit', $category) }}" class="admin-link">Edit</a>
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                        style="display:inline;" onsubmit="return confirm('Hapus kategori ini?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="admin-link text-danger"
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

<div class="admin-pagination">{{ $categories->appends(request()->query())->links() }}</div>
@endsection
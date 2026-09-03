@extends('layouts.admin')
@section('title', 'Produk')
@section('content')

<div class="admin-page-head">
    <h2>Produk</h2>
    <a href="{{ route('admin.products.create') }}" class="btn-primary ripple"><i class="lucide-plus"></i> Tambah
        Produk</a>
</div>

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif

<form method="GET" class="admin-filter-bar">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / SKU / barcode..."
        class="admin-input" style="max-width:260px;">
    <select name="category_id" class="admin-select" style="max-width:180px;">
        <option value="">Semua Kategori</option>
        @foreach ($categories as $category)
        <option value="{{ $category->id }}" @selected(request('category_id')==$category->id)>{{ $category->name }}
        </option>
        @endforeach
    </select>
    <select name="is_active" class="admin-select" style="max-width:160px;">
        <option value="">Semua Status</option>
        <option value="1" @selected(request('is_active')==='1' )>Aktif</option>
        <option value="0" @selected(request('is_active')==='0' )>Nonaktif</option>
    </select>
    <label style="display:flex;align-items:center;gap:6px;font-size:13.5px;">
        <input type="checkbox" name="low_stock" value="1" @checked(request('low_stock'))> Stok Menipis
    </label>
    <button class="btn-outline">Filter</button>
</form>

<div class="admin-card admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>SKU</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Satuan</th>
                <th>Harga Jual</th>
                <th>Status</th>
                <th class="cell-actions">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
            <tr>
                <td class="cell-mono">{{ $product->sku }}</td>
                <td>{{ $product->name }}</td>
                <td class="cell-muted">{{ $product->category->name ?? '-' }}</td>
                <td class="cell-muted">{{ $product->unit }}</td>
                <td class="cell-mono">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                <td>
                    @if($product->is_active)
                    <span class="admin-badge admin-badge-success">Aktif</span>
                    @else
                    <span class="admin-badge admin-badge-muted">Nonaktif</span>
                    @endif
                </td>
                <td class="cell-actions">
                    <a href="{{ route('admin.products.show', $product) }}" class="admin-link">Detail</a>
                    <a href="{{ route('admin.products.edit', $product) }}" class="admin-link">Edit</a>
                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" style="display:inline;"
                        onsubmit="return confirm('Hapus produk ini?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="admin-link text-danger"
                            style="background:none;border:none;cursor:pointer;">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="cell-empty">Belum ada produk.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-pagination">{{ $products->appends(request()->query())->links() }}</div>
@endsection



@extends('layouts.admin')

@section('title', 'Produk')

@section('content')
    <div class="admin-page-head">
        <h2>Produk</h2>
        <a href="{{ route('admin.products.create') }}" class="btn-primary ripple">
            <i data-lucide="plus"></i> Tambah Produk
        </a>
    </div>

    @if (session('success'))
        <div class="admin-alert admin-alert-success"><i data-lucide="check-circle"></i> {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="admin-alert admin-alert-error"><i data-lucide="alert-circle"></i> {{ session('error') }}</div>
    @endif

    <form method="GET" class="admin-filter-bar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, SKU, barcode..."
               class="admin-input" style="max-width:260px;">
        <select name="category_id" class="admin-select" style="max-width:190px;">
            <option value="">Semua Kategori</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
        <label class="admin-checkbox-label admin-input" style="width:auto;">
            <input type="checkbox" name="low_stock" value="1" @checked(request('low_stock'))>
            Stok rendah
        </label>
        <button class="btn-outline">Filter</button>
    </form>

    <div class="admin-card admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Unit</th>
                    <th>Harga Jual</th>
                    <th>Status</th>
                    <th class="cell-actions">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td class="cell-mono">{{ $product->sku }}</td>
                        <td>
                            <a href="{{ route('admin.products.show', $product) }}" class="admin-link" style="font-weight:600;color:var(--text-primary);">
                                {{ $product->name }}
                            </a>
                        </td>
                        <td>{{ $product->category->name ?? '-' }}</td>
                        <td>{{ $product->unit }}</td>
                        <td>Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                        <td>
                            <span class="admin-badge {{ $product->is_active ? 'admin-badge-success' : 'admin-badge-muted' }}">
                                {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="cell-actions">
                            <a href="{{ route('admin.products.edit', $product) }}" class="admin-link">Edit</a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" style="display:inline"
                                  onsubmit="return confirm('Hapus produk {{ $product->name }}?')">
                                @csrf @method('DELETE')
                                <button class="admin-link admin-link-danger" style="background:none;border:none;cursor:pointer;">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="cell-empty">Belum ada produk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="admin-pagination">{{ $products->links() }}</div>
@endsection

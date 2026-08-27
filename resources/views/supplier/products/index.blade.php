@extends('layouts.supplier')

@section('title', 'Produk Saya')
@section('header', 'Produk Saya')
@section('subheader', 'Semua produk yang terdaftar atas nama Anda')

@section('content')

<div class="section-head">
    <div>
        <span class="eyebrow">Katalog</span>
        <h2>Cari produk</h2>
    </div>
    <form method="GET" class="field-inline">
        <input type="text" name="search" class="input" placeholder="Cari nama produk atau SKU..."
            value="{{ request('search') }}" style="min-width:220px;">
        <button type="submit" class="btn btn-primary btn-sm">Cari</button>
    </form>
</div>

<div class="table-wrap">
    <table class="manifest">
        <thead>
            <tr>
                <th>Nama</th>
                <th>SKU</th>
                <th>Kategori</th>
                <th class="text-right">Harga Beli</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
            <tr>
                <td>
                    <a href="{{ route('supplier.products.show', $product) }}"
                        style="font-weight:600; text-decoration:none;">
                        {{ $product->name }}
                    </a>
                </td>
                <td class="mono text-muted">{{ $product->sku }}</td>
                <td>{{ $product->category->name ?? '-' }}</td>
                <td class="text-right mono">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                <td>
                    @if ($product->is_active)
                    <span class="stamp stamp-success">Aktif</span>
                    @else
                    <span class="stamp stamp-muted">Nonaktif</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">
                    <div class="empty-state">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.5">
                            <path d="M21 8 12 3 3 8v8l9 5 9-5V8Z" />
                            <path d="M3 8l9 5 9-5" />
                            <path d="M12 13v8" />
                        </svg>
                        <div class="title">Belum ada produk terdaftar</div>
                        <div class="desc">
                            @if (request('search'))
                            Tidak ada produk yang cocok dengan pencarian "{{ request('search') }}".
                            @else
                            Produk yang gudang daftarkan atas nama Anda akan muncul di sini.
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination-wrap">
    {{ $products->links() }}
</div>

@endsection
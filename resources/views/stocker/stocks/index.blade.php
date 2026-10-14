@extends('layouts.stocker')
@section('title', $lowOnly ? 'Stok Menipis' : 'Semua Stok')

@section('content')
<div class="page-head">
    <div>
        <h2>{{ $lowOnly ? 'Stok Menipis' : 'Semua Stok' }}</h2>
        <p>{{ $warehouse->name }} — {{ $lowOnly ? 'produk dengan qty di bawah atau sama dengan stok minimum.' : 'daftar stok gudang Anda.' }}</p>
    </div>
    <span class="pill">● Read Only</span>
</div>

<form method="GET" class="filters">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk / SKU (boleh beberapa kata)">
    <button class="btn btn-primary" type="submit">Cari</button>
    @if (request()->filled('search'))
        <a class="btn" href="{{ url()->current() }}">Reset</a>
    @endif
</form>

<div class="card" style="padding:0;overflow:hidden">
    <table>
        <thead>
            <tr><th>Produk</th><th>SKU</th><th class="r">Qty</th><th class="r">Min. Stok</th><th>Satuan</th></tr>
        </thead>
        <tbody>
        @forelse ($stocks as $s)
            @php $low = $s->quantity <= $s->min_stock; @endphp
            <tr>
                <td>{{ $s->product_name }}</td>
                <td class="mono muted">{{ $s->sku }}</td>
                <td class="r"><span class="badge {{ $low ? '' : 'ok' }}">{{ $s->quantity }}</span></td>
                <td class="r muted">{{ $s->min_stock }}</td>
                <td class="muted">{{ $s->unit }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="empty">Tidak ada data.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="pager">{{ $stocks->links() }}</div>
@endsection

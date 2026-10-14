@extends('layouts.stocker')
@section('title', 'Dashboard')

@section('content')
<div class="page-head">
    <div>
        <h2>Dashboard Stocker</h2>
        <p>Halo, <strong>{{ auth()->user()->name }}</strong> — ringkasan stok <strong>{{ $warehouse->name }}</strong>.</p>
    </div>
    <span class="pill">● Read Only</span>
</div>

<div class="grid3">
    <div class="card stat"><div class="ico"></div><div><div class="lbl">Jenis Produk di Gudang</div><div class="val">{{ number_format($stats['total_items'], 0, ',', '.') }}</div></div></div>
    <div class="card stat"><div class="ico" style="background:#dcfce7"></div><div><div class="lbl">Total Qty</div><div class="val">{{ number_format($stats['total_qty'], 0, ',', '.') }}</div></div></div>
    <div class="card stat"><div class="ico" style="background:#fee2e2"></div><div><div class="lbl">Produk Stok Menipis</div><div class="val" style="color:var(--danger)">{{ number_format($stats['low_stock'], 0, ',', '.') }}</div></div></div>
</div>

<div class="grid2">
    <div class="card">
        <h3>Stok Menipis <span>(Top 10)</span></h3>
        <table>
            <thead><tr><th>Produk</th><th class="r">Qty</th><th class="r">Min. Stok</th></tr></thead>
            <tbody>
            @forelse ($lowStocks as $row)
                <tr>
                    <td>{{ $row->product_name }}</td>
                    <td class="r"><span class="badge">● {{ $row->quantity }}</span></td>
                    <td class="r muted">{{ $row->min_stock }} {{ $row->unit }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="empty">Tidak ada stok menipis. 🎉</td></tr>
            @endforelse
            </tbody>
        </table>
        <div style="margin-top:14px"><a class="btn" href="{{ route('stocker.stocks.low-stock') }}">Lihat semua stok menipis</a></div>
    </div>

    <div class="card">
        <h3>Nilai Stok Gudang</h3>
        <div class="muted" style="font-size:12px">{{ $warehouse->name }}</div>
        <div class="val mono" style="font-size:24px;font-weight:700;margin:6px 0 16px">Rp {{ number_format($stats['total_value'], 0, ',', '.') }}</div>
        <a class="btn" href="{{ route('stocker.stocks.index') }}">Lihat semua stok</a>
    </div>
</div>
@endsection

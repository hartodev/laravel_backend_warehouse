@extends('layouts.app')
@section('title', 'Ringkasan Stok')
@section('breadcrumb')
<a href="{{ route('superadmin.stock-reports.index') }}" class="hover:text-primary-700">Laporan Stok</a>
<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-700 font-medium">Summary</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <div><h1 class="text-xl font-bold text-gray-900">Ringkasan Stok</h1><p class="text-sm text-gray-500">Kondisi stok terkini semua gudang</p></div>
    <a href="{{ route('superadmin.stocks.low-stock') }}" class="btn-danger btn">⚠️ Stok Menipis</a>
</div>

<div class="card mb-5">
    <form method="GET" class="card-body flex flex-wrap gap-3 items-end">
        <div class="w-48">
            <label class="form-label">Gudang</label>
            <select name="warehouse_id" class="form-select">
                <option value="">Semua Gudang</option>
                @foreach($warehouses as $w)
                <option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary btn">Filter</button>
        <a href="{{ route('superadmin.stock-reports.summary') }}" class="btn-secondary btn">Reset</a>
    </form>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="stat-card"><div class="stat-icon bg-blue-50 text-blue-600">📦</div><div><p class="text-2xl font-bold">{{ $summary->count() }}</p><p class="text-sm text-gray-500">Total Item</p></div></div>
    <div class="stat-card"><div class="stat-icon bg-green-50 text-green-600">✅</div><div><p class="text-2xl font-bold text-green-700">{{ $summary->where('is_low', false)->count() }}</p><p class="text-sm text-gray-500">Stok Normal</p></div></div>
    <div class="stat-card"><div class="stat-icon bg-red-50 text-red-600">⚠️</div><div><p class="text-2xl font-bold text-red-600">{{ $summary->where('is_low', true)->count() }}</p><p class="text-sm text-gray-500">Stok Menipis</p></div></div>
    <div class="stat-card"><div class="stat-icon bg-gray-50 text-gray-600">🔢</div><div><p class="text-2xl font-bold">{{ number_format($summary->sum('quantity')) }}</p><p class="text-sm text-gray-500">Total Unit</p></div></div>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr><th>Gudang</th><th>Produk</th><th>SKU</th><th>Satuan</th><th class="text-right">Stok Min</th><th class="text-right">Stok Saat Ini</th><th>Status</th></tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($summary as $item)
            <tr class="{{ $item['is_low'] ? 'bg-red-50' : '' }}">
                <td>{{ $item['warehouse'] }}</td>
                <td class="font-medium">{{ $item['product'] }}</td>
                <td class="font-mono text-xs text-gray-500">{{ $item['sku'] }}</td>
                <td>{{ $item['unit'] }}</td>
                <td class="text-right">{{ number_format($item['min_stock']) }}</td>
                <td class="text-right font-bold {{ $item['is_low'] ? 'text-red-600' : 'text-gray-900' }}">
                    {{ number_format($item['quantity']) }}
                </td>
                <td>
                    @if($item['is_low'])
                        <span class="badge badge-danger">⚠ Menipis</span>
                    @else
                        <span class="badge badge-success">Normal</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center py-12 text-gray-400">Tidak ada data stok</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

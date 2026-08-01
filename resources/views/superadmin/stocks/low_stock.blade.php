{{-- stocks/low_stock.blade.php --}}
@extends('layouts.app')
@section('title','Stok Menipis')
@section('breadcrumb')<span class="text-gray-700 font-medium">Stok Menipis</span>@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Stok Menipis</h1>
        <p class="text-sm text-gray-500">{{ $stocks->count() }} item perlu restock</p>
    </div>
    <a href="{{ route('superadmin.stocks.index') }}" class="btn btn-secondary">Lihat Semua Stok</a>
</div>

<div class="card mb-5">
    <form method="GET" class="card-body flex flex-wrap gap-3 items-end">
        <div class="w-48">
            <label class="form-label">Gudang</label>
            <select name="warehouse_id" class="form-select">
                <option value="">Semua Gudang</option>
                @foreach($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}" {{ request('warehouse_id')==$warehouse->id ? 'selected' : '' }}>
                    {{ $warehouse->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('superadmin.stocks.low-stock') }}" class="btn btn-secondary">Reset</a>
    </form>
</div>

@if($stocks->isEmpty())
<div class="card">
    <div class="card-body text-center py-12">
        <p class="text-gray-400">Semua stok masih dalam batas aman 👍</p>
    </div>
</div>
@else
<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Gudang</th>
                <th class="text-right">Qty Saat Ini</th>
                <th class="text-right">Min. Stok</th>
                <th class="text-right">Kurang</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($stocks as $stock)
            <tr class="bg-red-50">
                <td class="font-medium">
                    {{ $stock->product->name ?? '—' }}
                    <div class="text-xs text-gray-400 font-mono">{{ $stock->product->sku ?? '' }}</div>
                </td>
                <td>
                    {{ $stock->warehouse->name ?? '—' }}
                    <div class="text-xs text-gray-400 font-mono">{{ $stock->warehouse->code ?? '' }}</div>
                </td>
                <td class="text-right font-semibold text-red-600">{{ number_format($stock->quantity) }}
                    {{ $stock->product->unit ?? '' }}</td>
                <td class="text-right text-gray-500">{{ number_format($stock->product->min_stock ?? 0) }}</td>
                <td class="text-right font-semibold text-red-600">
                    {{ number_format(max(($stock->product->min_stock ?? 0) - $stock->quantity, 0)) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
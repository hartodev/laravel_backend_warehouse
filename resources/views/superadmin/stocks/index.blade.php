{{-- stocks/index.blade.php --}}
@extends('layouts.app')
@section('title','Stok Saat Ini')
@section('breadcrumb')<span class="text-gray-700 font-medium">Stok Saat Ini</span>@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Stok Saat Ini</h1>
        <p class="text-sm text-gray-500">{{ $stocks->total() }} item stok</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('stocks.low-stock') }}" class="btn btn-secondary">⚠ Stok Menipis</a>
    </div>
</div>

<div class="card mb-5">
    <form method="GET" class="card-body flex flex-wrap gap-3 items-end">
        <div class="w-64">
            <label class="form-label">Cari Produk</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau SKU produk..." class="form-input">
        </div>
        <div class="w-48">
            <label class="form-label">Gudang</label>
            <select name="warehouse_id" class="form-select">
                <option value="">Semua Gudang</option>
                @foreach($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}" {{ request('warehouse_id')==$warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('stocks.index') }}" class="btn btn-secondary">Reset</a>
    </form>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Gudang</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Min. Stok</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($stocks as $stock)
            @php $isLow = $stock->quantity <= ($stock->product->min_stock ?? 0); @endphp
            <tr class="{{ $isLow ? 'bg-red-50' : '' }}">
                <td class="font-medium">
                    {{ $stock->product->name ?? '—' }}
                    <div class="text-xs text-gray-400 font-mono">{{ $stock->product->sku ?? '' }}</div>
                </td>
                <td>
                    {{ $stock->warehouse->name ?? '—' }}
                    <div class="text-xs text-gray-400 font-mono">{{ $stock->warehouse->code ?? '' }}</div>
                </td>
                <td class="text-right font-semibold">{{ number_format($stock->quantity) }} {{ $stock->product->unit ?? '' }}</td>
                <td class="text-right text-gray-500">{{ number_format($stock->product->min_stock ?? 0) }}</td>
                <td>
                    @if($isLow)
                        <span class="badge badge-danger">Menipis</span>
                    @else
                        <span class="badge badge-success">Aman</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center py-12 text-gray-400">Belum ada data stok</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $stocks->links() }}</div>
@endsection

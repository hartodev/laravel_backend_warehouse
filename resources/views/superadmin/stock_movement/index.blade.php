{{-- stock_movements/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Pergerakan Stok')
@section('breadcrumb')<span class="text-gray-700 font-medium">Pergerakan Stok</span>@endsection

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Pergerakan Stok</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $movements->total() }} record</p>
    </div>
    <a href="{{ route('superadmin.stock-movements.create') }}" class="btn-primary btn">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Catat Manual
    </a>
</div>

<div class="card mb-5">
    <form method="GET" class="card-body flex flex-wrap gap-3 items-end">
        <div class="w-40">
            <label class="form-label">Tipe</label>
            <select name="type" class="form-select">
                <option value="">Semua</option>
                <option value="in" {{ request('type')==='in'         ? 'selected' : '' }}>Masuk (In)</option>
                <option value="out" {{ request('type')==='out'        ? 'selected' : '' }}>Keluar (Out)</option>
                <option value="adjustment" {{ request('type')==='adjustment' ? 'selected' : '' }}>Penyesuaian</option>
            </select>
        </div>
        <div class="w-48">
            <label class="form-label">Gudang</label>
            <select name="warehouse_id" class="form-select">
                <option value="">Semua Gudang</option>
                @foreach($warehouses as $w)
                <option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="w-48">
            <label class="form-label">Produk</label>
            <select name="product_id" class="form-select">
                <option value="">Semua Produk</option>
                @foreach($products as $p)
                <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="w-36">
            <label class="form-label">Dari</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input">
        </div>
        <div class="w-36">
            <label class="form-label">Sampai</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input">
        </div>
        <button type="submit" class="btn-primary btn">Filter</button>
        <a href="{{ route('superadmin.stock-movements.index') }}" class="btn-secondary btn">Reset</a>
    </form>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Gudang</th>
                <th>Tipe</th>
                <th class="text-right">Sblm</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Sesudah</th>
                <th>Catatan</th>
                <th>User</th>
                <th>Waktu</th>
                <th class="text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($movements as $mv)
            <tr>
                <td>
                    <p class="font-medium">{{ $mv->product->name ?? '—' }}</p>
                    <p class="text-xs text-gray-400 font-mono">{{ $mv->product->sku ?? '' }}</p>
                </td>
                <td>{{ $mv->warehouse->name ?? '—' }}</td>
                <td>
                    <span
                        class="badge {{ $mv->type === 'in' ? 'badge-success' : ($mv->type === 'out' ? 'badge-danger' : 'badge-warning') }}">
                        {{ strtoupper($mv->type) }}
                    </span>
                </td>
                <td class="text-right text-gray-500">{{ number_format($mv->quantity_before) }}</td>
                <td
                    class="text-right font-bold {{ $mv->type === 'in' ? 'text-green-700' : ($mv->type === 'out' ? 'text-red-600' : 'text-yellow-700') }}">
                    {{ $mv->type === 'in' ? '+' : ($mv->type === 'out' ? '-' : '±') }}{{ number_format($mv->quantity) }}
                </td>
                <td class="text-right font-semibold">{{ number_format($mv->quantity_after) }}</td>
                <td class="text-gray-500 max-w-xs truncate text-xs">{{ $mv->note ?? '—' }}</td>
                <td class="text-sm">{{ $mv->createdBy->name ?? '—' }}</td>
                <td class="text-xs text-gray-400">{{ $mv->created_at->isoFormat('D MMM, HH:mm') }}</td>
                <td class="text-right">
                    <a href="{{ route('superadmin.stock-movements.show', $mv) }}"
                        class="btn btn-secondary btn-sm">Detail</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center py-12 text-gray-400">Belum ada data pergerakan stok</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $movements->links() }}</div>
@endsection
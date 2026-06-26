{{-- stock_reports/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Laporan Stok')
@section('breadcrumb')<span class="text-gray-700 font-medium">Laporan Stok</span>@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <div><h1 class="text-xl font-bold text-gray-900">Laporan Stok</h1></div>
    <a href="{{ route('stock-reports.summary') }}" class="btn-secondary btn">Lihat Summary</a>
</div>

<div class="card mb-5">
    <form method="GET" class="card-body flex flex-wrap gap-3 items-end">
        <div class="w-36">
            <label class="form-label">Period Type</label>
            <select name="period_type" class="form-select">
                <option value="">Semua</option>
                <option value="daily"   {{ request('period_type')==='daily'   ? 'selected' : '' }}>Harian</option>
                <option value="weekly"  {{ request('period_type')==='weekly'  ? 'selected' : '' }}>Mingguan</option>
                <option value="monthly" {{ request('period_type')==='monthly' ? 'selected' : '' }}>Bulanan</option>
            </select>
        </div>
        <div class="w-48">
            <label class="form-label">Gudang</label>
            <select name="warehouse_id" class="form-select">
                <option value="">Semua Gudang</option>
                @foreach($warehouses as $w)
                <option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-36"><label class="form-label">Dari</label><input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input"></div>
        <div class="w-36"><label class="form-label">Sampai</label><input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input"></div>
        <button type="submit" class="btn-primary btn">Filter</button>
        <a href="{{ route('stock-reports.index') }}" class="btn-secondary btn">Reset</a>
    </form>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>Produk</th><th>Gudang</th><th>Period</th><th>Tipe</th>
                <th class="text-right">Stok Awal</th><th class="text-right">Masuk</th>
                <th class="text-right">Keluar</th><th class="text-right">Stok Akhir</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($reports as $r)
            <tr>
                <td><p class="font-medium">{{ $r->product->name ?? '—' }}</p><p class="font-mono text-xs text-gray-400">{{ $r->product->sku ?? '' }}</p></td>
                <td>{{ $r->warehouse->name ?? '—' }}</td>
                <td>{{ \Carbon\Carbon::parse($r->period_date)->isoFormat('D MMM Y') }}</td>
                <td><span class="badge badge-info">{{ ucfirst($r->period_type) }}</span></td>
                <td class="text-right">{{ number_format($r->opening_stock ?? 0) }}</td>
                <td class="text-right text-green-700 font-medium">+{{ number_format($r->total_in ?? 0) }}</td>
                <td class="text-right text-red-600 font-medium">-{{ number_format($r->total_out ?? 0) }}</td>
                <td class="text-right font-bold text-primary-700">{{ number_format($r->closing_stock ?? 0) }}</td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center py-12 text-gray-400">Belum ada laporan stok</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $reports->links() }}</div>
@endsection

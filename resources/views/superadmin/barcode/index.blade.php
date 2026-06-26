{{-- barcodes/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Barcode Log')
@section('breadcrumb')<span class="text-gray-700 font-medium">Barcode</span>@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <div><h1 class="text-xl font-bold text-gray-900">Barcode Scan Log</h1><p class="text-sm text-gray-500">{{ $logs->total() }} scan</p></div>
    <a href="{{ route('barcodes.scan') }}" class="btn-primary btn">📷 Scan Barcode</a>
</div>

<div class="card mb-5">
    <form method="GET" class="card-body flex flex-wrap gap-3 items-end">
        <div class="w-40">
            <label class="form-label">Tipe Scan</label>
            <select name="scan_type" class="form-select">
                <option value="">Semua</option>
                @foreach(['stock_in','stock_out','transfer','check','purchase'] as $t)
                <option value="{{ $t }}" {{ request('scan_type')===$t ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-48">
            <label class="form-label">Gudang</label>
            <select name="warehouse_id" class="form-select">
                <option value="">Semua</option>
                @foreach($warehouses as $w)
                <option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-36">
            <label class="form-label">Ditemukan</label>
            <select name="is_found" class="form-select">
                <option value="">Semua</option>
                <option value="1" {{ request('is_found')==='1' ? 'selected' : '' }}>Ditemukan</option>
                <option value="0" {{ request('is_found')==='0' ? 'selected' : '' }}>Tidak Ditemukan</option>
            </select>
        </div>
        <div class="w-36"><label class="form-label">Dari</label><input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input"></div>
        <div class="w-36"><label class="form-label">Sampai</label><input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input"></div>
        <button type="submit" class="btn-primary btn">Filter</button>
        <a href="{{ route('barcodes.index') }}" class="btn-secondary btn">Reset</a>
    </form>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Barcode</th><th>Produk</th><th>Tipe Scan</th><th>Gudang</th><th>User</th><th>Waktu</th><th>Status</th></tr></thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($logs as $log)
            <tr class="{{ !$log->is_found ? 'bg-red-50' : '' }}">
                <td class="font-mono text-sm">{{ $log->barcode_value }}</td>
                <td>{{ $log->product->name ?? '<span class="text-red-500 text-xs">Tidak ditemukan</span>' }}</td>
                <td><span class="badge badge-info">{{ ucfirst(str_replace('_',' ',$log->scan_type)) }}</span></td>
                <td>{{ $log->warehouse->name ?? '—' }}</td>
                <td>{{ $log->user->name ?? '—' }}</td>
                <td class="text-xs text-gray-500">{{ $log->created_at->isoFormat('D MMM, HH:mm') }}</td>
                <td>
                    @if($log->is_found)
                        <span class="badge badge-success">✓ Ditemukan</span>
                    @else
                        <span class="badge badge-danger">✗ Tidak Ada</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center py-12 text-gray-400">Belum ada scan log</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $logs->links() }}</div>
@endsection

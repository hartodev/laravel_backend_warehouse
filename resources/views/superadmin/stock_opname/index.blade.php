@extends('layouts.app')
@section('title', 'Stock Opname')
@section('breadcrumb')<span class="text-gray-700 font-medium">Stock Opname</span>@endsection

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Stock Opname</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $opnames->total() }} record</p>
    </div>
    <a href="{{ route('stock-opnames.create') }}" class="btn-primary btn">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Buat Opname
    </a>
</div>

<div class="card mb-5">
    <form method="GET" class="card-body flex flex-wrap gap-3 items-end">
        <div class="w-44">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua</option>
                @foreach(['draft','in_progress','pending_approval','approved','rejected'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                @endforeach
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
        <button type="submit" class="btn-primary btn">Filter</button>
        <a href="{{ route('stock-opnames.index') }}" class="btn-secondary btn">Reset</a>
    </form>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>No. Opname</th>
                <th>Gudang</th>
                <th>Tanggal</th>
                <th>Total Item</th>
                <th>Selisih</th>
                <th>Dibuat Oleh</th>
                <th>Status</th>
                <th class="text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($opnames as $opname)
            <tr>
                <td><span class="font-mono text-sm font-medium text-primary-700">{{ $opname->opname_number }}</span></td>
                <td>{{ $opname->warehouse->name ?? '—' }}</td>
                <td>{{ \Carbon\Carbon::parse($opname->opname_date)->isoFormat('D MMM Y') }}</td>
                <td>{{ $opname->items_count ?? $opname->items->count() }}</td>
                <td>
                    @php $diff = $opname->items->sum('difference') @endphp
                    <span class="{{ $diff < 0 ? 'text-red-600' : ($diff > 0 ? 'text-green-600' : 'text-gray-500') }} font-medium">
                        {{ $diff > 0 ? '+' : '' }}{{ $diff }}
                    </span>
                </td>
                <td>{{ $opname->createdBy->name ?? '—' }}</td>
                <td><x-status-badge :status="$opname->status" /></td>
                <td class="text-right">
                    <a href="{{ route('stock-opnames.show', $opname) }}" class="btn btn-secondary btn-sm">Detail</a>
                    @if($opname->status === 'pending_approval')
                    <form method="POST" action="{{ route('stock-opnames.approve', $opname) }}" class="inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">Setujui</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center py-12 text-gray-400">Belum ada data stock opname</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $opnames->links() }}</div>
@endsection

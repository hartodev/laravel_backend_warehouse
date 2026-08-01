{{-- resources/views/superadmin/product_submission/index.blade.php --}}
@extends('layouts.app')
@section('title','Pengajuan Produk')
@section('breadcrumb')<span class="text-gray-700 font-medium">Pengajuan Produk</span>@endsection

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Pengajuan Produk</h1>
        <p class="text-sm text-gray-500">{{ $submissions->total() }} pengajuan</p>
    </div>
</div>

<div class="card mb-5">
    <form method="GET" class="card-body flex flex-wrap gap-3 items-end">
        <div class="w-56">
            <label class="form-label">Cari Produk</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-input"
                placeholder="Nama produk / SKU">
        </div>
        <div class="w-40">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua</option>
                <option value="pending" {{ request('status')==='pending'?'selected':'' }}>Pending</option>
                <option value="approved" {{ request('status')==='approved'?'selected':'' }}>Approved</option>
                <option value="rejected" {{ request('status')==='rejected'?'selected':'' }}>Rejected</option>
            </select>
        </div>
        <div class="w-40">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input">
        </div>
        <div class="w-40">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input">
        </div>
        <button type="submit" class="btn-primary btn">Filter</button>
        <a href="{{ route('superadmin.product-submissions.index') }}" class="btn-secondary btn">Reset</a>
    </form>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>SKU</th>
                <th>Kategori</th>
                <th>Diajukan Oleh</th>
                <th>Tgl. Pengajuan</th>
                <th>Status</th>
                <th class="text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($submissions as $s)
            <tr>
                <td class="max-w-xs truncate">{{ $s->product->name ?? '—' }}</td>
                <td class="font-mono text-xs">{{ $s->product->sku ?? '—' }}</td>
                <td>{{ $s->product->category->name ?? '—' }}</td>
                <td>{{ $s->submittedBy->name ?? '—' }}</td>
                <td>{{ $s->created_at?->isoFormat('D MMM Y, HH:mm') }}</td>
                <td>
                    <span
                        class="badge {{ $s->status === 'approved' ? 'badge-success' : ($s->status === 'pending' ? 'badge-warning' : 'badge-danger') }}">
                        {{ ucfirst($s->status) }}
                    </span>
                </td>
                <td class="text-right">
                    <a href="{{ route('superadmin.product-submissions.show', $s) }}"
                        class="btn btn-secondary btn-sm">Detail</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-12 text-gray-400">Belum ada pengajuan produk</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $submissions->links() }}</div>
@endsection
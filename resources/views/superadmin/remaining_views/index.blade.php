{{-- product_submissions/index.blade.php --}}
@extends('superadmin.layouts.app')
@section('title','Submission Produk')
@section('breadcrumb')<span class="text-gray-700 font-medium">Submission Produk</span>@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <div><h1 class="text-xl font-bold text-gray-900">Submission Produk</h1><p class="text-sm text-gray-500">{{ $submissions->total() }} submission</p></div>
</div>

<div class="card mb-5">
    <form method="GET" class="card-body flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48"><label class="form-label">Cari</label><input type="text" name="search" value="{{ request('search') }}" placeholder="Nama produk, SKU..." class="form-input"></div>
        <div class="w-40"><label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua</option>
                <option value="pending" {{ request('status')==='pending'?'selected':'' }}>Pending</option>
                <option value="approved" {{ request('status')==='approved'?'selected':'' }}>Approved</option>
                <option value="rejected" {{ request('status')==='rejected'?'selected':'' }}>Rejected</option>
            </select>
        </div>
        <button type="submit" class="btn-primary btn">Filter</button>
        <a href="{{ route('superadmin.product-submissions.index') }}" class="btn-secondary btn">Reset</a>
    </form>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Produk</th><th>SKU</th><th>Kategori</th><th>Disubmit Oleh</th><th>Tgl.</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($submissions as $sub)
            <tr>
                <td class="font-medium">{{ $sub->product->name ?? '—' }}</td>
                <td class="font-mono text-xs text-gray-500">{{ $sub->product->sku ?? '—' }}</td>
                <td>{{ $sub->product->category->name ?? '—' }}</td>
                <td>{{ $sub->submittedBy->name ?? '—' }}</td>
                <td>{{ $sub->created_at->isoFormat('D MMM Y') }}</td>
                <td><x-status-badge :status="$sub->status" /></td>
                <td class="text-right">
                    <div class="flex items-center justify-end gap-1">
                        <a href="{{ route('superadmin.product-submissions.show', $sub) }}" class="btn btn-secondary btn-sm">Detail</a>
                        @if($sub->status === 'pending')
                        <form method="POST" action="{{ route('superadmin.product-submissions.approve', $sub) }}" class="inline">@csrf<button class="btn btn-success btn-sm">Setujui</button></form>
                        <button onclick="document.getElementById('rej-sub-{{ $sub->id }}').classList.remove('hidden')" class="btn btn-danger btn-sm">Tolak</button>
                        @endif
                    </div>
                    <x-confirm-modal :id="'rej-sub-'.$sub->id" title="Tolak Submission?" :message="'Submission produk '.$sub->product->name.' akan ditolak.'" :action="route('superadmin.product-submissions.reject', $sub)" method="POST" confirm-label="Tolak" confirm-class="btn-danger">
                        <div class="mt-3"><label class="form-label">Alasan <span class="text-red-500">*</span></label><textarea name="review_note" rows="2" required class="form-textarea" maxlength="500"></textarea></div>
                    </x-confirm-modal>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center py-12 text-gray-400">Belum ada submission produk</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $submissions->links() }}</div>
@endsection

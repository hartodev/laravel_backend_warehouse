@extends('layouts.admin')
@section('title', 'Pengajuan Produk')
@section('content')

<div class="admin-page-head admin-page-head-flex">
    <div>
        <h2>Pengajuan Produk</h2>
        <p class="admin-page-subtitle">Daftar pengajuan produk baru yang Anda ajukan.</p>
    </div>
    <a href="{{ route('admin.product-submissions.create') }}" class="btn btn-primary">
        <i class="lucide-plus"></i> Ajukan Produk Baru
    </a>
</div>

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif

<div class="admin-card admin-filter-card">
    <form method="GET" class="admin-filter-bar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama produk..."
            class="admin-input" style="max-width:260px;">
        <select name="status" class="admin-select" style="max-width:160px;">
            <option value="">Semua Status</option>
            <option value="pending" @selected(request('status')==='pending' )>Pending</option>
            <option value="approved" @selected(request('status')==='approved' )>Disetujui</option>
            <option value="rejected" @selected(request('status')==='rejected' )>Ditolak</option>
        </select>
        <button class="btn-outline">Filter</button>
        @if(request('search') || request('status'))
        <a href="{{ route('admin.product-submissions.index') }}" class="admin-link">Reset</a>
        @endif
    </form>
</div>

<div class="admin-card admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Stok Awal</th>
                <th>Gudang Awal</th>
                <th>Status</th>
                <th class="cell-actions">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($submissions as $submission)
            <tr>
                <td>
                    {{ $submission->name }}
                    @if($submission->is_urgent)
                    <span class="admin-badge admin-badge-danger" style="margin-left:6px;">Urgent</span>
                    @endif
                </td>
                <td class="cell-muted">{{ $submission->category->name ?? '-' }}</td>
                <td class="cell-mono">{{ $submission->initial_stock ?? 0 }}</td>
                <td class="cell-muted">{{ $submission->initialWarehouse->name ?? '-' }}</td>
                <td>
                    @if($submission->status === 'approved')
                    <span class="admin-badge admin-badge-success">Disetujui</span>
                    @elseif($submission->status === 'rejected')
                    <span class="admin-badge admin-badge-danger">Ditolak</span>
                    @else
                    <span class="admin-badge admin-badge-warning">Pending</span>
                    @endif
                </td>
                <td class="cell-actions">
                    <a href="{{ route('admin.product-submissions.show', $submission) }}" class="admin-link">Detail</a>
                    @if($submission->status === 'pending')
                    <a href="{{ route('admin.product-submissions.edit', $submission) }}" class="admin-link">Edit</a>
                    <form action="{{ route('admin.product-submissions.destroy', $submission) }}" method="POST"
                        style="display:inline;" onsubmit="return confirm('Hapus pengajuan ini?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="admin-link text-danger"
                            style="background:none;border:none;cursor:pointer;">Hapus</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="cell-empty">Belum ada pengajuan produk.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-pagination">{{ $submissions->appends(request()->query())->links() }}</div>

<style>
.admin-page-head-flex {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}

.admin-page-subtitle {
    margin: 4px 0 0;
    font-size: 13px;
    color: var(--admin-text-muted, #6b7280);
}

.admin-filter-card {
    padding: 14px 16px;
    margin-bottom: 16px;
}

.admin-filter-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    margin: 0;
}
</style>
@endsection
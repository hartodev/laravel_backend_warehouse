@extends('layouts.admin')
@section('title', 'Revisi Anggaran')
@section('content')

<div class="admin-page-head">
    <h2>Revisi Anggaran</h2>
    <a href="{{ route('admin.budget-revisions.create') }}" class="btn-primary">+ Ajukan Revisi</a>
</div>

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif

<form method="GET" class="admin-filter-bar">
    <select name="status" class="admin-select" style="max-width:180px;">
        <option value="">Semua Status</option>
        <option value="pending" @selected(request('status')==='pending' )>Pending</option>
        <option value="approved" @selected(request('status')==='approved' )>Approved</option>
        <option value="ditolak" @selected(request('status')==='ditolak' )>Ditolak</option>
    </select>
    <button class="btn-outline">Filter</button>
</form>

<div class="admin-card admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>RAB Terkait</th>
                <th>Akun Terdampak</th>
                <th>Jenis Perubahan</th>
                <th>Nominal</th>
                <th>Status</th>
                <th>Diajukan Oleh</th>
                <th class="cell-actions">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($revisions as $revision)
            <tr>
                <td class="cell-mono">{{ $revision->budgetRequest->nomor_form ?? '-' }}</td>
                <td>
                    {{ $revision->akun_terdampak }}
                    @if($revision->kode_akun)
                    <span class="cell-muted">({{ $revision->kode_akun }})</span>
                    @endif
                </td>
                <td>
                    @if($revision->jenis_perubahan === 'tambahan')
                    <span class="admin-badge admin-badge-success">Tambahan</span>
                    @else
                    <span class="admin-badge admin-badge-danger">Pengurangan</span>
                    @endif
                </td>
                <td class="cell-mono">Rp {{ number_format($revision->nominal_perubahan, 0, ',', '.') }}</td>
                <td>
                    @if($revision->status === 'approved')
                    <span class="admin-badge admin-badge-success">Approved</span>
                    @elseif($revision->status === 'ditolak')
                    <span class="admin-badge admin-badge-danger">Ditolak</span>
                    @else
                    <span class="admin-badge admin-badge-warning">Pending</span>
                    @endif
                </td>
                <td>{{ $revision->createdBy->name ?? '-' }}</td>
                <td class="cell-actions">
                    <a href="{{ route('admin.budget-revisions.show', $revision) }}" class="admin-link">Detail</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="cell-empty">Belum ada revisi anggaran.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-pagination">{{ $revisions->appends(request()->query())->links() }}</div>
@endsection
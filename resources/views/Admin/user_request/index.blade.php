@extends('layouts.admin')
@section('title', 'Pengajuan User')
@section('content')

<div class="admin-page-head">
    <h2>Pengajuan User</h2>
    <a href="{{ route('admin.user-requests.create') }}" class="btn-primary ripple"><i class="lucide-plus"></i> Ajukan User Baru</a>
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
        <option value="pending" @selected(request('status')==='pending')>Pending</option>
        <option value="approved" @selected(request('status')==='approved')>Disetujui</option>
        <option value="rejected" @selected(request('status')==='rejected')>Ditolak</option>
    </select>
    <button class="btn-outline">Filter</button>
</form>

<div class="admin-card admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Role Diajukan</th>
                <th>Status</th>
                <th class="cell-actions">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($userRequests as $req)
            <tr>
                <td>{{ $req->name }}</td>
                <td class="cell-muted">{{ $req->email }}</td>
                <td class="cell-muted">{{ ucfirst($req->role) }}</td>
                <td>
                    @php
                    $badgeMap = ['pending'=>'admin-badge-warning','approved'=>'admin-badge-success','rejected'=>'admin-badge-danger'];
                    @endphp
                    <span class="admin-badge {{ $badgeMap[$req->status] ?? 'admin-badge-muted' }}">{{ ucfirst($req->status) }}</span>
                </td>
                <td class="cell-actions">
                    <a href="{{ route('admin.user-requests.show', $req) }}" class="admin-link">Detail</a>
                    @if($req->status === 'pending')
                    <form action="{{ route('admin.user-requests.destroy', $req) }}" method="POST" style="display:inline;" onsubmit="return confirm('Batalkan pengajuan ini?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="admin-link text-danger" style="background:none;border:none;cursor:pointer;">Batalkan</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="cell-empty">Belum ada pengajuan user.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-pagination">{{ $userRequests->appends(request()->query())->links() }}</div>
@endsection

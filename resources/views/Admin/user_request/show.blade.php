@extends('layouts.admin')
@section('title', 'Detail Pengajuan User')
@section('content')

@php
$badgeMap = ['pending'=>'admin-badge-warning','approved'=>'admin-badge-success','rejected'=>'admin-badge-danger'];
$isSuperadmin = in_array(auth()->user()->role, ['superadmin', 'super_admin']);
@endphp

<div class="admin-page-head">
    <h2>Pengajuan — {{ $userRequest->name }}</h2>
    <span class="admin-badge {{ $badgeMap[$userRequest->status] ?? 'admin-badge-muted' }}">{{ ucfirst($userRequest->status) }}</span>
</div>

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif
@if ($errors->any())
<div class="admin-alert admin-alert-error">
    @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
</div>
@endif

<div class="admin-detail-grid" style="margin-bottom:20px;">
    <div class="admin-detail-item"><p class="admin-label">Email</p><p>{{ $userRequest->email }}</p></div>
    <div class="admin-detail-item"><p class="admin-label">Telepon</p><p>{{ $userRequest->phone ?? '-' }}</p></div>
    <div class="admin-detail-item"><p class="admin-label">Divisi</p><p>{{ $userRequest->division ?? '-' }}</p></div>
    <div class="admin-detail-item"><p class="admin-label">Role Diajukan</p><p>{{ ucfirst($userRequest->role) }}</p></div>
    <div class="admin-detail-item" style="grid-column:span 2;"><p class="admin-label">Alamat</p><p>{{ $userRequest->address ?? '-' }}</p></div>
    <div class="admin-detail-item" style="grid-column:span 2;"><p class="admin-label">Alasan Pengajuan</p><p>{{ $userRequest->reason }}</p></div>
    @if($userRequest->status === 'rejected' && $userRequest->reject_reason)
    <div class="admin-detail-item" style="grid-column:span 2;"><p class="admin-label">Alasan Ditolak</p><p>{{ $userRequest->reject_reason }}</p></div>
    @endif
</div>

@if($userRequest->status === 'pending')
<div class="admin-card" style="padding:16px;margin-bottom:20px;">
    <p class="admin-label" style="margin-bottom:10px;">Ubah Role Sebelum Diproses</p>
    <form action="{{ route('admin.user-requests.update', $userRequest) }}" method="POST" style="display:flex;gap:10px;align-items:flex-end;">
        @csrf @method('PATCH')
        <div>
            <label class="admin-label">Role</label>
            <select name="role" class="admin-select">
                <option value="user" @selected($userRequest->role === 'user')>User</option>
                <option value="admin" @selected($userRequest->role === 'admin')>Admin</option>
            </select>
        </div>
        <button type="submit" class="btn-outline">Update Role</button>
    </form>
</div>

@if($isSuperadmin)
<div class="admin-card" style="padding:16px;margin-bottom:20px;">
    <p class="admin-label" style="margin-bottom:10px;">Persetujuan Superadmin</p>
    <div style="display:flex;gap:10px;">
        <form action="{{ route('admin.user-requests.approve', $userRequest) }}" method="POST" onsubmit="return confirm('Setujui pengajuan ini? Akun user akan dibuat.');">
            @csrf
            <button type="submit" class="btn-primary ripple">Setujui</button>
        </form>
        <button type="button" class="btn-secondary" onclick="document.getElementById('req-reject-form').classList.toggle('hidden')">Tolak</button>
    </div>
    <form id="req-reject-form" action="{{ route('admin.user-requests.reject', $userRequest) }}" method="POST" class="hidden" style="margin-top:12px;">
        @csrf
        <label class="admin-label">Alasan Penolakan</label>
        <textarea name="reject_reason" class="admin-textarea"></textarea>
        <button type="submit" class="btn-primary ripple" style="margin-top:8px;">Kirim Penolakan</button>
    </form>
</div>
@endif

<form action="{{ route('admin.user-requests.destroy', $userRequest) }}" method="POST" onsubmit="return confirm('Batalkan pengajuan ini?');">
    @csrf @method('DELETE')
    <button type="submit" class="btn-secondary text-danger">Batalkan Pengajuan</button>
</form>
@endif

<div class="admin-action-panel" style="margin-top:20px;">
    <a href="{{ route('admin.user-requests.index') }}" class="btn-secondary">← Kembali</a>
</div>

<style>.hidden{display:none;}</style>
@endsection

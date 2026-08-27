@extends('layouts.admin')
@section('title', 'Detail User')
@section('content')

<div class="admin-page-head">
    <h2>{{ $user->name }}</h2>
    @if($user->is_active)
    <span class="admin-badge admin-badge-success">Aktif</span>
    @else
    <span class="admin-badge admin-badge-muted">Nonaktif</span>
    @endif
</div>

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif

<div class="admin-detail-grid" style="margin-bottom:20px;">
    @if($user->profile?->photo)
    <div class="admin-detail-item" style="grid-column:span 2;">
        <img src="{{ asset('storage/'.$user->profile->photo) }}" alt="{{ $user->name }}" style="max-height:120px;border-radius:8px;">
    </div>
    @endif
    <div class="admin-detail-item"><p class="admin-label">Email</p><p>{{ $user->email }}</p></div>
    <div class="admin-detail-item"><p class="admin-label">Role</p><p>{{ ucwords(str_replace('_', ' ', $user->role)) }}</p></div>
    <div class="admin-detail-item"><p class="admin-label">Telepon</p><p>{{ $user->profile->phone ?? '-' }}</p></div>
    <div class="admin-detail-item"><p class="admin-label">Alamat</p><p>{{ $user->profile->address ?? '-' }}</p></div>
</div>

<div class="admin-card" style="padding:16px;margin-bottom:20px;">
    <p class="admin-label" style="margin-bottom:10px;">Reset Password</p>
    <form action="{{ route('admin.users.reset-password', $user) }}" method="POST">
        @csrf @method('PATCH')
        <div class="admin-form-grid" style="grid-template-columns:repeat(2,1fr);margin-bottom:12px;max-width:480px;">
            <div>
                <label class="admin-label">Password Baru</label>
                <input type="password" name="password" required class="admin-input">
            </div>
            <div>
                <label class="admin-label">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required class="admin-input">
            </div>
        </div>
        <button type="submit" class="btn-outline">Reset Password</button>
    </form>
</div>

<div class="admin-action-panel" style="display:flex;justify-content:space-between;">
    <a href="{{ route('admin.users.index') }}" class="btn-secondary">← Kembali</a>
    <a href="{{ route('admin.users.edit', $user) }}" class="btn-primary ripple">Edit</a>
</div>
@endsection

@extends('layouts.admin')
@section('title', 'Edit User')
@section('content')

<div class="admin-page-head"><h2>Edit User — {{ $user->name }}</h2></div>

@if ($errors->any())
<div class="admin-alert admin-alert-error">
    @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
</div>
@endif

<form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="admin-form-grid" style="grid-template-columns:repeat(2,1fr);margin-bottom:20px;">
        <div>
            <label class="admin-label">Nama</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="admin-input">
        </div>
        <div>
            <label class="admin-label">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="admin-input">
        </div>
        <div>
            <label class="admin-label">Role</label>
            <select name="role" required class="admin-select">
                <option value="super_admin" @selected(old('role', $user->role)==='super_admin')>Super Admin</option>
                <option value="admin" @selected(old('role', $user->role)==='admin')>Admin</option>
                <option value="user" @selected(old('role', $user->role)==='user')>User</option>
            </select>
        </div>
        <div>
            <label class="admin-label">Telepon</label>
            <input type="text" name="phone" value="{{ old('phone', $user->profile->phone ?? '') }}" class="admin-input">
        </div>
        <div style="grid-column:span 2;">
            <label class="admin-label">Alamat</label>
            <textarea name="address" class="admin-textarea">{{ old('address', $user->profile->address ?? '') }}</textarea>
        </div>
        <div>
            <label class="admin-label">Foto</label>
            <input type="file" name="photo" accept="image/*" class="admin-input">
            @if($user->profile?->photo)
            <p class="cell-muted" style="margin-top:6px;">Foto saat ini: <img src="{{ asset('storage/'.$user->profile->photo) }}" alt="foto user" style="height:40px;border-radius:6px;vertical-align:middle;margin-left:6px;"></p>
            @endif
        </div>
    </div>
    <div class="admin-form-actions" style="justify-content:flex-end;">
        <a href="{{ route('admin.users.show', $user) }}" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary ripple">Simpan Perubahan</button>
    </div>
</form>
@endsection

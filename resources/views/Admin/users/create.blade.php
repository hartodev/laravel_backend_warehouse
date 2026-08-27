@extends('layouts.admin')
@section('title', 'Tambah User')
@section('content')

<div class="admin-page-head"><h2>Tambah User</h2></div>

@if ($errors->any())
<div class="admin-alert admin-alert-error">
    @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
</div>
@endif

<form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="admin-form-grid" style="grid-template-columns:repeat(2,1fr);margin-bottom:20px;">
        <div>
            <label class="admin-label">Nama</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="admin-input">
        </div>
        <div>
            <label class="admin-label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="admin-input">
        </div>
        <div>
            <label class="admin-label">Password</label>
            <input type="password" name="password" required class="admin-input">
        </div>
        <div>
            <label class="admin-label">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" required class="admin-input">
        </div>
        <div>
            <label class="admin-label">Role</label>
            <select name="role" required class="admin-select">
                <option value="">Pilih Role</option>
                <option value="super_admin" @selected(old('role')==='super_admin')>Super Admin</option>
                <option value="admin" @selected(old('role')==='admin')>Admin</option>
                <option value="user" @selected(old('role')==='user')>User</option>
            </select>
        </div>
        <div>
            <label class="admin-label">Telepon</label>
            <input type="text" name="phone" value="{{ old('phone') }}" class="admin-input">
        </div>
        <div style="grid-column:span 2;">
            <label class="admin-label">Alamat</label>
            <textarea name="address" class="admin-textarea">{{ old('address') }}</textarea>
        </div>
        <div>
            <label class="admin-label">Foto</label>
            <input type="file" name="photo" accept="image/*" class="admin-input">
        </div>
    </div>
    <div class="admin-form-actions" style="justify-content:flex-end;">
        <a href="{{ route('admin.users.index') }}" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary ripple">Simpan</button>
    </div>
</form>
@endsection

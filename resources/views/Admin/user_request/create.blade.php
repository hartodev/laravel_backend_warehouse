@extends('layouts.admin')
@section('title', 'Ajukan User Baru')
@section('content')

<div class="admin-page-head"><h2>Ajukan User Baru</h2></div>

@if ($errors->any())
<div class="admin-alert admin-alert-error">
    @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif

<form action="{{ route('admin.user-requests.store') }}" method="POST">
    @csrf
    <div class="admin-form-grid" style="grid-template-columns:repeat(2,1fr);margin-bottom:20px;">
        <div>
            <label class="admin-label">Nama</label>
            <input type="text" name="name" value="{{ old('name') }}" required maxlength="150" class="admin-input">
        </div>
        <div>
            <label class="admin-label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required maxlength="150" class="admin-input">
        </div>
        <div>
            <label class="admin-label">Telepon</label>
            <input type="text" name="phone" value="{{ old('phone') }}" maxlength="30" class="admin-input">
        </div>
        <div>
            <label class="admin-label">Role</label>
            <select name="role" required class="admin-select">
                <option value="">Pilih Role</option>
                <option value="user" @selected(old('role')==='user')>User</option>
                <option value="admin" @selected(old('role')==='admin')>Admin</option>
            </select>
        </div>
        <div style="grid-column:span 2;">
            <label class="admin-label">Alamat</label>
            <textarea name="address" maxlength="255" class="admin-textarea">{{ old('address') }}</textarea>
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
            <label class="admin-label">Divisi</label>
            <input type="text" name="division" value="{{ old('division') }}" maxlength="100" class="admin-input">
        </div>
        <div style="grid-column:span 2;">
            <label class="admin-label">Alasan Pengajuan</label>
            <textarea name="reason" required maxlength="1000" class="admin-textarea"></textarea>
        </div>
    </div>
    <div class="admin-form-actions" style="justify-content:flex-end;">
        <a href="{{ route('admin.user-requests.index') }}" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary ripple">Kirim Pengajuan</button>
    </div>
</form>
@endsection

{{-- users/edit.blade.php --}}
@extends('layouts.app')
@section('title','Edit User')
@section('breadcrumb')
    <a href="{{ route('users.index') }}" class="text-gray-500 hover:text-gray-700">User</a>
    <span class="text-gray-400 mx-1">/</span>
    <span class="text-gray-700 font-medium">Edit</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-bold text-gray-900">Edit User</h1>
    <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali</a>
</div>

<form method="POST" action="{{ route('users.update', $user) }}" class="space-y-5">
    @csrf
    @method('PUT')

    <div class="card">
        <div class="card-body">
            <h3 class="page-title text-base mb-4">Informasi User</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Nama <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input @error('name') is-invalid @enderror">
                    @error('name')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input @error('email') is-invalid @enderror">
                    @error('email')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input @error('phone') is-invalid @enderror">
                    @error('phone')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Role <span class="text-red-500">*</span></label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror">
                        <option value="super_admin" {{ old('role', $user->role)==='super_admin' ? 'selected' : '' }}>Super Admin</option>
                        <option value="admin" {{ old('role', $user->role)==='admin' ? 'selected' : '' }}>Admin</option>
                        <option value="user" {{ old('role', $user->role)==='user' ? 'selected' : '' }}>User</option>
                    </select>
                    @error('role')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Gudang</label>
                    <select name="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror">
                        <option value="">- Tidak Terikat Gudang -</option>
                        @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $user->warehouse_id)==$warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                    @error('warehouse_id')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>

            @if($user->id !== auth()->id())
            <div class="mt-4 flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                    {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                    class="form-checkbox">
                <label for="is_active" class="form-label mb-0">User Aktif</label>
            </div>
            @else
            <input type="hidden" name="is_active" value="1">
            <p class="text-xs text-gray-400 mt-4">Anda tidak dapat menonaktifkan akun sendiri.</p>
            @endif
        </div>
    </div>

    <div class="flex items-center justify-end gap-2">
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </div>
</form>

{{-- Reset Password --}}
<div class="card mt-5">
    <div class="card-body">
        <h3 class="page-title text-base mb-1">Reset Password</h3>
        <p class="text-sm text-gray-500 mb-4">Atur ulang password user ini secara manual.</p>

        <form method="POST" action="{{ route('users.reset-password', $user) }}" class="flex flex-wrap items-end gap-3">
            @csrf
            <div class="w-64">
                <label class="form-label">Password Baru <span class="text-red-500">*</span></label>
                <input type="password" name="new_password" class="form-input @error('new_password') is-invalid @enderror" autocomplete="new-password">
                <p class="text-xs text-gray-400 mt-1">Minimal 8 karakter, kombinasi huruf dan angka.</p>
                @error('new_password')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="btn btn-danger">Reset Password</button>
        </form>
    </div>
</div>
@endsection

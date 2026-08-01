{{-- users/create.blade.php --}}
@extends('layouts.app')
@section('title','Tambah User')
@section('breadcrumb')
<a href="{{ route('superadmin.users.index') }}" class="text-gray-500 hover:text-gray-700">User</a>
<span class="text-gray-400 mx-1">/</span>
<span class="text-gray-700 font-medium">Tambah</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-bold text-gray-900">Tambah User</h1>
    <a href="{{ route('superadmin.users.index') }}" class="btn btn-secondary">Kembali</a>
</div>

<form method="POST" action="{{ route('users.store') }}" class="space-y-5">
    @csrf

    <div class="card">
        <div class="card-body">
            <h3 class="page-title text-base mb-4">Informasi User</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Nama <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="form-input @error('name') is-invalid @enderror">
                    @error('name')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="form-input @error('email') is-invalid @enderror">
                    @error('email')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="form-input @error('phone') is-invalid @enderror">
                    @error('phone')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Role <span class="text-red-500">*</span></label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror">
                        <option value="">- Pilih Role -</option>
                        <option value="super_admin" {{ old('role')==='super_admin' ? 'selected' : '' }}>Super Admin
                        </option>
                        <option value="admin" {{ old('role')==='admin' ? 'selected' : '' }}>Admin</option>
                        <option value="user" {{ old('role')==='user' ? 'selected' : '' }}>User</option>
                    </select>
                    @error('role')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Gudang</label>
                    <select name="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror">
                        <option value="">- Tidak Terikat Gudang -</option>
                        @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}"
                            {{ old('warehouse_id')==$warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                    @error('warehouse_id')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h3 class="page-title text-base mb-4">Password</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" class="form-input @error('password') is-invalid @enderror"
                        autocomplete="new-password">
                    <p class="text-xs text-gray-400 mt-1">Minimal 8 karakter, kombinasi huruf dan angka.</p>
                    @error('password')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-4 flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                    {{ old('is_active', true) ? 'checked' : '' }} class="form-checkbox">
                <label for="is_active" class="form-label mb-0">User Aktif</label>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-2">
        <a href="{{ route('superadmin.users.index') }}" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan User</button>
    </div>
</form>
@endsection
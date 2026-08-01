@extends('layouts.admin')
@section('title', 'Ajukan User Baru')
@section('content')

<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-900">Ajukan User Baru</h1>
    <p class="text-sm text-gray-500">Isi data user, pengajuan akan direview oleh Superadmin sebelum akun dibuat.</p>
</div>

@if ($errors->any())
<div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm">
    <ul class="list-disc list-inside">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card max-w-2xl">
    <div class="card-body p-5">
        <form action="{{ route('admin.user-requests.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" required
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">-- Pilih Role --</option>
                        <option value="staff" @selected(old('role')==='staff' )>Staff</option>
                        <option value="warehouse_keeper" @selected(old('role')==='warehouse_keeper' )>Warehouse Keeper
                        </option>
                        <option value="admin" @selected(old('role')==='admin' )>Admin</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Divisi / Departemen</label>
                    <input type="text" name="division" value="{{ old('division') }}"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Pengajuan</label>
                    <textarea name="reason" rows="4" required
                        placeholder="Contoh: pegawai baru divisi gudang, menggantikan staff resign, dll"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">{{ old('reason') }}</textarea>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.user-requests.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
            </div>
        </form>
    </div>
</div>
@endsection
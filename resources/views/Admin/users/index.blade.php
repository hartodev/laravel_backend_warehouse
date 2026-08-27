@extends('layouts.admin')
@section('title', 'Pengguna')
@section('content')

<div class="admin-page-head">
    <h2>Pengguna</h2>
    <a href="{{ route('admin.users.create') }}" class="btn-primary ripple"><i class="lucide-plus"></i> Tambah User</a>
</div>

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif

<form method="GET" class="admin-filter-bar">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / email..." class="admin-input" style="max-width:240px;">
    <select name="role" class="admin-select" style="max-width:160px;">
        <option value="">Semua Role</option>
        <option value="super_admin" @selected(request('role')==='super_admin')>Super Admin</option>
        <option value="admin" @selected(request('role')==='admin')>Admin</option>
        <option value="user" @selected(request('role')==='user')>User</option>
    </select>
    <select name="is_active" class="admin-select" style="max-width:160px;">
        <option value="">Semua Status</option>
        <option value="1" @selected(request('is_active')==='1')>Aktif</option>
        <option value="0" @selected(request('is_active')==='0')>Nonaktif</option>
    </select>
    <button class="btn-outline">Filter</button>
</form>

<div class="admin-card admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Telepon</th>
                <th>Status</th>
                <th class="cell-actions">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td class="cell-muted">{{ $user->email }}</td>
                <td class="cell-muted">{{ ucwords(str_replace('_', ' ', $user->role)) }}</td>
                <td class="cell-muted">{{ $user->profile->phone ?? '-' }}</td>
                <td>
                    @if($user->is_active)
                    <span class="admin-badge admin-badge-success">Aktif</span>
                    @else
                    <span class="admin-badge admin-badge-muted">Nonaktif</span>
                    @endif
                </td>
                <td class="cell-actions">
                    <a href="{{ route('admin.users.show', $user) }}" class="admin-link">Detail</a>
                    <a href="{{ route('admin.users.edit', $user) }}" class="admin-link">Edit</a>
                    @if($user->id !== auth()->id())
                    <form action="{{ route('admin.users.toggle-active', $user) }}" method="POST" style="display:inline;">
                        @csrf @method('PATCH')
                        <button type="submit" class="admin-link" style="background:none;border:none;cursor:pointer;">{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                    </form>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus user ini?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="admin-link text-danger" style="background:none;border:none;cursor:pointer;">Hapus</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="cell-empty">Belum ada pengguna.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-pagination">{{ $users->appends(request()->query())->links() }}</div>
@endsection

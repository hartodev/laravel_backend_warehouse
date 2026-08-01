{{-- users/index.blade.php --}}
@extends('layouts.app')
@section('title','Users')
@section('breadcrumb')<span class="font-medium text-gray-700 dark:text-gray-200">Users</span>@endsection

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="page-title">Manajemen User</h1>
        <p class="text-sm text-gray-500">{{ $users->total() }} user terdaftar</p>
    </div>
    <a href="{{ route('superadmin.users.create') }}" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah User
    </a>
</div>

<div class="card mb-5">
    <form method="GET" class="card-body">
        <div class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-48">
                <label class="form-label">Cari</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nama, email, telepon..." class="form-input pl-9">
                </div>
            </div>
            <div class="w-40">
                <label class="form-label">Role</label>
                <select name="role" class="form-select">
                    <option value="">Semua Role</option>
                    <option value="super_admin" {{ request('role')==='super_admin'?'selected':'' }}>Super Admin</option>
                    <option value="admin" {{ request('role')==='admin'?'selected':'' }}>Admin</option>
                    <option value="user" {{ request('role')==='user'?'selected':'' }}>User</option>
                </select>
            </div>
            <div class="w-48">
                <label class="form-label">Gudang</label>
                <select name="warehouse_id" class="form-select">
                    <option value="">Semua Gudang</option>
                    @foreach($warehouses as $w)
                    <option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>
                        {{ $w->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-36">
                <label class="form-label">Status</label>
                <select name="is_active" class="form-select">
                    <option value="">Semua</option>
                    <option value="1" {{ request('is_active')==='1'?'selected':'' }}>Aktif</option>
                    <option value="0" {{ request('is_active')==='0'?'selected':'' }}>Nonaktif</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('superadmin.users.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-wrap border-0 rounded-xl">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Role</th>
                    <th>Gudang</th>
                    <th>Telepon</th>
                    <th>Terakhir Login</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $i => $user)
                <tr>
                    <td class="text-gray-400 text-xs">{{ $users->firstItem() + $i }}</td>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                style="background: linear-gradient(135deg, #6366f1, #8b5cf6)">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <a href="{{ route('superadmin.users.show', $user) }}"
                                    class="font-medium text-indigo-600 hover:underline truncate block">{{ $user->name }}</a>
                                <p class="text-xs text-gray-400 truncate">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        @php $roleColors = ['super_admin'=>'badge-danger','admin'=>'badge-brand','user'=>'badge-info'];
                        @endphp
                        <span class="badge {{ $roleColors[$user->role] ?? 'badge-gray' }}">
                            {{ str_replace('_',' ',ucfirst($user->role)) }}
                        </span>
                    </td>
                    <td class="text-sm text-gray-600 dark:text-gray-300">{{ $user->warehouse->name ?? '—' }}</td>
                    <td class="text-sm text-gray-600 dark:text-gray-300">{{ $user->phone ?? '—' }}</td>
                    <td class="text-xs text-gray-400">
                        {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() : '—' }}
                    </td>
                    <td><span
                            class="badge {{ $user->is_active ? 'badge-success' : 'badge-gray' }}">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    </td>
                    <td class="text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('superadmin.users.show', $user) }}" class="btn btn-secondary btn-xs">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            <a href="{{ route('superadmin.users.edit', $user) }}" class="btn btn-secondary btn-xs">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('superadmin.users.toggle-active', $user) }}"
                                class="inline">
                                @csrf @method('PATCH')
                                <button class="btn btn-xs {{ $user->is_active ? 'btn-warning' : 'btn-success' }}"
                                    title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                </button>
                            </form>
                            <button
                                onclick="document.getElementById('del-user-{{ $user->id }}').classList.remove('hidden')"
                                class="btn btn-danger btn-xs">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                            @endif
                        </div>
                        <div id="del-user-{{ $user->id }}" class="hidden modal-backdrop">
                            <div class="modal-box p-6">
                                <div
                                    class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <h3 class="text-center font-bold text-gray-900 dark:text-white mb-1">Hapus User?</h3>
                                <p class="text-center text-sm text-gray-500 mb-5"><strong>{{ $user->name }}</strong>
                                    akan dihapus permanen.</p>
                                <div class="flex gap-3">
                                    <button
                                        onclick="document.getElementById('del-user-{{ $user->id }}').classList.add('hidden')"
                                        class="btn btn-secondary flex-1 justify-center">Batal</button>
                                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="flex-1">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-danger w-full justify-center">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-16 text-gray-400 text-sm">Belum ada user</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="card-body border-t border-gray-100 dark:border-slate-700">{{ $users->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
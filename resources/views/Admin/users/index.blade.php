@extends('layouts.admin')
@section('title', 'Pengajuan User')
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Pengajuan User</h1>
        <p class="text-sm text-gray-500">Ajukan pembuatan akun user baru untuk disetujui Superadmin.</p>
    </div>
    <a href="{{ route('admin.user-requests.create') }}" class="btn btn-primary">+ Ajukan User Baru</a>
</div>

@if(session('success'))
<div class="mb-4 p-3 rounded-lg bg-green-50 text-green-700 text-sm">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm">{{ session('error') }}</div>
@endif

{{-- Filter status --}}
<form method="GET" class="card mb-4 p-4 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs text-gray-500 mb-1">Status</label>
        <select name="status" onchange="this.form.submit()"
            class="rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
            <option value="">Semua</option>
            <option value="pending" @selected(request('status')==='pending' )>Pending</option>
            <option value="approved" @selected(request('status')==='approved' )>Disetujui</option>
            <option value="rejected" @selected(request('status')==='rejected' )>Ditolak</option>
        </select>
    </div>
</form>

<div class="card">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Nama</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Role</th>
                    <th class="px-4 py-3 text-left">Diajukan</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($userRequests as $req)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $req->name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $req->email }}</td>
                    <td class="px-4 py-3 capitalize">{{ str_replace('_', ' ', $req->role) }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $req->created_at->format('d M Y') }}</td>
                    <td class="px-4 py-3">
                        <span class="badge
                            @if($req->status === 'approved') badge-success
                            @elseif($req->status === 'rejected') badge-danger
                            @else badge-warning
                            @endif">
                            {{ ucfirst($req->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('admin.user-requests.show', $req) }}"
                                class="text-primary-700 hover:underline text-xs">Detail</a>
                            @if($req->status === 'pending')
                            <form action="{{ route('admin.user-requests.destroy', $req) }}" method="POST"
                                onsubmit="return confirm('Batalkan pengajuan untuk {{ $req->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline text-xs">Batalkan</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">Belum ada pengajuan user</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($userRequests, 'links'))
    <div class="p-4">{{ $userRequests->appends(request()->query())->links() }}</div>
    @endif
</div>
@endsection
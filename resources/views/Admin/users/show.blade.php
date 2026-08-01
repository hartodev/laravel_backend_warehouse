@extends('layouts.admin')
@section('title', 'Detail Pengajuan User')
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">{{ $userRequest->name }}</h1>
        <p class="text-sm text-gray-500">Diajukan {{ $userRequest->created_at->format('d M Y, H:i') }}</p>
    </div>
    <span class="badge
        @if($userRequest->status === 'approved') badge-success
        @elseif($userRequest->status === 'rejected') badge-danger
        @else badge-warning
        @endif">
        {{ ucfirst($userRequest->status) }}
    </span>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
    <div class="card p-4">
        <p class="text-xs text-gray-500 mb-1">Email</p>
        <p class="text-sm font-semibold text-gray-900">{{ $userRequest->email }}</p>
    </div>
    <div class="card p-4">
        <p class="text-xs text-gray-500 mb-1">No. Telepon</p>
        <p class="text-sm font-semibold text-gray-900">{{ $userRequest->phone ?? '-' }}</p>
    </div>
    <div class="card p-4">
        <p class="text-xs text-gray-500 mb-1">Role</p>
        <p class="text-sm font-semibold text-gray-900 capitalize">{{ str_replace('_', ' ', $userRequest->role) }}</p>
    </div>
    <div class="card p-4">
        <p class="text-xs text-gray-500 mb-1">Divisi</p>
        <p class="text-sm font-semibold text-gray-900">{{ $userRequest->division ?? '-' }}</p>
    </div>
</div>

<div class="card p-4 mb-5">
    <p class="text-xs text-gray-500 mb-1">Alasan Pengajuan</p>
    <p class="text-sm text-gray-700">{{ $userRequest->reason }}</p>
</div>

@if($userRequest->status === 'rejected' && $userRequest->rejection_reason)
<div class="card p-4 mb-5 border border-red-100 bg-red-50">
    <p class="text-xs text-red-500 mb-1">Alasan Penolakan dari Superadmin</p>
    <p class="text-sm text-red-700">{{ $userRequest->rejection_reason }}</p>
</div>
@endif

@if($userRequest->status === 'approved')
<div class="card p-4 mb-5 border border-green-100 bg-green-50">
    <p class="text-sm text-green-700">✓ Akun user sudah dibuat oleh Superadmin.</p>
</div>
@endif

<div class="flex justify-between">
    <a href="{{ route('admin.user-requests.index') }}" class="btn btn-secondary">← Kembali</a>

    @if($userRequest->status === 'pending')
    <form action="{{ route('admin.user-requests.destroy', $userRequest) }}" method="POST"
        onsubmit="return confirm('Batalkan pengajuan ini?')">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-danger">Batalkan Pengajuan</button>
    </form>
    @endif
</div>
@endsection
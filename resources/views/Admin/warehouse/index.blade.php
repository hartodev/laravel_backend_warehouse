@extends('layouts.admin')
@section('title', 'Gudang')
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Gudang</h1>
        <p class="text-sm text-gray-500">Kelola daftar gudang penyimpanan.</p>
    </div>
    <a href="{{ route('admin.warehouses.create') }}" class="btn btn-primary">+ Tambah Gudang</a>
</div>

@if(session('success'))
<div class="mb-4 p-3 rounded-lg bg-green-50 text-green-700 text-sm">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($warehouses as $w)
    <div class="card p-4">
        <div class="flex items-start justify-between mb-2">
            <div>
                <p class="font-semibold text-gray-900">{{ $w->name }}</p>
                <p class="text-xs text-gray-400 font-mono">{{ $w->code ?? '-' }}</p>
            </div>
            <span class="badge {{ $w->is_active ? 'badge-success' : 'badge-secondary' }}">
                {{ $w->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
        </div>
        <p class="text-sm text-gray-500 mb-1">📍 {{ $w->address ?? '-' }}</p>
        <p class="text-sm text-gray-500 mb-3">☎ {{ $w->phone ?? '-' }}</p>
        <div class="flex justify-between items-center pt-3 border-t border-gray-100">
            <a href="{{ route('admin.warehouses.show', $w) }}"
                class="text-xs text-primary-700 hover:underline">Detail</a>
            <div class="flex gap-3">
                <a href="{{ route('admin.warehouses.edit', $w) }}"
                    class="text-xs text-gray-500 hover:underline">Edit</a>
                <form action="{{ route('admin.warehouses.destroy', $w) }}" method="POST"
                    onsubmit="return confirm('Hapus gudang {{ $w->name }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs text-red-600 hover:underline">Hapus</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full card p-8 text-center text-gray-400">Belum ada gudang</div>
    @endforelse
</div>

@if(method_exists($warehouses, 'links'))
<div class="mt-4">{{ $warehouses->links() }}</div>
@endif
@endsection
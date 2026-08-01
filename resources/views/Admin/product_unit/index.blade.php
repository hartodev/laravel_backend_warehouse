@extends('layouts.admin')
@section('title', 'Satuan Produk')
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Satuan Produk</h1>
        <p class="text-sm text-gray-500">Kelola unit satuan untuk produk.</p>
    </div>
    <a href="{{ route('admin.product-units.create') }}" class="btn btn-primary">+ Tambah Satuan</a>
</div>

@if(session('success'))
<div class="mb-4 p-3 rounded-lg bg-green-50 text-green-700 text-sm">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Nama</th>
                    <th class="px-4 py-3 text-left">Simbol</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($productUnits as $unit)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $unit->name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $unit->symbol ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <span class="badge {{ $unit->is_active ? 'badge-success' : 'badge-secondary' }}">
                            {{ $unit->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('admin.product-units.edit', $unit) }}"
                                class="text-primary-700 hover:underline text-xs">Edit</a>
                            <form action="{{ route('admin.product-units.destroy', $unit) }}" method="POST"
                                onsubmit="return confirm('Hapus satuan {{ $unit->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline text-xs">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-gray-400">Belum ada satuan produk</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($productUnits, 'links'))
    <div class="p-4">{{ $productUnits->links() }}</div>
    @endif
</div>
@endsection
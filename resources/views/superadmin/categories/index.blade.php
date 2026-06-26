{{-- categories/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Kategori')
@section('breadcrumb')<span class="text-gray-700 font-medium">Kategori</span>@endsection

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Manajemen Kategori</h1>
            <p class="text-sm text-gray-500">{{ $categories->total() }} kategori</p>
        </div>
        <a href="{{ route('categories.create') }}" class="btn-primary btn">+ Tambah Kategori</a>
    </div>

    <div class="card mb-5">
        <form method="GET" class="card-body flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-48"><label class="form-label">Cari</label><input type="text" name="search"
                    value="{{ request('search') }}" placeholder="Nama kategori..." class="form-input"></div>
            <div class="w-40"><label class="form-label">Status</label>
                <select name="is_active" class="form-select">
                    <option value="">Semua</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <button type="submit" class="btn-primary btn">Filter</button>
            <a href="{{ route('categories.index') }}" class="btn-secondary btn">Reset</a>
        </form>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Slug</th>
                    <th>Parent</th>
                    <th>Icon</th>
                    <th class="text-right">Produk</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($categories as $i => $cat)
                    <tr>
                        <td class="text-gray-400 text-xs">{{ $categories->firstItem() + $i }}</td>
                        <td>
                            <div class="flex items-center gap-3">
                                @if ($cat->image)
                                    <img src="{{ Storage::url($cat->image) }}" class="w-8 h-8 rounded-lg object-cover">
                                @endif
                                <a href="{{ route('categories.show', $cat) }}"
                                    class="font-medium text-primary-700 hover:underline">{{ $cat->name }}</a>
                            </div>
                        </td>
                        <td class="font-mono text-xs text-gray-500">{{ $cat->slug }}</td>
                        <td>
                            @if ($cat->parent)
                                {{ $cat->parent->name }}
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td>{{ $cat->icon ?? '—' }}</td>
                        <td class="text-right font-semibold">{{ number_format($cat->products_count) }}</td>
                        <td><x-status-badge :status="$cat->is_active ? '1' : '0'" /></td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('categories.edit', $cat) }}"
                                    class="btn btn-secondary btn-sm">Edit</a>
                                <button
                                    onclick="document.getElementById('del-cat-{{ $cat->id }}').classList.remove('hidden')"
                                    class="btn btn-danger btn-sm">Hapus</button>
                            </div>
                            <x-confirm-modal :id="'del-cat-' . $cat->id" title="Hapus Kategori?" :message="'Kategori \'' . $cat->name . '\' akan dihapus.'" :action="route('categories.destroy', $cat)"
                                method="DELETE" confirm-label="Hapus" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-12 text-gray-400">Belum ada kategori</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $categories->links() }}</div>
@endsection

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

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
            {{ session('error') }}
        </div>
    @endif

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
                                <button type="button"
                                    data-url="{{ route('categories.destroy', $cat) }}"
                                    data-name="{{ $cat->name }}"
                                    onclick="openDeleteModal(this)"
                                    class="btn btn-danger btn-sm">
                                    Hapus
                                </button>
                            </div>
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


    {{-- Modal Konfirmasi Hapus --}}
    <div id="delete-modal"
         class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
         onclick="if(event.target===this) closeDeleteModal()">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 p-6">
            <div class="flex items-start gap-4 mb-5">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900">Hapus Kategori?</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Kategori <strong id="delete-name" class="text-gray-800"></strong>
                        akan dihapus permanen dan tidak bisa dikembalikan.
                    </p>
                </div>
            </div>
            <form id="delete-form" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeDeleteModal()"
                        class="btn btn-secondary btn-sm px-4">Batal</button>
                    <button type="submit"
                        class="btn btn-danger btn-sm px-4">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    function openDeleteModal(btn) {
        document.getElementById('delete-name').textContent = btn.dataset.name;
        document.getElementById('delete-form').action = btn.dataset.url;
        document.getElementById('delete-modal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('delete-modal').classList.add('hidden');
    }
</script>
@endpush

@extends('layouts.app')
@section('title', 'Landing - FAQ')
@section('breadcrumb')
<span class="text-gray-700 font-medium">FAQ</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="page-title">Landing Page - FAQ</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $faqs->total() }} FAQ terdaftar</p>
    </div>
    <a href="{{ route('landing-faqs.create') }}" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah FAQ
    </a>
</div>

@if (session('success'))
<div class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-100 text-emerald-800 text-sm">
    {{ session('success') }}
</div>
@endif

{{-- Filter --}}
<div class="card mb-5">
    <form method="GET" class="card-body flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[220px]">
            <label class="form-label">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pertanyaan..."
                class="form-input">
        </div>
        <div class="w-44">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('landing-faqs.index') }}" class="btn btn-secondary">Reset</a>
    </form>
</div>

{{-- Table --}}
<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th class="w-12">#</th>
                <th>Pertanyaan</th>
                <th class="w-24">Urutan</th>
                <th class="w-32">Status</th>
                <th class="text-right w-40">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($faqs as $i => $faq)
            <tr>
                <td class="text-gray-400">{{ $faqs->firstItem() + $i }}</td>
                <td>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-400 to-violet-500 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                            {{ strtoupper(substr($faq->question, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ $faq->question }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ Str::limit($faq->answer, 60) }}</p>
                        </div>
                    </div>
                </td>
                <td class="text-gray-500">{{ $faq->order }}</td>
                <td>
                    <span class="badge {{ $faq->is_active ? 'badge-success' : 'badge-danger' }}">
                        {{ $faq->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td class="text-right">
                    <div class="inline-flex items-center gap-1.5">
                        <a href="{{ route('landing-faqs.edit', $faq) }}"
                            class="w-8 h-8 inline-flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition-colors"
                            title="Edit">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                        <form action="{{ route('landing-faqs.destroy', $faq) }}" method="POST"
                            onsubmit="return confirm('Hapus FAQ ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-8 h-8 inline-flex items-center justify-center rounded-lg border border-red-200 text-red-500 hover:bg-red-50 transition-colors"
                                title="Hapus">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-12 text-gray-400">Belum ada FAQ.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $faqs->appends(request()->query())->links() }}</div>
@endsection
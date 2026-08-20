@extends('layouts.app')
@section('title', 'Landing - Stats')
@section('breadcrumb')
<span class="text-gray-700 font-medium">Stats</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="page-title">Landing Page - Stats</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $stats->total() }} data stat</p>
    </div>
    <a href="{{ route('landing-stats.create') }}" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Stat
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
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari label..."
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
        <a href="{{ route('landing-stats.index') }}" class="btn btn-secondary">Reset</a>
    </form>
</div>

{{-- Table --}}
<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th class="w-12">#</th>
                <th>Label</th>
                <th class="w-28">Tipe</th>
                <th class="w-32">Nilai</th>
                <th class="w-24">Bar %</th>
                <th class="w-24">Urutan</th>
                <th class="w-32">Status</th>
                <th class="text-right w-40">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($stats as $i => $stat)
            <tr>
                <td class="text-gray-400">{{ $stats->firstItem() + $i }}</td>
                <td>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-400 to-violet-500 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                            {{ strtoupper(substr($stat->label, 0, 1)) }}
                        </div>
                        <p class="font-medium text-gray-900">{{ $stat->label }}</p>
                    </div>
                </td>
                <td>
                    @if ($stat->is_static)
                    <span class="badge badge-gray">Statis</span>
                    @else
                    <span class="badge badge-info">Counter</span>
                    @endif
                </td>
                <td class="text-gray-700">
                    @if ($stat->is_static)
                    {{ $stat->static_value }}
                    @else
                    {{ rtrim(rtrim(number_format($stat->target, $stat->decimal_places), '0'), '.') }}{{ $stat->suffix }}
                    @endif
                </td>
                <td class="text-gray-500">{{ $stat->bar_percentage }}%</td>
                <td class="text-gray-500">{{ $stat->order }}</td>
                <td>
                    <span class="badge {{ $stat->is_active ? 'badge-success' : 'badge-danger' }}">
                        {{ $stat->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td class="text-right">
                    <div class="inline-flex items-center gap-1.5">
                        <a href="{{ route('landing-stats.edit', $stat) }}"
                            class="w-8 h-8 inline-flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition-colors"
                            title="Edit">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                        <form action="{{ route('landing-stats.destroy', $stat) }}" method="POST"
                            onsubmit="return confirm('Hapus stat ini?');">
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
                <td colspan="8" class="text-center py-12 text-gray-400">Belum ada data stat.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $stats->appends(request()->query())->links() }}</div>
@endsection
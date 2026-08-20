@extends('layouts.superadmin')

@section('title', 'Landing - Solusi (Bento Grid)')

@section('breadcrumb')
    <a href="{{ route('superadmin.landing-solutions.index') }}">Landing Page</a>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round"
            stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <span>Solusi (Bento Grid)</span>
@endsection

@section('content')
<div class="p-6 space-y-5">

    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">Landing Page — Solusi (Bento Grid)</h1>
            <p class="text-sm text-gray-500 mt-0.5">12 kartu fitur di section "Semua Yang Anda Butuhkan Dalam Satu Platform".</p>
        </div>
        <a href="{{ route('superadmin.landing-solutions.create') }}" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Kartu
        </a>
    </div>

    <div class="card">
        <div class="table-wrap" style="border:none">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Urutan</th>
                        <th>Icon</th>
                        <th>Judul</th>
                        <th>Ukuran</th>
                        <th>Visual</th>
                        <th>Aktif</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($solutions as $solution)
                        <tr>
                            <td>{{ $solution->order }}</td>
                            <td><code class="text-xs">{{ $solution->icon }}</code></td>
                            <td class="font-medium text-gray-900 dark:text-white">{{ $solution->title }}</td>
                            <td><span class="badge badge-info">{{ strtoupper($solution->size) }}</span></td>
                            <td class="text-xs">
                                @if ($solution->visual_type === 'inventory')
                                    Inventory ({{ $solution->inventory_items_count }} baris)
                                @elseif ($solution->visual_type === 'chart')
                                    Chart
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if ($solution->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <a href="{{ route('superadmin.landing-solutions.edit', $solution) }}" class="btn btn-xs btn-secondary">Edit</a>
                                <form action="{{ route('superadmin.landing-solutions.destroy', $solution) }}" method="POST"
                                      class="inline" onsubmit="return confirm('Hapus kartu solusi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-400">Belum ada kartu solusi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($solutions->hasPages())
            <div class="card-footer">
                {{ $solutions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

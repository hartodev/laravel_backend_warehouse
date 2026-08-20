@extends('layouts.superadmin')

@section('title', 'Landing - Workflow')

@section('breadcrumb')
    <a href="{{ route('superadmin.landing-workflow-steps.index') }}">Landing Page</a>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round"
            stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <span>Workflow</span>
@endsection

@section('content')
<div class="p-6 space-y-5">

    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">Landing Page — Workflow</h1>
            <p class="text-sm text-gray-500 mt-0.5">Langkah-langkah di section "Alur Kerja yang Simpel & Efisien".</p>
        </div>
        <a href="{{ route('superadmin.landing-workflow-steps.create') }}" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Langkah
        </a>
    </div>

    <div class="card">
        <div class="table-wrap" style="border:none">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Urutan</th>
                        <th>Icon</th>
                        <th>Judul</th>
                        <th>Warna</th>
                        <th>Aktif</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($steps as $index => $step)
                        <tr>
                            <td>{{ str_pad($loop->iteration + ($steps->currentPage() - 1) * $steps->perPage(), 2, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $step->order }}</td>
                            <td><code class="text-xs">{{ $step->icon }}</code></td>
                            <td class="font-medium text-gray-900 dark:text-white">{{ $step->title }}</td>
                            <td><span class="badge badge-info">{{ $step->color }}</span></td>
                            <td>
                                @if ($step->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <a href="{{ route('superadmin.landing-workflow-steps.edit', $step) }}" class="btn btn-xs btn-secondary">Edit</a>
                                <form action="{{ route('superadmin.landing-workflow-steps.destroy', $step) }}" method="POST"
                                      class="inline" onsubmit="return confirm('Hapus langkah ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-400">Belum ada langkah workflow.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($steps->hasPages())
            <div class="card-footer">
                {{ $steps->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

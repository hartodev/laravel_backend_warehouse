@extends('layouts.superadmin')

@section('title', 'Landing - Dashboard Stats')

@section('breadcrumb')
    <a href="{{ route('superadmin.landing-dashboard-stats.index') }}">Landing Page</a>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round"
            stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <span>Dashboard — Stat Cards</span>
@endsection

@section('content')
<div class="p-6 space-y-5">

    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">Landing Page — Dashboard Stat Cards</h1>
            <p class="text-sm text-gray-500 mt-0.5">4 kartu angka di preview dashboard (Total Produk, Transaksi, dll).</p>
        </div>
        <a href="{{ route('superadmin.landing-dashboard-stats.create') }}" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Stat
        </a>
    </div>

    <div class="card">
        <div class="table-wrap" style="border:none">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Urutan</th>
                        <th>Icon</th>
                        <th>Label</th>
                        <th>Value</th>
                        <th>Trend</th>
                        <th>Warna</th>
                        <th>Aktif</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stats as $stat)
                        <tr>
                            <td>{{ $stat->order }}</td>
                            <td><code class="text-xs">{{ $stat->icon }}</code></td>
                            <td class="font-medium text-gray-900 dark:text-white">{{ $stat->label }}</td>
                            <td>{{ $stat->value }}</td>
                            <td class="text-xs">
                                {{ $stat->trend_direction === 'up' ? '↑' : '↓' }} {{ $stat->trend_text }}
                            </td>
                            <td><span class="badge badge-info">{{ $stat->color }}</span></td>
                            <td>
                                @if ($stat->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <a href="{{ route('superadmin.landing-dashboard-stats.edit', $stat) }}" class="btn btn-xs btn-secondary">Edit</a>
                                <form action="{{ route('superadmin.landing-dashboard-stats.destroy', $stat) }}" method="POST"
                                      class="inline" onsubmit="return confirm('Hapus stat ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-gray-400">Belum ada stat card.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($stats->hasPages())
            <div class="card-footer">
                {{ $stats->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

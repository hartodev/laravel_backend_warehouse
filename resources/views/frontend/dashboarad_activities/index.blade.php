@extends('layouts.superadmin')

@section('title', 'Landing - Dashboard Activities')

@section('breadcrumb')
    <a href="{{ route('superadmin.landing-dashboard-activities.index') }}">Landing Page</a>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round"
            stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <span>Dashboard — Recent Activity</span>
@endsection

@section('content')
<div class="p-6 space-y-5">

    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">Landing Page — Recent Activity</h1>
            <p class="text-sm text-gray-500 mt-0.5">Daftar aktivitas terbaru di preview dashboard.</p>
        </div>
        <a href="{{ route('superadmin.landing-dashboard-activities.create') }}" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Aktivitas
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
                        <th>Waktu</th>
                        <th>Nilai</th>
                        <th>Aktif</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activities as $activity)
                        <tr>
                            <td>{{ $activity->order }}</td>
                            <td><code class="text-xs">{{ $activity->icon }}</code></td>
                            <td class="font-medium text-gray-900 dark:text-white">{{ $activity->title }}</td>
                            <td class="text-xs text-gray-500">{{ $activity->time_text }}</td>
                            <td><span class="badge badge-info">{{ $activity->value_text }}</span></td>
                            <td>
                                @if ($activity->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <a href="{{ route('superadmin.landing-dashboard-activities.edit', $activity) }}" class="btn btn-xs btn-secondary">Edit</a>
                                <form action="{{ route('superadmin.landing-dashboard-activities.destroy', $activity) }}" method="POST"
                                      class="inline" onsubmit="return confirm('Hapus aktivitas ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-400">Belum ada aktivitas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($activities->hasPages())
            <div class="card-footer">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

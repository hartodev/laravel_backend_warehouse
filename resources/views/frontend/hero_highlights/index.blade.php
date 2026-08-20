@extends('layouts.superadmin')

@section('title', 'Landing - Hero Highlights')

@section('breadcrumb')
    <a href="{{ route('superadmin.landing-hero.edit') }}">Landing Page</a>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round"
            stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <a href="{{ route('superadmin.landing-hero.edit') }}">Hero</a>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round"
            stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <span>Kartu Highlight</span>
@endsection

@section('content')
<div class="p-6 space-y-5">

    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">Landing Page — Kartu Highlight (Home)</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kartu melayang di sisi kanan hero (floating cards).</p>
        </div>
        <a href="{{ route('superadmin.landing-hero-highlights.create') }}" class="btn btn-primary">
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
                        <th>Subtitle</th>
                        <th>Warna</th>
                        <th>Aktif</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($highlights as $highlight)
                        <tr>
                            <td>{{ $highlight->order }}</td>
                            <td><code class="text-xs">{{ $highlight->icon }}</code></td>
                            <td class="font-medium text-gray-900 dark:text-white">{{ $highlight->title }}</td>
                            <td>{{ $highlight->subtitle }}</td>
                            <td><span class="badge badge-info">{{ $highlight->color }}</span></td>
                            <td>
                                @if ($highlight->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <a href="{{ route('superadmin.landing-hero-highlights.edit', $highlight) }}" class="btn btn-xs btn-secondary">Edit</a>
                                <form action="{{ route('superadmin.landing-hero-highlights.destroy', $highlight) }}" method="POST"
                                      class="inline" onsubmit="return confirm('Hapus kartu ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-400">Belum ada kartu highlight.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($highlights->hasPages())
            <div class="card-footer">
                {{ $highlights->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

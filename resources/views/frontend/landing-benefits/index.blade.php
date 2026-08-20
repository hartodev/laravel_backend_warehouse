@extends('layouts.superadmin')

@section('title', 'Landing - Benefits')

@section('breadcrumb')
    <a href="{{ route('superadmin.landing-benefits.index') }}">Landing Page</a>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round"
            stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <span>Benefits</span>
@endsection

@section('content')
<div class="p-6 space-y-5">

    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">Landing Page — Benefits</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kartu 40% / 70% / 99.9% / 24 Jam di section "Hasil Nyata Untuk Bisnis Anda".</p>
        </div>
        <a href="{{ route('superadmin.landing-benefits.create') }}" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Benefit
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
                        <th>Nilai</th>
                        <th>Featured</th>
                        <th>Aktif</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($benefits as $benefit)
                        <tr>
                            <td>{{ $benefit->order }}</td>
                            <td><code class="text-xs">{{ $benefit->icon }}</code></td>
                            <td class="font-medium text-gray-900 dark:text-white">{{ $benefit->title }}</td>
                            <td>
                                @if ($benefit->is_static)
                                    {{ $benefit->static_value }}
                                @else
                                    {{ rtrim(rtrim(number_format($benefit->target, $benefit->decimal_places), '0'), '.') }}{{ $benefit->suffix }}
                                @endif
                            </td>
                            <td>
                                @if ($benefit->is_featured)
                                    <span class="badge badge-purple">Featured</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($benefit->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <a href="{{ route('superadmin.landing-benefits.edit', $benefit) }}" class="btn btn-xs btn-secondary">Edit</a>
                                <form action="{{ route('superadmin.landing-benefits.destroy', $benefit) }}" method="POST"
                                      class="inline" onsubmit="return confirm('Hapus benefit ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-400">Belum ada data benefit.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($benefits->hasPages())
            <div class="card-footer">
                {{ $benefits->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

{{-- Sesuaikan @extends & @section dengan layout admin yang sudah kamu pakai untuk Categories/Services --}}
@extends('layouts.admin')

@section('title', 'Landing - Stats')

@section('content')
<div class="section-body">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Landing Page - Stats</h4>
            <a href="{{ route('admin.landing-stats.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Stat
            </a>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Urutan</th>
                            <th>Label</th>
                            <th>Tipe</th>
                            <th>Nilai</th>
                            <th>Bar %</th>
                            <th>Aktif</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stats as $stat)
                            <tr>
                                <td>{{ $stat->order }}</td>
                                <td>{{ $stat->label }}</td>
                                <td>
                                    @if ($stat->is_static)
                                        <span class="badge badge-secondary">Statis</span>
                                    @else
                                        <span class="badge badge-info">Counter</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($stat->is_static)
                                        {{ $stat->static_value }}
                                    @else
                                        {{ rtrim(rtrim(number_format($stat->target, $stat->decimal_places), '0'), '.') }}{{ $stat->suffix }}
                                    @endif
                                </td>
                                <td>{{ $stat->bar_percentage }}%</td>
                                <td>
                                    @if ($stat->is_active)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-danger">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('admin.landing-stats.edit', $stat) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.landing-stats.destroy', $stat) }}" method="POST"
                                          class="d-inline" onsubmit="return confirm('Hapus stat ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Belum ada data stat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $stats->links() }}
        </div>
    </div>
</div>
@endsection

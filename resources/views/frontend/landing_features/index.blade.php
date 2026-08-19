@extends('layouts.admin')

@section('title', 'Landing - Features')

@section('content')
    <div class="section-body">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Landing Page - Features</h4>
                <a href="{{ route('admin.landing-features.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Fitur
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
                                <th>Icon</th>
                                <th>Judul</th>
                                <th>Warna</th>
                                <th>Aktif</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($features as $feature)
                                <tr>
                                    <td>{{ $feature->order }}</td>
                                    <td><code>{{ $feature->icon }}</code></td>
                                    <td>{{ $feature->title }}</td>
                                    <td><span class="badge badge-{{ $feature->color }}">{{ $feature->color }}</span></td>
                                    <td>
                                        @if ($feature->is_active)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('admin.landing-features.edit', $feature) }}"
                                            class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.landing-features.destroy', $feature) }}"
                                            method="POST" class="d-inline" onsubmit="return confirm('Hapus fitur ini?');">
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
                                    <td colspan="6" class="text-center">Belum ada fitur.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $features->links() }}
            </div>
        </div>
    </div>
@endsection

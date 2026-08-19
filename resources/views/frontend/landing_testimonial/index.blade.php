@extends('layouts.admin')

@section('title', 'Landing - Testimonials')

@section('content')
<div class="section-body">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Landing Page - Testimonials</h4>
            <a href="{{ route('admin.landing-testimonials.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Testimoni
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
                            <th>Nama</th>
                            <th>Role</th>
                            <th>Rating</th>
                            <th>Featured</th>
                            <th>Aktif</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($testimonials as $t)
                            <tr>
                                <td>{{ $t->order }}</td>
                                <td>{{ $t->name }}</td>
                                <td>{{ $t->role }}</td>
                                <td>{{ str_repeat('★', $t->rating) }}{{ str_repeat('☆', 5 - $t->rating) }}</td>
                                <td>
                                    @if ($t->is_featured)
                                        <span class="badge badge-primary">Featured</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if ($t->is_active)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-danger">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('admin.landing-testimonials.edit', $t) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.landing-testimonials.destroy', $t) }}" method="POST"
                                          class="d-inline" onsubmit="return confirm('Hapus testimoni ini?');">
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
                                <td colspan="7" class="text-center">Belum ada testimoni.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $testimonials->links() }}
        </div>
    </div>
</div>
@endsection

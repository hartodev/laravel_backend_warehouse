@extends('layouts.admin')

@section('title', 'Landing - FAQ')

@section('content')
<div class="section-body">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Landing Page - FAQ</h4>
            <a href="{{ route('admin.landing-faqs.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah FAQ
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
                            <th>Pertanyaan</th>
                            <th>Aktif</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($faqs as $faq)
                            <tr>
                                <td>{{ $faq->order }}</td>
                                <td>{{ $faq->question }}</td>
                                <td>
                                    @if ($faq->is_active)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-danger">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('admin.landing-faqs.edit', $faq) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.landing-faqs.destroy', $faq) }}" method="POST"
                                          class="d-inline" onsubmit="return confirm('Hapus FAQ ini?');">
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
                                <td colspan="4" class="text-center">Belum ada FAQ.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $faqs->links() }}
        </div>
    </div>
</div>
@endsection

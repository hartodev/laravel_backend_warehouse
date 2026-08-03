@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
    <div class="container-fluid px-4 py-4">

        {{-- Header --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-semibold mb-1">Edit Kategori</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item"><a href="{{ route('superadmin.categories.index') }}">Kategori</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('superadmin.categories.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="row g-4">

            {{-- Form Utama --}}
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold">Informasi Kategori</h6>
                    </div>
                    <div class="card-body p-4">
                        <form id="form_edit_category" action="{{ route('superadmin.categories.update', $category) }}"
                            method="POST" enctype="multipart/form-data"> @csrf
                            @method('PUT')

                            {{-- Nama --}}
                            <div class="mb-3">
                                <label for="name" class="form-label fw-medium">
                                    Nama Kategori <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="name" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $category->name) }}" placeholder="cth. Elektronik" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Slug --}}
                            <div class="mb-3">
                                <label for="slug" class="form-label fw-medium">Slug</label>
                                <input type="text" id="slug" name="slug"
                                    class="form-control @error('slug') is-invalid @enderror"
                                    value="{{ old('slug', $category->slug) }}"
                                    placeholder="auto-generate dari nama jika dikosongkan">
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Kosongkan untuk generate otomatis dari nama.</div>
                            </div>

                            {{-- Parent Kategori --}}
                            <div class="mb-3">
                                <label for="parent_id" class="form-label fw-medium">Kategori Induk</label>
                                <select id="parent_id" name="parent_id"
                                    class="form-select @error('parent_id') is-invalid @enderror">
                                    <option value="">— Tidak ada (kategori utama) —</option>
                                    @foreach ($parents as $parent)
                                        <option value="{{ $parent->id }}"
                                            {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Icon --}}
                            <div class="mb-3">
                                <label for="icon" class="form-label fw-medium">Icon</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i id="icon-preview" class="{{ old('icon', $category->icon ?? 'bi bi-tag') }}"></i>
                                    </span>
                                    <input type="text" id="icon" name="icon"
                                        class="form-control @error('icon') is-invalid @enderror"
                                        value="{{ old('icon', $category->icon) }}" placeholder="cth. bi bi-laptop">
                                    @error('icon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">Gunakan class Bootstrap Icons, cth: <code>bi bi-laptop</code></div>
                            </div>

                            {{-- Status --}}
                            <div class="mb-4">
                                <label class="form-label fw-medium d-block">Status</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                        value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Aktif</label>
                                </div>
                            </div>

                            {{-- Tombol --}}
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                                </button>
                                <a href="{{ route('superadmin.categories.index') }}"
                                    class="btn btn-outline-secondary px-4">
                                    Batal
                                </a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

            {{-- Sidebar: Gambar + Info --}}
            <div class="col-lg-4">

                {{-- Upload Gambar --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold">Gambar Kategori</h6>
                    </div>
                    <div class="card-body p-4">
                        {{-- Preview gambar saat ini --}}
                        <div id="image-wrapper" class="mb-3 text-center">
                            @if ($category->image)
                                <img id="image-preview" src="{{ asset('storage/' . $category->image) }}"
                                    alt="{{ $category->name }}" class="img-fluid rounded"
                                    style="max-height: 160px; object-fit: cover; width: 100%;">
                            @else
                                <div id="image-placeholder"
                                    class="d-flex align-items-center justify-content-center bg-light rounded"
                                    style="height: 160px;">
                                    <div class="text-center text-muted">
                                        <i class="bi bi-image fs-1"></i>
                                        <p class="small mb-0 mt-1">Belum ada gambar</p>
                                    </div>
                                </div>
                                <img id="image-preview" src="" alt="Preview" class="img-fluid rounded d-none"
                                    style="max-height: 160px; object-fit: cover; width: 100%;">
                            @endif
                        </div>

                        <label for="image" class="form-label fw-medium">Ganti Gambar</label>
                        <input type="file" id="image" name="image" form="{{ 'form_edit_category' }}"
                            class="form-control @error('image') is-invalid @enderror"
                            accept="image/jpg,image/jpeg,image/png,image/webp" onchange="previewImage(this)">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">JPG, PNG, WEBP. Maks 2 MB.</div>
                    </div>
                </div>

                {{-- Info Kategori --}}
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold">Info</h6>
                    </div>
                    <div class="card-body p-4">
                        <dl class="row mb-0 small">
                            <dt class="col-6 text-muted">ID</dt>
                            <dd class="col-6">{{ $category->id }}</dd>

                            <dt class="col-6 text-muted">Produk</dt>
                            <dd class="col-6">{{ $category->products_count ?? $category->products()->count() }}</dd>

                            <dt class="col-6 text-muted">Sub-kategori</dt>
                            <dd class="col-6">{{ $category->children()->count() }}</dd>

                            <dt class="col-6 text-muted">Dibuat</dt>
                            <dd class="col-6">{{ $category->created_at->format('d M Y') }}</dd>

                            <dt class="col-6 text-muted">Diupdate</dt>
                            <dd class="col-6">{{ $category->updated_at->format('d M Y') }}</dd>
                        </dl>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Live icon preview
        document.getElementById('icon').addEventListener('input', function() {
            const preview = document.getElementById('icon-preview');
            preview.className = this.value || 'bi bi-tag';
        });

        // Image preview before upload
        function previewImage(input) {
            if (!input.files || !input.files[0]) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('image-preview');
                const placeholder = document.getElementById('image-placeholder');

                preview.src = e.target.result;
                preview.classList.remove('d-none');

                if (placeholder) placeholder.classList.add('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        }
    </script>
@endpush

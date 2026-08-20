@extends('layouts.superadmin')

@section('title', 'Edit Header — ' . $label)

@section('breadcrumb')
    <a href="{{ route('superadmin.landing-section-headers.index') }}">Header Section</a>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round"
            stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <span>{{ $label }}</span>
@endsection

@section('content')
<div class="p-6 max-w-2xl">
    <h1 class="page-title mb-4">Edit Header — {{ $label }}</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('superadmin.landing-section-headers.update', $key) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="form-label">Badge</label>
                    <input type="text" name="badge" class="form-input" value="{{ old('badge', $header->badge) }}" required>
                    @error('badge') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">Judul (normal)</label>
                        <input type="text" name="title_normal" class="form-input" value="{{ old('title_normal', $header->title_normal) }}" required>
                    </div>
                    <div>
                        <label class="form-label">Judul (gradient/highlight)</label>
                        <input type="text" name="title_gradient" class="form-input" value="{{ old('title_gradient', $header->title_gradient) }}" required>
                    </div>
                </div>

                <div>
                    <label class="form-label">Subtitle</label>
                    <textarea name="subtitle" rows="3" class="form-textarea" required>{{ old('subtitle', $header->subtitle) }}</textarea>
                    @error('subtitle') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                @if ($key === 'contact')
                    <hr class="my-2">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Tombol CTA</p>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="form-label">Tombol Primary — Teks</label>
                            <input type="text" name="button_primary_text" class="form-input"
                                   value="{{ old('button_primary_text', $header->button_primary_text) }}">
                        </div>
                        <div>
                            <label class="form-label">Tombol Primary — URL</label>
                            <input type="text" name="button_primary_url" class="form-input"
                                   value="{{ old('button_primary_url', $header->button_primary_url) }}">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="form-label">Tombol Secondary — Teks</label>
                            <input type="text" name="button_secondary_text" class="form-input"
                                   value="{{ old('button_secondary_text', $header->button_secondary_text) }}">
                        </div>
                        <div>
                            <label class="form-label">Tombol Secondary — URL</label>
                            <input type="text" name="button_secondary_url" class="form-input"
                                   value="{{ old('button_secondary_url', $header->button_secondary_url) }}">
                        </div>
                    </div>
                @endif

                <div class="flex gap-2 pt-2">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('superadmin.landing-section-headers.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

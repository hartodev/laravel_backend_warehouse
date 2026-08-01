@php $c = $category ?? null; @endphp

<div class="admin-form-grid">
    <div>
        <label class="admin-label">Nama Kategori *</label>
        <input type="text" name="name" value="{{ old('name', $c?->name) }}" required
            class="admin-input @error('name') is-invalid @enderror">
        @error('name') <p class="admin-input-error">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="admin-label">Kode</label>
        <input type="text" name="code" value="{{ old('code', $c?->code) }}"
            class="admin-input @error('code') is-invalid @enderror">
        @error('code') <p class="admin-input-error">{{ $message }}</p> @enderror
    </div>
    <div class="span-2">
        <label class="admin-label">Deskripsi</label>
        <textarea name="description" rows="3"
            class="admin-textarea">{{ old('description', $c?->description) }}</textarea>
    </div>
    <div class="span-2">
        <label class="admin-checkbox-label">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $c?->is_active ?? true))>
            Kategori aktif
        </label>
    </div>
</div>

<div class="admin-form-actions">
    <button class="btn-primary ripple">Simpan</button>
    <a href="{{ route('admin.categories.index') }}" class="btn-ghost">Batal</a>
</div>
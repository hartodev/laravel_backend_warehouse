@csrf

@if (isset($feature))
    @method('PUT')
@endif

<div class="form-group">
    <label>Judul</label>
    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
        value="{{ old('title', $feature->title ?? '') }}" required>
    @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label>Deskripsi</label>
    <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $feature->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-6 form-group">
        <label>Icon (nama icon Lucide)</label>
        <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror"
            value="{{ old('icon', $feature->icon ?? '') }}" placeholder="Contoh: zap, shield-check, smartphone"
            required>
        <small class="form-text text-muted">Lihat daftar nama icon di <a href="https://lucide.dev/icons"
                target="_blank">lucide.dev/icons</a></small>
        @error('icon')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 form-group">
        <label>Warna</label>
        <select name="color" class="form-control @error('color') is-invalid @enderror" required>
            @foreach ($colors as $color)
                <option value="{{ $color }}"
                    {{ old('color', $feature->color ?? '') == $color ? 'selected' : '' }}>
                    {{ ucfirst($color) }}
                </option>
            @endforeach
        </select>
        @error('color')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="form-group">
    <label>Urutan</label>
    <input type="number" min="0" name="order" class="form-control @error('order') is-invalid @enderror"
        value="{{ old('order', $feature->order ?? 0) }}">
    @error('order')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <div class="custom-control custom-switch">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" id="is_active" value="1" class="custom-control-input"
            {{ old('is_active', $feature->is_active ?? true) ? 'checked' : '' }}>
        <label class="custom-control-label" for="is_active">Aktif (tampil di landing page)</label>
    </div>
</div>

<button type="submit" class="btn btn-primary">Simpan</button>
<a href="{{ route('admin.landing-features.index') }}" class="btn btn-secondary">Batal</a>

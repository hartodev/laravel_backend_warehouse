@csrf

@if (isset($testimonial))
    @method('PUT')
@endif

<div class="row">
    <div class="col-md-6 form-group">
        <label>Nama</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $testimonial->name ?? '') }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 form-group">
        <label>Role / Jabatan & Perusahaan</label>
        <input type="text" name="role" class="form-control @error('role') is-invalid @enderror"
            value="{{ old('role', $testimonial->role ?? '') }}"
            placeholder="Contoh: Warehouse Manager, PT Mitra Logistik" required>
        @error('role')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="form-group">
    <label>Quote / Isi Testimoni</label>
    <textarea name="quote" rows="3" class="form-control @error('quote') is-invalid @enderror" required>{{ old('quote', $testimonial->quote ?? '') }}</textarea>
    @error('quote')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-3 form-group">
        <label>Rating</label>
        <select name="rating" class="form-control @error('rating') is-invalid @enderror" required>
            @for ($i = 5; $i >= 1; $i--)
                <option value="{{ $i }}"
                    {{ old('rating', $testimonial->rating ?? 5) == $i ? 'selected' : '' }}>
                    {{ $i }} bintang
                </option>
            @endfor
        </select>
        @error('rating')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3 form-group">
        <label>Inisial Avatar</label>
        <input type="text" name="initials" maxlength="5"
            class="form-control @error('initials') is-invalid @enderror"
            value="{{ old('initials', $testimonial->initials ?? '') }}" placeholder="Contoh: AS" required>
        @error('initials')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3 form-group">
        <label>Warna Avatar</label>
        <select name="avatar_color" class="form-control @error('avatar_color') is-invalid @enderror" required>
            @foreach ($colors as $color)
                <option value="{{ $color }}"
                    {{ old('avatar_color', $testimonial->avatar_color ?? '') == $color ? 'selected' : '' }}>
                    {{ ucfirst($color) }}
                </option>
            @endforeach
        </select>
        @error('avatar_color')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3 form-group">
        <label>Urutan</label>
        <input type="number" min="0" name="order" class="form-control @error('order') is-invalid @enderror"
            value="{{ old('order', $testimonial->order ?? 0) }}">
        @error('order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="form-group">
    <div class="custom-control custom-switch">
        <input type="hidden" name="is_featured" value="0">
        <input type="checkbox" name="is_featured" id="is_featured" value="1" class="custom-control-input"
            {{ old('is_featured', $testimonial->is_featured ?? false) ? 'checked' : '' }}>
        <label class="custom-control-label" for="is_featured">Featured (kartu disorot/ditonjolkan)</label>
    </div>
</div>

<div class="form-group">
    <div class="custom-control custom-switch">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" id="is_active" value="1" class="custom-control-input"
            {{ old('is_active', $testimonial->is_active ?? true) ? 'checked' : '' }}>
        <label class="custom-control-label" for="is_active">Aktif (tampil di landing page)</label>
    </div>
</div>

<button type="submit" class="btn btn-primary">Simpan</button>
<a href="{{ route('admin.landing-testimonials.index') }}" class="btn btn-secondary">Batal</a>

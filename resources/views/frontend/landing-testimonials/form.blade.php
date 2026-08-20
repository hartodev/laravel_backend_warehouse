@csrf

@if (isset($testimonial))
@method('PUT')
@endif

<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="form-label">Nama</label>
            <input type="text" name="name" class="form-input" value="{{ old('name', $testimonial->name ?? '') }}"
                required>
            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">Role / Jabatan & Perusahaan</label>
            <input type="text" name="role" class="form-input" value="{{ old('role', $testimonial->role ?? '') }}"
                placeholder="Contoh: Warehouse Manager, PT Mitra Logistik" required>
            @error('role') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="form-label">Quote / Isi Testimoni</label>
        <textarea name="quote" rows="3" class="form-textarea"
            required>{{ old('quote', $testimonial->quote ?? '') }}</textarea>
        @error('quote') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="form-label">Rating</label>
            <select name="rating" class="form-select" required>
                @for ($i = 5; $i >= 1; $i--)
                <option value="{{ $i }}" {{ old('rating', $testimonial->rating ?? 5) == $i ? 'selected' : '' }}>
                    {{ $i }} bintang
                </option>
                @endfor
            </select>
            @error('rating') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">Inisial Avatar</label>
            <input type="text" name="initials" maxlength="5" class="form-input"
                value="{{ old('initials', $testimonial->initials ?? '') }}" placeholder="Contoh: AS" required>
            @error('initials') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">Warna Avatar</label>
            <select name="avatar_color" class="form-select" required>
                @foreach ($colors as $color)
                <option value="{{ $color }}"
                    {{ old('avatar_color', $testimonial->avatar_color ?? '') == $color ? 'selected' : '' }}>
                    {{ ucfirst($color) }}
                </option>
                @endforeach
            </select>
            @error('avatar_color') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">Urutan</label>
            <input type="number" min="0" name="order" class="form-input"
                value="{{ old('order', $testimonial->order ?? 0) }}">
            @error('order') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="flex items-center gap-2 text-sm text-gray-700">
            <input type="hidden" name="is_featured" value="0">
            <input type="checkbox" name="is_featured" value="1"
                {{ old('is_featured', $testimonial->is_featured ?? false) ? 'checked' : '' }}>
            Featured (kartu disorot/ditonjolkan)
        </label>
    </div>

    <div>
        <label class="flex items-center gap-2 text-sm text-gray-700">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1"
                {{ old('is_active', $testimonial->is_active ?? true) ? 'checked' : '' }}>
            Aktif (tampil di landing page)
        </label>
    </div>
</div>

<div class="card-body border-t mt-4 flex justify-end gap-2">
    <a href="{{ route('landing-testimonials.index') }}" class="btn-secondary btn">Batal</a>
    <button type="submit" class="btn-primary btn">Simpan</button>
</div>
@csrf

@if (isset($highlight))
    @method('PUT')
@endif

<div class="space-y-4">
    <div>
        <label class="form-label">Judul</label>
        <input type="text" name="title" class="form-input" value="{{ old('title', $highlight->title ?? '') }}"
               placeholder="Contoh: +125 Barang Masuk" required>
        @error('title') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="form-label">Subtitle</label>
        <input type="text" name="subtitle" class="form-input" value="{{ old('subtitle', $highlight->subtitle ?? '') }}"
               placeholder="Contoh: Hari ini · 14:30" required>
        @error('subtitle') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="form-label">Icon (nama icon Lucide)</label>
            <input type="text" name="icon" class="form-input" value="{{ old('icon', $highlight->icon ?? '') }}"
                   placeholder="Contoh: trending-up" required>
            <p class="text-xs text-gray-400 mt-1">Lihat di <a href="https://lucide.dev/icons" target="_blank" class="text-indigo-600 hover:underline">lucide.dev/icons</a></p>
            @error('icon') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="form-label">Warna</label>
            <select name="color" class="form-select" required>
                @foreach ($colors as $color)
                    <option value="{{ $color }}" {{ old('color', $highlight->color ?? '') == $color ? 'selected' : '' }}>
                        {{ ucfirst($color) }}
                    </option>
                @endforeach
            </select>
            @error('color') <div class="form-error">{{ $message }}</div> @enderror
        </div>
    </div>

    <div>
        <label class="form-label">Urutan</label>
        <input type="number" min="0" name="order" class="form-input" value="{{ old('order', $highlight->order ?? 0) }}">
        @error('order') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="flex items-center gap-2">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" id="is_active" value="1"
               {{ old('is_active', $highlight->is_active ?? true) ? 'checked' : '' }} class="rounded border-gray-300">
        <label for="is_active" class="form-label" style="margin:0">Aktif (tampil di landing page)</label>
    </div>
</div>

<div class="flex gap-2 pt-2">
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="{{ route('superadmin.landing-hero-highlights.index') }}" class="btn btn-secondary">Batal</a>
</div>

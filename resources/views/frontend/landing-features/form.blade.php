@csrf

@if (isset($feature))
@method('PUT')
@endif

<div class="space-y-4">
    <div>
        <label class="form-label">Judul</label>
        <input type="text" name="title" class="form-input" value="{{ old('title', $feature->title ?? '') }}" required>
        @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="form-label">Deskripsi</label>
        <textarea name="description" rows="3" class="form-textarea"
            required>{{ old('description', $feature->description ?? '') }}</textarea>
        @error('description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="form-label">Icon (nama icon Lucide)</label>
            <input type="text" name="icon" class="form-input" value="{{ old('icon', $feature->icon ?? '') }}"
                placeholder="Contoh: zap, shield-check, smartphone" required>
            <p class="text-xs text-gray-400 mt-1">
                Lihat daftar nama icon di
                <a href="https://lucide.dev/icons" target="_blank"
                    class="text-primary-600 hover:underline">lucide.dev/icons</a>
            </p>
            @error('icon') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">Warna</label>
            <select name="color" class="form-select" required>
                @foreach ($colors as $color)
                <option value="{{ $color }}" {{ old('color', $feature->color ?? '') == $color ? 'selected' : '' }}>
                    {{ ucfirst($color) }}
                </option>
                @endforeach
            </select>
            @error('color') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="w-36">
        <label class="form-label">Urutan</label>
        <input type="number" min="0" name="order" class="form-input" value="{{ old('order', $feature->order ?? 0) }}">
        @error('order') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="flex items-center gap-2 text-sm text-gray-700">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1"
                {{ old('is_active', $feature->is_active ?? true) ? 'checked' : '' }}>
            Aktif (tampil di landing page)
        </label>
    </div>
</div>

<div class="card-body border-t mt-4 flex justify-end gap-2">
    <a href="{{ route('landing-features.index') }}" class="btn-secondary btn">Batal</a>
    <button type="submit" class="btn-primary btn">Simpan</button>
</div>
@csrf

@if (isset($stat))
    @method('PUT')
@endif

<div class="space-y-4">
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="form-label">Label</label>
            <input type="text" name="label" class="form-input" value="{{ old('label', $stat->label ?? '') }}"
                   placeholder="Contoh: Total Produk" required>
            @error('label') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="form-label">Value</label>
            <input type="text" name="value" class="form-input" value="{{ old('value', $stat->value ?? '') }}"
                   placeholder="Contoh: 12,847 atau Rp 4.2M" required>
            @error('value') <div class="form-error">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="form-label">Teks Trend</label>
            <input type="text" name="trend_text" class="form-input" value="{{ old('trend_text', $stat->trend_text ?? '') }}"
                   placeholder="Contoh: +12.5% bulan ini" required>
            @error('trend_text') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="form-label">Arah Trend</label>
            <select name="trend_direction" class="form-select" required>
                <option value="up" {{ old('trend_direction', $stat->trend_direction ?? '') == 'up' ? 'selected' : '' }}>Naik (up)</option>
                <option value="down" {{ old('trend_direction', $stat->trend_direction ?? '') == 'down' ? 'selected' : '' }}>Turun (down)</option>
            </select>
            @error('trend_direction') <div class="form-error">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="form-label">Icon (nama icon Lucide)</label>
            <input type="text" name="icon" class="form-input" value="{{ old('icon', $stat->icon ?? '') }}"
                   placeholder="Contoh: package, activity" required>
            @error('icon') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="form-label">Warna</label>
            <select name="color" class="form-select" required>
                @foreach ($colors as $color)
                    <option value="{{ $color }}" {{ old('color', $stat->color ?? '') == $color ? 'selected' : '' }}>
                        {{ ucfirst($color) }}
                    </option>
                @endforeach
            </select>
            @error('color') <div class="form-error">{{ $message }}</div> @enderror
        </div>
    </div>

    <div>
        <label class="form-label">Urutan</label>
        <input type="number" min="0" name="order" class="form-input" value="{{ old('order', $stat->order ?? 0) }}">
        @error('order') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="flex items-center gap-2">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" id="is_active" value="1"
               {{ old('is_active', $stat->is_active ?? true) ? 'checked' : '' }} class="rounded border-gray-300">
        <label for="is_active" class="form-label" style="margin:0">Aktif (tampil di landing page)</label>
    </div>
</div>

<div class="flex gap-2 pt-2">
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="{{ route('superadmin.landing-dashboard-stats.index') }}" class="btn btn-secondary">Batal</a>
</div>

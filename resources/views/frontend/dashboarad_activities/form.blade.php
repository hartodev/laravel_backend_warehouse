@csrf

@if (isset($activity))
    @method('PUT')
@endif

<div class="space-y-4">
    <div>
        <label class="form-label">Judul Aktivitas</label>
        <input type="text" name="title" class="form-input" value="{{ old('title', $activity->title ?? '') }}"
               placeholder="Contoh: Barang Masuk #PO-2847" required>
        @error('title') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="form-label">Waktu</label>
        <input type="text" name="time_text" class="form-input" value="{{ old('time_text', $activity->time_text ?? '') }}"
               placeholder="Contoh: 2 menit lalu" required>
        @error('time_text') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="form-label">Icon (nama icon Lucide)</label>
            <input type="text" name="icon" class="form-input" value="{{ old('icon', $activity->icon ?? '') }}"
                   placeholder="Contoh: arrow-down-to-line" required>
            @error('icon') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="form-label">Warna Icon</label>
            <select name="color" class="form-select" required>
                @foreach ($colors as $color)
                    <option value="{{ $color }}" {{ old('color', $activity->color ?? '') == $color ? 'selected' : '' }}>
                        {{ ucfirst($color) }}
                    </option>
                @endforeach
            </select>
            @error('color') <div class="form-error">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="form-label">Teks Nilai</label>
            <input type="text" name="value_text" class="form-input" value="{{ old('value_text', $activity->value_text ?? '') }}"
                   placeholder="Contoh: +48, -24, ✓, !" required>
            @error('value_text') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="form-label">Warna Nilai</label>
            <select name="value_color" class="form-select" required>
                @foreach ($colors as $color)
                    <option value="{{ $color }}" {{ old('value_color', $activity->value_color ?? '') == $color ? 'selected' : '' }}>
                        {{ ucfirst($color) }}
                    </option>
                @endforeach
            </select>
            @error('value_color') <div class="form-error">{{ $message }}</div> @enderror
        </div>
    </div>

    <div>
        <label class="form-label">Urutan</label>
        <input type="number" min="0" name="order" class="form-input" value="{{ old('order', $activity->order ?? 0) }}">
        @error('order') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="flex items-center gap-2">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" id="is_active" value="1"
               {{ old('is_active', $activity->is_active ?? true) ? 'checked' : '' }} class="rounded border-gray-300">
        <label for="is_active" class="form-label" style="margin:0">Aktif (tampil di landing page)</label>
    </div>
</div>

<div class="flex gap-2 pt-2">
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="{{ route('superadmin.landing-dashboard-activities.index') }}" class="btn btn-secondary">Batal</a>
</div>

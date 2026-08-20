@csrf

@if (isset($stat))
@method('PUT')
@endif

<div class="space-y-4">
    <div>
        <label class="form-label">Label</label>
        <input type="text" name="label" class="form-input" value="{{ old('label', $stat->label ?? '') }}" required>
        @error('label') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="flex items-center gap-2 text-sm text-gray-700">
            <input type="hidden" name="is_static" value="0">
            <input type="checkbox" name="is_static" id="is_static" value="1"
                {{ old('is_static', $stat->is_static ?? false) ? 'checked' : '' }}
                onchange="document.getElementById('static-fields').classList.toggle('hidden', !this.checked); document.getElementById('counter-fields').classList.toggle('hidden', this.checked);">
            Nilai statis (bukan animasi counter)
        </label>
    </div>

    <div id="static-fields" class="{{ old('is_static', $stat->is_static ?? false) ? '' : 'hidden' }}">
        <label class="form-label">Nilai Statis</label>
        <input type="text" name="static_value" class="form-input"
            value="{{ old('static_value', $stat->static_value ?? '') }}" placeholder="Contoh: 24/7">
        @error('static_value') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div id="counter-fields" class="{{ old('is_static', $stat->is_static ?? false) ? 'hidden' : '' }}">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="form-label">Target Angka</label>
                <input type="number" step="0.01" name="target" class="form-input"
                    value="{{ old('target', $stat->target ?? '') }}" placeholder="Contoh: 500">
                @error('target') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="form-label">Suffix</label>
                <input type="text" name="suffix" class="form-input" value="{{ old('suffix', $stat->suffix ?? '') }}"
                    placeholder="Contoh: +, %">
                @error('suffix') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="form-label">Jumlah Desimal</label>
                <input type="number" min="0" max="3" name="decimal_places" class="form-input"
                    value="{{ old('decimal_places', $stat->decimal_places ?? 0) }}">
                @error('decimal_places') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <div>
        <label class="form-label">Bar Percentage (0-100)</label>
        <input type="number" min="0" max="100" name="bar_percentage" class="form-input"
            value="{{ old('bar_percentage', $stat->bar_percentage ?? 0) }}" required>
        @error('bar_percentage') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="w-36">
        <label class="form-label">Urutan</label>
        <input type="number" min="0" name="order" class="form-input" value="{{ old('order', $stat->order ?? 0) }}">
        @error('order') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="flex items-center gap-2 text-sm text-gray-700">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1"
                {{ old('is_active', $stat->is_active ?? true) ? 'checked' : '' }}>
            Aktif (tampil di landing page)
        </label>
    </div>
</div>

<div class="card-body border-t mt-4 flex justify-end gap-2">
    <a href="{{ route('landing-stats.index') }}" class="btn-secondary btn">Batal</a>
    <button type="submit" class="btn-primary btn">Simpan</button>
</div>
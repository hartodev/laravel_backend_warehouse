@csrf

@if (isset($benefit))
    @method('PUT')
@endif

<div class="space-y-4">
    <div>
        <label class="form-label">Judul</label>
        <input type="text" name="title" class="form-input" value="{{ old('title', $benefit->title ?? '') }}" required>
        @error('title') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="form-label">Deskripsi</label>
        <textarea name="description" rows="3" class="form-textarea" required>{{ old('description', $benefit->description ?? '') }}</textarea>
        @error('description') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="form-label">Icon (nama icon Lucide)</label>
        <input type="text" name="icon" class="form-input" value="{{ old('icon', $benefit->icon ?? '') }}"
               placeholder="Contoh: zap, shield-check, target, monitor" required>
        <p class="text-xs text-gray-400 mt-1">Lihat daftar nama icon di <a href="https://lucide.dev/icons" target="_blank" class="text-indigo-600 hover:underline">lucide.dev/icons</a></p>
        @error('icon') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="flex items-center gap-2">
        <input type="hidden" name="is_static" value="0">
        <input type="checkbox" name="is_static" id="is_static" value="1"
               {{ old('is_static', $benefit->is_static ?? false) ? 'checked' : '' }}
               class="rounded border-gray-300"
               onchange="document.getElementById('static-fields').classList.toggle('hidden', !this.checked); document.getElementById('counter-fields').classList.toggle('hidden', this.checked);">
        <label for="is_static" class="form-label" style="margin:0">Nilai statis (bukan animasi counter, contoh: "24 Jam")</label>
    </div>

    <div id="static-fields" class="{{ old('is_static', $benefit->is_static ?? false) ? '' : 'hidden' }}">
        <label class="form-label">Nilai Statis</label>
        <input type="text" name="static_value" class="form-input" value="{{ old('static_value', $benefit->static_value ?? '') }}" placeholder="Contoh: 24 Jam">
        @error('static_value') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div id="counter-fields" class="{{ old('is_static', $benefit->is_static ?? false) ? 'hidden' : '' }} grid grid-cols-3 gap-3">
        <div>
            <label class="form-label">Target Angka</label>
            <input type="number" step="0.01" name="target" class="form-input" value="{{ old('target', $benefit->target ?? '') }}" placeholder="Contoh: 40">
            @error('target') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="form-label">Suffix</label>
            <input type="text" name="suffix" class="form-input" value="{{ old('suffix', $benefit->suffix ?? '') }}" placeholder="%">
            @error('suffix') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="form-label">Jumlah Desimal</label>
            <input type="number" min="0" max="3" name="decimal_places" class="form-input" value="{{ old('decimal_places', $benefit->decimal_places ?? 0) }}">
            @error('decimal_places') <div class="form-error">{{ $message }}</div> @enderror
        </div>
    </div>

    <div>
        <label class="form-label">Bar Percentage (0-100)</label>
        <input type="number" min="0" max="100" name="bar_percentage" class="form-input"
               value="{{ old('bar_percentage', $benefit->bar_percentage ?? 0) }}" required>
        @error('bar_percentage') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="form-label">Urutan</label>
        <input type="number" min="0" name="order" class="form-input" value="{{ old('order', $benefit->order ?? 0) }}">
        @error('order') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="flex items-center gap-2">
        <input type="hidden" name="is_featured" value="0">
        <input type="checkbox" name="is_featured" id="is_featured" value="1"
               {{ old('is_featured', $benefit->is_featured ?? false) ? 'checked' : '' }} class="rounded border-gray-300">
        <label for="is_featured" class="form-label" style="margin:0">Featured (kartu disorot / background beda)</label>
    </div>

    <div class="flex items-center gap-2">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" id="is_active" value="1"
               {{ old('is_active', $benefit->is_active ?? true) ? 'checked' : '' }} class="rounded border-gray-300">
        <label for="is_active" class="form-label" style="margin:0">Aktif (tampil di landing page)</label>
    </div>
</div>

<div class="flex gap-2 pt-2">
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="{{ route('superadmin.landing-benefits.index') }}" class="btn btn-secondary">Batal</a>
</div>

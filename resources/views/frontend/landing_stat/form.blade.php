@csrf

@if (isset($stat))
    @method('PUT')
@endif

<div class="form-group">
    <label>Label</label>
    <input type="text" name="label" class="form-control @error('label') is-invalid @enderror"
           value="{{ old('label', $stat->label ?? '') }}" required>
    @error('label') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <div class="custom-control custom-switch">
        <input type="hidden" name="is_static" value="0">
        <input type="checkbox" name="is_static" id="is_static" value="1" class="custom-control-input"
               {{ old('is_static', $stat->is_static ?? false) ? 'checked' : '' }}
               onchange="document.getElementById('static-fields').classList.toggle('d-none', !this.checked); document.getElementById('counter-fields').classList.toggle('d-none', this.checked);">
        <label class="custom-control-label" for="is_static">Nilai statis (bukan animasi counter)</label>
    </div>
</div>

<div id="static-fields" class="{{ old('is_static', $stat->is_static ?? false) ? '' : 'd-none' }}">
    <div class="form-group">
        <label>Nilai Statis</label>
        <input type="text" name="static_value" class="form-control @error('static_value') is-invalid @enderror"
               value="{{ old('static_value', $stat->static_value ?? '') }}" placeholder="Contoh: 24/7">
        @error('static_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div id="counter-fields" class="{{ old('is_static', $stat->is_static ?? false) ? 'd-none' : '' }}">
    <div class="row">
        <div class="col-md-4 form-group">
            <label>Target Angka</label>
            <input type="number" step="0.01" name="target" class="form-control @error('target') is-invalid @enderror"
                   value="{{ old('target', $stat->target ?? '') }}" placeholder="Contoh: 500">
            @error('target') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4 form-group">
            <label>Suffix</label>
            <input type="text" name="suffix" class="form-control @error('suffix') is-invalid @enderror"
                   value="{{ old('suffix', $stat->suffix ?? '') }}" placeholder="Contoh: +, %">
            @error('suffix') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4 form-group">
            <label>Jumlah Desimal</label>
            <input type="number" min="0" max="3" name="decimal_places"
                   class="form-control @error('decimal_places') is-invalid @enderror"
                   value="{{ old('decimal_places', $stat->decimal_places ?? 0) }}">
            @error('decimal_places') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

<div class="form-group">
    <label>Bar Percentage (0-100)</label>
    <input type="number" min="0" max="100" name="bar_percentage"
           class="form-control @error('bar_percentage') is-invalid @enderror"
           value="{{ old('bar_percentage', $stat->bar_percentage ?? 0) }}" required>
    @error('bar_percentage') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label>Urutan</label>
    <input type="number" min="0" name="order" class="form-control @error('order') is-invalid @enderror"
           value="{{ old('order', $stat->order ?? 0) }}">
    @error('order') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <div class="custom-control custom-switch">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" id="is_active" value="1" class="custom-control-input"
               {{ old('is_active', $stat->is_active ?? true) ? 'checked' : '' }}>
        <label class="custom-control-label" for="is_active">Aktif (tampil di landing page)</label>
    </div>
</div>

<button type="submit" class="btn btn-primary">Simpan</button>
<a href="{{ route('admin.landing-stats.index') }}" class="btn btn-secondary">Batal</a>

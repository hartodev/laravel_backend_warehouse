@if ($errors->any())
<div class="admin-alert admin-alert-error">
    <div>
        @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
        @endforeach
    </div>
</div>
@endif

<div class="admin-form-grid" style="grid-template-columns:repeat(2,1fr);margin-bottom:20px;">
    <div>
        <label class="admin-label">Nama Gudang</label>
        <input type="text" name="name" value="{{ old('name', $warehouse->name ?? '') }}" required class="admin-input">
    </div>
    <div>
        <label class="admin-label">Kode</label>
        <input type="text" name="code" value="{{ old('code', $warehouse->code ?? '') }}" required class="admin-input">
    </div>
    <div style="grid-column:span 2;">
        <label class="admin-label">Lokasi</label>
        <textarea name="location" required class="admin-textarea">{{ old('location', $warehouse->location ?? '') }}</textarea>
    </div>
    <div>
        <label class="admin-label">Nama PIC</label>
        <input type="text" name="pic_name" value="{{ old('pic_name', $warehouse->pic_name ?? '') }}" class="admin-input">
    </div>
    <div>
        <label class="admin-label">Telepon PIC</label>
        <input type="text" name="pic_phone" value="{{ old('pic_phone', $warehouse->pic_phone ?? '') }}" class="admin-input">
    </div>
    <div>
        <label class="admin-label">Status</label>
        <select name="is_active" class="admin-select">
            <option value="1" @selected(old('is_active', $warehouse->is_active ?? true) == 1)>Aktif</option>
            <option value="0" @selected(old('is_active', $warehouse->is_active ?? true) == 0)>Nonaktif</option>
        </select>
    </div>
    <div>
        <label class="admin-label">Foto Gudang</label>
        <input type="file" name="photo" accept="image/*" class="admin-input">
        @isset($warehouse)
            @if($warehouse->photo)
            <p class="cell-muted" style="margin-top:6px;">Foto saat ini: <img src="{{ asset('storage/'.$warehouse->photo) }}" alt="foto gudang" style="height:40px;border-radius:6px;vertical-align:middle;margin-left:6px;"></p>
            @endif
        @endisset
    </div>
</div>

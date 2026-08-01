@php $p = $product ?? null; @endphp

<div class="admin-form-grid">
    <div>
        <label class="admin-label">Kategori *</label>
        <select name="category_id" required class="admin-select @error('category_id') is-invalid @enderror">
            <option value="">-- Pilih Kategori --</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" @selected(old('category_id', $p?->category_id) == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
        @error('category_id') <p class="admin-input-error">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="admin-label">Nama Produk *</label>
        <input type="text" name="name" value="{{ old('name', $p?->name) }}" required
               class="admin-input @error('name') is-invalid @enderror">
        @error('name') <p class="admin-input-error">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="admin-label">SKU *</label>
        <input type="text" name="sku" value="{{ old('sku', $p?->sku) }}" required
               class="admin-input @error('sku') is-invalid @enderror">
        @error('sku') <p class="admin-input-error">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="admin-label">Barcode</label>
        <input type="text" name="barcode" value="{{ old('barcode', $p?->barcode) }}"
               class="admin-input @error('barcode') is-invalid @enderror">
        @error('barcode') <p class="admin-input-error">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="admin-label">Unit Dasar *</label>
        <input type="text" name="unit" value="{{ old('unit', $p?->unit) }}" required placeholder="pcs, box, dus..."
               class="admin-input @error('unit') is-invalid @enderror">
        @error('unit') <p class="admin-input-error">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="admin-label">Stok Minimum</label>
        <input type="number" name="min_stock" min="0" value="{{ old('min_stock', $p?->min_stock ?? 0) }}" class="admin-input">
    </div>
    <div>
        <label class="admin-label">Harga Beli</label>
        <input type="number" name="purchase_price" min="0" step="0.01" value="{{ old('purchase_price', $p?->purchase_price ?? 0) }}" class="admin-input">
    </div>
    <div>
        <label class="admin-label">Harga Jual</label>
        <input type="number" name="selling_price" min="0" step="0.01" value="{{ old('selling_price', $p?->selling_price ?? 0) }}" class="admin-input">
    </div>
    <div class="span-2">
        <label class="admin-label">Deskripsi</label>
        <textarea name="description" rows="3" class="admin-textarea">{{ old('description', $p?->description) }}</textarea>
    </div>
    <div>
        <label class="admin-label">Foto Produk</label>
        <input type="file" name="photo" accept="image/*" class="admin-input" style="padding:6px 10px;">
        @error('photo') <p class="admin-input-error">{{ $message }}</p> @enderror
        @if ($p?->photo)
            <img src="{{ Storage::url($p->photo) }}" class="admin-thumb" style="margin-top:8px;">
        @endif
    </div>
    <div style="display:flex;align-items:flex-end;">
        <label class="admin-checkbox-label">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $p?->is_active ?? true))>
            Produk aktif
        </label>
    </div>
</div>

<div class="admin-form-actions">
    <button class="btn-primary ripple">Simpan</button>
    <a href="{{ route('admin.products.index') }}" class="btn-ghost">Batal</a>
</div>

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
        <label class="admin-label">Kategori</label>
        <select name="category_id" required class="admin-select">
            <option value="">— Pilih Kategori —</option>
            @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ??
                '')==$category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="admin-label">Nama Produk</label>
        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required class="admin-input">
    </div>
    <div>
        <label class="admin-label">SKU</label>
        <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}" required class="admin-input">
    </div>
    <div>
        <label class="admin-label">Barcode</label>
        <input type="text" name="barcode" value="{{ old('barcode', $product->barcode ?? '') }}" class="admin-input">
    </div>
    <div>
        <label class="admin-label">Satuan</label>
        <input type="text" name="unit" value="{{ old('unit', $product->unit ?? '') }}" required class="admin-input"
            placeholder="mis. pcs, box, kg">
    </div>
    <div>
        <label class="admin-label">Stok Minimum</label>
        <input type="number" name="min_stock" value="{{ old('min_stock', $product->min_stock ?? 0) }}" min="0"
            class="admin-input">
    </div>
    <div>
        <label class="admin-label">Harga Beli</label>
        <input type="number" step="0.01" name="purchase_price"
            value="{{ old('purchase_price', $product->purchase_price ?? 0) }}" min="0" class="admin-input">
    </div>
    <div>
        <label class="admin-label">Harga Jual</label>
        <input type="number" step="0.01" name="selling_price"
            value="{{ old('selling_price', $product->selling_price ?? 0) }}" min="0" class="admin-input">
    </div>
    <div>
        <label class="admin-label">Status</label>
        <select name="is_active" class="admin-select">
            <option value="1" @selected(old('is_active', $product->is_active ?? true) == 1)>Aktif</option>
            <option value="0" @selected(old('is_active', $product->is_active ?? true) == 0)>Nonaktif</option>
        </select>
    </div>
    <div>
        <label class="admin-label">Foto Produk</label>
        <input type="file" name="photo" accept="image/*" class="admin-input">
        @isset($product)
        @if($product->photo)
        <p class="cell-muted" style="margin-top:6px;">Foto saat ini: <img src="{{ asset('storage/'.$product->photo) }}"
                alt="foto produk" style="height:40px;border-radius:6px;vertical-align:middle;margin-left:6px;"></p>
        @endif
        @endisset
    </div>
    <div style="grid-column:span 2;">
        <label class="admin-label">Deskripsi</label>
        <textarea name="description"
            class="admin-textarea">{{ old('description', $product->description ?? '') }}</textarea>
    </div>
</div>
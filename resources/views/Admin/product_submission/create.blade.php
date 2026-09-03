@extends('layouts.admin')
@section('title', 'Ajukan Produk Baru')
@section('content')

<div class="admin-page-head">
    <div>
        <h2>Ajukan Produk Baru</h2>
        <p class="admin-page-subtitle">Pengajuan ini akan diteruskan ke Super Admin untuk disetujui.</p>
    </div>
    <a href="{{ route('admin.product-submissions.index') }}" class="btn-outline">
        <i class="lucide-arrow-left"></i> Kembali
    </a>
</div>

@if($errors->any())
<div class="admin-alert admin-alert-error">
    <i class="lucide-alert-circle"></i>
    <div>
        <strong>Periksa kembali isian Anda:</strong>
        <ul style="margin:6px 0 0; padding-left:18px;">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<form action="{{ route('admin.product-submissions.store') }}" method="POST" class="admin-form">
    @csrf

    <div class="admin-card admin-form-section">
        <h3 class="admin-form-section-title">Informasi Produk</h3>

        <div class="admin-form-row">
            <div class="admin-form-group">
                <label for="name">Nama Produk <span class="req">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                    class="admin-input" placeholder="Contoh: Kabel Listrik NYA 2.5mm" required>
            </div>
            <div class="admin-form-group">
                <label for="category_id">Kategori <span class="req">*</span></label>
                <select id="category_id" name="category_id" class="admin-select" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="admin-form-row">
            <div class="admin-form-group">
                <label for="sku">SKU <span class="hint">(opsional, otomatis jika kosong)</span></label>
                <input type="text" id="sku" name="sku" value="{{ old('sku') }}"
                    class="admin-input" placeholder="Contoh: KBL-NYA-25">
            </div>
            <div class="admin-form-group">
                <label for="barcode">Barcode <span class="hint">(opsional)</span></label>
                <input type="text" id="barcode" name="barcode" value="{{ old('barcode') }}"
                    class="admin-input" placeholder="Contoh: 8991234567890">
            </div>
            <div class="admin-form-group">
                <label for="unit">Satuan <span class="req">*</span></label>
                <input type="text" id="unit" name="unit" value="{{ old('unit') }}"
                    class="admin-input" placeholder="Contoh: pcs, roll, box" required>
            </div>
        </div>

        <div class="admin-form-group">
            <label for="description">Deskripsi <span class="hint">(opsional)</span></label>
            <textarea id="description" name="description" class="admin-input" rows="3"
                placeholder="Catatan tambahan mengenai produk...">{{ old('description') }}</textarea>
        </div>
    </div>

    <div class="admin-card admin-form-section">
        <h3 class="admin-form-section-title">Harga</h3>

        <div class="admin-form-row">
            <div class="admin-form-group">
                <label for="purchase_price">Harga Beli <span class="hint">(opsional)</span></label>
                <div class="admin-input-prefix">
                    <span>Rp</span>
                    <input type="number" id="purchase_price" name="purchase_price" value="{{ old('purchase_price') }}"
                        class="admin-input" min="0" step="1" placeholder="0">
                </div>
            </div>
            <div class="admin-form-group">
                <label for="selling_price">Harga Jual <span class="hint">(opsional)</span></label>
                <div class="admin-input-prefix">
                    <span>Rp</span>
                    <input type="number" id="selling_price" name="selling_price" value="{{ old('selling_price') }}"
                        class="admin-input" min="0" step="1" placeholder="0">
                </div>
            </div>
        </div>
    </div>

    <div class="admin-card admin-form-section">
        <h3 class="admin-form-section-title">Stok Awal <span class="hint">(opsional)</span></h3>
        <p class="admin-form-hint">Isi bagian ini jika produk ini sudah ada fisiknya dan siap dicatat sebagai stok awal saat disetujui.</p>

        <div class="admin-form-row">
            <div class="admin-form-group">
                <label for="initial_stock">Jumlah Stok Awal</label>
                <input type="number" id="initial_stock" name="initial_stock" value="{{ old('initial_stock', 0) }}"
                    class="admin-input" min="0" step="1">
            </div>
            <div class="admin-form-group">
                <label for="initial_warehouse_id">Gudang</label>
                <select id="initial_warehouse_id" name="initial_warehouse_id" class="admin-select">
                    <option value="">-- Pilih Gudang --</option>
                    @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}" @selected(old('initial_warehouse_id') == $warehouse->id)>
                        {{ $warehouse->name }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="admin-card admin-form-section admin-form-urgent">
        <label class="admin-checkbox-row">
            <input type="checkbox" name="is_urgent" value="1" @checked(old('is_urgent'))>
            <div>
                <strong>Tandai sebagai pengajuan mendesak (Urgent)</strong>
                <p class="admin-form-hint">Pengajuan urgent akan ditampilkan lebih menonjol kepada Super Admin agar dapat diprioritaskan.</p>
            </div>
        </label>
    </div>

    <div class="admin-form-actions">
        <a href="{{ route('admin.product-submissions.index') }}" class="btn-outline">Batal</a>
        <button type="submit" class="btn btn-primary">
            <i class="lucide-send"></i> Kirim Pengajuan
        </button>
    </div>
</form>

<style>
    .admin-page-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }
    .admin-page-subtitle {
        margin: 4px 0 0;
        font-size: 13px;
        color: var(--admin-text-muted, #6b7280);
    }
    .admin-form {
        display: flex;
        flex-direction: column;
        gap: 16px;
        max-width: 780px;
    }
    .admin-form-section {
        padding: 18px 20px;
    }
    .admin-form-section-title {
        margin: 0 0 14px;
        font-size: 15px;
        font-weight: 600;
    }
    .admin-form-hint {
        margin: -6px 0 12px;
        font-size: 12.5px;
        color: var(--admin-text-muted, #6b7280);
    }
    .admin-form-row {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }
    .admin-form-row:last-child {
        margin-bottom: 0;
    }
    .admin-form-group {
        flex: 1 1 220px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .admin-form-group:last-child {
        margin-bottom: 0;
    }
    .admin-form-group label {
        font-size: 13px;
        font-weight: 500;
    }
    .admin-form-group .req {
        color: #dc2626;
    }
    .admin-form-group .hint {
        font-weight: 400;
        font-size: 12px;
        color: var(--admin-text-muted, #6b7280);
    }
    .admin-input-prefix {
        display: flex;
        align-items: center;
        border: 1px solid var(--admin-border, #d1d5db);
        border-radius: 8px;
        overflow: hidden;
    }
    .admin-input-prefix span {
        padding: 0 10px;
        background: var(--admin-bg-subtle, #f3f4f6);
        font-size: 13px;
        color: var(--admin-text-muted, #6b7280);
        align-self: stretch;
        display: flex;
        align-items: center;
    }
    .admin-input-prefix input {
        border: none;
        flex: 1;
    }
    .admin-form-urgent {
        background: #fff7ed;
        border-color: #fdba74;
    }
    .admin-checkbox-row {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        cursor: pointer;
        margin: 0;
    }
    .admin-checkbox-row input[type="checkbox"] {
        margin-top: 3px;
        width: 16px;
        height: 16px;
    }
    .admin-checkbox-row p {
        margin: 2px 0 0;
    }
    .admin-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding-top: 4px;
    }
</style>
@endsection

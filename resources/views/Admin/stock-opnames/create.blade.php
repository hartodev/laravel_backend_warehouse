@extends('layouts.admin')
@section('title', 'Buat Stock Opname')
@section('content')

<div class="admin-page-head">
    <h2>Buat Stock Opname</h2>
</div>

@if ($errors->any())
<div class="admin-alert admin-alert-error">
    @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif

<form action="{{ route('admin.stock-opnames.store') }}" method="POST" id="opname-form">
    @csrf

    <div class="admin-form-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:20px;">
        <div>
            <label class="admin-label">Gudang</label>
            <select name="warehouse_id" id="warehouse_id" required class="admin-select">
                <option value="">Pilih Gudang</option>
                @foreach($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}" @selected(old('warehouse_id')==$warehouse->
                    id)>{{ $warehouse->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="admin-label">Tanggal Opname</label>
            <input type="date" name="opname_date" value="{{ old('opname_date', date('Y-m-d')) }}" required
                class="admin-input">
        </div>
        <div>
            <label class="admin-label">Scope</label>
            <select name="scope" id="scope" required class="admin-select">
                <option value="all" @selected(old('scope')==='all' )>Semua Produk</option>
                <option value="category" @selected(old('scope')==='category' )>Per Kategori</option>
                <option value="manual" @selected(old('scope')==='manual' )>Pilih Manual</option>
            </select>
        </div>
        <div id="category-wrap" class="hidden">
            <label class="admin-label">Kategori</label>
            <select name="category_id" id="category_id" class="admin-select">
                <option value="">Pilih Kategori</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id')==$category->id)>{{ $category->name }}
                </option>
                @endforeach
            </select>
        </div>
        <div style="grid-column:span 3;">
            <label class="admin-label">Catatan</label>
            <textarea name="notes" class="admin-textarea" maxlength="500">{{ old('notes') }}</textarea>
        </div>
    </div>

    <div id="manual-picker" class="hidden" style="margin-bottom:20px;">
        <label class="admin-label">Pilih Produk</label>
        <div class="admin-card admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:40px;"></th>
                        <th>Produk</th>
                        <th>SKU</th>
                        <th>Stok Saat Ini</th>
                    </tr>
                </thead>
                <tbody id="manual-products-body">
                    <tr>
                        <td colspan="4" class="cell-empty">Pilih gudang terlebih dahulu.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="preview-panel" class="hidden" style="margin-bottom:20px;">
        <label class="admin-label">Preview Produk yang Akan Diopname</label>
        <div class="admin-card admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>SKU</th>
                        <th>Stok Saat Ini</th>
                    </tr>
                </thead>
                <tbody id="preview-products-body">
                    <tr>
                        <td colspan="3" class="cell-empty">Pilih gudang terlebih dahulu.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-form-actions" style="justify-content:flex-end;">
        <a href="{{ route('admin.stock-opnames.index') }}" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary ripple">Buat Opname</button>
    </div>
</form>

<style>
.hidden {
    display: none;
}
</style>
<script>
const warehouseSelect = document.getElementById('warehouse_id');
const scopeSelect = document.getElementById('scope');
const categorySelect = document.getElementById('category_id');
const categoryWrap = document.getElementById('category-wrap');
const manualPicker = document.getElementById('manual-picker');
const previewPanel = document.getElementById('preview-panel');
const manualBody = document.getElementById('manual-products-body');
const previewBody = document.getElementById('preview-products-body');
const form = document.getElementById('opname-form');

function toggleScopeUI() {
    const scope = scopeSelect.value;
    categoryWrap.classList.toggle('hidden', scope !== 'category');
    manualPicker.classList.toggle('hidden', scope !== 'manual');
    previewPanel.classList.toggle('hidden', scope === 'manual');
    if (scope === 'manual') loadManualProducts();
    if (scope === 'all' || scope === 'category') loadPreview();
}

function loadManualProducts() {
    const warehouseId = warehouseSelect.value;
    if (!warehouseId) {
        manualBody.innerHTML = '<tr><td colspan="4" class="cell-empty">Pilih gudang terlebih dahulu.</td></tr>';
        return;
    }
    fetch(`{{ route('admin.stock-opnames.products-for-scope') }}?warehouse_id=${warehouseId}`)
        .then(r => r.json())
        .then(res => {
            const products = res.data || [];
            if (!products.length) {
                manualBody.innerHTML =
                    '<tr><td colspan="4" class="cell-empty">Tidak ada produk di gudang ini.</td></tr>';
                return;
            }
            manualBody.innerHTML = products.map(p => `
                <tr>
                    <td><input type="checkbox" name="product_ids[]" value="${p.id}"></td>
                    <td>${p.name}</td>
                    <td class="cell-mono">${p.sku}</td>
                    <td class="cell-mono">${p.stock}</td>
                </tr>
            `).join('');
        });
}

function loadPreview() {
    const warehouseId = warehouseSelect.value;
    if (!warehouseId) {
        previewBody.innerHTML = '<tr><td colspan="3" class="cell-empty">Pilih gudang terlebih dahulu.</td></tr>';
        return;
    }
    let url = `{{ route('admin.stock-opnames.products-for-scope') }}?warehouse_id=${warehouseId}`;
    if (scopeSelect.value === 'category' && categorySelect.value) {
        url += `&category_id=${categorySelect.value}`;
    }
    fetch(url)
        .then(r => r.json())
        .then(res => {
            const products = res.data || [];
            previewBody.innerHTML = products.length ?
                products.map(p =>
                    `<tr><td>${p.name}</td><td class="cell-mono">${p.sku}</td><td class="cell-mono">${p.stock}</td></tr>`
                    ).join('') :
                '<tr><td colspan="3" class="cell-empty">Tidak ada produk ditemukan.</td></tr>';
        });
}

warehouseSelect.addEventListener('change', toggleScopeUI);
scopeSelect.addEventListener('change', toggleScopeUI);
categorySelect.addEventListener('change', loadPreview);
toggleScopeUI();
</script>
@endsection
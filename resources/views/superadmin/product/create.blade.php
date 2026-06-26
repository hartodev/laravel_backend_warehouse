{{-- ============================================================
  resources/views/superadmin/products/create.blade.php
============================================================ --}}
@extends('layouts.app')
@section('title','Tambah Produk')
@section('breadcrumb')
    <a href="{{ route('products.index') }}" class="text-indigo-500 hover:underline">Produk</a>
    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-gray-700 dark:text-gray-200">Tambah</span>
@endsection

@section('content')
<form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data"
      x-data="productForm()">
@csrf

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- LEFT: Main Fields --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Info Dasar --}}
        <div class="card">
            <div class="card-header">
                <h2 class="font-semibold text-gray-900 dark:text-white">Informasi Dasar</h2>
            </div>
            <div class="card-body space-y-4">
                <div>
                    <label class="form-label">Nama Produk <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="form-input @error('name') border-red-400 @enderror"
                           placeholder="Nama produk lengkap...">
                    @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">SKU <span class="text-red-500">*</span></label>
                        <input type="text" name="sku" value="{{ old('sku') }}" required
                               class="form-input font-mono @error('sku') border-red-400 @enderror"
                               placeholder="cth. PRD-001">
                        @error('sku')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Barcode</label>
                        <input type="text" name="barcode" value="{{ old('barcode') }}"
                               class="form-input font-mono" placeholder="Scan atau ketik barcode">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Kategori <span class="text-red-500">*</span></label>
                        <select name="category_id" required class="form-select @error('category_id') border-red-400 @enderror">
                            <option value="">— Pilih Kategori —</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Supplier</label>
                        <select name="supplier_id" class="form-select">
                            <option value="">— Pilih Supplier —</option>
                            @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" rows="4" class="form-input resize-none"
                              placeholder="Deskripsi produk...">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Harga & Stok --}}
        <div class="card">
            <div class="card-header">
                <h2 class="font-semibold text-gray-900 dark:text-white">Harga & Stok</h2>
            </div>
            <div class="card-body grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Harga Beli <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-medium">Rp</span>
                        <input type="number" name="purchase_price" value="{{ old('purchase_price', 0) }}" required min="0"
                               class="form-input pl-9 @error('purchase_price') border-red-400 @enderror"
                               placeholder="0">
                    </div>
                    @error('purchase_price')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Harga Jual <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-medium">Rp</span>
                        <input type="number" name="selling_price" value="{{ old('selling_price', 0) }}" required min="0"
                               class="form-input pl-9 @error('selling_price') border-red-400 @enderror"
                               placeholder="0">
                    </div>
                    @error('selling_price')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Satuan <span class="text-red-500">*</span></label>
                    <input type="text" name="unit" value="{{ old('unit', 'pcs') }}" required
                           class="form-input" placeholder="pcs / kg / liter / box">
                    @error('unit')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Stok Minimum</label>
                    <input type="number" name="min_stock" value="{{ old('min_stock', 0) }}" min="0"
                           class="form-input">
                    <p class="text-xs text-gray-400 mt-1">Alert otomatis jika stok ≤ nilai ini</p>
                </div>
                <div>
                    <label class="form-label">Berat (gram)</label>
                    <input type="number" name="weight" value="{{ old('weight') }}" min="0" step="0.01"
                           class="form-input" placeholder="0">
                </div>
                <div>
                    <label class="form-label">Dimensi (P×L×T cm)</label>
                    <div class="grid grid-cols-3 gap-1">
                        <input type="number" name="length" value="{{ old('length') }}" min="0" step="0.1" class="form-input text-center" placeholder="P">
                        <input type="number" name="width"  value="{{ old('width') }}"  min="0" step="0.1" class="form-input text-center" placeholder="L">
                        <input type="number" name="height" value="{{ old('height') }}" min="0" step="0.1" class="form-input text-center" placeholder="T">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Image + Status --}}
    <div class="space-y-5">

        {{-- Foto Produk --}}
        <div class="card">
            <div class="card-header">
                <h2 class="font-semibold text-gray-900 dark:text-white">Foto Produk</h2>
            </div>
            <div class="card-body">
                <div class="border-2 border-dashed border-gray-200 dark:border-slate-600 rounded-xl p-4 text-center cursor-pointer hover:border-indigo-400 transition-colors"
                     @click="$refs.imgInput.click()"
                     x-on:dragover.prevent x-on:drop.prevent="handleDrop($event)">
                    <img x-show="preview" :src="preview" class="w-full h-40 object-cover rounded-lg mb-3">
                    <div x-show="!preview" class="py-6">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <p class="text-sm text-gray-400">Klik atau drag foto ke sini</p>
                        <p class="text-xs text-gray-300 mt-1">JPG, PNG, WEBP — maks 2MB</p>
                    </div>
                    <input type="file" name="image" accept="image/*" x-ref="imgInput" class="hidden"
                           @change="handleFile($event)">
                </div>
                <p x-show="preview" class="text-xs text-center text-gray-400 mt-2 cursor-pointer hover:text-red-400"
                   @click="preview = null; $refs.imgInput.value = ''">✕ Hapus foto</p>
            </div>
        </div>

        {{-- Status & Publish --}}
        <div class="card">
            <div class="card-header">
                <h2 class="font-semibold text-gray-900 dark:text-white">Status</h2>
            </div>
            <div class="card-body space-y-4">
                <label class="flex items-center gap-3 cursor-pointer">
                    <div class="relative">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="peer sr-only">
                        <div class="w-10 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 transition-colors"></div>
                        <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-4 transition-transform"></div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Produk Aktif</p>
                        <p class="text-xs text-gray-400">Produk akan tampil & bisa ditransaksikan</p>
                    </div>
                </label>

                <label class="flex items-center gap-3 cursor-pointer">
                    <div class="relative">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                               class="peer sr-only">
                        <div class="w-10 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 transition-colors"></div>
                        <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-4 transition-transform"></div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Produk Unggulan</p>
                        <p class="text-xs text-gray-400">Tampil di bagian teratas list</p>
                    </div>
                </label>
            </div>
            <div class="card-footer flex gap-2 justify-end">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Produk
                </button>
            </div>
        </div>
    </div>
</div>
</form>
@endsection

@push('scripts')
<script>
function productForm() {
    return {
        preview: null,
        handleFile(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => this.preview = e.target.result;
            reader.readAsDataURL(file);
        },
        handleDrop(e) {
            const file = e.dataTransfer.files[0];
            if (!file || !file.type.startsWith('image/')) return;
            this.$refs.imgInput.files = e.dataTransfer.files;
            const reader = new FileReader();
            reader.onload = e => this.preview = e.target.result;
            reader.readAsDataURL(file);
        }
    }
}
</script>
@endpush

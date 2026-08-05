{{-- ============================================================
  resources/views/superadmin/products/edit.blade.php
============================================================ --}}
@extends('layouts.app')
@section('title','Edit Produk')
@section('breadcrumb')
<a href="{{ route('superadmin.products.index') }}" class="text-indigo-500 hover:underline">Produk</a>
<svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
</svg>
<a href="{{ route('superadmin.products.show', $product) }}"
    class="text-indigo-500 hover:underline">{{ Str::limit($product->name, 24) }}</a>
<svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
</svg>
<span class="font-medium text-gray-700 dark:text-gray-200">Edit</span>
@endsection

@section('content')
<form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data"
    x-data="{ preview: '{{ $product->image ? Storage::url($product->image) : '' }}' }">
    @csrf @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 space-y-5">
            <div class="card">
                <div class="card-header">
                    <h2 class="font-semibold text-gray-900 dark:text-white">Informasi Dasar</h2>
                    <span class="badge badge-gray font-mono text-xs">{{ $product->sku }}</span>
                </div>
                <div class="card-body space-y-4">
                    <div>
                        <label class="form-label">Nama Produk <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                            class="form-input @error('name') border-red-400 @enderror">
                        @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">SKU <span class="text-red-500">*</span></label>
                            <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required
                                class="form-input font-mono @error('sku') border-red-400 @enderror">
                            @error('sku')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="form-label">Barcode</label>
                            <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}"
                                class="form-input font-mono">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Kategori <span class="text-red-500">*</span></label>
                            <select name="category_id" required class="form-select">
                                <option value="">— Pilih —</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Supplier</label>
                            <select name="supplier_id" class="form-select">
                                <option value="">— Pilih —</option>
                                @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}"
                                    {{ old('supplier_id', $product->supplier_id) == $sup->id ? 'selected' : '' }}>
                                    {{ $sup->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" rows="4"
                            class="form-input resize-none">{{ old('description', $product->description) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="font-semibold text-gray-900 dark:text-white">Harga & Stok</h2>
                </div>
                <div class="card-body grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Harga Beli <span class="text-red-500">*</span></label>
                        <div class="relative"><span
                                class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">Rp</span>
                            <input type="number" name="purchase_price"
                                value="{{ old('purchase_price', $product->purchase_price) }}" required min="0"
                                class="form-input pl-9">
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Harga Jual <span class="text-red-500">*</span></label>
                        <div class="relative"><span
                                class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">Rp</span>
                            <input type="number" name="selling_price"
                                value="{{ old('selling_price', $product->selling_price) }}" required min="0"
                                class="form-input pl-9">
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Satuan <span class="text-red-500">*</span></label>
                        <input type="text" name="unit" value="{{ old('unit', $product->unit) }}" required
                            class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Stok Minimum</label>
                        <input type="number" name="min_stock" value="{{ old('min_stock', $product->min_stock) }}"
                            min="0" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Berat (gram)</label>
                        <input type="number" name="weight" value="{{ old('weight', $product->weight) }}" min="0"
                            step="0.01" class="form-input">
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-5">
            <div class="card">
                <div class="card-header">
                    <h2 class="font-semibold text-gray-900 dark:text-white">Foto Produk</h2>
                </div>
                <div class="card-body">
                    <div class="border-2 border-dashed border-gray-200 dark:border-slate-600 rounded-xl p-4 text-center cursor-pointer hover:border-indigo-400 transition-colors"
                        @click="$refs.imgInput.click()">
                        <img x-show="preview" :src="preview" class="w-full h-40 object-cover rounded-lg mb-2">
                        <div x-show="!preview" class="py-4">
                            <p class="text-sm text-gray-400">Klik untuk ganti foto</p>
                        </div>
                        <input type="file" name="image" accept="image/*" x-ref="imgInput" class="hidden"
                            @change="e => { const r = new FileReader(); r.onload = e => preview = e.target.result; r.readAsDataURL(e.target.files[0]); }">
                    </div>
                    <p x-show="preview && '{{ $product->image }}'" class="text-xs text-center text-gray-400 mt-2">Upload
                        baru untuk mengganti foto saat ini</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="font-semibold text-gray-900 dark:text-white">Status</h2>
                </div>
                <div class="card-body space-y-4">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <div class="relative">
                            <input type="checkbox" name="is_active" value="1"
                                {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="peer sr-only">
                            <div
                                class="w-10 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 transition-colors">
                            </div>
                            <div
                                class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-4 transition-transform">
                            </div>
                        </div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Produk Aktif</p>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <div class="relative">
                            <input type="checkbox" name="is_featured" value="1"
                                {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}
                                class="peer sr-only">
                            <div
                                class="w-10 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 transition-colors">
                            </div>
                            <div
                                class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-4 transition-transform">
                            </div>
                        </div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Produk Unggulan</p>
                    </label>
                </div>
                <div class="card-footer flex gap-2 justify-end">
                    <a href="{{ route('superadmin.products.show', $product) }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Update Produk
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

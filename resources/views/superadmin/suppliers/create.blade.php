{{-- suppliers/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Tambah Supplier')
@section('breadcrumb')
    <a href="{{ route('suppliers.index') }}" class="text-indigo-500 hover:underline">Supplier</a>
    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
    </svg>
    <span class="font-medium text-gray-700 dark:text-gray-200">Tambah</span>
@endsection

@section('content')
    <div class="max-w-3xl">
        <form method="POST" action="{{ route('suppliers.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="space-y-5">

                {{-- Info Utama --}}
                <div class="card">
                    <div class="card-header">
                        <h2 class="font-semibold text-gray-900 dark:text-white">Informasi Supplier</h2>
                    </div>
                    <div class="card-body grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="form-label">Nama Supplier <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="form-input @error('name') border-red-400 @enderror"
                                placeholder="Nama perusahaan / individu">
                            @error('name')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="form-input @error('email') border-red-400 @enderror"
                                placeholder="supplier@email.com">
                            @error('email')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="form-input"
                                placeholder="08xx">
                        </div>
                        <div>
                            <label class="form-label">Kota</label>
                            <input type="text" name="city" value="{{ old('city') }}" class="form-input"
                                placeholder="cth. Jakarta">
                        </div>
                        <div class="col-span-2">
                            <label class="form-label">Alamat</label>
                            <textarea name="address" rows="3" class="form-input resize-none" placeholder="Alamat lengkap...">{{ old('address') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Kontak PIC --}}
                <div class="card">
                    <div class="card-header">
                        <h2 class="font-semibold text-gray-900 dark:text-white">Contact Person</h2>
                    </div>
                    <div class="card-body grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Nama Contact Person</label>
                            <input type="text" name="contact_person" value="{{ old('contact_person') }}"
                                class="form-input">
                        </div>
                        <div>
                            <label class="form-label">HP Contact Person</label>
                            <input type="text" name="contact_phone" value="{{ old('contact_phone') }}"
                                class="form-input" placeholder="08xx">
                        </div>

                        <div>
                            <label class="form label">code</label>
                            <input type="text" name="code" value="{{ old('code') }}" class="form-input"
                                placeholder="cth. SUP-001" required>
                        </div>
                    </div>
                </div>

                {{-- Info Bank --}}
                <div class="card">
                    <div class="card-header">
                        <h2 class="font-semibold text-gray-900 dark:text-white">Informasi Bank</h2>
                    </div>
                    <div class="card-body grid grid-cols-3 gap-4">
                        <div>
                            <label class="form-label">Nama Bank</label>
                            <input type="text" name="bank_name" value="{{ old('bank_name') }}" class="form-input"
                                placeholder="BCA, BNI, dll">
                        </div>
                        <div>
                            <label class="form-label">No. Rekening</label>
                            <input type="text" name="bank_account" value="{{ old('bank_account') }}"
                                class="form-input font-mono">
                        </div>
                        <div>
                            <label class="form-label">Atas Nama</label>
                            <input type="text" name="bank_holder" value="{{ old('bank_holder') }}" class="form-input">
                        </div>
                    </div>
                </div>

                {{-- Notes + Logo --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="font-semibold text-gray-900 dark:text-white">Logo</h2>
                        </div>
                        <div class="card-body" x-data="{ preview: null }">
                            <div class="border-2 border-dashed border-gray-200 dark:border-slate-600 rounded-xl p-4 text-center cursor-pointer hover:border-indigo-400 transition-colors"
                                @click="$refs.logoInput.click()">
                                <img x-show="preview" :src="preview"
                                    class="w-24 h-24 object-cover rounded-lg mx-auto mb-2">
                                <div x-show="!preview" class="py-4">
                                    <svg class="w-8 h-8 text-gray-300 mx-auto mb-1" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-xs text-gray-400">Upload logo</p>
                                </div>
                                <input type="file" name="logo" accept="image/*" x-ref="logoInput" class="hidden"
                                    @change="e => { const r=new FileReader(); r.onload=e=>preview=e.target.result; r.readAsDataURL(e.target.files[0]); }">
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h2 class="font-semibold text-gray-900 dark:text-white">Catatan & Status</h2>
                        </div>
                        <div class="card-body space-y-4">
                            <div>
                                <label class="form-label">Catatan</label>
                                <textarea name="notes" rows="4" class="form-input resize-none"
                                    placeholder="Catatan tambahan tentang supplier...">{{ old('notes') }}</textarea>
                            </div>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <div class="relative">
                                    <input type="checkbox" name="is_active" value="1"
                                        {{ old('is_active', true) ? 'checked' : '' }} class="peer sr-only">
                                    <div
                                        class="w-10 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 transition-colors">
                                    </div>
                                    <div
                                        class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-4 transition-transform">
                                    </div>
                                </div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Supplier Aktif</p>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Supplier
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

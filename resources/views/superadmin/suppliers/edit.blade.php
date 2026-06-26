{{-- suppliers/edit.blade.php --}}
@extends('layouts.app')
@section('title','Edit Supplier')
@section('breadcrumb')
    <a href="{{ route('suppliers.index') }}" class="text-gray-500 hover:text-gray-700">Supplier</a>
    <span class="text-gray-400 mx-1">/</span>
    <span class="text-gray-700 font-medium">Edit</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-bold text-gray-900">Edit Supplier</h1>
    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Kembali</a>
</div>

<form method="POST" action="{{ route('suppliers.update', $supplier) }}" enctype="multipart/form-data" class="space-y-5">
    @csrf
    @method('PUT')

    {{-- Info Dasar --}}
    <div class="card">
        <div class="card-body">
            <h3 class="page-title text-base mb-4">Informasi Dasar</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Nama Supplier <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $supplier->name) }}" class="form-input @error('name') is-invalid @enderror">
                    @error('name')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $supplier->email) }}" class="form-input @error('email') is-invalid @enderror">
                    @error('email')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}" class="form-input @error('phone') is-invalid @enderror">
                    @error('phone')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Kota</label>
                    <input type="text" name="city" value="{{ old('city', $supplier->city) }}" class="form-input @error('city') is-invalid @enderror">
                    @error('city')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" rows="2" class="form-textarea @error('address') is-invalid @enderror">{{ old('address', $supplier->address) }}</textarea>
                    @error('address')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Kontak Person --}}
    <div class="card">
        <div class="card-body">
            <h3 class="page-title text-base mb-4">Kontak Person</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Nama Kontak</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}" class="form-input @error('contact_person') is-invalid @enderror">
                    @error('contact_person')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">No. Telepon Kontak</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $supplier->contact_phone) }}" class="form-input @error('contact_phone') is-invalid @enderror">
                    @error('contact_phone')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Info Bank --}}
    <div class="card">
        <div class="card-body">
            <h3 class="page-title text-base mb-4">Informasi Bank</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="form-label">Nama Bank</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $supplier->bank_name) }}" class="form-input @error('bank_name') is-invalid @enderror">
                    @error('bank_name')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">No. Rekening</label>
                    <input type="text" name="bank_account" value="{{ old('bank_account', $supplier->bank_account) }}" class="form-input @error('bank_account') is-invalid @enderror">
                    @error('bank_account')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Nama Pemilik Rekening</label>
                    <input type="text" name="bank_holder" value="{{ old('bank_holder', $supplier->bank_holder) }}" class="form-input @error('bank_holder') is-invalid @enderror">
                    @error('bank_holder')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Logo & Catatan --}}
    <div class="card">
        <div class="card-body">
            <h3 class="page-title text-base mb-4">Logo &amp; Catatan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Logo Supplier</label>
                    @if($supplier->logo)
                        <div class="mb-2">
                            <img src="{{ asset('storage/'.$supplier->logo) }}" alt="{{ $supplier->name }}" class="h-16 w-16 object-cover rounded-lg border border-gray-200">
                        </div>
                    @endif
                    <input type="file" name="logo" accept="image/jpeg,image/png,image/webp" class="form-input @error('logo') is-invalid @enderror">
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, atau WEBP. Maks. 2MB. Kosongkan jika tidak ingin mengubah logo.</p>
                    @error('logo')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" rows="3" class="form-textarea @error('notes') is-invalid @enderror">{{ old('notes', $supplier->notes) }}</textarea>
                    @error('notes')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-4 flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                    {{ old('is_active', $supplier->is_active) ? 'checked' : '' }}
                    class="form-checkbox">
                <label for="is_active" class="form-label mb-0">Supplier Aktif</label>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-2">
        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </div>
</form>
@endsection

@extends('layouts.app')
@section('title', 'Tambah Gudang')
@section('breadcrumb')
<a href="{{ route('superadmin.warehouses.index') }}" class="hover:text-primary-700">Gudang</a>
<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
</svg>
<span class="text-gray-700 font-medium">Tambah</span>
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <div class="card-header">
            <h2 class="font-semibold text-gray-900">Tambah Gudang Baru</h2>
        </div>
        <form method="POST" action="{{ route('superadmin.warehouses.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="card-body space-y-5">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Nama Gudang <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="form-input @error('name') border-red-400 @enderror" placeholder="cth. Gudang Utama">
                        @error('name')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Kode Gudang <span class="text-red-500">*</span></label>
                        <input type="text" name="code" value="{{ old('code') }}"
                            class="form-input @error('code') border-red-400 @enderror font-mono uppercase"
                            placeholder="cth. GDG-01" oninput="this.value=this.value.toUpperCase()">
                        @error('code')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="form-label">Lokasi / Alamat <span class="text-red-500">*</span></label>
                    <textarea name="location" rows="3" class="form-textarea @error('location') border-red-400 @enderror"
                        placeholder="Alamat lengkap gudang...">{{ old('location') }}</textarea>
                    @error('location')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Nama PIC</label>
                        <input type="text" name="pic_name" value="{{ old('pic_name') }}" class="form-input"
                            placeholder="Penanggung jawab">
                    </div>
                    <div>
                        <label class="form-label">No. HP PIC</label>
                        <input type="text" name="pic_phone" value="{{ old('pic_phone') }}" class="form-input"
                            placeholder="08xx">
                    </div>
                </div>

                <div>
                    <label class="form-label">Foto Gudang</label>
                    <input type="file" name="photo" accept="image/*" class="form-input" id="photoInput">
                    <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, WEBP. Maks 2MB.</p>
                    <img id="photoPreview" class="mt-2 w-32 h-24 object-cover rounded-lg border hidden">
                </div>

                <div class="flex items-center gap-3 pt-1">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                        {{ old('is_active', true) ? 'checked' : '' }}
                        class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <label for="is_active" class="text-sm font-medium text-gray-700">Gudang Aktif</label>
                </div>

            </div>
            <div class="card-body border-t border-gray-100 flex justify-end gap-3">
                <a href="{{ route('superadmin.warehouses.index') }}" class="btn-secondary btn">Batal</a>
                <button type="submit" class="btn-primary btn">Simpan Gudang</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('photoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        const img = document.getElementById('photoPreview');
        img.src = e.target.result;
        img.classList.remove('hidden');
    };
    reader.readAsDataURL(file);
});
</script>
@endpush

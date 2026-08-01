@extends('layouts.app')
@section('title', 'Edit Gudang')
@section('breadcrumb')
<a href="{{ route('superadmin.warehouses.index') }}" class="hover:text-primary-700">Gudang</a>
<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
</svg>
<span class="text-gray-700 font-medium">Edit: {{ $warehouse->name }}</span>
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <div class="card-header">
            <h2 class="font-semibold text-gray-900">Edit Gudang — {{ $warehouse->name }}</h2>
            <x-status-badge :status="$warehouse->is_active ? '1' : '0'" />
        </div>
        <form method="POST" action="{{ route('warehouses.update', $warehouse) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="card-body space-y-5">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Nama Gudang <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $warehouse->name) }}"
                            class="form-input @error('name') border-red-400 @enderror">
                        @error('name')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Kode Gudang <span class="text-red-500">*</span></label>
                        <input type="text" name="code" value="{{ old('code', $warehouse->code) }}"
                            class="form-input @error('code') border-red-400 @enderror font-mono uppercase"
                            oninput="this.value=this.value.toUpperCase()">
                        @error('code')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="form-label">Lokasi / Alamat <span class="text-red-500">*</span></label>
                    <textarea name="location" rows="3"
                        class="form-textarea @error('location') border-red-400 @enderror">{{ old('location', $warehouse->location) }}</textarea>
                    @error('location')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Nama PIC</label>
                        <input type="text" name="pic_name" value="{{ old('pic_name', $warehouse->pic_name) }}"
                            class="form-input">
                    </div>
                    <div>
                        <label class="form-label">No. HP PIC</label>
                        <input type="text" name="pic_phone" value="{{ old('pic_phone', $warehouse->pic_phone) }}"
                            class="form-input">
                    </div>
                </div>

                <div>
                    <label class="form-label">Foto Gudang</label>
                    @if($warehouse->photo)
                    <div class="mb-2">
                        <img src="{{ Storage::url($warehouse->photo) }}"
                            class="w-32 h-24 object-cover rounded-lg border" id="photoPreview">
                        <p class="text-xs text-gray-400 mt-1">Foto saat ini. Upload baru untuk mengganti.</p>
                    </div>
                    @else
                    <img id="photoPreview" class="mt-2 w-32 h-24 object-cover rounded-lg border hidden mb-2">
                    @endif
                    <input type="file" name="photo" accept="image/*" class="form-input" id="photoInput">
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                        {{ old('is_active', $warehouse->is_active) ? 'checked' : '' }}
                        class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <label for="is_active" class="text-sm font-medium text-gray-700">Gudang Aktif</label>
                </div>

            </div>
            <div class="card-body border-t border-gray-100 flex justify-end gap-3">
                <a href="{{ route('superadmin.warehouses.show', $warehouse) }}" class="btn-secondary btn">Batal</a>
                <button type="submit" class="btn-primary btn">Update Gudang</button>
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
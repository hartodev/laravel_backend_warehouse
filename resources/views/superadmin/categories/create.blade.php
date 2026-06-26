{{-- categories/create.blade.php --}}
@extends('superadmin.layouts.app')
@section('title','Tambah Kategori')
@section('breadcrumb')
<a href="{{ route('superadmin.categories.index') }}" class="hover:text-primary-700">Kategori</a>
<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-700 font-medium">Tambah</span>
@endsection

@section('content')
<div class="max-w-xl">
<div class="card">
    <div class="card-header"><h2 class="font-semibold text-gray-900">Tambah Kategori Baru</h2></div>
    <form method="POST" action="{{ route('superadmin.categories.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="card-body space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="form-label">Nama Kategori <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required class="form-input @error('name') border-red-400 @enderror" placeholder="cth. Elektronik">
                @error('name')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Slug</label>
                <input type="text" name="slug" value="{{ old('slug') }}" class="form-input font-mono" placeholder="otomatis jika kosong">
            </div>
            <div>
                <label class="form-label">Icon (emoji/class)</label>
                <input type="text" name="icon" value="{{ old('icon') }}" class="form-input" placeholder="cth. 📦 atau fa-box">
            </div>
        </div>
        <div>
            <label class="form-label">Parent Kategori</label>
            <select name="parent_id" class="form-select">
                <option value="">— Tidak ada (kategori utama) —</option>
                @foreach($parents as $p)
                <option value="{{ $p->id }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Gambar Kategori</label>
            <input type="file" name="image" accept="image/*" class="form-input" id="imgInput">
            <img id="imgPreview" class="mt-2 w-24 h-24 object-cover rounded-lg border hidden">
        </div>
        <div class="flex items-center gap-3">
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 text-primary-600 border-gray-300 rounded">
            <label for="is_active" class="text-sm font-medium text-gray-700">Kategori Aktif</label>
        </div>
    </div>
    <div class="card-body border-t flex justify-end gap-3">
        <a href="{{ route('superadmin.categories.index') }}" class="btn-secondary btn">Batal</a>
        <button type="submit" class="btn-primary btn">Simpan</button>
    </div>
    </form>
</div>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('imgInput').addEventListener('change', function(e) {
    const reader = new FileReader();
    reader.onload = e => { const img = document.getElementById('imgPreview'); img.src = e.target.result; img.classList.remove('hidden'); };
    reader.readAsDataURL(e.target.files[0]);
});
</script>
@endpush

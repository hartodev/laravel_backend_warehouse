@extends('layouts.admin')
@section('title', 'Edit Kategori')
@section('content')

<div class="admin-page-head"><h2>Edit Kategori · {{ $category->name }}</h2></div>

@if ($errors->any())
<div class="admin-alert admin-alert-error">
    <div>
        @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
        @endforeach
    </div>
</div>
@endif

<form action="{{ route('admin.categories.update', $category) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="admin-form-grid" style="grid-template-columns:repeat(2,1fr);margin-bottom:20px;">
        <div>
            <label class="admin-label">Nama Kategori</label>
            <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="admin-input">
        </div>
        <div>
            <label class="admin-label">Kode (opsional)</label>
            <input type="text" name="code" value="{{ old('code', $category->code) }}" class="admin-input">
        </div>
        <div style="grid-column:span 2;">
            <label class="admin-label">Deskripsi</label>
            <textarea name="description" class="admin-textarea">{{ old('description', $category->description) }}</textarea>
        </div>
        <div>
            <label class="admin-label">Status</label>
            <select name="is_active" class="admin-select">
                <option value="1" @selected(old('is_active', $category->is_active)==1)>Aktif</option>
                <option value="0" @selected(old('is_active', $category->is_active)==0)>Nonaktif</option>
            </select>
        </div>
    </div>

    <div class="admin-form-actions" style="justify-content:flex-end;">
        <a href="{{ route('admin.categories.show', $category) }}" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary ripple">Simpan Perubahan</button>
    </div>
</form>
@endsection

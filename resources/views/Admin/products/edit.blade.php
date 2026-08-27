@extends('layouts.admin')
@section('title', 'Edit Produk')
@section('content')

<div class="admin-page-head">
    <h2>Edit Produk · {{ $product->name }}</h2>
</div>

<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @include('Admin.products._form')
    <div class="admin-form-actions" style="justify-content:flex-end;">
        <a href="{{ route('admin.products.show', $product) }}" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary ripple">Simpan Perubahan</button>
    </div>
</form>
@endsection
@extends('layouts.admin')
@section('title', 'Tambah Produk')
@section('content')

<div class="admin-page-head">
    <h2>Tambah Produk</h2>
</div>

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @include('Admin.products._form')
    <div class="admin-form-actions" style="justify-content:flex-end;">
        <a href="{{ route('admin.products.index') }}" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary ripple">Simpan</button>
    </div>
</form>
@endsection
@extends('layouts.admin')
@section('title', 'Tambah Gudang')
@section('content')

<div class="admin-page-head"><h2>Tambah Gudang</h2></div>

<form action="{{ route('admin.warehouses.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @include('Admin.warehouse._form')
    <div class="admin-form-actions" style="justify-content:flex-end;">
        <a href="{{ route('admin.warehouses.index') }}" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary ripple">Simpan</button>
    </div>
</form>
@endsection

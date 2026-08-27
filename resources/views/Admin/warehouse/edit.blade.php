@extends('layouts.admin')
@section('title', 'Edit Gudang')
@section('content')

<div class="admin-page-head"><h2>Edit Gudang · {{ $warehouse->name }}</h2></div>

<form action="{{ route('admin.warehouses.update', $warehouse) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @include('Admin.warehouse._form')
    <div class="admin-form-actions" style="justify-content:flex-end;">
        <a href="{{ route('admin.warehouses.show', $warehouse) }}" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary ripple">Simpan Perubahan</button>
    </div>
</form>
@endsection

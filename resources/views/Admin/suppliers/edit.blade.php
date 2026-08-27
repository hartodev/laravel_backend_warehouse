@extends('layouts.admin')
@section('title', 'Edit Supplier')
@section('content')

<div class="admin-page-head">
    <h2>Edit Supplier · {{ $supplier->name }}</h2>
</div>

<form action="{{ route('admin.suppliers.update', $supplier) }}" method="POST">
    @csrf
    @method('PUT')
    @include('Admin.suppliers._form')
    <div class="admin-form-actions" style="justify-content:flex-end;">
        <a href="{{ route('admin.suppliers.index') }}" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary ripple">Simpan Perubahan</button>
    </div>
</form>
@endsection
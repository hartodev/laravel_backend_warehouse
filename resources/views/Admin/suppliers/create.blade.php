@extends('layouts.admin')
@section('title', 'Tambah Supplier')
@section('content')

<div class="admin-page-head"><h2>Tambah Supplier</h2></div>

<form action="{{ route('admin.suppliers.store') }}" method="POST">
    @csrf
    @include('admin.suppliers._form')
    <div class="admin-form-actions" style="justify-content:flex-end;">
        <a href="{{ route('admin.suppliers.index') }}" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary ripple">Simpan</button>
    </div>
</form>
@endsection

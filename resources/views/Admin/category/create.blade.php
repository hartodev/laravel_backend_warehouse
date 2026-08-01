@extends('layouts.admin')

@section('title', 'Tambah Kategori')

@section('content')
<div class="admin-page-head">
    <h2>Tambah Kategori</h2>
</div>

<form method="POST" action="{{ route('admin.categories.store') }}" class="admin-card admin-card-pad">
    @csrf
    @include('admin.categories._form')
</form>
@endsection
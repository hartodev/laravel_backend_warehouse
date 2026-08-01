@extends('layouts.admin')

@section('title', 'Edit Kategori')

@section('content')
<div class="admin-page-head">
    <h2>Edit Kategori — {{ $category->name }}</h2>
</div>

<form method="POST" action="{{ route('admin.categories.update', $category) }}" class="admin-card admin-card-pad">
    @csrf
    @method('PUT')
    @include('admin.categories._form')
</form>
@endsection
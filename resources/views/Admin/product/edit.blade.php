@extends('layouts.admin')

@section('title', 'Edit Produk')

@section('content')
    <div class="admin-page-head"><h2>Edit Produk — {{ $product->name }}</h2></div>

    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data"
          class="admin-card admin-card-pad">
        @csrf
        @method('PUT')
        @include('admin.products._form')
    </form>
@endsection

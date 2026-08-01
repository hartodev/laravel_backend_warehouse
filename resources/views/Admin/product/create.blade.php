@extends('layouts.admin')

@section('title', 'Tambah Produk')

@section('content')
    <div class="admin-page-head"><h2>Tambah Produk</h2></div>

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data"
          class="admin-card admin-card-pad">
        @csrf
        @include('admin.products._form')
    </form>
@endsection

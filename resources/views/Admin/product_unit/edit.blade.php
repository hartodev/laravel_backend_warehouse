@extends('layouts.admin')
@section('title', 'Edit Satuan Produk')
@section('content')

<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-900">Edit Satuan Produk</h1>
    <p class="text-sm text-gray-500">{{ $productUnit->name }}</p>
</div>

<div class="card max-w-lg">
    <div class="card-body p-5">
        <form action="{{ route('admin.product-units.update', $productUnit) }}" method="POST">
            @csrf
            @method('PUT')
            @include('Admin.product_unit.form')
            <div class="flex justify-end gap-2 mt-4 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.product-units.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
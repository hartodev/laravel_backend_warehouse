@extends('layouts.admin')
@section('title', 'Tambah Satuan Produk')
@section('content')

<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-900">Tambah Satuan Produk</h1>
    <p class="text-sm text-gray-500">Buat unit satuan baru untuk produk (pcs, kg, box, dsb).</p>
</div>

<div class="card max-w-lg">
    <div class="card-body p-5">
        <form action="{{ route('admin.product-units.store') }}" method="POST">
            @csrf
            @include('Admin.product_unit.form')
            <div class="flex justify-end gap-2 mt-4 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.product-units.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
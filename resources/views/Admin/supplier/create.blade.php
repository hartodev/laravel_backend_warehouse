@extends('layouts.admin')

@section('title', 'Tambah Supplier')

@section('content')
<div class="admin-page-head">
    <h2>Tambah Supplier</h2>
</div>

<form method="POST" action="{{ route('admin.suppliers.store') }}" class="admin-card admin-card-pad">
    @csrf
    @include('admin.suppliers._form')
</form>
@endsection
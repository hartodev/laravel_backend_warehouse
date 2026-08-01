@extends('layouts.admin')

@section('title', 'Edit Supplier')

@section('content')
<div class="admin-page-head">
    <h2>Edit Supplier — {{ $supplier->name }}</h2>
</div>

<form method="POST" action="{{ route('admin.suppliers.update', $supplier) }}" class="admin-card admin-card-pad">
    @csrf
    @method('PUT')
    @include('admin.suppliers._form')
</form>
@endsection
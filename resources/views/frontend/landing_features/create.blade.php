@extends('layouts.admin')

@section('title', 'Tambah Fitur')

@section('content')
<div class="section-body">
    <div class="card">
        <div class="card-header"><h4>Tambah Fitur</h4></div>
        <div class="card-body">
            <form action="{{ route('admin.landing-features.store') }}" method="POST">
                @include('admin.landing-features._form')
            </form>
        </div>
    </div>
</div>
@endsection

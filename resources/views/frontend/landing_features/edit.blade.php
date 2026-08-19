@extends('layouts.admin')

@section('title', 'Edit Fitur')

@section('content')
<div class="section-body">
    <div class="card">
        <div class="card-header"><h4>Edit Fitur</h4></div>
        <div class="card-body">
            <form action="{{ route('admin.landing-features.update', $feature) }}" method="POST">
                @include('admin.landing-features._form')
            </form>
        </div>
    </div>
</div>
@endsection

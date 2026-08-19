@extends('layouts.admin')

@section('title', 'Edit Testimoni')

@section('content')
<div class="section-body">
    <div class="card">
        <div class="card-header"><h4>Edit Testimoni</h4></div>
        <div class="card-body">
            <form action="{{ route('admin.landing-testimonials.update', $testimonial) }}" method="POST">
                @include('admin.landing-testimonials._form')
            </form>
        </div>
    </div>
</div>
@endsection

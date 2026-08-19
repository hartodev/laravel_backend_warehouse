@extends('layouts.admin')

@section('title', 'Edit FAQ')

@section('content')
<div class="section-body">
    <div class="card">
        <div class="card-header"><h4>Edit FAQ</h4></div>
        <div class="card-body">
            <form action="{{ route('admin.landing-faqs.update', $faq) }}" method="POST">
                @include('admin.landing-faqs._form')
            </form>
        </div>
    </div>
</div>
@endsection

@extends('layouts.superadmin')

@section('title', 'Edit Kartu Highlight')

@section('breadcrumb')
    <a href="{{ route('superadmin.landing-hero-highlights.index') }}">Kartu Highlight</a>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round"
            stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <span>Edit</span>
@endsection

@section('content')
<div class="p-6 max-w-2xl">
    <h1 class="page-title mb-4">Edit Kartu Highlight</h1>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('superadmin.landing-hero-highlights.update', $highlight) }}" method="POST">
                @include('superadmin.landing-hero.highlights._form')
            </form>
        </div>
    </div>
</div>
@endsection

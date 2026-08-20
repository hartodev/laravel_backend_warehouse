@extends('layouts.app')
@section('title', 'Tambah Fitur')
@section('breadcrumb')
<a href="{{ route('landing-features.index') }}" class="hover:text-primary-700">Landing Page - Features</a>
<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
</svg>
<span class="text-gray-700 font-medium">Tambah</span>
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <div class="card-header">
            <h2 class="font-semibold text-gray-900">Tambah Fitur</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('landing-features.store') }}" method="POST">
                @include('frontend.landing-features.form')
            </form>
        </div>
    </div>
</div>
@endsection
@extends('layouts.superadmin')

@section('title', 'Tambah Benefit')

@section('breadcrumb')
    <a href="{{ route('superadmin.landing-benefits.index') }}">Landing Page</a>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round"
            stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <a href="{{ route('superadmin.landing-benefits.index') }}">Benefits</a>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round"
            stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <span>Tambah</span>
@endsection

@section('content')
<div class="p-6 max-w-2xl">
    <h1 class="page-title mb-4">Tambah Benefit</h1>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('superadmin.landing-benefits.store') }}" method="POST">
                @include('superadmin.landing-benefits._form')
            </form>
        </div>
    </div>
</div>
@endsection

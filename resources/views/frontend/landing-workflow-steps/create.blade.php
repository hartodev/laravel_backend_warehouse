@extends('layouts.superadmin')

@section('title', 'Tambah Langkah Workflow')

@section('breadcrumb')
    <a href="{{ route('superadmin.landing-workflow-steps.index') }}">Landing Page</a>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round"
            stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <a href="{{ route('superadmin.landing-workflow-steps.index') }}">Workflow</a>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round"
            stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <span>Tambah</span>
@endsection

@section('content')
<div class="p-6 max-w-2xl">
    <h1 class="page-title mb-4">Tambah Langkah Workflow</h1>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('superadmin.landing-workflow-steps.store') }}" method="POST">
                @include('superadmin.landing-workflow-steps._form')
            </form>
        </div>
    </div>
</div>
@endsection

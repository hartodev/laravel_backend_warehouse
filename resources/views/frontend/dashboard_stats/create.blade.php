@extends('layouts.superadmin')

@section('title', 'Tambah Dashboard Stat')

@section('breadcrumb')
    <a href="{{ route('superadmin.landing-dashboard-stats.index') }}">Dashboard Stats</a>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round"
            stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <span>Tambah</span>
@endsection

@section('content')
<div class="p-6 max-w-2xl">
    <h1 class="page-title mb-4">Tambah Dashboard Stat</h1>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('superadmin.landing-dashboard-stats.store') }}" method="POST">
                @include('superadmin.landing-dashboard-stats._form')
            </form>
        </div>
    </div>
</div>
@endsection

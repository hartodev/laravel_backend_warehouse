@extends('layouts.admin')

@section('title', 'Tambah Stat')

@section('content')
<div class="section-body">
    <div class="card">
        <div class="card-header"><h4>Tambah Stat</h4></div>
        <div class="card-body">
            <form action="{{ route('admin.landing-stats.store') }}" method="POST">
                @include('admin.landing-stats._form')
            </form>
        </div>
    </div>
</div>
@endsection

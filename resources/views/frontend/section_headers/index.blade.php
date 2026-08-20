@extends('layouts.superadmin')

@section('title', 'Landing - Header Section')

@section('breadcrumb')
    <a href="{{ route('superadmin.landing-section-headers.index') }}">Landing Page</a>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round"
            stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <span>Header Section</span>
@endsection

@section('content')
<div class="p-6 space-y-5">

    <div>
        <h1 class="page-title">Landing Page — Header Section</h1>
        <p class="text-sm text-gray-500 mt-0.5">Badge, judul, dan subtitle untuk section Dashboard, Solusi, dan Contact.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach ($headers as $item)
            <div class="card">
                <div class="card-body space-y-2">
                    <div class="badge badge-info">{{ $item['label'] }}</div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">
                        {{ $item['header']->title_normal }}
                        <span class="text-indigo-500">{{ $item['header']->title_gradient }}</span>
                    </h3>
                    <p class="text-sm text-gray-500 line-clamp-2">{{ $item['header']->subtitle }}</p>
                    <a href="{{ route('superadmin.landing-section-headers.edit', $item['key']) }}" class="btn btn-xs btn-secondary mt-2">
                        Edit
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

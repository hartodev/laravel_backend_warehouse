@extends('superadmin.layouts.app')
@section('title', 'Detail Activity Log')
@section('breadcrumb')
<a href="{{ route('superadmin.activity-logs.index') }}" class="hover:text-primary-700">Activity Log</a>
<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-700 font-medium">Detail</span>
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <div class="card-header">
            <h2 class="font-semibold text-gray-900">Detail Activity Log</h2>
            <span class="badge {{ str_contains($activityLog->action,'create') ? 'badge-success' : (str_contains($activityLog->action,'delete') ? 'badge-danger' : 'badge-info') }}">
                {{ $activityLog->action }}
            </span>
        </div>
        <div class="card-body space-y-4 text-sm">
            <div class="grid grid-cols-2 gap-4">
                <div><p class="text-xs text-gray-400 mb-1">User</p><p class="font-medium">{{ $activityLog->user->name ?? 'System' }}</p></div>
                <div><p class="text-xs text-gray-400 mb-1">Waktu</p><p>{{ $activityLog->created_at->isoFormat('D MMMM Y, HH:mm:ss') }}</p></div>
                <div><p class="text-xs text-gray-400 mb-1">Model</p><p class="font-mono">{{ $activityLog->model_type ?? '—' }}</p></div>
                <div><p class="text-xs text-gray-400 mb-1">Model ID</p><p class="font-mono">{{ $activityLog->model_id ?? '—' }}</p></div>
                <div><p class="text-xs text-gray-400 mb-1">IP Address</p><p class="font-mono">{{ $activityLog->ip_address ?? '—' }}</p></div>
                <div><p class="text-xs text-gray-400 mb-1">User Agent</p><p class="text-gray-500 text-xs truncate">{{ $activityLog->user_agent ?? '—' }}</p></div>
            </div>

            @if($activityLog->old_values)
            <div>
                <p class="text-xs text-gray-400 mb-2 font-semibold uppercase tracking-wider">Data Sebelum</p>
                <pre class="bg-red-50 border border-red-100 rounded-lg p-3 text-xs overflow-x-auto text-red-800">{{ json_encode(json_decode($activityLog->old_values), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
            @endif

            @if($activityLog->new_values)
            <div>
                <p class="text-xs text-gray-400 mb-2 font-semibold uppercase tracking-wider">Data Sesudah</p>
                <pre class="bg-green-50 border border-green-100 rounded-lg p-3 text-xs overflow-x-auto text-green-800">{{ json_encode(json_decode($activityLog->new_values), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
            @endif

            @if($activityLog->description)
            <div>
                <p class="text-xs text-gray-400 mb-1">Deskripsi</p>
                <p class="text-gray-700">{{ $activityLog->description }}</p>
            </div>
            @endif
        </div>
        <div class="card-body border-t">
            <a href="{{ route('superadmin.activity-logs.index') }}" class="btn-secondary btn">← Kembali</a>
        </div>
    </div>
</div>
@endsection

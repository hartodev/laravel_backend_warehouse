{{-- activity_logs/index.blade.php --}}
@extends('layouts.app')
@section('title','Activity Log')
@section('breadcrumb')<span class="text-gray-700 font-medium">Activity Log</span>@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <div><h1 class="text-xl font-bold text-gray-900">Activity Log</h1><p class="text-sm text-gray-500">{{ $logs->total() }} entri</p></div>
</div>

<div class="card mb-5">
    <form method="GET" class="card-body flex flex-wrap gap-3 items-end">
        <div class="w-44"><label class="form-label">User</label>
            <select name="user_id" class="form-select">
                <option value="">Semua User</option>
                @foreach($users as $u)<option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>@endforeach
            </select>
        </div>
        <div class="flex-1 min-w-40"><label class="form-label">Action</label><input type="text" name="action" value="{{ request('action') }}" placeholder="cth. create, update..." class="form-input"></div>
        <div class="flex-1 min-w-40"><label class="form-label">Model</label><input type="text" name="model_type" value="{{ request('model_type') }}" placeholder="cth. Product, StockOpname..." class="form-input"></div>
        <div class="w-36"><label class="form-label">Dari</label><input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input"></div>
        <div class="w-36"><label class="form-label">Sampai</label><input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input"></div>
        <button type="submit" class="btn-primary btn">Filter</button>
        <a href="{{ route('activity-logs.index') }}" class="btn-secondary btn">Reset</a>
    </form>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>User</th><th>Action</th><th>Model</th><th>ID</th><th>IP Address</th><th>Waktu</th><th class="text-right">Aksi</th></tr></thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($logs as $log)
            <tr>
                <td class="font-medium">{{ $log->user->name ?? 'System' }}</td>
                <td><span class="badge {{ str_contains($log->action,'create') ? 'badge-success' : (str_contains($log->action,'delete') ? 'badge-danger' : 'badge-info') }}">{{ $log->action }}</span></td>
                <td class="font-mono text-xs">{{ class_basename($log->model_type ?? '') }}</td>
                <td class="font-mono text-xs text-gray-500">{{ $log->model_id ?? '—' }}</td>
                <td class="font-mono text-xs text-gray-400">{{ $log->ip_address ?? '—' }}</td>
                <td class="text-sm text-gray-500">{{ $log->created_at->isoFormat('D MMM, HH:mm') }}</td>
                <td class="text-right"><a href="{{ route('activity-logs.show', $log) }}" class="btn btn-secondary btn-sm">Detail</a></td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center py-12 text-gray-400">Belum ada activity log</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $logs->links() }}</div>
@endsection

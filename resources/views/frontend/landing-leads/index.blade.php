{{-- Sesuaikan @extends kalau nama layout kamu bukan 'layouts.superadmin' --}}
@extends('layouts.superadmin')

@section('title', 'Contact Leads')

@section('breadcrumb')
    <a href="{{ route('superadmin.landing-leads.index') }}">Landing Page</a>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round"
            stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <span>Contact Leads</span>
@endsection

@section('content')
<div class="p-6 space-y-5">

    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">Contact Leads</h1>
            <p class="text-sm text-gray-500 mt-0.5">Pesan masuk dari tombol "Contact Sales" di landing page.</p>
        </div>
    </div>

    {{-- Filter tabs by status --}}
    <div class="flex items-center gap-2 flex-wrap">
        @php
            $tabs = [
                'all'       => 'Semua',
                'new'       => 'Baru',
                'contacted' => 'Dihubungi',
                'closed'    => 'Selesai',
            ];
            $activeStatus = request('status', 'all');
        @endphp
        @foreach ($tabs as $key => $label)
            <a href="{{ route('superadmin.landing-leads.index', $key === 'all' ? [] : ['status' => $key]) }}"
               class="btn btn-sm {{ $activeStatus === $key ? 'btn-primary' : 'btn-secondary' }}">
                {{ $label }}
                <span class="badge badge-gray ml-1">{{ $counts[$key] }}</span>
            </a>
        @endforeach

        <form action="{{ route('superadmin.landing-leads.index') }}" method="GET" class="ml-auto flex items-center gap-2">
            @if (request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / email / perusahaan..."
                   class="form-input" style="min-width:240px">
            <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
        </form>
    </div>

    <div class="card">
        <div class="table-wrap" style="border:none">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Perusahaan</th>
                        <th>Pesan</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($leads as $lead)
                        <tr>
                            <td class="whitespace-nowrap">{{ $lead->created_at->format('d M Y, H:i') }}</td>
                            <td class="font-medium text-gray-900 dark:text-white">{{ $lead->name }}</td>
                            <td>{{ $lead->email }}</td>
                            <td>{{ $lead->company ?: '-' }}</td>
                            <td class="max-w-xs truncate" title="{{ $lead->message }}">{{ $lead->message }}</td>
                            <td>
                                @php
                                    $statusBadge = [
                                        'new'       => 'badge-info',
                                        'contacted' => 'badge-warning',
                                        'closed'    => 'badge-success',
                                    ][$lead->status] ?? 'badge-gray';
                                    $statusLabel = [
                                        'new'       => 'Baru',
                                        'contacted' => 'Dihubungi',
                                        'closed'    => 'Selesai',
                                    ][$lead->status] ?? $lead->status;
                                @endphp
                                <span class="badge {{ $statusBadge }}">{{ $statusLabel }}</span>
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <a href="{{ route('superadmin.landing-leads.show', $lead) }}" class="btn btn-xs btn-secondary">
                                    Detail
                                </a>
                                <form action="{{ route('superadmin.landing-leads.destroy', $lead) }}" method="POST"
                                      class="inline" onsubmit="return confirm('Hapus lead ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-400">Belum ada lead masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($leads->hasPages())
            <div class="card-footer">
                {{ $leads->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

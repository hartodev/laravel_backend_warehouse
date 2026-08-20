@extends('layouts.superadmin')

@section('title', 'Detail Lead')

@section('breadcrumb')
    <a href="{{ route('superadmin.landing-leads.index') }}">Landing Page</a>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round"
            stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <a href="{{ route('superadmin.landing-leads.index') }}">Contact Leads</a>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round"
            stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <span>{{ $lead->name }}</span>
@endsection

@section('content')
<div class="p-6 space-y-5 max-w-3xl">

    <div class="flex items-center justify-between">
        <h1 class="page-title">Detail Lead</h1>
        <a href="{{ route('superadmin.landing-leads.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="font-semibold text-gray-800 dark:text-white">Informasi Kontak</h3>
            <span class="text-xs text-gray-400">{{ $lead->created_at->format('d M Y, H:i') }}</span>
        </div>
        <div class="card-body space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-400">Nama</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $lead->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Email</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        <a href="mailto:{{ $lead->email }}" class="text-indigo-600 hover:underline">{{ $lead->email }}</a>
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Telepon</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $lead->phone ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Perusahaan</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $lead->company ?: '-' }}</p>
                </div>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Pesan</p>
                <p class="text-sm text-gray-700 dark:text-gray-200 whitespace-pre-line bg-gray-50 dark:bg-slate-900 rounded-lg p-3">{{ $lead->message }}</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="font-semibold text-gray-800 dark:text-white">Tindak Lanjut</h3>
        </div>
        <form action="{{ route('superadmin.landing-leads.update', $lead) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body space-y-4">
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="new" {{ $lead->status === 'new' ? 'selected' : '' }}>Baru</option>
                        <option value="contacted" {{ $lead->status === 'contacted' ? 'selected' : '' }}>Dihubungi</option>
                        <option value="closed" {{ $lead->status === 'closed' ? 'selected' : '' }}>Selesai</option>
                    </select>
                    @error('status') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">Catatan Internal</label>
                    <textarea name="admin_note" rows="4" class="form-textarea"
                              placeholder="Catatan follow-up, hasil telepon, dsb.">{{ old('admin_note', $lead->admin_note) }}</textarea>
                    @error('admin_note') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                @if ($lead->handled_at)
                    <p class="text-xs text-gray-400">
                        Terakhir diupdate oleh {{ $lead->handledBy->name ?? '-' }}
                        pada {{ $lead->handled_at->format('d M Y, H:i') }}
                    </p>
                @endif
            </div>
            <div class="card-footer flex justify-end gap-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

{{-- ============================================================
     payments/index.blade.php
============================================================ --}}
@extends('layouts.app')
@section('title','Pembayaran')
@section('breadcrumb')<span class="text-gray-700 font-medium">Pembayaran</span>@endsection

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Pembayaran</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $payments->total() }} transaksi</p>
    </div>
    <a href="{{ route('superadmin.payments.create') }}" class="btn-primary btn">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Catat Pembayaran
    </a>
</div>

<div class="card mb-5">
    <form method="GET" class="card-body flex flex-wrap gap-3 items-end">
        <div class="w-36"><label class="form-label">Tipe</label>
            <select name="payment_type" class="form-select">
                <option value="">Semua</option>
                <option value="masuk" {{ request('payment_type')==='masuk'?'selected':'' }}>Masuk</option>
                <option value="keluar" {{ request('payment_type')==='keluar'?'selected':'' }}>Keluar</option>
            </select>
        </div>
        <div class="w-36"><label class="form-label">Metode</label>
            <select name="payment_method" class="form-select">
                <option value="">Semua</option>
                @foreach(['cash','transfer','cek','giro'] as $m)
                <option value="{{ $m }}" {{ request('payment_method')===$m?'selected':'' }}>{{ ucfirst($m) }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-36"><label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua</option>
                <option value="pending" {{ request('status')==='pending'?'selected':'' }}>Pending</option>
                <option value="verified" {{ request('status')==='verified'?'selected':'' }}>Verified</option>
            </select>
        </div>
        <div class="w-36"><label class="form-label">Dari</label><input type="date" name="date_from"
                value="{{ request('date_from') }}" class="form-input"></div>
        <div class="w-36"><label class="form-label">Sampai</label><input type="date" name="date_to"
                value="{{ request('date_to') }}" class="form-input"></div>
        <button type="submit" class="btn-primary btn">Filter</button>
        <a href="{{ route('superadmin.payments.index') }}" class="btn-secondary btn">Reset</a>
    </form>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>No. Bayar</th>
                <th>Tipe</th>
                <th>Metode</th>
                <th>Dari/Ke</th>
                <th>Tgl.</th>
                <th class="text-right">Nominal</th>
                <th>Status</th>
                <th class="text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($payments as $p)
            <tr>
                <td><span class="font-mono text-xs font-medium text-primary-700">{{ $p->payment_number }}</span></td>
                <td><span
                        class="badge {{ $p->payment_type === 'masuk' ? 'badge-success' : 'badge-danger' }}">{{ ucfirst($p->payment_type) }}</span>
                </td>
                <td>{{ ucfirst($p->payment_method) }}</td>
                <td class="max-w-xs truncate text-sm">{{ $p->diterima_dari ?? $p->nama_penerima ?? '—' }}</td>
                <td>{{ \Carbon\Carbon::parse($p->payment_date)->isoFormat('D MMM Y') }}</td>
                <td
                    class="text-right font-semibold {{ $p->payment_type === 'masuk' ? 'text-green-700' : 'text-red-600' }}">
                    {{ $p->payment_type === 'masuk' ? '+' : '-' }} Rp {{ number_format($p->nominal) }}
                </td>
                <td>
                    <x-status-badge :status="$p->status" />
                </td>
                <td class="text-right">
                    <div class="flex items-center justify-end gap-1">
                        <a href="{{ route('superadmin.payments.show', $p) }}"
                            class="btn btn-secondary btn-sm">Detail</a>
                        @if($p->status === 'pending')
                        <form method="POST" action="{{ route('superadmin.payments.verify', $p) }}" class="inline">@csrf<button
                                class="btn btn-success btn-sm">Verifikasi</button></form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center py-12 text-gray-400">Belum ada data pembayaran</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $payments->links() }}</div>
@endsection



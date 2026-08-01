@extends('layouts.app')
@section('title', $payment->payment_number)
@section('breadcrumb')
<a href="{{ route('superadmin.payments.index') }}" class="hover:text-primary-700">Pembayaran</a>
<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
</svg>
<span class="text-gray-700 font-medium">{{ $payment->payment_number }}</span>
@endsection

@section('content')
<div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-3 flex-wrap">
            <h1 class="text-xl font-bold text-gray-900">{{ $payment->payment_number }}</h1>
            <span
                class="badge {{ $payment->payment_type === 'masuk' ? 'badge-success' : 'badge-danger' }}">{{ ucfirst($payment->payment_type) }}</span>
            <x-status-badge :status="$payment->status" />
        </div>
        <p class="text-sm text-gray-500 mt-0.5">
            {{ \Carbon\Carbon::parse($payment->payment_date)->isoFormat('D MMMM Y') }} ·
            {{ ucfirst($payment->payment_method) }}</p>
    </div>
    <div class="flex gap-2">
        @if($payment->status === 'pending')
        <a href="{{ route('superadmin.payments.edit', $payment) }}" class="btn-secondary btn">Edit</a>
        <form method="POST" action="{{ route('payments.verify', $payment) }}" class="inline">@csrf<button
                class="btn-success btn">✓ Verifikasi</button></form>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
    <div class="card p-5 space-y-3 text-sm">
        <p class="font-semibold text-gray-900 mb-2">Detail Pembayaran</p>
        <div class="flex justify-between"><span class="text-gray-400">Nominal</span><span
                class="font-bold text-xl {{ $payment->payment_type === 'masuk' ? 'text-green-700' : 'text-red-600' }}">{{ $payment->payment_type === 'masuk' ? '+' : '-' }}
                Rp {{ number_format($payment->nominal) }}</span></div>
        <div class="flex justify-between"><span class="text-gray-400">Terbilang</span><span
                class="italic text-right max-w-xs">{{ $payment->terbilang ?? '—' }}</span></div>
        <div class="flex justify-between"><span class="text-gray-400">Untuk</span><span
                class="text-right max-w-xs">{{ $payment->untuk_pembayaran ?? '—' }}</span></div>
        @if($payment->diterima_dari)<div class="flex justify-between"><span class="text-gray-400">Diterima
                Dari</span><span>{{ $payment->diterima_dari }}</span></div>@endif
        @if($payment->keterangan)<div class="flex justify-between"><span class="text-gray-400">Keterangan</span><span
                class="text-right max-w-xs">{{ $payment->keterangan }}</span></div>@endif
    </div>
    <div class="card p-5 space-y-3 text-sm">
        <p class="font-semibold text-gray-900 mb-2">Info Transfer</p>
        @if($payment->nama_pengirim)<div class="flex justify-between"><span
                class="text-gray-400">Pengirim</span><span>{{ $payment->nama_pengirim }}@if($payment->bank_pengirim)
                <span class="text-gray-400">({{ $payment->bank_pengirim }})</span>@endif</span></div>@endif
        @if($payment->nama_penerima)<div class="flex justify-between"><span
                class="text-gray-400">Penerima</span><span>{{ $payment->nama_penerima }}@if($payment->bank_penerima)
                <span class="text-gray-400">({{ $payment->bank_penerima }})</span>@endif</span></div>@endif
        @if($payment->no_rekening_tujuan)<div class="flex justify-between"><span class="text-gray-400">No.
                Rekening</span><span class="font-mono">{{ $payment->no_rekening_tujuan }}</span></div>@endif
        <div class="flex justify-between"><span class="text-gray-400">Dibuat
                Oleh</span><span>{{ $payment->createdBy->name ?? '—' }}</span></div>
        @if($payment->verified_by)<div class="flex justify-between"><span
                class="text-gray-400">Diverifikasi</span><span>{{ $payment->verifiedBy->name }} ·
                {{ \Carbon\Carbon::parse($payment->verified_at)->isoFormat('D MMM Y') }}</span></div>@endif
    </div>
</div>

@if($payment->bukti_file)
<div class="card p-5 mb-5">
    <p class="font-semibold text-gray-900 mb-3">Bukti Pembayaran</p>
    @php $ext = pathinfo($payment->bukti_file, PATHINFO_EXTENSION); @endphp
    @if(in_array(strtolower($ext), ['jpg','jpeg','png','webp']))
    <img src="{{ Storage::url($payment->bukti_file) }}" class="max-w-md rounded-lg border">
    @else
    <a href="{{ Storage::url($payment->bukti_file) }}" target="_blank" class="btn-secondary btn">📎 Download Bukti</a>
    @endif
</div>
@endif

@if($payment->cashBook)
<div class="card p-5">
    <p class="font-semibold text-gray-900 mb-2">Entri Buku Kas</p>
    <p class="text-sm text-gray-500">No. Bukti: <span
            class="font-mono font-medium">{{ $payment->cashBook->no_bukti }}</span></p>
    <a href="{{ route('superadmin.cash-books.show', $payment->cashBook) }}"
        class="text-sm text-primary-700 hover:underline">Lihat di Buku Kas →</a>
</div>
@endif
@endsection
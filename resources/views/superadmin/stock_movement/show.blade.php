{{-- stock_movements/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Detail Pergerakan Stok')
@section('breadcrumb')
<a href="{{ route('stock-movements.index') }}" class="hover:text-primary-700">Pergerakan Stok</a>
<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-700 font-medium">Detail</span>
@endsection

@section('content')
<div class="max-w-xl">
    <div class="card">
        <div class="card-header">
            <h2 class="font-semibold text-gray-900">Detail Pergerakan Stok</h2>
            <span class="badge {{ $movement->type === 'in' ? 'badge-success' : ($movement->type === 'out' ? 'badge-danger' : 'badge-warning') }}">
                {{ strtoupper($movement->type) }}
            </span>
        </div>
        <div class="card-body grid grid-cols-2 gap-4 text-sm">
            <div><p class="text-xs text-gray-400 mb-1">Produk</p><p class="font-semibold">{{ $movement->product->name ?? '—' }}</p><p class="font-mono text-xs text-gray-400">{{ $movement->product->sku ?? '' }}</p></div>
            <div><p class="text-xs text-gray-400 mb-1">Gudang</p><p class="font-medium">{{ $movement->warehouse->name ?? '—' }}</p></div>
            <div><p class="text-xs text-gray-400 mb-1">Stok Sebelum</p><p class="text-xl font-bold text-gray-500">{{ number_format($movement->quantity_before) }}</p></div>
            <div><p class="text-xs text-gray-400 mb-1">Perubahan</p>
                <p class="text-xl font-bold {{ $movement->type === 'in' ? 'text-green-700' : ($movement->type === 'out' ? 'text-red-600' : 'text-yellow-700') }}">
                    {{ $movement->type === 'in' ? '+' : ($movement->type === 'out' ? '-' : '±') }}{{ number_format($movement->quantity) }}
                </p>
            </div>
            <div class="col-span-2"><p class="text-xs text-gray-400 mb-1">Stok Sesudah</p><p class="text-2xl font-bold text-primary-700">{{ number_format($movement->quantity_after) }}</p></div>
            <div><p class="text-xs text-gray-400 mb-1">Dicatat Oleh</p><p>{{ $movement->createdBy->name ?? '—' }}</p></div>
            <div><p class="text-xs text-gray-400 mb-1">Waktu</p><p>{{ $movement->created_at?->isoFormat('D MMMM Y, HH:mm') ?? '—' }}</p></div>
            @if($movement->note)
            <div class="col-span-2"><p class="text-xs text-gray-400 mb-1">Catatan</p><p class="text-gray-700">{{ $movement->note }}</p></div>
            @endif
        </div>
        <div class="card-body border-t"><a href="{{ route('stock-movements.index') }}" class="btn-secondary btn">← Kembali</a></div>
    </div>
</div>
@endsection

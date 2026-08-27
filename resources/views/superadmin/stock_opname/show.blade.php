@extends('layouts.app')
@section('title', 'Detail Opname')
@section('breadcrumb')
<a href="{{ route('superadmin.stock-opnames.index') }}" class="hover:text-primary-700">Stock Opname</a>
<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
</svg>
<span class="text-gray-700 font-medium">{{ $stockOpname->opname_number }}</span>
@endsection

@section('content')
{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-3 flex-wrap">
            <h1 class="text-xl font-bold text-gray-900">{{ $stockOpname->opname_number }}</h1>
            <x-status-badge :status="$stockOpname->status" />
        </div>
        <p class="text-sm text-gray-500 mt-0.5">
            Gudang: <strong>{{ $stockOpname->warehouse->name ?? '—' }}</strong> ·
            Tanggal: {{ \Carbon\Carbon::parse($stockOpname->opname_date)->isoFormat('D MMMM Y') }}
        </p>
    </div>
    <div class="flex flex-wrap gap-2">
        @if($stockOpname->status === 'draft')
        <form method="POST" action="{{ route('superadmin.stock-opnames.start', $stockOpname) }}">
            @csrf
            <button type="submit" class="btn-primary btn">Mulai Opname</button>
        </form>
        <a href="{{ route('superadmin.stock-opnames.edit', $stockOpname) }}" class="btn-secondary btn">Edit</a>
        @elseif($stockOpname->status === 'in_progress')
        <button onclick="document.getElementById('complete-form').classList.toggle('hidden')" class="btn-primary btn">
            Selesaikan Opname
        </button>
        @elseif($stockOpname->status === 'pending_approval')
        <form method="POST" action="{{ route('superadmin.stock-opnames.approve', $stockOpname) }}" class="inline">
            @csrf
            <button type="submit" class="btn-success btn">Setujui & Terapkan</button>
        </form>
        <button onclick="document.getElementById('reject-modal').classList.remove('hidden')" class="btn-danger btn">
            Tolak
        </button>
        @endif
    </div>
</div>

{{-- Info cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="stat-card">
        <div class="stat-icon bg-blue-50 text-blue-600">📋</div>
        <div>
            <p class="text-2xl font-bold">{{ $stockOpname->items->count() }}</p>
            <p class="text-sm text-gray-500">Total Item</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-green-50 text-green-600">✅</div>
        <div>
            <p class="text-2xl font-bold text-green-700">{{ $stockOpname->items->where('difference', '>', 0)->count() }}
            </p>
            <p class="text-sm text-gray-500">Lebih</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-red-50 text-red-600">❌</div>
        <div>
            <p class="text-2xl font-bold text-red-700">{{ $stockOpname->items->where('difference', '<', 0)->count() }}
            </p>
            <p class="text-sm text-gray-500">Kurang</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-gray-50 text-gray-600">🟰</div>
        <div>
            <p class="text-2xl font-bold">{{ $stockOpname->items->where('difference', 0)->count() }}</p>
            <p class="text-sm text-gray-500">Sesuai</p>
        </div>
    </div>
</div>

{{-- Items table / form --}}
@if($stockOpname->status === 'in_progress')
{{-- FORM COMPLETE --}}
<div id="complete-form" class="hidden mb-6">
    <div class="card border-primary-200 bg-primary-50/30">
        <div class="card-header">
            <h3 class="font-semibold text-primary-900">Update Stok Fisik</h3>
            <p class="text-sm text-primary-600">Isi jumlah stok yang dihitung secara fisik</p>
        </div>
        <form method="POST" action="{{ route('superadmin.stock-opnames.complete', $stockOpname) }}">
            @csrf
            <div class="table-wrap rounded-none border-0">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>SKU</th>
                            <th class="text-right">Stok Sistem</th>
                            <th class="text-right">Stok Fisik</th>
                            <th class="text-right">Selisih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($stockOpname->items as $item)
                        <tr x-data="{ sys: {{ $item->system_stock }}, phys: {{ $item->physical_stock ?? 0 }} }">
                            <td class="font-medium">{{ $item->product->name ?? '—' }}</td>
                            <td class="font-mono text-xs text-gray-500">{{ $item->product->sku ?? '—' }}</td>
                            <td class="text-right font-medium">{{ $item->system_stock }}</td>
                            <td class="text-right">
                                <input type="hidden" name="items[{{ $loop->index }}][stock_opname_item_id]"
                                    value="{{ $item->id }}">
                                <input type="number" name="items[{{ $loop->index }}][physical_stock]"
                                    x-model.number="phys" min="0" class="w-24 form-input text-right text-sm py-1.5"
                                    required>
                            </td>
                            <td class="text-right font-semibold"
                                :class="phys - sys < 0 ? 'text-red-600' : phys - sys > 0 ? 'text-green-600' : 'text-gray-400'">
                                <span x-text="(phys - sys > 0 ? '+' : '') + (phys - sys)"></span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-body border-t flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('complete-form').classList.add('hidden')"
                    class="btn-secondary btn">Batal</button>
                <button type="submit" class="btn-primary btn">Simpan & Kirim Approval</button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- Read-only items --}}
<div class="card">
    <div class="card-header">
        <h3 class="font-semibold text-gray-900">Daftar Item Opname</h3>
        @if($stockOpname->notes)
        <span class="text-sm text-gray-500">Catatan: {{ $stockOpname->notes }}</span>
        @endif
    </div>
    <div class="table-wrap rounded-none border-0">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>SKU</th>
                    <th class="text-right">Stok Sistem</th>
                    <th class="text-right">Stok Fisik</th>
                    <th class="text-right">Selisih</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($stockOpname->items as $item)
                <tr>
                    <td class="font-medium">{{ $item->product->name ?? '—' }}</td>
                    <td class="font-mono text-xs text-gray-500">{{ $item->product->sku ?? '—' }}</td>
                    <td class="text-right">{{ number_format($item->system_stock) }}</td>
                    <td class="text-right">{{ number_format($item->physical_stock) }}</td>
                    <td
                        class="text-right font-semibold {{ $item->difference < 0 ? 'text-red-600' : ($item->difference > 0 ? 'text-green-600' : 'text-gray-400') }}">
                        {{ $item->difference > 0 ? '+' : '' }}{{ $item->difference }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-8 text-gray-400">Belum ada item (klik Mulai Opname dulu)</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Reject modal --}}
<div id="reject-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
        <div class="px-6 py-5 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Tolak Opname</h3>
        </div>
        <form method="POST" action="{{ route('superadmin.stock-opnames.reject', $stockOpname) }}">
            @csrf
            <div class="px-6 py-4 space-y-3">
                <p class="text-sm text-gray-600">Berikan alasan penolakan agar tim dapat memperbaiki opname.</p>
                <div>
                    <label class="form-label">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <textarea name="reject_reason" rows="3" required class="form-textarea"
                        placeholder="Tulis alasan..."></textarea>
                </div>
            </div>
            <div class="px-6 py-4 border-t flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('reject-modal').classList.add('hidden')"
                    class="btn-secondary btn">Batal</button>
                <button type="submit" class="btn-danger btn">Tolak Opname</button>
            </div>
        </form>
    </div>
</div>
@endsection
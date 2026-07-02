{{-- resources/views/superadmin/product_submission/show.blade.php --}}
@extends('layouts.app')
@section('title','Detail Pengajuan Produk')
@section('breadcrumb')
    <a href="{{ route('product-submissions.index') }}" class="hover:text-primary-700">Pengajuan Produk</a>
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-700 font-medium">Detail</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Pengajuan Produk</h1>
        <p class="text-sm text-gray-500">
            Diajukan oleh {{ $productSubmission->submittedBy->name ?? '—' }}
            pada {{ $productSubmission->created_at?->isoFormat('D MMM Y, HH:mm') }}
        </p>
    </div>
    <span class="badge {{ $productSubmission->status === 'approved' ? 'badge-success' : ($productSubmission->status === 'pending' ? 'badge-warning' : 'badge-danger') }}">
        {{ ucfirst($productSubmission->status) }}
    </span>
</div>

@if (session('success'))
    <div class="mb-5 rounded-md bg-green-50 border border-green-200 p-4 text-sm text-green-700">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="mb-5 rounded-md bg-red-50 border border-red-200 p-4 text-sm text-red-700">{{ session('error') }}</div>
@endif

<div class="card mb-5">
    <div class="card-header"><h2 class="font-semibold text-gray-800">Informasi Produk</h2></div>
    <div class="card-body grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        <div>
            <p class="text-gray-500">Nama Produk</p>
            <p class="font-medium text-gray-900">{{ $productSubmission->product->name ?? '—' }}</p>
        </div>
        <div>
            <p class="text-gray-500">SKU</p>
            <p class="font-medium text-gray-900 font-mono">{{ $productSubmission->product->sku ?? '—' }}</p>
        </div>
        <div>
            <p class="text-gray-500">Barcode</p>
            <p class="font-medium text-gray-900 font-mono">{{ $productSubmission->product->barcode ?? '—' }}</p>
        </div>
        <div>
            <p class="text-gray-500">Kategori</p>
            <p class="font-medium text-gray-900">{{ $productSubmission->product->category->name ?? '—' }}</p>
        </div>
        <div>
            <p class="text-gray-500">Supplier</p>
            <p class="font-medium text-gray-900">{{ $productSubmission->product->supplier->name ?? '—' }}</p>
        </div>
        <div>
            <p class="text-gray-500">Status Produk Saat Ini</p>
            <p class="font-medium {{ $productSubmission->product->is_active ?? false ? 'text-green-600' : 'text-gray-500' }}">
                {{ ($productSubmission->product->is_active ?? false) ? 'Aktif' : 'Tidak Aktif' }}
            </p>
        </div>
    </div>
</div>

@if ($productSubmission->change_data)
<div class="card mb-5">
    <div class="card-header"><h2 class="font-semibold text-gray-800">Perubahan yang Diajukan</h2></div>
    <div class="card-body">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-gray-500 text-left border-b border-gray-100">
                    <th class="py-2 pr-4 font-medium">Field</th>
                    <th class="py-2 font-medium">Nilai Baru</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($productSubmission->change_data as $field => $value)
                <tr>
                    <td class="py-2 pr-4 font-medium text-gray-700">{{ Str::headline($field) }}</td>
                    <td class="py-2 text-gray-900">
                        @if (is_array($value))
                            {{ json_encode($value) }}
                        @elseif (is_bool($value))
                            {{ $value ? 'Ya' : 'Tidak' }}
                        @else
                            {{ $value }}
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<div class="card mb-5">
    <div class="card-body text-sm text-gray-400">
        Tidak ada perubahan data — pengajuan ini hanya untuk aktivasi produk.
    </div>
</div>
@endif

@if ($productSubmission->status !== 'pending')
<div class="card mb-5">
    <div class="card-header"><h2 class="font-semibold text-gray-800">Hasil Review</h2></div>
    <div class="card-body grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        <div>
            <p class="text-gray-500">Direview Oleh</p>
            <p class="font-medium text-gray-900">{{ $productSubmission->reviewedBy->name ?? '—' }}</p>
        </div>
        <div>
            <p class="text-gray-500">Tanggal Review</p>
            <p class="font-medium text-gray-900">{{ $productSubmission->reviewed_at ? \Carbon\Carbon::parse($productSubmission->reviewed_at)->isoFormat('D MMM Y, HH:mm') : '—' }}</p>
        </div>
        <div class="sm:col-span-2">
            <p class="text-gray-500">Catatan Review</p>
            <p class="font-medium text-gray-900">{{ $productSubmission->review_note ?? '—' }}</p>
        </div>
    </div>
</div>
@endif

<div class="flex justify-between">
    <a href="{{ route('product-submissions.index') }}" class="btn-secondary btn">&larr; Kembali</a>

    @if ($productSubmission->status === 'pending')
    <div class="flex gap-3">
        <form method="POST" action="{{ route('product-submissions.approve', $productSubmission) }}" class="inline">
            @csrf
            <input type="hidden" name="review_note" value="">
            <button type="submit" class="btn-primary btn" onclick="return confirm('Setujui pengajuan produk ini? Produk akan otomatis diaktifkan.')">Setujui</button>
        </form>

        <button type="button" class="btn-danger btn" onclick="document.getElementById('reject-modal').classList.remove('hidden')">Tolak</button>
    </div>
    @endif
</div>

@if ($productSubmission->status === 'pending')
<div id="reject-modal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
        <h3 class="font-semibold text-gray-900 mb-3">Tolak Pengajuan Produk</h3>
        <form method="POST" action="{{ route('product-submissions.reject', $productSubmission) }}">
            @csrf
            <label class="form-label">Catatan Penolakan <span class="text-red-500">*</span></label>
            <textarea name="review_note" rows="3" maxlength="500" class="form-textarea mb-4" required></textarea>
            <div class="flex justify-end gap-3">
                <button type="button" class="btn-secondary btn" onclick="document.getElementById('reject-modal').classList.add('hidden')">Batal</button>
                <button type="submit" class="btn-danger btn">Tolak Pengajuan</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection




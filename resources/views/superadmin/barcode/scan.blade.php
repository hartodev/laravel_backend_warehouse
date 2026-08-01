{{-- barcodes/scan.blade.php --}}
@extends('layouts.app')
@section('title', 'Scan Barcode')
@section('breadcrumb')
<a href="{{ route('superadmin.barcodes.index') }}" class="hover:text-primary-700">Barcode</a>
<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
</svg>
<span class="text-gray-700 font-medium">Scan</span>
@endsection

@section('content')
<div class="max-w-xl">
    @if(session('scan_result'))
    @php $result = session('scan_result'); $product = $result['product']; @endphp
    <div class="card mb-5 border-green-300 bg-green-50">
        <div class="card-header bg-green-100">
            <h3 class="font-semibold text-green-900">✓ Produk Ditemukan</h3>
        </div>
        <div class="card-body space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-gray-500">Nama</span><span
                    class="font-bold">{{ $product->name }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">SKU</span><span
                    class="font-mono">{{ $product->sku }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Barcode</span><span
                    class="font-mono">{{ $product->barcode }}</span></div>
            <div class="flex justify-between"><span
                    class="text-gray-500">Kategori</span><span>{{ $product->category->name ?? '—' }}</span></div>
            @if(!empty($result['stockInfo']))
            <div class="flex justify-between border-t pt-2 mt-2"><span class="text-gray-500">Stok di Gudang</span><span
                    class="font-bold text-lg text-primary-700">{{ number_format($result['stockInfo']['quantity']) }}</span>
            </div>
            @endif
        </div>
        <div class="card-body border-t">
            <a href="{{ route('superadmin.products.show', $product) }}" class="btn-primary btn btn-sm">Lihat Produk
                →</a>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h2 class="font-semibold text-gray-900">Scan Barcode Produk</h2>
        </div>
        <form method="POST" action="{{ route('barcodes.do-scan') }}">
            @csrf
            <div class="card-body space-y-4">
                <div>
                    <label class="form-label">Nilai Barcode / SKU <span class="text-red-500">*</span></label>
                    <input type="text" name="barcode_value" id="barcodeInput" required autofocus
                        class="form-input font-mono text-lg @error('barcode_value') border-red-400 @enderror"
                        placeholder="Scan atau ketik barcode...">
                    @error('barcode_value')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Tipe Scan <span class="text-red-500">*</span></label>
                    <select name="scan_type" required class="form-select">
                        <option value="check" {{ old('scan_type','check')==='check'    ? 'selected' : '' }}>Check /
                            Pengecekan</option>
                        <option value="stock_in" {{ old('scan_type')==='stock_in' ? 'selected' : '' }}>Stok Masuk
                        </option>
                        <option value="stock_out" {{ old('scan_type')==='stock_out'? 'selected' : '' }}>Stok Keluar
                        </option>
                        <option value="transfer" {{ old('scan_type')==='transfer' ? 'selected' : '' }}>Transfer</option>
                        <option value="purchase" {{ old('scan_type')==='purchase' ? 'selected' : '' }}>Pembelian
                        </option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Gudang</label>
                    <select name="warehouse_id" class="form-select">
                        <option value="">— Opsional —</option>
                        @foreach($warehouses as $w)
                        <option value="{{ $w->id }}" {{ old('warehouse_id') == $w->id ? 'selected' : '' }}>
                            {{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="card-body border-t flex justify-end gap-3">
                <a href="{{ route('superadmin.barcodes.index') }}" class="btn-secondary btn">Log</a>
                <button type="submit" class="btn-primary btn">🔍 Scan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-focus barcode input
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('barcodeInput').focus();
});
</script>
@endpush
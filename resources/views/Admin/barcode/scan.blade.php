@extends('layouts.admin')
@section('title', 'Scan Barcode')
@section('content')

<div class="admin-page-head">
    <h2>Scan Barcode</h2>
</div>

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif

<div class="admin-card admin-card-pad" style="max-width:560px;margin-bottom:20px;">
    <form action="{{ route('admin.barcodes.do-scan') }}" method="POST">
        @csrf
        <div class="admin-form-grid" style="margin-bottom:16px;">
            <div>
                <label class="admin-label">Kode Barcode / SKU</label>
                <input type="text" name="barcode_value" required autofocus class="admin-input"
                    placeholder="Scan atau ketik kode...">
            </div>
            <div>
                <label class="admin-label">Jenis Scan</label>
                <select name="scan_type" required class="admin-select">
                    <option value="stock_in">Stok Masuk</option>
                    <option value="stock_out">Stok Keluar</option>
                    <option value="transfer">Transfer</option>
                    <option value="check">Cek Stok</option>
                    <option value="purchase">Pembelian</option>
                </select>
            </div>
            <div>
                <label class="admin-label">Gudang (opsional)</label>
                <select name="warehouse_id" class="admin-select">
                    <option value="">— Pilih Gudang —</option>
                    @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <button type="submit" class="btn-primary ripple">Scan</button>
    </form>
</div>

@if(session('scan_result'))
@php $result = session('scan_result'); @endphp
<div class="admin-card admin-card-pad" style="max-width:560px;">
    <h3 style="margin-top:0;">Hasil Scan</h3>
    <div class="admin-detail-grid">
        <div class="admin-detail-item">
            <p class="admin-label">Produk</p>
            <p>{{ $result['product']->name ?? '-' }}</p>
        </div>
        <div class="admin-detail-item">
            <p class="admin-label">SKU</p>
            <p class="cell-mono">{{ $result['product']->sku ?? '-' }}</p>
        </div>
        @if($result['stockInfo'])
        <div class="admin-detail-item">
            <p class="admin-label">Stok di Gudang Terpilih</p>
            <p>{{ $result['stockInfo']['quantity'] }}</p>
        </div>
        @endif
    </div>
</div>
@endif
@endsection
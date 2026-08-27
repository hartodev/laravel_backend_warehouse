@extends('layouts.admin')
@section('title', 'Buat Transfer Stok')
@section('content')

<div class="admin-page-head">
    <h2>Buat Request Transfer Stok</h2>
</div>

@if ($errors->any())
<div class="admin-alert admin-alert-error">
    @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif

<div class="admin-alert" style="background:#fffbe6;border-color:#ffe58f;">
    <i class="lucide-info"></i> Gudang asal harus sesuai dengan gudang tempat Anda bertugas.
</div>

<form action="{{ route('admin.stock-transfers.store') }}" method="POST" id="transfer-form">
    @csrf

    <div class="admin-form-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:20px;">
        <div>
            <label class="admin-label">Dari Gudang</label>
            <select name="from_warehouse_id" required class="admin-select">
                <option value="">Pilih Gudang Asal</option>
                @foreach($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}" @selected(old('from_warehouse_id')==$warehouse->
                    id)>{{ $warehouse->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="admin-label">Ke Gudang</label>
            <select name="to_warehouse_id" required class="admin-select">
                <option value="">Pilih Gudang Tujuan</option>
                @foreach($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}" @selected(old('to_warehouse_id')==$warehouse->
                    id)>{{ $warehouse->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="admin-label">Tanggal Transfer</label>
            <input type="date" name="transfer_date" value="{{ old('transfer_date', date('Y-m-d')) }}" required
                class="admin-input">
        </div>
        <div>
            <label class="admin-label">Estimasi Tiba</label>
            <input type="date" name="expected_arrival" value="{{ old('expected_arrival') }}" class="admin-input">
        </div>
        <div style="grid-column:span 2;">
            <label class="admin-label">Catatan</label>
            <textarea name="notes" class="admin-textarea">{{ old('notes') }}</textarea>
        </div>
    </div>

    <div class="admin-card admin-table-wrap" style="margin-bottom:12px;">
        <table class="admin-table" id="items-table">
            <thead>
                <tr>
                    <th style="width:50%;">Produk</th>
                    <th>Qty Diminta</th>
                    <th class="cell-actions"></th>
                </tr>
            </thead>
            <tbody id="items-body"></tbody>
        </table>
    </div>
    <button type="button" class="btn-outline" id="add-item" style="margin-bottom:20px;"><i class="lucide-plus"></i>
        Tambah Item</button>

    <div class="admin-form-actions" style="justify-content:flex-end;">
        <a href="{{ route('admin.stock-transfers.index') }}" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary ripple">Kirim Request</button>
    </div>
</form>

<template id="item-row-template">
    <tr class="item-row">
        <td>
            <select name="items[__i__][product_id]" class="admin-select" required>
                <option value="">Pilih Produk</option>
                @foreach($products as $product)
                <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }}) — {{ $product->unit }}
                </option>
                @endforeach
            </select>
        </td>
        <td><input type="number" min="1" name="items[__i__][quantity_requested]" class="admin-input" required></td>
        <td class="cell-actions"><button type="button" class="admin-link text-danger remove-item"
                style="background:none;border:none;cursor:pointer;">Hapus</button></td>
    </tr>
</template>

<script>
let itemIndex = 0;
const tbody = document.getElementById('items-body');
const template = document.getElementById('item-row-template');

function addItemRow() {
    const html = template.innerHTML.replaceAll('__i__', itemIndex++);
    const wrapper = document.createElement('tbody');
    wrapper.innerHTML = html;
    const row = wrapper.firstElementChild;
    tbody.appendChild(row);
    row.querySelector('.remove-item').addEventListener('click', () => row.remove());
}

document.getElementById('add-item').addEventListener('click', addItemRow);
addItemRow();
</script>
@endsection
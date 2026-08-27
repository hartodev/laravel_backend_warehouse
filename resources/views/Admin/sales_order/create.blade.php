@extends('layouts.admin')
@section('title', 'Buat Sales Order')
@section('content')

<div class="admin-page-head"><h2>Buat Sales Order</h2></div>

@if ($errors->any())
<div class="admin-alert admin-alert-error">
    @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
</div>
@endif

<form action="{{ route('admin.sales-orders.store') }}" method="POST" id="so-form">
    @csrf

    <div class="admin-form-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:20px;">
        <div>
            <label class="admin-label">Nama Customer</label>
            <input type="text" name="customer_name" value="{{ old('customer_name') }}" required class="admin-input">
        </div>
        <div>
            <label class="admin-label">Gudang</label>
            <select name="warehouse_id" required class="admin-select">
                <option value="">Pilih Gudang</option>
                @foreach($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}" @selected(old('warehouse_id') == $warehouse->id)>{{ $warehouse->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="admin-label">Metode Pembayaran</label>
            <select name="payment_method" required class="admin-select">
                <option value="">Pilih Metode</option>
                <option value="cash" @selected(old('payment_method')==='cash')>Cash</option>
                <option value="transfer" @selected(old('payment_method')==='transfer')>Transfer</option>
                <option value="credit" @selected(old('payment_method')==='credit')>Credit</option>
            </select>
        </div>
        <div style="grid-column:span 2;">
            <label class="admin-label">Alamat Customer</label>
            <textarea name="customer_address" class="admin-textarea">{{ old('customer_address') }}</textarea>
        </div>
        <div>
            <label class="admin-label">Tanggal Order</label>
            <input type="date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" required class="admin-input">
        </div>
        <div>
            <label class="admin-label">Jatuh Tempo</label>
            <input type="date" name="due_date" value="{{ old('due_date') }}" class="admin-input">
        </div>
        <div>
            <label class="admin-label">Pajak (%)</label>
            <input type="number" step="0.01" min="0" max="100" name="tax_percent" id="tax_percent" value="{{ old('tax_percent', 0) }}" class="admin-input">
        </div>
        <div>
            <label class="admin-label">Diskon (Rp)</label>
            <input type="number" step="0.01" min="0" name="discount_amount" id="discount_amount" value="{{ old('discount_amount', 0) }}" class="admin-input">
        </div>
        <div style="grid-column:span 2;">
            <label class="admin-label">Catatan</label>
            <textarea name="notes" class="admin-textarea">{{ old('notes') }}</textarea>
        </div>
    </div>

    <div class="admin-card admin-table-wrap" style="margin-bottom:12px;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width:36%;">Produk</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                    <th class="cell-actions"></th>
                </tr>
            </thead>
            <tbody id="items-body"></tbody>
        </table>
    </div>
    <button type="button" class="btn-outline" id="add-item" style="margin-bottom:20px;"><i class="lucide-plus"></i> Tambah Item</button>

    <div class="admin-detail-grid" style="margin-bottom:20px;max-width:320px;margin-left:auto;">
        <div class="admin-detail-item"><p class="admin-label">Subtotal</p><p id="sum-subtotal">Rp0</p></div>
        <div class="admin-detail-item"><p class="admin-label">Pajak</p><p id="sum-tax">Rp0</p></div>
        <div class="admin-detail-item"><p class="admin-label">Diskon</p><p id="sum-discount">Rp0</p></div>
        <div class="admin-detail-item"><p class="admin-label"><strong>Total</strong></p><p id="sum-total"><strong>Rp0</strong></p></div>
    </div>

    <div class="admin-form-actions" style="justify-content:flex-end;">
        <a href="{{ route('admin.sales-orders.index') }}" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary ripple">Simpan SO</button>
    </div>
</form>

<template id="item-row-template">
    <tr class="item-row">
        <td>
            <select name="items[__i__][product_id]" class="admin-select product-select" required>
                <option value="">Pilih Produk</option>
                @foreach($products as $product)
                <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}">{{ $product->name }} ({{ $product->sku }}) — {{ $product->unit }}</option>
                @endforeach
            </select>
        </td>
        <td><input type="number" min="1" name="items[__i__][quantity]" class="admin-input qty-input" required></td>
        <td><input type="number" step="0.01" min="0" name="items[__i__][price]" class="admin-input price-input" required></td>
        <td class="cell-mono row-subtotal">Rp0</td>
        <td class="cell-actions"><button type="button" class="admin-link text-danger remove-item" style="background:none;border:none;cursor:pointer;">Hapus</button></td>
    </tr>
</template>

<script>
let itemIndex = 0;
const tbody = document.getElementById('items-body');
const template = document.getElementById('item-row-template');

function formatRp(n) {
    return 'Rp' + Number(n || 0).toLocaleString('id-ID', { maximumFractionDigits: 0 });
}

function addItemRow() {
    const html = template.innerHTML.replaceAll('__i__', itemIndex++);
    const wrapper = document.createElement('tbody');
    wrapper.innerHTML = html;
    const row = wrapper.firstElementChild;
    tbody.appendChild(row);

    row.querySelector('.product-select').addEventListener('change', function () {
        const opt = this.selectedOptions[0];
        row.querySelector('.price-input').value = opt ? (opt.dataset.price || 0) : 0;
        recalc();
    });
    row.querySelectorAll('.qty-input, .price-input').forEach(el => el.addEventListener('input', recalc));
    row.querySelector('.remove-item').addEventListener('click', function () {
        row.remove();
        recalc();
    });
}

function recalc() {
    let subtotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const rowSub = qty * price;
        row.querySelector('.row-subtotal').textContent = formatRp(rowSub);
        subtotal += rowSub;
    });

    const taxPercent = parseFloat(document.getElementById('tax_percent').value) || 0;
    const discountAmount = parseFloat(document.getElementById('discount_amount').value) || 0;
    const tax = subtotal * (taxPercent / 100);
    const total = subtotal + tax - discountAmount;

    document.getElementById('sum-subtotal').textContent = formatRp(subtotal);
    document.getElementById('sum-tax').textContent = formatRp(tax);
    document.getElementById('sum-discount').textContent = formatRp(discountAmount);
    document.getElementById('sum-total').innerHTML = '<strong>' + formatRp(total) + '</strong>';
}

document.getElementById('add-item').addEventListener('click', addItemRow);
document.getElementById('tax_percent').addEventListener('input', recalc);
document.getElementById('discount_amount').addEventListener('input', recalc);

addItemRow();
</script>
@endsection

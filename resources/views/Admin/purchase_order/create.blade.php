@extends('layouts.admin')
@section('title', 'Buat Purchase Order')
@section('content')

<div class="admin-page-head">
    <h2>Buat Purchase Order</h2>
</div>

@if ($errors->any())
<div class="admin-alert admin-alert-error">
    <div>
        @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
        @endforeach
    </div>
</div>
@endif

<form action="{{ route('admin.purchase-orders.store') }}" method="POST" id="po-form">
    @csrf
    <div class="admin-form-grid" style="grid-template-columns:repeat(2,1fr);margin-bottom:20px;">
        <div>
            <label class="admin-label">Supplier</label>
            <select name="supplier_id" required class="admin-select">
                <option value="">— Pilih Supplier —</option>
                @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}" @selected(old('supplier_id')==$supplier->id)>{{ $supplier->name }}
                    ({{ $supplier->code }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="admin-label">Gudang Tujuan</label>
            <select name="warehouse_id" required class="admin-select">
                <option value="">— Pilih Gudang —</option>
                @foreach ($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}" @selected(old('warehouse_id')==$warehouse->
                    id)>{{ $warehouse->name }} ({{ $warehouse->code }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="admin-label">Tanggal Order</label>
            <input type="date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" required
                class="admin-input">
        </div>
        <div>
            <label class="admin-label">Tanggal Diharapkan</label>
            <input type="date" name="expected_date" value="{{ old('expected_date') }}" class="admin-input">
        </div>
        <div>
            <label class="admin-label">Syarat Pembayaran</label>
            <input type="text" name="payment_term" value="{{ old('payment_term') }}" class="admin-input"
                placeholder="mis. Net 30">
        </div>
        <div>
            <label class="admin-label">Pajak (%)</label>
            <input type="number" step="0.01" name="tax_percent" id="tax_percent" value="{{ old('tax_percent', 0) }}"
                min="0" max="100" class="admin-input">
        </div>
        <div>
            <label class="admin-label">Diskon (Rp)</label>
            <input type="number" step="0.01" name="discount_amount" id="discount_amount"
                value="{{ old('discount_amount', 0) }}" min="0" class="admin-input">
        </div>
        <div style="grid-column:span 2;">
            <label class="admin-label">Catatan</label>
            <textarea name="notes" class="admin-textarea">{{ old('notes') }}</textarea>
        </div>
    </div>

    <div class="admin-card admin-card-pad" style="margin-bottom:20px;">
        <h3 style="margin-top:0;">Item Purchase Order</h3>
        <div id="items-wrap"></div>
        <button type="button" class="btn-outline" onclick="addItemRow()" style="margin-top:10px;"><i
                class="lucide-plus"></i> Tambah Item</button>

        <div style="margin-top:16px;text-align:right;font-size:14px;">
            <p>Subtotal: <span id="subtotal-display" class="cell-mono">Rp 0</span></p>
            <p>Pajak: <span id="tax-display" class="cell-mono">Rp 0</span></p>
            <p>Diskon: <span id="discount-display" class="cell-mono">Rp 0</span></p>
            <p style="font-weight:700;">Total: <span id="total-display" class="cell-mono">Rp 0</span></p>
        </div>
    </div>

    <div class="admin-form-actions" style="justify-content:flex-end;">
        <a href="{{ route('admin.purchase-orders.index') }}" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary ripple">Simpan PO</button>
    </div>
</form>

<script>
const products = @json($products);
let itemIndex = 0;

function addItemRow() {
    const wrap = document.getElementById('items-wrap');
    const row = document.createElement('div');
    row.className = 'admin-form-grid';
    row.style = 'grid-template-columns:2fr 1fr 1fr 1fr auto;gap:10px;margin-bottom:10px;align-items:end;';
    row.innerHTML = `
        <div>
            <label class="admin-label">Produk</label>
            <select name="items[${itemIndex}][product_id]" required class="admin-select product-select" onchange="fillPrice(this)">
                <option value="">— Pilih Produk —</option>
                ${products.map(p => `<option value="${p.id}" data-price="${p.purchase_price}">${p.name} (${p.sku})</option>`).join('')}
            </select>
        </div>
        <div>
            <label class="admin-label">Qty</label>
            <input type="number" name="items[${itemIndex}][quantity_ordered]" min="1" required class="admin-input qty-input" oninput="calcTotal()">
        </div>
        <div>
            <label class="admin-label">Harga Satuan</label>
            <input type="number" step="0.01" name="items[${itemIndex}][unit_price]" min="0" required class="admin-input price-input" oninput="calcTotal()">
        </div>
        <div>
            <label class="admin-label">Diskon (%)</label>
            <input type="number" step="0.01" name="items[${itemIndex}][discount_percent]" min="0" max="100" value="0" class="admin-input disc-input" oninput="calcTotal()">
        </div>
        <div>
            <button type="button" class="btn-danger" onclick="this.closest('.admin-form-grid').remove(); calcTotal();">Hapus</button>
        </div>
    `;
    wrap.appendChild(row);
    itemIndex++;
}

function fillPrice(select) {
    const opt = select.options[select.selectedIndex];
    const row = select.closest('.admin-form-grid');
    row.querySelector('.price-input').value = opt.dataset.price || 0;
    calcTotal();
}

function calcTotal() {
    let subtotal = 0;
    document.querySelectorAll('#items-wrap .admin-form-grid').forEach(row => {
        const qty = parseFloat(row.querySelector('.qty-input')?.value) || 0;
        const price = parseFloat(row.querySelector('.price-input')?.value) || 0;
        const disc = parseFloat(row.querySelector('.disc-input')?.value) || 0;
        subtotal += qty * price * (1 - disc / 100);
    });
    const taxPercent = parseFloat(document.getElementById('tax_percent').value) || 0;
    const discountAmount = parseFloat(document.getElementById('discount_amount').value) || 0;
    const taxAmount = subtotal * (taxPercent / 100);
    const total = subtotal + taxAmount - discountAmount;

    document.getElementById('subtotal-display').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
    document.getElementById('tax-display').textContent = 'Rp ' + taxAmount.toLocaleString('id-ID');
    document.getElementById('discount-display').textContent = 'Rp ' + discountAmount.toLocaleString('id-ID');
    document.getElementById('total-display').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

document.getElementById('tax_percent').addEventListener('input', calcTotal);
document.getElementById('discount_amount').addEventListener('input', calcTotal);

addItemRow();
</script>
@endsection
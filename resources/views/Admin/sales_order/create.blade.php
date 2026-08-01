@extends('layouts.admin')
@section('title', 'Buat Sales Order')
@section('content')

<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-900">Buat Sales Order</h1>
    <p class="text-sm text-gray-500">Isi detail pesanan penjualan.</p>
</div>

@if ($errors->any())
<div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm">
    <ul class="list-disc list-inside">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('admin.sales-orders.store') }}" method="POST" id="soForm">
    @csrf

    <div class="card mb-5 p-5 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Customer</label>
            <input type="text" name="customer_name" value="{{ old('customer_name') }}" required
                class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gudang</label>
            <select name="warehouse_id" required
                class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="">-- Pilih Gudang --</option>
                @foreach($warehouses as $w)
                <option value="{{ $w->id }}" @selected(old('warehouse_id')==$w->id)>{{ $w->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
            <input type="text" name="notes" value="{{ old('notes') }}"
                class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
        </div>
    </div>

    {{-- Item SO --}}
    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-semibold text-gray-900">Item Pesanan</h3>
            <button type="button" onclick="addRow()" class="btn btn-secondary text-xs">+ Tambah Item</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-3 py-2 text-left">Produk</th>
                        <th class="px-3 py-2 text-right w-28">Qty</th>
                        <th class="px-3 py-2 text-right w-40">Harga Satuan</th>
                        <th class="px-3 py-2 text-right w-40">Subtotal</th>
                        <th class="px-3 py-2 w-10"></th>
                    </tr>
                </thead>
                <tbody id="itemRows"></tbody>
            </table>
        </div>

        <div class="flex justify-end mt-4 pt-4 border-t border-gray-100">
            <div class="text-right">
                <p class="text-sm text-gray-500">Total</p>
                <p class="text-xl font-bold text-gray-900" id="grandTotal">Rp 0</p>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-2 mt-5">
        <a href="{{ route('admin.sales-orders.index') }}" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Sales Order</button>
    </div>
</form>

@push('scripts')
<script>
const products = @json($products); // [{id, name, sell_price, unit}]
let rowIndex = 0;

function formatRupiah(num) {
    return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
}

function addRow() {
    const tbody = document.getElementById('itemRows');
    const i = rowIndex++;
    const options = products.map(p => `<option value="${p.id}" data-price="${p.sell_price}">${p.name}</option>`).join(
        '');

    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td class="px-3 py-2">
            <select name="items[${i}][product_id]" class="product-select w-full rounded-lg border-gray-300 text-sm" required>
                <option value="">-- Pilih Produk --</option>
                ${options}
            </select>
        </td>
        <td class="px-3 py-2">
            <input type="number" name="items[${i}][quantity]" min="1" value="1" class="qty-input w-full rounded-lg border-gray-300 text-sm text-right" required>
        </td>
        <td class="px-3 py-2">
            <input type="number" name="items[${i}][price]" min="0" step="0.01" class="price-input w-full rounded-lg border-gray-300 text-sm text-right" required>
        </td>
        <td class="px-3 py-2 text-right subtotal-cell">Rp 0</td>
        <td class="px-3 py-2 text-center">
            <button type="button" class="text-red-500 hover:text-red-700" onclick="this.closest('tr').remove(); recalc();">✕</button>
        </td>
    `;
    tbody.appendChild(tr);

    const select = tr.querySelector('.product-select');
    const priceInput = tr.querySelector('.price-input');
    select.addEventListener('change', () => {
        const opt = select.selectedOptions[0];
        priceInput.value = opt?.dataset.price || 0;
        recalc();
    });
    tr.querySelector('.qty-input').addEventListener('input', recalc);
    priceInput.addEventListener('input', recalc);
}

function recalc() {
    let grand = 0;
    document.querySelectorAll('#itemRows tr').forEach(tr => {
        const qty = parseFloat(tr.querySelector('.qty-input')?.value || 0);
        const price = parseFloat(tr.querySelector('.price-input')?.value || 0);
        const subtotal = qty * price;
        tr.querySelector('.subtotal-cell').textContent = formatRupiah(subtotal);
        grand += subtotal;
    });
    document.getElementById('grandTotal').textContent = formatRupiah(grand);
}

// Start with one row
addRow();
</script>
@endpush
@endsection
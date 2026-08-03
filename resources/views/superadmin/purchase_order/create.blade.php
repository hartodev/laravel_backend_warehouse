@extends('layouts.app')
@section('title', 'Buat Purchase Order')
@section('breadcrumb')
<a href="{{ route('superadmin.purchase-orders.index') }}" class="hover:text-primary-700">Purchase Order</a>
<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
</svg>
<span class="text-gray-700 font-medium">Buat Baru</span>
@endsection

@section('content')
<form method="POST" action="{{ route('superadmin.purchase-orders.store') }}" x-data="poForm()" @submit="prepareSubmit">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Left: Header --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="card">
                <div class="card-header">
                    <h2 class="font-semibold text-gray-900">Informasi PO</h2>
                </div>
                <div class="card-body grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Supplier <span class="text-red-500">*</span></label>
                        <select name="supplier_id" required class="form-select">
                            <option value="">— Pilih Supplier —</option>
                            @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
                                {{ $sup->name }}</option>
                            @endforeach
                        </select>
                        @error('supplier_id')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Gudang Tujuan <span class="text-red-500">*</span></label>
                        <select name="warehouse_id" required class="form-select">
                            <option value="">— Pilih Gudang —</option>
                            @foreach($warehouses as $w)
                            <option value="{{ $w->id }}" {{ old('warehouse_id') == $w->id ? 'selected' : '' }}>
                                {{ $w->name }}</option>
                            @endforeach
                        </select>
                        @error('warehouse_id')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Tanggal Order <span class="text-red-500">*</span></label>
                        <input type="date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" required
                            class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Tgl. Diharapkan Tiba</label>
                        <input type="date" name="expected_date" value="{{ old('expected_date') }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Metode Pembayaran</label>
                        <select name="payment_method" class="form-select">
                            <option value="">— Pilih —</option>
                            <option value="cash">Cash</option>
                            <option value="transfer">Transfer</option>
                            <option value="credit">Kredit</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">PPN (%)</label>
                        <input type="number" name="tax_percent" x-model.number="taxPercent"
                            value="{{ old('tax_percent', 0) }}" min="0" max="100" step="0.1" class="form-input">
                    </div>
                    <div class="col-span-2">
                        <label class="form-label">Diskon (Rp)</label>
                        <input type="number" name="discount_amount" x-model.number="discount"
                            value="{{ old('discount_amount', 0) }}" min="0" class="form-input">
                    </div>
                    <div class="col-span-2">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" rows="2" class="form-textarea">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Items --}}
            <div class="card">
                <div class="card-header">
                    <h2 class="font-semibold text-gray-900">Item Produk</h2>
                    <button type="button" @click="addRow" class="btn-primary btn btn-sm">+ Tambah Baris</button>
                </div>
                <div class="table-wrap rounded-none border-0">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="w-64">Produk</th>
                                <th class="w-24 text-right">Qty</th>
                                <th class="w-36 text-right">Harga Satuan</th>
                                <th class="w-36 text-right">Subtotal</th>
                                <th class="w-10"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(row, idx) in rows" :key="idx">
                                <tr>
                                    <td>
                                        <select :name="'items['+idx+'][product_id]'" x-model="row.product_id"
                                            @change="setPrice(row)" required class="form-select text-sm py-1.5">
                                            <option value="">— Pilih Produk —</option>
                                            @foreach($products as $p)
                                            <option value="{{ $p->id }}" data-price="{{ $p->purchase_price }}">
                                                {{ $p->name }} ({{ $p->sku }})</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" :name="'items['+idx+'][quantity]'" x-model.number="row.qty"
                                            @input="calcRow(row)" min="1" required
                                            class="form-input text-right text-sm py-1.5 w-20">
                                    </td>
                                    <td>
                                        <input type="number" :name="'items['+idx+'][price]'" x-model.number="row.price"
                                            @input="calcRow(row)" min="0" required
                                            class="form-input text-right text-sm py-1.5 w-32">
                                    </td>
                                    <td class="text-right font-medium text-sm pr-4"
                                        x-text="'Rp '+row.total.toLocaleString('id-ID')"></td>
                                    <td>
                                        <button type="button" @click="removeRow(idx)"
                                            class="text-red-400 hover:text-red-600">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right: Summary --}}
        <div class="lg:col-span-1">
            <div class="card sticky top-20">
                <div class="card-header">
                    <h3 class="font-semibold text-gray-900">Ringkasan</h3>
                </div>
                <div class="card-body space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="font-medium" x-text="'Rp '+subtotal.toLocaleString('id-ID')"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">PPN (<span x-text="taxPercent"></span>%)</span>
                        <span x-text="'Rp '+tax.toLocaleString('id-ID')"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Diskon</span>
                        <span class="text-red-600" x-text="'- Rp '+discount.toLocaleString('id-ID')"></span>
                    </div>
                    <div class="border-t border-gray-100 pt-3 flex justify-between font-bold text-base">
                        <span>Total</span>
                        <span class="text-primary-700" x-text="'Rp '+total.toLocaleString('id-ID')"></span>
                    </div>
                </div>
                <div class="card-body border-t pt-3">
                    <button type="submit" class="btn-primary btn w-full justify-center">Buat Purchase Order</button>
                    <a href="{{ route('superadmin.purchase-orders.index') }}"
                        class="btn-secondary btn w-full justify-center mt-2">Batal</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
const productPrices = {
    @foreach($products as $p)
    "{{ $p->id }}": {
        {
            $p - > purchase_price ?? 0
        }
    },
    @endforeach
};

function poForm() {
    return {
        rows: [{
            product_id: '',
            qty: 1,
            price: 0,
            total: 0
        }],
        taxPercent: {
            {
                old('tax_percent', 0)
            }
        },
        discount: {
            {
                old('discount_amount', 0)
            }
        },
        get subtotal() {
            return this.rows.reduce((s, r) => s + r.total, 0);
        },
        get tax() {
            return Math.round(this.subtotal * this.taxPercent / 100);
        },
        get total() {
            return this.subtotal + this.tax - this.discount;
        },
        addRow() {
            this.rows.push({
                product_id: '',
                qty: 1,
                price: 0,
                total: 0
            });
        },
        removeRow(idx) {
            if (this.rows.length > 1) this.rows.splice(idx, 1);
        },
        calcRow(row) {
            row.total = row.qty * row.price;
        },
        setPrice(row) {
            row.price = productPrices[row.product_id] || 0;
            row.total = row.qty * row.price;
        },
        prepareSubmit() {
            /* normal submit */ }
    };
}
</script>
@endpush

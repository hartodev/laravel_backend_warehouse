@extends('layouts.admin')

@section('title', 'Buat Purchase Order')

@section('content')
    <div class="admin-page-head">
        <h2>Buat Purchase Order</h2>
        <a href="{{ route('admin.purchase-orders.index') }}" class="btn-ghost">← Kembali</a>
    </div>

    @if ($errors->any())
        <div class="admin-alert admin-alert-error">
            <ul style="margin:0;padding-left:18px;">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.purchase-orders.store') }}" class="admin-card admin-card-pad">
        @csrf

        <div class="admin-form-grid" style="margin-bottom:18px;">
            <div>
                <label class="admin-label">Supplier *</label>
                <select name="supplier_id" required class="admin-select">
                    <option value="">-- Pilih Supplier --</option>
                    @foreach ($suppliers as $sup)
                        <option value="{{ $sup->id }}" @selected(old('supplier_id') == $sup->id)>{{ $sup->name }} ({{ $sup->code }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="admin-label">Gudang Tujuan *</label>
                <select name="warehouse_id" required class="admin-select">
                    <option value="">-- Pilih Gudang --</option>
                    @foreach ($warehouses as $wh)
                        <option value="{{ $wh->id }}" @selected(old('warehouse_id') == $wh->id)>{{ $wh->name }} ({{ $wh->code }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="admin-label">Tanggal Order *</label>
                <input type="date" name="order_date" value="{{ old('order_date', now()->toDateString()) }}" required class="admin-input">
            </div>
            <div>
                <label class="admin-label">Estimasi Tiba</label>
                <input type="date" name="expected_date" value="{{ old('expected_date') }}" class="admin-input">
            </div>
            <div>
                <label class="admin-label">Termin Pembayaran</label>
                <input type="text" name="payment_term" value="{{ old('payment_term') }}" placeholder="Net 30, COD, dsb" class="admin-input">
            </div>
            <div>
                <label class="admin-label">Pajak (%)</label>
                <input type="number" name="tax_percent" value="{{ old('tax_percent', 0) }}" min="0" max="100" step="0.01" class="admin-input" id="tax_percent">
            </div>
            <div>
                <label class="admin-label">Diskon Nominal</label>
                <input type="number" name="discount_amount" value="{{ old('discount_amount', 0) }}" min="0" step="0.01" class="admin-input" id="discount_amount">
            </div>
            <div class="span-2">
                <label class="admin-label">Catatan</label>
                <textarea name="notes" rows="2" class="admin-textarea">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="admin-section-title" style="border-top:1px solid var(--border);padding-top:18px;">Item Barang</div>

        <div class="admin-table-wrap" style="border:1px solid var(--border);border-radius:var(--r-sm);margin-bottom:12px;">
            <table class="admin-table" id="item-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th style="width:110px">Qty</th>
                        <th style="width:150px">Harga Satuan</th>
                        <th style="width:100px">Diskon %</th>
                        <th style="width:60px"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="item-row">
                        <td>
                            <select name="items[0][product_id]" required class="admin-select product-select">
                                <option value="">-- Pilih Produk --</option>
                                @foreach ($products as $p)
                                    <option value="{{ $p->id }}" data-price="{{ $p->purchase_price }}">{{ $p->name }} ({{ $p->sku }})</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="items[0][quantity_ordered]" min="1" required class="admin-input"></td>
                        <td><input type="number" name="items[0][unit_price]" min="0" step="0.01" required class="admin-input price-input"></td>
                        <td><input type="number" name="items[0][discount_percent]" min="0" max="100" step="0.01" value="0" class="admin-input"></td>
                        <td><button type="button" class="btn-ghost btn-sm remove-row">✕</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <button type="button" id="add-row" class="btn-ghost btn-sm"><i data-lucide="plus"></i> Tambah Item</button>

        <div class="admin-form-actions">
            <button type="submit" class="btn-primary ripple">Buat Purchase Order</button>
        </div>
    </form>

    <template id="row-template">
        <tr class="item-row">
            <td>
                <select name="items[__INDEX__][product_id]" required class="admin-select product-select">
                    <option value="">-- Pilih Produk --</option>
                    @foreach ($products as $p)
                        <option value="{{ $p->id }}" data-price="{{ $p->purchase_price }}">{{ $p->name }} ({{ $p->sku }})</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" name="items[__INDEX__][quantity_ordered]" min="1" required class="admin-input"></td>
            <td><input type="number" name="items[__INDEX__][unit_price]" min="0" step="0.01" required class="admin-input price-input"></td>
            <td><input type="number" name="items[__INDEX__][discount_percent]" min="0" max="100" step="0.01" value="0" class="admin-input"></td>
            <td><button type="button" class="btn-ghost btn-sm remove-row">✕</button></td>
        </tr>
    </template>

    @push('scripts')
    <script>
        let rowIndex = 1;
        document.getElementById('add-row').addEventListener('click', () => {
            const tpl = document.getElementById('row-template').innerHTML.replaceAll('__INDEX__', rowIndex++);
            document.querySelector('#item-table tbody').insertAdjacentHTML('beforeend', tpl);
            lucide.createIcons();
        });
        document.querySelector('#item-table').addEventListener('click', (e) => {
            if (e.target.closest('.remove-row')) {
                const rows = document.querySelectorAll('.item-row');
                if (rows.length > 1) e.target.closest('tr').remove();
            }
        });
        // Auto-isi harga beli default saat produk dipilih
        document.querySelector('#item-table').addEventListener('change', (e) => {
            if (e.target.classList.contains('product-select')) {
                const opt = e.target.selectedOptions[0];
                const priceInput = e.target.closest('tr').querySelector('.price-input');
                if (opt && opt.dataset.price) priceInput.value = opt.dataset.price;
            }
        });
    </script>
    @endpush
@endsection

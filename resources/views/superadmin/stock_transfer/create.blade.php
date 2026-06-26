@extends('superadmin.layouts.app')
@section('title','Buat Transfer Stok')
@section('breadcrumb')
<a href="{{ route('superadmin.stock-transfers.index') }}" class="hover:text-primary-700">Transfer Stok</a>
<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-700 font-medium">Buat Transfer</span>
@endsection

@section('content')
<form method="POST" action="{{ route('superadmin.stock-transfers.store') }}" x-data="trfForm()">
@csrf
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 space-y-4">
        <div class="card">
            <div class="card-header"><h2 class="font-semibold text-gray-900">Informasi Transfer</h2></div>
            <div class="card-body grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Dari Gudang <span class="text-red-500">*</span></label>
                    <select name="from_warehouse_id" required class="form-select" x-model="fromWarehouse">
                        <option value="">— Pilih —</option>
                        @foreach($warehouses as $w)
                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                        @endforeach
                    </select>
                    @error('from_warehouse_id')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Ke Gudang <span class="text-red-500">*</span></label>
                    <select name="to_warehouse_id" required class="form-select">
                        <option value="">— Pilih —</option>
                        @foreach($warehouses as $w)
                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                        @endforeach
                    </select>
                    @error('to_warehouse_id')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Tanggal Transfer <span class="text-red-500">*</span></label>
                    <input type="date" name="transfer_date" value="{{ old('transfer_date', date('Y-m-d')) }}" required class="form-input">
                </div>
                <div>
                    <label class="form-label">Estimasi Tiba</label>
                    <input type="date" name="expected_arrival" value="{{ old('expected_arrival') }}" class="form-input">
                </div>
                <div class="col-span-2">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" rows="2" class="form-textarea">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="font-semibold text-gray-900">Item Transfer</h2>
                <button type="button" @click="addRow" class="btn-primary btn btn-sm">+ Tambah Baris</button>
            </div>
            <div class="table-wrap rounded-none border-0">
                <table class="data-table">
                    <thead><tr><th>Produk</th><th class="text-right">Qty</th><th class="w-10"></th></tr></thead>
                    <tbody>
                        <template x-for="(row,idx) in rows" :key="idx">
                        <tr>
                            <td>
                                <select :name="'items['+idx+'][product_id]'" x-model="row.product_id" required class="form-select text-sm py-1.5">
                                    <option value="">— Pilih Produk —</option>
                                    @foreach($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->sku }})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" :name="'items['+idx+'][quantity_requested]'" x-model.number="row.qty" min="1" required class="form-input text-right text-sm py-1.5 w-24">
                            </td>
                            <td>
                                <button type="button" @click="removeRow(idx)" class="text-red-400 hover:text-red-600">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </td>
                        </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="lg:col-span-1">
        <div class="card sticky top-20 p-5 space-y-4">
            <p class="font-semibold text-gray-900">Ringkasan</p>
            <p class="text-sm text-gray-500">Total item: <span class="font-bold text-gray-900" x-text="rows.length"></span> produk</p>
            <p class="text-sm text-gray-500">Total qty: <span class="font-bold text-gray-900" x-text="rows.reduce((s,r)=>s+r.qty,0)"></span></p>
            <div class="border-t border-gray-100 pt-4 space-y-2">
                <button type="submit" class="btn-primary btn w-full justify-center">Buat Transfer</button>
                <a href="{{ route('superadmin.stock-transfers.index') }}" class="btn-secondary btn w-full justify-center">Batal</a>
            </div>
        </div>
    </div>
</div>
</form>
@endsection

@push('scripts')
<script>
function trfForm() {
    return {
        fromWarehouse: '',
        rows: [{ product_id: '', qty: 1 }],
        addRow()    { this.rows.push({ product_id: '', qty: 1 }); },
        removeRow(i){ if (this.rows.length > 1) this.rows.splice(i, 1); },
    };
}
</script>
@endpush

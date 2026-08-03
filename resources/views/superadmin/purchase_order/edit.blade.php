{{-- resources/views/superadmin/purchase_order/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Purchase Order')
@section('breadcrumb')
<a href="{{ route('superadmin.purchase-orders.index') }}" class="hover:text-primary-700">Purchase Order</a>
<svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
</svg>
<a href="{{ route('superadmin.purchase-orders.show', $purchaseOrder) }}" class="hover:text-primary-700">
    {{ $purchaseOrder->po_number }}
</a>
<svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
</svg>
<span class="font-medium text-gray-700">Edit</span>
@endsection

@section('content')
<form method="POST" action="{{ route('superadmin.purchase-orders.update', $purchaseOrder) }}" x-data="poEditForm()">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- LEFT: Info PO --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Info Dasar (readonly) --}}
            <div class="card">
                <div class="card-header">
                    <h2 class="font-semibold text-gray-900 dark:text-white">Informasi PO</h2>
                    <span class="badge badge-warning text-xs">{{ strtoupper($purchaseOrder->status) }}</span>
                </div>
                <div class="card-body grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">No. PO</label>
                        <input type="text" value="{{ $purchaseOrder->po_number }}" disabled
                            class="form-input bg-gray-50 text-gray-500 cursor-not-allowed font-mono">
                    </div>
                    <div>
                        <label class="form-label">Supplier</label>
                        <input type="text" value="{{ $purchaseOrder->supplier->name ?? '—' }}" disabled
                            class="form-input bg-gray-50 text-gray-500 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="form-label">Gudang</label>
                        <input type="text" value="{{ $purchaseOrder->warehouse->name ?? '—' }}" disabled
                            class="form-input bg-gray-50 text-gray-500 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="form-label">Tanggal Order</label>
                        <input type="text"
                            value="{{ \Carbon\Carbon::parse($purchaseOrder->order_date)->isoFormat('D MMMM Y') }}"
                            disabled class="form-input bg-gray-50 text-gray-500 cursor-not-allowed">
                    </div>
                </div>
            </div>

            {{-- Field yang bisa diedit --}}
            <div class="card">
                <div class="card-header">
                    <h2 class="font-semibold text-gray-900 dark:text-white">Detail yang Dapat Diubah</h2>
                </div>
                <div class="card-body space-y-4">

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Estimasi Tiba</label>
                            <input type="date" name="expected_date"
                                value="{{ old('expected_date', $purchaseOrder->expected_date ? \Carbon\Carbon::parse($purchaseOrder->expected_date)->format('Y-m-d') : '') }}"
                                class="form-input @error('expected_date') border-red-400 @enderror">
                            @error('expected_date')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label">Metode Pembayaran</label>
                            <select name="payment_method"
                                class="form-select @error('payment_method') border-red-400 @enderror">
                                <option value="">— Pilih —</option>
                                <option value="cash"
                                    {{ old('payment_method', $purchaseOrder->payment_method) === 'cash'     ? 'selected' : '' }}>
                                    Cash</option>
                                <option value="transfer"
                                    {{ old('payment_method', $purchaseOrder->payment_method) === 'transfer' ? 'selected' : '' }}>
                                    Transfer</option>
                                <option value="credit"
                                    {{ old('payment_method', $purchaseOrder->payment_method) === 'credit'   ? 'selected' : '' }}>
                                    Credit</option>
                            </select>
                            @error('payment_method')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Diskon (Rp)</label>
                        <div class="relative">
                            <span
                                class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-medium">Rp</span>
                            <input type="number" name="discount_amount" min="0"
                                value="{{ old('discount_amount', $purchaseOrder->discount_amount ?? 0) }}"
                                class="form-input pl-9 @error('discount_amount') border-red-400 @enderror"
                                placeholder="0">
                        </div>
                        @error('discount_amount')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" rows="3"
                            class="form-input resize-none @error('notes') border-red-400 @enderror"
                            placeholder="Catatan tambahan...">{{ old('notes', $purchaseOrder->notes) }}</textarea>
                        @error('notes')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- Daftar Item (readonly) --}}
            <div class="card">
                <div class="card-header">
                    <h2 class="font-semibold text-gray-900 dark:text-white">Item Pesanan</h2>
                    <span class="text-xs text-gray-400">{{ $purchaseOrder->items->count() }} item</span>
                </div>
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Produk</th>
                                <th>SKU</th>
                                <th class="text-right">Qty</th>
                                <th class="text-right">Harga Satuan</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($purchaseOrder->items as $i => $item)
                            <tr>
                                <td class="text-gray-400 text-xs">{{ $i + 1 }}</td>
                                <td class="font-medium">{{ $item->product->name ?? '—' }}</td>
                                <td class="font-mono text-xs text-gray-500">{{ $item->product->sku ?? '—' }}</td>
                                <td class="text-right">{{ number_format($item->quantity_ordered) }}</td>
                                <td class="text-right">Rp {{ number_format($item->unit_price) }}</td>
                                <td class="text-right font-semibold">Rp {{ number_format($item->subtotal) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-body border-t">
                    <p class="text-xs text-gray-400">
                        <svg class="w-3.5 h-3.5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Item pesanan tidak dapat diubah setelah PO dibuat.
                    </p>
                </div>
            </div>

        </div>

        {{-- RIGHT: Ringkasan & Aksi --}}
        <div class="space-y-5">

            {{-- Ringkasan Harga --}}
            <div class="card">
                <div class="card-header">
                    <h2 class="font-semibold text-gray-900 dark:text-white">Ringkasan</h2>
                </div>
                <div class="card-body space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="font-medium">Rp {{ number_format($purchaseOrder->subtotal) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Pajak ({{ $purchaseOrder->tax_percent }}%)</span>
                        <span class="font-medium">Rp {{ number_format($purchaseOrder->tax_amount) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Diskon</span>
                        <span class="font-medium text-red-500">- Rp
                            {{ number_format($purchaseOrder->discount_amount) }}</span>
                    </div>
                    <div class="border-t pt-3 flex justify-between">
                        <span class="font-semibold">Total</span>
                        <span class="font-bold text-lg text-primary-700">Rp
                            {{ number_format($purchaseOrder->total_amount) }}</span>
                    </div>
                    <p class="text-xs text-gray-400">* Total akan berubah sesuai diskon yang diinput</p>
                </div>
            </div>

            {{-- Info --}}
            <div class="card">
                <div class="card-header">
                    <h2 class="font-semibold text-gray-900 dark:text-white">Info</h2>
                </div>
                <div class="card-body space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Dibuat oleh</span>
                        <span>{{ $purchaseOrder->createdBy->name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Dibuat</span>
                        <span>{{ $purchaseOrder->created_at?->isoFormat('D MMM Y') ?? '—' }}</span>
                    </div>
                    @if($purchaseOrder->approvedBy)
                    <div class="flex justify-between">
                        <span class="text-gray-400">Disetujui oleh</span>
                        <span>{{ $purchaseOrder->approvedBy->name }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Aksi --}}
            <div class="card">
                <div class="card-body space-y-2">
                    <button type="submit" class="btn btn-primary w-full justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('superadmin.purchase-orders.show', $purchaseOrder) }}"
                        class="btn btn-secondary w-full justify-center">
                        Batal
                    </a>
                </div>
            </div>

            {{-- Danger Zone --}}
            @if($purchaseOrder->status === 'draft')
            <div class="card border border-red-200">
                <div class="card-header">
                    <h2 class="font-semibold text-red-600">Danger Zone</h2>
                </div>
                <div class="card-body">
                    <p class="text-xs text-gray-500 mb-3">Hapus PO ini secara permanen. Aksi ini tidak bisa dibatalkan.
                    </p>
                    <button type="button" onclick="document.getElementById('modal-delete').classList.remove('hidden')"
                        class="btn btn-danger btn-sm w-full justify-center">
                        Hapus PO
                    </button>
                </div>
            </div>
            @endif

        </div>
    </div>
</form>


{{-- Modal Hapus --}}
@if($purchaseOrder->status === 'draft')
<div id="modal-delete" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
    onclick="if(event.target===this) this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 p-6">
        <div class="flex items-start gap-4 mb-5">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                </svg>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-900">Hapus Purchase Order?</h3>
                <p class="text-sm text-gray-500 mt-1">
                    PO <strong>{{ $purchaseOrder->po_number }}</strong> akan dihapus permanen beserta semua item-nya.
                </p>
            </div>
        </div>
        <form method="POST" action="{{ route('superadmin.purchase-orders.destroy', $purchaseOrder) }}">
            @csrf
            @method('DELETE')
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-delete').classList.add('hidden')"
                    class="btn btn-secondary btn-sm px-4">Batal</button>
                <button type="submit" class="btn btn-danger btn-sm px-4">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
function poEditForm() {
    return {}
}
</script>
@endpush

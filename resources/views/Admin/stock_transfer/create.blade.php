@extends('layouts.admin')

@section('title', 'Buat Request Transfer')
@section('page-title', 'Buat Request Transfer Stok')

@section('content')
<div class="card-panel" style="max-width:720px;">
    @if(!$user->warehouse_id)
    <div class="alert alert-warning small">
        Akun Anda belum ditugaskan ke gudang manapun. Hubungi Superadmin untuk mengatur <code>warehouse_id</code> Anda
        sebelum bisa membuat request transfer.
    </div>
    @endif

    <form method="POST" action="{{ route('admin.stock-transfers.store') }}">
        @csrf
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label small">Gudang Asal</label>
                {{-- Gudang asal WAJIB sama dengan gudang milik Admin login (dicek juga di controller) --}}
                <select name="from_warehouse_id" class="form-select @error('from_warehouse_id') is-invalid @enderror"
                    required>
                    @foreach ($warehouses as $w)
                    <option value="{{ $w->id }}"
                        {{ (old('from_warehouse_id', $user->warehouse_id) == $w->id) ? 'selected' : '' }}
                        {{ $w->id !== $user->warehouse_id ? 'disabled' : '' }}>
                        {{ $w->name }} ({{ $w->code }})
                    </option>
                    @endforeach
                </select>
                <div class="form-text">Hanya bisa memilih gudang Anda sendiri.</div>
                @error('from_warehouse_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label small">Gudang Tujuan</label>
                <select name="to_warehouse_id" class="form-select @error('to_warehouse_id') is-invalid @enderror"
                    required>
                    <option value="">-- Pilih Gudang Tujuan --</option>
                    @foreach ($warehouses as $w)
                    @if($w->id !== $user->warehouse_id)
                    <option value="{{ $w->id }}" {{ old('to_warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}
                        ({{ $w->code }})</option>
                    @endif
                    @endforeach
                </select>
                @error('to_warehouse_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label small">Tanggal Transfer</label>
                <input type="date" name="transfer_date"
                    class="form-control @error('transfer_date') is-invalid @enderror"
                    value="{{ old('transfer_date', now()->format('Y-m-d')) }}" required>
                @error('transfer_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label small">Perkiraan Tiba (opsional)</label>
                <input type="date" name="expected_arrival" class="form-control" value="{{ old('expected_arrival') }}">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label small">Catatan</label>
            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
        </div>

        <hr>
        <label class="form-label small fw-bold">Item yang Ditransfer</label>
        <div id="item-rows">
            <div class="row g-2 mb-2 item-row">
                <div class="col-7">
                    <select name="items[0][product_id]" class="form-select form-select-sm" required>
                        <option value="">-- Pilih Produk --</option>
                        @foreach ($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->sku }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-3">
                    <input type="number" name="items[0][quantity_requested]" class="form-control form-control-sm"
                        placeholder="Qty" min="1" required>
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-row"><i
                            class="lucide-trash-2"></i></button>
                </div>
            </div>
        </div>
        <button type="button" id="add-row" class="btn btn-sm btn-outline-primary mb-4">
            <i class="lucide-plus"></i> Tambah Item
        </button>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Buat Request</button>
            <a href="{{ route('admin.stock-transfers.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
let rowIndex = 1;
const productOptions =
    `@foreach ($products as $p)<option value="{{ $p->id }}">{{ $p->name }} ({{ $p->sku }})</option>@endforeach`;

document.getElementById('add-row').addEventListener('click', () => {
    const wrapper = document.getElementById('item-rows');
    const row = document.createElement('div');
    row.className = 'row g-2 mb-2 item-row';
    row.innerHTML = `
      <div class="col-7">
        <select name="items[${rowIndex}][product_id]" class="form-select form-select-sm" required>
          <option value="">-- Pilih Produk --</option>
          ${productOptions}
        </select>
      </div>
      <div class="col-3">
        <input type="number" name="items[${rowIndex}][quantity_requested]" class="form-control form-control-sm" placeholder="Qty" min="1" required>
      </div>
      <div class="col-2">
        <button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="lucide-trash-2"></i></button>
      </div>`;
    wrapper.appendChild(row);
    rowIndex++;
});

document.getElementById('item-rows').addEventListener('click', (e) => {
    if (e.target.closest('.remove-row')) {
        const rows = document.querySelectorAll('.item-row');
        if (rows.length > 1) e.target.closest('.item-row').remove();
    }
});
</script>
@endpush
@endsection
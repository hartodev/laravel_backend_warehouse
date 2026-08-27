@extends('layouts.admin')
@section('title', 'Stok')
@section('content')

<div class="admin-page-head">
    <h2>Stok</h2>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('admin.stocks.low-stock') }}" class="btn-outline"><i class="lucide-alert-triangle"></i> Stok
            Menipis</a>
        <button type="button" class="btn-primary ripple"
            onclick="document.getElementById('manual-in-modal').classList.remove('hidden')"><i class="lucide-plus"></i>
            Input Stok Manual</button>
    </div>
</div>

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif
@if ($errors->any())
<div class="admin-alert admin-alert-error">
    @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
</div>
@endif

<form method="GET" class="admin-filter-bar">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / SKU produk..."
        class="admin-input" style="max-width:240px;">
    <select name="warehouse_id" class="admin-select" style="max-width:180px;">
        <option value="">Semua Gudang</option>
        @foreach($warehouses as $warehouse)
        <option value="{{ $warehouse->id }}" @selected(request('warehouse_id')==$warehouse->id)>{{ $warehouse->name }}
        </option>
        @endforeach
    </select>
    <label style="display:flex;align-items:center;gap:6px;">
        <input type="checkbox" name="low_stock" value="1" @checked(request('low_stock'))> Stok menipis saja
    </label>
    <button class="btn-outline">Filter</button>
</form>

<div class="admin-card admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>SKU</th>
                <th>Gudang</th>
                <th>Qty</th>
                <th>Satuan</th>
                <th>Min. Stok</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($stocks as $stock)
            <tr>
                <td>{{ $stock->product->name ?? '-' }}</td>
                <td class="cell-mono">{{ $stock->product->sku ?? '-' }}</td>
                <td class="cell-muted">{{ $stock->warehouse->name ?? '-' }}</td>
                <td class="cell-mono">{{ $stock->quantity }}</td>
                <td class="cell-muted">{{ $stock->product->unit ?? '-' }}</td>
                <td class="cell-mono cell-muted">{{ $stock->product->min_stock ?? '-' }}</td>
                <td>
                    @if(($stock->product->min_stock ?? null) !== null && $stock->quantity <= $stock->product->min_stock)
                        <span class="admin-badge admin-badge-danger">Menipis</span>
                        @else
                        <span class="admin-badge admin-badge-success">Aman</span>
                        @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="cell-empty">Belum ada data stok.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-pagination">{{ $stocks->appends(request()->query())->links() }}</div>

<div id="manual-in-modal" class="admin-modal-overlay {{ $errors->any() ? '' : 'hidden' }}">
    <div class="admin-card" style="padding:20px;max-width:420px;width:100%;">
        <h3 style="margin-bottom:12px;">Input Stok Manual</h3>
        <form action="{{ route('admin.stocks.manual-in') }}" method="POST">
            @csrf
            <div style="margin-bottom:12px;">
                <label class="admin-label">Gudang</label>
                <select name="warehouse_id" required class="admin-select">
                    <option value="">Pilih Gudang</option>
                    @foreach($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}" @selected(old('warehouse_id')==$warehouse->
                        id)>{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom:12px;">
                <label class="admin-label">Produk</label>
                <select name="product_id" required class="admin-select">
                    <option value="">Pilih Produk</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}" @selected(old('product_id')==$product->id)>{{ $product->name }}
                        ({{ $product->sku }}) — {{ $product->unit }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom:12px;">
                <label class="admin-label">Jumlah</label>
                <input type="number" min="1" name="quantity" value="{{ old('quantity') }}" required class="admin-input">
            </div>
            <div style="margin-bottom:16px;">
                <label class="admin-label">Catatan</label>
                <textarea name="note" class="admin-textarea" placeholder="Opsional">{{ old('note') }}</textarea>
            </div>
            <div class="admin-form-actions" style="justify-content:flex-end;">
                <button type="button" class="btn-secondary"
                    onclick="document.getElementById('manual-in-modal').classList.add('hidden')">Batal</button>
                <button type="submit" class="btn-primary ripple">Simpan</button>
            </div>
        </form>
    </div>
</div>

<style>
/* Overlay modal: display diatur di sini, BUKAN inline style,
   supaya class .hidden bisa menimpanya */
.admin-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, .4);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 50;
}

/* Specificity .admin-modal-overlay.hidden > .admin-modal-overlay saja,
   jadi aturan ini selalu menang saat class "hidden" ditambahkan */
.admin-modal-overlay.hidden {
    display: none;
}

.hidden {
    display: none;
}
</style>
<script>
// Tutup modal kalau klik area gelap di luar box
document.getElementById('manual-in-modal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});

// Tutup modal dengan tombol Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.getElementById('manual-in-modal').classList.add('hidden');
    }
});
</script>
@endsection
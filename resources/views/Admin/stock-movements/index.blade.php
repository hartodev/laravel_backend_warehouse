@extends('layouts.admin')
@section('title', 'Pergerakan Stok')
@section('content')

<div class="admin-page-head">
    <h2>Pergerakan Stok</h2>
    <button type="button" class="btn-primary ripple"
        onclick="document.getElementById('add-movement-modal').classList.remove('hidden')"><i class="lucide-plus"></i>
        Catat Pergerakan</button>
</div>

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
@endif
@if ($errors->any())
<div class="admin-alert admin-alert-error">
    @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
</div>
@endif

<form method="GET" class="admin-filter-bar">
    <select name="warehouse_id" class="admin-select" style="max-width:180px;">
        <option value="">Semua Gudang</option>
        @foreach($warehouses as $warehouse)
        <option value="{{ $warehouse->id }}" @selected(request('warehouse_id')==$warehouse->id)>{{ $warehouse->name }}
        </option>
        @endforeach
    </select>
    <select name="type" class="admin-select" style="max-width:150px;">
        <option value="">Semua Tipe</option>
        <option value="in" @selected(request('type')==='in' )>Masuk</option>
        <option value="out" @selected(request('type')==='out' )>Keluar</option>
        <option value="adjustment" @selected(request('type')==='adjustment' )>Penyesuaian</option>
    </select>
    <input type="date" name="date_from" value="{{ request('date_from') }}" class="admin-input" style="max-width:150px;">
    <input type="date" name="date_to" value="{{ request('date_to') }}" class="admin-input" style="max-width:150px;">
    <button class="btn-outline">Filter</button>
</form>

<div class="admin-card admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Produk</th>
                <th>Gudang</th>
                <th>Tipe</th>
                <th>Qty</th>
                <th>Sebelum → Sesudah</th>
                <th>Oleh</th>
                <th class="cell-actions">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($movements as $movement)
            <tr>
                <td class="cell-muted">{{ $movement->created_at->format('d M Y H:i') }}</td>
                <td>{{ $movement->product->name ?? '-' }}</td>
                <td class="cell-muted">{{ $movement->warehouse->name ?? '-' }}</td>
                <td>
                    @php
                    $typeBadge = ['in' => 'admin-badge-success', 'out' => 'admin-badge-danger', 'adjustment' =>
                    'admin-badge-warning'];
                    $typeLabel = ['in' => 'Masuk', 'out' => 'Keluar', 'adjustment' => 'Penyesuaian'];
                    @endphp
                    <span
                        class="admin-badge {{ $typeBadge[$movement->type] ?? 'admin-badge-muted' }}">{{ $typeLabel[$movement->type] ?? $movement->type }}</span>
                </td>
                <td class="cell-mono">{{ $movement->quantity }} {{ $movement->product->unit ?? '' }}</td>
                <td class="cell-mono cell-muted">{{ $movement->quantity_before }} → {{ $movement->quantity_after }}</td>
                <td class="cell-muted">{{ $movement->createdBy->name ?? '-' }}</td>
                <td class="cell-actions">
                    <a href="{{ route('admin.stock-movements.show', $movement) }}" class="admin-link">Detail</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="cell-empty">Belum ada pergerakan stok.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-pagination">{{ $movements->appends(request()->query())->links() }}</div>

<div id="add-movement-modal" class="admin-modal-overlay {{ $errors->any() ? '' : 'hidden' }}">
    <div class="admin-card" style="padding:20px;max-width:420px;width:100%;">
        <h3 style="margin-bottom:12px;">Catat Pergerakan Stok</h3>

        @if ($errors->any())
        <div class="admin-alert admin-alert-error" style="margin-bottom:12px;">
            @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
        @endif

        <form action="{{ route('admin.stock-movements.store') }}" method="POST">
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
                <input type="number" name="product_id" value="{{ old('product_id') }}" required class="admin-input"
                    placeholder="ID Produk">
            </div>
            <div style="margin-bottom:12px;">
                <label class="admin-label">Tipe</label>
                <select name="type" id="movement-type" required class="admin-select">
                    <option value="in" @selected(old('type','in')==='in' )>Masuk</option>
                    <option value="out" @selected(old('type')==='out' )>Keluar</option>
                    <option value="adjustment" @selected(old('type')==='adjustment' )>Penyesuaian</option>
                </select>
            </div>
            <div id="adjustment-direction-wrap" class="{{ old('type') === 'adjustment' ? '' : 'hidden' }}"
                style="margin-bottom:12px;">
                <label class="admin-label">Arah Penyesuaian</label>
                <select name="adjustment_type" class="admin-select">
                    <option value="in" @selected(old('adjustment_type')==='in' )>Tambah</option>
                    <option value="out" @selected(old('adjustment_type')==='out' )>Kurangi</option>
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
                    onclick="document.getElementById('add-movement-modal').classList.add('hidden')">Batal</button>
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
document.getElementById('movement-type').addEventListener('change', function() {
    document.getElementById('adjustment-direction-wrap').classList.toggle('hidden', this.value !==
        'adjustment');
});

// Tutup modal kalau klik area gelap di luar box
document.getElementById('add-movement-modal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});

// Tutup modal dengan tombol Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.getElementById('add-movement-modal').classList.add('hidden');
    }
});
</script>
@endsection
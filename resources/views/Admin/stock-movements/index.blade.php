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
                <td class="cell-muted">{{ $movement->created_at?->format('d M Y H:i') ?? '-' }}</td>
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
            <div style="margin-bottom:12px; position:relative;">
                <label class="admin-label">Produk</label>
                <input type="text" id="product-search" class="admin-input" placeholder="Ketik nama atau SKU produk..."
                    autocomplete="off" value="{{ old('product_name') }}">
                <input type="hidden" name="product_id" id="product-id" value="{{ old('product_id') }}">
                <div id="product-search-results" class="product-search-dropdown hidden"></div>
            </div>

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
                <select name="adjustment_type" id="adjustment-type" class="admin-select">
                    <option value="in" @selected(old('adjustment_type')==='in' )>Tambah</option>
                    <option value="out" @selected(old('adjustment_type')==='out' )>Kurangi</option>
                </select>
            </div>

            <div style="margin-bottom:12px;">
                <label class="admin-label">Jumlah</label>
                <input type="number" min="1" name="quantity" value="{{ old('quantity') }}" required class="admin-input">
            </div>

            {{-- Field baru: hanya muncul kalau tipe-nya "keluar" --}}
            <div id="taken-by-wrap" class="hidden" style="margin-bottom:12px;">
                <label class="admin-label">Diambil Oleh (Nama)</label>
                <input type="text" name="taken_by_name" value="{{ old('taken_by_name') }}" class="admin-input"
                    placeholder="Nama karyawan yang mengambil barang">
            </div>
            <div id="taken-by-division-wrap" class="hidden" style="margin-bottom:12px;">
                <label class="admin-label">Divisi / Departemen</label>
                <input type="text" name="taken_by_division" value="{{ old('taken_by_division') }}" class="admin-input"
                    placeholder="Contoh: Produksi, Maintenance, Gudang">
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

{{--
    Data produk sudah di-JSON-encode di controller (variabel $productsJson),
    jadi baris ini murni {{ }} tanpa fn()/-> apapun. Tidak ada lagi
yang bisa dirusak auto-formatter di sini.
--}}
<div id="product-data" class="hidden" data-products="{{ $productsJson }}"></div>

<style>
.admin-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, .4);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 50;
}

.admin-modal-overlay.hidden {
    display: none;
}

.hidden {
    display: none;
}

.product-search-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    max-height: 220px;
    overflow-y: auto;
    background: #fff;
    border: 1px solid #d1d5db;
    border-top: none;
    border-radius: 0 0 6px 6px;
    z-index: 60;
    box-shadow: 0 4px 10px rgba(0, 0, 0, .08);
}

.product-search-item {
    padding: 8px 12px;
    cursor: pointer;
    font-size: 14px;
}

.product-search-item:hover,
.product-search-item.active {
    background: #f3f4f6;
}

.product-search-empty {
    padding: 8px 12px;
    font-size: 13px;
    color: #9ca3af;
}
</style>

<script>
document.getElementById('movement-type').addEventListener('change', function() {
    document.getElementById('adjustment-direction-wrap').classList.toggle('hidden', this.value !==
        'adjustment');
});

document.getElementById('add-movement-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.getElementById('add-movement-modal').classList.add('hidden');
    }
});

var productDataEl = document.getElementById('product-data');
var productList = JSON.parse(productDataEl.getAttribute('data-products'));

var searchInput = document.getElementById('product-search');
var hiddenId = document.getElementById('product-id');
var resultsBox = document.getElementById('product-search-results');

function buildResultsHtml(list) {
    if (list.length === 0) {
        return '<div class="product-search-empty">Produk tidak ditemukan</div>';
    }
    var html = '';
    for (var j = 0; j < list.length; j++) {
        var item = list[j];
        var safeName = item.name.replace(/"/g, '&quot;');
        html += '<div class="product-search-item" data-id="' + item.id + '" data-name="' + safeName + ' (' + item.sku +
            ')">';
        html += item.name + ' <span class="cell-muted">(' + item.sku + ')</span>';
        html += '</div>';
    }
    return html;
}

function renderResults(query) {
    var q = query.trim().toLowerCase();
    var matches;

    if (!q) {
        // Query kosong: tampilkan SEMUA produk (bisa di-scroll)
        matches = productList;
    } else {
        matches = [];
        for (var i = 0; i < productList.length; i++) {
            var p = productList[i];
            var nameMatch = p.name.toLowerCase().indexOf(q) !== -1;
            var skuMatch = p.sku.toLowerCase().indexOf(q) !== -1;
            if (nameMatch || skuMatch) {
                matches.push(p);
            }
        }
    }

    resultsBox.innerHTML = buildResultsHtml(matches);
    resultsBox.classList.remove('hidden');
}

searchInput.addEventListener('input', function() {
    hiddenId.value = '';
    renderResults(this.value);
});

searchInput.addEventListener('focus', function() {
    // Selalu tampilkan list (kosong = semua produk) saat input di-klik/focus
    renderResults(this.value);
});

resultsBox.addEventListener('click', function(e) {
    var item = e.target.closest('.product-search-item');
    if (!item) {
        return;
    }
    hiddenId.value = item.getAttribute('data-id');
    searchInput.value = item.getAttribute('data-name');
    resultsBox.classList.add('hidden');
});

document.addEventListener('click', function(e) {
    var insideSearch = e.target.closest('#product-search');
    var insideResults = e.target.closest('#product-search-results');
    if (!insideSearch && !insideResults) {
        resultsBox.classList.add('hidden');
    }
});
var typeSelect = document.getElementById('movement-type');
var adjTypeSelect = document.getElementById('adjustment-type');
var adjWrap = document.getElementById('adjustment-direction-wrap');
var takenByWrap = document.getElementById('taken-by-wrap');
var takenByDivisionWrap = document.getElementById('taken-by-division-wrap');

function updateFormVisibility() {
    var isAdjustment = typeSelect.value === 'adjustment';
    adjWrap.classList.toggle('hidden', !isAdjustment);

    var isOut = typeSelect.value === 'out' || (isAdjustment && adjTypeSelect.value === 'out');

    takenByWrap.classList.toggle('hidden', !isOut);
    takenByDivisionWrap.classList.toggle('hidden', !isOut);
}

typeSelect.addEventListener('change', updateFormVisibility);
adjTypeSelect.addEventListener('change', updateFormVisibility);
updateFormVisibility();
</script>
@endsection
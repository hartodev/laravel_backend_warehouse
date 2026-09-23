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
                <th>Aksi</th>
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
                <td>
                    <button type="button" class="btn-outline btn-edit-stock"
                        data-update-url="{{ route('admin.stocks.update', $stock->id) }}"
                        data-product="{{ $stock->product->name ?? '-' }}" data-quantity="{{ $stock->quantity }}">
                        <i class="lucide-pencil"></i> Edit
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="cell-empty">Belum ada data stok.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-pagination">{{ $stocks->appends(request()->query())->links() }}</div>

{{-- ── Modal: Input Stok Manual (sudah ada sebelumnya) ── --}}
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
                @php($selectedProduct = $products->firstWhere('id', (int) old('product_id')))
                <div class="searchable-select" id="product-select">
                    <input type="text" id="product-search-input" class="admin-input"
                        placeholder="Cari nama / SKU produk..." autocomplete="off"
                        value="{{ $selectedProduct?->name }}">
                    <input type="hidden" name="product_id" id="product-select-value" value="{{ old('product_id') }}">
                    <div class="searchable-select-list" id="product-select-list" hidden>
                        @foreach($products as $product)
                        <div class="searchable-select-option" data-id="{{ $product->id }}"
                            data-search="{{ strtolower($product->name.' '.$product->sku) }}">
                            <strong>{{ $product->name }}</strong>
                            <span class="cell-muted">({{ $product->sku }}) — {{ $product->unit }}</span>
                        </div>
                        @endforeach
                        <div class="searchable-select-empty" hidden>Produk tidak ditemukan.</div>
                    </div>
                </div>
                <p class="searchable-select-error" id="product-select-error" hidden>Silakan pilih produk dari daftar.
                </p>
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

{{-- ── Modal: Koreksi Qty Stok (BARU — untuk method update()) ── --}}
<div id="edit-stock-modal" class="admin-modal-overlay hidden">
    <div class="admin-card" style="padding:20px;max-width:380px;width:100%;">
        <h3 style="margin-bottom:12px;">Koreksi Stok — <span id="edit-stock-product"></span></h3>
        <form id="edit-stock-form" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="margin-bottom:12px;">
                <label class="admin-label">Qty Baru</label>
                <input type="number" min="0" name="quantity" id="edit-stock-quantity" required class="admin-input">
            </div>
            <div style="margin-bottom:16px;">
                <label class="admin-label">Catatan</label>
                <textarea name="note" class="admin-textarea" placeholder="Alasan koreksi (opsional)"></textarea>
            </div>
            <div class="admin-form-actions" style="justify-content:flex-end;">
                <button type="button" class="btn-secondary"
                    onclick="document.getElementById('edit-stock-modal').classList.add('hidden')">Batal</button>
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

/* ── Combobox pencarian produk (menggantikan <select> panjang) ── */
.searchable-select {
    position: relative;
}

.searchable-select-list {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    max-height: 220px;
    overflow-y: auto;
    background: #fff;
    border: 1px solid var(--border-strong);
    border-radius: var(--r-md);
    box-shadow: var(--shadow-md);
    z-index: 60;
    padding: 4px;
}

.searchable-select-option {
    padding: 8px 10px;
    border-radius: var(--r-sm);
    cursor: pointer;
    font-size: 13.5px;
    display: flex;
    flex-direction: column;
    gap: 2px;
    line-height: 1.3;
}

.searchable-select-option:hover,
.searchable-select-option.is-active {
    background: var(--bg-subtle);
}

.searchable-select-option span {
    font-size: 11.5px;
}

.searchable-select-empty {
    padding: 10px;
    font-size: 13px;
    color: var(--text-muted);
    text-align: center;
}

.searchable-select-error {
    color: var(--accent-red);
    font-size: 12px;
    margin: 6px 0 0;
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
        document.getElementById('edit-stock-modal').classList.add('hidden');
    }
});

// ── Combobox pencarian Produk ──────────────────────────────────
// Menggantikan <select> panjang: user cukup mengetik nama/SKU,
// daftar produk otomatis difilter tanpa harus scroll manual.
(function() {
    var wrapper = document.getElementById('product-select');
    var input = document.getElementById('product-search-input');
    var hiddenInput = document.getElementById('product-select-value');
    var list = document.getElementById('product-select-list');
    var emptyMsg = list.querySelector('.searchable-select-empty');
    var errorMsg = document.getElementById('product-select-error');
    var options = Array.prototype.slice.call(list.querySelectorAll('.searchable-select-option'));

    function openList() {
        filterOptions();
        list.hidden = false;
    }

    function closeList() {
        list.hidden = true;
    }

    function filterOptions() {
        var query = input.value.trim().toLowerCase();
        var visibleCount = 0;
        options.forEach(function(opt) {
            var match = opt.dataset.search.indexOf(query) !== -1;
            opt.hidden = !match;
            if (match) visibleCount++;
        });
        emptyMsg.hidden = visibleCount !== 0;
    }

    function selectOption(opt) {
        hiddenInput.value = opt.dataset.id;
        input.value = opt.querySelector('strong').textContent;
        errorMsg.hidden = true;
        closeList();
    }

    input.addEventListener('focus', openList);
    input.addEventListener('input', function() {
        // Kalau user mengetik ulang setelah sebelumnya sudah pilih produk,
        // anggap pilihan lama batal sampai dia klik salah satu opsi lagi —
        // supaya tidak submit produk yang beda dari teks yang terlihat.
        hiddenInput.value = '';
        openList();
    });

    options.forEach(function(opt) {
        opt.addEventListener('click', function() {
            selectOption(opt);
        });
    });

    document.addEventListener('click', function(e) {
        if (!wrapper.contains(e.target)) closeList();
    });

    // <input type="hidden"> tidak ditegakkan oleh atribut "required" bawaan
    // browser, jadi validasi wajib-pilih dilakukan manual saat submit.
    wrapper.closest('form').addEventListener('submit', function(e) {
        if (!hiddenInput.value) {
            e.preventDefault();
            errorMsg.hidden = false;
            input.focus();
        }
    });
})();

// ── Modal Koreksi Qty Stok (BARU) ──────────────────────────────
(function() {
    var modal = document.getElementById('edit-stock-modal');
    var form = document.getElementById('edit-stock-form');
    var productLabel = document.getElementById('edit-stock-product');
    var quantityInput = document.getElementById('edit-stock-quantity');

    document.querySelectorAll('.btn-edit-stock').forEach(function(btn) {
        btn.addEventListener('click', function() {
            form.action = btn.dataset.updateUrl;
            productLabel.textContent = btn.dataset.product;
            quantityInput.value = btn.dataset.quantity;
            modal.classList.remove('hidden');
        });
    });

    modal.addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('hidden');
    });
})();
</script>
@endsection
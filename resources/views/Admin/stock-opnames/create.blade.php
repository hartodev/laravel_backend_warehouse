@extends('layouts.admin')

@section('title', 'Opname Baru')

@section('content')
    <div class="admin-page-head">
        <h2>Buat Stock Opname</h2>
        <a href="{{ route('admin.stock-opnames.index') }}" class="btn-ghost">← Kembali</a>
    </div>

    @if ($errors->any())
        <div class="admin-alert admin-alert-error">
            <ul style="margin:0;padding-left:18px;">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif
    @if (session('error'))
        <div class="admin-alert admin-alert-error">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.stock-opnames.store') }}" id="opname-form" class="admin-card admin-card-pad">
        @csrf

        <div class="admin-form-grid">
            <div>
                <label class="admin-label">Gudang *</label>
                <select name="warehouse_id" id="warehouse_id" required class="admin-select">
                    <option value="">-- Pilih Gudang --</option>
                    @foreach ($warehouses as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->name }} ({{ $wh->code }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="admin-label">Tanggal Opname *</label>
                <input type="date" name="opname_date" value="{{ old('opname_date', now()->toDateString()) }}" required class="admin-input">
            </div>
            <div class="span-2">
                <label class="admin-label">Scope Produk *</label>
                <div style="display:flex;gap:20px;">
                    <label class="admin-checkbox-label"><input type="radio" name="scope" value="all" checked> Semua Produk</label>
                    <label class="admin-checkbox-label"><input type="radio" name="scope" value="category"> Per Kategori</label>
                    <label class="admin-checkbox-label"><input type="radio" name="scope" value="manual"> Pilih Manual</label>
                </div>
            </div>

            <div class="span-2 scope-panel" id="scope-category" style="display:none;">
                <label class="admin-label">Kategori</label>
                <select name="category_id" class="admin-select">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="span-2 scope-panel" id="scope-manual" style="display:none;">
                <label class="admin-label">Pilih Produk (muncul setelah gudang dipilih)</label>
                <div id="manual-product-list" style="max-height:220px;overflow-y:auto;border:1px solid var(--border);border-radius:var(--r-sm);padding:12px;">
                    <p class="text-muted">Pilih gudang terlebih dahulu.</p>
                </div>
            </div>

            <div class="span-2">
                <label class="admin-label">Catatan</label>
                <textarea name="notes" rows="2" class="admin-textarea">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="admin-form-actions">
            <button type="submit" class="btn-primary ripple">Buat Opname</button>
        </div>
    </form>

    @push('scripts')
    <script>
        const scopeRadios   = document.querySelectorAll('input[name="scope"]');
        const categoryPanel = document.getElementById('scope-category');
        const manualPanel   = document.getElementById('scope-manual');
        const warehouseSel  = document.getElementById('warehouse_id');
        const manualList    = document.getElementById('manual-product-list');

        function togglePanels() {
            const val = document.querySelector('input[name="scope"]:checked').value;
            categoryPanel.style.display = val === 'category' ? 'block' : 'none';
            manualPanel.style.display   = val === 'manual' ? 'block' : 'none';
            if (val === 'manual') loadManualProducts();
        }
        scopeRadios.forEach(r => r.addEventListener('change', togglePanels));
        warehouseSel.addEventListener('change', () => {
            if (document.querySelector('input[name="scope"]:checked').value === 'manual') loadManualProducts();
        });

        function loadManualProducts() {
            const warehouseId = warehouseSel.value;
            if (!warehouseId) {
                manualList.innerHTML = '<p class="text-muted">Pilih gudang terlebih dahulu.</p>';
                return;
            }
            manualList.innerHTML = '<p class="text-muted">Memuat produk...</p>';
            fetch(`{{ route('admin.stock-opnames.products-for-scope') }}?warehouse_id=${warehouseId}`)
                .then(r => r.json())
                .then(res => {
                    if (!res.data.length) {
                        manualList.innerHTML = '<p class="text-muted">Tidak ada produk dengan stok di gudang ini.</p>';
                        return;
                    }
                    manualList.innerHTML = res.data.map(p => `
                        <label class="admin-checkbox-label" style="display:flex;padding:6px 0;">
                            <input type="checkbox" name="product_ids[]" value="${p.id}">
                            <span>${p.name} <span class="cell-muted">(${p.sku}) — stok: ${p.stock}</span></span>
                        </label>
                    `).join('');
                });
        }
    </script>
    @endpush
@endsection

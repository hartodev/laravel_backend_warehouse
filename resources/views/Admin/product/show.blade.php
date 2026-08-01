@extends('layouts.admin')

@section('title', $product->name)

@section('content')
    <div class="admin-page-head">
        <h2>{{ $product->name }}</h2>
        <a href="{{ route('admin.products.edit', $product) }}" class="btn-primary ripple">
            <i data-lucide="pencil"></i> Edit Produk
        </a>
    </div>

    @if (session('success'))
        <div class="admin-alert admin-alert-success"><i data-lucide="check-circle"></i> {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="admin-alert admin-alert-error"><i data-lucide="alert-circle"></i> {{ session('error') }}</div>
    @endif

    {{-- Info Umum --}}
    <div class="admin-card admin-card-pad" style="margin-bottom:20px;">
        <div class="admin-detail-grid">
            <div class="admin-detail-item"><div class="label">SKU</div><div class="value">{{ $product->sku }}</div></div>
            <div class="admin-detail-item"><div class="label">Barcode</div><div class="value">{{ $product->barcode ?? '-' }}</div></div>
            <div class="admin-detail-item"><div class="label">Kategori</div><div class="value">{{ $product->category->name ?? '-' }}</div></div>
            <div class="admin-detail-item"><div class="label">Unit Dasar</div><div class="value">{{ $product->unit }}</div></div>
            <div class="admin-detail-item"><div class="label">Harga Beli</div><div class="value">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</div></div>
            <div class="admin-detail-item"><div class="label">Harga Jual</div><div class="value">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</div></div>
            <div class="admin-detail-item"><div class="label">Stok Minimum</div><div class="value">{{ $product->min_stock }}</div></div>
            <div class="admin-detail-item"><div class="label">Total Stok</div><div class="value">{{ $product->stocks->sum('quantity') }}</div></div>
            <div class="admin-detail-item">
                <div class="label">Status</div>
                <div class="value">
                    <span class="admin-badge {{ $product->is_active ? 'admin-badge-success' : 'admin-badge-muted' }}">
                        {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Stok per Gudang --}}
    <div class="admin-card admin-card-pad" style="margin-bottom:20px;">
        <div class="admin-section-title">Stok per Gudang</div>
        <table class="admin-table">
            <thead><tr><th>Gudang</th><th>Kode</th><th style="text-align:right;">Jumlah</th></tr></thead>
            <tbody>
                @forelse ($product->stocks as $stock)
                    <tr>
                        <td>{{ $stock->warehouse->name ?? '-' }}</td>
                        <td class="cell-mono">{{ $stock->warehouse->code ?? '-' }}</td>
                        <td style="text-align:right;{{ $stock->quantity <= $product->min_stock ? 'color:var(--accent-red);font-weight:600;' : '' }}">
                            {{ $stock->quantity }} {{ $product->unit }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="cell-empty">Belum ada stok di gudang manapun.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Unit Produk --}}
    <div class="admin-card admin-card-pad">
        <div class="admin-section-title">Unit Konversi</div>

        <table class="admin-table" style="margin-bottom:20px;">
            <thead>
                <tr>
                    <th>Nama Unit</th>
                    <th>Nilai Konversi (ke unit dasar)</th>
                    <th>Unit Beli</th>
                    <th>Unit Jual</th>
                    <th class="cell-actions">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($product->units as $unit)
                    <tr>
                        <td>{{ $unit->unit_name }}</td>
                        <td class="cell-muted">1 {{ $unit->unit_name }} = {{ $unit->conversion_value }} {{ $product->unit }}</td>
                        <td>{{ $unit->is_purchase_unit ? '✓' : '-' }}</td>
                        <td>{{ $unit->is_sell_unit ? '✓' : '-' }}</td>
                        <td class="cell-actions">
                            <form action="{{ route('admin.products.units.destroy', [$product, $unit]) }}" method="POST" style="display:inline"
                                  onsubmit="return confirm('Hapus unit {{ $unit->unit_name }}?')">
                                @csrf @method('DELETE')
                                <button class="admin-link admin-link-danger" style="background:none;border:none;cursor:pointer;">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="cell-empty">Belum ada unit konversi selain unit dasar.</td></tr>
                @endforelse
            </tbody>
        </table>

        {{-- Form tambah unit --}}
        <form action="{{ route('admin.products.units.store', $product) }}" method="POST"
              style="display:grid;grid-template-columns:2fr 1fr 1.2fr auto;gap:12px;align-items:end;border-top:1px solid var(--border);padding-top:18px;">
            @csrf
            <div>
                <label class="admin-label">Nama Unit</label>
                <input type="text" name="unit_name" placeholder="dus, box, karton..." required class="admin-input">
            </div>
            <div>
                <label class="admin-label">Nilai Konversi</label>
                <input type="number" step="0.0001" min="0.0001" name="conversion_value" placeholder="12" required class="admin-input">
            </div>
            <div style="display:flex;gap:16px;">
                <label class="admin-checkbox-label"><input type="checkbox" name="is_purchase_unit" value="1"> Beli</label>
                <label class="admin-checkbox-label"><input type="checkbox" name="is_sell_unit" value="1"> Jual</label>
            </div>
            <button class="btn-primary ripple"><i data-lucide="plus"></i> Tambah</button>
        </form>
        @error('unit_name') <p class="admin-input-error">{{ $message }}</p> @enderror
    </div>
@endsection

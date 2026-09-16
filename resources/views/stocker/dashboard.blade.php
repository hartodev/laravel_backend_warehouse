@extends('layouts.stocker')
@section('title', 'Dashboard Stocker')
@section('page-title', 'Dashboard')
@section('content')

<div class="stocker-page-head">
    <div>
        <h2>Dashboard Stocker</h2>
        <p>Halo, <strong>{{ auth()->user()->name ?? 'Stocker' }}</strong> — berikut ringkasan kondisi stok gudang saat
            ini.</p>
    </div>
    <span class="admin-badge admin-badge-info"><i class="lucide-eye"></i> Read Only</span>
</div>

{{-- ── Stat cards ─────────────────────────────────────────── --}}
<div class="stocker-stat-grid">
    <div class="admin-card stocker-stat-card">
        <div class="stocker-stat-icon stocker-stat-icon--blue"><i class="lucide-package"></i></div>
        <div>
            <div class="stocker-stat-label">Total Produk Aktif</div>
            <div class="stocker-stat-value">{{ $stats['total_products'] }}</div>
        </div>
    </div>

    <div class="admin-card stocker-stat-card">
        <div class="stocker-stat-icon stocker-stat-icon--green"><i class="lucide-warehouse"></i></div>
        <div>
            <div class="stocker-stat-label">Total Gudang Aktif</div>
            <div class="stocker-stat-value">{{ $stats['total_warehouses'] }}</div>
        </div>
    </div>

    <div class="admin-card stocker-stat-card">
        <div class="stocker-stat-icon stocker-stat-icon--red"><i class="lucide-alert-triangle"></i></div>
        <div>
            <div class="stocker-stat-label">Produk Stok Menipis</div>
            <div class="stocker-stat-value stocker-stat-value--danger">{{ $lowStocks->count() }}</div>
        </div>
    </div>
</div>

{{-- ── Detail panels ──────────────────────────────────────── --}}
<div class="stocker-panel-grid">

    <div class="admin-card stocker-panel">
        <div class="stocker-panel-head">
            <h3><i class="lucide-alert-triangle" style="color:var(--accent-amber);"></i> Stok Menipis <span
                    class="cell-muted" style="font-weight:500;">(Top 10)</span></h3>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Gudang</th>
                        <th style="text-align:right;">Qty</th>
                        <th style="text-align:right;">Min. Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lowStocks as $stock)
                    <tr>
                        <td>{{ $stock->product->name ?? '-' }}</td>
                        <td class="cell-muted">{{ $stock->warehouse->name ?? '-' }}</td>
                        <td style="text-align:right;">
                            <span class="admin-badge admin-badge-danger">{{ $stock->quantity }}</span>
                        </td>
                        <td class="cell-mono cell-muted" style="text-align:right;">
                            {{ $stock->product->min_stock ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="cell-empty">Tidak ada stok yang menipis. Semua aman 🎉</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <a href="{{ route('stocker.stocks.low-stock') }}" class="btn-outline stocker-panel-cta">
            Lihat semua stok menipis <i class="lucide-arrow-right"></i>
        </a>
    </div>

    <div class="admin-card stocker-panel">
        <div class="stocker-panel-head">
            <h3><i class="lucide-warehouse" style="color:var(--primary-600);"></i> Nilai Stok per Gudang</h3>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Gudang</th>
                        <th style="text-align:right;">Nilai Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockValueByWarehouse as $wh)
                    <tr>
                        <td>{{ $wh->name }}</td>
                        <td class="cell-mono" style="text-align:right;">Rp
                            {{ number_format($wh->stock_value ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="cell-empty">Belum ada data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <a href="{{ route('stocker.stocks.index') }}" class="btn-outline stocker-panel-cta">
            Lihat semua stok <i class="lucide-arrow-right"></i>
        </a>
    </div>

</div>

<style>
.stocker-page-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}

.stocker-page-head h2 {
    font-size: 22px;
    font-weight: 800;
    letter-spacing: -.015em;
    margin: 0 0 4px;
    color: var(--text-primary);
}

.stocker-page-head p {
    margin: 0;
    font-size: 13.5px;
    color: var(--text-secondary);
}

.stocker-stat-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}

@media (max-width: 900px) {
    .stocker-stat-grid {
        grid-template-columns: 1fr;
    }
}

.stocker-stat-card {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 20px;
}

.stocker-stat-icon {
    width: 46px;
    height: 46px;
    border-radius: var(--r-md, 12px);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

.stocker-stat-icon--blue {
    background: var(--primary-50);
    color: var(--primary-600);
}

.stocker-stat-icon--green {
    background: var(--accent-green-bg);
    color: var(--accent-green);
}

.stocker-stat-icon--red {
    background: var(--accent-red-bg);
    color: var(--accent-red);
}

.stocker-stat-label {
    font-size: 12.5px;
    color: var(--text-secondary);
    font-weight: 500;
    margin-bottom: 2px;
}

.stocker-stat-value {
    font-size: 26px;
    font-weight: 800;
    color: var(--text-primary);
    letter-spacing: -.02em;
}

.stocker-stat-value--danger {
    color: var(--accent-red);
}

.stocker-panel-grid {
    display: grid;
    grid-template-columns: 1.15fr 1fr;
    gap: 16px;
    align-items: start;
}

@media (max-width: 900px) {
    .stocker-panel-grid {
        grid-template-columns: 1fr;
    }
}

.stocker-panel {
    padding: 20px;
}

.stocker-panel-head {
    margin-bottom: 14px;
}

.stocker-panel-head h3 {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14.5px;
    font-weight: 700;
    margin: 0;
    color: var(--text-primary);
}

.stocker-panel-cta {
    margin-top: 16px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
}
</style>

@endsection
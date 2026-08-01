@extends('layouts.admin')

@section('title', 'Stok Gudang')

@section('content')
    <div class="admin-page-head">
        <h2>Stok Gudang</h2>
        <button type="button" class="btn-primary ripple" onclick="document.getElementById('manual-in-modal').classList.remove('hidden')">
            <i data-lucide="plus"></i> Input Stok Manual
        </button>
    </div>

    @if (session('success'))
        <div class="admin-alert admin-alert-success"><i data-lucide="check-circle"></i> {{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="admin-alert admin-alert-error">
            <ul style="margin:0;padding-left:18px;">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="GET" class="admin-filter-bar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk / SKU..." class="admin-input" style="max-width:240px;">
        <select name="warehouse_id" onchange="this.form.submit()" class="admin-select" style="max-width:200px;">
            <option value="">Semua Gudang</option>
            @foreach ($warehouses as $wh)
                <option value="{{ $wh->id }}" @selected((string) request('warehouse_id') === (string) $wh->id)>{{ $wh->name }}</option>
            @endforeach
        </select>
        <label class="admin-checkbox-label admin-input" style="width:auto;">
            <input type="checkbox" name="low_stock" value="1" @checked(request('low_stock'))>
            Stok rendah saja
        </label>
        <button class="btn-outline">Filter</button>
    </form>

    <div class="admin-card admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Gudang</th>
                    <th>Jumlah</th>
                    <th>Stok Minimum</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($stocks as $stock)
                    <tr>
                        <td>{{ $stock->product->name ?? '-' }} <span class="cell-muted">({{ $stock->product->sku ?? '-' }})</span></td>
                        <td>{{ $stock->warehouse->name ?? '-' }}</td>
                        <td>{{ $stock->quantity }} {{ $stock->product->unit ?? '' }}</td>
                        <td class="cell-muted">{{ $stock->product->min_stock ?? 0 }}</td>
                        <td>
                            @if ($stock->product && $stock->quantity <= $stock->product->min_stock)
                                <span class="admin-badge admin-badge-danger">Rendah</span>
                            @else
                                <span class="admin-badge admin-badge-success">Aman</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="cell-empty">Belum ada data stok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="admin-pagination">{{ $stocks->links() }}</div>

    {{-- Modal input stok manual --}}
    <div id="manual-in-modal" class="hidden" style="position:fixed;inset:0;background:rgba(15,23,42,0.5);z-index:1000;display:flex;align-items:center;justify-content:center;">
        <div class="admin-card admin-card-pad" style="width:100%;max-width:480px;">
            <div class="admin-section-title">Input Stok Manual</div>
            <form method="POST" action="{{ route('admin.stocks.manual-in') }}">
                @csrf
                <div style="margin-bottom:14px;">
                    <label class="admin-label">Gudang *</label>
                    <select name="warehouse_id" required class="admin-select">
                        <option value="">-- Pilih Gudang --</option>
                        @foreach ($warehouses as $wh)
                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom:14px;">
                    <label class="admin-label">Produk *</label>
                    <select name="product_id" required class="admin-select">
                        <option value="">-- Pilih Produk --</option>
                        @foreach ($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->sku }})</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom:14px;">
                    <label class="admin-label">Jumlah *</label>
                    <input type="number" name="quantity" min="1" required class="admin-input">
                </div>
                <div style="margin-bottom:18px;">
                    <label class="admin-label">Catatan</label>
                    <input type="text" name="note" placeholder="Alasan input manual" class="admin-input">
                </div>
                <div style="display:flex;gap:10px;">
                    <button type="submit" class="btn-primary ripple">Simpan</button>
                    <button type="button" class="btn-ghost" onclick="document.getElementById('manual-in-modal').classList.add('hidden')">Batal</button>
                </div>
            </form>
        </div>
    </div>

    @if ($errors->any())
        @push('scripts')
        <script>document.getElementById('manual-in-modal').classList.remove('hidden');</script>
        @endpush
    @endif
@endsection

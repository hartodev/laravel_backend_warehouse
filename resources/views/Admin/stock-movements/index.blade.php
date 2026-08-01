@extends('layouts.admin')

@section('title', 'Riwayat Pergerakan Stok')

@section('content')
    <div class="admin-page-head">
        <h2>Riwayat Pergerakan Stok</h2>
    </div>

    <form method="GET" class="admin-filter-bar">
        <select name="warehouse_id" onchange="this.form.submit()" class="admin-select" style="max-width:200px;">
            <option value="">Semua Gudang</option>
            @foreach ($warehouses as $wh)
                <option value="{{ $wh->id }}" @selected((string) request('warehouse_id') === (string) $wh->id)>{{ $wh->name }}</option>
            @endforeach
        </select>
        <select name="type" onchange="this.form.submit()" class="admin-select" style="max-width:180px;">
            <option value="">Semua Tipe</option>
            @foreach (['in','out','adjustment','transfer_in','transfer_out'] as $t)
                <option value="{{ $t }}" @selected(request('type') === $t)>{{ ucwords(str_replace('_', ' ', $t)) }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="admin-input" style="max-width:160px;">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="admin-input" style="max-width:160px;">
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
                    <th>Sebelum</th>
                    <th>Sesudah</th>
                    <th>Oleh</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($movements as $m)
                    <tr>
                        <td class="cell-muted">{{ $m->created_at->format('d M Y H:i') }}</td>
                        <td>{{ $m->product->name ?? '-' }}</td>
                        <td>{{ $m->warehouse->name ?? '-' }}</td>
                        <td>
                            <span class="admin-badge {{ str_contains($m->type, 'in') ? 'admin-badge-success' : (str_contains($m->type, 'out') ? 'admin-badge-danger' : 'admin-badge-info') }}">
                                {{ ucwords(str_replace('_', ' ', $m->type)) }}
                            </span>
                        </td>
                        <td>{{ $m->quantity }}</td>
                        <td class="cell-muted">{{ $m->quantity_before }}</td>
                        <td class="cell-muted">{{ $m->quantity_after }}</td>
                        <td>{{ $m->createdBy->name ?? '-' }}</td>
                        <td class="cell-muted">{{ $m->note ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="cell-empty">Belum ada pergerakan stok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="admin-pagination">{{ $movements->links() }}</div>
@endsection

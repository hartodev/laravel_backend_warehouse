@extends('layouts.admin')
@section('title', 'Detail Stock Opname')
@section('content')

@php
$badgeMap =
['draft'=>'admin-badge-muted','in_progress'=>'admin-badge-warning','pending_approval'=>'admin-badge-info','approved'=>'admin-badge-success'];
$labelMap = ['draft'=>'Draft','in_progress'=>'Sedang Berjalan','pending_approval'=>'Menunggu
Persetujuan','approved'=>'Disetujui'];
@endphp

<div class="admin-page-head">
    <h2>Opname {{ $opname->opname_number }}</h2>
    <span
        class="admin-badge {{ $badgeMap[$opname->status] ?? 'admin-badge-muted' }}">{{ $labelMap[$opname->status] ?? ucfirst($opname->status) }}</span>
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
@if($opname->reject_reason && $opname->status === 'in_progress')
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> Dikembalikan oleh Super Admin:
    {{ $opname->reject_reason }}</div>
@endif

<div class="admin-detail-grid" style="margin-bottom:20px;">
    <div class="admin-detail-item">
        <p class="admin-label">Gudang</p>
        <p>{{ $opname->warehouse->name ?? '-' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Tanggal Opname</p>
        <p>{{ \Illuminate\Support\Carbon::parse($opname->opname_date)->format('d M Y') }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Scope</p>
        <p>{{ ucfirst($opname->scope) }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Dibuat Oleh</p>
        <p>{{ $opname->createdBy->name ?? '-' }}</p>
    </div>
    @if($opname->notes)
    <div class="admin-detail-item" style="grid-column:span 2;">
        <p class="admin-label">Catatan</p>
        <p>{{ $opname->notes }}</p>
    </div>
    @endif
</div>

@if($opname->status === 'draft')
<form action="{{ route('admin.stock-opnames.start', $opname) }}" method="POST" style="margin-bottom:20px;">
    @csrf
    <button type="submit" class="btn-primary ripple">Mulai Hitung Fisik</button>
</form>
@endif

@if(in_array($opname->status, ['draft', 'in_progress']))
<form id="progress-form">
    @csrf
    <div class="admin-card admin-table-wrap" style="margin-bottom:12px;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>SKU</th>
                    <th>Stok Sistem</th>
                    <th>Stok Fisik</th>
                    <th>Selisih</th>
                </tr>
            </thead>
            <tbody>
                @foreach($opname->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? '-' }}</td>
                    <td class="cell-mono">{{ $item->product->sku ?? '-' }}</td>
                    <td class="cell-mono">{{ $item->system_stock }} {{ $item->product->unit ?? '' }}</td>
                    <td>
                        <input type="hidden" name="items[{{ $loop->index }}][product_id]"
                            value="{{ $item->product_id }}">
                        <input type="number" min="0" name="items[{{ $loop->index }}][physical_stock]"
                            value="{{ $item->physical_stock }}" class="admin-input physical-input"
                            data-system="{{ $item->system_stock }}" style="max-width:120px;">
                    </td>
                    <td class="cell-mono diff-cell">{{ $item->difference ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <button type="button" class="btn-outline" id="save-progress-btn">Simpan Progress</button>
    <button type="button" class="btn-primary ripple" id="complete-btn">Selesaikan Opname</button>
</form>

<script>
document.querySelectorAll('.physical-input').forEach(input => {
    input.addEventListener('input', function() {
        const system = parseInt(this.dataset.system, 10) || 0;
        const physical = parseInt(this.value, 10);
        const cell = this.closest('tr').querySelector('.diff-cell');
        cell.textContent = isNaN(physical) ? '-' : (physical - system);
    });
});

function submitOpnameForm(url, method) {
    const form = document.getElementById('progress-form');
    const target = document.createElement('form');
    target.method = 'POST';
    target.action = url;

    // Salin SEMUA input dari form asli (termasuk _token CSRF dan items[])
    form.querySelectorAll('input, select, textarea').forEach(field => {
        const clone = field.cloneNode(true);
        target.appendChild(clone);
    });

    if (method !== 'POST') {
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = method;
        target.appendChild(methodField);
    }

    document.body.appendChild(target);
    target.submit();
}

document.getElementById('save-progress-btn').addEventListener('click', function() {
    submitOpnameForm(@json(route('admin.stock-opnames.save-progress', $opname)), 'POST');
});
document.getElementById('complete-btn').addEventListener('click', function() {
    submitOpnameForm(@json(route('admin.stock-opnames.complete', $opname)), 'POST');
});
</script>
@else
<div class="admin-card admin-table-wrap" style="margin-bottom:20px;">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>SKU</th>
                <th>Stok Sistem</th>
                <th>Stok Fisik</th>
                <th>Selisih</th>
            </tr>
        </thead>
        <tbody>
            @foreach($opname->items as $item)
            <tr>
                <td>{{ $item->product->name ?? '-' }}</td>
                <td class="cell-mono">{{ $item->product->sku ?? '-' }}</td>
                <td class="cell-mono">{{ $item->system_stock }} {{ $item->product->unit ?? '' }}</td>
                <td class="cell-mono">{{ $item->physical_stock ?? '-' }}</td>
                <td class="cell-mono">
                    @if($item->difference === null) - @elseif($item->difference == 0) <span
                        class="admin-badge admin-badge-success">0</span>
                    @else <span
                        class="admin-badge admin-badge-warning">{{ $item->difference > 0 ? '+' : '' }}{{ $item->difference }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@if($opname->status === 'pending_approval')
<div class="admin-card" style="padding:16px;margin-bottom:20px;">
    <p class="admin-label" style="margin-bottom:10px;">Persetujuan Super Admin</p>
    <div style="display:flex;gap:10px;">
        <form action="{{ route('admin.stock-opnames.approve', $opname) }}" method="POST"
            onsubmit="return confirm('Setujui opname ini? Stok akan disesuaikan otomatis.');">
            @csrf
            <button type="submit" class="btn-primary ripple">Setujui &amp; Sesuaikan Stok</button>
        </form>
        <button type="button" class="btn-secondary"
            onclick="document.getElementById('opname-reject-form').classList.toggle('hidden')">Kembalikan</button>
    </div>
    <form id="opname-reject-form" action="{{ route('admin.stock-opnames.reject', $opname) }}" method="POST"
        class="hidden" style="margin-top:12px;">
        @csrf
        <label class="admin-label">Alasan Pengembalian</label>
        <textarea name="reject_reason" required maxlength="500" class="admin-textarea"></textarea>
        <button type="submit" class="btn-primary ripple" style="margin-top:8px;">Kirim</button>
    </form>
</div>
@endif

<div class="admin-action-panel" style="margin-top:20px;">
    <a href="{{ route('admin.stock-opnames.index') }}" class="btn-secondary">← Kembali</a>
</div>

<style>
.hidden {
    display: none;
}
</style>
@endsection
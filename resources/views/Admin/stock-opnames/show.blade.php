@extends('layouts.admin')

@section('title', $opname->opname_number)

@section('content')
    <div class="admin-page-head">
        <h2>{{ $opname->opname_number }}</h2>
        <a href="{{ route('admin.stock-opnames.index') }}" class="btn-ghost">← Kembali</a>
    </div>

    @if (session('success'))
        <div class="admin-alert admin-alert-success"><i data-lucide="check-circle"></i> {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="admin-alert admin-alert-error"><i data-lucide="alert-circle"></i> {{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="admin-alert admin-alert-error">
            <ul style="margin:0;padding-left:18px;">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="admin-card admin-card-pad" style="margin-bottom:20px;">
        <div class="admin-detail-grid">
            <div class="admin-detail-item">
                <div class="label">Status</div>
                <div class="value"><span class="admin-badge admin-badge-{{ $opname->status }}">{{ ucwords(str_replace('_', ' ', $opname->status)) }}</span></div>
            </div>
            <div class="admin-detail-item"><div class="label">Gudang</div><div class="value">{{ $opname->warehouse->name }} ({{ $opname->warehouse->code }})</div></div>
            <div class="admin-detail-item"><div class="label">Tanggal Opname</div><div class="value">{{ \Carbon\Carbon::parse($opname->opname_date)->format('d M Y') }}</div></div>
            <div class="admin-detail-item"><div class="label">Scope</div><div class="value">{{ ucfirst($opname->scope) }}</div></div>
            <div class="admin-detail-item"><div class="label">Dibuat Oleh</div><div class="value">{{ $opname->createdBy->name ?? '-' }}</div></div>
        </div>
        @if ($opname->notes)
            <p class="text-muted" style="margin-top:16px;"><strong style="color:var(--text-primary);">Catatan:</strong> {{ $opname->notes }}</p>
        @endif
        @if ($opname->reject_reason)
            <div class="admin-alert admin-alert-warning" style="margin-top:16px;">
                <strong>Dikembalikan Super Admin:</strong>&nbsp;{{ $opname->reject_reason }} — mohon perbaiki dan selesaikan kembali.
            </div>
        @endif
    </div>

    @php $editable = in_array($opname->status, ['draft', 'in_progress']); @endphp

    <form method="POST" action="{{ route('admin.stock-opnames.complete', $opname) }}" class="admin-card admin-card-pad">
        @csrf

        <div class="admin-section-title">Worksheet Hitung Fisik</div>

        <div class="admin-table-wrap" style="border:1px solid var(--border);border-radius:var(--r-sm);margin-bottom:16px;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Stok Sistem</th>
                        <th style="width:160px">Stok Fisik</th>
                        <th>Selisih</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($opname->items as $item)
                        <tr>
                            <td>{{ $item->product->name }} <span class="cell-muted">({{ $item->product->sku }})</span></td>
                            <td>{{ $item->system_stock }} {{ $item->product->unit }}</td>
                            <td>
                                <input type="hidden" name="items[{{ $loop->index }}][product_id]" value="{{ $item->product_id }}">
                                @if ($editable)
                                    <input type="number" name="items[{{ $loop->index }}][physical_stock]" min="0"
                                           value="{{ old("items.$loop->index.physical_stock", $item->physical_stock) }}"
                                           class="admin-input">
                                @else
                                    {{ $item->physical_stock ?? '-' }} {{ $item->product->unit }}
                                @endif
                            </td>
                            <td style="{{ $item->difference && $item->difference != 0 ? 'color:var(--accent-red);font-weight:600;' : '' }}">
                                {{ $item->difference ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($editable)
            <div style="display:flex;gap:10px;">
                <button type="submit" formaction="{{ route('admin.stock-opnames.save-progress', $opname) }}" formmethod="POST" class="btn-ghost">
                    Simpan Progress
                </button>
                <button type="submit" formaction="{{ route('admin.stock-opnames.complete', $opname) }}" formmethod="POST" class="btn-primary ripple">
                    Selesaikan &amp; Ajukan Persetujuan
                </button>
            </div>
            <p class="text-muted" style="margin-top:10px;">
                "Simpan Progress" bisa dipakai berkali-kali tanpa perlu semua baris terisi.
                "Selesaikan" membutuhkan semua baris stok fisik terisi dan akan mengirim opname untuk disetujui Super Admin.
            </p>
        @elseif ($opname->status === 'draft')
            <form method="POST" action="{{ route('admin.stock-opnames.start', $opname) }}">
                @csrf
                <button class="btn-primary ripple">Mulai Opname</button>
            </form>
        @else
            <p class="text-muted">Worksheet sudah final, tidak dapat diedit lagi.</p>
        @endif
    </form>
@endsection

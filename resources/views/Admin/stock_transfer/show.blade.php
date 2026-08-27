@extends('layouts.admin')
@section('title', 'Detail Transfer Stok')
@section('content')

@php
$badgeMap = [
'pending_confirmation'=>'admin-badge-muted','pending_approval'=>'admin-badge-warning','approved'=>'admin-badge-info',
'in_transit'=>'admin-badge-info','discrepancy'=>'admin-badge-danger','received'=>'admin-badge-success',
'rejected'=>'admin-badge-danger','cancelled'=>'admin-badge-muted',
];
$labelMap = [
'pending_confirmation'=>'Menunggu Konfirmasi','pending_approval'=>'Menunggu Approval','approved'=>'Disetujui',
'in_transit'=>'Dalam
Pengiriman','discrepancy'=>'Selisih','received'=>'Diterima','rejected'=>'Ditolak','cancelled'=>'Dibatalkan',
];
$user = auth()->user();
$isSuperadmin = in_array($user->role, ['superadmin', 'super_admin']);
$isFromWarehouseAdmin = (int) $user->warehouse_id === (int) $transfer->from_warehouse_id;
$isToWarehouseAdmin = (int) $user->warehouse_id === (int) $transfer->to_warehouse_id;
$isRequester = (int) $transfer->requested_by === (int) $user->id;
@endphp

<div class="admin-page-head">
    <h2>Transfer {{ $transfer->transfer_number }}</h2>
    <span
        class="admin-badge {{ $badgeMap[$transfer->status] ?? 'admin-badge-muted' }}">{{ $labelMap[$transfer->status] ?? ucfirst($transfer->status) }}</span>
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

<div class="admin-detail-grid" style="margin-bottom:20px;">
    <div class="admin-detail-item">
        <p class="admin-label">Dari Gudang</p>
        <p>{{ $transfer->fromWarehouse->name ?? '-' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Ke Gudang</p>
        <p>{{ $transfer->toWarehouse->name ?? '-' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Diminta Oleh</p>
        <p>{{ $transfer->requestedBy->name ?? '-' }}</p>
    </div>
    <div class="admin-detail-item">
        <p class="admin-label">Tanggal Transfer</p>
        <p>{{ \Illuminate\Support\Carbon::parse($transfer->transfer_date)->format('d M Y') }}</p>
    </div>
    @if($transfer->notes)
    <div class="admin-detail-item" style="grid-column:span 2;">
        <p class="admin-label">Catatan</p>
        <p>{{ $transfer->notes }}</p>
    </div>
    @endif
    @if($transfer->status === 'cancelled' && $transfer->cancel_reason)
    <div class="admin-detail-item" style="grid-column:span 2;">
        <p class="admin-label">Alasan Pembatalan</p>
        <p>{{ $transfer->cancel_reason }}</p>
    </div>
    @endif
    @if($transfer->status === 'rejected' && $transfer->reject_reason)
    <div class="admin-detail-item" style="grid-column:span 2;">
        <p class="admin-label">Alasan Penolakan</p>
        <p>{{ $transfer->reject_reason }}</p>
    </div>
    @endif
    @if($transfer->discrepancy_notes)
    <div class="admin-detail-item" style="grid-column:span 2;">
        <p class="admin-label">Catatan Selisih</p>
        <p>{{ $transfer->discrepancy_notes }}</p>
    </div>
    @endif
    @if($transfer->resolution_notes)
    <div class="admin-detail-item" style="grid-column:span 2;">
        <p class="admin-label">Catatan Resolusi</p>
        <p>{{ $transfer->resolution_notes }}</p>
    </div>
    @endif
</div>

<div class="admin-card admin-table-wrap" style="margin-bottom:20px;">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Diminta</th>
                <th>Dikirim</th>
                <th>Diterima</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transfer->items as $item)
            <tr>
                <td>{{ $item->product->name ?? '-' }} <span
                        class="cell-muted cell-mono">({{ $item->product->sku ?? '-' }})</span></td>
                <td class="cell-mono">{{ $item->quantity_requested }} {{ $item->product->unit ?? '' }}</td>
                <td class="cell-mono">{{ $item->quantity_sent }} {{ $item->product->unit ?? '' }}</td>
                <td class="cell-mono">{{ $item->quantity_received }} {{ $item->product->unit ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- pending_confirmation: requester confirms or cancels --}}
@if($transfer->status === 'pending_confirmation' && $isRequester)
<div class="admin-card" style="padding:16px;margin-bottom:20px;">
    <p class="admin-label" style="margin-bottom:10px;">Konfirmasi Request</p>
    <div style="display:flex;gap:10px;">
        <form action="{{ route('admin.stock-transfers.confirm', $transfer) }}" method="POST">
            @csrf
            <button type="submit" class="btn-primary ripple">Konfirmasi</button>
        </form>
        <button type="button" class="btn-secondary"
            onclick="document.getElementById('cancel-form').classList.toggle('hidden')">Batalkan</button>
    </div>
    <form id="cancel-form" action="{{ route('admin.stock-transfers.cancel', $transfer) }}" method="POST" class="hidden"
        style="margin-top:12px;">
        @csrf
        <label class="admin-label">Alasan Pembatalan</label>
        <textarea name="cancel_reason" required class="admin-textarea"></textarea>
        <button type="submit" class="btn-primary ripple" style="margin-top:8px;">Kirim</button>
    </form>
</div>
@endif

{{-- pending_approval: superadmin approves or rejects --}}
@if($transfer->status === 'pending_approval' && $isSuperadmin)
<div class="admin-card" style="padding:16px;margin-bottom:20px;">
    <p class="admin-label" style="margin-bottom:10px;">Approval Superadmin</p>
    <div style="display:flex;gap:10px;">
        <form action="{{ route('admin.stock-transfers.approve', $transfer) }}" method="POST">
            @csrf
            <button type="submit" class="btn-primary ripple">Setujui</button>
        </form>
        <button type="button" class="btn-secondary"
            onclick="document.getElementById('transfer-reject-form').classList.toggle('hidden')">Tolak</button>
    </div>
    <form id="transfer-reject-form" action="{{ route('admin.stock-transfers.reject', $transfer) }}" method="POST"
        class="hidden" style="margin-top:12px;">
        @csrf
        <label class="admin-label">Alasan Penolakan</label>
        <textarea name="reject_reason" required class="admin-textarea"></textarea>
        <button type="submit" class="btn-primary ripple" style="margin-top:8px;">Kirim</button>
    </form>
</div>
@endif

{{-- approved: from-warehouse admin sends the goods --}}
@if($transfer->status === 'approved' && $isFromWarehouseAdmin)
<div class="admin-card" style="padding:16px;margin-bottom:20px;">
    <p class="admin-label" style="margin-bottom:10px;">Kirim Barang</p>
    <form action="{{ route('admin.stock-transfers.send', $transfer) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <table class="admin-table" style="margin-bottom:12px;">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Diminta</th>
                    <th>Qty Dikirim</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transfer->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? '-' }}</td>
                    <td class="cell-mono">{{ $item->quantity_requested }}</td>
                    <td>
                        <input type="hidden" name="items[{{ $loop->index }}][stock_transfer_item_id]"
                            value="{{ $item->id }}">
                        <input type="number" min="1" max="{{ $item->quantity_requested }}"
                            name="items[{{ $loop->index }}][quantity_sent]" class="admin-input" style="max-width:120px;"
                            required>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-bottom:12px;">
            <label class="admin-label">Lampiran Bukti Kirim (wajib)</label>
            <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf" required class="admin-input">
        </div>
        <button type="submit" class="btn-primary ripple">Kirim Barang</button>
    </form>
</div>
@endif

{{-- in_transit: to-warehouse admin checks received goods --}}
@if($transfer->status === 'in_transit' && $isToWarehouseAdmin)
<div class="admin-card" style="padding:16px;margin-bottom:20px;">
    <p class="admin-label" style="margin-bottom:10px;">Checklist Penerimaan</p>
    <form action="{{ route('admin.stock-transfers.checklist', $transfer) }}" method="POST">
        @csrf
        <table class="admin-table" style="margin-bottom:12px;">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Dikirim</th>
                    <th>Qty Diterima</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transfer->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? '-' }}</td>
                    <td class="cell-mono">{{ $item->quantity_sent }}</td>
                    <td>
                        <input type="hidden" name="items[{{ $loop->index }}][stock_transfer_item_id]"
                            value="{{ $item->id }}">
                        <input type="number" min="0" name="items[{{ $loop->index }}][quantity_received]"
                            value="{{ $item->quantity_sent }}" class="admin-input" style="max-width:120px;" required>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-bottom:12px;">
            <label class="admin-label">Catatan Selisih <span class="cell-muted">(wajib jika ada selisih)</span></label>
            <textarea name="discrepancy_notes" class="admin-textarea"></textarea>
        </div>
        <button type="submit" class="btn-primary ripple">Simpan Checklist</button>
    </form>
</div>
@endif

{{-- discrepancy: superadmin resolves --}}
@if($transfer->status === 'discrepancy' && $isSuperadmin)
<div class="admin-card" style="padding:16px;margin-bottom:20px;">
    <p class="admin-label" style="margin-bottom:10px;">Resolusi Selisih</p>
    <form action="{{ route('admin.stock-transfers.resolve-discrepancy', $transfer) }}" method="POST">
        @csrf
        <div style="margin-bottom:12px;">
            <label class="admin-label">Keputusan</label>
            <select name="resolution" required class="admin-select">
                <option value="accept">Terima Apa Adanya</option>
                <option value="cancel">Batalkan Transfer</option>
            </select>
        </div>
        <div style="margin-bottom:12px;">
            <label class="admin-label">Catatan</label>
            <textarea name="notes" required class="admin-textarea"></textarea>
        </div>
        <button type="submit" class="btn-primary ripple">Simpan Resolusi</button>
    </form>
</div>
@endif

<div class="admin-action-panel" style="margin-top:20px;">
    <a href="{{ route('admin.stock-transfers.index') }}" class="btn-secondary">← Kembali</a>
</div>

<style>
.hidden {
    display: none;
}
</style>
@endsection
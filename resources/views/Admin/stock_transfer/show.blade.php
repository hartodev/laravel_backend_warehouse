@extends('layouts.admin')

@section('title', 'Detail Transfer')
@section('page-title', $transfer->transfer_number)

@php
$user = auth()->user();
$isRequester = (int) $transfer->requested_by === (int) $user->id;
$isFromWarehouse = (int) $user->warehouse_id === (int) $transfer->from_warehouse_id;
$isToWarehouse = (int) $user->warehouse_id === (int) $transfer->to_warehouse_id;
@endphp

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card-panel">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h5 class="fw-bold mb-1">{{ $transfer->transfer_number }}</h5>
                    <div class="text-muted small">{{ $transfer->fromWarehouse->name }} →
                        {{ $transfer->toWarehouse->name }}</div>
                </div>
                <span
                    class="badge-status bg-primary-subtle text-primary">{{ str_replace('_', ' ', $transfer->status) }}</span>
            </div>

            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Diminta</th>
                        <th>Dikirim</th>
                        <th>Diterima</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transfer->items as $item)
                    <tr>
                        <td>{{ $item->product->name }} <span class="text-muted small">({{ $item->product->sku }})</span>
                        </td>
                        <td>{{ $item->quantity_requested }} {{ $item->product->unit }}</td>
                        <td>{{ $item->quantity_sent ?? '-' }}</td>
                        <td>{{ $item->quantity_received ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($transfer->notes)
            <div class="text-muted small mt-2"><strong>Catatan:</strong> {{ $transfer->notes }}</div>
            @endif
            @if($transfer->discrepancy_notes)
            <div class="alert alert-danger small mt-3 mb-0">
                <strong>Selisih dilaporkan:</strong> {{ $transfer->discrepancy_notes }}
            </div>
            @endif
        </div>

        {{-- ── AKSI: hanya muncul sesuai status + kepemilikan gudang ── --}}

        @if($transfer->status === 'pending_confirmation' && $isRequester)
        <div class="card-panel">
            <h6 class="fw-bold mb-3">Konfirmasi Request</h6>
            <p class="text-muted small">Request ini masih draft Anda. Konfirmasi untuk meneruskan ke approval
                Superadmin, atau batalkan.</p>
            <div class="d-flex gap-2">
                <form method="POST" action="{{ route('admin.stock-transfers.confirm', $transfer) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm">Konfirmasi & Lanjutkan</button>
                </form>
                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                    data-bs-target="#cancelModal">Batalkan</button>
            </div>
        </div>

        <!-- Cancel Modal -->
        <div class="modal fade" id="cancelModal" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.stock-transfers.cancel', $transfer) }}"
                    class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h6 class="modal-title">Batalkan Transfer</h6><button class="btn-close"
                            data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label small">Alasan pembatalan</label>
                        <textarea name="cancel_reason" class="form-control" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger btn-sm">Ya, Batalkan</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        @if($transfer->status === 'pending_approval')
        <div class="card-panel">
            <p class="text-muted small mb-0">
                <i class="lucide-clock"></i> Menunggu approval Superadmin. Anda tidak perlu melakukan apa-apa di tahap
                ini.
            </p>
        </div>
        @endif

        @if($transfer->status === 'approved' && $isFromWarehouse)
        <div class="card-panel">
            <h6 class="fw-bold mb-3">Kirim Barang</h6>
            <p class="text-muted small">Transfer sudah disetujui. Masukkan jumlah yang dikirim per item dan lampirkan
                bukti pengiriman.</p>
            <form method="POST" action="{{ route('admin.stock-transfers.send', $transfer) }}"
                enctype="multipart/form-data">
                @csrf
                @foreach ($transfer->items as $i => $item)
                <div class="row g-2 align-items-center mb-2">
                    <div class="col-7 small">{{ $item->product->name }}</div>
                    <div class="col-5">
                        <input type="hidden" name="items[{{ $i }}][stock_transfer_item_id]" value="{{ $item->id }}">
                        <input type="number" name="items[{{ $i }}][quantity_sent]" class="form-control form-control-sm"
                            max="{{ $item->quantity_requested }}" value="{{ $item->quantity_requested }}" min="1"
                            required>
                    </div>
                </div>
                @endforeach
                <div class="mb-3 mt-2">
                    <label class="form-label small">Bukti Pengiriman (foto/PDF)</label>
                    <input type="file" name="attachment" class="form-control form-control-sm"
                        accept=".jpg,.jpeg,.png,.pdf" required>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Kirim Barang</button>
            </form>
        </div>
        @endif

        @if($transfer->status === 'in_transit' && $isToWarehouse)
        <div class="card-panel">
            <h6 class="fw-bold mb-3">Checklist Penerimaan</h6>
            <p class="text-muted small">Cocokkan jumlah barang yang benar-benar diterima.</p>
            <form method="POST" action="{{ route('admin.stock-transfers.checklist', $transfer) }}">
                @csrf
                @foreach ($transfer->items as $i => $item)
                <div class="row g-2 align-items-center mb-2">
                    <div class="col-7 small">{{ $item->product->name }} <span class="text-muted">(dikirim:
                            {{ $item->quantity_sent }})</span></div>
                    <div class="col-5">
                        <input type="hidden" name="items[{{ $i }}][stock_transfer_item_id]" value="{{ $item->id }}">
                        <input type="number" name="items[{{ $i }}][quantity_received]"
                            class="form-control form-control-sm" value="{{ $item->quantity_sent }}" min="0" required>
                    </div>
                </div>
                @endforeach
                <div class="mb-3 mt-2">
                    <label class="form-label small">Catatan selisih (isi jika ada jumlah yang tidak sesuai)</label>
                    <textarea name="discrepancy_notes" class="form-control form-control-sm"></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Simpan Checklist</button>
            </form>
        </div>
        @endif

        @if($transfer->status === 'discrepancy')
        <div class="card-panel">
            <p class="text-muted small mb-0">
                <i class="lucide-alert-triangle"></i> Ada selisih barang, menunggu resolusi dari Superadmin.
            </p>
        </div>
        @endif

        @if(in_array($transfer->status, ['received', 'rejected', 'cancelled']))
        <div class="card-panel">
            <p class="text-muted small mb-0">Transfer ini sudah final — tidak ada aksi lebih lanjut.</p>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card-panel">
            <h6 class="fw-bold mb-3">Riwayat</h6>
            <ul class="list-unstyled small mb-0">
                <li class="mb-2"><strong>Diminta oleh:</strong><br>{{ $transfer->requestedBy->name ?? '-' }}</li>
                @if($transfer->confirmedBy)
                <li class="mb-2"><strong>Dikonfirmasi oleh:</strong><br>{{ $transfer->confirmedBy->name }} ·
                    {{ $transfer->confirmed_at?->format('d M Y H:i') }}</li>
                @endif
                @if($transfer->approvedBy)
                <li class="mb-2"><strong>Disetujui oleh:</strong><br>{{ $transfer->approvedBy->name }}</li>
                @endif
                @if($transfer->sent_at)
                <li class="mb-2">
                    <strong>Dikirim:</strong><br>{{ \Illuminate\Support\Carbon::parse($transfer->sent_at)->format('d M Y H:i') }}
                </li>
                @endif
                @if($transfer->receivedBy)
                <li class="mb-2"><strong>Diterima oleh:</strong><br>{{ $transfer->receivedBy->name }}</li>
                @endif
            </ul>
        </div>
    </div>
</div>
@endsection
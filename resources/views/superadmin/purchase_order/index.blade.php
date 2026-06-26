@extends('layouts.app')
@section('title', 'Purchase Order')
@section('breadcrumb')<span class="text-gray-700 font-medium">Purchase Order</span>@endsection

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Purchase Order</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $pos->total() }} PO terdaftar</p>
        </div>
        <a href="{{ route('purchase-orders.create') }}" class="btn-primary btn">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Buat PO
        </a>
    </div>

    <div class="card mb-5">
        <form method="GET" class="card-body flex flex-wrap gap-3 items-end">
            <div class="w-40">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    @foreach (['draft', 'pending', 'approved', 'rejected', 'received'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                            {{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-48">
                <label class="form-label">Supplier</label>
                <select name="supplier_id" class="form-select">
                    <option value="">Semua Supplier</option>
                    @foreach ($suppliers as $sup)
                        <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>
                            {{ $sup->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-48">
                <label class="form-label">Gudang</label>
                <select name="warehouse_id" class="form-select">
                    <option value="">Semua Gudang</option>
                    @foreach ($warehouses as $w)
                        <option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>
                            {{ $w->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-36">
                <label class="form-label">Dari</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input">
            </div>
            <div class="w-36">
                <label class="form-label">Sampai</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input">
            </div>
            <button type="submit" class="btn-primary btn">Filter</button>
            <a href="{{ route('purchase-orders.index') }}" class="btn-secondary btn">Reset</a>
        </form>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No. PO</th>
                    <th>Supplier</th>
                    <th>Gudang</th>
                    <th>Tgl. Order</th>
                    <th>Total</th>
                    <th>Pembayaran</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pos as $po)
                    <tr>
                        <td><span class="font-mono text-sm font-medium text-primary-700">{{ $po->po_number }}</span></td>
                        <td>{{ $po->supplier->name ?? '—' }}</td>
                        <td>{{ $po->warehouse->name ?? '—' }}</td>
                        <td>{{ \Carbon\Carbon::parse($po->order_date)->isoFormat('D MMM Y') }}</td>
                        <td class="font-semibold">Rp {{ number_format($po->total_amount) }}</td>
                        <td>{{ $po->payment_method ? ucfirst($po->payment_method) : '—' }}</td>
                        <td><x-status-badge :status="$po->status" /></td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('purchase-orders.show', $po) }}"
                                    class="btn btn-secondary btn-sm">Detail</a>
                                @if ($po->status === 'pending')
                                    <form method="POST" action="{{ route('purchase-orders.approve', $po) }}"
                                        class="inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">Setujui</button>
                                    </form>
                                    <button
                                        onclick="document.getElementById('reject-po-{{ $po->id }}').classList.remove('hidden')"
                                        class="btn btn-danger btn-sm">Tolak</button>
                                @elseif($po->status === 'approved')
                                    <form method="POST" action="{{ route('purchase-orders.receive', $po) }}"
                                        class="inline">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm">Terima Barang</button>
                                    </form>
                                @endif
                            </div>
                            <x-confirm-modal :id="'reject-po-' . $po->id" title="Tolak Purchase Order?" :message="'PO ' . $po->po_number . ' akan ditolak.'"
                                :action="route('purchase-orders.reject', $po)" method="POST" confirm-label="Tolak PO" confirm-class="btn-danger">
                                <div class="mt-3">
                                    <label class="form-label">Alasan <span class="text-red-500">*</span></label>
                                    <textarea name="reject_reason" rows="2" required class="form-textarea" placeholder="Tulis alasan..."></textarea>
                                </div>
                            </x-confirm-modal>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-12 text-gray-400">Belum ada Purchase Order</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $pos->links() }}</div>
@endsection

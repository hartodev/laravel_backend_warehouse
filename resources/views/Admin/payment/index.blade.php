@extends('layouts.admin')
@section('title', 'Pembayaran')
@section('content')

<div class="admin-page-head">
    <h2>Pembayaran</h2>
    <a href="{{ route('admin.payments.create') }}" class="btn-primary ripple"><i class="lucide-plus"></i> Catat
        Pembayaran</a>
</div>

@if(session('success'))
<div class="admin-alert admin-alert-success"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="admin-alert admin-alert-error"><i class="lucide-alert-circle"></i> {{ session('error') }}</div>
@endif

<form method="GET" class="admin-filter-bar">
    <select name="payment_type" class="admin-select" style="max-width:160px;">
        <option value="">Semua Jenis</option>
        <option value="masuk" @selected(request('payment_type')==='masuk' )>Masuk</option>
        <option value="keluar" @selected(request('payment_type')==='keluar' )>Keluar</option>
    </select>
    <select name="payment_method" class="admin-select" style="max-width:160px;">
        <option value="">Semua Metode</option>
        <option value="cash" @selected(request('payment_method')==='cash' )>Cash</option>
        <option value="transfer" @selected(request('payment_method')==='transfer' )>Transfer</option>
        <option value="cek" @selected(request('payment_method')==='cek' )>Cek</option>
        <option value="giro" @selected(request('payment_method')==='giro' )>Giro</option>
    </select>
    <select name="status" class="admin-select" style="max-width:160px;">
        <option value="">Semua Status</option>
        <option value="pending" @selected(request('status')==='pending' )>Pending</option>
        <option value="verified" @selected(request('status')==='verified' )>Terverifikasi</option>
    </select>
    <input type="date" name="date_from" value="{{ request('date_from') }}" class="admin-input" style="max-width:160px;">
    <input type="date" name="date_to" value="{{ request('date_to') }}" class="admin-input" style="max-width:160px;">
    <button class="btn-outline">Filter</button>
</form>

<div class="admin-card admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>No. Pembayaran</th>
                <th>Jenis</th>
                <th>Metode</th>
                <th>Nominal</th>
                <th>Tanggal</th>
                <th>Dibuat Oleh</th>
                <th>Status</th>
                <th class="cell-actions">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payments as $payment)
            <tr>
                <td class="cell-mono">{{ $payment->payment_number }}</td>
                <td>
                    @if($payment->payment_type === 'masuk')
                    <span class="admin-badge admin-badge-success">Masuk</span>
                    @else
                    <span class="admin-badge admin-badge-danger">Keluar</span>
                    @endif
                </td>
                <td class="cell-muted">{{ ucfirst($payment->payment_method) }}</td>
                <td class="cell-mono">Rp {{ number_format($payment->nominal, 0, ',', '.') }}</td>
                <td>{{ optional($payment->payment_date)->format('d M Y') }}</td>
                <td class="cell-muted">{{ $payment->createdBy->name ?? '-' }}</td>
                <td>
                    @if($payment->status === 'verified')
                    <span class="admin-badge admin-badge-success">Terverifikasi</span>
                    @else
                    <span class="admin-badge admin-badge-warning">Pending</span>
                    @endif
                </td>
                <td class="cell-actions">
                    <a href="{{ route('admin.payments.show', $payment) }}" class="admin-link">Detail</a>
                    @if($payment->status !== 'verified')
                    <a href="{{ route('admin.payments.edit', $payment) }}" class="admin-link">Edit</a>
                    <form action="{{ route('admin.payments.destroy', $payment) }}" method="POST" style="display:inline;"
                        onsubmit="return confirm('Hapus pembayaran ini?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="admin-link text-danger"
                            style="background:none;border:none;cursor:pointer;">Hapus</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="cell-empty">Belum ada pembayaran.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-pagination">{{ $payments->appends(request()->query())->links() }}</div>
@endsection
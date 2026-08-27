@extends('layouts.admin')
@section('title', 'Edit Sales Order')
@section('content')

<div class="admin-page-head"><h2>Edit SO {{ $salesOrder->so_number }}</h2></div>

@if ($errors->any())
<div class="admin-alert admin-alert-error">
    @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
</div>
@endif

<div class="admin-alert" style="background:#fffbe6;border-color:#ffe58f;">
    <i class="lucide-info"></i> Item pesanan tidak dapat diubah dari form ini — hanya data header SO.
</div>

<form action="{{ route('admin.sales-orders.update', $salesOrder) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="admin-form-grid" style="grid-template-columns:repeat(2,1fr);margin-bottom:20px;">
        <div>
            <label class="admin-label">Nama Customer</label>
            <input type="text" name="customer_name" value="{{ old('customer_name', $salesOrder->customer_name) }}" required class="admin-input">
        </div>
        <div>
            <label class="admin-label">Metode Pembayaran</label>
            <select name="payment_method" required class="admin-select">
                <option value="cash" @selected(old('payment_method', $salesOrder->payment_method)==='cash')>Cash</option>
                <option value="transfer" @selected(old('payment_method', $salesOrder->payment_method)==='transfer')>Transfer</option>
                <option value="credit" @selected(old('payment_method', $salesOrder->payment_method)==='credit')>Credit</option>
            </select>
        </div>
        <div style="grid-column:span 2;">
            <label class="admin-label">Alamat Customer</label>
            <textarea name="customer_address" class="admin-textarea">{{ old('customer_address', $salesOrder->customer_address) }}</textarea>
        </div>
        <div>
            <label class="admin-label">Tanggal Order</label>
            <input type="date" name="order_date" value="{{ old('order_date', \Illuminate\Support\Carbon::parse($salesOrder->order_date)->format('Y-m-d')) }}" required class="admin-input">
        </div>
        <div>
            <label class="admin-label">Jatuh Tempo</label>
            <input type="date" name="due_date" value="{{ old('due_date', $salesOrder->due_date ? \Illuminate\Support\Carbon::parse($salesOrder->due_date)->format('Y-m-d') : '') }}" class="admin-input">
        </div>
        <div>
            <label class="admin-label">Diskon (Rp)</label>
            <input type="number" step="0.01" min="0" name="discount_amount" value="{{ old('discount_amount', $salesOrder->discount_amount) }}" class="admin-input">
        </div>
        <div style="grid-column:span 2;">
            <label class="admin-label">Catatan</label>
            <textarea name="notes" class="admin-textarea">{{ old('notes', $salesOrder->notes) }}</textarea>
        </div>
    </div>

    <div class="admin-form-actions" style="justify-content:flex-end;">
        <a href="{{ route('admin.sales-orders.show', $salesOrder) }}" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary ripple">Simpan Perubahan</button>
    </div>
</form>
@endsection

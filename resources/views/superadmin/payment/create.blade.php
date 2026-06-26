@extends('layouts.app')
@section('title','Catat Pembayaran')
@section('breadcrumb')
<a href="{{ route('payments.index') }}" class="hover:text-primary-700">Pembayaran</a>
<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-700 font-medium">Catat Baru</span>
@endsection

@section('content')
<div class="max-w-2xl">
<div class="card" x-data="{ tipe: '{{ old('payment_type','masuk') }}', metode: '{{ old('payment_method','transfer') }}' }">
    <div class="card-header"><h2 class="font-semibold text-gray-900">Form Pencatatan Pembayaran</h2></div>
    <form method="POST" action="{{ route('payments.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="card-body space-y-4">

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="form-label">Tipe Pembayaran <span class="text-red-500">*</span></label>
                <select name="payment_type" x-model="tipe" required class="form-select">
                    <option value="masuk">Uang Masuk</option>
                    <option value="keluar">Uang Keluar</option>
                </select>
            </div>
            <div>
                <label class="form-label">Metode <span class="text-red-500">*</span></label>
                <select name="payment_method" x-model="metode" required class="form-select">
                    <option value="cash">Cash</option>
                    <option value="transfer">Transfer</option>
                    <option value="cek">Cek</option>
                    <option value="giro">Giro</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="form-label">Nominal <span class="text-red-500">*</span></label>
                <input type="number" name="nominal" value="{{ old('nominal') }}" required min="0" class="form-input">
            </div>
            <div>
                <label class="form-label">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required class="form-input">
            </div>
        </div>

        <div x-show="tipe === 'masuk'">
            <label class="form-label">Diterima Dari</label>
            <input type="text" name="diterima_dari" value="{{ old('diterima_dari') }}" class="form-input" placeholder="Nama pihak pengirim">
        </div>

        <div>
            <label class="form-label">Untuk Pembayaran</label>
            <textarea name="untuk_pembayaran" rows="2" class="form-textarea" placeholder="Keterangan pembayaran...">{{ old('untuk_pembayaran') }}</textarea>
        </div>

        <div>
            <label class="form-label">Terbilang</label>
            <input type="text" name="terbilang" value="{{ old('terbilang') }}" class="form-input" placeholder="cth. Satu Juta Rupiah">
        </div>

        <div x-show="metode === 'transfer'" class="grid grid-cols-2 gap-4 border border-gray-100 rounded-lg p-4 bg-gray-50">
            <p class="col-span-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Detail Transfer</p>
            <div>
                <label class="form-label">Nama Pengirim</label>
                <input type="text" name="nama_pengirim" value="{{ old('nama_pengirim') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Bank Pengirim</label>
                <input type="text" name="bank_pengirim" value="{{ old('bank_pengirim') }}" class="form-input" placeholder="BCA, BNI, dll">
            </div>
            <div>
                <label class="form-label">Nama Penerima</label>
                <input type="text" name="nama_penerima" value="{{ old('nama_penerima') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Bank Penerima</label>
                <input type="text" name="bank_penerima" value="{{ old('bank_penerima') }}" class="form-input">
            </div>
            <div class="col-span-2">
                <label class="form-label">No. Rekening Tujuan</label>
                <input type="text" name="no_rekening_tujuan" value="{{ old('no_rekening_tujuan') }}" class="form-input font-mono">
            </div>
        </div>

        <div>
            <label class="form-label">Keterangan</label>
            <textarea name="keterangan" rows="2" class="form-textarea">{{ old('keterangan') }}</textarea>
        </div>

        <div>
            <label class="form-label">Bukti Pembayaran</label>
            <input type="file" name="bukti_file" accept="image/*,.pdf" class="form-input">
            <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, PDF. Maks 5MB.</p>
        </div>

    </div>
    <div class="card-body border-t flex justify-end gap-3">
        <a href="{{ route('payments.index') }}" class="btn-secondary btn">Batal</a>
        <button type="submit" class="btn-primary btn">Simpan Pembayaran</button>
    </div>
    </form>
</div>
</div>
@endsection

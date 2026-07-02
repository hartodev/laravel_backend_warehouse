{{-- resources/views/superadmin/expense_report/create.blade.php --}}
@extends('layouts.app')
@section('title','Buat Laporan Pertanggungjawaban')
@section('breadcrumb')
    <a href="{{ route('expense-reports.index') }}" class="hover:text-primary-700">Laporan Pertanggungjawaban</a>
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-700 font-medium">Buat Baru</span>
@endsection

@section('content')
<div class="max-w-2xl">
<div class="card">
    <div class="card-header"><h2 class="font-semibold text-gray-900">Form Laporan Pertanggungjawaban</h2></div>

    @if ($errors->any())
        <div class="card-body border-b">
            <div class="rounded-md bg-red-50 border border-red-200 p-4">
                <ul class="text-sm text-red-700 list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('expense-reports.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body space-y-4">

            <div>
                <label class="form-label">Pengajuan Anggaran <span class="text-red-500">*</span></label>
                <select name="budget_request_id" required class="form-select">
                    <option value="">— Pilih —</option>
                    @foreach ($budgetRequests as $br)
                        <option value="{{ $br->id }}" {{ old('budget_request_id') == $br->id ? 'selected' : '' }}>
                            {{ $br->nomor_form }} — {{ $br->items->pluck('nama_item')->join(', ') }} (Rp {{ number_format($br->total_estimasi, 0, ',', '.') }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Nomor Invoice</label>
                    <input type="text" name="nomor_invoice" value="{{ old('nomor_invoice') }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Nama Vendor</label>
                    <input type="text" name="nama_vendor" value="{{ old('nama_vendor') }}" class="form-input">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Tanggal Transaksi <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_transaksi" value="{{ old('tanggal_transaksi', now()->format('Y-m-d')) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Nominal Realisasi <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0" name="nominal_realisasi" value="{{ old('nominal_realisasi') }}" class="form-input">
                </div>
            </div>

            <div>
                <label class="form-label mb-2 block">Lampiran yang Disertakan</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="lamp_invoice" value="1" {{ old('lamp_invoice') ? 'checked' : '' }}>
                        Invoice
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="lamp_bukti_transfer" value="1" {{ old('lamp_bukti_transfer') ? 'checked' : '' }}>
                        Bukti Transfer
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="lamp_kartu_garansi" value="1" {{ old('lamp_kartu_garansi') ? 'checked' : '' }}>
                        Kartu Garansi
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="lamp_serah_terima" value="1" {{ old('lamp_serah_terima') ? 'checked' : '' }}>
                        Serah Terima
                    </label>
                </div>
            </div>

            <div>
                <label class="form-label">Lampiran Lainnya (keterangan)</label>
                <input type="text" name="lamp_lainnya" value="{{ old('lamp_lainnya') }}" class="form-input" placeholder="Opsional">
            </div>

            <div>
                <label class="form-label">Upload File Lampiran</label>
                <input type="file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf" class="form-input">
                <p class="text-xs text-gray-400 mt-1">Bisa pilih lebih dari satu file. Maks 5MB per file (jpg, png, pdf).</p>
            </div>

            <div>
                <label class="form-label">Catatan</label>
                <textarea name="catatan" rows="3" class="form-textarea">{{ old('catatan') }}</textarea>
            </div>
        </div>

        <div class="card-body border-t flex justify-end gap-3">
            <a href="{{ route('expense-reports.index') }}" class="btn-secondary btn">Batal</a>
            <button type="submit" class="btn-primary btn">Kirim Laporan</button>
        </div>
    </form>
</div>
</div>
@endsection






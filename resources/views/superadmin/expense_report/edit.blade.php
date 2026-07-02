{{-- resources/views/superadmin/expense_report/edit.blade.php --}}
@extends('layouts.app')
@section('title','Edit Laporan Pertanggungjawaban')
@section('breadcrumb')
    <a href="{{ route('expense-reports.index') }}" class="hover:text-primary-700">Laporan Pertanggungjawaban</a>
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-700 font-medium">Edit</span>
@endsection

@section('content')
<div class="max-w-2xl">
<div class="card">
    <div class="card-header"><h2 class="font-semibold text-gray-900">Edit Laporan Pertanggungjawaban</h2></div>

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

    <form method="POST" action="{{ route('expense-reports.update', $expenseReport) }}">
        @csrf
        @method('PUT')
        <div class="card-body space-y-4">

            <div>
                <label class="form-label">Pengajuan Anggaran</label>
                <input type="text"
                       value="{{ $expenseReport->budgetRequest->nomor_form ?? '—' }} — {{ $expenseReport->budgetRequest->items->pluck('nama_item')->join(', ') ?? '' }}"
                       disabled class="form-input bg-gray-100 text-gray-500">
                <p class="text-xs text-gray-400 mt-1">Pengajuan terkait tidak dapat diubah setelah laporan dibuat.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Nomor Invoice</label>
                    <input type="text" name="nomor_invoice" value="{{ old('nomor_invoice', $expenseReport->nomor_invoice) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Nama Vendor</label>
                    <input type="text" name="nama_vendor" value="{{ old('nama_vendor', $expenseReport->nama_vendor) }}" class="form-input">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Tanggal Transaksi <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_transaksi" value="{{ old('tanggal_transaksi', \Carbon\Carbon::parse($expenseReport->tanggal_transaksi)->format('Y-m-d')) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Nominal Realisasi <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0" name="nominal_realisasi" value="{{ old('nominal_realisasi', $expenseReport->nominal_realisasi) }}" class="form-input">
                </div>
            </div>

            <div>
                <label class="form-label mb-2 block">Lampiran yang Disertakan</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="lamp_invoice" value="1" {{ old('lamp_invoice', $expenseReport->lamp_invoice) ? 'checked' : '' }}>
                        Invoice
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="lamp_bukti_transfer" value="1" {{ old('lamp_bukti_transfer', $expenseReport->lamp_bukti_transfer) ? 'checked' : '' }}>
                        Bukti Transfer
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="lamp_kartu_garansi" value="1" {{ old('lamp_kartu_garansi', $expenseReport->lamp_kartu_garansi) ? 'checked' : '' }}>
                        Kartu Garansi
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="lamp_serah_terima" value="1" {{ old('lamp_serah_terima', $expenseReport->lamp_serah_terima) ? 'checked' : '' }}>
                        Serah Terima
                    </label>
                </div>
            </div>

            <div>
                <label class="form-label">Lampiran Lainnya (keterangan)</label>
                <input type="text" name="lamp_lainnya" value="{{ old('lamp_lainnya', $expenseReport->lamp_lainnya) }}" class="form-input">
            </div>

            @if ($expenseReport->attachment_files)
            <div>
                <label class="form-label mb-2 block">File Terupload Sebelumnya</label>
                <ul class="text-sm text-primary-700 space-y-1">
                    @foreach ($expenseReport->attachment_files as $path)
                        <li><a href="{{ asset('storage/' . $path) }}" target="_blank" class="hover:underline">{{ basename($path) }}</a></li>
                    @endforeach
                </ul>
                <p class="text-xs text-gray-400 mt-1">Untuk mengganti lampiran, hubungi admin (fitur upload ulang belum tersedia di form ini).</p>
            </div>
            @endif

            <div>
                <label class="form-label">Catatan</label>
                <textarea name="catatan" rows="3" class="form-textarea">{{ old('catatan', $expenseReport->catatan) }}</textarea>
            </div>
        </div>

        <div class="card-body border-t flex justify-end gap-3">
            <a href="{{ route('expense-reports.show', $expenseReport) }}" class="btn-secondary btn">Batal</a>
            <button type="submit" class="btn-primary btn">Update Laporan</button>
        </div>
    </form>
</div>
</div>
@endsection



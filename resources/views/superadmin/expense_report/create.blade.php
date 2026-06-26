{{-- expense_reports/create.blade.php --}}
@extends('superadmin.layouts.app')
@section('title','Buat Laporan Pertanggungjawaban')
@section('breadcrumb')
<a href="{{ route('superadmin.expense-reports.index') }}" class="hover:text-primary-700">Lap. Pertanggungjawaban</a>
<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-700 font-medium">Buat Baru</span>
@endsection

@section('content')
<div class="max-w-2xl">
<div class="card">
    <div class="card-header"><h2 class="font-semibold text-gray-900">Form Pertanggungjawaban Anggaran</h2></div>
    <form method="POST" action="{{ route('superadmin.expense-reports.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="card-body space-y-4">
        <div>
            <label class="form-label">Pengajuan Anggaran <span class="text-red-500">*</span></label>
            <select name="budget_request_id" required class="form-select @error('budget_request_id') border-red-400 @enderror">
                <option value="">— Pilih Pengajuan —</option>
                @foreach($budgetRequests as $br)
                <option value="{{ $br->id }}" {{ old('budget_request_id') == $br->id ? 'selected' : '' }}>
                    {{ $br->nomor_form }} — {{ $br->nama_item }} (Rp {{ number_format($br->estimasi_biaya) }})
                </option>
                @endforeach
            </select>
            @error('budget_request_id')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="form-label">Tanggal Transaksi <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_transaksi" value="{{ old('tanggal_transaksi', date('Y-m-d')) }}" required class="form-input">
            </div>
            <div>
                <label class="form-label">Nominal Realisasi <span class="text-red-500">*</span></label>
                <input type="number" name="nominal_realisasi" value="{{ old('nominal_realisasi') }}" required min="0" class="form-input">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="form-label">No. Invoice</label>
                <input type="text" name="nomor_invoice" value="{{ old('nomor_invoice') }}" class="form-input font-mono">
            </div>
            <div>
                <label class="form-label">Nama Vendor</label>
                <input type="text" name="nama_vendor" value="{{ old('nama_vendor') }}" class="form-input">
            </div>
        </div>

        <div>
            <p class="form-label">Kelengkapan Dokumen</p>
            <div class="grid grid-cols-2 gap-2 mt-1">
                @foreach(['lamp_invoice'=>'Invoice/Kuitansi','lamp_bukti_transfer'=>'Bukti Transfer','lamp_kartu_garansi'=>'Kartu Garansi','lamp_serah_terima'=>'Berita Acara Serah Terima'] as $key => $label)
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input type="checkbox" name="{{ $key }}" value="1" {{ old($key) ? 'checked' : '' }} class="w-4 h-4 text-primary-600 border-gray-300 rounded">
                    {{ $label }}
                </label>
                @endforeach
            </div>
            <div class="mt-2">
                <label class="form-label">Dokumen Lainnya</label>
                <input type="text" name="lamp_lainnya" value="{{ old('lamp_lainnya') }}" class="form-input" placeholder="Sebutkan jika ada...">
            </div>
        </div>

        <div>
            <label class="form-label">Upload Attachment</label>
            <input type="file" name="attachments[]" multiple accept="image/*,.pdf" class="form-input">
            <p class="text-xs text-gray-400 mt-1">Bisa upload banyak file. Format: JPG, PNG, PDF. Maks 5MB per file.</p>
        </div>

        <div>
            <label class="form-label">Catatan</label>
            <textarea name="catatan" rows="3" class="form-textarea">{{ old('catatan') }}</textarea>
        </div>
    </div>
    <div class="card-body border-t flex justify-end gap-3">
        <a href="{{ route('superadmin.expense-reports.index') }}" class="btn-secondary btn">Batal</a>
        <button type="submit" class="btn-primary btn">Submit Laporan</button>
    </div>
    </form>
</div>
</div>
@endsection

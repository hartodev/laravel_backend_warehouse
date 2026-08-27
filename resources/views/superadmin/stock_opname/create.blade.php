@extends('layouts.app')
@section('title', 'Buat Stock Opname')
@section('breadcrumb')
<a href="{{ route('superadmin.stock-opnames.index') }}" class="hover:text-primary-700">Stock Opname</a>
<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
</svg>
<span class="text-gray-700 font-medium">Buat Baru</span>
@endsection

@section('content')
<div class="max-w-xl">
    <div class="card">
        <div class="card-header">
            <h2 class="font-semibold text-gray-900">Buat Stock Opname Baru</h2>
        </div>
        <form method="POST" action="{{ route('superadmin.stock-opnames.store') }}">
            @csrf
            <div class="card-body space-y-4">
                <div>
                    <label class="form-label">Gudang <span class="text-red-500">*</span></label>
                    <select name="warehouse_id" required
                        class="form-select @error('warehouse_id') border-red-400 @enderror">
                        <option value="">— Pilih Gudang —</option>
                        @foreach($warehouses as $w)
                        <option value="{{ $w->id }}" {{ old('warehouse_id') == $w->id ? 'selected' : '' }}>
                            {{ $w->name }}</option>
                        @endforeach
                    </select>
                    @error('warehouse_id')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Tanggal Opname <span class="text-red-500">*</span></label>
                    <input type="date" name="opname_date" value="{{ old('opname_date', date('Y-m-d')) }}" required
                        class="form-input @error('opname_date') border-red-400 @enderror">
                    @error('opname_date')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" rows="3" class="form-textarea"
                        placeholder="Opsional...">{{ old('notes') }}</textarea>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm text-blue-700">
                    💡 Setelah membuat opname, klik <strong>Mulai Opname</strong> untuk mengisi stok fisik. Semua produk
                    di gudang akan otomatis dimuat.
                </div>
            </div>
            <div class="card-body border-t flex justify-end gap-3">
                <a href="{{ route('superadmin.stock-opnames.index') }}" class="btn-secondary btn">Batal</a>
                <button type="submit" class="btn-primary btn">Buat Opname</button>
            </div>
        </form>
    </div>
</div>
@endsection
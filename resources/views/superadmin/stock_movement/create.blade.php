{{-- stock_movements/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Catat Pergerakan Manual')
@section('breadcrumb')
<a href="{{ route('superadmin.stock-movements.index') }}" class="hover:text-primary-700">Pergerakan Stok</a>
<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-700 font-medium">Catat Manual</span>
@endsection

@section('content')
<div class="max-w-xl">
    <div class="card">
        <div class="card-header">
            <h2 class="font-semibold text-gray-900">Pergerakan Stok Manual</h2>
        </div>
        <form method="POST" action="{{ route('superadmin.stock-movements.store') }}">
            @csrf
            <div class="card-body space-y-4">
                <div>
                    <label class="form-label">Gudang <span class="text-red-500">*</span></label>
                    <select name="warehouse_id" required class="form-select @error('warehouse_id') border-red-400 @enderror">
                        <option value="">— Pilih Gudang —</option>
                        @foreach($warehouses as $w)
                        <option value="{{ $w->id }}" {{ old('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                        @endforeach
                    </select>
                    @error('warehouse_id')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Produk <span class="text-red-500">*</span></label>
                    <select name="product_id" required class="form-select @error('product_id') border-red-400 @enderror">
                        <option value="">— Pilih Produk —</option>
                        @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }} ({{ $p->sku }}) — {{ $p->unit }}
                        </option>
                        @endforeach
                    </select>
                    @error('product_id')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Tipe <span class="text-red-500">*</span></label>
                        <select name="type" required class="form-select @error('type') border-red-400 @enderror">
                            <option value="">— Pilih —</option>
                            <option value="in"         {{ old('type') === 'in'         ? 'selected' : '' }}>Masuk (In)</option>
                            <option value="out"        {{ old('type') === 'out'        ? 'selected' : '' }}>Keluar (Out)</option>
                            <option value="adjustment" {{ old('type') === 'adjustment' ? 'selected' : '' }}>Penyesuaian</option>
                        </select>
                        @error('type')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Jumlah <span class="text-red-500">*</span></label>
                        <input type="number" name="quantity" value="{{ old('quantity') }}" required min="1"
                               class="form-input @error('quantity') border-red-400 @enderror">
                        @error('quantity')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label class="form-label">Catatan</label>
                    <textarea name="note" rows="3" class="form-textarea"
                              placeholder="Alasan pergerakan stok...">{{ old('note') }}</textarea>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-sm text-yellow-800">
                    ⚠️ Pencatatan manual ini akan langsung memperbarui stok di gudang. Pastikan data sudah benar.
                </div>
            </div>
            <div class="card-body border-t flex justify-end gap-3">
                <a href="{{ route('superadmin.stock-movements.index') }}" class="btn-secondary btn">Batal</a>
                <button type="submit" class="btn-primary btn">Simpan Pergerakan</button>
            </div>
        </form>
    </div>
</div>
@endsection

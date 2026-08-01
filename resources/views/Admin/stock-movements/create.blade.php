@extends('layouts.admin')
@section('title', 'Catat Pergerakan Stok')
@section('content')

<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-900">Catat Pergerakan Stok</h1>
    <p class="text-sm text-gray-500">Input stok masuk atau keluar secara manual (di luar PO/SO/Transfer).</p>
</div>

@if ($errors->any())
<div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm">
    <ul class="list-disc list-inside">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card max-w-2xl">
    <div class="card-body p-5">
        <form action="{{ route('admin.stock-movements.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Produk</label>
                    <select name="product_id" required
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">-- Pilih Produk --</option>
                        @foreach($products as $p)
                        <option value="{{ $p->id }}" @selected(old('product_id')==$p->id)>{{ $p->name }}
                            ({{ $p->unit }})</option>
                        @endforeach
                    </select>
                    @error('product_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gudang</label>
                    <select name="warehouse_id" required
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">-- Pilih Gudang --</option>
                        @foreach($warehouses as $w)
                        <option value="{{ $w->id }}" @selected(old('warehouse_id')==$w->id)>{{ $w->name }}</option>
                        @endforeach
                    </select>
                    @error('warehouse_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Pergerakan</label>
                    <div class="flex gap-4 mt-2">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="radio" name="type" value="in" {{ old('type', 'in') === 'in' ? 'checked' : '' }}
                                class="text-green-600 focus:ring-green-500">
                            <span class="text-green-700 font-medium">Masuk</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="radio" name="type" value="out" {{ old('type') === 'out' ? 'checked' : '' }}
                                class="text-red-600 focus:ring-red-500">
                            <span class="text-red-700 font-medium">Keluar</span>
                        </label>
                    </div>
                    @error('type')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                    <input type="number" name="quantity" min="1" value="{{ old('quantity') }}" required
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                    @error('quantity')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan / Alasan</label>
                    <textarea name="note" rows="3" placeholder="Contoh: koreksi stok opname, barang rusak, dll"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">{{ old('note') }}</textarea>
                    @error('note')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.stock-movements.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
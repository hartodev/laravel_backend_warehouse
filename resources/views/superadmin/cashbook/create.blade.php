{{-- resources/views/superadmin/cashbook/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold text-gray-800">Tambah Entri Kas</h1>
        <a href="{{ route('superadmin.cash-books.index') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr;
            Kembali</a>
    </div>

    @if ($errors->any())
    <div class="mb-4 rounded-md bg-red-50 border border-red-200 p-4">
        <ul class="text-sm text-red-700 list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('superadmin.cash-books.store') }}" method="POST"
        class="bg-white rounded-lg shadow p-6 space-y-5">
        @csrf

        <div>
            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Tipe Transaksi</label>
            <select name="type" id="type"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">-- Pilih Tipe --</option>
                <option value="masuk" {{ old('type') == 'masuk' ? 'selected' : '' }}>Kas Masuk</option>
                <option value="keluar" {{ old('type') == 'keluar' ? 'selected' : '' }}>Kas Keluar</option>
            </select>
        </div>

        <div>
            <label for="pihak" class="block text-sm font-medium text-gray-700 mb-1">Pihak</label>
            <input type="text" name="pihak" id="pihak" value="{{ old('pihak') }}"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Nama pihak terkait">
        </div>

        <div>
            <label for="jumlah_uang" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Uang</label>
            <input type="number" step="0.01" min="0" name="jumlah_uang" id="jumlah_uang"
                value="{{ old('jumlah_uang') }}"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="0">
        </div>

        <div>
            <label for="terbilang" class="block text-sm font-medium text-gray-700 mb-1">Terbilang</label>
            <input type="text" name="terbilang" id="terbilang" value="{{ old('terbilang') }}"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Contoh: Seratus ribu rupiah">
        </div>

        <div>
            <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
            <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal') }}"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div>
            <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
            <textarea name="keterangan" id="keterangan" rows="3"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Opsional">{{ old('keterangan') }}</textarea>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('superadmin.cash-books.index') }}"
                class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 text-sm hover:bg-gray-50">Batal</a>
            <button type="submit"
                class="px-4 py-2 rounded-md bg-indigo-600 text-white text-sm hover:bg-indigo-700">Simpan</button>
        </div>
    </form>
</div>
@endsection



{{-- Form ini dipakai bareng oleh create.blade.php & edit.blade.php --}}
<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Satuan</label>
    <input type="text" name="name" value="{{ old('name', $productUnit->name ?? '') }}"
        placeholder="Pieces, Kilogram, Dus, dll"
        class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500" required>
    @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Simbol / Singkatan</label>
    <input type="text" name="symbol" value="{{ old('symbol', $productUnit->symbol ?? '') }}" placeholder="pcs, kg, dus"
        class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
    @error('symbol')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
</div>

<div class="mb-2 flex items-center gap-2">
    <input type="checkbox" id="is_active" name="is_active" value="1"
        {{ old('is_active', $productUnit->is_active ?? true) ? 'checked' : '' }}
        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
    <label for="is_active" class="text-sm text-gray-700">Aktif</label>
</div>
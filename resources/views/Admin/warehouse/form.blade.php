{{-- Form dipakai bareng oleh create.blade.php & edit.blade.php --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Gudang</label>
        <input type="text" name="name" value="{{ old('name', $warehouse->name ?? '') }}" required
            class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
        @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Gudang</label>
        <input type="text" name="code" value="{{ old('code', $warehouse->code ?? '') }}" placeholder="WH-01"
            class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
        @error('code')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
        <input type="text" name="phone" value="{{ old('phone', $warehouse->phone ?? '') }}"
            class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
        @error('phone')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Penanggung Jawab</label>
        <input type="text" name="pic_name" value="{{ old('pic_name', $warehouse->pic_name ?? '') }}"
            class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
        @error('pic_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
        <textarea name="address" rows="3"
            class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">{{ old('address', $warehouse->address ?? '') }}</textarea>
        @error('address')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
    </div>
    <div class="flex items-center gap-2">
        <input type="checkbox" id="is_active" name="is_active" value="1"
            {{ old('is_active', $warehouse->is_active ?? true) ? 'checked' : '' }}
            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
        <label for="is_active" class="text-sm text-gray-700">Aktif</label>
    </div>
</div>
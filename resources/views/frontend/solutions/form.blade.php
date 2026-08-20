@csrf

@if (isset($solution))
    @method('PUT')
@endif

<div class="space-y-4">
    <div>
        <label class="form-label">Judul</label>
        <input type="text" name="title" class="form-input" value="{{ old('title', $solution->title ?? '') }}"
               placeholder="Contoh: Inventory Management" required>
        @error('title') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="form-label">Deskripsi</label>
        <textarea name="description" rows="2" class="form-textarea" required>{{ old('description', $solution->description ?? '') }}</textarea>
        @error('description') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="form-label">Icon (nama icon Lucide)</label>
            <input type="text" name="icon" class="form-input" value="{{ old('icon', $solution->icon ?? '') }}"
                   placeholder="Contoh: package, users" required>
            <p class="text-xs text-gray-400 mt-1">Lihat di <a href="https://lucide.dev/icons" target="_blank" class="text-indigo-600 hover:underline">lucide.dev/icons</a></p>
            @error('icon') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="form-label">Warna Icon</label>
            <select name="color" class="form-select" required>
                @foreach ($colors as $color)
                    <option value="{{ $color }}" {{ old('color', $solution->color ?? '') == $color ? 'selected' : '' }}>
                        {{ ucfirst($color) }}
                    </option>
                @endforeach
            </select>
            @error('color') <div class="form-error">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="form-label">Ukuran Kartu</label>
            <select name="size" class="form-select" required>
                @foreach ($sizes as $size)
                    <option value="{{ $size }}" {{ old('size', $solution->size ?? '') == $size ? 'selected' : '' }}>
                        {{ strtoupper($size) }} — {{ ['sm' => '1 kolom, polos', 'md' => '2 kolom + mini chart', 'lg' => '2 kolom + mini inventory'][$size] }}
                    </option>
                @endforeach
            </select>
            @error('size') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="form-label">Visual Tambahan</label>
            <select name="visual_type" id="visual_type" class="form-select" required
                    onchange="window.toggleSolutionVisual(this.value)">
                @foreach ($visualTypes as $type)
                    <option value="{{ $type }}" {{ old('visual_type', $solution->visual_type ?? '') == $type ? 'selected' : '' }}>
                        {{ ['none' => 'Tanpa visual', 'inventory' => 'Mini Inventory List', 'chart' => 'Mini Bar Chart'][$type] }}
                    </option>
                @endforeach
            </select>
            @error('visual_type') <div class="form-error">{{ $message }}</div> @enderror
        </div>
    </div>

    {{-- Chart data — hanya relevan kalau visual_type = chart --}}
    <div id="chart-data-field" style="display:none">
        <label class="form-label">Data Mini Chart (tinggi bar, dipisah koma, dalam %)</label>
        <input type="text" name="chart_data" class="form-input"
               value="{{ old('chart_data', $solution->chart_data ?? '40,65,45,80,55,90,70') }}"
               placeholder="Contoh: 40,65,45,80,55,90,70">
        <p class="text-xs text-gray-400 mt-1">Setiap angka jadi satu batang. Boleh berapapun jumlah angkanya.</p>
        @error('chart_data') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    {{-- Mini inventory rows — hanya relevan kalau visual_type = inventory --}}
    <div id="inventory-field" style="display:none">
        <label class="form-label">Baris Mini Inventory</label>
        <div id="inventory-rows" class="space-y-2">
            @php $existingItems = old('inventory', ($solution->inventoryItems ?? collect())->map(fn($i) => ['name' => $i->name, 'stock' => $i->stock, 'color' => $i->color])->toArray()); @endphp
            @forelse ($existingItems as $i => $item)
                <div class="inventory-row grid grid-cols-12 gap-2 items-start">
                    <input type="text" name="inventory[{{ $i }}][name]" class="form-input col-span-6" value="{{ $item['name'] }}" placeholder="SKU-001 · Laptop">
                    <input type="text" name="inventory[{{ $i }}][stock]" class="form-input col-span-3" value="{{ $item['stock'] }}" placeholder="248">
                    <select name="inventory[{{ $i }}][color]" class="form-select col-span-2">
                        @foreach ($inventoryColors as $c)
                            <option value="{{ $c }}" {{ $item['color'] == $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-xs btn-danger col-span-1" onclick="this.closest('.inventory-row').remove()">✕</button>
                </div>
            @empty
            @endforelse
        </div>
        <button type="button" class="btn btn-xs btn-secondary mt-2" onclick="window.addInventoryRow()">+ Tambah Baris</button>
    </div>

    <div>
        <label class="form-label">Urutan</label>
        <input type="number" min="0" name="order" class="form-input" value="{{ old('order', $solution->order ?? 0) }}">
        @error('order') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="flex items-center gap-2">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" id="is_active" value="1"
               {{ old('is_active', $solution->is_active ?? true) ? 'checked' : '' }} class="rounded border-gray-300">
        <label for="is_active" class="form-label" style="margin:0">Aktif (tampil di landing page)</label>
    </div>
</div>

<div class="flex gap-2 pt-2">
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="{{ route('superadmin.landing-solutions.index') }}" class="btn btn-secondary">Batal</a>
</div>

<script>
    (function () {
        let rowIndex = {{ count($existingItems) }};
        const colors = @json($inventoryColors);

        window.addInventoryRow = function () {
            const wrap = document.getElementById('inventory-rows');
            const div = document.createElement('div');
            div.className = 'inventory-row grid grid-cols-12 gap-2 items-start';
            div.innerHTML = `
                <input type="text" name="inventory[${rowIndex}][name]" class="form-input col-span-6" placeholder="SKU-001 · Laptop">
                <input type="text" name="inventory[${rowIndex}][stock]" class="form-input col-span-3" placeholder="248">
                <select name="inventory[${rowIndex}][color]" class="form-select col-span-2">
                    ${colors.map(c => `<option value="${c}">${c.charAt(0).toUpperCase() + c.slice(1)}</option>`).join('')}
                </select>
                <button type="button" class="btn btn-xs btn-danger col-span-1" onclick="this.closest('.inventory-row').remove()">✕</button>
            `;
            wrap.appendChild(div);
            rowIndex++;
        };

        window.toggleSolutionVisual = function (value) {
            document.getElementById('chart-data-field').style.display = value === 'chart' ? 'block' : 'none';
            document.getElementById('inventory-field').style.display = value === 'inventory' ? 'block' : 'none';
        };

        // Set kondisi awal saat halaman dimuat
        window.toggleSolutionVisual(document.getElementById('visual_type').value);
    })();
</script>

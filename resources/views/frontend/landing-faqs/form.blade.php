@csrf

@if (isset($faq))
@method('PUT')
@endif

<div class="space-y-4">
    <div>
        <label class="form-label">Pertanyaan</label>
        <input type="text" name="question" class="form-input" value="{{ old('question', $faq->question ?? '') }}"
            required>
        @error('question') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="form-label">Jawaban</label>
        <textarea name="answer" rows="4" class="form-textarea"
            required>{{ old('answer', $faq->answer ?? '') }}</textarea>
        @error('answer') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="w-36">
        <label class="form-label">Urutan</label>
        <input type="number" min="0" name="order" class="form-input" value="{{ old('order', $faq->order ?? 0) }}">
        @error('order') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="flex items-center gap-2 text-sm text-gray-700">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1"
                {{ old('is_active', $faq->is_active ?? true) ? 'checked' : '' }}>
            Aktif (tampil di landing page)
        </label>
    </div>
</div>

<div class="card-body border-t mt-4 flex justify-end gap-2">
    <a href="{{ route('landing-faqs.index') }}" class="btn-secondary btn">Batal</a>
    <button type="submit" class="btn-primary btn">Simpan</button>
</div>
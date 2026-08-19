@csrf

@if (isset($faq))
    @method('PUT')
@endif

<div class="form-group">
    <label>Pertanyaan</label>
    <input type="text" name="question" class="form-control @error('question') is-invalid @enderror"
           value="{{ old('question', $faq->question ?? '') }}" required>
    @error('question') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label>Jawaban</label>
    <textarea name="answer" rows="4" class="form-control @error('answer') is-invalid @enderror" required>{{ old('answer', $faq->answer ?? '') }}</textarea>
    @error('answer') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label>Urutan</label>
    <input type="number" min="0" name="order" class="form-control @error('order') is-invalid @enderror"
           value="{{ old('order', $faq->order ?? 0) }}">
    @error('order') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <div class="custom-control custom-switch">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" id="is_active" value="1" class="custom-control-input"
               {{ old('is_active', $faq->is_active ?? true) ? 'checked' : '' }}>
        <label class="custom-control-label" for="is_active">Aktif (tampil di landing page)</label>
    </div>
</div>

<button type="submit" class="btn btn-primary">Simpan</button>
<a href="{{ route('admin.landing-faqs.index') }}" class="btn btn-secondary">Batal</a>

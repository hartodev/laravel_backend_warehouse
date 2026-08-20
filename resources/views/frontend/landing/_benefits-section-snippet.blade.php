{{--
    Ganti seluruh <section class="benefits-section..."> ... </section> yang lama
    (section "Hasil Nyata Untuk Bisnis Anda") dengan blok ini.
--}}

<!-- Benefits Section (DINAMIS dari database) -->
<section class="benefits-section section-pad" id="benefits">
    <div class="container">
        <div class="section-header fade-up">
            <div class="section-badge">Benefits</div>
            <h2 class="section-title">Hasil Nyata<br /><span class="gradient-text">Untuk Bisnis Anda</span></h2>
            <p class="section-sub">Data dari ratusan perusahaan yang telah menggunakan StockFlow.</p>
        </div>
        <div class="benefits-grid fade-up">
            @foreach ($benefits as $benefit)
                <div class="benefit-card {{ $benefit->is_featured ? 'featured' : '' }}">
                    @if ($benefit->is_static)
                        <div class="benefit-num gradient-text stat-static">{{ $benefit->static_value }}</div>
                    @else
                        <div class="benefit-num gradient-text"
                             data-target="{{ $benefit->target }}"
                             data-suffix="{{ $benefit->suffix }}"
                             @if ($benefit->decimal_places > 0) data-decimal="{{ $benefit->decimal_places }}" @endif>0%</div>
                    @endif
                    <h3>{{ $benefit->title }}</h3>
                    <p>{{ $benefit->description }}</p>
                    <div class="benefit-progress">
                        <div class="benefit-bar" style="--target-width: {{ $benefit->bar_percentage }}%"></div>
                    </div>
                    <div class="benefit-icon"><i data-lucide="{{ $benefit->icon }}"></i></div>
                </div>
            @endforeach
        </div>
    </div>
</section>

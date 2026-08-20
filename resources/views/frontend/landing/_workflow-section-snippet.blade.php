{{--
    Ganti seluruh <section class="workflow-section..."> ... </section> yang lama
    (section "Alur Kerja yang Simpel & Efisien") dengan blok ini.
--}}

<!-- Workflow Section (DINAMIS dari database) -->
<section class="workflow-section section-pad">
    <div class="container">
        <div class="section-header fade-up">
            <div class="section-badge">Workflow</div>
            <h2 class="section-title">Alur Kerja yang<br /><span class="gradient-text">Simpel & Efisien</span></h2>
            <p class="section-sub">Dari barang datang hingga laporan, semua terotomasi dengan sempurna.</p>
        </div>
        <div class="workflow-timeline fade-up">
            @foreach ($workflowSteps as $step)
                <div class="workflow-step {{ $loop->last ? 'last' : '' }}">
                    <div class="wf-card">
                        <div class="wf-icon {{ $step->color }}"><i data-lucide="{{ $step->icon }}"></i></div>
                        <div class="wf-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                        <h4>{{ $step->title }}</h4>
                        <p>{{ $step->description }}</p>
                    </div>
                    @unless ($loop->last)
                        <div class="wf-arrow"><i data-lucide="arrow-right"></i></div>
                    @endunless
                </div>
            @endforeach
        </div>
    </div>
</section>

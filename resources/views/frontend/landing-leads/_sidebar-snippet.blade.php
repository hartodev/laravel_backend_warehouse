{{--
    Tempel blok ini di dalam <nav> layout superadmin kamu, misalnya
    sebelum bagian {{-- SISTEM --}} atau setelah {{-- KEUANGAN --}}.

    Menampilkan grup baru "Landing Page" berisi: Stats, Fitur, Testimoni, FAQ, Contact Leads.
    Badge merah muncul kalau ada lead baru yang belum ditindaklanjuti.
--}}

<div class="group-label">Landing Page</div>

@php
    $landingMenus = [
        [
            'route' => 'superadmin.landing-stats',
            'label' => 'Stats',
            'icon'  => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6h6zm0 0h6v-10a2 2 0 00-2-2h-2a2 2 0 00-2 2v10zm6 0h4v-4a2 2 0 00-2-2h-2v6z',
        ],
        [
            'route' => 'superadmin.landing-features',
            'label' => 'Fitur',
            'icon'  => 'M13 10V3L4 14h7v7l9-11h-7z',
        ],
        [
            'route' => 'superadmin.landing-testimonials',
            'label' => 'Testimoni',
            'icon'  => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
        ],
        [
            'route' => 'superadmin.landing-faqs',
            'label' => 'FAQ',
            'icon'  => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
    ];
@endphp

@foreach ($landingMenus as $m)
    <a href="{{ route($m['route'] . '.index') }}"
        class="nav-item {{ request()->routeIs($m['route'] . '*') ? 'active' : '' }}">
        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $m['icon'] }}" />
        </svg>
        <span class="nav-text">{{ $m['label'] }}</span>
    </a>
@endforeach

@php
    $newLeadsCount = \App\Models\LandingContactLead::status('new')->count();
@endphp
<a href="{{ route('superadmin.landing-leads.index') }}"
    class="nav-item {{ request()->routeIs('superadmin.landing-leads*') ? 'active' : '' }}">
    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
    </svg>
    <span class="nav-text flex-1">Contact Leads</span>
    @if ($newLeadsCount > 0)
        <span class="nav-text badge-pill inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full bg-red-500 text-white text-[10px] font-bold">
            {{ $newLeadsCount }}
        </span>
    @endif
</a>

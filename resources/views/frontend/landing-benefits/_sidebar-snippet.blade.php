{{--
    Tambahkan 1 <a> ini ke dalam $landingMenus array di sidebar snippet
    yang sudah kamu pasang sebelumnya (resources/views/superadmin/landing-leads/_sidebar-snippet.blade.php),
    ATAU cukup tempel <a> berikut langsung sejajar dengan link Stats/Fitur/Testimoni/FAQ yang lain:
--}}

<a href="{{ route('superadmin.landing-benefits.index') }}"
    class="nav-item {{ request()->routeIs('superadmin.landing-benefits*') ? 'active' : '' }}">
    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6h6zm0 0h6v-10a2 2 0 00-2-2h-2a2 2 0 00-2 2v10zm6 0h4v-4a2 2 0 00-2-2h-2v6z" />
    </svg>
    <span class="nav-text">Benefits</span>
</a>

{{--
    Kalau mau pakai array $landingMenus (lebih rapi), tambahkan elemen ini ke array-nya:

    [
        'route' => 'superadmin.landing-benefits',
        'label' => 'Benefits',
        'icon'  => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6h6zm0 0h6v-10a2 2 0 00-2-2h-2a2 2 0 00-2 2v10zm6 0h4v-4a2 2 0 00-2-2h-2v6z',
    ],
--}}

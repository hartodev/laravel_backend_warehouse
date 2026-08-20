{{--
    Tambahkan <a> ini sejajar dengan link Stats/Fitur/Testimoni/FAQ/Benefits
    di grup "Landing Page" pada sidebar admin kamu.
--}}

<a href="{{ route('superadmin.landing-workflow-steps.index') }}"
    class="nav-item {{ request()->routeIs('superadmin.landing-workflow-steps*') ? 'active' : '' }}">
    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
    </svg>
    <span class="nav-text">Workflow</span>
</a>

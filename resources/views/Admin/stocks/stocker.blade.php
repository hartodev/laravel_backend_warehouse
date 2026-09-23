3
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'GudangPro') · Stocker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/lucide-static@latest/font/lucide.css" rel="stylesheet">
    <link href="{{ asset('backend/css/app.css') }}" rel="stylesheet">
    @stack('styles')
</head>

<body>
    <div class="app-wrapper">

        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <i class="lucide-box"></i>
                <span>GudangPro</span>
            </div>

            {{-- Menu sengaja dibuat minim: role stocker cuma boleh lihat stok. --}}
            <nav class="sidebar-nav">
                <div class="nav-section-title">Utama</div>
                <a href="{{ route('stocker.dashboard') }}"
                    class="nav-link {{ request()->routeIs('stocker.dashboard') ? 'active' : '' }}">
                    <i class="lucide-layout-dashboard"></i> Dashboard
                </a>

                <div class="nav-section-title">Stok</div>
                <a href="{{ route('stocker.stocks.index') }}"
                    class="nav-link {{ request()->routeIs('stocker.stocks.index') || request()->routeIs('stocker.stocks.by-warehouse') ? 'active' : '' }}">
                    <i class="lucide-boxes"></i> Semua Stok
                    <span class="badge bg-secondary-subtle text-secondary ms-1"
                        style="font-size:.6rem;">read-only</span>
                </a>
                <a href="{{ route('stocker.stocks.low-stock') }}"
                    class="nav-link {{ request()->routeIs('stocker.stocks.low-stock') ? 'active' : '' }}">
                    <i class="lucide-alert-triangle"></i> Stok Menipis
                </a>
            </nav>
        </aside>

        <div class="main-area">
            <header class="topbar">
                <button class="btn-icon d-lg-none" id="sidebarToggle"><i class="lucide-menu"></i></button>
                <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
                <div class="topbar-actions">
                    <span class="topbar-user">
                        {{ auth()->user()->name ?? 'Stocker' }}
                        @if(auth()->user()?->warehouse)
                        <span class="text-muted small">· {{ auth()->user()->warehouse->name }}</span>
                        @endif
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-sm btn-outline-danger" type="submit">
                            <i class="lucide-log-out"></i> Logout
                        </button>
                    </form>
                </div>
            </header>

            <main class="content-area">
                @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
                @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('backend/js/app.js') }}"></script>
    @stack('scripts')
</body>

</html>
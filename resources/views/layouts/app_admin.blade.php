<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — GudangPro</title>

    {{-- Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap"
        rel="stylesheet">

    {{-- Bootstrap 5 / Stisla --}}
    <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">

    {{-- Tema biru custom (override) --}}
    <link rel="stylesheet" href="{{ asset('css/custom-theme.css') }}">

    @stack('styles')
</head>

<body style="font-family:'Inter',sans-serif;">
    <div id="app">
        <div class="main-wrapper">

            {{-- ===== SIDEBAR ===== --}}
            <div class="main-sidebar">
                <aside id="sidebar-wrapper">
                    <div class="sidebar-brand d-flex align-items-center gap-2 py-3">
                        <div
                            style="width:32px;height:32px;border-radius:8px;background:#3b82f6;display:flex;align-items:center;justify-content:center;color:#fff;font-family:'JetBrains Mono';font-weight:600;font-size:12px;">
                            GP</div>
                        <div>
                            <a href="{{ route('dashboard') }}" class="brand-text d-block">GudangPro</a>
                            <span
                                style="font-family:'JetBrains Mono';font-size:10px;color:#7d97bd;letter-spacing:1.5px;text-transform:uppercase;">Admin
                                Gudang</span>
                        </div>
                    </div>

                    <ul class="sidebar-menu">
                        <li class="menu-header">Menu Utama</li>
                        <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <a href="{{ route('dashboard') }}" class="nav-link">
                                <i class="fas fa-th-large"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="menu-header">Inventaris</li>
                        <li class="nav-item {{ request()->routeIs('stok.*') ? 'active' : '' }}">
                            <a href="{{ route('stok.index') }}" class="nav-link">
                                <i class="fas fa-boxes"></i>
                                <span>Stok Barang</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('transfer.*') ? 'active' : '' }}">
                            <a href="{{ route('transfer.index') }}" class="nav-link">
                                <i class="fas fa-exchange-alt"></i>
                                <span>Transfer Stok</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('permintaan.*') ? 'active' : '' }}">
                            <a href="{{ route('permintaan.index') }}" class="nav-link">
                                <i class="fas fa-clipboard-list"></i>
                                <span>Permintaan</span>
                            </a>
                        </li>

                        <li class="menu-header">Manajemen</li>
                        <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <a href="{{ route('users.index') }}" class="nav-link">
                                <i class="fas fa-users-cog"></i>
                                <span>Pengguna &amp; Role</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('gudang.*') ? 'active' : '' }}">
                            <a href="{{ route('gudang.index') }}" class="nav-link">
                                <i class="fas fa-warehouse"></i>
                                <span>Gudang &amp; Zona</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                            <a href="{{ route('laporan.index') }}" class="nav-link">
                                <i class="fas fa-chart-bar"></i>
                                <span>Laporan</span>
                            </a>
                        </li>
                    </ul>
                </aside>
            </div>

            {{-- ===== MAIN CONTENT ===== --}}
            <div class="main-content">
                <nav class="navbar navbar-expand navbar-light">
                    <div class="d-flex align-items-center" style="width:280px;">
                        <input type="text" class="form-control" placeholder="Cari SKU, produk, atau permintaan…">
                    </div>
                    <div class="navbar-nav ms-auto">
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle d-flex align-items-center gap-2"
                                data-bs-toggle="dropdown">
                                <div
                                    style="width:32px;height:32px;border-radius:50%;background:#3b82f6;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:12px;">
                                    {{ Str::of(auth()->user()->name ?? 'AG')->substr(0,2)->upper() }}
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ route('profile.edit') }}" class="dropdown-item">Profil</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Keluar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </nav>

                <section class="section">
                    <div class="section-body">
                        @yield('content')
                    </div>
                </section>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/modules/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/modules/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    @stack('scripts')
</body>

</html>
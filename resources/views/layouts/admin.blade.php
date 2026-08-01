<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'GudangPro') · Admin</title>
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

            <nav class="sidebar-nav">
                <div class="nav-section-title">Utama</div>
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="lucide-layout-dashboard"></i> Dashboard
                </a>

                <div class="nav-section-title">Master Data</div>
                <a href="{{ route('admin.categories.index') }}"
                    class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="lucide-tags"></i> Kategori
                </a>
                <a href="{{ route('admin.suppliers.index') }}"
                    class="nav-link {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">
                    <i class="lucide-users"></i> Supplier
                </a>
                <a href="{{ route('admin.products.index') }}"
                    class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <i class="lucide-package"></i> Produk
                </a>
                <a href="{{ route('admin.warehouses.index') }}"
                    class="nav-link {{ request()->routeIs('admin.warehouses.*') ? 'active' : '' }}">
                    <i class="lucide-warehouse"></i> Gudang
                </a>
                {{-- Users: Admin hanya boleh LIHAT, tidak ada tombol tambah/edit/hapus di view --}}
                <a href="{{ route('admin.user-requests.index') }}"
                    class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="lucide-users"></i> Pengguna <span class="badge bg-secondary-subtle text-secondary ms-1"
                        style="font-size:.6rem;">read-only</span>
                </a>

                <div class="nav-section-title">Stok & Gudang</div>
                <a href="{{ route('admin.stocks.index') }}"
                    class="nav-link {{ request()->routeIs('admin.stocks.*') ? 'active' : '' }}">
                    <i class="lucide-boxes"></i> Stok
                </a>
                <a href="{{ route('admin.stock-movements.index') }}"
                    class="nav-link {{ request()->routeIs('admin.stock-movements.*') ? 'active' : '' }}">
                    <i class="lucide-arrow-left-right"></i> Mutasi Stok
                </a>
                <a href="{{ route('admin.stock-transfers.index') }}"
                    class="nav-link {{ request()->routeIs('admin.stock-transfers.*') ? 'active' : '' }}">
                    <i class="lucide-truck"></i> Transfer Stok
                </a>
                <a href="{{ route('admin.stock-opnames.index') }}"
                    class="nav-link {{ request()->routeIs('admin.stock-opnames.*') ? 'active' : '' }}">
                    <i class="lucide-clipboard-check"></i> Stock Opname
                </a>
                <a href="{{ route('admin.stock-reports.index') }}"
                    class="nav-link {{ request()->routeIs('admin.stock-reports.*') ? 'active' : '' }}">
                    <i class="lucide-bar-chart-3"></i> Laporan Stok
                </a>
                <a href="{{ route('admin.product-submissions.index') }}"
                    class="nav-link {{ request()->routeIs('admin.product-submissions.*') ? 'active' : '' }}">
                    <i class="lucide-package-plus"></i> Pengajuan Produk
                </a>

                <div class="nav-section-title">Transaksi</div>
                <a href="{{ route('admin.purchase-orders.index') }}"
                    class="nav-link {{ request()->routeIs('admin.purchase-orders.*') ? 'active' : '' }}">
                    <i class="lucide-shopping-cart"></i> Purchase Order
                </a>
                <a href="{{ route('admin.sales-orders.index') }}"
                    class="nav-link {{ request()->routeIs('admin.sales-orders.*') ? 'active' : '' }}">
                    <i class="lucide-send"></i> Sales Order
                </a>
                <a href="{{ route('admin.payments.index') }}"
                    class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                    <i class="lucide-credit-card"></i> Pembayaran
                </a>
                <a href="{{ route('admin.cash-books.index') }}"
                    class="nav-link {{ request()->routeIs('admin.cash-books.*') ? 'active' : '' }}">
                    <i class="lucide-book-open"></i> Buku Kas
                </a>

                <div class="nav-section-title">Anggaran (RAB)</div>
                <a href="{{ route('admin.budget_requests.index') }}"
                    class="nav-link {{ request()->routeIs('admin.budget-requests.*') ? 'active' : '' }}">
                    <i class="lucide-file-spreadsheet"></i> Review RAB Masuk
                </a>
                <a href="{{ route('admin.budget-verifications.index') }}"
                    class="nav-link {{ request()->routeIs('admin.budget-verifications.*') ? 'active' : '' }}">
                    <i class="lucide-check-square"></i> Verifikasi Finance
                </a>
                <a href="{{ route('admin.budget_revisions.index') }}"
                    class="nav-link {{ request()->routeIs('admin.budget-revisions.*') ? 'active' : '' }}">
                    <i class="lucide-refresh-cw"></i> Revisi Anggaran
                </a>
                <a href="{{ route('admin.expense-reports.index') }}"
                    class="nav-link {{ request()->routeIs('admin.expense-reports.*') ? 'active' : '' }}">
                    <i class="lucide-receipt"></i> Laporan Realisasi
                </a>

                <div class="nav-section-title">Barcode</div>
                <a href="{{ route('admin.barcodes.scan') }}"
                    class="nav-link {{ request()->routeIs('admin.barcodes.*') ? 'active' : '' }}">
                    <i class="lucide-scan-barcode"></i> Scan Barcode
                </a>
            </nav>
        </aside>

        <div class="main-area">
            <header class="topbar">
                <button class="btn-icon d-lg-none" id="sidebarToggle"><i class="lucide-menu"></i></button>
                <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
                <div class="topbar-actions">
                    <span class="topbar-user">
                        {{ auth()->user()->name ?? 'Admin' }}
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
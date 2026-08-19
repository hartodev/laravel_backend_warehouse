<!DOCTYPE html>
<html lang="id" x-data="adminLayout()" :class="{ 'sidebar-collapsed': collapsed, 'dark': darkMode }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — GudangPro</title>

    <!-- Fonts: Inter (body) + Plus Jakarta Sans (heading) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap"
        rel="stylesheet">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Inter', 'sans-serif'],
                    heading: ['"Plus Jakarta Sans"', 'sans-serif'],
                },
                colors: {
                    brand: {
                        50: '#eef2ff',
                        100: '#e0e7ff',
                        200: '#c7d2fe',
                        400: '#818cf8',
                        500: '#6366f1',
                        600: '#4f46e5',
                        700: '#4338ca',
                        800: '#3730a3',
                        900: '#312e81',
                    },
                    sidebar: {
                        DEFAULT: '#1e1b4b',
                        hover: '#312e81',
                        active: '#4338ca',
                        border: '#2d2a5e',
                        text: '#a5b4fc',
                        muted: '#6366f1',
                    },
                },
                transitionDuration: {
                    DEFAULT: '200ms'
                },
            }
        }
    }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
    /* ── Root & Reset ───────────────────────────────────── */
    *,
    *::before,
    *::after {
        box-sizing: border-box;
    }

    [x-cloak] {
        display: none !important;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: #f1f5f9;
    }

    :root {
        --sidebar-w: 260px;
        --sidebar-w-mini: 72px;
        --topbar-h: 64px;
        --transition: 200ms cubic-bezier(.4, 0, .2, 1);
    }

    #sidebar {
        width: var(--sidebar-w);
        transition: width var(--transition), transform var(--transition);
        will-change: width;
        z-index: 50;
    }

    .sidebar-collapsed #sidebar {
        width: var(--sidebar-w-mini);
    }

    .sidebar-collapsed .nav-label,
    .sidebar-collapsed .nav-text,
    .sidebar-collapsed .group-label,
    .sidebar-collapsed .brand-name,
    .sidebar-collapsed .badge-pill {
        opacity: 0;
        pointer-events: none;
        width: 0;
        overflow: hidden;
    }

    .sidebar-collapsed .nav-item {
        justify-content: center;
    }

    .sidebar-collapsed .nav-icon {
        margin: 0;
    }

    #main-content {
        margin-left: var(--sidebar-w);
        transition: margin-left var(--transition);
    }

    .sidebar-collapsed #main-content {
        margin-left: var(--sidebar-w-mini);
    }

    @media (max-width: 1023px) {
        #sidebar {
            transform: translateX(-100%);
            width: var(--sidebar-w) !important;
        }

        #sidebar.mobile-open {
            transform: translateX(0);
        }

        #main-content {
            margin-left: 0 !important;
        }

        #sidebar-overlay {
            display: block;
        }
    }

    .nav-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 14px;
        border-radius: 8px;
        cursor: pointer;
        color: #a5b4fc;
        font-size: 0.875rem;
        font-weight: 500;
        transition: background var(--transition), color var(--transition);
        white-space: nowrap;
        overflow: hidden;
        position: relative;
    }

    .nav-item:hover {
        background: #312e81;
        color: #fff;
    }

    .nav-item.active {
        background: #4338ca;
        color: #fff;
    }

    .nav-item.active .nav-icon {
        color: #818cf8;
    }

    .nav-icon {
        width: 18px;
        height: 18px;
        flex-shrink: 0;
    }

    .group-label {
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: #4338ca;
        padding: 18px 14px 4px;
        white-space: nowrap;
        overflow: hidden;
    }

    .sub-menu {
        overflow: hidden;
        transition: max-height .25s ease;
        max-height: 0;
    }

    .sub-menu.open {
        max-height: 600px;
    }

    .sub-nav-item {
        display: flex;
        align-items: center;
        gap-2;
        padding: 7px 14px 7px 42px;
        border-radius: 6px;
        color: #818cf8;
        font-size: 0.8125rem;
        font-weight: 400;
        cursor: pointer;
        white-space: nowrap;
        overflow: hidden;
        transition: background var(--transition), color var(--transition);
    }

    .sub-nav-item:hover {
        background: #312e81;
        color: #fff;
    }

    .sub-nav-item.active {
        color: #fff;
    }

    .sub-dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: #4338ca;
        flex-shrink: 0;
    }

    .sub-nav-item.active .sub-dot {
        background: #818cf8;
    }

    #topbar {
        height: var(--topbar-h);
        position: sticky;
        top: 0;
        z-index: 40;
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
    }

    .dark #topbar {
        background: #1e293b;
        border-color: #334155;
    }

    #page-content {
        min-height: calc(100vh - var(--topbar-h));
        padding: 24px;
    }

    .card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .06), 0 1px 2px rgba(0, 0, 0, .04);
    }

    .dark .card {
        background: #1e293b;
        border-color: #334155;
    }

    .card-header {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .dark .card-header {
        border-color: #334155;
    }

    .card-body {
        padding: 20px;
    }

    .card-footer {
        padding: 14px 20px;
        border-top: 1px solid #f1f5f9;
    }

    .dark .card-footer {
        border-color: #334155;
    }

    .stat-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 20px;
        display: flex;
        align-items: flex-start;
        gap: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .06);
        transition: transform .15s, box-shadow .15s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, .1);
    }

    .dark .stat-card {
        background: #1e293b;
        border-color: #334155;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all .15s;
        border: none;
        text-decoration: none;
    }

    .btn:disabled {
        opacity: .5;
        cursor: not-allowed;
    }

    .btn-sm {
        padding: 5px 12px;
        font-size: 0.8125rem;
    }

    .btn-xs {
        padding: 3px 9px;
        font-size: 0.75rem;
    }

    .btn-primary {
        background: #4338ca;
        color: #fff;
    }

    .btn-primary:hover {
        background: #3730a3;
    }

    .btn-secondary {
        background: #fff;
        color: #374151;
        border: 1px solid #d1d5db;
    }

    .btn-secondary:hover {
        background: #f9fafb;
    }

    .btn-success {
        background: #059669;
        color: #fff;
    }

    .btn-success:hover {
        background: #047857;
    }

    .btn-danger {
        background: #dc2626;
        color: #fff;
    }

    .btn-danger:hover {
        background: #b91c1c;
    }

    .btn-warning {
        background: #d97706;
        color: #fff;
    }

    .btn-warning:hover {
        background: #b45309;
    }

    .btn-info {
        background: #0ea5e9;
        color: #fff;
    }

    .btn-info:hover {
        background: #0284c7;
    }

    .dark .btn-secondary {
        background: #334155;
        color: #e2e8f0;
        border-color: #475569;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 2px 9px;
        border-radius: 20px;
        font-size: 0.72rem;
        font-weight: 600;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-info {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-purple {
        background: #ede9fe;
        color: #5b21b6;
    }

    .badge-gray {
        background: #f3f4f6;
        color: #374151;
    }

    .badge-brand {
        background: #e0e7ff;
        color: #3730a3;
    }

    .dark .badge-success {
        background: #064e3b;
        color: #6ee7b7;
    }

    .dark .badge-warning {
        background: #78350f;
        color: #fcd34d;
    }

    .dark .badge-danger {
        background: #7f1d1d;
        color: #fca5a5;
    }

    .dark .badge-info {
        background: #1e3a5f;
        color: #93c5fd;
    }

    .form-label {
        display: block;
        font-size: .8125rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 4px;
    }

    .dark .form-label {
        color: #cbd5e1;
    }

    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: .875rem;
        color: #111827;
        background: #fff;
        transition: border-color .15s, box-shadow .15s;
        outline: none;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
    }

    .dark .form-input,
    .dark .form-select,
    .dark .form-textarea {
        background: #1e293b;
        border-color: #475569;
        color: #f1f5f9;
    }

    .dark .form-input:focus,
    .dark .form-select:focus {
        border-color: #818cf8;
        box-shadow: 0 0 0 3px rgba(129, 140, 248, .2);
    }

    .form-textarea {
        resize: none;
    }

    .form-error {
        font-size: .75rem;
        color: #dc2626;
        margin-top: 3px;
    }

    .table-wrap {
        overflow-x: auto;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    .dark .table-wrap {
        border-color: #334155;
    }

    table.data-table {
        width: 100%;
        border-collapse: collapse;
    }

    table.data-table thead tr {
        background: #f8fafc;
    }

    .dark table.data-table thead tr {
        background: #0f172a;
    }

    table.data-table thead th {
        padding: 11px 16px;
        text-align: left;
        font-size: .72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #64748b;
        white-space: nowrap;
        border-bottom: 1px solid #e2e8f0;
    }

    .dark table.data-table thead th {
        color: #94a3b8;
        border-color: #334155;
    }

    table.data-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background .1s;
    }

    .dark table.data-table tbody tr {
        border-color: #1e293b;
    }

    table.data-table tbody tr:hover {
        background: #f8fafc;
    }

    .dark table.data-table tbody tr:hover {
        background: #0f172a;
    }

    table.data-table tbody td {
        padding: 12px 16px;
        font-size: .875rem;
        color: #374151;
    }

    .dark table.data-table tbody td {
        color: #cbd5e1;
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: .8125rem;
        color: #64748b;
    }

    .breadcrumb a {
        color: #6366f1;
    }

    .breadcrumb a:hover {
        text-decoration: underline;
    }

    .breadcrumb svg {
        width: 14px;
        height: 14px;
    }

    .toast {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 16px;
        border-radius: 10px;
        margin-bottom: 8px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, .12);
        font-size: .875rem;
        animation: slideIn .2s ease;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(24px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .toast-success {
        background: #ecfdf5;
        border: 1px solid #6ee7b7;
        color: #065f46;
    }

    .toast-error {
        background: #fef2f2;
        border: 1px solid #fca5a5;
        color: #991b1b;
    }

    .toast-warning {
        background: #fffbeb;
        border: 1px solid #fcd34d;
        color: #92400e;
    }

    .dark body {
        background: #0f172a;
        color: #f1f5f9;
    }

    ::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }

    ::-webkit-scrollbar-track {
        background: transparent;
    }

    ::-webkit-scrollbar-thumb {
        background: #c7d2fe;
        border-radius: 99px;
    }

    #sidebar::-webkit-scrollbar-thumb {
        background: #4338ca;
    }

    .page-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1.375rem;
        font-weight: 700;
        color: #0f172a;
    }

    .dark .page-title {
        color: #f1f5f9;
    }

    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, .5);
        backdrop-filter: blur(2px);
        z-index: 60;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    .modal-box {
        background: #fff;
        border-radius: 16px;
        width: 100%;
        max-width: 440px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, .2);
    }

    .dark .modal-box {
        background: #1e293b;
    }

    mark {
        background: #c7d2fe;
        color: #312e81;
        padding: 0 2px;
        border-radius: 2px;
    }

    .skeleton {
        background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.4s infinite;
        border-radius: 6px;
    }

    @keyframes shimmer {
        to {
            background-position: -200% 0;
        }
    }
    </style>
    @stack('styles')
</head>

<body class="antialiased" x-cloak>

    <div id="sidebar-overlay" class="hidden fixed inset-0 bg-black/50 z-40 lg:hidden"
        @click="mobileOpen = false; document.getElementById('sidebar').classList.remove('mobile-open')">
    </div>

    <aside id="sidebar" class="fixed inset-y-0 left-0 flex flex-col bg-[#1e1b4b] overflow-y-auto overflow-x-hidden"
        :class="{ 'mobile-open': mobileOpen }">

        <div class="flex items-center gap-3 px-4 py-5 border-b border-[#2d2a5e] flex-shrink-0">
            <div
                class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-400 to-violet-600 flex items-center justify-center flex-shrink-0 shadow-lg">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                </svg>
            </div>
            <div class="brand-name transition-all duration-200">
                <p class="text-white font-bold text-sm leading-tight"
                    style="font-family:'Plus Jakarta Sans',sans-serif">GudangPro</p>
                <p class="text-indigo-400 text-xs">Super Admin</p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 py-3 px-2 space-y-0.5">

            {{-- Dashboard (route name HAS 'superadmin.' prefix) --}}
            <a href="{{ route('superadmin.dashboard') }}"
                class="nav-item {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10-2a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1h-4a1 1 0 01-1-1v-6z" />
                </svg>
                <span class="nav-text">Dashboard</span>
            </a>

            {{-- MASTER DATA (route names WITHOUT 'superadmin.' prefix) --}}
            <div class="group-label">Master Data</div>

            @php
            $masterMenus = [
            [
            'route' => 'superadmin.categories',
            'label' => 'Kategori',
            'icon' =>
            'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013
            12V7a4 4 0 014-4z',
            ],
            [
            'route' => 'superadmin.products',
            'label' => 'Produk',
            'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10',
            ],
            [
            'route' => 'superadmin.suppliers',
            'label' => 'Supplier',
            'icon' =>
            'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0
            015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0',
            ],
            [
            'route' => 'superadmin.warehouses',
            'label' => 'Gudang',
            'icon' => 'M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z',
            ],
            [
            'route' => 'superadmin.users',
            'label' => 'Users',
            'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197',
            ],

            [
            'route' => 'user-requests',
            'label' => 'Pengajuan User',
            'icon' =>
            'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M9 12l2 2 4-4',
            ],
            [
            'route' => 'superadmin.product-submissions',
            'label' => 'Submission Produk',
            'icon' =>
            'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12',
            ],
            [
            'route' => 'superadmin.activity-logs',
            'label' => 'Activity Log',
            'icon' =>
            'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0
            012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
            ],
            ];
            @endphp

            @foreach ($masterMenus as $m)
            <a href="{{ route($m['route'] . '.index') }}"
                class="nav-item {{ request()->routeIs($m['route'] . '*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $m['icon'] }}" />
                </svg>
                <span class="nav-text">{{ $m['label'] }}</span>
            </a>
            @endforeach

            {{-- INVENTORI --}}
            <div class="group-label">Inventori</div>

            {{-- Submenu: Stok (no 'superadmin.' prefix) --}}
            <div
                x-data="{ open: {{ request()->routeIs('stocks*', 'stock-movements*', 'stock-opnames*', 'stock-transfers*', 'stock-reports*') ? 'true' : 'false' }} }">
                <div class="nav-item" @click="open = !open">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span class="nav-text flex-1">Manaj. Stok</span>
                    <svg class="nav-text w-4 h-4 flex-shrink-0 transition-transform duration-200"
                        :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <div class="sub-menu" :class="{ open }">
                    @php
                    $stockSubs = [
                    ['route' => 'superadmin.stocks.index', 'label' => 'Stok Saat Ini', 'base' => 'stocks'],
                    ['route' => 'superadmin.stocks.low-stock', 'label' => 'Stok Menipis', 'base' => 'stocks.low-stock'],
                    [
                    'route' => 'superadmin.stock-movements.index',
                    'label' => 'Pergerakan Stok',
                    'base' => 'stock-movements',
                    ],
                    ['route' => 'superadmin.stock-opnames.index', 'label' => 'Stock Opname', 'base' => 'stock-opnames'],
                    [
                    'route' => 'superadmin.stock-transfers.index',
                    'label' => 'Transfer Stok',
                    'base' => 'stock-transfers',
                    ],
                    ['route' => 'superadmin.stock-reports.index', 'label' => 'Laporan Stok', 'base' => 'stock-reports'],
                    ];
                    @endphp
                    @foreach ($stockSubs as $s)
                    <a href="{{ route($s['route']) }}"
                        class="sub-nav-item {{ request()->routeIs($s['base'] . '*') ? 'active' : '' }}">
                        <span class="sub-dot"></span>
                        <span>{{ $s['label'] }}</span>
                    </a>
                    @endforeach
                </div>
            </div>

            <a href="{{ route('superadmin.barcodes.index') }}"
                class="nav-item {{ request()->routeIs('barcodes*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
                <span class="nav-text">Barcode</span>
            </a>

            {{-- TRANSAKSI (no 'superadmin.' prefix) --}}
            <div class="group-label">Transaksi</div>

            @php
            $txMenus = [
            [
            'route' => 'superadmin.purchase-orders',
            'label' => 'Purchase Order',
            'icon' =>
            'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2
            2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
            ],
            [
            'route' => 'superadmin.sales-orders',
            'label' => 'Sales Order',
            'icon' =>
            'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0
            012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
            ],
            [
            'route' => 'superadmin.requests',
            'label' => 'Request Barang',
            'icon' =>
            'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0
            00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4',
            ],
            ];
            @endphp
            @foreach ($txMenus as $m)
            <a href="{{ route($m['route'] . '.index') }}"
                class="nav-item {{ request()->routeIs($m['route'] . '*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $m['icon'] }}" />
                </svg>
                <span class="nav-text">{{ $m['label'] }}</span>
            </a>
            @endforeach

            {{-- KEUANGAN (no 'superadmin.' prefix) --}}
            <div class="group-label">Keuangan</div>
            <div
                x-data="{ open: {{ request()->routeIs('payments*', 'cash-books*', 'budget-requests*', 'budget-verifications*', 'budget-revisions*', 'expense-reports*') ? 'true' : 'false' }} }">
                <div class="nav-item" @click="open = !open">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="nav-text flex-1">Keuangan</span>
                    <svg class="nav-text w-4 h-4 flex-shrink-0 transition-transform" :class="open ? 'rotate-180' : ''"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <div class="sub-menu" :class="{ open }">
                    @php
                    $finSubs = [
                    ['route' => 'superadmin.payments.index', 'label' => 'Pembayaran', 'base' => 'payments'],
                    ['route' => 'superadmin.cash-books.index', 'label' => 'Buku Kas', 'base' => 'cash-books'],
                    [
                    'route' => 'superadmin.budget-requests.index',
                    'label' => 'Pengajuan Anggaran',
                    'base' => 'budget-requests',
                    ],
                    [
                    'route' => 'superadmin.budget-verifications.index',
                    'label' => 'Verifikasi Anggaran',
                    'base' => 'budget-verifications',
                    ],
                    [
                    'route' => 'superadmin.budget-revisions.index',
                    'label' => 'Revisi Anggaran',
                    'base' => 'budget-revisions',
                    ],
                    [
                    'route' => 'superadmin.expense-reports.index',
                    'label' => 'Lap. Pertanggjwbn',
                    'base' => 'expense-reports',
                    ],
                    ];
                    @endphp
                    @foreach ($finSubs as $s)
                    <a href="{{ route($s['route']) }}"
                        class="sub-nav-item {{ request()->routeIs($s['base'] . '*') ? 'active' : '' }}">
                        <span class="sub-dot"></span>
                        <span>{{ $s['label'] }}</span>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- SISTEM (no 'superadmin.' prefix) --}}
            <div class="group-label">Sistem</div>
            @php
            $sysMenus = [
            [
            'route' => 'superadmin.product-submissions',
            'label' => 'Submission Produk',
            'icon' =>
            'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12',
            ],
            [
            'route' => 'superadmin.activity-logs',
            'label' => 'Activity Log',
            'icon' =>
            'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0
            012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
            ],
            ];
            @endphp
            @foreach ($sysMenus as $m)
            <a href="{{ route($m['route'] . '.index') }}"
                class="nav-item {{ request()->routeIs($m['route'] . '*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $m['icon'] }}" />
                </svg>
                <span class="nav-text">{{ $m['label'] }}</span>
            </a>
            @endforeach

            <div class="pb-4"></div>
        </nav>

        {{-- User Card --}}
        <div class="border-t border-[#2d2a5e] p-3 flex-shrink-0">
            <div
                class="flex items-center gap-3 px-1 py-2 rounded-lg hover:bg-[#312e81] transition-colors cursor-pointer group">
                <div
                    class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-violet-500 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="brand-name min-w-0">
                    <p class="text-white text-sm font-medium truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-indigo-400 text-xs truncate">{{ auth()->user()->email ?? '' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="brand-name ml-auto flex-shrink-0">
                    @csrf
                    <button type="submit" class="text-indigo-400 hover:text-red-400 transition-colors p-1"
                        title="Logout">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div id="main-content" class="flex flex-col min-h-screen">

        <header id="topbar" class="flex items-center gap-3 px-5 shadow-sm">

            <button @click="collapsed = !collapsed"
                class="hidden lg:flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors"
                title="Toggle Sidebar">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                </svg>
            </button>

            <button
                @click="mobileOpen = !mobileOpen; document.getElementById('sidebar').classList.toggle('mobile-open'); document.getElementById('sidebar-overlay').classList.toggle('hidden')"
                class="flex lg:hidden items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                </svg>
            </button>

            <nav class="breadcrumb flex-1 min-w-0 hidden sm:flex">
                <a href="{{ route('superadmin.dashboard') }}">Dashboard</a>
                @hasSection('breadcrumb')
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                @yield('breadcrumb')
                @endif
            </nav>

            <div class="flex items-center gap-2 ml-auto">

                <div
                    class="hidden md:flex items-center gap-2 bg-gray-100 dark:bg-slate-700 rounded-lg px-3 py-1.5 text-sm text-gray-500">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span class="text-xs">Cari... <kbd class="text-xs opacity-50 font-mono">⌘K</kbd></span>
                </div>

                <button @click="darkMode = !darkMode"
                    class="w-9 h-9 rounded-lg flex items-center justify-center text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                    <svg x-show="!darkMode" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg x-show="darkMode" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>

                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="w-9 h-9 rounded-lg flex items-center justify-center text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors relative">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span
                            class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
                    </button>
                    <div x-show="open" @click.outside="open=false" x-transition
                        class="absolute right-0 top-full mt-2 w-72 card shadow-xl z-50 p-0 overflow-hidden">
                        <div
                            class="px-4 py-3 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between">
                            <p class="font-semibold text-sm text-gray-900 dark:text-white">Notifikasi</p>
                            <span class="badge badge-brand">3 Baru</span>
                        </div>
                        <div class="divide-y divide-gray-50 dark:divide-slate-700 max-h-64 overflow-y-auto">
                            <a href="{{ route('superadmin.purchase-orders.index', ['status' => 'pending']) }}"
                                class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                                <div
                                    class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0 text-sm">
                                    🛒</div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800 dark:text-white">PO Menunggu Approval
                                    </p>
                                    <p class="text-xs text-gray-400">Beberapa menit lalu</p>
                                </div>
                            </a>
                            <a href="{{ route('superadmin.budget-requests.index', ['status' => 'pending']) }}"
                                class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                                <div
                                    class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0 text-sm">
                                    📋</div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800 dark:text-white">Pengajuan Anggaran
                                        Baru</p>
                                    <p class="text-xs text-gray-400">1 jam lalu</p>
                                </div>
                            </a>
                            <a href="{{ route('superadmin.stocks.low-stock') }}"
                                class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                                <div
                                    class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0 text-sm">
                                    ⚠️</div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800 dark:text-white">Stok Menipis
                                        Terdeteksi</p>
                                    <p class="text-xs text-gray-400">2 jam lalu</p>
                                </div>
                            </a>
                        </div>
                        <div class="px-4 py-2.5 border-t border-gray-100 dark:border-slate-700 text-center">
                            <a href="#" class="text-xs text-indigo-600 hover:underline">Lihat semua
                                notifikasi</a>
                        </div>
                    </div>
                </div>

                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        <div
                            class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-400 to-violet-500 flex items-center justify-center text-white text-xs font-bold">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <span
                            class="text-sm font-medium text-gray-700 dark:text-gray-200 hidden md:block">{{ Str::limit(auth()->user()->name ?? 'Admin', 14) }}</span>
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" @click.outside="open=false" x-transition
                        class="absolute right-0 top-full mt-2 w-48 card shadow-xl z-50 py-1 overflow-hidden">
                        <a href="#"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-slate-700">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Profil Saya
                        </a>
                        <a href="#"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-slate-700">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Pengaturan
                        </a>
                        <div class="border-t border-gray-100 dark:border-slate-700 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main id="page-content" class="flex-1">

            @if (session('success') || session('error') || session('warning'))
            <div class="fixed top-20 right-4 z-50 space-y-2 w-80" x-data="{ show: true }" x-show="show"
                x-init="setTimeout(() => show = false, 4500)" x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-8">
                @if (session('success'))
                <div class="toast toast-success">
                    <svg class="w-5 h-5 flex-shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <div class="flex-1 text-sm">{{ session('success') }}</div>
                    <button @click="show = false"
                        class="text-emerald-400 hover:text-emerald-700 ml-1 flex-shrink-0">✕</button>
                </div>
                @endif
                @if (session('error'))
                <div class="toast toast-error">
                    <svg class="w-5 h-5 flex-shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                    <div class="flex-1 text-sm">{{ session('error') }}</div>
                    <button @click="show = false" class="text-red-400 hover:text-red-700 ml-1 flex-shrink-0">✕</button>
                </div>
                @endif
                @if (session('warning'))
                <div class="toast toast-warning">
                    <svg class="w-5 h-5 flex-shrink-0 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    <div class="flex-1 text-sm">{{ session('warning') }}</div>
                    <button @click="show = false"
                        class="text-yellow-500 hover:text-yellow-700 ml-1 flex-shrink-0">✕</button>
                </div>
                @endif
            </div>
            @endif

            @if ($errors->any())
            <div class="mx-6 mt-4">
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex gap-3">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-red-800">Ada {{ $errors->count() }} kesalahan pada
                            form:</p>
                        <ul class="mt-1 list-disc list-inside text-sm text-red-700 space-y-0.5">
                            @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            @yield('content')
        </main>

        <footer
            class="px-6 py-3 flex items-center justify-between text-xs text-gray-400 border-t border-gray-100 dark:border-slate-700">
            <span>© {{ date('Y') }} <strong class="text-indigo-500">GudangPro</strong> — Super Admin Panel</span>
            <span>v1.0.0</span>
        </footer>
    </div>

    <script>
    function adminLayout() {
        return {
            collapsed: localStorage.getItem('sidebar_collapsed') === 'true',
            darkMode: localStorage.getItem('dark_mode') === 'true',
            mobileOpen: false,
            init() {
                this.$watch('collapsed', v => localStorage.setItem('sidebar_collapsed', v));
                this.$watch('darkMode', v => localStorage.setItem('dark_mode', v));
            }
        }
    }
    </script>

    <script>
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            // Paksa hilangkan backdrop yang mungkin macet karena transisi terhenti
            document.querySelectorAll('.modal-backdrop').forEach(el => {
                // Reset inline style paksa jika transisi meninggalkan opacity aneh
                el.style.display = 'none';
            });
        }
    });
    </script>
    @stack('scripts')
</body>

</html>


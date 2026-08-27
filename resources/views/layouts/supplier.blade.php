<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Portal Supplier')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap"
        rel="stylesheet">

    {{-- Kalau kamu punya build asset sendiri (Vite/app.css), boleh tetap disertakan di sini --}}
    {{-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> --}}

    <style>
    :root {
        --font-display: 'Space Grotesk', system-ui, sans-serif;
        --font-body: 'Inter', system-ui, sans-serif;
        --font-mono: 'IBM Plex Mono', ui-monospace, monospace;

        --ink: #17211D;
        --muted: #5B6862;
        --canvas: #EDF1EE;
        --paper: #FFFFFF;
        --line: #DCE3DE;

        --cargo: #0F3D37;
        --cargo-2: #175048;
        --cargo-ink: #0A2622;

        --amber: #D98E2B;
        --amber-2: #B9741A;
        --amber-soft: #F6E4C6;

        --info: #3E6FA8;
        --info-soft: #DCE6F1;
        --success: #2F7D5A;
        --success-soft: #DCEBE1;
        --danger: #B23A34;
        --danger-soft: #F3DEDC;
        --muted-soft: #E4E8E4;
    }

    * {
        box-sizing: border-box;
    }

    html,
    body {
        margin: 0;
        padding: 0;
        background: var(--canvas);
        color: var(--ink);
        font-family: var(--font-body);
        font-size: 15px;
        line-height: 1.5;
    }

    a {
        color: inherit;
    }

    h1,
    h2,
    h3,
    h4,
    .font-display {
        font-family: var(--font-display);
        letter-spacing: -0.01em;
        margin: 0;
    }

    code,
    .font-mono {
        font-family: var(--font-mono);
    }

    *:focus-visible {
        outline: 2px solid var(--amber);
        outline-offset: 2px;
    }

    /* ── Shell ─────────────────────────────────────────── */
    .app-shell {
        display: flex;
        min-height: 100vh;
    }

    .sidebar {
        width: 240px;
        flex-shrink: 0;
        background: var(--cargo);
        color: #E9F1EE;
        display: flex;
        flex-direction: column;
        position: sticky;
        top: 0;
        height: 100vh;
    }

    .sidebar-brand {
        padding: 22px 20px 18px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.12);
    }

    .sidebar-brand .eyebrow {
        display: block;
        font-family: var(--font-mono);
        font-size: 11px;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: #9FC2B7;
        margin-bottom: 4px;
    }

    .sidebar-brand strong {
        font-family: var(--font-display);
        font-size: 18px;
        font-weight: 600;
    }

    .sidebar-nav {
        flex: 1;
        padding: 16px 12px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .sidebar-nav a {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border-radius: 8px;
        text-decoration: none;
        color: #CFE1DB;
        font-size: 14px;
        font-weight: 500;
        transition: background 0.15s ease, color 0.15s ease;
    }

    .sidebar-nav a svg {
        flex-shrink: 0;
        opacity: 0.85;
    }

    .sidebar-nav a:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
    }

    .sidebar-nav a.active {
        background: var(--amber);
        color: var(--cargo-ink);
        font-weight: 600;
    }

    .sidebar-nav a.active svg {
        opacity: 1;
    }

    .sidebar-foot {
        padding: 16px;
        border-top: 1px solid rgba(255, 255, 255, 0.12);
    }

    .btn-logout {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 9px 12px;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.25);
        background: transparent;
        color: #E9F1EE;
        font-family: var(--font-body);
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.15s ease, border-color 0.15s ease;
    }

    .btn-logout:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.4);
    }

    .main {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
    }

    .topbar {
        height: 68px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 28px;
        background: var(--paper);
        border-bottom: 1px solid var(--line);
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .topbar h1 {
        font-size: 19px;
        font-weight: 600;
    }

    .topbar .subhead {
        font-size: 13px;
        color: var(--muted);
        margin-top: 2px;
    }

    .topbar-user {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--amber-soft);
        color: var(--amber-2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: var(--font-display);
        font-weight: 700;
        font-size: 13px;
    }

    .topbar-user .name {
        font-size: 13px;
        font-weight: 600;
        line-height: 1.2;
    }

    .topbar-user .role {
        font-size: 11.5px;
        color: var(--muted);
    }

    .content {
        padding: 28px;
        flex: 1;
    }

    .content-inner {
        animation: rise 0.35s ease both;
    }

    @keyframes rise {
        from {
            opacity: 0;
            transform: translateY(6px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        * {
            animation: none !important;
            transition: none !important;
        }
    }

    /* ── Section heading ──────────────────────────────── */
    .section-head {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        margin-bottom: 14px;
        gap: 12px;
        flex-wrap: wrap;
    }

    .section-head h2 {
        font-size: 16px;
        font-weight: 600;
    }

    .section-head .eyebrow {
        font-family: var(--font-mono);
        font-size: 11px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--muted);
    }

    /* ── Cards ─────────────────────────────────────────── */
    .card {
        background: var(--paper);
        border: 1px solid var(--line);
        border-radius: 10px;
        padding: 20px;
    }

    /* Signature: tag/label pengiriman untuk kartu statistik */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 28px;
    }

    .stat-tag {
        position: relative;
        background: var(--paper);
        border: 1px solid var(--line);
        border-left: none;
        clip-path: polygon(22px 0, 100% 0, 100% 100%, 22px 100%, 2px 50%);
        padding: 18px 18px 18px 32px;
    }

    .stat-tag::after {
        content: "";
        position: absolute;
        left: 11px;
        top: 50%;
        transform: translateY(-50%);
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--canvas);
        border: 1px solid var(--line);
    }

    .stat-tag .label {
        font-family: var(--font-mono);
        font-size: 11px;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--muted);
        display: block;
        margin-bottom: 6px;
    }

    .stat-tag .value {
        font-family: var(--font-display);
        font-size: 28px;
        font-weight: 700;
        color: var(--cargo-ink);
    }

    /* ── Tables ("manifes") ────────────────────────────── */
    .table-wrap {
        background: var(--paper);
        border: 1px solid var(--line);
        border-radius: 10px;
        overflow: hidden;
    }

    table.manifest {
        width: 100%;
        border-collapse: collapse;
    }

    table.manifest thead th {
        text-align: left;
        font-family: var(--font-mono);
        font-size: 11px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--muted);
        background: var(--canvas);
        padding: 12px 16px;
        border-bottom: 1px solid var(--line);
        font-weight: 500;
    }

    table.manifest td {
        padding: 13px 16px;
        border-bottom: 1px solid var(--line);
        font-size: 14px;
        vertical-align: middle;
    }

    table.manifest tbody tr:last-child td {
        border-bottom: none;
    }

    table.manifest tbody tr:hover {
        background: rgba(15, 61, 55, 0.03);
    }

    .mono {
        font-family: var(--font-mono);
        font-size: 13.5px;
    }

    .text-muted {
        color: var(--muted);
    }

    .text-right {
        text-align: right;
    }

    /* ── Stempel status ────────────────────────────────── */
    .stamp {
        display: inline-block;
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        padding: 3px 10px;
        border: 1.5px solid currentColor;
        border-radius: 4px;
        transform: rotate(-2deg);
        white-space: nowrap;
    }

    .stamp-info {
        color: var(--info);
        background: var(--info-soft);
    }

    .stamp-success {
        color: var(--success);
        background: var(--success-soft);
    }

    .stamp-danger {
        color: var(--danger);
        background: var(--danger-soft);
    }

    .stamp-amber {
        color: var(--amber-2);
        background: var(--amber-soft);
    }

    .stamp-muted {
        color: var(--muted);
        background: var(--muted-soft);
    }

    /* ── Forms & buttons ───────────────────────────────── */
    .field-inline {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
    }

    .input,
    .select {
        font-family: var(--font-body);
        font-size: 13.5px;
        padding: 9px 12px;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: var(--paper);
        color: var(--ink);
    }

    .input:focus,
    .select:focus {
        border-color: var(--cargo);
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-family: var(--font-body);
        font-size: 13.5px;
        font-weight: 600;
        padding: 9px 16px;
        border-radius: 8px;
        border: 1px solid transparent;
        cursor: pointer;
        text-decoration: none;
        transition: filter 0.15s ease, background 0.15s ease;
    }

    .btn-primary {
        background: var(--cargo);
        color: #fff;
    }

    .btn-primary:hover {
        background: var(--cargo-2);
    }

    .btn-outline {
        background: transparent;
        border-color: var(--line);
        color: var(--ink);
    }

    .btn-outline:hover {
        border-color: var(--cargo);
        color: var(--cargo);
    }

    .btn-ghost {
        background: transparent;
        color: var(--cargo);
        padding-left: 4px;
        padding-right: 4px;
    }

    .btn-ghost:hover {
        text-decoration: underline;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 12.5px;
    }

    /* ── Detail meta grid ──────────────────────────────── */
    .meta-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .meta-item .label {
        font-family: var(--font-mono);
        font-size: 11px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--muted);
        margin-bottom: 4px;
        display: block;
    }

    .meta-item .value {
        font-size: 14.5px;
        font-weight: 500;
    }

    .total-line {
        display: flex;
        justify-content: flex-end;
        align-items: baseline;
        gap: 10px;
        padding: 16px 20px;
        border-top: 2px dashed var(--line);
        margin-top: -1px;
    }

    .total-line .label {
        font-size: 13px;
        color: var(--muted);
    }

    .total-line .value {
        font-family: var(--font-mono);
        font-size: 20px;
        font-weight: 600;
        color: var(--cargo-ink);
    }

    /* ── Empty state ───────────────────────────────────── */
    .empty-state {
        text-align: center;
        padding: 56px 24px;
        color: var(--muted);
    }

    .empty-state svg {
        margin-bottom: 12px;
        opacity: 0.5;
    }

    .empty-state .title {
        font-family: var(--font-display);
        font-size: 15px;
        font-weight: 600;
        color: var(--ink);
        margin-bottom: 4px;
    }

    .empty-state .desc {
        font-size: 13.5px;
        max-width: 340px;
        margin: 0 auto;
    }

    /* ── Pagination (styling default Laravel pagination links) ── */
    .pagination-wrap {
        margin-top: 18px;
    }

    .pagination-wrap nav {
        font-family: var(--font-body);
        font-size: 13px;
    }

    /* ── Responsive ────────────────────────────────────── */
    @media (max-width: 980px) {
        .stat-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .meta-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 860px) {
        .app-shell {
            flex-direction: column;
        }

        .sidebar {
            width: 100%;
            height: auto;
            position: static;
            flex-direction: row;
            align-items: center;
            padding: 0 12px;
        }

        .sidebar-brand {
            border: none;
            padding: 12px 10px;
        }

        .sidebar-nav {
            flex-direction: row;
            padding: 8px;
            overflow-x: auto;
        }

        .sidebar-nav a span {
            display: none;
        }

        .sidebar-foot {
            border: none;
            padding: 8px 12px;
        }

        .btn-logout {
            width: auto;
            padding: 8px 10px;
        }

        .content {
            padding: 18px;
        }
    }

    @media (max-width: 560px) {

        .stat-grid,
        .meta-grid {
            grid-template-columns: 1fr;
        }

        .topbar {
            padding: 0 16px;
        }
    }
    </style>

    @stack('styles')
</head>

<body>
    <div class="app-shell">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <span class="eyebrow">Manifes Digital</span>
                <strong>Portal Supplier</strong>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('supplier.dashboard') }}"
                    class="{{ request()->routeIs('supplier.dashboard') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8">
                        <rect x="3" y="3" width="7" height="9" rx="1.5" />
                        <rect x="14" y="3" width="7" height="5" rx="1.5" />
                        <rect x="14" y="12" width="7" height="9" rx="1.5" />
                        <rect x="3" y="16" width="7" height="5" rx="1.5" />
                    </svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('supplier.products.index') }}"
                    class="{{ request()->routeIs('supplier.products.*') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8">
                        <path d="M21 8 12 3 3 8v8l9 5 9-5V8Z" />
                        <path d="M3 8l9 5 9-5" />
                        <path d="M12 13v8" />
                    </svg>
                    <span>Produk Saya</span>
                </a>
                <a href="{{ route('supplier.purchase-orders.index') }}"
                    class="{{ request()->routeIs('supplier.purchase-orders.*') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8">
                        <rect x="3" y="7" width="13" height="11" rx="1.5" />
                        <path d="M16 11h3.3a1 1 0 0 1 .8.4l1.9 2.5v3a1 1 0 0 1-1 1H16" />
                        <circle cx="7.5" cy="19.5" r="1.5" />
                        <circle cx="17.5" cy="19.5" r="1.5" />
                    </svg>
                    <span>Purchase Order</span>
                </a>
            </nav>

            <div class="sidebar-foot">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                            <path d="M16 17l5-5-5-5" />
                            <path d="M21 12H9" />
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <div class="main">
            <header class="topbar">
                <div>
                    <h1>@yield('header', 'Dashboard')</h1>
                    @hasSection('subheader')
                    <div class="subhead">@yield('subheader')</div>
                    @endif
                </div>
                <div class="topbar-user">
                    <div>
                        <div class="name" style="text-align:right">
                            {{ auth()->user()->supplier->name ?? auth()->user()->name }}</div>
                        <div class="role" style="text-align:right">Akun Supplier</div>
                    </div>
                    <div class="avatar">
                        {{ strtoupper(substr(auth()->user()->supplier->name ?? auth()->user()->name, 0, 2)) }}</div>
                </div>
            </header>

            <main class="content">
                <div class="content-inner">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>

</html>
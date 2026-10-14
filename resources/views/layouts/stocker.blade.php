<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Stocker') — GudangPro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root { --ink:#0f172a; --muted:#64748b; --line:#e2e8f0; --bg:#f8fafc; --nav:#0b1437; --nav-act:#1b2a63; --brand:#4f46e5; --danger:#dc2626; --danger-soft:#fee2e2; }
        * { box-sizing: border-box; }
        body { margin:0; font-family:'Inter',system-ui,sans-serif; font-size:14px; color:var(--ink); background:var(--bg); }
        .shell { display:flex; min-height:100vh; }
        .sidebar { width:232px; background:var(--nav); color:#cbd5e1; padding:20px 12px; position:sticky; top:0; height:100vh; flex-shrink:0; }
        .brand { display:flex; align-items:center; gap:10px; padding:0 8px 22px; color:#fff; font-weight:700; font-size:16px; }
        .brand i { width:30px; height:30px; border-radius:8px; background:linear-gradient(135deg,#6366f1,#8b5cf6); display:inline-block; }
        .group { font-size:10px; letter-spacing:.14em; text-transform:uppercase; color:#64748b; padding:14px 10px 6px; }
        .sidebar a { display:flex; align-items:center; justify-content:space-between; gap:8px; padding:9px 12px; margin-bottom:2px; border-radius:8px; color:#e2e8f0; text-decoration:none; font-weight:500; }
        .sidebar a:hover { background:rgba(255,255,255,.06); }
        .sidebar a.active { background:var(--nav-act); color:#fff; }
        .tag { font-size:9px; background:#e2e8f0; color:#334155; border-radius:6px; padding:2px 6px; font-family:'IBM Plex Mono',monospace; }
        .main { flex:1; min-width:0; }
        .topbar { background:#fff; border-bottom:1px solid var(--line); padding:14px 28px; display:flex; justify-content:space-between; align-items:center; }
        .topbar h1 { margin:0; font-size:16px; }
        .topbar .who { color:var(--muted); margin-right:14px; }
        .btn { display:inline-block; border:1px solid var(--line); background:#fff; border-radius:8px; padding:7px 14px; font:inherit; font-weight:600; cursor:pointer; text-decoration:none; color:var(--ink); }
        .btn-danger { color:var(--danger); border-color:var(--danger); }
        .btn-primary { background:var(--brand); border-color:var(--brand); color:#fff; }
        .content { padding:24px 28px 48px; }
        .page-head { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; }
        .page-head h2 { margin:0 0 4px; font-size:22px; }
        .page-head p { margin:0; color:var(--muted); }
        .pill { background:#eef2ff; color:#3730a3; border-radius:999px; padding:4px 10px; font-size:11px; font-weight:600; }
        .grid3 { display:grid; grid-template-columns:repeat(3,1fr); gap:18px; margin-bottom:22px; }
        .grid2 { display:grid; grid-template-columns:1.5fr 1fr; gap:18px; }
        .card { background:#fff; border:1px solid var(--line); border-radius:14px; padding:20px; }
        .stat { display:flex; align-items:center; gap:16px; }
        .stat .ico { width:44px; height:44px; border-radius:10px; background:#eef2ff; }
        .stat .lbl { font-size:12px; color:var(--muted); }
        .stat .val { font-size:26px; font-weight:700; }
        .card h3 { margin:0 0 14px; font-size:14px; }
        .card h3 span { color:#94a3b8; font-weight:400; }
        table { width:100%; border-collapse:collapse; }
        th { text-align:left; font-size:11px; letter-spacing:.06em; text-transform:uppercase; color:var(--muted); background:#f1f5f9; padding:10px 14px; }
        td { padding:12px 14px; border-top:1px solid var(--line); }
        th.r, td.r { text-align:right; }
        .muted { color:#94a3b8; }
        .mono { font-family:'IBM Plex Mono',monospace; font-size:12px; }
        .badge { display:inline-block; min-width:36px; text-align:center; padding:2px 10px; border-radius:999px; font-size:12px; font-weight:700; background:var(--danger-soft); color:var(--danger); }
        .badge.ok { background:#dcfce7; color:#166534; }
        .filters { display:flex; gap:10px; margin-bottom:16px; flex-wrap:wrap; }
        .filters input, .filters select { padding:9px 12px; border:1px solid var(--line); border-radius:8px; font:inherit; background:#fff; }
        .filters input { min-width:280px; }
        .pager { margin-top:16px; }
        .empty { text-align:center; padding:36px; color:var(--muted); }
        @media (max-width: 900px) { .sidebar{display:none} .grid3,.grid2{grid-template-columns:1fr} .content{padding:16px} }
    </style>
</head>
<body>
<div class="shell">
    <aside class="sidebar">
        <div class="brand"><i></i> GudangPro</div>
        <div class="group">Utama</div>
        <a href="{{ route('stocker.dashboard') }}" class="{{ request()->routeIs('stocker.dashboard') ? 'active' : '' }}">Dashboard</a>
        <div class="group">Stok</div>
        <a href="{{ route('stocker.stocks.index') }}" class="{{ request()->routeIs('stocker.stocks.index') ? 'active' : '' }}">Semua Stok <span class="tag">read-only</span></a>
        <a href="{{ route('stocker.stocks.low-stock') }}" class="{{ request()->routeIs('stocker.stocks.low-stock') ? 'active' : '' }}">Stok Menipis</a>
    </aside>
    <div class="main">
        <div class="topbar">
            <h1>@yield('title', 'Dashboard')</h1>
            <div style="display:flex;align-items:center">
                <span class="who">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button class="btn btn-danger" type="submit">Logout</button>
                </form>
            </div>
        </div>
        <div class="content">@yield('content')</div>
    </div>
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>StockFlow – Smart Warehouse Management System</title>
    <meta name="description"
        content="Satu platform modern untuk mengelola stok barang, supplier, transaksi, laporan, dan aktivitas pengguna secara real-time." />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet" />
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link rel="stylesheet" href="frontend/css/style.css" />
</head>

<body>

    <!-- Background Floating Shapes -->
    <div class="bg-shapes" aria-hidden="true">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
        <div class="shape shape-4"></div>
        <div class="shape shape-5"></div>
    </div>

    <!-- Navbar -->
    {{-- <nav class="navbar" id="navbar">
        <div class="nav-container">
            <a class="nav-logo" href="#home">
                <div class="logo-icon">
                    <i data-lucide="box"></i>
                </div>
                <span>StockFlow</span>
            </a>

            <div class="nav-links" id="navLinks">
                <a href="#home" class="nav-link active">Home</a>
                <a href="#features" class="nav-link">Features</a>
                <a href="#dashboard" class="nav-link">Dashboard</a>
                <a href="#benefits" class="nav-link">Benefits</a>
                <a href="#faq" class="nav-link">FAQ</a>
                <a href="#contact" class="nav-link">Contact</a>

                            <div class="nav-actions">
                <a href="{{ route('login') }}" class="btn-ghost">Login</a>
    <a href="{{ route('register') }}" class="btn-primary ripple">Get Started</a>
    </div>
    </div>



    <button class="nav-toggle" id="navToggle" aria-label="Toggle menu">
        <i data-lucide="menu"></i>
    </button>
    </div>
    </nav> --}}

    <nav class="navbar" id="navbar">

        <div class="nav-container">

            <a class="nav-logo" href="#home">

                <div class="logo-icon">

                    <i data-lucide="box"></i>

                </div>

                <span>StockFlow</span>

            </a>

            <div class="nav-links" id="navLinks">

                <a href="#home" class="nav-link active">Home</a>

                <a href="#features" class="nav-link">Features</a>

                <a href="#dashboard" class="nav-link">Dashboard</a>

                <a href="#benefits" class="nav-link">Benefits</a>

                <a href="#faq" class="nav-link">FAQ</a>

                <a href="#contact" class="nav-link">Contact</a>

                <div class="nav-actions-mobile">

                    <a href="{{ route('login') }}" class="btn-ghost">Login</a>

                    <a href="{{ route('register') }}" class="btn-primary ripple">Get Started</a>

                </div>

            </div>

            <button class="nav-toggle" id="navToggle" aria-label="Toggle menu">

                <i data-lucide="menu"></i>

            </button>

        </div>

    </nav>

    {{-- <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text fade-up">
                    <div class="badge">
                        <span class="badge-dot"></span>
                        ✨ Smart Warehouse Management System
                    </div>
                    <h1 class="hero-title">
                        Kelola Gudang<br />
                        <span class="gradient-text">Lebih Cepat.</span><br />
                        Pantau Stok<br />
                        <span class="gradient-text">Secara Real-Time.</span><br />
                        Semua Dalam<br />
                        <span class="gradient-text">Satu Dashboard.</span>
                    </h1>
                    <p class="hero-subtitle">
                        Satu platform modern untuk mengelola stok barang, supplier, transaksi barang masuk dan keluar,
                        laporan, serta aktivitas pengguna secara real-time.
                    </p>
                    <div class="hero-actions">
                        <a href="#" class="btn-primary btn-lg ripple">
                            <i data-lucide="rocket"></i>
                            Start Free
                        </a>
                        <a href="#" class="btn-outline btn-lg ripple">
                            <i data-lucide="play-circle"></i>
                            Book Demo
                        </a>
                    </div>
                    <div class="hero-trust">
                        <div class="trust-avatars">
                            <div class="avatar av1"></div>
                            <div class="avatar av2"></div>
                            <div class="avatar av3"></div>
                            <div class="avatar av4"></div>
                        </div>
                        <span class="trust-text"><strong>500+</strong> perusahaan mempercayai StockFlow</span>
                    </div>
                </div>

                <div class="hero-visual fade-right" id="heroVisual">
                    <!-- Dashboard Mockup -->
                    <div class="dashboard-mockup">
                        <div class="mockup-header">
                            <div class="mockup-dots">
                                <span></span><span></span><span></span>
                            </div>
                            <div class="mockup-title-bar">StockFlow Dashboard</div>
                            <div class="mockup-actions">
                                <div class="mockup-avatar-sm"></div>
                            </div>
                        </div>
                        <div class="mockup-body">
                            <!-- Sidebar -->
                            <div class="mock-sidebar">
                                <div class="mock-logo-sm">
                                    <div class="mock-logo-icon"></div>
                                    <div class="mock-logo-text"></div>
                                </div>
                                <div class="mock-nav">
                                    <div class="mock-nav-item active"></div>
                                    <div class="mock-nav-item"></div>
                                    <div class="mock-nav-item"></div>
                                    <div class="mock-nav-item"></div>
                                    <div class="mock-nav-item"></div>
                                    <div class="mock-nav-item"></div>
                                </div>
                            </div>
                            <!-- Main Content -->
                            <div class="mock-main">
                                <!-- Stat Cards -->
                                <div class="mock-stat-cards">
                                    <div class="mock-stat-card blue">
                                        <div class="mock-stat-icon"></div>
                                        <div class="mock-stat-info">
                                            <div class="mock-stat-num"></div>
                                            <div class="mock-stat-label"></div>
                                        </div>
                                    </div>
                                    <div class="mock-stat-card green">
                                        <div class="mock-stat-icon"></div>
                                        <div class="mock-stat-info">
                                            <div class="mock-stat-num"></div>
                                            <div class="mock-stat-label"></div>
                                        </div>
                                    </div>
                                    <div class="mock-stat-card purple">
                                        <div class="mock-stat-icon"></div>
                                        <div class="mock-stat-info">
                                            <div class="mock-stat-num"></div>
                                            <div class="mock-stat-label"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Charts Row -->
                                <div class="mock-charts">
                                    <!-- Bar Chart -->
                                    <div class="mock-chart-box">
                                        <div class="mock-chart-header">
                                            <div class="mock-chart-title"></div>
                                        </div>
                                        <div class="mock-bar-chart">
                                            <div class="bar b1"></div>
                                            <div class="bar b2"></div>
                                            <div class="bar b3"></div>
                                            <div class="bar b4"></div>
                                            <div class="bar b5"></div>
                                            <div class="bar b6"></div>
                                            <div class="bar b7"></div>
                                        </div>
                                    </div>
                                    <!-- Pie Chart -->
                                    <div class="mock-chart-box small">
                                        <div class="mock-chart-header">
                                            <div class="mock-chart-title"></div>
                                        </div>
                                        <div class="mock-pie">
                                            <div class="pie-circle"></div>
                                            <div class="pie-legend">
                                                <div class="pie-leg-item"></div>
                                                <div class="pie-leg-item"></div>
                                                <div class="pie-leg-item"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Table -->
                                <div class="mock-table-box">
                                    <div class="mock-table-header">
                                        <div class="mock-table-title"></div>
                                        <div class="mock-table-btn"></div>
                                    </div>
                                    <div class="mock-table">
                                        <div class="mock-row header-row">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                        </div>
                                        <div class="mock-row">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div class="status-badge green"></div>
                                        </div>
                                        <div class="mock-row">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div class="status-badge blue"></div>
                                        </div>
                                        <div class="mock-row">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div class="status-badge yellow"></div>
                                        </div>
                                        <div class="mock-row">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div class="status-badge green"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Floating Cards -->
                    <div class="floating-card fc1 float-1">
                        <div class="fc-icon green"><i data-lucide="trending-up"></i></div>
                        <div class="fc-info">
                            <div class="fc-title">+125 Barang Masuk</div>
                            <div class="fc-sub">Hari ini · 14:30</div>
                        </div>
                    </div>
                    <div class="floating-card fc2 float-2">
                        <div class="fc-icon blue"><i data-lucide="refresh-cw"></i></div>
                        <div class="fc-info">
                            <div class="fc-title">Stock Updated</div>
                            <div class="fc-sub">Real-time sync</div>
                        </div>
                    </div>
                    <div class="floating-card fc3 float-3">
                        <div class="fc-icon cyan"><i data-lucide="check-circle-2"></i></div>
                        <div class="fc-info">
                            <div class="fc-title">Inventory Synced</div>
                            <div class="fc-sub">99.9% accuracy</div>
                        </div>
                    </div>
                    <div class="floating-card fc4 float-1">
                        <div class="fc-icon purple"><i data-lucide="user-plus"></i></div>
                        <div class="fc-info">
                            <div class="fc-title">Supplier Added</div>
                            <div class="fc-sub">PT. Mitra Logistik</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}




    <!-- Hero Section (DINAMIS dari database) -->
    <section class="hero" id="home">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text fade-up">
                    <div class="badge">
                        <span class="badge-dot"></span>
                        ✨ {{ $hero->badge_text }}
                    </div>
                    <h1 class="hero-title">
                        {{ $hero->title_line_1 }}<br />
                        <span class="gradient-text">{{ $hero->title_line_1_highlight }}</span><br />
                        {{ $hero->title_line_2 }}<br />
                        <span class="gradient-text">{{ $hero->title_line_2_highlight }}</span><br />
                        {{ $hero->title_line_3 }}<br />
                        <span class="gradient-text">{{ $hero->title_line_3_highlight }}</span>
                    </h1>
                    <p class="hero-subtitle">
                        {{ $hero->subtitle }}
                    </p>
                    <div class="hero-actions">
                        <a href="{{ $hero->cta_primary_url }}" class="btn-primary btn-lg ripple">
                            <i data-lucide="rocket"></i>
                            {{ $hero->cta_primary_text }}
                        </a>
                        <a href="{{ $hero->cta_secondary_url }}" class="btn-outline btn-lg ripple">
                            <i data-lucide="play-circle"></i>
                            {{ $hero->cta_secondary_text }}
                        </a>
                    </div>
                    <div class="hero-trust">
                        <div class="trust-avatars">
                            <div class="avatar av1"></div>
                            <div class="avatar av2"></div>
                            <div class="avatar av3"></div>
                            <div class="avatar av4"></div>
                        </div>
                        <span class="trust-text"><strong>{{ $hero->trust_count }}</strong>
                            {{ $hero->trust_text }}</span>
                    </div>
                </div>

                <div class="hero-visual fade-right" id="heroVisual">
                    {{-- Dashboard mockup ilustrasi tetap statis (dekorasi CSS, bukan data) --}}
                    <div class="dashboard-mockup">
                        <div class="mockup-header">
                            <div class="mockup-dots"><span></span><span></span><span></span></div>
                            <div class="mockup-title-bar">StockFlow Dashboard</div>
                            <div class="mockup-actions">
                                <div class="mockup-avatar-sm"></div>
                            </div>
                        </div>
                        <div class="mockup-body">
                            <div class="mock-sidebar">
                                <div class="mock-logo-sm">
                                    <div class="mock-logo-icon"></div>
                                    <div class="mock-logo-text"></div>
                                </div>
                                <div class="mock-nav">
                                    <div class="mock-nav-item active"></div>
                                    <div class="mock-nav-item"></div>
                                    <div class="mock-nav-item"></div>
                                    <div class="mock-nav-item"></div>
                                    <div class="mock-nav-item"></div>
                                    <div class="mock-nav-item"></div>
                                </div>
                            </div>
                            <div class="mock-main">
                                <div class="mock-stat-cards">
                                    <div class="mock-stat-card blue">
                                        <div class="mock-stat-icon"></div>
                                        <div class="mock-stat-info">
                                            <div class="mock-stat-num"></div>
                                            <div class="mock-stat-label"></div>
                                        </div>
                                    </div>
                                    <div class="mock-stat-card green">
                                        <div class="mock-stat-icon"></div>
                                        <div class="mock-stat-info">
                                            <div class="mock-stat-num"></div>
                                            <div class="mock-stat-label"></div>
                                        </div>
                                    </div>
                                    <div class="mock-stat-card purple">
                                        <div class="mock-stat-icon"></div>
                                        <div class="mock-stat-info">
                                            <div class="mock-stat-num"></div>
                                            <div class="mock-stat-label"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mock-charts">
                                    <div class="mock-chart-box">
                                        <div class="mock-chart-header">
                                            <div class="mock-chart-title"></div>
                                        </div>
                                        <div class="mock-bar-chart">
                                            <div class="bar b1"></div>
                                            <div class="bar b2"></div>
                                            <div class="bar b3"></div>
                                            <div class="bar b4"></div>
                                            <div class="bar b5"></div>
                                            <div class="bar b6"></div>
                                            <div class="bar b7"></div>
                                        </div>
                                    </div>
                                    <div class="mock-chart-box small">
                                        <div class="mock-chart-header">
                                            <div class="mock-chart-title"></div>
                                        </div>
                                        <div class="mock-pie">
                                            <div class="pie-circle"></div>
                                            <div class="pie-legend">
                                                <div class="pie-leg-item"></div>
                                                <div class="pie-leg-item"></div>
                                                <div class="pie-leg-item"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mock-table-box">
                                    <div class="mock-table-header">
                                        <div class="mock-table-title"></div>
                                        <div class="mock-table-btn"></div>
                                    </div>
                                    <div class="mock-table">
                                        <div class="mock-row header-row">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                        </div>
                                        <div class="mock-row">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div class="status-badge green"></div>
                                        </div>
                                        <div class="mock-row">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div class="status-badge blue"></div>
                                        </div>
                                        <div class="mock-row">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div class="status-badge yellow"></div>
                                        </div>
                                        <div class="mock-row">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div class="status-badge green"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Floating cards (DINAMIS dari database) --}}
                    @php $floatAnim = ['float-1', 'float-2', 'float-3']; @endphp
                    @foreach ($heroHighlights as $highlight)
                    <div class="floating-card fc{{ $loop->iteration }} {{ $floatAnim[$loop->index % 3] }}">
                        <div class="fc-icon {{ $highlight->color }}"><i data-lucide="{{ $highlight->icon }}"></i></div>
                        <div class="fc-info">
                            <div class="fc-title">{{ $highlight->title }}</div>
                            <div class="fc-sub">{{ $highlight->subtitle }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid fade-up">
                @foreach ($stats as $stat)
                <div class="stat-item">
                    @if ($stat->is_static)
                    <div class="stat-value stat-static">{{ $stat->static_value }}</div>
                    @else
                    <div class="stat-value" data-target="{{ $stat->target }}" data-suffix="{{ $stat->suffix }}"
                        data-decimal="{{ $stat->decimal ?? 0 }}">0</div>
                    @endif
                    <div class="stat-label">{{ $stat->label }}</div>
                    <div class="stat-bar">
                        <div class="stat-bar-fill" style="width: {{ $stat->bar_width }}%"></div>
                    </div>
                </div>
                @if (!$loop->last)
                <div class="stat-divider"></div>
                @endif
                @endforeach
            </div>
        </div>
    </section>

    <!-- Client Logo Marquee -->
    <section class="marquee-section">
        <div class="marquee-label fade-up">Dipercaya oleh perusahaan terkemuka Indonesia</div>
        <div class="marquee-track">
            <div class="marquee-inner">
                <div class="marquee-logo"><i data-lucide="building-2"></i><span>Astra</span></div>
                <div class="marquee-logo"><i data-lucide="droplets"></i><span>Pertamina</span></div>
                <div class="marquee-logo"><i data-lucide="shopping-bag"></i><span>Indomaret</span></div>
                <div class="marquee-logo"><i data-lucide="shopping-cart"></i><span>Shopee</span></div>
                <div class="marquee-logo"><i data-lucide="store"></i><span>Tokopedia</span></div>
                <div class="marquee-logo"><i data-lucide="warehouse"></i><span>Mitra Gudang</span></div>
                <!-- Duplicate for infinite scroll -->
                <div class="marquee-logo"><i data-lucide="building-2"></i><span>Astra</span></div>
                <div class="marquee-logo"><i data-lucide="droplets"></i><span>Pertamina</span></div>
                <div class="marquee-logo"><i data-lucide="shopping-bag"></i><span>Indomaret</span></div>
                <div class="marquee-logo"><i data-lucide="shopping-cart"></i><span>Shopee</span></div>
                <div class="marquee-logo"><i data-lucide="store"></i><span>Tokopedia</span></div>
                <div class="marquee-logo"><i data-lucide="warehouse"></i><span>Mitra Gudang</span></div>
            </div>
        </div>
    </section>

    <!-- Problem Section -->
    <section class="problem-section section-pad">
        <div class="container">
            <div class="section-header fade-up">
                <div class="section-badge">Pain Points</div>
                <h2 class="section-title">Masih Kelola Gudang<br /><span class="gradient-text">Secara Manual?</span>
                </h2>
                <p class="section-sub">Banyak bisnis masih terjebak dalam cara lama yang lambat, rawan error, dan sulit
                    dipantau.</p>
            </div>
            <div class="problem-grid fade-up">
                <div class="problem-card">
                    <div class="problem-icon red"><i data-lucide="file-spreadsheet"></i></div>
                    <h3>Spreadsheet Berantakan</h3>
                    <p>Data tersebar di ratusan file Excel tanpa sinkronisasi, membuat tracking menjadi mimpi buruk.</p>
                </div>
                <div class="problem-card">
                    <div class="problem-icon orange"><i data-lucide="alert-triangle"></i></div>
                    <h3>Human Error</h3>
                    <p>Kesalahan input manual menyebabkan ketidakakuratan data yang berdampak besar pada operasional.
                    </p>
                </div>
                <div class="problem-card">
                    <div class="problem-icon red"><i data-lucide="package-x"></i></div>
                    <h3>Barang Hilang</h3>
                    <p>Tanpa sistem tracking yang baik, barang mudah hilang, salah kirim, atau tidak tercatat.</p>
                </div>
                <div class="problem-card">
                    <div class="problem-icon orange"><i data-lucide="clipboard-x"></i></div>
                    <h3>Laporan Manual</h3>
                    <p>Membuat laporan membutuhkan waktu berjam-jam padahal keputusan bisnis butuh data instan.</p>
                </div>
                <div class="problem-card">
                    <div class="problem-icon red"><i data-lucide="bar-chart-2"></i></div>
                    <h3>Stock Tidak Akurat</h3>
                    <p>Selisih antara stok fisik dan catatan membuat proses order dan penjualan menjadi kacau.</p>
                </div>
                <div class="problem-card">
                    <div class="problem-icon orange"><i data-lucide="eye-off"></i></div>
                    <h3>Sulit Monitoring</h3>
                    <p>Tidak ada visibilitas real-time membuat manajer buta terhadap kondisi gudang sebenarnya.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Solution Section (Bento Grid) -->
    <section class="solution-section section-pad" id="features">
        <div class="container">
            <div class="section-header fade-up">
                <div class="section-badge">Solusi</div>
                <h2 class="section-title">Semua Yang Anda Butuhkan<br /><span class="gradient-text">Dalam Satu
                        Platform</span></h2>
                <p class="section-sub">StockFlow hadir dengan fitur lengkap untuk mengotomasi seluruh proses gudang
                    Anda.</p>
            </div>
            <div class="bento-grid fade-up">
                <div class="bento-card bento-lg">
                    <div class="bento-icon blue"><i data-lucide="package"></i></div>
                    <h3>Inventory Management</h3>
                    <p>Kelola ribuan SKU dengan tracking real-time, batch management, dan expiry monitoring otomatis.
                    </p>
                    <div class="bento-visual">
                        <div class="mini-inventory">
                            <div class="inv-row"><span class="inv-name">SKU-001 · Laptop</span><span
                                    class="inv-stock blue">248</span></div>
                            <div class="inv-row"><span class="inv-name">SKU-002 · Monitor</span><span
                                    class="inv-stock green">132</span></div>
                            <div class="inv-row"><span class="inv-name">SKU-003 · Keyboard</span><span
                                    class="inv-stock yellow">12</span></div>
                            <div class="inv-row"><span class="inv-name">SKU-004 · Mouse</span><span
                                    class="inv-stock green">89</span></div>
                        </div>
                    </div>
                </div>
                <div class="bento-card bento-sm">
                    <div class="bento-icon cyan"><i data-lucide="users"></i></div>
                    <h3>Supplier Management</h3>
                    <p>Kelola data supplier, PO, dan riwayat transaksi dengan mudah.</p>
                </div>
                <div class="bento-card bento-sm">
                    <div class="bento-icon purple"><i data-lucide="warehouse"></i></div>
                    <h3>Warehouse</h3>
                    <p>Mapping area gudang, zone management, dan slot optimization.</p>
                </div>
                <div class="bento-card bento-sm">
                    <div class="bento-icon green"><i data-lucide="shopping-cart"></i></div>
                    <h3>Purchase Order</h3>
                    <p>Buat dan track PO dari request hingga barang diterima.</p>
                </div>
                <div class="bento-card bento-sm">
                    <div class="bento-icon blue"><i data-lucide="send"></i></div>
                    <h3>Sales Order</h3>
                    <p>Proses SO, picking, packing, dan pengiriman dalam satu alur.</p>
                </div>
                <div class="bento-card bento-md">
                    <div class="bento-icon orange"><i data-lucide="bar-chart-3"></i></div>
                    <h3>Reports & Analytics</h3>
                    <p>Laporan komprehensif dengan visualisasi data yang mudah dipahami dan dapat diexport ke berbagai
                        format.</p>
                    <div class="bento-mini-chart">
                        <div class="mini-bar" style="height:40%"></div>
                        <div class="mini-bar" style="height:65%"></div>
                        <div class="mini-bar" style="height:45%"></div>
                        <div class="mini-bar" style="height:80%"></div>
                        <div class="mini-bar" style="height:55%"></div>
                        <div class="mini-bar" style="height:90%"></div>
                        <div class="mini-bar" style="height:70%"></div>
                    </div>
                </div>
                <div class="bento-card bento-sm">
                    <div class="bento-icon cyan"><i data-lucide="scan-barcode"></i></div>
                    <h3>Barcode Scanner</h3>
                    <p>Scan barcode/QR untuk proses yang lebih cepat dan akurat.</p>
                </div>
                <div class="bento-card bento-sm">
                    <div class="bento-icon purple"><i data-lucide="activity"></i></div>
                    <h3>Activity Log</h3>
                    <p>Rekam jejak semua aktivitas pengguna dan transaksi sistem.</p>
                </div>
                <div class="bento-card bento-sm">
                    <div class="bento-icon green"><i data-lucide="bell"></i></div>
                    <h3>Notification</h3>
                    <p>Alert otomatis untuk stok rendah, PO jatuh tempo, dan anomali.</p>
                </div>
                <div class="bento-card bento-sm">
                    <div class="bento-icon blue"><i data-lucide="pie-chart"></i></div>
                    <h3>Analytics</h3>
                    <p>Dashboard analitik mendalam dengan insight bisnis berbasis AI.</p>
                </div>
                <div class="bento-card bento-sm">
                    <div class="bento-icon orange"><i data-lucide="shield-check"></i></div>
                    <h3>Role Permission</h3>
                    <p>Kontrol akses granular untuk setiap pengguna dan departemen.</p>
                </div>
                <div class="bento-card bento-sm">
                    <div class="bento-icon cyan"><i data-lucide="git-branch"></i></div>
                    <h3>Multi Warehouse</h3>
                    <p>Kelola banyak gudang di berbagai lokasi dalam satu platform.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Feature Section -->
    <section class="feature-section section-pad">
        <div class="container">
            <div class="section-header fade-up">
                <div class="section-badge">{{ $featureHeader->badge }}</div>
                <h2 class="section-title">{{ $featureHeader->title_normal }}<br /><span
                        class="gradient-text">{{ $featureHeader->title_gradient }}</span></h2>
                <p class="section-sub">{{ $featureHeader->subtitle }}</p>
            </div>
            <div class="feature-grid fade-up">
                @foreach ($features as $feature)
                <div class="feature-card glow-{{ $feature->color }}">
                    <div class="feature-icon {{ $feature->color }}"><i data-lucide="{{ $feature->icon }}"></i></div>
                    <h4>{{ $feature->title }}</h4>
                    <p>{{ $feature->description }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Dashboard Preview Section (DINAMIS dari database) -->
    <section class="dashboard-section section-pad" id="dashboard">
        <div class="container">
            <div class="section-header fade-up">
                <div class="section-badge">{{ $dashboardHeader->badge }}</div>
                <h2 class="section-title">{{ $dashboardHeader->title_normal }}<br /><span
                        class="gradient-text">{{ $dashboardHeader->title_gradient }}</span></h2>
                <p class="section-sub">{{ $dashboardHeader->subtitle }}</p>
            </div>

            <div class="dashboard-preview fade-up">
                <div class="dp-window">
                    <div class="dp-header">
                        <div class="dp-dots"><span></span><span></span><span></span></div>
                        <div class="dp-url">stockflow.app/dashboard</div>
                        <div class="dp-actions">
                            <div class="dp-search"><i data-lucide="search"></i><span>Search...</span></div>
                            <div class="dp-notif"><i data-lucide="bell"></i><span class="notif-dot"></span></div>
                            <div class="dp-profile">
                                <div class="dp-avatar"></div>
                                <div class="dp-profile-info">
                                    <div class="dp-name">Admin User</div>
                                    <div class="dp-role">Warehouse Manager</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="dp-body">
                        <div class="dp-sidebar">
                            <div class="dp-menu-item dp-active"><i
                                    data-lucide="layout-dashboard"></i><span>Dashboard</span></div>
                            <div class="dp-menu-item"><i data-lucide="package"></i><span>Inventory</span></div>
                            <div class="dp-menu-item"><i data-lucide="truck"></i><span>Purchase Order</span></div>
                            <div class="dp-menu-item"><i data-lucide="send"></i><span>Sales Order</span></div>
                            <div class="dp-menu-item"><i data-lucide="users"></i><span>Suppliers</span></div>
                            <div class="dp-menu-item"><i data-lucide="bar-chart-3"></i><span>Reports</span></div>
                            <div class="dp-menu-item"><i data-lucide="settings"></i><span>Settings</span></div>
                        </div>
                        <div class="dp-main">
                            {{-- Stat Cards (DINAMIS) --}}
                            <div class="dp-stat-cards">
                                @foreach ($dashboardStats as $stat)
                                <div class="dp-stat-card">
                                    <div class="dp-stat-header">
                                        <span>{{ $stat->label }}</span>
                                        <div class="dp-stat-icon {{ $stat->color }}"><i
                                                data-lucide="{{ $stat->icon }}"></i></div>
                                    </div>
                                    <div class="dp-stat-value">{{ $stat->value }}</div>
                                    <div class="dp-stat-trend {{ $stat->trend_direction }}">
                                        <i
                                            data-lucide="{{ $stat->trend_direction === 'up' ? 'trending-up' : 'trending-down' }}"></i>
                                        {{ $stat->trend_text }}
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div class="dp-middle">
                                {{-- Sales chart tetap statis (ilustrasi SVG) --}}
                                <div class="dp-chart-main">
                                    <div class="dp-chart-head">
                                        <h4>Sales Chart</h4>
                                        <div class="dp-chart-tabs">
                                            <button class="active">7H</button>
                                            <button>1B</button>
                                            <button>3B</button>
                                        </div>
                                    </div>
                                    <div class="dp-line-chart">
                                        <svg viewBox="0 0 400 100" preserveAspectRatio="none">
                                            <defs>
                                                <linearGradient id="lineGrad" x1="0" y1="0" x2="0" y2="1">
                                                    <stop offset="0%" stop-color="#3B82F6" stop-opacity="0.3" />
                                                    <stop offset="100%" stop-color="#3B82F6" stop-opacity="0" />
                                                </linearGradient>
                                            </defs>
                                            <path
                                                d="M0,80 C40,60 80,70 120,45 C160,20 200,50 240,35 C280,20 320,55 360,25 L400,20 L400,100 L0,100 Z"
                                                fill="url(#lineGrad)" />
                                            <path
                                                d="M0,80 C40,60 80,70 120,45 C160,20 200,50 240,35 C280,20 320,55 360,25 L400,20"
                                                fill="none" stroke="#3B82F6" stroke-width="2" />
                                            <circle cx="120" cy="45" r="3" fill="#3B82F6" />
                                            <circle cx="240" cy="35" r="3" fill="#3B82F6" />
                                            <circle cx="360" cy="25" r="3" fill="#3B82F6" />
                                        </svg>
                                    </div>
                                </div>

                                {{-- Recent Activity (DINAMIS) --}}
                                <div class="dp-activity">
                                    <h4>Recent Activity</h4>
                                    <div class="dp-act-list">
                                        @foreach ($dashboardActivities as $activity)
                                        <div class="dp-act-item">
                                            <div class="dp-act-icon {{ $activity->color }}"><i
                                                    data-lucide="{{ $activity->icon }}"></i></div>
                                            <div class="dp-act-info">
                                                <div class="dp-act-title">{{ $activity->title }}</div>
                                                <div class="dp-act-time">{{ $activity->time_text }}</div>
                                            </div>
                                            <div class="dp-act-val {{ $activity->value_color }}">
                                                {{ $activity->value_text }}</div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="dp-bottom">
                                {{-- Top Products (DINAMIS) --}}
                                <div class="dp-order-table">
                                    <h4>Top Products</h4>
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Produk</th>
                                                <th>SKU</th>
                                                <th>Stock</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($dashboardProducts as $product)
                                            <tr>
                                                <td>{{ $product->name }}</td>
                                                <td>{{ $product->sku }}</td>
                                                <td>{{ $product->stock }}</td>
                                                <td>
                                                    @if ($product->status === 'normal')
                                                    <span class="badge-status green">Normal</span>
                                                    @elseif ($product->status === 'low')
                                                    <span class="badge-status orange">Low</span>
                                                    @else
                                                    <span class="badge-status red">Critical</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Stock chart (donut) tetap statis (ilustrasi SVG) --}}
                                <div class="dp-pie-box">
                                    <h4>Stock Chart</h4>
                                    <div class="dp-donut">
                                        <svg viewBox="0 0 100 100">
                                            <circle cx="50" cy="50" r="38" fill="none" stroke="#1E293B"
                                                stroke-width="14" />
                                            <circle cx="50" cy="50" r="38" fill="none" stroke="#3B82F6"
                                                stroke-width="14" stroke-dasharray="100 139.6" stroke-dashoffset="35"
                                                transform="rotate(-90 50 50)" />
                                            <circle cx="50" cy="50" r="38" fill="none" stroke="#06B6D4"
                                                stroke-width="14" stroke-dasharray="60 179.6" stroke-dashoffset="-65"
                                                transform="rotate(-90 50 50)" />
                                            <circle cx="50" cy="50" r="38" fill="none" stroke="#8B5CF6"
                                                stroke-width="14" stroke-dasharray="39.6 200" stroke-dashoffset="-125"
                                                transform="rotate(-90 50 50)" />
                                            <text x="50" y="47" text-anchor="middle" fill="white" font-size="10"
                                                font-weight="700">12.8K</text>
                                            <text x="50" y="58" text-anchor="middle" fill="#94a3b8" font-size="6">Total
                                                SKU</text>
                                        </svg>
                                        <div class="donut-legend">
                                            <div class="donut-leg-item"><span
                                                    class="leg-dot blue"></span><span>Elektronik 40%</span></div>
                                            <div class="donut-leg-item"><span
                                                    class="leg-dot cyan"></span><span>Peralatan 25%</span></div>
                                            <div class="donut-leg-item"><span
                                                    class="leg-dot purple"></span><span>Lainnya 35%</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Workflow Section -->
    <section class="workflow-section section-pad">
        <div class="container">
            <div class="section-header fade-up">
                <div class="section-badge">{{ $workflowHeader->badge }}</div>
                <h2 class="section-title">{{ $workflowHeader->title_normal }}<br /><span
                        class="gradient-text">{{ $workflowHeader->title_gradient }}</span></h2>
                <p class="section-sub">{{ $workflowHeader->subtitle }}</p>
            </div>
            <div class="workflow-timeline fade-up">
                @foreach ($workflowSteps as $step)
                <div class="workflow-step {{ $loop->last ? 'last' : '' }}">
                    <div class="wf-card">
                        <div class="wf-icon {{ $step->color }}"><i data-lucide="{{ $step->icon }}"></i></div>
                        <div class="wf-num">{{ str_pad($step->step_number, 2, '0', STR_PAD_LEFT) }}</div>
                        <h4>{{ $step->title }}</h4>
                        <p>{{ $step->description }}</p>
                    </div>
                    @if (!$loop->last)
                    <div class="wf-arrow"><i data-lucide="arrow-right"></i></div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="benefits-section section-pad" id="benefits">
        <div class="container">
            <div class="section-header fade-up">
                <div class="section-badge">{{ $benefitsHeader->badge }}</div>
                <h2 class="section-title">{{ $benefitsHeader->title_normal }}<br /><span
                        class="gradient-text">{{ $benefitsHeader->title_gradient }}</span></h2>
                <p class="section-sub">{{ $benefitsHeader->subtitle }}</p>
            </div>
            <div class="benefits-grid fade-up">
                @foreach ($benefits as $benefit)
                <div class="benefit-card {{ $benefit->is_featured ? 'featured' : '' }}">
                    @if ($benefit->is_static)
                    <div class="benefit-num gradient-text stat-static">{{ $benefit->static_value }}</div>
                    @else
                    <div class="benefit-num gradient-text" data-target="{{ $benefit->target }}"
                        data-suffix="{{ $benefit->suffix }}" data-decimal="{{ $benefit->decimal ?? 0 }}">0%</div>
                    @endif
                    <h3>{{ $benefit->title }}</h3>
                    <p>{{ $benefit->description }}</p>
                    <div class="benefit-progress">
                        <div class="benefit-bar" style="--target-width: {{ $benefit->bar_width }}%"></div>
                    </div>
                    <div class="benefit-icon"><i data-lucide="{{ $benefit->icon }}"></i></div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonial-section section-pad">
        <div class="container">
            <div class="section-header fade-up">
                <div class="section-badge">{{ $testimonialHeader->badge }}</div>
                <h2 class="section-title">{{ $testimonialHeader->title_normal }}<br /><span
                        class="gradient-text">{{ $testimonialHeader->title_gradient }}</span></h2>
                <p class="section-sub">{{ $testimonialHeader->subtitle }}</p>
            </div>
            <div class="testimonial-grid fade-up">
                @foreach ($testimonials as $testi)
                <div class="testimonial-card {{ $testi->is_featured ? 'featured-card' : '' }}">
                    <div class="testi-stars">
                        @for ($i = 0; $i < $testi->rating; $i++)
                            <i data-lucide="star"></i>
                            @endfor
                    </div>
                    <p class="testi-quote">"{{ $testi->quote }}"</p>
                    <div class="testi-author">
                        <div class="testi-avatar av-{{ $testi->avatar_color }}">{{ $testi->initials }}</div>
                        <div class="testi-info">
                            <div class="testi-name">{{ $testi->name }}</div>
                            <div class="testi-role">{{ $testi->role }} · {{ $testi->company }}</div>
                        </div>
                        @if ($testi->is_verified)
                        <div class="testi-verified"><i data-lucide="badge-check"></i></div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section section-pad" id="faq">
        <div class="container">
            <div class="section-header fade-up">
                <div class="section-badge">{{ $faqHeader->badge }}</div>
                <h2 class="section-title">{{ $faqHeader->title_normal }}<br /><span
                        class="gradient-text">{{ $faqHeader->title_gradient }}</span></h2>
                <p class="section-sub">{{ $faqHeader->subtitle }}</p>
            </div>
            <div class="faq-grid fade-up">
                @foreach ($faqs as $faq)
                <div class="faq-item">
                    <button class="faq-question">
                        <span>{{ $faq->question }}</span>
                        <div class="faq-icon"><i data-lucide="plus"></i></div>
                    </button>
                    <div class="faq-answer">
                        <p>{{ $faq->answer }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>



    <!-- CTA Section -->
    <section class="cta-section section-pad" id="contact">
        <div class="container">
            <div class="cta-box fade-up">
                <div class="cta-glow"></div>
                <div class="cta-content">
                    <div class="section-badge">{{ $contactHeader->badge }}</div>
                    <h2 class="cta-title">{{ $contactHeader->title_normal }}<br /><span
                            class="gradient-text">{{ $contactHeader->title_gradient }}</span></h2>
                    <p class="cta-sub">{{ $contactHeader->subtitle }}</p>
                    <div class="cta-features">
                        @foreach ($ctaFeatures as $feature)
                        <div class="cta-feat"><i data-lucide="check-circle-2"></i><span>{{ $feature->text }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="cta-actions">
                        <a href="{{ $contactHeader->cta_primary_url ?? '#' }}" class="btn-primary btn-xl ripple">
                            <i data-lucide="rocket"></i> Start Free Trial
                        </a>
                        <a href="{{ $contactHeader->cta_secondary_url ?? '#' }}" class="btn-ghost-white btn-xl ripple">
                            <i data-lucide="phone"></i> Contact Sales
                        </a>
                    </div>
                </div>
                <div class="cta-decoration"></div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" id="contact">
        <div class="container">
            <div class="footer-top">
                <div class="footer-brand">
                    <a class="nav-logo" href="#home">
                        <div class="logo-icon"><i data-lucide="box"></i></div>
                        <span>StockFlow</span>
                    </a>
                    <p>Platform WMS modern untuk bisnis yang ingin tumbuh lebih cepat dengan manajemen gudang yang
                        cerdas dan efisien.</p>
                    <div class="social-links">
                        <a href="#" class="social-link" aria-label="Twitter">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z" />
                            </svg>
                        </a>
                        <a href="#" class="social-link" aria-label="LinkedIn">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z" />
                                <rect x="2" y="9" width="4" height="12" />
                                <circle cx="4" cy="4" r="2" />
                            </svg>
                        </a>
                        <a href="#" class="social-link" aria-label="Instagram">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" />
                            </svg>
                        </a>
                        <a href="#" class="social-link" aria-label="YouTube">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 0 0-1.95 1.96A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.57 2.78 2.78 0 0 0 1.95 1.97C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.97A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z" />
                                <polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" />
                            </svg>
                        </a>
                        <a href="#" class="social-link" aria-label="GitHub">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22" />
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="footer-links">
                    <div class="footer-col">
                        <h5>Product</h5>
                        <ul>
                            <li><a href="#">Inventory Management</a></li>
                            <li><a href="#">Warehouse Management</a></li>
                            <li><a href="#">Purchase Order</a></li>
                            <li><a href="#">Sales Order</a></li>
                            <li><a href="#">Analytics & Reports</a></li>
                            <li><a href="#">Mobile App</a></li>
                        </ul>
                    </div>
                    <div class="footer-col">
                        <h5>Resources</h5>
                        <ul>
                            <li><a href="#">Documentation</a></li>
                            <li><a href="#">API Reference</a></li>
                            <li><a href="#">Blog & Insights</a></li>
                            <li><a href="#">Video Tutorials</a></li>
                            <li><a href="#">Case Studies</a></li>
                            <li><a href="#">Community Forum</a></li>
                        </ul>
                    </div>
                    <div class="footer-col">
                        <h5>Company</h5>
                        <ul>
                            <li><a href="#">About Us</a></li>
                            <li><a href="#">Our Team</a></li>
                            <li><a href="#">Careers</a></li>
                            <li><a href="#">Press Kit</a></li>
                            <li><a href="#">Partners</a></li>
                            <li><a href="#">Contact Us</a></li>
                        </ul>
                    </div>
                    <div class="footer-col">
                        <h5>Legal</h5>
                        <ul>
                            <li><a href="#">Privacy Policy</a></li>
                            <li><a href="#">Terms of Service</a></li>
                            <li><a href="#">Cookie Policy</a></li>
                            <li><a href="#">GDPR Compliance</a></li>
                            <li><a href="#">Security</a></li>
                            <li><a href="#">SLA</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 StockFlow Technologies, Inc. All rights reserved.</p>
                <div class="footer-badges">
                    <div class="footer-badge"><i data-lucide="shield-check"></i> SOC 2 Type II</div>
                    <div class="footer-badge"><i data-lucide="lock"></i> ISO 27001</div>
                    <div class="footer-badge"><i data-lucide="server"></i> 99.9% Uptime</div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop" aria-label="Back to top">
        <i data-lucide="arrow-up"></i>
    </button>

    <script src="frontend/js/script.js"></script>
</body>

</html>
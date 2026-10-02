<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#ec4899">
    <title>TOKO DIAN NAME | Kasir & Manajemen Toko</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            font-synthesis: none;
            text-rendering: optimizeLegibility;
            --ink: #4a1234;
            --muted: #84385e;
            --panel: rgba(255, 240, 247, .9);
            --line: rgba(190, 24, 93, .18);
            --cyan: #ec4899;
            --pink: #be185d;
            --yellow: #f472b6;
            --violet: #db2777;
        }

        * { box-sizing: border-box; }

        body {
            min-width: 320px;
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
            color: var(--ink);
            background:
                radial-gradient(ellipse at 13% 15%, rgba(236, 72, 153, .24), transparent 31rem),
                radial-gradient(ellipse at 90% 78%, rgba(219, 39, 119, .2), transparent 29rem),
                #fce7f3;
        }

        a { color: inherit; text-decoration: none; }
        button, a { -webkit-tap-highlight-color: transparent; }

        .site-shell { width: min(1160px, calc(100% - 48px)); margin: 0 auto; }

        .navbar {
            min-height: 92px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-size: 17px;
            font-weight: 850;
            letter-spacing: .08em;
        }

        .brand-mark {
            display: grid;
            width: 42px;
            height: 42px;
            place-items: center;
            border: 1px solid rgba(255, 255, 255, .3);
            border-radius: 14px;
            color: #fff;
            background: linear-gradient(145deg, #f472b6, #db2777);
            box-shadow: 0 8px 24px rgba(255, 105, 174, .25), inset 0 2px 2px rgba(255, 255, 255, .8);
            transform: rotate(-7deg);
        }

        .brand-mark svg { width: 23px; height: 23px; }
        .brand-name span { color: var(--cyan); }

        .nav-actions { display: flex; align-items: center; gap: 12px; }
        .button {
            min-height: 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            padding: 0 20px;
            border: 1px solid var(--line);
            border-radius: 14px;
            font-size: 14px;
            font-weight: 750;
            transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
        }

        .button:hover { transform: translateY(-3px); }
        .button:focus-visible { outline: 3px solid var(--yellow); outline-offset: 3px; }
        .button-quiet { background: rgba(255, 255, 255, .06); }
        .button-quiet:hover { background: rgba(255, 255, 255, .12); }
        .button-bright {
            border-color: transparent;
            color: #11152d;
            color: #fff;
            background: linear-gradient(115deg, #ec4899, #be185d);
            box-shadow: 0 9px 24px rgba(255, 105, 174, .2), inset 0 2px 1px rgba(255, 255, 255, .8);
        }

        .hero {
            min-height: calc(100vh - 92px);
            display: grid;
            grid-template-columns: 1fr 1.05fr;
            align-items: center;
            gap: clamp(32px, 6vw, 78px);
            padding: 40px 0 78px;
        }

        .hero-copy { position: relative; z-index: 2; }
        .eyebrow {
            width: fit-content;
            display: flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 22px;
            padding: 9px 14px;
            border: 1px solid rgba(255, 143, 199, .25);
            border-radius: 999px;
            color: #9d174d;
            background: rgba(255, 255, 255, .7);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .09em;
            text-transform: uppercase;
        }

        .eyebrow-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--cyan);
            box-shadow: 0 0 14px var(--cyan);
        }

        h1 {
            max-width: 610px;
            margin: 0;
            font-size: clamp(46px, 6.3vw, 76px);
            font-weight: 900;
            letter-spacing: -.065em;
            line-height: .99;
        }

        .title-highlight {
            display: block;
            color: transparent;
            background: linear-gradient(95deg, #be185d 5%, #db2777 52%, #9d174d 100%);
            background-clip: text;
            -webkit-background-clip: text;
        }

        .hero-description {
            max-width: 500px;
            margin: 24px 0 30px;
            color: var(--muted);
            font-size: 17px;
            line-height: 1.75;
        }

        .hero-actions { display: flex; flex-wrap: wrap; gap: 13px; }
        .hero-actions .button { min-height: 54px; padding: 0 23px; }
        .button-arrow { font-size: 19px; transition: transform .2s ease; }
        .button:hover .button-arrow { transform: translateX(4px); }

        .trust-row {
            display: flex;
            flex-wrap: wrap;
            gap: 18px 24px;
            margin-top: 30px;
            color: #70234a;
            font-size: 12px;
            font-weight: 650;
        }

        .trust-row span { display: inline-flex; align-items: center; gap: 7px; }
        .trust-check { color: var(--yellow); font-size: 15px; }

        .scene {
            position: relative;
            min-height: 510px;
            display: grid;
            place-items: center;
            perspective: 1100px;
            isolation: isolate;
        }

        .scene-glow {
            position: absolute;
            z-index: -1;
            width: 76%;
            aspect-ratio: 1;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(255, 143, 199, .27), rgba(214, 126, 207, .28) 52%, rgba(255, 92, 171, .25));
            filter: blur(50px);
        }

        .dashboard {
            position: relative;
            width: min(100%, 490px);
            padding: 20px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, .22);
            border-radius: 25px;
            background: linear-gradient(145deg, rgba(83, 28, 66, .95), rgba(42, 16, 34, .96));
            box-shadow: 0 38px 70px rgba(0, 0, 0, .42), 0 14px 30px rgba(90, 84, 220, .22), inset 0 1px 1px rgba(255, 255, 255, .28);
            transform: rotateY(-13deg) rotateX(7deg) rotateZ(-2deg);
            transform-style: preserve-3d;
            animation: dashboard-float 6s ease-in-out infinite;
        }

        .dashboard::after {
            position: absolute;
            inset: 0;
            z-index: -1;
            border-radius: inherit;
            background: linear-gradient(120deg, rgba(255, 143, 199, .13), transparent 38%, rgba(255, 109, 179, .12));
            content: "";
            pointer-events: none;
        }

        .dashboard-top, .dashboard-title, .sales-row, .chart-heading, .product-grid, .product-card, .sale-info {
            display: flex;
            align-items: center;
        }

        .dashboard-top { justify-content: space-between; margin-bottom: 22px; }
        .window-dots { display: flex; gap: 6px; }
        .window-dots i { width: 8px; height: 8px; border-radius: 50%; background: #ff728d; }
        .window-dots i:nth-child(2) { background: var(--yellow); }
        .window-dots i:nth-child(3) { background: var(--cyan); }
        .dashboard-label { color: #b7bfdc; font-size: 10px; font-weight: 800; letter-spacing: .12em; }
        .dashboard-title { justify-content: space-between; margin-bottom: 17px; }
        .dashboard-title h2 { margin: 0; font-size: 18px; letter-spacing: -.03em; }
        .online-badge { padding: 6px 9px; border-radius: 999px; color: #ffd9eb; background: rgba(236, 72, 153, .16); font-size: 9px; font-weight: 800; }

        .sales-row { gap: 12px; margin-bottom: 14px; }
        .sales-card {
            flex: 1;
            min-width: 0;
            padding: 14px;
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 16px;
            background: rgba(255, 255, 255, .055);
            transform: translateZ(14px);
        }

        .sales-card:first-child { background: linear-gradient(145deg, rgba(83, 241, 226, .17), rgba(83, 241, 226, .045)); }
        .sales-card small { display: block; margin-bottom: 7px; color: #b9c1dc; font-size: 9px; }
        .sales-card strong { display: block; overflow: hidden; color: #fff; font-size: clamp(15px, 2vw, 21px); letter-spacing: -.04em; text-overflow: ellipsis; }
        .sales-card em { display: block; margin-top: 6px; color: var(--cyan); font-size: 9px; font-style: normal; font-weight: 750; }

        .chart-card {
            padding: 14px;
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 17px;
            background: rgba(12, 16, 39, .55);
            transform: translateZ(24px);
        }

        .chart-heading { justify-content: space-between; margin-bottom: 8px; }
        .chart-heading strong { font-size: 11px; }
        .chart-heading span { color: #a9b0ce; font-size: 9px; }
        .chart { width: 100%; height: 98px; overflow: visible; }
        .chart-grid line { stroke: rgba(255, 255, 255, .08); stroke-dasharray: 3 5; }
        .chart-line { fill: none; stroke: url(#chartGradient); stroke-linecap: round; stroke-linejoin: round; stroke-width: 4; }
        .chart-area { fill: url(#areaGradient); }
        .chart-point { fill: var(--cyan); stroke: #fff; stroke-width: 2; }

        .product-grid { gap: 9px; margin-top: 13px; }
        .product-card {
            flex: 1;
            gap: 8px;
            min-width: 0;
            padding: 9px;
            border: 1px solid rgba(255, 255, 255, .09);
            border-radius: 13px;
            background: rgba(255, 255, 255, .05);
            transform: translateZ(18px);
        }

        .product-icon { width: 29px; height: 29px; display: grid; flex: 0 0 auto; place-items: center; border-radius: 9px; font-size: 15px; }
        .product-icon.coral { background: rgba(255, 109, 179, .2); }
        .product-icon.gold { background: rgba(255, 183, 216, .18); }
        .product-icon.blue { background: rgba(236, 72, 153, .19); }
        .product-copy { min-width: 0; }
        .product-copy strong, .product-copy small { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .product-copy strong { font-size: 9px; }
        .product-copy small { margin-top: 3px; color: #aab2ce; font-size: 8px; }

        .floating-card {
            position: absolute;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 15px;
            border: 1px solid rgba(255, 255, 255, .24);
            border-radius: 16px;
            background: rgba(35, 41, 78, .92);
            box-shadow: 0 18px 32px rgba(0, 0, 0, .32), inset 0 1px rgba(255, 255, 255, .2);
            backdrop-filter: blur(12px);
            animation: float 5s ease-in-out infinite;
        }

        .floating-card p { margin: 0; }
        .floating-card small { display: block; margin-bottom: 3px; color: #b7bfdc; font-size: 9px; }
        .floating-card strong { font-size: 12px; }
        .float-icon { width: 33px; height: 33px; display: grid; place-items: center; border-radius: 11px; color: #202242; background: var(--yellow); font-size: 17px; }
        .float-sale { top: 62px; right: -17px; }
        .float-stock { bottom: 59px; left: -18px; animation-delay: -2.5s; }
        .float-stock .float-icon { color: #541034; background: var(--cyan); }

        .footer-note {
            padding: 0 0 25px;
            color: rgba(112, 35, 74, .65);
            font-size: 11px;
            text-align: center;
        }

        .dashboard {
            color: #fff;
            background: linear-gradient(145deg, #f472b6, #db2777 58%, #be185d);
            box-shadow: 0 38px 70px rgba(190, 24, 93, .28), 0 14px 30px rgba(236, 72, 153, .2), inset 0 1px 1px rgba(255, 255, 255, .45);
        }

        .dashboard::after {
            background: linear-gradient(120deg, rgba(255, 255, 255, .22), transparent 38%, rgba(255, 214, 235, .2));
        }

        .dashboard-label,
        .sales-card small,
        .chart-heading span,
        .product-copy small {
            color: #ffe4f1;
        }

        .online-badge {
            color: #9d174d;
            background: #fff0f7;
        }

        .sales-card,
        .sales-card:first-child,
        .product-card {
            background: rgba(112, 23, 68, .24);
        }

        .chart-card {
            background: rgba(74, 18, 52, .32);
        }

        .chart-line {
            stroke: #ffd6ea;
        }

        .chart-grid line {
            stroke: rgba(255, 255, 255, .2);
        }

        .floating-card {
            color: #4a1234;
            background: rgba(255, 250, 253, .96);
            box-shadow: 0 18px 32px rgba(112, 23, 68, .2), inset 0 1px rgba(255, 255, 255, .8);
        }

        .floating-card small {
            color: #84385e;
        }

        .float-icon {
            color: #fff;
            background: var(--pink);
        }

        .float-stock .float-icon {
            color: #fff;
            background: var(--pink);
        }

        @keyframes dashboard-float {
            0%, 100% { transform: rotateY(-13deg) rotateX(7deg) rotateZ(-2deg) translateY(0); }
            50% { transform: rotateY(-10deg) rotateX(5deg) rotateZ(-1deg) translateY(-9px); }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(1deg); }
            50% { transform: translateY(-10px) rotate(-1deg); }
        }

        @media (max-width: 900px) {
            .hero { grid-template-columns: 1fr; gap: 30px; padding-top: 58px; }
            .hero-copy { max-width: 670px; margin: 0 auto; text-align: center; }
            .eyebrow { margin-right: auto; margin-left: auto; }
            .hero-description { margin-right: auto; margin-left: auto; }
            .hero-actions, .trust-row { justify-content: center; }
            .scene { width: min(100%, 590px); min-height: 470px; margin: 0 auto; }
            .footer-note { padding-top: 6px; }
        }

        @media (max-width: 560px) {
            .site-shell { width: min(100% - 32px, 480px); }
            .navbar { min-height: 76px; gap: 10px; }
            .brand { gap: 8px; font-size: 13px; }
            .brand-mark { width: 36px; height: 36px; border-radius: 12px; }
            .nav-actions { gap: 7px; }
            .button { min-height: 41px; padding: 0 13px; border-radius: 12px; font-size: 12px; }
            .hero { min-height: auto; padding: 48px 0 35px; }
            h1 { font-size: clamp(43px, 13vw, 60px); }
            .hero-description { margin-top: 18px; font-size: 15px; }
            .hero-actions .button { min-height: 49px; padding: 0 17px; }
            .trust-row { gap: 10px 15px; font-size: 10px; }
            .scene { min-height: 380px; margin-top: 1px; }
            .dashboard { padding: 15px; border-radius: 21px; transform: rotateY(-7deg) rotateX(4deg) rotateZ(-1deg); }
            .dashboard-title h2 { font-size: 16px; }
            .sales-row { gap: 8px; }
            .sales-card { padding: 11px; }
            .chart-card { padding: 11px; }
            .chart { height: 82px; }
            .product-grid { gap: 6px; }
            .product-card { gap: 6px; padding: 7px 6px; }
            .product-icon { width: 25px; height: 25px; }
            .product-copy strong { font-size: 8px; }
            .product-copy small { font-size: 7px; }
            .float-sale { top: 27px; right: -3px; }
            .float-stock { bottom: 30px; left: -4px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; animation-duration: .01ms !important; animation-iteration-count: 1 !important; transition-duration: .01ms !important; }
        }
    </style>
</head>
<body>
    <div class="site-shell">
        <nav class="navbar" aria-label="Navigasi utama">
            <a href="{{ url('/') }}" class="brand" aria-label="TOKO DIAN NAME - Beranda">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M4 9.5h16l-1.4 10H5.4L4 9.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        <path d="m3.5 9.5 2-5h13l2 5M9 9.5v10m6-10v10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="brand-name">TOKO <span>DIAN</span></span>
            </a>
            <div class="nav-actions">
                <a href="{{ route('login') }}" class="button button-quiet">Masuk</a>
                <a href="{{ route('register') }}" class="button button-bright">Daftar</a>
            </div>
        </nav>

        <main class="hero">
            <section class="hero-copy">
                <div class="eyebrow"><span class="eyebrow-dot"></span> Sistem toko dalam satu genggaman</div>
                <h1>Jualan lebih mudah, <span class="title-highlight">toko makin maju.</span></h1>
                <p class="hero-description">
                    Kelola produk, pantau stok, dan layani transaksi dengan cepat. Semua kebutuhan toko harianmu tersusun rapi dalam satu sistem.
                </p>
                <div class="hero-actions">
                    <a href="{{ route('login') }}" class="button button-bright">Mulai sekarang <span class="button-arrow" aria-hidden="true">→</span></a>
                    <a href="{{ route('register') }}" class="button button-quiet">Buat akun toko</a>
                </div>
                <div class="trust-row" aria-label="Fitur utama">
                    <span><b class="trust-check" aria-hidden="true">✦</b> Kasir praktis</span>
                    <span><b class="trust-check" aria-hidden="true">✦</b> Stok terpantau</span>
                    <span><b class="trust-check" aria-hidden="true">✦</b> Data lebih rapi</span>
                </div>
            </section>

            <section class="scene" aria-label="Pratinjau dashboard toko">
                <div class="scene-glow" aria-hidden="true"></div>
                <div class="dashboard">
                    <div class="dashboard-top">
                        <div class="window-dots" aria-hidden="true"><i></i><i></i><i></i></div>
                        <span class="dashboard-label">TOKO DIAN · PREVIEW</span>
                    </div>
                    <div class="dashboard-title">
                        <h2>Ringkasan penjualan</h2>
                        <span class="online-badge">● TOKO AKTIF</span>
                    </div>
                    <div class="sales-row">
                        <div class="sales-card">
                            <small>Total penjualan hari ini</small>
                            <strong>Rp 2.450.000</strong>
                            <em>↗ Toko makin ramai</em>
                        </div>
                        <div class="sales-card">
                            <small>Transaksi selesai</small>
                            <strong>38 transaksi</strong>
                            <em>Hari ini · berjalan</em>
                        </div>
                    </div>
                    <div class="chart-card">
                        <div class="chart-heading"><strong>Aktivitas penjualan</strong><span>Senin – Minggu</span></div>
                        <svg class="chart" viewBox="0 0 420 110" role="img" aria-label="Grafik penjualan meningkat sepanjang minggu">
                            <defs>
                                <linearGradient id="chartGradient" x1="0" x2="1">
                                    <stop offset="0" stop-color="#ff8fc7"/>
                                    <stop offset="1" stop-color="#ff5ca8"/>
                                </linearGradient>
                                <linearGradient id="areaGradient" x1="0" x2="0" y1="0" y2="1">
                                    <stop offset="0" stop-color="#ff8fc7" stop-opacity=".25"/>
                                    <stop offset="1" stop-color="#ff8fc7" stop-opacity="0"/>
                                </linearGradient>
                            </defs>
                            <g class="chart-grid">
                                <line x1="0" y1="20" x2="420" y2="20"/><line x1="0" y1="52" x2="420" y2="52"/><line x1="0" y1="84" x2="420" y2="84"/>
                            </g>
                            <path class="chart-area" d="M8 82 C45 76 51 58 91 66 S145 73 174 48 S224 61 257 39 S305 52 337 28 S384 39 412 12 L412 100 L8 100Z"/>
                            <path class="chart-line" d="M8 82 C45 76 51 58 91 66 S145 73 174 48 S224 61 257 39 S305 52 337 28 S384 39 412 12"/>
                            <circle class="chart-point" cx="412" cy="12" r="5"/>
                        </svg>
                    </div>
                    <div class="product-grid" aria-label="Produk terlaris">
                        <div class="product-card"><span class="product-icon coral" aria-hidden="true">🍓</span><span class="product-copy"><strong>Buah segar</strong><small>Terlaris hari ini</small></span></div>
                        <div class="product-card"><span class="product-icon gold" aria-hidden="true">🥐</span><span class="product-copy"><strong>Roti pilihan</strong><small>Stok tersedia</small></span></div>
                        <div class="product-card"><span class="product-icon blue" aria-hidden="true">🥤</span><span class="product-copy"><strong>Minuman</strong><small>Stok tersedia</small></span></div>
                    </div>
                </div>
                <div class="floating-card float-sale">
                    <span class="float-icon" aria-hidden="true">↗</span>
                    <p><small>Penjualan hari ini</small><strong>Terus bertumbuh!</strong></p>
                </div>
                <div class="floating-card float-stock">
                    <span class="float-icon" aria-hidden="true">✓</span>
                    <p><small>Produk & stok</small><strong>Selalu terpantau</strong></p>
                </div>
            </section>
        </main>

        <footer class="footer-note">TOKO DIAN NAME · Kelola toko dengan lebih percaya diri.</footer>
    </div>
</body>
</html>

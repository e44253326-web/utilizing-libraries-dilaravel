<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin | TOKO DIAN NAME')</title>
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet">
    <style>
        :root { --admin-ink: #182230; --admin-muted: #667085; --admin-border: #e5eaf1; --admin-primary: #1769e0; --admin-bg: #f4f7fb; }
        body { min-height: 100vh; color: var(--admin-ink); background: var(--admin-bg); font-family: Inter, "Segoe UI", Arial, sans-serif; }
        .admin-sidebar { position: fixed; inset: 0 auto 0 0; z-index: 1030; display: flex; width: 252px; flex-direction: column; padding: 1.35rem 1rem; background: #12233e; }
        .admin-brand { display: flex; align-items: center; gap: .75rem; padding: .2rem .45rem 1.65rem; color: #fff; font-size: .92rem; font-weight: 800; letter-spacing: .025em; }
        .admin-brand-icon { display: grid; width: 40px; height: 40px; place-items: center; color: white; background: linear-gradient(145deg, #3287fa, #1755bb); border-radius: 12px; font-size: 1.1rem; }
        .admin-nav-label { margin: .55rem .65rem .55rem; color: #8292aa; font-size: .67rem; font-weight: 750; letter-spacing: .1em; }
        .admin-nav { display: grid; gap: .32rem; }
        .admin-nav-link { display: flex; align-items: center; gap: .7rem; padding: .72rem .75rem; color: #bdc9da; border-radius: 8px; font-size: .88rem; font-weight: 600; text-decoration: none; transition: .15s ease; }
        .admin-nav-link i { width: 1.2rem; font-size: 1rem; }
        .admin-nav-link:hover, .admin-nav-link.active { color: #fff; background: #244b7f; }
        .admin-sidebar-bottom { margin-top: auto; padding-top: 1rem; border-top: 1px solid rgb(255 255 255 / 12%); }
        .admin-user { display: flex; align-items: center; gap: .65rem; margin-bottom: .8rem; padding: .4rem; color: white; }
        .admin-avatar { display: grid; width: 34px; height: 34px; place-items: center; color: white; background: #416487; border-radius: 50%; font-weight: 700; }
        .admin-user-name { overflow: hidden; font-size: .82rem; font-weight: 700; text-overflow: ellipsis; white-space: nowrap; }
        .admin-user-role { color: #a9b7ca; font-size: .7rem; }
        .admin-logout { width: 100%; color: #ffc4c4; text-align: left; }
        .admin-logout:hover { color: #fff; background: rgb(255 255 255 / 10%); }
        .admin-main { min-height: 100vh; margin-left: 252px; }
        .admin-topbar { display: flex; min-height: 70px; align-items: center; justify-content: space-between; gap: 1rem; padding: .8rem 1.6rem; background: #fff; border-bottom: 1px solid var(--admin-border); }
        .admin-topbar h1 { margin: 0; font-size: 1.05rem; font-weight: 750; }
        .admin-topbar-date { color: var(--admin-muted); font-size: .8rem; }
        .admin-content { padding: 1.55rem; }
        .admin-welcome { display: flex; flex-wrap: wrap; align-items: end; justify-content: space-between; gap: .8rem; }
        .admin-eyebrow { margin-bottom: .45rem; color: var(--admin-primary); font-size: .68rem; font-weight: 800; letter-spacing: .1em; }
        .admin-date { color: var(--admin-muted); font-size: .82rem; }
        .admin-stat-card, .admin-panel { height: 100%; background: #fff; border: 1px solid var(--admin-border); border-radius: 12px; box-shadow: 0 4px 14px rgb(16 24 40 / 3%); }
        .admin-stat-card { padding: 1.1rem; }
        .admin-stat-icon { display: grid; width: 38px; height: 38px; place-items: center; margin-bottom: .9rem; border-radius: 10px; font-size: 1.05rem; }
        .admin-stat-icon.blue { color: #175cd3; background: #eff6ff; }
        .admin-stat-icon.green { color: #027a48; background: #ecfdf3; }
        .admin-stat-icon.purple { color: #6941c6; background: #f4f3ff; }
        .admin-stat-icon.orange { color: #b54708; background: #fff7ed; }
        .admin-stat-label { color: var(--admin-muted); font-size: .78rem; font-weight: 650; }
        .admin-stat-value { overflow: hidden; margin: .18rem 0; font-size: 1.7rem; font-weight: 800; letter-spacing: -.03em; text-overflow: ellipsis; white-space: nowrap; }
        .admin-stat-value.admin-currency { font-size: clamp(1.15rem, 1.5vw, 1.55rem); }
        .admin-stat-note { color: #98a2b3; font-size: .73rem; }
        .admin-panel { overflow: hidden; }
        .admin-panel-header { display: flex; align-items: center; justify-content: space-between; gap: .75rem; padding: 1rem 1.1rem; border-bottom: 1px solid #edf0f4; }
        .admin-panel-header h3 { font-weight: 750; }
        .admin-table { min-width: 650px; }
        .admin-table thead th { padding: .7rem .85rem; color: var(--admin-muted); background: #f9fafb; border-bottom-color: #edf0f4; font-size: .69rem; font-weight: 750; text-transform: uppercase; white-space: nowrap; }
        .admin-table tbody td { padding: .78rem .85rem; border-color: #f0f2f5; font-size: .79rem; }
        .admin-payment-badge { padding: .25rem .48rem; color: #175cd3; background: #eff6ff; border-radius: 999px; font-size: .7rem; font-weight: 700; }
        .admin-empty { padding: 2.2rem 1rem !important; color: #98a2b3; text-align: center; font-size: .83rem; }
        .admin-count-badge { display: grid; min-width: 28px; height: 28px; place-items: center; padding: 0 .4rem; color: #b54708; background: #fff7ed; border-radius: 50px; font-size: .75rem; font-weight: 750; }
        .low-stock-list { padding: .35rem 1rem; }
        .low-stock-item { display: flex; align-items: center; justify-content: space-between; gap: .75rem; padding: .78rem 0; border-bottom: 1px solid #f0f2f5; }
        .low-stock-item:last-child { border-bottom: 0; }
        .low-stock-product { display: grid; min-width: 0; }
        .low-stock-name { overflow: hidden; font-size: .82rem; font-weight: 700; text-overflow: ellipsis; white-space: nowrap; }
        .low-stock-product small { color: var(--admin-muted); font-size: .72rem; }
        .stock-badge { flex: 0 0 auto; padding: .28rem .48rem; border-radius: 999px; font-size: .68rem; font-weight: 750; }
        .stock-badge.low { color: #b54708; background: #fff7ed; }
        .stock-badge.empty { color: #b42318; background: #fef3f2; }
        .held-notice { display: flex; justify-content: space-between; gap: .5rem; margin: .55rem .95rem .85rem; padding: .7rem .75rem; color: #6941c6; background: #f4f3ff; border-radius: 8px; font-size: .78rem; font-weight: 700; text-decoration: none; }
        .held-notice:hover { background: #e9e7ff; }
        .admin-mobile-toggle { display: none; }
        @media (max-width: 767.98px) {
            .admin-sidebar { position: static; width: 100%; padding: .7rem 1rem; }
            .admin-brand { padding: .1rem .25rem; }
            .admin-nav-label { display: none; }
            .admin-nav { display: flex; overflow-x: auto; }
            .admin-nav-link { flex: 0 0 auto; }
            .admin-sidebar-bottom { display: block; margin-top: .55rem; padding-top: .35rem; }
            .admin-user { display: none; }
            .admin-logout { width: auto; padding: .45rem .55rem; }
            .admin-main { margin-left: 0; }
            .admin-topbar { min-height: 58px; padding: .65rem 1rem; }
            .admin-topbar-date { display: none; }
            .admin-content { padding: 1rem; }
        }
    </style>
    <link href="{{ asset('assets/css/pink-theme.css') }}" rel="stylesheet">
</head>
<body>
    <aside class="admin-sidebar">
        <a class="admin-brand text-decoration-none" href="{{ route('admin.index') }}">
            <span class="admin-brand-icon"><i class="bi bi-shop"></i></span>
            <span>TOKO DIAN NAME</span>
        </a>
        <div class="admin-nav-label">MENU UTAMA</div>
        <nav class="admin-nav" aria-label="Navigasi admin">
            <a href="{{ route('admin.index') }}" class="admin-nav-link{{ request()->routeIs('admin.index') ? ' active' : '' }}"><i class="bi bi-grid-1x2"></i> Dashboard</a>
            <a href="{{ route('products.index') }}" class="admin-nav-link{{ request()->routeIs('products.*') ? ' active' : '' }}"><i class="bi bi-box-seam"></i> Produk</a>
            <a href="{{ route('settings.index') }}" class="admin-nav-link{{ request()->routeIs('settings.*') ? ' active' : '' }}"><i class="bi bi-gear"></i> Pengaturan akun</a>
        </nav>
        <div class="admin-sidebar-bottom">
            <div class="admin-user">
                <span class="admin-avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                <div class="min-w-0">
                    <div class="admin-user-name">{{ auth()->user()->name }}</div>
                    <div class="admin-user-role">Administrator</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="admin-nav-link admin-logout border-0 bg-transparent"><i class="bi bi-box-arrow-left"></i> Keluar</button>
            </form>
        </div>
    </aside>
    <main class="admin-main">
        <header class="admin-topbar">
            <h1>@yield('page-title', 'Dashboard')</h1>
            <span class="admin-topbar-date"><i class="bi bi-clock me-1"></i>{{ now('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</span>
        </header>
        <div class="admin-content">
            @yield('content')
        </div>
    </main>
</body>
</html>

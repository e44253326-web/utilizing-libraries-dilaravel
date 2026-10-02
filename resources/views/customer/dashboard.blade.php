<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akun Pelanggan | TOKO DIAN NAME</title>
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/pink-theme.css') }}" rel="stylesheet">
</head>
<body class="bg-light customer-dashboard">
    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <span class="navbar-brand fw-bold"><i class="bi bi-shop me-2"></i>TOKO DIAN NAME</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-outline-light btn-sm" type="submit">Keluar</button>
            </form>
        </div>
    </nav>
    <main class="container py-5">
        <div class="card border-0 shadow-sm mx-auto" style="max-width: 680px">
            <div class="card-body p-4 p-md-5">
                <span class="badge text-bg-primary mb-3">Akun pelanggan</span>
                <h1 class="h3">Halo, {{ auth()->user()->name }}</h1>
                <p class="text-secondary mb-0">Akun Anda adalah akun pelanggan biasa. Akses admin dan kasir hanya diberikan kepada akun staf yang ditetapkan secara terpisah.</p>
            </div>
        </div>
    </main>
</body>
</html>

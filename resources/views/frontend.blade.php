<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda Modern - Offline Mode</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background-color: #0f172a; color: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; display: flex; flex-direction: column; min-height: 100vh; }
        .navbar { background-color: #1e293b; padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #334155; }
        .brand { font-size: 24px; font-weight: 800; color: #ffffff; text-decoration: none; letter-spacing: 1px; }
        .brand span { color: #8b5cf6; }
        .btn { display: inline-block; padding: 10px 24px; border-radius: 50px; font-weight: 600; text-decoration: none; transition: all 0.3s ease; cursor: pointer; }
        .btn-primary { background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%); color: white; border: none; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(139, 92, 246, 0.4); }
        .hero { flex: 1; display: flex; align-items: center; justify-content: center; padding: 60px 40px; max-width: 1200px; margin: 0 auto; width: 100%; gap: 50px; }
        .hero-text { flex: 1; }
        .badge { display: inline-block; background-color: rgba(139, 92, 246, 0.1); color: #a78bfa; padding: 6px 16px; rounded-pill: 50px; border-radius: 50px; font-size: 14px; font-weight: 600; margin-bottom: 20px; }
        .hero-title { font-size: 48px; font-weight: 800; line-height: 1.2; margin-bottom: 20px; color: #ffffff; }
        .hero-subtitle { font-size: 18px; color: #94a3b8; line-height: 1.6; margin-bottom: 30px; }
        .hero-visual { flex: 1; display: flex; justify-content: center; }
        .preview-card { background-color: #1e293b; border: 1px solid #334155; border-radius: 20px; padding: 40px; width: 100%; max-width: 450px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.3); }
        .dots { display: flex; gap: 8px; margin-bottom: 20px; }
        .dot { width: 12px; height: 12px; border-radius: 50%; }
        .dot-r { background-color: #ef4444; }
        .dot-y { background-color: #f59e0b; }
        .dot-g { background-color: #10b981; }
        .preview-card h3 { font-size: 20px; margin-bottom: 10px; color: #ffffff; }
        .preview-card p { color: #94a3b8; font-size: 14px; line-height: 1.5; }
        @media (max-width: 768px) { .hero { flex-direction: column; text-align: center; } .navbar { padding: 20px; } }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="#" class="brand">NEO<span>SPACE</span></a>
        <a href="{{ url('/login') }}" class="btn btn-primary">Sign In</a>
    </nav>

    <div class="hero">
        <div class="hero-text">
            <span class="badge">Pure CSS Offline Mode</span>
            <h1 class="hero-title">Jelajahi Pengalaman Digital Baru.</h1>
            <p class="hero-subtitle">Kelola sistem, basis data, dan performa web Anda melalui satu dasbor terintegrasi yang responsif dan cepat.</p>
            <a href="{{ url('/login') }}" class="btn btn-primary" style="padding: 14px 32px; font-size: 16px;">Mulai Sekarang &rarr;</a>
        </div>
        <div class="hero-visual">
            <div class="preview-card">
                <div class="dots">
                    <div class="dot dot-r"></div>
                    <div class="dot dot-y"></div>
                    <div class="dot dot-g"></div>
                </div>
                <h3>Sistem Terintegrasi</h3>
                <p>Alur otentikasi siap diuji. Klik tombol masuk di atas atau tombol utama untuk melanjutkan proses validasi halaman login.</p>
            </div>
        </div>
    </div>

</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pembuat Komponen - Offline</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background-color: #0b0f19; height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Segoe UI', sans-serif; }
        .login-card { background-color: #1e293b; border: 1px solid #334155; border-radius: 24px; width: 100%; max-width: 400px; padding: 40px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); text-align: center; color: #ffffff; }
        .header mb { margin-bottom: 30px; }
        .header h2 { font-size: 26px; font-weight: 700; margin-bottom: 8px; }
        .header p { color: #94a3b8; font-size: 14px; }
        .form-group { text-align: left; margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; color: #94a3b8; font-weight: 600; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-control { width: 100%; background-color: #0f172a; border: 1px solid #334155; color: #f8fafc; padding: 12px 16px; border-radius: 12px; font-size: 15px; outline: none; }
        .form-control:focus { border-color: #8b5cf6; }
        .btn-submit { width: 100%; background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%); color: white; border: none; padding: 14px; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; margin-top: 10px; }
        .btn-submit:hover { opacity: 0.95; transform: translateY(-1px); box-shadow: 0 10px 20px rgba(139, 92, 246, 0.3); }
        .back-link { display: inline-block; margin-top: 25px; color: #94a3b8; text-decoration: none; font-size: 14px; }
        .back-link:hover { color: #8b5cf6; }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="header" style="margin-bottom: 30px;">
            <h2>Selamat Datang</h2>
            <p>Gunakan akses instan di bawah untuk masuk</p>
        </div>
        
        <form action="{{ url('/admin') }}" method="GET">
            <div class="form-group">
                <label>Username / Email</label>
                <input type="text" class="form-control" value="administrator" readonly>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" class="form-control" value="••••••••" readonly>
            </div>
            
            <button type="submit" class="btn-submit">Masuk Ke Dasbor Admin</button>
        </form>

        <a href="{{ url('/') }}" class="back-link">&larr; Kembali ke Beranda</a>
    </div>

</body>
</html>

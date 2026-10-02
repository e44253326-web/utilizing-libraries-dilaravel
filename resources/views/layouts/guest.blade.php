<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TOKO DIAN NAME | Akun</title>
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet">
    <style>
        :root {
            color-scheme: light;
            font-family: Inter, "Segoe UI", Arial, sans-serif;
            background: #f4f5fb;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            color: #172039;
            background:
                radial-gradient(ellipse at 88% 8%, rgb(178 162 255 / 25%), transparent 30rem),
                radial-gradient(ellipse at 75% 92%, rgb(111 235 220 / 19%), transparent 30rem),
                #f4f5fb;
        }

        .auth-shell {
            display: grid;
            min-height: 100vh;
            grid-template-columns: minmax(0, 1.03fr) minmax(380px, 0.97fr);
        }

        .auth-brand-panel {
            position: relative;
            display: flex;
            min-height: 100vh;
            flex-direction: column;
            justify-content: space-between;
            padding: clamp(2rem, 5.7vw, 5.25rem);
            overflow: hidden;
            color: #f8f8ff;
            background:
                radial-gradient(ellipse at 12% 96%, rgb(79 228 214 / 20%), transparent 34rem),
                radial-gradient(ellipse at 95% 4%, rgb(255 112 183 / 20%), transparent 27rem),
                linear-gradient(145deg, #121532, #29255b 56%, #42368b);
            isolation: isolate;
        }

        .auth-brand-panel::before {
            position: absolute;
            inset: 0;
            z-index: -1;
            background-image: linear-gradient(rgb(255 255 255 / 3%) 1px, transparent 1px), linear-gradient(90deg, rgb(255 255 255 / 3%) 1px, transparent 1px);
            background-size: 38px 38px;
            content: "";
            mask-image: linear-gradient(130deg, #000, transparent 78%);
        }

        .auth-brand-panel::after {
            position: absolute;
            top: 27%;
            right: -145px;
            z-index: -1;
            width: 330px;
            height: 330px;
            border: 1px solid rgb(255 255 255 / 10%);
            border-radius: 50%;
            box-shadow: 0 0 0 32px rgb(255 255 255 / 3%), 0 0 0 70px rgb(255 255 255 / 2%);
            content: "";
        }

        .auth-brand {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            font-size: 0.96rem;
            font-weight: 850;
            letter-spacing: 0.055em;
        }

        .auth-brand-icon {
            display: grid;
            width: 48px;
            height: 48px;
            place-items: center;
            border: 1px solid rgb(255 255 255 / 29%);
            border-radius: 16px;
            color: #141a35;
            background: linear-gradient(145deg, #a4ffeb, #61ddda);
            box-shadow: 0 10px 24px rgb(66 221 212 / 25%), inset 0 2px 2px rgb(255 255 255 / 80%);
            font-size: 1.3rem;
            transform: rotate(-6deg);
        }

        .auth-brand-copy {
            position: relative;
            z-index: 1;
            max-width: 540px;
            margin-block: auto;
            padding-block: 4rem 13rem;
        }

        .auth-brand-copy::before {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            margin-bottom: 1rem;
            padding: 0.5rem 0.8rem;
            border: 1px solid rgb(130 255 229 / 25%);
            border-radius: 999px;
            color: #bafff0;
            background: rgb(107 255 224 / 8%);
            content: "✦  SISTEM TOKO TERINTEGRASI";
            font-size: 0.67rem;
            font-weight: 800;
            letter-spacing: 0.11em;
        }

        .auth-brand-copy h1 {
            max-width: 480px;
            margin: 0;
            font-size: clamp(2.2rem, 4.1vw, 3.7rem);
            font-weight: 900;
            letter-spacing: -0.055em;
            line-height: 1.04;
        }

        .auth-brand-copy p,
        .auth-brand-footnote {
            color: rgb(231 235 255 / 75%);
        }

        .auth-brand-copy p { max-width: 410px; line-height: 1.7; }

        .auth-visual {
            position: absolute;
            right: clamp(1.5rem, 5vw, 4.5rem);
            bottom: 4.6rem;
            width: min(78%, 390px);
            height: 190px;
            padding: 17px;
            border: 1px solid rgb(255 255 255 / 22%);
            border-radius: 19px;
            background: linear-gradient(145deg, rgb(255 255 255 / 13%), rgb(255 255 255 / 5%));
            box-shadow: 0 25px 50px rgb(0 0 0 / 25%), inset 0 1px rgb(255 255 255 / 18%);
            backdrop-filter: blur(12px);
            transform: perspective(700px) rotateY(-10deg) rotateX(5deg) rotateZ(-2deg);
        }

        .auth-visual-top { display: flex; align-items: center; justify-content: space-between; }
        .auth-visual-top strong { font-size: 0.72rem; }
        .auth-visual-top span { color: #a8ffdf; font-size: 0.62rem; font-weight: 750; }
        .auth-visual-stats { display: flex; gap: 9px; margin-top: 12px; }
        .auth-visual-stat { flex: 1; padding: 9px 10px; border: 1px solid rgb(255 255 255 / 10%); border-radius: 12px; background: rgb(255 255 255 / 7%); }
        .auth-visual-stat small, .auth-visual-stat b { display: block; }
        .auth-visual-stat small { margin-bottom: 5px; color: rgb(231 235 255 / 68%); font-size: 0.55rem; }
        .auth-visual-stat b { color: #fff; font-size: 0.8rem; }
        .auth-visual-chart { display: flex; height: 43px; align-items: end; gap: 7px; margin-top: 10px; }
        .auth-visual-chart i { flex: 1; height: var(--bar-height); border-radius: 5px 5px 2px 2px; background: linear-gradient(180deg, #75f4dc, #8e84ff); box-shadow: 0 0 13px rgb(117 244 220 / 18%); }

        .auth-brand-footnote { font-size: 0.75rem; }

        .auth-content-panel {
            display: grid;
            min-width: 0;
            place-items: center;
            padding: clamp(1.5rem, 5vw, 4rem);
        }

        .auth-card {
            width: min(100%, 450px);
            padding: clamp(1.6rem, 4vw, 2.55rem);
            border: 1px solid rgb(255 255 255 / 90%);
            border-radius: 24px;
            background: rgb(255 255 255 / 88%);
            box-shadow: 0 28px 75px rgb(36 42 84 / 13%), 0 5px 16px rgb(36 42 84 / 5%), inset 0 1px #fff;
            backdrop-filter: blur(16px);
        }

        .auth-card h2 {
            color: #18213d;
            font-weight: 850;
            letter-spacing: -0.035em;
        }

        .auth-card .text-primary,
        .auth-card .auth-eyebrow { color: #6354d9 !important; }

        .auth-card .form-label {
            color: #303a56;
            font-size: 0.88rem;
            font-weight: 700;
        }

        .auth-card .form-control {
            min-height: 48px;
            border-color: #dfe3f1;
            border-radius: 12px;
            background-color: #fbfcff;
            transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
        }

        .auth-card .form-control:focus {
            border-color: #8276ee;
            background-color: #fff;
            box-shadow: 0 0 0 0.23rem rgb(130 118 238 / 15%);
        }

        .auth-card .input-group .btn {
            min-width: 48px;
            border-color: #dfe3f1;
            border-radius: 0 12px 12px 0;
            color: #59627b;
            background: #fbfcff;
        }

        .auth-card .input-group .btn:hover {
            color: #5747ce;
            background: #f0efff;
        }

        .auth-card .btn-primary {
            min-height: 49px;
            border: 0;
            border-radius: 12px;
            color: #fff;
            background: linear-gradient(110deg, #6257df, #7663ee 54%, #8f71f4);
            box-shadow: 0 9px 20px rgb(102 85 222 / 23%), inset 0 1px rgb(255 255 255 / 24%);
            font-weight: 750;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .auth-card .btn-primary:hover {
            background: linear-gradient(110deg, #5145ca, #6653de 54%, #805ee4);
            box-shadow: 0 12px 25px rgb(102 85 222 / 31%), inset 0 1px rgb(255 255 255 / 24%);
            transform: translateY(-2px);
        }

        .auth-card .form-check-input:checked {
            border-color: #6a5ce2;
            background-color: #6a5ce2;
        }

        .auth-card .alert { border: 0; border-radius: 12px; }
        .auth-link { color: #5d50d1; font-size: 0.88rem; font-weight: 700; text-decoration: none; }
        .auth-link:hover { color: #4336b4; text-decoration: underline; }

        @media (max-width: 767.98px) {
            .auth-shell { grid-template-columns: 1fr; }
            .auth-brand-panel { min-height: 0; padding: 1.25rem 1.5rem 1.4rem; }
            .auth-brand { font-size: 0.82rem; }
            .auth-brand-icon { width: 40px; height: 40px; border-radius: 13px; }
            .auth-brand-copy { padding-block: 1.7rem 0.2rem; }
            .auth-brand-copy::before { margin-bottom: 0.65rem; padding: 0.4rem 0.65rem; font-size: 0.58rem; }
            .auth-brand-copy h1 { max-width: 520px; font-size: clamp(1.8rem, 7vw, 2.5rem); }
            .auth-brand-copy p, .auth-brand-footnote, .auth-visual { display: none; }
            .auth-content-panel { align-items: start; padding: 1.25rem 1rem 2rem; }
            .auth-card { padding: 1.5rem; border-radius: 20px; }
        }

        @media (max-width: 380px) {
            .auth-brand-panel { padding-inline: 1.1rem; }
            .auth-content-panel { padding-inline: 0.8rem; }
            .auth-card { padding: 1.25rem; }
            .auth-card .d-flex.justify-content-between { gap: 0.5rem !important; }
            .auth-link { font-size: 0.8rem; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; }
        }
    </style>
    <link href="{{ asset('assets/css/pink-theme.css') }}" rel="stylesheet">
</head>
<body>
    <main class="auth-shell">
        <section class="auth-brand-panel">
            <div class="auth-brand">
                <span class="auth-brand-icon"><i class="bi bi-shop"></i></span>
                <span>TOKO DIAN NAME</span>
            </div>
            <div class="auth-brand-copy">
                <h1>Operasional toko lebih mudah, transaksi lebih cepat.</h1>
                <p class="mt-3 mb-0">Masuk untuk mengelola transaksi penjualan dan melayani pelanggan.</p>
            </div>
            <div class="auth-visual" aria-hidden="true">
                <div class="auth-visual-top">
                    <strong>Ringkasan toko</strong>
                    <span>● AKTIF</span>
                </div>
                <div class="auth-visual-stats">
                    <div class="auth-visual-stat"><small>Penjualan hari ini</small><b>Rp 2.450.000</b></div>
                    <div class="auth-visual-stat"><small>Transaksi</small><b>38 pesanan</b></div>
                </div>
                <div class="auth-visual-chart">
                    <i style="--bar-height: 38%"></i>
                    <i style="--bar-height: 60%"></i>
                    <i style="--bar-height: 48%"></i>
                    <i style="--bar-height: 74%"></i>
                    <i style="--bar-height: 57%"></i>
                    <i style="--bar-height: 88%"></i>
                    <i style="--bar-height: 100%"></i>
                </div>
            </div>
            <div class="auth-brand-footnote small">Sistem kasir minimarket</div>
        </section>
        <section class="auth-content-panel">
            <div class="auth-card">
                {{ $slot }}
            </div>
        </section>
    </main>
    <script>
        document.addEventListener('click', (event) => {
            const button = event.target.closest('[data-toggle-password]');

            if (!button) {
                return;
            }

            const input = document.getElementById(button.dataset.togglePassword);
            const icon = button.querySelector('i');

            if (!input || !icon) {
                return;
            }

            const showPassword = input.type === 'password';
            input.type = showPassword ? 'text' : 'password';
            icon.classList.toggle('bi-eye', !showPassword);
            icon.classList.toggle('bi-eye-slash', showPassword);
            button.setAttribute('aria-label', showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
            button.setAttribute('title', showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        });
    </script>
</body>
</html>

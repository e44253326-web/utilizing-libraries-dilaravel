<x-guest-layout>
    <div class="mb-4">
        <div class="text-primary small fw-bold text-uppercase mb-2">Selamat datang</div>
        <h2 class="h3 mb-2">Masuk ke akun Anda</h2>
        <p class="text-secondary mb-0">Masukkan email dan kata sandi akun Anda.</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success" role="status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Alamat email</label>
            <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nama@email.com">
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Kata sandi</label>
            <div class="input-group">
                <input id="password" class="form-control" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan kata sandi">
                <button class="btn btn-outline-secondary" type="button" data-toggle-password="password" aria-label="Tampilkan kata sandi" title="Tampilkan kata sandi">
                    <i class="bi bi-eye" aria-hidden="true"></i>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-between align-items-center gap-3 mb-4">
            <div class="form-check">
                <input id="remember_me" class="form-check-input" type="checkbox" name="remember">
                <label class="form-check-label small text-secondary" for="remember_me">Ingat saya</label>
            </div>
            @if (Route::has('password.request'))
                <a class="auth-link" href="{{ route('password.request') }}">Lupa kata sandi?</a>
            @endif
        </div>

        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
        </button>
    </form>

    @if (Route::has('register'))
        <div class="text-center border-top mt-4 pt-3">
            <span class="small text-secondary">Belum punya akun?</span>
            <a class="auth-link ms-1" href="{{ route('register') }}">Daftar di sini</a>
        </div>
    @endif
</x-guest-layout>

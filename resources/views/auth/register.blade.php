<x-guest-layout>
    <div class="mb-4">
        <div class="text-primary small fw-bold text-uppercase mb-2">Akun pelanggan</div>
        <h2 class="h3 mb-2">Buat akun baru</h2>
        <p class="text-secondary mb-0">Daftar untuk membuat akun pelanggan. Akun admin dan kasir dikelola terpisah.</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Nama lengkap</label>
            <input id="name" class="form-control" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Nama lengkap">
            @error('name')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Alamat email</label>
            <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="nama@email.com">
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Kata sandi</label>
            <div class="input-group">
                <input id="password" class="form-control" type="password" name="password" required autocomplete="new-password" placeholder="Buat kata sandi">
                <button class="btn btn-outline-secondary" type="button" data-toggle-password="password" aria-label="Tampilkan kata sandi" title="Tampilkan kata sandi">
                    <i class="bi bi-eye" aria-hidden="true"></i>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Ulangi kata sandi</label>
            <div class="input-group">
                <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi kata sandi">
                <button class="btn btn-outline-secondary" type="button" data-toggle-password="password_confirmation" aria-label="Tampilkan kata sandi" title="Tampilkan kata sandi">
                    <i class="bi bi-eye" aria-hidden="true"></i>
                </button>
            </div>
            @error('password_confirmation')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-person-plus me-2"></i>Buat akun dan masuk
        </button>
    </form>

    <div class="text-center border-top mt-4 pt-3">
        <span class="small text-secondary">Sudah punya akun?</span>
        <a class="auth-link ms-1" href="{{ route('login') }}">Masuk</a>
    </div>
</x-guest-layout>

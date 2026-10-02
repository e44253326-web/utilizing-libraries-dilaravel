<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Transaksi Ditahan | TOKO DIAN NAME</title>
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/kasir.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/pink-theme.css') }}" rel="stylesheet">
</head>
<body>
    <header class="store-header">
        <div class="container-fluid px-3 px-xl-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="store-mark"><i class="bi bi-shop"></i></div>
                <div>
                    <h1 class="store-name">TOKO DIAN NAME</h1>
                    <div class="store-address"><i class="bi bi-pause-circle me-1"></i>Transaksi yang menunggu dilanjutkan</div>
                </div>
            </div>
            <a href="{{ route('kasir.index') }}" class="btn btn-light"><i class="bi bi-arrow-left me-1"></i> Kembali ke Kasir</a>
        </div>
    </header>
    <main class="container-fluid px-3 px-xl-4 py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
            <div>
                <div class="eyebrow">DAFTAR TRANSAKSI</div>
                <h2 class="h4 mb-1">Transaksi Ditahan</h2>
                <p class="text-secondary mb-0">Pilih transaksi untuk melanjutkan pembayaran.</p>
            </div>
            <span class="item-counter">{{ $transactions->count() }} transaksi</span>
        </div>

        @if ($transactions->isEmpty())
            <div class="empty-state card pos-card">
                <div class="empty-state-icon"><i class="bi bi-inbox"></i></div>
                <h3 class="h5">Tidak ada transaksi ditahan</h3>
                <p class="text-secondary">Transaksi yang ditahan akan muncul di halaman ini.</p>
                <a href="{{ route('kasir.index') }}" class="btn btn-primary align-self-center"><i class="bi bi-cart-plus me-1"></i> Transaksi Baru</a>
            </div>
        @else
            <div class="row g-3">
                @foreach ($transactions as $transaction)
                    <div class="col-12 col-md-6 col-xl-4">
                        <article class="card pos-card held-card h-100">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between gap-2">
                                    <div>
                                        <div class="transaction-number">{{ $transaction->transaction_number }}</div>
                                        <small class="text-secondary">{{ $transaction->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</small>
                                    </div>
                                    <span class="badge text-bg-warning align-self-start">Ditahan</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-2"><span class="text-secondary">Pelanggan</span><strong>{{ $transaction->customer_name }}</strong></div>
                                <div class="d-flex justify-content-between mb-2"><span class="text-secondary">Jumlah item</span><strong>{{ $transaction->details->sum('qty') }} item</strong></div>
                                <div class="d-flex justify-content-between mb-3"><span class="text-secondary">Total sementara</span><strong class="text-primary">{{ 'Rp'.number_format((float) $transaction->grand_total, 0, ',', '.') }}</strong></div>
                                <div class="d-grid gap-2 mt-auto">
                                    <a href="{{ route('kasir.index', ['held' => $transaction->id]) }}" class="btn btn-primary"><i class="bi bi-play-fill me-1"></i> Lanjutkan</a>
                                    <form method="POST" action="{{ route('kasir.held.destroy', $transaction) }}" onsubmit="return confirm('Hapus transaksi yang ditahan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger w-100"><i class="bi bi-trash me-1"></i> Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        @endif
    </main>
</body>
</html>

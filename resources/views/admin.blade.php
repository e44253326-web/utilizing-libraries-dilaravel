@extends('layouts.admin')

@section('title', 'Dashboard Admin | TOKO DIAN NAME')
@section('page-title', 'Ringkasan Dashboard')

@section('content')
    <div class="admin-welcome mb-4">
        <div>
            <div class="admin-eyebrow">DASHBOARD ADMIN</div>
            <h2 class="h4 mb-1">Selamat datang, {{ $adminName }}</h2>
            <p class="text-secondary mb-0">Berikut ringkasan operasional TOKO DIAN NAME hari ini.</p>
        </div>
        <div class="admin-date"><i class="bi bi-calendar3 me-2"></i>{{ now('Asia/Jakarta')->translatedFormat('l, d F Y') }}</div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <article class="admin-stat-card">
                <div class="admin-stat-icon blue"><i class="bi bi-box-seam"></i></div>
                <div class="admin-stat-label">Total Produk</div>
                <div class="admin-stat-value">{{ number_format($productCount, 0, ',', '.') }}</div>
                <div class="admin-stat-note">produk terdaftar</div>
            </article>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <article class="admin-stat-card">
                <div class="admin-stat-icon green"><i class="bi bi-receipt"></i></div>
                <div class="admin-stat-label">Transaksi Hari Ini</div>
                <div class="admin-stat-value">{{ number_format($todayTransactions, 0, ',', '.') }}</div>
                <div class="admin-stat-note">transaksi berhasil dibayar</div>
            </article>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <article class="admin-stat-card">
                <div class="admin-stat-icon purple"><i class="bi bi-cash-stack"></i></div>
                <div class="admin-stat-label">Penjualan Hari Ini</div>
                <div class="admin-stat-value admin-currency">Rp{{ number_format((float) $todayRevenue, 0, ',', '.') }}</div>
                <div class="admin-stat-note">total transaksi lunas</div>
            </article>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <article class="admin-stat-card">
                <div class="admin-stat-icon orange"><i class="bi bi-exclamation-triangle"></i></div>
                <div class="admin-stat-label">Stok Perlu Dicek</div>
                <div class="admin-stat-value">{{ number_format($lowStockCount, 0, ',', '.') }}</div>
                <div class="admin-stat-note">{{ number_format($outOfStockCount, 0, ',', '.') }} produk stok habis</div>
            </article>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-xl-8">
            <section class="admin-panel h-100">
                <div class="admin-panel-header">
                    <div>
                        <h3 class="h6 mb-1">Transaksi Terbaru</h3>
                        <p class="text-secondary small mb-0">Transaksi pembayaran yang berhasil.</p>
                    </div>
                    <a href="{{ route('kasir.index') }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-plus-lg me-1"></i> Transaksi Baru</a>
                </div>
                <div class="table-responsive">
                    <table class="table admin-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>No. Transaksi</th>
                                <th>Kasir</th>
                                <th>Metode</th>
                                <th>Waktu</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentTransactions as $transaction)
                                <tr>
                                    <td class="fw-semibold text-primary">{{ $transaction->transaction_number }}</td>
                                    <td>{{ $transaction->user?->name ?? 'Kasir' }}</td>
                                    <td><span class="admin-payment-badge">{{ $transaction->payment_method }}</span></td>
                                    <td class="text-secondary text-nowrap">{{ $transaction->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</td>
                                    <td class="text-end fw-semibold text-nowrap">Rp{{ number_format((float) $transaction->grand_total, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="admin-empty">Belum ada transaksi berhasil.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="col-12 col-xl-4">
            <section class="admin-panel h-100">
                <div class="admin-panel-header">
                    <div>
                        <h3 class="h6 mb-1">Stok Menipis</h3>
                        <p class="text-secondary small mb-0">Produk dengan stok 5 atau kurang.</p>
                    </div>
                    <span class="admin-count-badge">{{ $lowStockCount }}</span>
                </div>
                <div class="low-stock-list">
                    @forelse ($lowStockProducts as $product)
                        <div class="low-stock-item">
                            <div class="low-stock-product">
                                <span class="low-stock-name">{{ $product->name }}</span>
                                <small>Rp{{ number_format((float) $product->price, 0, ',', '.') }}</small>
                            </div>
                            @if ($product->stock === 0)
                                <span class="stock-badge empty">Habis</span>
                            @else
                                <span class="stock-badge low">{{ $product->stock }} tersisa</span>
                            @endif
                        </div>
                    @empty
                        <div class="admin-empty">Semua stok produk mencukupi.</div>
                    @endforelse
                </div>
                @if ($heldCount > 0)
                    <a href="{{ route('kasir.ditahan') }}" class="held-notice">
                        <span><i class="bi bi-pause-circle me-2"></i>{{ $heldCount }} transaksi menunggu</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                @endif
            </section>
        </div>
    </div>
@endsection

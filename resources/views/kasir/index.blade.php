<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kasir | TOKO DIAN NAME</title>
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/kasir.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/pink-theme.css') }}" rel="stylesheet">
</head>
<body>
    <header class="store-header">
        <div class="container-fluid px-3 px-xl-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="store-mark"><i class="bi bi-shop"></i></div>
                    <div>
                        <h1 class="store-name">TOKO DIAN NAME</h1>
                        <div class="store-address"><i class="bi bi-geo-alt me-1"></i>Alamat toko belum dikonfigurasi</div>
                    </div>
                </div>
                <div class="transaction-meta text-md-end">
                    <div class="meta-label">Nomor transaksi</div>
                    <div id="transactionNumber" class="transaction-number">{{ $transactionNumber }}</div>
                    <div class="meta-small"><span id="currentDateTime">{{ now('Asia/Jakarta')->format('d/m/Y H:i:s') }}</span> <span class="mx-1">·</span> Kasir: {{ $cashierName }}</div>
                </div>
            </div>
        </div>
    </header>

    <main class="container-fluid px-3 px-xl-4 py-4">
        <div class="page-heading d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
            <div>
                <div class="eyebrow">POINT OF SALE</div>
                <h2 class="h4 mb-1">Transaksi Pembayaran</h2>
                <p class="text-secondary mb-0">Cari produk, atur keranjang, lalu selesaikan pembayaran.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('settings.index') }}" class="btn btn-light border">
                    <i class="bi bi-gear me-1"></i> Pengaturan akun
                </a>
                <a href="{{ route('kasir.ditahan') }}" class="btn btn-light border position-relative">
                    <i class="bi bi-pause-circle me-1"></i> Transaksi Ditahan
                    @if ($heldCount > 0)
                        <span class="badge rounded-pill text-bg-danger ms-1">{{ $heldCount }}</span>
                    @endif
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-box-arrow-right me-1"></i> Keluar
                    </button>
                </form>
            </div>
        </div>

        <div class="row g-3 align-items-start">
            <section class="col-12 col-xl-8">
                <div class="card pos-card product-search-card mb-3">
                    <div class="card-header pos-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <span class="section-icon"><i class="bi bi-upc-scan"></i></span>
                            <div>
                                <h3 class="h6 mb-0">Tambah Barang</h3>
                                <small class="text-secondary">Produk terbaru tersinkron otomatis. Ketik nama untuk mencari.</small>
                            </div>
                        </div>
                        <span class="keyboard-hint d-none d-md-inline">F2 <span>cari barang</span></span>
                    </div>
                    <div class="card-body">
                        <div class="search-row">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white"><i class="bi bi-search text-secondary"></i></span>
                                <input type="search" id="searchBarang" class="form-control" placeholder="Cari nama produk..." autocomplete="off" aria-label="Cari produk">
                                <button type="button" id="tombolCari" class="btn btn-primary px-4"><i class="bi bi-search me-1"></i> Cari</button>
                            </div>
                            <div id="hasilPencarian" class="search-results d-none" aria-live="polite"></div>
                        </div>

                        <div class="customer-fields mt-3">
                            <div>
                                <label for="pelanggan" class="form-label">Pelanggan</label>
                                <select id="pelanggan" class="form-select">
                                    <option value="Umum">Umum</option>
                                    <option value="lainnya">Pelanggan lain</option>
                                </select>
                            </div>
                            <div id="namaPelangganWrap" class="d-none">
                                <label for="namaPelanggan" class="form-label">Nama pelanggan</label>
                                <input id="namaPelanggan" type="text" class="form-control" maxlength="255" placeholder="Nama pelanggan">
                            </div>
                            <div>
                                <label for="nomorPelanggan" class="form-label">No. member / HP <span class="text-secondary fw-normal">(opsional)</span></label>
                                <input id="nomorPelanggan" type="text" class="form-control" maxlength="30" placeholder="08xx-xxxx-xxxx">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card pos-card">
                    <div class="card-header pos-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <span class="section-icon cart-icon"><i class="bi bi-basket2"></i></span>
                            <div>
                                <h3 class="h6 mb-0">Keranjang Belanja</h3>
                                <small class="text-secondary">Pastikan jumlah dan barang sudah sesuai</small>
                            </div>
                        </div>
                        <span id="jumlahItem" class="item-counter">0 item</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table cart-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Barang</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Subtotal</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="keranjang"></tbody>
                        </table>
                    </div>
                    <div class="cart-footer d-flex justify-content-between">
                        <span><i class="bi bi-box-seam me-1"></i> Stok produk berkurang setelah transaksi berhasil</span>
                        <button id="kosongkanKeranjang" type="button" class="btn btn-sm btn-link text-danger p-0">Kosongkan</button>
                    </div>
                </div>
            </section>

            <aside class="col-12 col-xl-4">
                <div class="card pos-card payment-card">
                    <div class="card-header pos-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <span class="section-icon payment-icon"><i class="bi bi-receipt-cutoff"></i></span>
                            <div>
                                <h3 class="h6 mb-0">Ringkasan Pembayaran</h3>
                                <small class="text-secondary">Total dihitung kembali oleh server</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="summary-line"><span>Total Item</span><strong id="totalItem">0</strong></div>
                        <div class="summary-line"><span>Subtotal</span><strong id="subtotalText">Rp0</strong></div>

                        <div class="adjustment-grid">
                            <div>
                                <label for="diskonPersen" class="form-label">Diskon (%)</label>
                                <div class="input-group">
                                    <input id="diskonPersen" type="number" class="form-control" value="0" min="0" max="100" step="0.01" inputmode="decimal">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div>
                                <label for="diskonRupiah" class="form-label">Diskon (Rp)</label>
                                <input id="diskonRupiah" type="number" class="form-control" value="0" min="0" step="1" inputmode="numeric">
                            </div>
                            <div>
                                <label for="pajak" class="form-label">Pajak / PPN</label>
                                <input id="pajak" type="number" class="form-control" value="0" min="0" step="1" inputmode="numeric">
                            </div>
                            <div>
                                <label for="biayaLain" class="form-label">Biaya lain</label>
                                <input id="biayaLain" type="number" class="form-control" value="0" min="0" step="1" inputmode="numeric">
                            </div>
                        </div>

                        <div class="grand-total-box">
                            <div>
                                <span class="grand-total-label">TOTAL AKHIR</span>
                                <small id="rincianDiskon" class="d-block text-white-50">Diskon Rp0</small>
                            </div>
                            <strong id="totalAkhir">Rp0</strong>
                        </div>

                        <label for="uangDibayar" class="form-label fw-semibold mt-3">Uang Dibayar</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white">Rp</span>
                            <input id="uangDibayar" type="number" class="form-control" value="0" min="0" step="1" inputmode="numeric" aria-label="Nominal uang dibayar">
                        </div>
                        <div class="quick-amounts mt-2">
                            <button type="button" class="btn btn-sm btn-light border" data-quick-amount="exact">Uang Pas</button>
                            <button type="button" class="btn btn-sm btn-light border" data-quick-amount="40000">Rp40.000</button>
                            <button type="button" class="btn btn-sm btn-light border" data-quick-amount="50000">Rp50.000</button>
                            <button type="button" class="btn btn-sm btn-light border" data-quick-amount="100000">Rp100.000</button>
                        </div>

                        <div id="kembaliBox" class="change-box insufficient mt-3" aria-live="polite">
                            <span id="labelKembalian">UANG KURANG</span>
                            <strong id="kembalian">Rp0</strong>
                        </div>

                        <div class="mt-3">
                            <div class="form-label fw-semibold mb-2">Metode Pembayaran</div>
                            <div class="payment-methods" role="group" aria-label="Metode pembayaran">
                                @foreach (['Tunai', 'QRIS', 'Debit', 'Kredit', 'E-Wallet', 'Transfer'] as $method)
                                    <button type="button" class="payment-method{{ $loop->first ? ' active' : '' }}" data-method="{{ $method }}">
                                        <i class="bi {{ ['Tunai' => 'bi-cash-coin', 'QRIS' => 'bi-qr-code', 'Debit' => 'bi-credit-card', 'Kredit' => 'bi-credit-card-2-front', 'E-Wallet' => 'bi-phone', 'Transfer' => 'bi-bank'][$method] }}"></i>
                                        <span>{{ $method }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div id="notifikasiKasir" class="alert d-none mt-3 mb-0" role="alert"></div>
                        <div class="action-buttons mt-3">
                            <button type="button" id="tahanTransaksi" class="btn btn-outline-warning"><i class="bi bi-pause-circle me-1"></i> Tahan</button>
                            <button type="button" id="batalTransaksi" class="btn btn-outline-danger"><i class="bi bi-x-circle me-1"></i> Batal</button>
                            <button type="button" id="bayarCetak" class="btn btn-success btn-pay"><i class="bi bi-printer me-2"></i> BAYAR &amp; CETAK</button>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
        <footer class="pos-footer text-center mt-4">F2 cari barang <span>·</span> F4 uang dibayar <span>·</span> ESC batal transaksi</footer>
    </main>

    <script>
        window.kasirConfig = {{ \Illuminate\Support\Js::from([
            'searchUrl' => route('kasir.search'),
            'storeUrl' => route('kasir.store'),
            'holdUrl' => route('kasir.hold'),
            'heldDeleteBaseUrl' => url('/kasir/ditahan'),
            'csrfToken' => csrf_token(),
            'heldTransaction' => $heldTransaction,
        ]) }};
    </script>
    <script src="{{ asset('assets/js/kasir.js') }}" defer></script>
</body>
</html>

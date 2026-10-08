<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk {{ $transaction->transaction_number }} | TOKO DIAN NAME</title>
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        body { background: #f4f7fb; color: #1d2939; font-family: Arial, sans-serif; }
        .receipt { width: min(100%, 440px); margin: 2rem auto; padding: 1.5rem; background: white; border: 1px solid #e5eaf1; border-radius: 12px; }
        .receipt-brand { font-size: 1.15rem; font-weight: 800; letter-spacing: .04em; }
        .receipt-meta, .receipt-footnote { color: #667085; font-size: .82rem; }
        .receipt-table { font-size: .82rem; }
        .receipt-table th { color: #667085; font-weight: 600; }
        .receipt-total { font-size: 1.2rem; font-weight: 800; }
        .receipt-rule { border-top: 1px dashed #98a2b3; opacity: 1; }
        @media print {
            body { background: white; }
            .receipt { width: 80mm; max-width: 80mm; margin: 0; padding: 0; border: 0; border-radius: 0; }
            .print-actions { display: none !important; }
            @page { size: auto; margin: 5mm; }
        }
    </style>
    <link href="{{ asset('assets/css/pink-theme.css') }}" rel="stylesheet">
</head>
<body>
    <main class="receipt">
        @if (request()->query('thermal_print') === 'success' || session('success'))
            <div class="alert alert-success print-actions" role="status">
                {{ session('success', 'Struk berhasil dikirim ke printer thermal.') }}
            </div>
        @elseif (request()->query('thermal_print') === 'failed' || session('error'))
            <div class="alert alert-warning print-actions" role="alert">
                {{ session('error', 'Transaksi tersimpan, tetapi struk thermal tidak tercetak. Periksa printer lalu coba lagi.') }}
            </div>
        @endif
        <div class="text-center mb-3">
            <div class="receipt-brand">TOKO DIAN NAME</div>
            <div class="receipt-meta">Alamat toko belum dikonfigurasi</div>
            <div class="receipt-meta">Terima kasih telah berbelanja</div>
        </div>
        <hr class="receipt-rule">
        <div class="receipt-meta">
            <div class="d-flex justify-content-between gap-2"><span>No. transaksi</span><strong class="text-dark">{{ $transaction->transaction_number }}</strong></div>
            <div class="d-flex justify-content-between gap-2"><span>Tanggal</span><span>{{ $transaction->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i:s') }}</span></div>
            <div class="d-flex justify-content-between gap-2"><span>Kasir</span><span>{{ $cashierName }}</span></div>
            <div class="d-flex justify-content-between gap-2"><span>Pelanggan</span><span>{{ $transaction->customer_name }}</span></div>
        </div>
        <hr class="receipt-rule">
        <div class="table-responsive">
            <table class="table table-sm table-borderless receipt-table mb-2">
                <thead>
                    <tr><th>Barang</th><th class="text-end">Qty</th><th class="text-end">Harga</th><th class="text-end">Subtotal</th></tr>
                </thead>
                <tbody>
                    @foreach ($transaction->details as $detail)
                        <tr>
                            <td>{{ $detail->product_name }}</td>
                            <td class="text-end">{{ $detail->qty }}</td>
                            <td class="text-end text-nowrap">{{ 'Rp'.number_format((float) $detail->price, 0, ',', '.') }}</td>
                            <td class="text-end text-nowrap">{{ 'Rp'.number_format((float) $detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <hr class="receipt-rule">
        <div class="receipt-meta">
            <div class="d-flex justify-content-between"><span>Subtotal</span><span>{{ 'Rp'.number_format((float) $transaction->subtotal, 0, ',', '.') }}</span></div>
            <div class="d-flex justify-content-between"><span>Diskon ({{ number_format((float) $transaction->discount_percent, 2, ',', '.') }}% + nominal)</span><span>-{{ 'Rp'.number_format((float) $transaction->discount, 0, ',', '.') }}</span></div>
            <div class="d-flex justify-content-between"><span>Pajak / PPN</span><span>{{ 'Rp'.number_format((float) $transaction->tax, 0, ',', '.') }}</span></div>
            <div class="d-flex justify-content-between"><span>Biaya lain</span><span>{{ 'Rp'.number_format((float) $transaction->other_fee, 0, ',', '.') }}</span></div>
        </div>
        <hr class="receipt-rule">
        <div class="d-flex justify-content-between receipt-total"><span>TOTAL</span><span>{{ 'Rp'.number_format((float) $transaction->grand_total, 0, ',', '.') }}</span></div>
        <div class="receipt-meta mt-2">
            <div class="d-flex justify-content-between"><span>Uang dibayar</span><span>{{ 'Rp'.number_format((float) $transaction->paid_amount, 0, ',', '.') }}</span></div>
            <div class="d-flex justify-content-between"><span>Kembalian</span><span>{{ 'Rp'.number_format((float) $transaction->change_amount, 0, ',', '.') }}</span></div>
            <div class="d-flex justify-content-between"><span>Metode pembayaran</span><span>{{ $transaction->payment_method }}</span></div>
        </div>
        <hr class="receipt-rule">
        <p class="receipt-footnote text-center mb-3">Barang yang sudah dibeli tidak dapat ditukar atau dikembalikan.</p>
        <div class="print-actions d-flex justify-content-center gap-2">
            <form method="POST" action="{{ route('kasir.receipt.print', $transaction) }}">
                @csrf
                <button type="submit" class="btn btn-primary">Cetak ulang via thermal</button>
            </form>
            <button type="button" class="btn btn-outline-primary" onclick="window.print()">Cetak via browser</button>
            <a href="{{ route('kasir.index') }}" class="btn btn-outline-secondary">Transaksi baru</a>
        </div>
    </main>
</body>
</html>

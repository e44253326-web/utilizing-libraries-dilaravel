<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $transaction->transaction_number }}</title>
    <style>
        body { color: #1d2939; font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1, .text-center { text-align: center; }
        .muted { color: #667085; }
        .meta, .totals { width: 100%; }
        .meta td, .totals td { padding: 4px 0; }
        .meta td:first-child, .totals td:first-child { color: #667085; }
        .meta td:last-child, .totals td:last-child { text-align: right; }
        table.items { width: 100%; margin-top: 20px; border-collapse: collapse; }
        .items th, .items td { padding: 8px; border-bottom: 1px solid #e5e7eb; }
        .items th { background: #f3f4f6; text-align: left; }
        .items .number { text-align: right; white-space: nowrap; }
        .totals { margin-top: 16px; }
        .grand-total td { padding-top: 10px; border-top: 1px solid #98a2b3; color: #1d2939; font-size: 14px; font-weight: bold; }
        .footer { margin-top: 28px; text-align: center; color: #667085; }
    </style>
</head>
<body>
    <h1>INVOICE</h1>
    <p class="text-center muted">TOKO DIAN NAME</p>

    <table class="meta">
        <tr>
            <td>No. transaksi</td>
            <td>{{ $transaction->transaction_number }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>{{ $transaction->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i:s') }}</td>
        </tr>
        <tr>
            <td>Kasir</td>
            <td>{{ $transaction->user?->name ?? 'Kasir' }}</td>
        </tr>
        <tr>
            <td>Pelanggan</td>
            <td>{{ $transaction->customer_name }}</td>
        </tr>
        @if ($transaction->customer_phone)
            <tr>
                <td>No. telepon</td>
                <td>{{ $transaction->customer_phone }}</td>
            </tr>
        @endif
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Produk</th>
                <th class="number">Harga</th>
                <th class="number">Jumlah</th>
                <th class="number">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaction->details as $detail)
                <tr>
                    <td>{{ $detail->product_name }}</td>
                    <td class="number">Rp {{ number_format((float) $detail->price, 0, ',', '.') }}</td>
                    <td class="number">{{ $detail->qty }}</td>
                    <td class="number">Rp {{ number_format((float) $detail->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td>Subtotal</td>
            <td>Rp {{ number_format((float) $transaction->subtotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Diskon</td>
            <td>- Rp {{ number_format((float) $transaction->discount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Pajak / PPN</td>
            <td>Rp {{ number_format((float) $transaction->tax, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Biaya lain</td>
            <td>Rp {{ number_format((float) $transaction->other_fee, 0, ',', '.') }}</td>
        </tr>
        <tr class="grand-total">
            <td>Total</td>
            <td>Rp {{ number_format((float) $transaction->grand_total, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Uang dibayar</td>
            <td>Rp {{ number_format((float) $transaction->paid_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Kembalian</td>
            <td>Rp {{ number_format((float) $transaction->change_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Metode pembayaran</td>
            <td>{{ $transaction->payment_method }}</td>
        </tr>
    </table>

    <p class="footer">Terima kasih telah berbelanja di TOKO DIAN NAME.</p>
</body>
</html>

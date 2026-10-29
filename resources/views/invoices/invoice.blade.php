<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice Kasir</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 30px;
        }
        .invoice {
            background: white;
            max-width: 600px;
            margin: auto;
            padding: 25px;
            border-radius: 10px;
        }
        h2, .center {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border-bottom: 1px solid #ddd;
            padding: 12px 5px;
            text-align: left;
        }
        .total {
            text-align: right;
            font-weight: bold;
        }
        button {
            padding: 10px 20px;
            background: #198754;
            color: white;
            border: none;
            cursor: pointer;
        }
        @media print {
            button { display: none; }
            body { background: white; }
        }
    </style>
</head>
<body>
<div class="invoice">
    <h2>INVOICE PEMBAYARAN</h2>
    <p class="center">Toko Penjualan</p>

    <p>No. Invoice: INV-001</p>
    <p>Tanggal: {{ date('d-m-Y') }}</p>
    <p>Kasir: Admin</p>

    <table>
        <tr>
            <th>Produk</th>
            <th>Jumlah</th>
            <th>Harga</th>
        </tr>
        <tr>
            <td>Buku</td>
            <td>2</td>
            <td>Rp10.000</td>
        </tr>
        <tr>
            <td>Pulpen</td>
            <td>3</td>
            <td>Rp6.000</td>
        </tr>
    </table>

    <p class="total">Total: Rp16.000</p>

    <p class="center">Terima kasih telah berbelanja!</p>

    <div class="center">
        <button onclick="window.print()">Cetak Invoice</button>
    </div>
</div>
</body>
</html>
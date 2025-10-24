<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $reportTitle }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; }
        .table th { background-color: #f2f2f2; text-align: left; }
        .text-right { text-align: right; }
        h1 { text-align: center; margin-bottom: 0; }
        .summary-table { width: 100%; margin-bottom: 20px; }
        .summary-table td { padding: 5px; }
        .summary-label { font-weight: bold; }
    </style>
</head>
<body>
    <h1>{{ $reportTitle }}</h1>
    
    {{-- [PERUBAHAN] Tabel ringkasan baru --}}
    <table class="summary-table">
        <tr>
            <td class="summary-label">Total Pemasukan:</td>
            <td>Rp{{ number_format($totalRevenue, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="summary-label">Total Transaksi Penjualan:</td>
            <td>{{ $transactionCount }} Transaksi</td>
        </tr>
        <tr>
            <td class="summary-label">Total Trade-In:</td>
            <td>{{ $tradeInCount }} Transaksi</td>
        </tr>
    </table>
    <hr>

    <table class="table">
        <thead>
            <tr>
                <th>ID Transaksi</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Metode</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($filteredTransactions as $trx)
                <tr>
                    <td>{{ $trx->id }}</td>
                    <td>{{ $trx->date->format('d M Y, H:i') }}</td>
                    <td>{{ $trx->customer }}</td>
                    <td>{{ $trx->method }}</td>
                    <td class="text-right">Rp{{ number_format($trx->total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Tidak ada data penjualan untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
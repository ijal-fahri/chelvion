<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Invoice Checkout #{{ $checkout->id }}</title>
  <style>
    body {
      font-family: 'Arial', sans-serif;
      color: #333;
      margin: 40px;
      font-size: 13px;
      line-height: 1.6;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 2px solid #333;
      padding-bottom: 10px;
      margin-bottom: 25px;
    }

    .header h2 {
      margin: 0;
      color: #2c3e50;
      font-size: 20px;
    }

    .info {
      margin-bottom: 25px;
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      background: #f9f9f9;
      padding: 12px 20px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    .info-left, .info-right {
      width: 48%;
      min-width: 200px;
    }

    .info p {
      margin: 6px 0;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 12px;
    }

    th, td {
      border: 1px solid #444;
      padding: 8px 10px;
      text-align: center;
      vertical-align: middle;
    }

    th {
      background: #f1f1f1;
    }

    tbody tr:nth-child(even) {
      background: #fafafa;
    }

    .total {
      margin-top: 20px;
      text-align: right;
      font-size: 15px;
      font-weight: bold;
      padding-top: 8px;
      border-top: 2px solid #555;
    }

    .footer {
      text-align: center;
      margin-top: 35px;
      font-size: 11px;
      color: #777;
    }
  </style>
</head>
<body>

  <div class="header">
    <h2>Invoice Checkout #{{ $checkout->id }}</h2>
    <span>{{ $checkout->created_at->format('d-m-Y H:i') }}</span>
  </div>

  <div class="info">
    <div class="info-left">
      <p><strong>Nama Pembeli:</strong> {{ $checkout->receiver_name }}</p>
      <p><strong>Metode Pembayaran:</strong> {{ ucfirst($checkout->payment_method) }}</p>
      <p><strong>Alamat:</strong> {{ $checkout->address }}</p>
      <p><strong>Kota:</strong> {{ $checkout->city }}</p>
    </div>
    <div class="info-right">
      <p><strong>Pengambilan:</strong>
        @if ($checkout->delivery_method === 'antar')
          Diantar ke Alamat
        @else
          Ambil di Toko - {{ $checkout->pickup_location }}
        @endif
      </p>
      <p><strong>Status:</strong> {{ ucfirst($checkout->status) }}</p>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th style="width: 5%;">No</th>
        <th style="width: 25%;">Produk</th>
        <th style="width: 15%;">Warna</th>
        <th style="width: 10%;">RAM</th>
        <th style="width: 15%;">Harga</th>
        <th style="width: 10%;">Qty</th>
        <th style="width: 20%;">Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($checkout->items as $item)
      <tr>
        <td>{{ $loop->iteration }}</td>
        <td style="text-align: left;">{{ $item->product->name }}</td>
        <td>{{ $item->color }}</td>
        <td>{{ $item->ram }} GB</td>
        <td>Rp{{ number_format($item->price, 0, ',', '.') }}</td>
        <td>{{ $item->quantity }}</td>
        <td>Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <div class="total">
    Total Bayar: Rp{{ number_format($checkout->total_price, 0, ',', '.') }}
  </div>

  <div class="footer">
    <p>Toko Gadget 2025.</p>
  </div>

</body>
</html>

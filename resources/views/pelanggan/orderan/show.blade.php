<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Pesanan {{ $order->order_number }} - CELVION</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        body { background-color: #f8fafc; }
        .card-custom { border: none; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .btn-purple { background-color: #4f46e5; border-color: #4f46e5; color: white; }
        .btn-purple:hover { background-color: #4338ca; border-color: #4338ca; }
        .btn-outline-purple { color: #4f46e5; border-color: #4f46e5; }
        .btn-outline-purple:hover { background-color: #4f46e5; color: white; }
        .status-badge { font-size: 0.75rem; padding: 0.35rem 0.75rem; }
        .status-selesai { background-color: #10b981 !important; color: white !important; }
        .status-diproses, .status-dikirim, .status-menunggu-diambil { background-color: #3b82f6 !important; color: white !important; }
        .status-dibatalkan { background-color: #ef4444 !important; color: white !important; }
        .product-img { width: 80px; height: 80px; object-fit: cover; border-radius: 0.5rem; }
        .harga-ungu { color: #4f46e5 !important; }
        .detail-item { display: flex; justify-content: space-between; margin-bottom: 0.5rem; }
    </style>
</head>

<body>
    @include('components.navbar-pelanggan')

    <div class="container my-5">
        
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong>Berhasil!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="card card-custom mb-4">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="h4 fw-bold mb-0">Detail Pesanan</h2>
                            <span class="badge status-badge status-{{ \Illuminate\Support\Str::slug($order->status) }}">{{ $order->status }}</span>
                        </div>

                        <div class="mb-4">
                            <h5 class="h6 fw-bold">Pengiriman & Penerima</h5>
                            @if ($order->delivery_method == 'antar')
                                <p class="mb-1 text-dark fw-semibold">{{ $order->receiver_name }}</p>
                                <p class="text-muted small mb-1">{{ $order->phone_number }}</p>
                                <p class="text-muted small mb-0">
                                    {{ $order->full_address }}, {{ $order->kecamatan }}, {{ $order->city }}, {{ $order->province }}
                                </p>
                                <p class="text-muted small mt-2">Dikirim dari: <strong>{{ $order->shippingCabang->nama_cabang ?? 'Gudang Pusat' }}</strong></p>
                            @else
                                <p class="mb-1 text-dark fw-semibold">Ambil di Toko</p>
                                <p class="text-muted small mb-0">
                                    Lokasi: <strong>{{ $order->pickupCabang->nama_cabang ?? 'Gudang Pusat' }}</strong><br>
                                    ({{ $order->pickupCabang->alamat ?? '' }})
                                </p>
                            @endif
                        </div>

                        <hr class="my-4">

                        <h5 class="h6 fw-bold mb-3">Produk yang Dipesan</h5>
                        <div class="vstack gap-4">
                            @foreach ($order->items as $item)
                                <div class="row g-3">
                                    <div class="col-auto">
                                        <img src="{{ optional($item->variant)->first_image_url ?? 'https://placehold.co/100x100/eef2ff/4f46e5?text=N/A' }}"
                                            alt="{{ $item->product_name }}" class="product-img">
                                    </div>
                                    <div class="col">
                                        <h6 class="fw-bold fs-6 mb-1">{{ $item->product_name }}</h6>
                                        <p class="text-muted small mb-1">{{ $item->variant_info }}</p>
                                        <p class="text-muted small mb-1">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="col-12 col-md text-md-end">
                                        <h6 class="fw-semibold harga-ungu mb-0">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</h6>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-custom sticky-top" style="top: 2rem;">
                    <div class="card-body p-4 p-md-5">
                        <h4 class="h5 fw-bold mb-4">Ringkasan Pembayaran</h4>
                        @php
                            // Hitung subtotal murni dari item
                            $subtotal = $order->items->sum(fn($item) => $item->price * $item->quantity);
                        @endphp
                        <div class="vstack gap-2">
                            <div class="detail-item text-muted">
                                <span>Subtotal</span>
                                <span class="fw-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="detail-item text-muted">
                                <span>Pengiriman</span>
                                <span class="fw-semibold">Gratis</span>
                            </div>

                            @if ($order->discount_amount > 0)
                                <div class="detail-item text-success">
                                    <span>Diskon ({{ $order->voucher_code }})</span>
                                    <span class="fw-semibold">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif

                            <hr class="my-2">
                            
                            <div class="detail-item fw-bold fs-5">
                                <span class="text-dark">Total Bayar</span>
                                <span class="harga-ungu">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                            </div>

                            <div class="detail-item text-muted border-top pt-3 mt-2">
                                <span>Metode Pembayaran</span>
                                <span class="fw-semibold">{{ $order->payment_method }}</span>
                            </div>
                        </div>

                        <a href="{{ route('orderan.index') }}" class="btn btn-outline-purple w-100 mt-4">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat Pesanan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.footer-pelanggan')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
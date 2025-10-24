<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pesanan Saya - CELVION</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f8fafc;
        }

        .card-custom {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn-purple {
            background-color: #4f46e5;
            border-color: #4f46e5;
            color: white;
        }

        .btn-purple:hover {
            background-color: #4338ca;
            border-color: #4338ca;
        }

        .btn-outline-purple {
            color: #4f46e5;
            border-color: #4f46e5;
        }

        .btn-outline-purple:hover {
            background-color: #4f46e5;
            color: white;
        }

        .search-purple:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.35rem 0.75rem;
        }

        .status-selesai {
            background-color: #10b981 !important;
            color: white !important;
        }

        .status-diproses,
        .status-dikirim,
        .status-menunggu-diambil {
            background-color: #3b82f6 !important;
            color: white !important;
        }

        .status-dibatalkan {
            background-color: #ef4444 !important;
            color: white !important;
        }

        .stat-card {
            border-left: 4px solid #4f46e5;
        }

        .stat-card.success {
            border-left-color: #10b981;
        }

        .stat-card.secondary {
            border-left-color: #64748b;
        }

        .order-id {
            background-color: #eef2ff;
            color: #4f46e5;
        }

        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 0.5rem;
        }

        .harga-ungu {
            color: #4f46e5 !important;
        }

        .pagination .page-item.active .page-link {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }

        .pagination .page-link {
            color: #4f46e5;
        }

        .modal-header,
        .modal-footer {
            border-color: #eef2ff;
            padding: 1.25rem;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-title {
            font-weight: 600;
        }

        .detail-section {
            margin-bottom: 1.5rem;
        }

        .detail-section h6 {
            font-weight: 600;
            margin-bottom: 0.75rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .rating-stars .bi-star,
        .rating-stars .bi-star-fill {
            font-size: 1.5rem;
            color: #d1d5db;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .rating-stars .bi-star:hover,
        .rating-stars .bi-star-fill {
            color: #f59e0b;
        }

        .rating-stars .bi-star-fill.rated {
            color: #f59e0b;
        }
    </style>
</head>

<body>
    @include('components.navbar-pelanggan')

    <div class="container my-5">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h2 fw-bold text-dark mb-2">Pesanan Saya</h1>
                <p class="text-muted">Kelola semua riwayat pesanan Anda di sini.</p>
            </div>
        </div>

        <form id="filterForm" action="{{ route('orderan.index') }}" method="GET">
            <div class="card card-custom mb-4">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cari Pesanan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0"><i
                                        class="bi bi-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control search-purple border-start-0"
                                    placeholder="Cari ID pesanan atau produk..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select search-purple">
                                <option value="">Semua Status</option>
                                <option value="Diproses" @selected(request('status') == 'Diproses')>Diproses</option>
                                <option value="Dikirim" @selected(request('status') == 'Dikirim')>Dikirim</option>
                                <option value="Selesai" @selected(request('status') == 'Selesai')>Selesai</option>
                                <option value="Dibatalkan" @selected(request('status') == 'Dibatalkan')>Dibatalkan</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Tanggal</label>
                            <select name="date" class="form-select search-purple">
                                <option value="">Semua Waktu</option>
                                <option value="7days" @selected(request('date') == '7days')>7 Hari Terakhir</option>
                                <option value="30days" @selected(request('date') == '30days')>30 Hari Terakhir</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="row mb-4">
            <div class="col-md-4 col-12 mb-3">
                <div class="card card-custom stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Total Pesanan</p>
                                <h3 class="fw-bold text-dark mb-0">{{ $totalPesanan }}</h3>
                            </div>
                            <i class="bi bi-journal-text text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-6 mb-3">
                <div class="card card-custom stat-card success h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Dalam Proses/Pengiriman</p>
                                <h3 class="fw-bold text-dark mb-0">{{ $dalamPengiriman }}</h3>
                            </div>
                            <i class="bi bi-truck text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-6 mb-3">
                <div class="card card-custom stat-card secondary h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Selesai</p>
                                <h3 class="fw-bold text-dark mb-0">{{ $selesai }}</h3>
                            </div>
                            <i class="bi bi-check-circle text-secondary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                @forelse ($orders as $order)
                    <div class="card card-custom mb-4">
                        <div class="card-header bg-white border-bottom p-3">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                        <span
                                            class="badge order-id status-badge fw-bold">#{{ $order->order_number }}</span>
                                        <span
                                            class="badge status-badge status-{{ \Illuminate\Support\Str::slug($order->status) }}">{{ $order->status }}</span>
                                    </div>
                                    <small class="text-muted">Dibuat pada:
                                        {{ $order->created_at->format('d M Y, H:i') }}</small>
                                </div>
                                <div class="col-md-4 text-md-end mt-2 mt-md-0">
                                    <h5 class="harga-ungu fw-bold mb-1">Rp
                                        {{ number_format($order->total_price, 0, ',', '.') }}</h5>
                                    <small class="text-muted">{{ $order->items->count() }} Produk</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            @foreach ($order->items as $item)
                                <div
                                    class="row align-items-center @if (!$loop->last || $order->status == 'Selesai') mb-3 pb-3 border-bottom @endif">
                                    <div class="col-md-8 col-9">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ optional($item->variant)->first_image_url ?? 'https://placehold.co/100x100/eef2ff/4f46e5?text=N/A' }}"
                                                alt="{{ $item->product_name }}" class="product-img me-3">
                                            <div>
                                                <h6 class="fw-bold mb-1 fs-6">{{ $item->product_name }}</h6>
                                                <p class="text-muted small mb-1">{{ $item->variant_info }}</p>
                                                <p class="text-muted small mb-0">Qty: {{ $item->quantity }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-3 text-end">
                                        <h6 class="fw-semibold harga-ungu">Rp
                                            {{ number_format($item->price, 0, ',', '.') }}</h6>
                                    </div>
                                </div>
                                @if ($order->status == 'Selesai')
                                    <div
                                        class="text-md-end @if (!$loop->last) mb-3 pb-3 border-bottom @endif">
                                        @if ($item->review)
                                            <button class="btn btn-outline-success btn-sm disabled"><i
                                                    class="bi bi-check-circle-fill me-1"></i> Sudah Diulas</button>
                                        @else
                                            <button class="btn btn-outline-purple btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#beriUlasanModal"
                                                data-item='@json($item)'
                                                data-order-date='{{ $order->created_at }}'>
                                                <i class="bi bi-chat-left-text me-1"></i>Beri Ulasan
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            @endforeach

                            <div
                                class="row align-items-center pt-3 @if ($order->status == 'Selesai') mt-0 @else mt-3 @endif">
                                <div class="col-12">
                                    <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                                        <button class="btn btn-outline-purple btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#detailPesananModal"
                                            data-order='@json($order)'>
                                            <i class="bi bi-eye me-1"></i>Detail Pesanan
                                        </button>
                                        @if ($order->status == 'Selesai')
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card card-custom">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-search fs-1 text-muted"></i>
                            <h4 class="mt-3 fw-semibold">Tidak Ada Pesanan Ditemukan</h4>
                            <p class="text-muted">Coba ubah kata kunci pencarian atau filter Anda.</p>
                            <a href="{{ route('orderan.index') }}" class="btn btn-purple mt-3 fw-semibold">
                                <i class="bi bi-arrow-left"></i> Reset Filter
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $orders->links() }}
        </div>
    </div>

    @include('components.footer-pelanggan')

    <div class="modal fade" id="detailPesananModal" tabindex="-1" aria-labelledby="detailPesananModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailPesananModalLabel">Detail Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="detail-section">
                        <h6>Status Pesanan</h6>
                        <span class="badge status-badge" id="modal-order-status"></span>
                    </div>
                    <div class="detail-section">
                        <h6>Informasi Penerima</h6>
                        <div id="modal-shipping-address" class="text-muted"></div>
                    </div>
                    <div class="detail-section">
                        <h6>Detail Produk</h6>
                        <div id="modal-item-list"></div>
                    </div>
                    <div class="detail-section">
                        <h6>Ringkasan Pembayaran</h6>
                        <div class="detail-item">
                            <span class="label">Metode Pembayaran</span>
                            <span class="value" id="modal-payment-method">-</span>
                        </div>
                        <hr class="my-2">
                        <div class="detail-item">
                            <span class="label">Subtotal</span>
                            <span class="value" id="modal-subtotal">Rp 0</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Biaya Pengiriman</span>
                            <span class="value" id="modal-shipping-cost">Rp 0</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Diskon</span>
                            <span class="value text-success" id="modal-discount">- Rp 0</span>
                        </div>
                        <div class="detail-item fw-bold pt-2 border-top">
                            <span class="label">Total Pembayaran</span>
                            <span class="value harga-ungu" id="modal-total">Rp 0</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-purple" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="beriUlasanModal" tabindex="-1" aria-labelledby="beriUlasanModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('reviews.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="order_item_id" id="review-order-item-id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="beriUlasanModalLabel">Beri Ulasan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="detail-section">
                            <h6>Produk yang Diulas</h6>
                            <div class="d-flex align-items-center">
                                <img src="" id="review-product-image" alt="Produk"
                                    class="product-img me-3">
                                <div>
                                    <h6 class="fw-bold mb-1" id="review-product-name">Nama Produk</h6>
                                    <p class="text-muted small mb-0">Dibeli pada: <span
                                            id="review-purchase-date"></span></p>
                                </div>
                            </div>
                        </div>
                        <div class="detail-section">
                            <h6>Rating Produk</h6>
                            <div class="rating-stars" id="ratingStars">
                                <i class="bi bi-star rating-star" data-rating="1"></i>
                                <i class="bi bi-star rating-star" data-rating="2"></i>
                                <i class="bi bi-star rating-star" data-rating="3"></i>
                                <i class="bi bi-star rating-star" data-rating="4"></i>
                                <i class="bi bi-star rating-star" data-rating="5"></i>
                            </div>
                            <input type="hidden" name="rating" id="rating-value" value="" required>
                            <small class="text-danger d-none" id="rating-error">Rating wajib diisi.</small>
                        </div>
                        <div class="detail-section">
                            <h6>Ulasan</h6>
                            <textarea name="comment" class="form-control" rows="4"
                                placeholder="Bagikan pengalaman Anda menggunakan produk ini..."></textarea>
                        </div>
                        <div class="detail-section">
                            <h6>Unggah Foto (Opsional)</h6>
                            <input type="file" name="images[]" class="form-control" multiple accept="image/*"
                                id="review-images">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-purple" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-purple">Kirim Ulasan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Helper Functions ---
            const formatCurrency = (amount) => new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);

            // --- Logika untuk Filter ---
            const filterForm = document.getElementById('filterForm');
            const selects = filterForm.querySelectorAll('select');
            const searchInput = filterForm.querySelector('input[type="text"]');
            let searchTimeout;

            selects.forEach(select => {
                select.addEventListener('change', () => filterForm.submit());
            });

            searchInput.addEventListener('keyup', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => filterForm.submit(), 500);
            });

            // --- Event Listener untuk Modal Detail Pesanan ---
            const detailModalEl = document.getElementById('detailPesananModal');
            if (detailModalEl) {
                detailModalEl.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const modal = this;
                    try {
                        const order = JSON.parse(button.getAttribute('data-order'));
                        modal.querySelector('#detailPesananModalLabel').textContent =
                            `Detail Pesanan #${order.order_number}`;
                        const statusBadge = modal.querySelector('#modal-order-status');
                        statusBadge.textContent = order.status;
                        statusBadge.className =
                            `badge status-badge status-${order.status.toLowerCase().replace(/[^a-z0-9]/g, '')}`;

                        const shippingAddressEl = modal.querySelector('#modal-shipping-address');
                        if (order.delivery_method === 'antar') {
                            const address = [order.full_address, order.kecamatan, order.city, order
                                .province].filter(Boolean).join(', ');
                            shippingAddressEl.innerHTML =
                                `<span class="fw-semibold">${order.receiver_name}</span><br>${order.phone_number}<br>${address}`;
                        } else {
                            shippingAddressEl.innerHTML =
                                `<span class="fw-semibold">Pesanan Diambil di Toko</span>`;
                        }

                        const itemsContainer = modal.querySelector('#modal-item-list');
                        itemsContainer.innerHTML = '';
                        let subtotal = 0;
                        order.items.forEach(item => {
                            subtotal += parseFloat(item.price) * item.quantity;
                            const imageUrl = item.variant?.first_image_url ||
                                'https://placehold.co/100x100/eef2ff/4f46e5?text=N/A';
                            itemsContainer.innerHTML += `
                                <div class="row align-items-center mb-3">
                                    <div class="col-8"><div class="d-flex align-items-center">
                                        <img src="${imageUrl}" alt="${item.product_name}" class="product-img me-3">
                                        <div>
                                            <h6 class="fw-bold mb-1" style="font-size: 0.9rem;">${item.product_name}</h6>
                                            <p class="text-muted small mb-1">${item.variant_info || ''}</p>
                                            <p class="text-muted small mb-0">Qty: ${item.quantity}</p>
                                        </div>
                                    </div></div>
                                    <div class="col-4 text-end"><h6 class="fw-bold harga-ungu" style="font-size: 0.9rem;">${formatCurrency(item.price)}</h6></div>
                                </div>`;
                        });

                        const shippingCost = (parseFloat(order.total_price) + parseFloat(order
                            .discount_amount)) - subtotal;
                        modal.querySelector('#modal-payment-method').textContent = order.payment_method;
                        modal.querySelector('#modal-subtotal').textContent = formatCurrency(subtotal);
                        modal.querySelector('#modal-shipping-cost').textContent = formatCurrency(
                            shippingCost > 0 ? shippingCost : 0);
                        modal.querySelector('#modal-discount').textContent =
                            `- ${formatCurrency(order.discount_amount)}`;
                        modal.querySelector('#modal-total').textContent = formatCurrency(order.total_price);
                    } catch (e) {
                        console.error("Gagal memproses data pesanan:", e);
                        modal.querySelector('.modal-body').textContent = "Gagal memuat detail pesanan.";
                    }
                });
            }

            // --- Event Listener untuk Modal Beri Ulasan ---
            const ulasanModalEl = document.getElementById('beriUlasanModal');
            if (ulasanModalEl) {
                ulasanModalEl.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const item = JSON.parse(button.getAttribute('data-item'));
                    const orderDate = button.getAttribute('data-order-date');
                    const modal = this;

                    modal.querySelector('#review-product-image').src = item.variant?.first_image_url ||
                        'https://placehold.co/100x100';
                    modal.querySelector('#review-product-name').textContent = item.product_name;
                    modal.querySelector('#review-purchase-date').textContent = new Date(orderDate)
                        .toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        });
                    modal.querySelector('#review-order-item-id').value = item.id;

                    // Reset form ulasan setiap kali modal dibuka
                    modal.querySelector('form').reset();
                    const ratingValueInput = modal.querySelector('#rating-value');
                    ratingValueInput.value = '';
                    modal.querySelectorAll('.rating-star').forEach(s => {
                        s.classList.remove('bi-star-fill', 'text-warning', 'rated');
                        s.classList.add('bi-star');
                    });
                });

                // Logika untuk rating bintang
                const ratingStars = ulasanModalEl.querySelectorAll('.rating-star');
                const ratingValueInput = ulasanModalEl.querySelector('#rating-value');
                const ratingError = ulasanModalEl.querySelector('#rating-error');

                ratingStars.forEach(star => {
                    star.addEventListener('click', function() {
                        const rating = this.dataset.rating;
                        ratingValueInput.value = rating;
                        ratingError.classList.add('d-none');
                        ratingStars.forEach(s => {
                            s.classList.toggle('bi-star-fill', s.dataset.rating <= rating);
                            s.classList.toggle('bi-star', s.dataset.rating > rating);
                            s.classList.toggle('text-warning', s.dataset.rating <= rating);
                            s.classList.toggle('rated', s.dataset.rating <= rating);
                        });
                    });
                });

                // Validasi rating sebelum submit
                ulasanModalEl.querySelector('form').addEventListener('submit', function(e) {
                    if (!ratingValueInput.value) {
                        e.preventDefault(); // Hentikan pengiriman form
                        ratingError.classList.remove('d-none');
                        Swal.fire('Error', 'Rating bintang wajib diisi.', 'error');
                    }
                });
            }
        });
    </script>
</body>

</html>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>CELVION - Inovasi dalam Genggaman Anda</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #eef2ff;
            --secondary: #64748b;
            --light: #f8f9fa;
            --dark: #1e293b;
            --body-bg: #f8fafc;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --success: #10b981;
            --warning: #f59e0b;
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--body-bg);
            color: var(--dark);
        }

        /* --- Hero Section --- */
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 1.5rem;
            padding: 4rem 2rem;
            margin-bottom: 3rem;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000" opacity="0.1"><polygon fill="white" points="0,1000 1000,0 1000,1000"/></svg>');
            background-size: cover;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        /* --- Voucher Carousel (DIPERBAIKI) --- */
        .voucher-carousel {
            margin-bottom: 3rem;
            position: relative;
        }

        .voucher-container {
            display: flex;
            gap: 1.5rem;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding: 1rem 0.5rem 1.5rem;
            scrollbar-width: none;
            -ms-overflow-style: none;
            -webkit-overflow-scrolling: touch;
            cursor: grab;
        }

        .voucher-container::-webkit-scrollbar {
            display: none;
        }

        .voucher-container:active {
            cursor: grabbing;
        }

        .voucher-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 1rem;
            padding: 1.8rem;
            color: white;

            /* === PERUBAHAN DI SINI === */
            /* Ukuran dasar untuk (Large) screen */
            min-width: 440px;

            flex: 0 0 auto;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            height: 220px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .voucher-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
        }

        .voucher-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 120px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(45deg);
        }

        .voucher-card.premium {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .voucher-card.success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .voucher-card.danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .voucher-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .voucher-discount {
            font-size: 2.8rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .voucher-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .voucher-description {
            font-size: 0.9rem;
            opacity: 0.95;
            margin-bottom: 1rem;
            line-height: 1.4;
            flex: 1;
        }

        .voucher-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
            gap: 0.5rem;
        }

        .voucher-code {
            background: rgba(255, 255, 255, 0.25);
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-family: 'Courier New', monospace;
            font-weight: 700;
            font-size: 0.9rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            flex-shrink: 0;
        }

        .voucher-claim-btn {
            background: white;
            color: var(--primary);
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .voucher-claim-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            color: var(--primary);
        }

        .voucher-expiry {
            font-size: 0.75rem;
            opacity: 0.9;
            text-align: center;
            font-style: italic;
            margin-top: 0.5rem;
            width: 100%;
        }

        /* Navigation buttons */
        .carousel-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 45px;
            height: 45px;
            background: var(--primary);
            border: none;
            border-radius: 50%;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10;
            opacity: 0.9;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .carousel-nav:hover {
            opacity: 1;
            transform: translateY(-50%) scale(1.1);
        }

        .carousel-nav.prev {
            left: -20px;
        }

        .carousel-nav.next {
            right: -20px;
        }

        /* --- Category Cards --- */
        .category-card {
            background: white;
            border-radius: 1rem;
            padding: 2rem 1rem;
            text-align: center;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            text-decoration: none;
            color: var(--dark);
        }

        .category-card:hover {
            border-color: var(--primary);
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            color: var(--dark);
        }

        .category-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
            background: var(--primary-light);
            color: var(--primary);
        }

        /* --- Product Cards --- */
        .product-card {
            background: var(--card-bg);
            border-radius: 1rem;
            border: 1px solid var(--border-color);
            transition: transform .3s ease, box-shadow .3s ease;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 1rem 2rem rgba(30, 41, 59, 0.1);
        }

        .product-card .img-wrapper {
            height: 200px;
            padding: 1.25rem;
            background-color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-card .product-img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            transition: transform .3s ease;
        }

        .product-card:hover .product-img {
            transform: scale(1.05);
        }

        .product-card .card-body {
            padding: 1rem 1.25rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .product-card .product-category {
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--primary);
            background-color: var(--primary-light);
            padding: 0.2rem 0.6rem;
            border-radius: 6px;
        }

        .product-card .product-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark);
            line-height: 1.4;
        }

        .product-card .product-rating {
            color: var(--warning);
            font-size: 0.9rem;
        }

        .product-card .product-price {
            font-size: 1.5rem;
            color: var(--primary);
            font-weight: 700;
        }

        .discount-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: var(--success);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* --- Section Headers --- */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: end;
            margin-bottom: 2rem;
            border-bottom: 2px solid var(--primary-light);
            padding-bottom: 1rem;
        }

        .section-title {
            font-weight: 700;
            color: var(--dark);
            margin: 0;
            font-size: 1.75rem;
        }

        /* --- Responsive --- */

        /* === TAMBAHAN BARU DI SINI === */
        /* Untuk layar (Extra Large) */
        @media (min-width: 1200px) {
            .voucher-card {
                min-width: 530px;
                /* 2 kartu akan mengisi ~1084px */
            }
        }

        /* Untuk layar (Extra Extra Large) */
        @media (min-width: 1400px) {
            .voucher-card {
                min-width: 620px;
                /* 2 kartu akan mengisi ~1264px */
            }
        }

        /* === AKHIR TAMBAHAN === */


        @media (max-width: 768px) {
            .hero-section {
                padding: 2rem 1rem;
                text-align: center;
            }

            .section-header {
                flex-direction: column;
                align-items: start;
                gap: 1rem;
            }

            .carousel-nav.prev {
                left: 5px;
            }

            .carousel-nav.next {
                right: 5px;
            }

            .carousel-nav {
                width: 40px;
                height: 40px;
            }

            .voucher-card {
                /* Mengubah min-width di mobile agar tidak terlalu besar */
                min-width: 280px;
                height: 210px;
                padding: 1.5rem;
            }

            .voucher-discount {
                font-size: 2.4rem;
            }

            .voucher-title {
                font-size: 1.2rem;
            }

            .voucher-container {
                cursor: default;
            }
        }

        @media (max-width: 576px) {
            .voucher-card {
                min-width: 260px;
                padding: 1.2rem;
            }

            .voucher-footer {
                flex-direction: column;
                gap: 0.8rem;
                align-items: stretch;
            }

            .voucher-claim-btn {
                margin-left: 0;
                width: 100%;
            }

            .voucher-code {
                text-align: center;
            }
        }

        .btn-outline-purple {
            color: #4f46e5;
            border: 1px solid #4f46e5;
            background-color: transparent;
            transition: all 0.2s ease;
        }

        .btn-outline-purple:hover,
        .btn-outline-purple:focus,
        .btn-outline-purple.active {
            background-color: #4f46e5;
            color: #fff;
        }
    </style>
</head>

<body>
    <!-- Include Navbar Component -->
    @include('components.navbar-pelanggan', [
        'orderCount' => $orderCount ?? 0,
        'wishlistCount' => $wishlistCount ?? 0,
        'cartCount' => $cartCount ?? 0,
    ])

    <div class="container my-4">
        <!-- Hero Section -->
        <section class="hero-section text-white">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <h1 class="display-5 fw-bold mb-3">Teknologi Terbaru, Harga Terbaik</h1>
                    <p class="lead mb-4 opacity-90">Temukan smartphone, laptop, dan aksesoris original dengan garansi
                        resmi. Dapatkan penawaran eksklusif hanya di CELVION.</p>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80"
                        alt="Smartphones" class="img-fluid rounded-3" style="max-height: 300px;">
                </div>
            </div>
        </section>

        <!-- Voucher Carousel (DIPERBAIKI) -->
        <section class="voucher-carousel">
            <div class="section-header">
                <h2 class="section-title">Voucher Spesial</h2>
            </div>

            <div class="position-relative">
                <button class="carousel-nav prev">
                    <i class="bi bi-chevron-left"></i>
                </button>

                <div class="voucher-container" id="voucherContainer">
                    @php
                        // Array kelas warna untuk di-cycle
                        $voucherColors = ['', 'premium', 'success', 'danger'];
                    @endphp

                    @forelse ($vouchers as $voucher)
                        @php
                            // Tentukan kelas warna berdasarkan index loop
                            $colorClass = $voucherColors[$loop->index % count($voucherColors)];
                        @endphp

                        <div class="voucher-card {{ $colorClass }}">
                            <div class="voucher-content">

                                {{-- DIV INI (dengan flex: 1) akan mendorong tanggal ke bawah --}}
                                <div style="flex: 1;">
                                    {{-- Tampilkan nilai diskon berdasarkan tipe --}}
                                    @if ($voucher->type == 'percentage')
                                        <div class="voucher-discount">{{ (int) $voucher->discount_percentage }}%</div>
                                    @elseif ($voucher->type == 'fixed')
                                        {{-- Beri style sedikit agar Rp muat jika angkanya besar --}}
                                        <div class="voucher-discount" style="font-size: 2.4rem; line-height: 1.1;">
                                            Rp{{ number_format($voucher->discount_amount, 0, ',', '.') }}
                                        </div>
                                    @else
                                        {{-- Fallback jika tipe tidak diset --}}
                                        <div class="voucher-discount">Info</div>
                                    @endif

                                    <h3 class="voucher-title">{{ $voucher->name }}</h3>

                                    @if ($voucher->description)
                                        <p class="voucher-description">{{ $voucher->description }}</p>
                                    @endif
                                </div>

                                {{-- Tampilkan tanggal kadaluarsa --}}
                                @if ($voucher->expiry_date)
                                    <p class="voucher-expiry">
                                        Berlaku hingga {{ $voucher->expiry_date->format('d M Y') }}
                                    </p>
                                @else
                                    <p class="voucher-expiry">Berlaku Seterusnya</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        {{-- Tampilan jika tidak ada voucher --}}
                        <div classs="w-100 text-center py-5">
                            <h5 class="text-muted">Belum ada voucher spesial saat ini.</h5>
                        </div>
                    @endforelse
                </div>

                <button class="carousel-nav next">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </section>

        <!-- Featured Products -->
        <section id="products" class="mb-5">
            <div class="section-header">
                <h2 class="section-title">Produk Terpopuler</h2>
            </div>

            <div class="row g-4">
                @forelse ($popularProducts as $product)
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <a href="{{ route('products.show', $product['id']) }}" class="text-decoration-none">
                            <div class="product-card h-100">
                                <div class="img-wrapper">
                                    <img src="{{ $product['images'][0] }}" class="product-img"
                                        alt="{{ $product['name'] }}"
                                        onerror="this.onerror=null;this.src='https://placehold.co/400x400/eef2ff/4f46e5?text=N/A';">
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="product-category">{{ $product['category'] ?? 'Lainnya' }}</span>
                                        @if ($product['condition'] == 'Baru')
                                            <span class="badge bg-success">Baru</span>
                                        @else
                                            <span class="badge bg-warning">Second</span>
                                        @endif
                                    </div>
                                    <h5 class="product-title mb-2">{{ $product['name'] }}</h5>

                                    <div class="mt-auto pt-2">
                                        <p class="product-price mb-3">
                                            Rp{{ number_format($product['master_price'] ?: $product['display_price'], 0, ',', '.') }}
                                        </p>
                                        <div class="btn btn-outline-purple w-100 fw-semibold">
                                            <i class="bi bi-eye-fill"></i> Lihat Detail
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-box-seam display-1 text-muted"></i>
                        <h4 class="mt-3 text-muted">Belum ada produk populer</h4>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="mb-5">
            <div class="section-header">
                <h2 class="section-title">Produk Terbaru</h2>
            </div>
            <div class="row g-4">
                @forelse ($newProducts as $product)
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <a href="{{ route('products.show', $product['id']) }}" class="text-decoration-none">
                            <div class="product-card h-100">
                                <div class="img-wrapper">
                                    <img src="{{ $product['images'][0] }}" class="product-img"
                                        alt="{{ $product['name'] }}"
                                        onerror="this.onerror=null;this.src='https://placehold.co/400x400/eef2ff/4f46e5?text=N/A';">
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="product-category">{{ $product['category'] ?? 'Lainnya' }}</span>
                                        <span class="badge bg-success">Baru</span>
                                    </div>
                                    <h5 class="product-title mb-2">{{ $product['name'] }}</h5>

                                    <div class="mt-auto pt-2">
                                        <p class="product-price mb-3">
                                            Rp{{ number_format($product['display_price'], 0, ',', '.') }}</p>
                                        <div class="btn btn-outline-purple w-100 fw-semibold">
                                            <i class="bi bi-eye-fill"></i> Lihat Detail
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-box-seam display-1 text-muted"></i>
                        <h4 class="mt-3 text-muted">Belum ada produk baru</h4>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Second Arrivals -->
        <section class="mb-5">
            <div class="section-header">
                <h2 class="section-title">Produk Second</h2>
            </div>
            <div class="row g-4">
                @forelse ($secondProducts as $product)
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <a href="{{ route('products.show', $product['id']) }}" class="text-decoration-none">
                            <div class="product-card h-100">
                                <div class="img-wrapper">
                                    <img src="{{ $product['images'][0] }}" class="product-img"
                                        alt="{{ $product['name'] }}"
                                        onerror="this.onerror=null;this.src='https://placehold.co/400x400/eef2ff/4f46e5?text=N/A';">
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="product-category">{{ $product['category'] ?? 'Lainnya' }}</span>
                                        <span class="badge bg-warning">Second</span>
                                    </div>
                                    <h5 class="product-title mb-2">{{ $product['name'] }}</h5>

                                    <div class="mt-auto pt-2">
                                        <p class="product-price mb-3">
                                            Rp{{ number_format($product['display_price'], 0, ',', '.') }}</p>
                                        <div class="btn btn-outline-purple w-100 fw-semibold">
                                            <i class="bi bi-eye-fill"></i> Lihat Detail
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-box-seam display-1 text-muted"></i>
                        <h4 class="mt-3 text-muted">Belum ada produk second</h4>
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    @include('components.footer-pelanggan')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Voucher carousel functionality (DIPERBAIKI: Fixed auto-scroll bug)
        const voucherContainer = document.getElementById('voucherContainer');
        const prevBtn = document.querySelector('.carousel-nav.prev');
        const nextBtn = document.querySelector('.carousel-nav.next');

        // Get card width dynamically
        const getCardWidth = () => {
            const firstCard = voucherContainer.querySelector('.voucher-card');
            return firstCard ? firstCard.offsetWidth : 320;
        };

        // Get gap width
        const getGapWidth = () => {
            const style = window.getComputedStyle(voucherContainer);
            return parseInt(style.gap) || 24;
        };

        // Calculate scroll amount (2 cards at a time)
        const getScrollAmount = () => {
            const cardWidth = getCardWidth();
            const gapWidth = getGapWidth();
            return (cardWidth + gapWidth) * 2;
        };

        // Manual navigation
        nextBtn.addEventListener('click', () => {
            const scrollAmount = getScrollAmount();
            voucherContainer.scrollBy({
                left: scrollAmount,
                behavior: 'smooth'
            });
            stopAutoScroll();
            // Resume auto-scroll after manual interaction
            setTimeout(startAutoScroll, 5000);
        });

        prevBtn.addEventListener('click', () => {
            const scrollAmount = getScrollAmount();
            voucherContainer.scrollBy({
                left: -scrollAmount,
                behavior: 'smooth'
            });
            stopAutoScroll();
            // Resume auto-scroll after manual interaction
            setTimeout(startAutoScroll, 5000);
        });

        // Drag-to-scroll functionality
        let isDragging = false;
        let startX;
        let scrollLeft;

        voucherContainer.addEventListener('mousedown', (e) => {
            isDragging = true;
            startX = e.pageX - voucherContainer.offsetLeft;
            scrollLeft = voucherContainer.scrollLeft;
            voucherContainer.style.cursor = 'grabbing';
            stopAutoScroll();
        });

        voucherContainer.addEventListener('mouseleave', () => {
            if (isDragging) {
                isDragging = false;
                voucherContainer.style.cursor = 'grab';
                // Resume auto-scroll after drag ends
                setTimeout(startAutoScroll, 3000);
            }
        });

        voucherContainer.addEventListener('mouseup', () => {
            isDragging = false;
            voucherContainer.style.cursor = 'grab';
            // Resume auto-scroll after drag ends
            setTimeout(startAutoScroll, 3000);
        });

        voucherContainer.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            e.preventDefault();
            const x = e.pageX - voucherContainer.offsetLeft;
            const walk = (x - startX) * 2;
            voucherContainer.scrollLeft = scrollLeft - walk;
        });

        // Touch events for mobile
        let touchStartX = 0;
        voucherContainer.addEventListener('touchstart', (e) => {
            touchStartX = e.touches[0].clientX;
            stopAutoScroll();
        });

        voucherContainer.addEventListener('touchend', (e) => {
            const touchEndX = e.changedTouches[0].clientX;
            const diffX = touchStartX - touchEndX;
            if (Math.abs(diffX) > 50) {
                const scrollAmount = getScrollAmount();
                voucherContainer.scrollBy({
                    left: diffX > 0 ? scrollAmount : -scrollAmount,
                    behavior: 'smooth'
                });
            }
            // Resume auto-scroll after touch interaction
            setTimeout(startAutoScroll, 3000);
        });

        // Auto-scroll functionality (FIXED)
        let autoScrollInterval;
        let isAutoScrolling = false;

        function startAutoScroll() {
            if (isAutoScrolling || isDragging) return;

            isAutoScrolling = true;
            autoScrollInterval = setInterval(() => {
                const scrollAmount = getScrollAmount();
                const currentScroll = voucherContainer.scrollLeft;
                const maxScroll = voucherContainer.scrollWidth - voucherContainer.clientWidth;

                // Check if we've reached the end
                if (currentScroll + voucherContainer.clientWidth >= voucherContainer.scrollWidth - 10) {
                    // Smoothly scroll back to start
                    voucherContainer.scrollTo({
                        left: 0,
                        behavior: 'smooth'
                    });
                } else {
                    // Normal auto-scroll
                    voucherContainer.scrollBy({
                        left: scrollAmount,
                        behavior: 'smooth'
                    });
                }
            }, 4000); // Scroll every 4 seconds
        }

        function stopAutoScroll() {
            isAutoScrolling = false;
            clearInterval(autoScrollInterval);
        }

        // Pause auto-scroll on user interaction
        voucherContainer.addEventListener('mouseenter', stopAutoScroll);
        voucherContainer.addEventListener('mouseleave', () => {
            if (!isDragging) {
                setTimeout(startAutoScroll, 2000);
            }
        });

        voucherContainer.addEventListener('touchstart', stopAutoScroll);
        voucherContainer.addEventListener('touchend', () => {
            setTimeout(startAutoScroll, 2000);
        });

        // Initialize
        voucherContainer.style.cursor = 'grab';

        // Start auto-scroll after page load with a delay
        setTimeout(startAutoScroll, 2000);

        // Recalculate on window resize
        window.addEventListener('resize', () => {
            // Restart auto-scroll with new dimensions
            stopAutoScroll();
            setTimeout(startAutoScroll, 1000);
        });
    </script>
</body>

</html>

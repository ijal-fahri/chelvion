<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $product->name }} - CELVION</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #eef2ff;
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        .carousel-image {
            width: 100%;
            height: 450px;
            object-fit: contain;
            background-color: #f8f9fa;
        }

        .thumbnail {
            width: 70px;
            height: 70px;
            border-radius: 8px;
            object-fit: cover;
            cursor: pointer;
            border: 2px solid transparent;
            transition: border-color 0.3s ease;
        }

        .thumbnail.active {
            border-color: var(--primary);
        }

        .variant-option {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid #dee2e6;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .variant-option.selected {
            border-color: var(--primary);
            background-color: var(--primary-light);
            color: var(--primary);
            font-weight: 600;
        }

        .variant-option.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background-color: #f8f9fa;
            text-decoration: line-through;
        }

        /* [CSS PENTING DI SINI] */
        .variant-thumb-img {
            width: 35px;
            height: 35px;
            border-radius: 6px;
            object-fit: cover;
        }

        .quantity-btn {
            width: 40px;
            height: 40px;
            border: 1px solid #dee2e6;
            background: white;
        }

        .quantity-input {
            width: 60px;
            height: 40px;
            border-left: none;
            border-right: none;
        }

        .btn-primary-custom {
            background-color: var(--primary);
            border-color: var(--primary);
            padding: 12px 24px;
            font-weight: 600;
        }

        .btn-primary-custom:hover {
            background-color: #4338ca;
            border-color: #4338ca;
        }

        .price-display {
            background-color: var(--primary-light);
        }

        .stock-available {
            color: #10b981;
        }

        .stock-unavailable {
            color: #ef4444;
        }

        .section-title {
            border-bottom: 2px solid var(--primary-light);
        }

        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }
    </style>
</head>

<body class="bg-light">
    @include('components.navbar-pelanggan')

    <main class="container my-4 my-lg-5">
        <div class="card card-custom mb-5">
            <div class="card-body p-4 p-lg-5">
                <div class="row g-4 g-lg-5">
                    <div class="col-lg-5">
                        <div id="productCarousel" class="carousel slide">
                            <div class="carousel-inner rounded-3">
                                @forelse ($allImages as $index => $imageInfo)
                                    <div class="carousel-item {{ $index == 0 ? 'active' : '' }}"
                                        data-image-url="{{ $imageInfo['url'] }}">
                                        <img src="{{ $imageInfo['url'] }}" class="d-block carousel-image"
                                            alt="Gambar Produk {{ $index + 1 }}">
                                    </div>
                                @empty
                                    <div class="carousel-item active">
                                        <img src="https://placehold.co/600x600/eef2ff/4f46e5?text=N/A"
                                            class="d-block carousel-image" alt="Placeholder">
                                    </div>
                                @endforelse
                            </div>
                            @if ($allImages->count() > 1)
                                <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel"
                                    data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span><span
                                        class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#productCarousel"
                                    data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span><span
                                        class="visually-hidden">Next</span>
                                </button>
                            @endif
                        </div>
                        @if ($allImages->count() > 1)
                            <div class="d-flex flex-wrap gap-2 mt-3">
                                @foreach ($allImages as $index => $imageInfo)
                                    <img src="{{ $imageInfo['url'] }}"
                                        class="thumbnail {{ $index == 0 ? 'active' : '' }}"
                                        data-bs-target="#productCarousel" data-bs-slide-to="{{ $index }}">
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="col-lg-7">
                        <h1 class="h2 fw-bold text-dark mb-2">{{ $product->name }}</h1>
                        <div class="d-flex align-items-center mb-3 text-muted small">
                            <span>Kategori: {{ $product->category }}</span>
                        </div>
                        <div class="price-display p-3 rounded-3 mb-4">
                            <p id="product-price-display" class="h3 fw-bold text-primary mb-0"></p>
                        </div>
                        <div id="variants-container" class="mb-4"></div>
                        <div class="border-top pt-4">
                            <div class="row align-items-center">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label fw-semibold">Jumlah</label>
                                    <div class="d-flex">
                                        <button type="button" id="quantity-minus"
                                            class="quantity-btn btn rounded-end-0">-</button>
                                        <input type="number" id="quantity-input" value="1" min="1"
                                            class="form-control quantity-input text-center rounded-0" readonly>
                                        <button type="button" id="quantity-plus"
                                            class="quantity-btn btn rounded-start-0">+</button>
                                    </div>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <div id="stock-info" class="text-muted">Stok: <span id="stock-display"
                                            class="fw-bold">-</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="button" id="add-to-cart-btn"
                                class="btn btn-primary-custom w-100 py-3 disabled"><i
                                    class="bi bi-cart-plus-fill me-2"></i>Pilih Varian</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card card-custom">
                    <div class="card-body p-4">
                        <h3 class="h4 fw-bold text-dark section-title pb-3 mb-4">Spesifikasi & Deskripsi</h3>
                        <p class="text-secondary">{{ $product->description }}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card card-custom">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-dark mb-3">Keunggulan Produk</h5>
                        <div class="d-flex align-items-center mb-3"><i
                                class="bi bi-shield-check text-success me-3 fs-5"></i><span>Garansi Resmi 1 Tahun</span>
                        </div>
                        <div class="d-flex align-items-center mb-3"><i
                                class="bi bi-truck text-primary me-3 fs-5"></i><span>Gratis Ongkir</span></div>
                        <div class="d-flex align-items-center"><i
                                class="bi bi-headset text-info me-3 fs-5"></i><span>Customer Service 24/7</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <div class="card card-custom">
                <div class="card-body p-4">
                    <h3 class="h4 fw-bold text-dark section-title pb-3 mb-4">Ulasan Pembeli</h3>

                    @forelse ($reviews as $review)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-start mb-2">
                                <div class="flex-shrink-0 me-3">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($review->user->name ?? 'U') }}&background=eef2ff&color=4f46e5&rounded=true&size=40"
                                        alt="{{ $review->user->name ?? 'User' }}" class="rounded-circle">
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-semibold mb-0">{{ $review->user->name ?? 'Pengguna Anonim' }}</h6>
                                    <small
                                        class="text-muted">{{ $review->created_at->isoFormat('D MMMM YYYY') }}</small>
                                </div>
                            </div>

                            <div class="mb-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i
                                        class="bi {{ $i <= $review->rating ? 'bi-star-fill text-warning' : 'bi-star text-muted' }}"></i>
                                @endfor
                            </div>

                            @if ($review->comment)
                                <p class="text-secondary mb-2" style="white-space: pre-wrap;">{{ $review->comment }}
                                </p>
                            @endif

                            @if ($review->images && count($review->images) > 0)
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($review->images as $imagePath)
                                        <a href="{{ asset('storage/' . $imagePath) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $imagePath) }}" alt="Gambar Ulasan"
                                                style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid #eee;">
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-chat-square-text fs-1"></i>
                            <p class="mt-3">Belum ada ulasan untuk produk ini.</p>
                        </div>
                    @endforelse

                    @if ($reviews->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $reviews->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>

        @if ($recommendedProducts->isNotEmpty())
            <div class="mt-5">
                <h2 class="h3 fw-bold text-dark mb-4">Mungkin Kamu Suka</h2>
                <div class="row g-3">
                    @foreach ($recommendedProducts as $recProduct)
                        <div class="col-6 col-md-4 col-lg-3">
                            <a href="{{ route('products.show', $recProduct->id) }}" class="text-decoration-none">
                                <div class="card card-custom h-100">
                                    <img src="{{ asset('storage/' . (json_decode($recProduct->image)[0] ?? $recProduct->image)) }}"
                                        class="card-img-top p-3" alt="{{ $recProduct->name }}"
                                        style="height: 180px; object-fit: contain;">
                                    <div class="card-body pt-0">
                                        <h6 class="card-title fw-semibold text-dark mb-2" style="font-size: 0.9rem;">
                                            {{ \Illuminate\Support\Str::limit($recProduct->name, 45) }}</h6>
                                        <p class="card-text fw-bold text-primary mb-0">
                                            Rp{{ number_format($recProduct->master_price, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </main>

    @include('components.footer-pelanggan')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- DATA & ELEMEN DOM ---
            const product = @json($product);
            const allImages = @json($allImages);
            const variantsData = product.variants;
            const mainCarousel = new bootstrap.Carousel(document.getElementById('productCarousel'));

            const elements = {
                variantsContainer: document.getElementById('variants-container'),
                priceDisplay: document.getElementById('product-price-display'),
                stockDisplay: document.getElementById('stock-display'),
                addToCartBtn: document.getElementById('add-to-cart-btn'),
                quantityInput: document.getElementById('quantity-input'),
                qtyPlus: document.getElementById('quantity-plus'),
                qtyMinus: document.getElementById('quantity-minus'),
                thumbnails: document.querySelectorAll('.thumbnail')
            };

            let selectedOptions = {};
            let selectedVariant = null;

            const availableVariantTypes = {};
            variantsData.forEach(v => {
                if (v.color)(availableVariantTypes.color = availableVariantTypes.color || new Set()).add(v
                    .color);
                if (v.ram)(availableVariantTypes.ram = availableVariantTypes.ram || new Set()).add(v.ram);
            });
            const existingTypes = Object.keys(availableVariantTypes);

            // --- FUNGSI-FUNGSI UI (Tidak Berubah) ---
            const formatCurrency = (value) => new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(value);

            function renderVariantOptions() {
                let html = '';
                for (const [type, values] of Object.entries(availableVariantTypes)) {
                    const typeName = type === 'ram' ? 'RAM/Memori' : 'Warna';
                    html +=
                        `<div class="mb-3"><h5 class="fw-semibold text-dark mb-2 fs-6">${typeName}</h5><div class="d-flex flex-wrap gap-2">`;
                    values.forEach(value => {
                        const variantForThumb = variantsData.find(v => v[type] === value);
                        const thumbUrl = (variantForThumb.image_urls && variantForThumb.image_urls[0]) ||
                            allImages[0].url;
                        const content = type === 'color' ?
                            `<img src="${thumbUrl}" class="variant-thumb-img" alt="${value}"> <span class="ps-2">${value}</span>` :
                            value;
                        const padding = type === 'color' ? 'p-1' : 'px-3 py-2';
                        html +=
                            `<div class="variant-option d-flex align-items-center ${padding}" data-type="${type}" data-value="${value}">${content}</div>`;
                    });
                    html += `</div></div>`;
                }
                elements.variantsContainer.innerHTML = html;
            }

            function updateUI() {
                const allOptionsSelected = existingTypes.length === Object.keys(selectedOptions).length;
                selectedVariant = allOptionsSelected ? variantsData.find(v => existingTypes.every(t => v[t] ===
                    selectedOptions[t])) : null;

                if (selectedVariant) {
                    elements.priceDisplay.textContent = formatCurrency(selectedVariant.price);
                    elements.stockDisplay.textContent = selectedVariant.stock;
                    elements.quantityInput.max = selectedVariant.stock;
                    if (selectedVariant.image_urls && selectedVariant.image_urls.length > 0) {
                        const targetImage = selectedVariant.image_urls[0];
                        const slideIndex = allImages.findIndex(img => img.url === targetImage);
                        if (slideIndex !== -1) mainCarousel.to(slideIndex);
                    }
                } else {
                    elements.priceDisplay.textContent = formatCurrency(product.master_price);
                    elements.stockDisplay.textContent = variantsData.reduce((sum, v) => sum + v.stock, 0);
                }

                if (parseInt(elements.quantityInput.value) > parseInt(elements.quantityInput.max)) {
                    elements.quantityInput.value = elements.quantityInput.max > 0 ? 1 : 0;
                }

                elements.addToCartBtn.classList.add('disabled');
                if (allOptionsSelected) {
                    if (selectedVariant && selectedVariant.stock > 0) {
                        elements.addToCartBtn.innerHTML =
                            '<i class="bi bi-cart-plus-fill me-2 text-white">Tambah ke Keranjang</i>';
                        elements.addToCartBtn.classList.remove('disabled');
                    } else {
                        elements.addToCartBtn.innerHTML = '<i class="bi bi-x-circle me-2"></i>Stok Habis';
                    }
                } else {
                    const needed = existingTypes.filter(t => !selectedOptions[t]).map(t => t === 'ram' ? 'RAM' :
                        'Warna').join(' & ');
                    elements.addToCartBtn.innerHTML = `<i class="bi bi-card-checklist me-2"></i>Pilih ${needed}`;
                }

                document.querySelectorAll('.variant-option').forEach(opt => {
                    const type = opt.dataset.type;
                    const value = opt.dataset.value;
                    opt.classList.remove('disabled');
                    opt.classList.toggle('selected', selectedOptions[type] === value);
                    const tempSelection = {
                        ...selectedOptions
                    };
                    delete tempSelection[type];
                    const isPossible = variantsData.some(v => v[type] === value && Object.keys(
                        tempSelection).every(t => v[t] === tempSelection[t]) && v.stock > 0);
                    if (!isPossible && !opt.classList.contains('selected')) {
                        opt.classList.add('disabled');
                    }
                });
            }

            // --- EVENT LISTENERS (Tidak Berubah) ---
            elements.variantsContainer.addEventListener('click', e => {
                const target = e.target.closest('.variant-option');
                if (!target || target.classList.contains('disabled')) return;
                const {
                    type,
                    value
                } = target.dataset;
                selectedOptions[type] === value ? delete selectedOptions[type] : selectedOptions[type] =
                    value;
                updateUI();
            });

            const updateQuantity = amount => {
                let newVal = parseInt(elements.quantityInput.value) + amount;
                const maxStock = selectedVariant ? selectedVariant.stock : 0;
                if (newVal < 1) newVal = 1;
                if (maxStock > 0 && newVal > maxStock) newVal = maxStock;
                elements.quantityInput.value = newVal;
            };

            elements.qtyPlus.addEventListener('click', () => updateQuantity(1));
            elements.qtyMinus.addEventListener('click', () => updateQuantity(-1));

            // ... (Event listener lain jika ada)

            // --- [INI BAGIAN UTAMA YANG DIPERBARUI] ---
            // Logika Tombol "Tambah ke Keranjang"
            @auth
            // --- JIKA USER SUDAH LOGIN ---
            elements.addToCartBtn.addEventListener('click', function() {
                if (!selectedVariant) {
                    Swal.fire('Pilih Varian', 'Anda harus memilih varian produk terlebih dahulu.',
                        'warning');
                    return;
                }
                if (this.classList.contains('disabled')) return;

                this.innerHTML =
                    `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menambahkan...`;
                this.disabled = true;

                const cartData = {
                    product_id: product.id,
                    variant_id: selectedVariant.id,
                    quantity: elements.quantityInput.value,
                    color: selectedVariant.color || null, // Kirim null jika tidak ada
                    ram: selectedVariant.ram || null, // Kirim null jika tidak ada
                    price: selectedVariant.price,
                };

                fetch("{{ route('cart.add') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(cartData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: data.message,
                                showConfirmButton: false,
                                timer: 2000
                            });
                            // Update cart count di navbar
                            // Pastikan elemen badge di navbar Anda memiliki id="cart-badge-count"
                            const cartBadge = document.getElementById('cart-badge-count');
                            if (cartBadge) {
                                cartBadge.textContent = data.cart_count;
                                cartBadge.classList.remove('d-none');
                            }
                        } else {
                            // Jika ada redirect (karena belum login), arahkan ke halaman login
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                Swal.fire('Gagal', data.message || 'Gagal menambahkan produk.',
                                    'error');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Terjadi kesalahan. Silakan coba lagi.', 'error');
                    })
                    .finally(() => {
                        updateUI(); // Kembalikan teks dan status tombol
                        this.disabled = false;
                    });
            });
        @else
            // --- JIKA USER BELUM LOGIN (GUEST) ---
            elements.addToCartBtn.addEventListener('click', () => {
                Swal.fire({
                    icon: 'info',
                    title: 'Login Diperlukan',
                    text: 'Anda harus login terlebih dahulu untuk menambahkan produk ke keranjang.',
                    confirmButtonText: 'Login Sekarang',
                    showCancelButton: true,
                    cancelButtonText: 'Nanti Saja',
                }).then(result => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('login') }}";
                    }
                });
            });
        @endauth

        // --- INISIALISASI ---
        renderVariantOptions(); updateUI();
        });
    </script>

</body>

</html>

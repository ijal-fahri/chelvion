<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Keranjang Belanja - GADGETSTORE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary: #4f46e5;
            /* Indigo-600 */
            --primary-light: #eef2ff;
            /* Indigo-50 */
            --secondary: #64748b;
            /* Slate-500 */
            --light: #f8f9fa;
            --dark: #1e293b;
            /* Slate-800 */
            --body-bg: #f1f5f9;
            /* Slate-100 */
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            /* Slate-200 */
            --success: #10b981;
            /* Emerald-500 */
            --danger: #ef4444;
            /* Red-500 */
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--body-bg);
            color: var(--dark);
        }

        .fw-semibold {
            font-weight: 600 !important;
        }

        /* --- Header --- */
        .header-main {
            background-color: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
        }

        .header-main .form-control {
            border-radius: 99px;
            border-color: var(--border-color);
            padding-left: 2.5rem;
            background-color: var(--body-bg);
        }

        .header-main .input-group-text {
            background: transparent;
            border: none;
            position: absolute;
            z-index: 10;
        }

        .header-main .form-control:focus {
            box-shadow: 0 0 0 3px var(--primary-light);
            border-color: var(--primary);
            background-color: #fff;
        }

        .header-actions .btn-icon {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background-color: var(--body-bg);
            color: var(--secondary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            border: 1px solid var(--border-color);
            transition: all .2s ease;
            position: relative;
        }

        .header-actions .btn-icon:hover {
            background-color: var(--primary-light);
            color: var(--primary);
            border-color: var(--primary-light);
        }

        .cart-badge {
            position: absolute;
            top: -2px;
            right: -4px;
            font-size: 0.65rem;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: 2px solid #fff;
        }

        /* --- Cart Page Specific Styles --- */
        .cart-item-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .cart-item-img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            border-radius: 0.75rem;
            background-color: #f8fafc;
        }

        .cart-item-title {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--dark);
        }

        .cart-item-details {
            font-size: 0.85rem;
            color: var(--secondary);
        }

        .cart-item-price {
            font-size: 1rem;
            font-weight: 600;
        }

        .summary-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            padding: 1.5rem;
        }

        /* Sticky summary for larger screens */
        @media (min-width: 992px) {
            .summary-card {
                position: sticky;
                top: 100px;
                /* Adjust based on header height */
            }
        }

        .summary-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            color: var(--secondary);
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
            font-weight: 700;
            font-size: 1.1rem;
        }

        .summary-total .total-price {
            color: var(--primary);
            font-size: 1.25rem;
        }

        .empty-cart {
            background: var(--card-bg);
            border: 2px dashed var(--border-color);
            border-radius: 1rem;
            padding: 4rem 1rem;
            text-align: center;
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

        .btn-primary {
            background-color: #4f46e5 !important;
            border-color: #4f46e5 !important;
        }

        .btn-primary:hover {
            background-color: #4338ca !important;
            border-color: #4338ca !important;
        }
    </style>
</head>

<body>

    @include('components.navbar-pelanggan', [
        'orderCount' => $orderCount ?? 0,
        'wishlistCount' => $wishlistCount ?? 0,
        'cartCount' => $cartCount ?? 0,
    ])

    <div class="container py-5">
        <h2 class="fw-bolder mb-4">
            <i class="bi bi-cart-check-fill text-primary"></i> Keranjang Belanja Anda
        </h2>

        @if (session('success'))
            <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
        @endif

        @if ($carts->isEmpty())
            <div class="empty-cart">
                <i class="bi bi-cart-x" style="font-size: 4rem; color: var(--secondary);"></i>
                <h4 class="mt-3">Keranjang Anda Kosong</h4>
                <p class="text-secondary">Sepertinya Anda belum menambahkan produk apapun.</p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary mt-3 fw-semibold">
                    <i class="bi bi-arrow-left"></i> Mulai Belanja
                </a>
            </div>
        @else
            <div class="row g-4">
                {{-- Cart Items Column --}}
                <div class="col-lg-8">
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="selectAll">
                        <label class="form-check-label fw-semibold" for="selectAll">Pilih Semua</label>
                    </div>

                    @php $grandTotal = 0; @endphp
                    @foreach ($carts as $item)
                        @php
                            $price = $item->variant->price ?? $item->product->price;
                            $subtotal = $price * $item->quantity;
                            $grandTotal += $subtotal;
                        @endphp
                        <div class="cart-item-card d-flex align-items-start">
                            <div class="form-check me-3">
                                <input type="checkbox" class="form-check-input cart-checkbox" name="selected_items[]"
                                    value="{{ $item->id }}" data-price="{{ $subtotal }}">
                            </div>
                            <div class="d-flex justify-content-between align-items-start flex-grow-1">
                                <div class="d-flex align-items-center flex-grow-1">
                                    @php
                                        $imageUrl =
                                            $item->variant->first_image_url ??
                                            asset('storage/products/' . $item->product->image);
                                    @endphp
                                    <img src="{{ $imageUrl }}" alt="{{ $item->product->name }}"
                                        class="cart-item-img me-3">
                                    <div>
                                        <h5 class="cart-item-title mb-1">{{ $item->product->name }}</h5>
                                        <p class="cart-item-details mb-1">
                                            @if ($item->variant)
                                                Varian: {{ $item->variant->ram }} / {{ $item->variant->color }}
                                            @else
                                                Produk Standar
                                            @endif
                                        </p>
                                        <p class="cart-item-price text-secondary">
                                            {{ $item->quantity }} x Rp{{ number_format($price, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-end ms-3">
                                    <div class="fw-semibold text-dark mb-2 fs-5">
                                        Rp{{ number_format($subtotal, 0, ',', '.') }}
                                    </div>
                                    <form action="{{ route('cart.destroy', $item->id) }}" method="POST"
                                        class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete">
                                            <i class="bi bi-trash3"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Summary Column --}}
                <div class="col-lg-4">
                    <div class="summary-card">
                        <h4 class="summary-title">Ringkasan Belanja</h4>
                        <div class="summary-item">
                            <span>Produk Terpilih</span>
                            <span id="selectedCount">0</span>
                        </div>
                        <div class="summary-total">
                            <span>Total</span>
                            <span class="total-price" id="totalPrice">Rp0</span>
                        </div>
                        <div class="d-grid mt-4">
                            <form id="checkoutForm" action="{{ route('checkout.create') }}" method="GET">
                                <input type="hidden" name="selected_ids" id="selectedIds">
                                <button type="submit" class="btn btn-primary btn-lg fw-semibold w-100" id="checkoutBtn"
                                    disabled>
                                    Lanjut ke Checkout <i class="bi bi-arrow-right"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ url()->previous() }}" class="text-decoration-none text-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali Belanja
                        </a>
                    </div>
                </div>

            </div>
        @endif
    </div>

    @include('components.footer-pelanggan')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Referensi ke Elemen-elemen PENTING di Halaman ---
            const cartContainer = document.querySelector('.row.g-4');
            const emptyCartView = document.querySelector('.empty-cart');

            // --- Logika Checkbox & Ringkasan Belanja ---
            const selectAllCheckbox = document.getElementById('selectAll');
            const selectedCountSpan = document.getElementById('selectedCount');
            const totalPriceSpan = document.getElementById('totalPrice');
            const checkoutButton = document.getElementById('checkoutBtn');
            const selectedIdsInput = document.getElementById('selectedIds');

            const updateSummary = () => {
                let count = 0;
                let total = 0;
                let ids = [];
                const itemCheckboxes = document.querySelectorAll('.cart-checkbox');

                itemCheckboxes.forEach(cb => {
                    if (cb.checked) {
                        count++;
                        total += parseInt(cb.dataset.price);
                        ids.push(cb.value);
                    }
                });

                if (selectedCountSpan) selectedCountSpan.textContent = count;
                if (totalPriceSpan) totalPriceSpan.textContent = "Rp" + total.toLocaleString('id-ID');
                if (checkoutButton) checkoutButton.disabled = count === 0;
                if (selectedIdsInput) selectedIdsInput.value = ids.join(',');
            };

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    document.querySelectorAll('.cart-checkbox').forEach(cb => {
                        cb.checked = this.checked;
                    });
                    updateSummary();
                });
            }

            document.querySelectorAll('.cart-checkbox').forEach(cb => {
                cb.addEventListener('change', () => {
                    if (!cb.checked && selectAllCheckbox) {
                        selectAllCheckbox.checked = false;
                    }
                    updateSummary();
                });
            });

            // --- Logika Tombol Hapus ---
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();

                    const form = this.closest('.delete-form');
                    const url = form.getAttribute('action');
                    const cartItemCard = this.closest('.cart-item-card');

                    Swal.fire({
                        title: 'Hapus Produk?',
                        text: "Produk ini akan dihapus dari keranjang Anda.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(url, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').getAttribute(
                                            'content'),
                                        'Accept': 'application/json',
                                    }
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

                                        // Animasi fade out lalu hapus elemen
                                        cartItemCard.style.transition =
                                            'opacity 0.4s ease';
                                        cartItemCard.style.opacity = '0';

                                        setTimeout(() => {
                                            cartItemCard.remove();
                                            updateSummary
                                        (); // Hitung ulang ringkasan belanja

                                            // Update badge di navbar
                                            const cartBadge = document
                                                .getElementById(
                                                    'cart-badge-count');
                                            if (cartBadge) cartBadge
                                                .textContent = data.cart_count;

                                            // Cek jika keranjang menjadi kosong
                                            if (document.querySelectorAll(
                                                    '.cart-item-card')
                                                .length === 0) {
                                                if (cartContainer) cartContainer
                                                    .style.display = 'none';
                                                if (emptyCartView) emptyCartView
                                                    .style.display = 'block';
                                            }
                                        }, 400);

                                    } else {
                                        Swal.fire('Gagal', data.message ||
                                            'Gagal menghapus item.', 'error');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    Swal.fire('Error', 'Terjadi kesalahan sistem.',
                                        'error');
                                });
                        }
                    });
                });
            });

            // Panggil saat pertama kali halaman dimuat
            updateSummary();
        });
    </script>
</body>

</html>

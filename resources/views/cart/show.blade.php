<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja - GADGETSTORE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary: #4f46e5; /* Indigo-600 */
            --primary-light: #eef2ff; /* Indigo-50 */
            --secondary: #64748b;   /* Slate-500 */
            --light: #f8f9fa;
            --dark: #1e293b;       /* Slate-800 */
            --body-bg: #f1f5f9;     /* Slate-100 */
            --card-bg: #ffffff;
            --border-color: #e2e8f0; /* Slate-200 */
            --success: #10b981; /* Emerald-500 */
            --danger: #ef4444;  /* Red-500 */
        }
        * { font-family: 'Poppins', sans-serif; }
        body { background-color: var(--body-bg); color: var(--dark); }
        .fw-semibold { font-weight: 600 !important; }

        /* --- Header --- */
        .header-main {
            background-color: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
        }
        .header-main .form-control {
            border-radius: 99px; border-color: var(--border-color);
            padding-left: 2.5rem; background-color: var(--body-bg);
        }
        .header-main .input-group-text {
            background: transparent; border: none;
            position: absolute; z-index: 10;
        }
        .header-main .form-control:focus { 
            box-shadow: 0 0 0 3px var(--primary-light); 
            border-color: var(--primary); background-color: #fff;
        }
        .header-actions .btn-icon {
            width: 42px; height: 42px; border-radius: 50%;
            background-color: var(--body-bg); color: var(--secondary);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.2rem; border: 1px solid var(--border-color);
            transition: all .2s ease; position: relative;
        }
        .header-actions .btn-icon:hover { background-color: var(--primary-light); color: var(--primary); border-color: var(--primary-light); }
        .cart-badge {
            position: absolute; top: -2px; right: -4px;
            font-size: 0.65rem; width: 18px; height: 18px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%; border: 2px solid #fff;
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
                top: 100px; /* Adjust based on header height */
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
    </style>
</head>
<body>

<header class="header-main sticky-top py-2">
    <div class="container">
        <div class="d-flex align-items-center">
            <a class="navbar-brand fw-bolder fs-4 me-4 d-none d-lg-block" href="{{ url('/') }}">
                <i class="bi bi-phone-vibrate-fill text-primary"></i> GADGETSTORE
            </a>
            <div class="flex-grow-1">
                <div class="input-group align-items-center">
                    <span class="input-group-text ps-3 text-secondary"><i class="bi bi-search"></i></span>
                    <input type="search" class="form-control" placeholder="Cari gadget impianmu..." aria-label="Search" disabled>
                </div>
            </div>
            <div class="header-actions d-flex align-items-center ms-3">
                <a class="btn-icon active" href="{{ route('cart.show') }}">
                    <i class="bi bi-cart3"></i>
                    {{-- Logic to show cart count can be added here --}}
                </a>
                @auth
                    <div class="dropdown ms-2">
                        <button class="btn-icon" type="button" data-bs-toggle="dropdown"><i class="bi bi-person-circle"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end mt-2">
                                @if(auth()->user()->is_admin)
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Admin Dashboard</a></li>
                                @endif
                            <li><a class="dropdown-item" href="#">Profil Saya</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary ms-2 d-none d-md-block">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-primary ms-2 d-none d-md-block">Register</a>
                @endauth
            </div>
        </div>
    </div>
</header>

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
            @php $grandTotal = 0; @endphp
            @foreach ($carts as $item)
                @php
                    // Assuming price is on the variant if it exists, otherwise on the product
                    $price = $item->variant->price ?? $item->product->price;
                    $subtotal = $price * $item->quantity;
                    $grandTotal += $subtotal;
                @endphp
                <div class="cart-item-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex align-items-center flex-grow-1">
                            {{-- CORE FIX: Changed asset path to match product page --}}
                            <img src="{{ asset('storage/products/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="cart-item-img me-3">
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
                            <div class="fw-semibold text-dark mb-2 fs-5">Rp{{ number_format($subtotal, 0, ',', '.') }}</div>
                            <form action="{{ route('cart.destroy', $item->id) }}" method="POST" class="delete-form d-inline">
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
                    <span>Subtotal ({{ $carts->count() }} produk)</span>
                    <span>Rp{{ number_format($grandTotal, 0, ',', '.') }}</span>
                </div>
                <div class="summary-item">
                    <span>Biaya Pengiriman</span>
                    <span>Gratis</span>
                </div>
                <div class="summary-total">
                    <span>Total</span>
                    <span class="total-price">Rp{{ number_format($grandTotal, 0, ',', '.') }}</span>
                </div>
                <div class="d-grid mt-4">
                    <a href="{{ route('checkout.create') }}" class="btn btn-primary btn-lg fw-semibold">
                        Lanjut ke Checkout <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="text-center mt-3">
               {{-- [PERBAIKAN] Mengubah link untuk kembali ke halaman sebelumnya --}}
               <a href="{{ url()->previous() }}" class="text-decoration-none text-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali Belanja
                </a>
            </div>
        </div>
    </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('.delete-form');

            Swal.fire({
                title: 'Hapus Produk Ini?',
                text: "Produk akan dihapus secara permanen dari keranjang Anda.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-4',
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary ms-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
</body>
</html>


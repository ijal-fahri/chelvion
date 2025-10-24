<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3 d-flex align-items-center" href="{{ url('pelanggan/dashboard') }}">
            <img src="{{ asset('navbar/logo.png') }}" alt="CELVION Logo" class="me-2 navbar-logo">
            CELVION
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link fw-semibold {{ request()->is('pelanggan/dashboard') ? 'active text-primary' : 'text-dark' }}"
                        href="{{ url('pelanggan/dashboard') }}">
                        <i class="bi bi-house me-1"></i> Home
                    </a>
                </li>
                <li class="nav-item dropdown position-static">
                    <!-- PERBAIKAN: Tambahkan class untuk deteksi halaman kategori -->
                    <a class="nav-link fw-semibold dropdown-toggle {{ request()->is('pelanggan/kategori*') || request()->routeIs('kategori.*') ? 'active text-primary' : 'text-dark' }}"
                        href="#" id="kategoriDropdown" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-grid me-1"></i> Kategori
                    </a>

                    <div class="dropdown-menu w-100 shadow border-0 mt-0 py-4" aria-labelledby="kategoriDropdown"
                        style="border-radius: 0 0 1rem 1rem;">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <h6 class="text-primary fw-bold mb-3">Handphone</h6>
                                    <ul class="list-unstyled">
                                        <li><a href="{{ route('kategori.index', ['brand' => 'Samsung', 'type' => 'Handphone']) }}"
                                                class="dropdown-item py-1 {{ request('brand') == 'Samsung' && request('type') == 'Handphone' ? 'active text-primary fw-bold' : '' }}">Samsung</a>
                                        </li>
                                        <li><a href="{{ route('kategori.index', ['brand' => 'iPhone', 'type' => 'Handphone']) }}"
                                                class="dropdown-item py-1 {{ request('brand') == 'iPhone' && request('type') == 'Handphone' ? 'active text-primary fw-bold' : '' }}">iPhone</a>
                                        </li>
                                        <li><a href="{{ route('kategori.index', ['brand' => 'Xiaomi', 'type' => 'Handphone']) }}"
                                                class="dropdown-item py-1 {{ request('brand') == 'Xiaomi' && request('type') == 'Handphone' ? 'active text-primary fw-bold' : '' }}">Xiaomi</a>
                                        </li>
                                        <li><a href="{{ route('kategori.index', ['brand' => 'OPPO', 'type' => 'Handphone']) }}"
                                                class="dropdown-item py-1 {{ request('brand') == 'OPPO' && request('type') == 'Handphone' ? 'active text-primary fw-bold' : '' }}">OPPO</a>
                                        </li>
                                        <li><a href="{{ route('kategori.index', ['brand' => 'Vivo', 'type' => 'Handphone']) }}"
                                                class="dropdown-item py-1 {{ request('brand') == 'Vivo' && request('type') == 'Handphone' ? 'active text-primary fw-bold' : '' }}">Vivo</a>
                                        </li>
                                        <li><a href="{{ route('kategori.index', ['brand' => 'Realme', 'type' => 'Handphone']) }}"
                                                class="dropdown-item py-1 {{ request('brand') == 'Realme' && request('type') == 'Handphone' ? 'active text-primary fw-bold' : '' }}">Realme</a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="col-lg-3 col-md-6 mb-3">
                                    <h6 class="text-primary fw-bold mb-3">Aksesoris</h6>
                                    <ul class="list-unstyled">
                                        <li><a href="{{ route('kategori.index', ['brand' => 'Casing', 'type' => 'Aksesori']) }}"
                                                class="dropdown-item py-1 {{ request('brand') == 'Casing' && request('type') == 'Aksesori' ? 'active text-primary fw-bold' : '' }}">Casing</a>
                                        </li>
                                        <li><a href="{{ route('kategori.index', ['brand' => 'Tempered Glass', 'type' => 'Aksesori']) }}"
                                                class="dropdown-item py-1 {{ request('brand') == 'Tempered Glass' && request('type') == 'Aksesori' ? 'active text-primary fw-bold' : '' }}">Tempered
                                                Glass</a></li>
                                        <li><a href="{{ route('kategori.index', ['brand' => 'Earphone', 'type' => 'Aksesori']) }}"
                                                class="dropdown-item py-1 {{ request('brand') == 'Earphone' && request('type') == 'Aksesori' ? 'active text-primary fw-bold' : '' }}">Earphone</a>
                                        </li>
                                        <li><a href="{{ route('kategori.index', ['brand' => 'Charger', 'type' => 'Aksesori']) }}"
                                                class="dropdown-item py-1 {{ request('brand') == 'Charger' && request('type') == 'Aksesori' ? 'active text-primary fw-bold' : '' }}">Charger</a>
                                        </li>
                                        <li><a href="{{ route('kategori.index', ['brand' => 'Powerbank', 'type' => 'Aksesori']) }}"
                                                class="dropdown-item py-1 {{ request('brand') == 'Powerbank' && request('type') == 'Aksesori' ? 'active text-primary fw-bold' : '' }}">Powerbank</a>
                                        </li>
                                        <li><a href="{{ route('kategori.index', ['brand' => 'Kabel Data', 'type' => 'Aksesori']) }}"
                                                class="dropdown-item py-1 {{ request('brand') == 'Kabel Data' && request('type') == 'Aksesori' ? 'active text-primary fw-bold' : '' }}">Kabel
                                                Data</a></li>
                                    </ul>
                                </div>

                                <div class="col-lg-3 col-md-6 mb-3">
                                    <h6 class="text-primary fw-bold mb-3">Brand Populer</h6>
                                    <ul class="list-unstyled">
                                        <li><a href="{{ route('kategori.index', ['brand' => 'Apple']) }}"
                                                class="dropdown-item py-1 {{ request('brand') == 'Apple' ? 'active text-primary fw-bold' : '' }}">Apple</a>
                                        </li>
                                        <li><a href="{{ route('kategori.index', ['brand' => 'Samsung']) }}"
                                                class="dropdown-item py-1 {{ request('brand') == 'Samsung' ? 'active text-primary fw-bold' : '' }}">Samsung</a>
                                        </li>
                                        <li><a href="{{ route('kategori.index', ['brand' => 'Baseus']) }}"
                                                class="dropdown-item py-1 {{ request('brand') == 'Baseus' ? 'active text-primary fw-bold' : '' }}">Baseus</a>
                                        </li>
                                        <li><a href="{{ route('kategori.index', ['brand' => 'Anker']) }}"
                                                class="dropdown-item py-1 {{ request('brand') == 'Anker' ? 'active text-primary fw-bold' : '' }}">Anker</a>
                                        </li>
                                        <li><a href="{{ route('kategori.index', ['brand' => 'Ugreen']) }}"
                                                class="dropdown-item py-1 {{ request('brand') == 'Ugreen' ? 'active text-primary fw-bold' : '' }}">Ugreen</a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="col-lg-3 col-md-6 mb-3">
                                    <h6 class="text-primary fw-bold mb-3">Promo & Rekomendasi</h6>
                                    <ul class="list-unstyled">
                                        <li><a href="{{ route('kategori.index', ['type' => 'Handphone', 'sort' => 'newest']) }}"
                                                class="dropdown-item py-1 {{ request('type') == 'Handphone' && request('sort') == 'newest' ? 'active text-primary fw-bold' : '' }}">Handphone
                                                Terbaru</a></li>
                                        <li><a href="{{ route('kategori.index', ['type' => 'Aksesori']) }}"
                                                class="dropdown-item py-1 {{ request('type') == 'Aksesori' ? 'active text-primary fw-bold' : '' }}">Aksesoris
                                                Diskon</a></li>
                                        <li><a href="{{ route('kategori.index', ['sort' => 'newest']) }}"
                                                class="dropdown-item py-1 {{ request('sort') == 'newest' ? 'active text-primary fw-bold' : '' }}">Best
                                                Seller Minggu Ini</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>

                @auth
                    <li class="nav-item">
                        <a class="nav-link fw-semibold {{ request()->is('orderan*') ? 'active text-primary' : 'text-dark' }}"
                            href="{{ route('orderan.index') }}">
                            <i class="bi bi-cart-check me-1"></i> Pesanan Saya
                            @if (isset($orderCount) && $orderCount > 0)
                                <span class="badge bg-primary ms-1">{{ $orderCount }}</span>
                            @endif
                        </a>
                    </li>
                @endauth
            </ul>

            <div class="d-flex align-items-center">
                <!-- Form Search yang sudah diperbaiki -->
                <form action="{{ route('kategori.index') }}" method="GET" class="me-3 d-none d-lg-flex">
                    <div class="input-group" style="width: 300px;">
                        <input type="text" name="search" class="form-control border-end-0 search-purple"
                            placeholder="Cari produk..." value="{{ request('search') }}" id="navbar-search-input">
                        <button class="btn btn-outline-purple border-start-0" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>

                <div class="d-flex gap-2">
                    @auth
                        <a href="{{ route('cart.show') }}" class="btn btn-outline-purple position-relative">
                            <i class="bi bi-cart3"></i>
                            <span id="cart-badge-count"
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary @if (!isset($cartCount) || $cartCount <= 0) d-none @endif">
                                {{ $cartCount ?? 0 }}
                            </span>
                        </a>
                    @endauth

                    @auth
                        <div class="dropdown">
                            <button class="btn btn-outline-purple dropdown-toggle d-flex align-items-center"
                                type="button" data-bs-toggle="dropdown">

                                {{-- [BARU] Tambahkan <img> dengan photo_url --}}
                                <img src="{{ Auth::user()->photo_url }}" alt="Foto Profil" class="rounded-circle me-2"
                                    style="width: 28px; height: 28px; object-fit: cover;">

                                <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                                <span class="d-inline d-md-none">{{ Str::limit(Auth::user()->name, 8) }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i
                                            class="bi bi-person me-2"></i>Profil Saya</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>

                                @if (auth()->check() && auth()->user()->is_admin)
                                    <li><a class="dropdown-item text-warning" href="{{ route('admin.dashboard') }}"><i
                                                class="bi bi-speedometer2 me-2"></i>Admin Dashboard</a></li>
                                @endif

                                @if (auth()->check() && auth()->user()->role == 'owner')
                                    <li><a class="dropdown-item text-warning" href="{{ route('owner.dashboard') }}"><i
                                                class="bi bi-speedometer2 me-2"></i>Owner Dashboard</a></li>
                                @endif

                                @if (auth()->check() && auth()->user()->role == 'kasir')
                                    <li><a class="dropdown-item text-warning" href="{{ route('kasir.dashboard') }}"><i
                                                class="bi bi-speedometer2 me-2"></i>Kasir Dashboard</a></li>
                                @endif

                                @if (auth()->check() && auth()->user()->role == 'staf_gudang')
                                    <li><a class="dropdown-item text-warning" href="{{ route('staff.dashboard') }}"><i
                                                class="bi bi-speedometer2 me-2"></i>Staff Dashboard</a></li>
                                @endif

                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>

                                <li>
                                    <a class="dropdown-item text-primary" href="{{ route('bantuan.index') }}">
                                        <i class="bi bi-question-circle me-2"></i>Bantuan
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @else
                        <div class="d-flex gap-2">
                            <a href="{{ route('login') }}" class="btn btn-outline-purple">Login</a>
                            <a href="{{ route('register') }}" class="btn btn-outline-purple">Register</a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Search Mobile (Tambahkan ini di bawah navbar untuk mobile) -->
<div class="d-lg-none bg-white border-bottom py-2">
    <div class="container">
        <form action="{{ route('kategori.index') }}" method="GET">
            <div class="input-group">
                <input type="text" name="search" class="form-control search-purple" placeholder="Cari produk..."
                    value="{{ request('search') }}">
                <button class="btn btn-outline-purple" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .navbar-logo {
        height: 40px;
        width: auto;
        object-fit: contain;
        transition: all 0.2s ease;
    }

    @media (max-width: 991px) {
        .navbar-logo {
            height: 34px;
        }
    }

    @media (max-width: 576px) {
        .navbar-logo {
            height: 28px;
        }
    }

    .navbar .dropdown-menu {
        top: 100%;
        left: 0;
        right: 0;
        border-top: 3px solid #4f46e5;
        background: #fff;
    }

    .navbar .dropdown-menu h6 {
        font-size: 1rem;
    }

    .navbar .dropdown-menu .dropdown-item {
        color: #374151;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .navbar .dropdown-menu .dropdown-item:hover,
    .navbar .dropdown-menu .dropdown-item.active {
        background-color: #eef2ff;
        color: #4f46e5;
        border-radius: 0.5rem;
    }

    @media (max-width: 991px) {
        .navbar .dropdown-menu {
            position: static;
            border-top: none;
            box-shadow: none;
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

    .search-purple {
        border: 1px solid #4f46e5;
        box-shadow: none;
        transition: all 0.2s ease;
    }

    .search-purple::placeholder {
        color: #9ca3af;
    }

    .search-purple:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
    }

    .navbar {
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        padding: 0.8rem 0;
    }

    .navbar-nav .nav-link {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        margin: 0 0.2rem;
    }

    .navbar-nav .nav-link:hover,
    .navbar-nav .nav-link.active {
        background-color: #eef2ff;
        color: #4f46e5 !important;
    }

    .navbar-brand {
        color: #1e293b !important;
    }

    .input-group .form-control:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
    }

    /* PERBAIKAN: Style untuk item aktif di dropdown */
    .dropdown-item.active {
        font-weight: 600 !important;
    }
</style>

<script>
    // PERBAIKAN: JavaScript untuk menangani dropdown kategori di semua halaman
    document.addEventListener('DOMContentLoaded', function() {
        const kategoriDropdown = document.getElementById('kategoriDropdown');
        const dropdownMenu = kategoriDropdown?.nextElementSibling;

        if (!kategoriDropdown || !dropdownMenu) return;

        // Cek jika kita berada di halaman kategori
        const isKategoriPage = window.location.pathname.includes('kategori') ||
            window.location.href.includes('route=kategori');

        // Prevent default behavior hanya di halaman kategori
        if (isKategoriPage) {
            kategoriDropdown.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Toggle manual dropdown
                const isShowing = dropdownMenu.classList.contains('show');
                if (isShowing) {
                    dropdownMenu.classList.remove('show');
                    kategoriDropdown.setAttribute('aria-expanded', 'false');
                } else {
                    dropdownMenu.classList.add('show');
                    kategoriDropdown.setAttribute('aria-expanded', 'true');
                }
            });

            // Close dropdown ketika klik di luar
            document.addEventListener('click', function(e) {
                if (!kategoriDropdown.contains(e.target) && !dropdownMenu.contains(e.target)) {
                    dropdownMenu.classList.remove('show');
                    kategoriDropdown.setAttribute('aria-expanded', 'false');
                }
            });

            // Handle escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    dropdownMenu.classList.remove('show');
                    kategoriDropdown.setAttribute('aria-expanded', 'false');
                }
            });
        }

        // Untuk mobile, pastikan dropdown tetap bekerja
        const navbarToggler = document.querySelector('.navbar-toggler');
        if (navbarToggler) {
            navbarToggler.addEventListener('click', function() {
                // Reset dropdown state ketika navbar di-toggle
                setTimeout(() => {
                    if (!document.querySelector('.navbar-collapse').classList.contains(
                            'show')) {
                        dropdownMenu.classList.remove('show');
                        kategoriDropdown.setAttribute('aria-expanded', 'false');
                    }
                }, 100);
            });
        }

        // Handle resize untuk reset state
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992) {
                // Di desktop, biarkan Bootstrap handle
                dropdownMenu.classList.remove('show');
            }
        });

        console.log('Dropdown kategori handler loaded - Page:', isKategoriPage ? 'Kategori' : 'Other');
    });

    // Fallback: Inisialisasi Bootstrap dropdown secara manual
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
        const dropdownList = dropdownElementList.map(function(dropdownToggleEl) {
            return new bootstrap.Dropdown(dropdownToggleEl);
        });
    });
</script>

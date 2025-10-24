<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CELVION | The Future is Now</title>
    {{-- Tailwind CSS & Font --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    {{-- Ikon & Animasi --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    {{-- SweetAlert2 untuk Notifikasi --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" href="{{ asset('logo/favicon.ico') }}" type="image/x-ico">
    
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #0d1117;
            color: #e5e7eb;
            overflow-x: hidden;
        }

        .navbar-scrolled {
            background-color: rgba(13, 17, 23, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #30363d;
        }
        
        .hero-bg {
            background-image: 
                radial-gradient(at 20% 25%, hsla(212, 92%, 58%, 0.15) 0px, transparent 50%),
                radial-gradient(at 80% 70%, hsla(262, 85%, 60%, 0.15) 0px, transparent 50%);
        }
        
        .text-glow {
            text-shadow: 0 0 8px rgba(99, 102, 241, 0.3);
        }

        .card-glow {
            background-color: #161b22;
            border: 1px solid #30363d;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .card-glow:hover {
            border-color: #4f46e5;
            box-shadow: 0 0 20px rgba(79, 70, 229, 0.2);
        }

        .btn-primary {
            transition: all 0.3s ease;
            box-shadow: 0 0 15px rgba(79, 70, 229, 0.2);
        }
        .btn-primary:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 0 25px rgba(79, 70, 229, 0.4);
        }
        
        .btn-secondary {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }
        
        .product-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            z-index: 10;
        }
        
        .product-badge.new {
            background-color: #4f46e5;
            color: white;
        }
        
        .product-badge.discount {
            background-color: #10b981;
            color: white;
        }
        
        .filter-btn {
            background-color: #161b22;
            border: 1px solid #30363d;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .filter-btn:hover, .filter-btn.active {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }
        
        .price-original {
            text-decoration: line-through;
            color: #9ca3af;
            font-size: 0.9rem;
        }
        
        .rating {
            color: #fbbf24;
        }
        
        .product-item {
            display: none;
            animation: fadeIn 0.5s ease-in-out;
        }
        
        .product-item.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .category-label {
            display: none;
        }
        
        .category-label.active {
            display: block;
        }

        /* Container untuk membatasi lebar maksimal */
        .container-max {
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Dropdown Styles */
        .dropdown-menu {
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 0.5rem;
            background-color: #1f2937;
            border: 1px solid #374151;
            border-radius: 0.75rem;
            padding: 0.5rem;
            min-width: 180px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            z-index: 50;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s ease;
        }

        .dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            color: #e5e7eb;
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 0.875rem;
        }

        .dropdown-item:hover {
            background-color: #374151;
            color: white;
        }

        .dropdown-item i {
            margin-right: 0.75rem;
            width: 16px;
            text-align: center;
        }

        .profile-btn {
            background: transparent;
            border: 1px solid #374151;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e5e7eb;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .profile-btn:hover {
            background-color: #374151;
            border-color: #4f46e5;
            color: white;
        }
    </style>
</head>
<body class="antialiased">

    <header id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">
        <div class="container-max px-4 sm:px-6">
            <div class="flex justify-between items-center h-20">
                <a href="#hero" class="text-xl font-black tracking-wider text-white">
                    C<span class="text-indigo-500">N</span>
                </a>
                <nav class="hidden md:flex items-center space-x-8 text-sm font-semibold text-gray-400">
                    <a href="#produk" class="hover:text-white transition">Produk</a>
                    <a href="#brands" class="hover:text-white transition">Brand</a>
                    <a href="#promo" class="hover:text-white transition">Promo</a>
                    <a href="#testimoni" class="hover:text-white transition">Testimoni</a>
                </nav>
                <div class="flex items-center space-x-4">
                    <div class="relative hidden sm:block">
                        <input type="text" placeholder="Cari produk..." class="bg-gray-800 text-white px-4 py-2 rounded-full text-sm w-40 md:w-64 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <i class="bi bi-search absolute right-3 top-2.5 text-gray-400"></i>
                    </div>
                    
                    <!-- Profile Dropdown -->
                    <div class="relative">
                        <button class="profile-btn" id="profileDropdownBtn">
                            <i class="bi bi-person text-lg"></i>
                        </button>
                        <div class="dropdown-menu" id="profileDropdown">
                            <a href="{{ route('login') }}" class="dropdown-item">
                                <i class="bi bi-box-arrow-in-right"></i>
                                Masuk
                            </a>
                            <a href="{{ route('register') }}" class="dropdown-item">
                                <i class="bi bi-person-plus"></i>
                                Daftar
                            </a>
                            {{-- <div class="border-t border-gray-600 my-2"></div>
                            <a href="#" class="dropdown-item">
                                <i class="bi bi-info-circle"></i>
                                Tentang Kami
                            </a>
                            <a href="#" class="dropdown-item">
                                <i class="bi bi-headset"></i>
                                Bantuan
                            </a> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section id="hero" class="pt-40 pb-24 text-center flex items-center min-h-screen hero-bg">
            <div class="container-max px-4 sm:px-6">
                <div class="max-w-4xl mx-auto">
                    <div data-aos="fade-up">
                        <span class="text-sm font-semibold tracking-widest text-indigo-400">SELAMAT DATANG DI MASA DEPAN</span>
                        <h1 class="text-4xl md:text-6xl font-extrabold text-white my-4 leading-tight text-glow">
                            Era Baru Teknologi Dimulai Di Sini.
                        </h1>
                        <p class="text-lg max-w-2xl mx-auto text-gray-400 mb-10">
                           Temukan koleksi handphone dan aksesori terbaru dengan teknologi canggih untuk melampaui batas imajinasi Anda.
                        </p>
                        <a href="pelanggan/dashboard" class="btn-primary bg-indigo-600 text-white font-bold px-8 py-4 rounded-full text-lg">
                            Jelajahi Sekarang
                        </a>
                    </div>
                    <div data-aos="fade-up" data-aos-delay="200" class="mt-20">
                        <img src="https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1480&q=80" alt="Smartphone Collection" class="mx-auto rounded-2xl shadow-2xl shadow-indigo-900/20 w-full max-w-4xl">
                    </div>
                </div>
            </div>
        </section>

        <section id="brands" class="py-16 bg-gray-900/50">
            <div class="container-max px-4 sm:px-6">
                <div class="text-center mb-12" data-aos="fade-up">
                    <h2 class="text-2xl font-bold text-white">Brand Terpopuler</h2>
                    <p class="text-gray-400 mt-2">Pilih dari brand gadget dan aksesori terkemuka di dunia</p>
                </div>
                <div class="flex flex-wrap justify-center gap-6 md:gap-8" data-aos="fade-up" data-aos-delay="100">
                    <div class="text-center">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="bi bi-phone text-3xl text-indigo-400"></i>
                        </div>
                        <span class="text-white font-medium">Samsung</span>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="bi bi-phone text-3xl text-indigo-400"></i>
                        </div>
                        <span class="text-white font-medium">Apple</span>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="bi bi-phone text-3xl text-indigo-400"></i>
                        </div>
                        <span class="text-white font-medium">Xiaomi</span>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="bi bi-phone text-3xl text-indigo-400"></i>
                        </div>
                        <span class="text-white font-medium">Oppo</span>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="bi bi-phone text-3xl text-indigo-400"></i>
                        </div>
                        <span class="text-white font-medium">Vivo</span>
                    </div>
                </div>
            </div>
        </section>

        <section id="produk" class="py-24">
            <div class="container-max px-4 sm:px-6">
                <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                    <h2 class="text-3xl font-bold text-white">Koleksi Produk</h2>
                    <p class="text-gray-400 mt-4">Pilih gadget dan aksesori terbaik dengan teknologi terkini untuk kebutuhan Anda.</p>
                </div>
                
                <div class="flex flex-wrap justify-center gap-3 mb-12" data-aos="fade-up">
                    <button class="filter-btn active" data-category="all">Semua</button>
                    <button class="filter-btn" data-category="smartphone">Smartphone</button>
                    <button class="filter-btn" data-category="headset">Headset</button>
                    <button class="filter-btn" data-category="earphone">Earphone</button>
                    <button class="filter-btn" data-category="aksesoris">Aksesoris</button>
                </div>
                
                <div id="product-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <!-- Smartphone Products -->
                    <div class="product-item active" data-category="smartphone" data-aos="fade-up">
                        <div class="card-glow rounded-2xl overflow-hidden relative flex flex-col h-full">
                            <div class="product-badge new">BARU</div>
                            <div class="h-64 p-6 flex items-center justify-center">
                                <img src="https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" class="h-full w-full object-contain" alt="Samsung Galaxy S23 Ultra">
                            </div>
                            <div class="p-6 flex flex-col flex-grow">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-xl font-bold text-white">Samsung Galaxy S23 Ultra</h3>
                                    <div class="rating flex items-center">
                                        <i class="bi bi-star-fill"></i>
                                        <span class="ml-1 text-sm">4.7</span>
                                    </div>
                                </div>
                                <p class="text-gray-400 text-sm mb-4 flex-grow">Snapdragon 8 Gen 2, 12GB RAM, 512GB, Kamera 200MP</p>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-2xl font-bold text-indigo-400">Rp18.999.000</p>
                                        <p class="price-original">Rp21.999.000</p>
                                    </div>
                                    <button onclick="alertLogin()" class="btn-secondary text-white font-semibold w-full py-3 rounded-lg text-sm flex items-center justify-center gap-2">
                                        <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="product-item active" data-category="smartphone" data-aos="fade-up" data-aos-delay="100">
                        <div class="card-glow rounded-2xl overflow-hidden relative flex flex-col h-full">
                            <div class="product-badge discount">DISKON 15%</div>
                            <div class="h-64 p-6 flex items-center justify-center">
                                <img src="https://images.unsplash.com/photo-1592750475338-74b7b21085ab?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" class="h-full w-full object-contain" alt="iPhone 14 Pro Max">
                            </div>
                            <div class="p-6 flex flex-col flex-grow">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-xl font-bold text-white">iPhone 14 Pro Max</h3>
                                    <div class="rating flex items-center">
                                        <i class="bi bi-star-fill"></i>
                                        <span class="ml-1 text-sm">4.9</span>
                                    </div>
                                </div>
                                <p class="text-gray-400 text-sm mb-4 flex-grow">A16 Bionic, 6GB RAM, 1TB, Dynamic Island</p>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-2xl font-bold text-indigo-400">Rp22.499.000</p>
                                        <p class="price-original">Rp26.499.000</p>
                                    </div>
                                    <button onclick="alertLogin()" class="btn-secondary text-white font-semibold w-full py-3 rounded-lg text-sm flex items-center justify-center gap-2">
                                        <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="product-item active" data-category="smartphone" data-aos="fade-up" data-aos-delay="200">
                        <div class="card-glow rounded-2xl overflow-hidden relative flex flex-col h-full">
                            <div class="h-64 p-6 flex items-center justify-center">
                                <img src="https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" class="h-full w-full object-contain" alt="Xiaomi 13 Pro">
                            </div>
                            <div class="p-6 flex flex-col flex-grow">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-xl font-bold text-white">Xiaomi 13 Pro</h3>
                                    <div class="rating flex items-center">
                                        <i class="bi bi-star-fill"></i>
                                        <span class="ml-1 text-sm">4.8</span>
                                    </div>
                                </div>
                                <p class="text-gray-400 text-sm mb-4 flex-grow">Snapdragon 8 Gen 2, 12GB RAM, 256GB, Leica Kamera</p>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-2xl font-bold text-indigo-400">Rp12.999.000</p>
                                    </div>
                                    <button onclick="alertLogin()" class="btn-secondary text-white font-semibold w-full py-3 rounded-lg text-sm flex items-center justify-center gap-2">
                                        <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="product-item active" data-category="smartphone" data-aos="fade-up" data-aos-delay="300">
                        <div class="card-glow rounded-2xl overflow-hidden relative flex flex-col h-full">
                            <div class="product-badge discount">DISKON 10%</div>
                            <div class="h-64 p-6 flex items-center justify-center">
                                <img src="https://images.unsplash.com/photo-1598327105854-c8674faddf74?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" class="h-full w-full object-contain" alt="Oppo Find X5 Pro">
                            </div>
                            <div class="p-6 flex flex-col flex-grow">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-xl font-bold text-white">Oppo Find X5 Pro</h3>
                                    <div class="rating flex items-center">
                                        <i class="bi bi-star-fill"></i>
                                        <span class="ml-1 text-sm">4.6</span>
                                    </div>
                                </div>
                                <p class="text-gray-400 text-sm mb-4 flex-grow">Snapdragon 8 Gen 1, 12GB RAM, 256GB, Hasselblad Kamera</p>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-2xl font-bold text-indigo-400">Rp10.799.000</p>
                                        <p class="price-original">Rp11.999.000</p>
                                    </div>
                                    <button onclick="alertLogin()" class="btn-secondary text-white font-semibold w-full py-3 rounded-lg text-sm flex items-center justify-center gap-2">
                                        <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Headset Products -->
                    <div class="product-item active" data-category="headset" data-aos="fade-up">
                        <div class="card-glow rounded-2xl overflow-hidden relative flex flex-col h-full">
                            <div class="product-badge new">BARU</div>
                            <div class="h-64 p-6 flex items-center justify-center">
                                <img src="https://images.unsplash.com/photo-1583394838336-acd977736f90?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" class="h-full w-full object-contain" alt="Sony WH-1000XM5">
                            </div>
                            <div class="p-6 flex flex-col flex-grow">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-xl font-bold text-white">Sony WH-1000XM5</h3>
                                    <div class="rating flex items-center">
                                        <i class="bi bi-star-fill"></i>
                                        <span class="ml-1 text-sm">4.9</span>
                                    </div>
                                </div>
                                <p class="text-gray-400 text-sm mb-4 flex-grow">Noise Cancelling Premium, Baterai 30 jam</p>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-2xl font-bold text-indigo-400">Rp4.999.000</p>
                                    </div>
                                    <button onclick="alertLogin()" class="btn-secondary text-white font-semibold w-full py-3 rounded-lg text-sm flex items-center justify-center gap-2">
                                        <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="product-item active" data-category="headset" data-aos="fade-up" data-aos-delay="100">
                        <div class="card-glow rounded-2xl overflow-hidden relative flex flex-col h-full">
                            <div class="product-badge discount">DISKON 20%</div>
                            <div class="h-64 p-6 flex items-center justify-center">
                                <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" class="h-full w-full object-contain" alt="Bose QuietComfort 45">
                            </div>
                            <div class="p-6 flex flex-col flex-grow">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-xl font-bold text-white">Bose QuietComfort 45</h3>
                                    <div class="rating flex items-center">
                                        <i class="bi bi-star-fill"></i>
                                        <span class="ml-1 text-sm">4.7</span>
                                    </div>
                                </div>
                                <p class="text-gray-400 text-sm mb-4 flex-grow">Noise Cancelling, Sound Balance, Baterai 24 jam</p>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-2xl font-bold text-indigo-400">Rp3.999.000</p>
                                        <p class="price-original">Rp4.999.000</p>
                                    </div>
                                    <button onclick="alertLogin()" class="btn-secondary text-white font-semibold w-full py-3 rounded-lg text-sm flex items-center justify-center gap-2">
                                        <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Earphone Products -->
                    <div class="product-item active" data-category="earphone" data-aos="fade-up" data-aos-delay="200">
                        <div class="card-glow rounded-2xl overflow-hidden relative flex flex-col h-full">
                            <div class="product-badge new">BARU</div>
                            <div class="h-64 p-6 flex items-center justify-center">
                                <img src="https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" class="h-full w-full object-contain" alt="AirPods Pro 2">
                            </div>
                            <div class="p-6 flex flex-col flex-grow">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-xl font-bold text-white">AirPods Pro 2</h3>
                                    <div class="rating flex items-center">
                                        <i class="bi bi-star-fill"></i>
                                        <span class="ml-1 text-sm">4.8</span>
                                    </div>
                                </div>
                                <p class="text-gray-400 text-sm mb-4 flex-grow">Active Noise Cancellation, Spatial Audio, MagSafe Charging</p>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-2xl font-bold text-indigo-400">Rp3.499.000</p>
                                    </div>
                                    <button onclick="alertLogin()" class="btn-secondary text-white font-semibold w-full py-3 rounded-lg text-sm flex items-center justify-center gap-2">
                                        <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="product-item active" data-category="earphone" data-aos="fade-up" data-aos-delay="300">
                        <div class="card-glow rounded-2xl overflow-hidden relative flex flex-col h-full">
                            <div class="h-64 p-6 flex items-center justify-center">
                                <img src="https://images.unsplash.com/photo-1600294037681-c80b4cb5b434?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" class="h-full w-full object-contain" alt="Samsung Galaxy Buds2 Pro">
                            </div>
                            <div class="p-6 flex flex-col flex-grow">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-xl font-bold text-white">Samsung Galaxy Buds2 Pro</h3>
                                    <div class="rating flex items-center">
                                        <i class="bi bi-star-fill"></i>
                                        <span class="ml-1 text-sm">4.6</span>
                                    </div>
                                </div>
                                <p class="text-gray-400 text-sm mb-4 flex-grow">360 Audio, ANC, Bixby Voice Wake Up</p>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-2xl font-bold text-indigo-400">Rp2.299.000</p>
                                    </div>
                                    <button onclick="alertLogin()" class="btn-secondary text-white font-semibold w-full py-3 rounded-lg text-sm flex items-center justify-center gap-2">
                                        <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Aksesoris Products -->
                    <div class="product-item active" data-category="aksesoris" data-aos="fade-up">
                        <div class="card-glow rounded-2xl overflow-hidden relative flex flex-col h-full">
                            <div class="product-badge discount">DISKON 15%</div>
                            <div class="h-64 p-6 flex items-center justify-center">
                                <img src="https://images.unsplash.com/photo-1601593346740-925612772716?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" class="h-full w-full object-contain" alt="Casing iPhone 14 Pro">
                            </div>
                            <div class="p-6 flex flex-col flex-grow">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-xl font-bold text-white">Casing iPhone 14 Pro</h3>
                                    <div class="rating flex items-center">
                                        <i class="bi bi-star-fill"></i>
                                        <span class="ml-1 text-sm">4.5</span>
                                    </div>
                                </div>
                                <p class="text-gray-400 text-sm mb-4 flex-grow">Silicone Case Original Apple, MagSafe Compatible</p>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-2xl font-bold text-indigo-400">Rp799.000</p>
                                        <p class="price-original">Rp949.000</p>
                                    </div>
                                    <button onclick="alertLogin()" class="btn-secondary text-white font-semibold w-full py-3 rounded-lg text-sm flex items-center justify-center gap-2">
                                        <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="product-item active" data-category="aksesoris" data-aos="fade-up" data-aos-delay="100">
                        <div class="card-glow rounded-2xl overflow-hidden relative flex flex-col h-full">
                            <div class="h-64 p-6 flex items-center justify-center">
                                <img src="https://images.unsplash.com/photo-1609592810794-3cbcb1ef9c98?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" class="h-full w-full object-contain" alt="Wireless Charger">
                            </div>
                            <div class="p-6 flex flex-col flex-grow">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-xl font-bold text-white">Wireless Charger Pad</h3>
                                    <div class="rating flex items-center">
                                        <i class="bi bi-star-fill"></i>
                                        <span class="ml-1 text-sm">4.3</span>
                                    </div>
                                </div>
                                <p class="text-gray-400 text-sm mb-4 flex-grow">Fast Charging 15W, Compatible dengan semua smartphone</p>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-2xl font-bold text-indigo-400">Rp349.000</p>
                                    </div>
                                    <button onclick="alertLogin()" class="btn-secondary text-white font-semibold w-full py-3 rounded-lg text-sm flex items-center justify-center gap-2">
                                        <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-12" data-aos="fade-up">
                    <button class="btn-secondary text-white font-semibold px-8 py-3 rounded-full">
                        Lihat Lebih Banyak
                    </button>
                </div>
            </div>
        </section>

        <section id="promo" class="py-24 bg-gray-900/50">
            <div class="container-max px-4 sm:px-6">
                <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                    <h2 class="text-3xl font-bold text-white">Promo Spesial</h2>
                    <p class="text-gray-400 mt-4">Jangan lewatkan penawaran menarik untuk pembelian gadget dan aksesori favorit Anda</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-6 md:p-8" data-aos="fade-right">
                        <h3 class="text-xl md:text-2xl font-bold text-white mb-4">Cashback hingga Rp 1.000.000</h3>
                        <p class="text-indigo-100 mb-6">Dapatkan cashback langsung untuk pembelian smartphone flagship dengan kartu kredit partner.</p>
                        <button class="bg-white text-indigo-600 font-semibold px-6 py-2 rounded-full">Lihat Syarat</button>
                    </div>
                    
                    <div class="bg-gradient-to-r from-cyan-600 to-blue-600 rounded-2xl p-6 md:p-8" data-aos="fade-left">
                        <h3 class="text-xl md:text-2xl font-bold text-white mb-4">Gratis Aksesoris Premium</h3>
                        <p class="text-cyan-100 mb-6">Beli smartphone pilihan dan dapatkan casing premium & screen protector gratis.</p>
                        <button class="bg-white text-cyan-600 font-semibold px-6 py-2 rounded-full">Lihat Produk</button>
                    </div>
                </div>
            </div>
        </section>

        <section id="testimoni" class="py-24">
            <div class="container-max px-4 sm:px-6">
                <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                    <h2 class="text-3xl font-bold text-white">Apa Kata Pelanggan Kami</h2>
                    <p class="text-gray-400 mt-4">Pengalaman nyata dari pelanggan yang telah berbelanja di GADGETSTORE</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="card-glow rounded-2xl p-6" data-aos="fade-up">
                        <div class="rating flex mb-4">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <p class="text-gray-300 mb-6">"Pengiriman cepat, produk original, dan harga bersaing. Saya puas berbelanja di GADGETSTORE!"</p>
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-indigo-500 rounded-full flex items-center justify-center text-white font-bold mr-4">AR</div>
                            <div>
                                <p class="font-semibold text-white">Ahmad Rizki</p>
                                <p class="text-gray-400 text-sm">Pembeli Samsung S23 Ultra</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-glow rounded-2xl p-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="rating flex mb-4">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-half"></i>
                        </div>
                        <p class="text-gray-300 mb-6">"Pelayanan customer service sangat membantu. Produk sesuai deskripsi dan packing aman."</p>
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-pink-500 rounded-full flex items-center justify-center text-white font-bold mr-4">SD</div>
                            <div>
                                <p class="font-semibold text-white">Sari Dewi</p>
                                <p class="text-gray-400 text-sm">Pembeli iPhone 14 Pro</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-glow rounded-2xl p-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="rating flex mb-4">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <p class="text-gray-300 mb-6">"Garansi resmi dan proses klaim mudah. Recommended untuk beli gadget high-end!"</p>
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-cyan-500 rounded-full flex items-center justify-center text-white font-bold mr-4">BW</div>
                            <div>
                                <p class="font-semibold text-white">Budi Wibowo</p>
                                <p class="text-gray-400 text-sm">Pembeli Xiaomi 13 Pro</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-24">
            <div class="container-max px-4 sm:px-6">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 md:p-12 text-center" data-aos="zoom-in">
                    <h2 class="text-2xl md:text-3xl font-bold text-white mb-4">Siap Menjadi Bagian dari Masa Depan?</h2>
                    <p class="text-indigo-200 max-w-2xl mx-auto mb-8">Buat akun untuk mendapatkan akses ke penawaran eksklusif, pembaruan produk, dan layanan prioritas.</p>
                    <a href="{{ route('register') }}" class="bg-white text-indigo-600 font-bold px-6 md:px-8 py-3 rounded-full text-lg hover:bg-gray-200 transition-transform hover:scale-105 shadow-lg inline-block">
                        Buat Akun Gratis
                    </a>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-gray-800 pt-16 pb-8">
        <div class="container-max px-4 sm:px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-8 text-sm">
                <div>
                    <h3 class="font-semibold text-white mb-4">Toko</h3>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="#" class="hover:text-white">Smartphone</a></li>
                        <li><a href="#" class="hover:text-white">Aksesoris</a></li>
                        <li><a href="#" class="hover:text-white">Headset & Earphone</a></li>
                    </ul>
                </div>
                <div>
                     <h3 class="font-semibold text-white mb-4">Layanan</h3>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="#" class="hover:text-white">Trade-In</a></li>
                        <li><a href="#" class="hover:text-white">Servis</a></li>
                        <li><a href="#" class="hover:text-white">Cicilan</a></li>
                    </ul>
                </div>
                 <div>
                     <h3 class="font-semibold text-white mb-4">Perusahaan</h3>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="#" class="hover:text-white">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-white">Kontak</a></li>
                        <li><a href="#" class="hover:text-white">Karir</a></li>
                    </ul>
                </div>
                <div class="col-span-2 md:col-span-1">
                     <h3 class="font-semibold text-white mb-4">Ikuti Kami</h3>
                    <div class="flex space-x-4 text-2xl">
                        <a href="#" class="text-gray-400 hover:text-white"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 mt-8 flex flex-col sm:flex-row justify-between items-center text-xs text-gray-500">
                <p>&copy; {{ date('Y') }} GADGETSTORE. All Rights Reserved.</p>
                <div class="flex space-x-6 mt-4 sm:mt-0">
                    <a href="#" class="hover:text-white">Syarat & Ketentuan</a>
                    <a href="#" class="hover:text-white">Kebijakan Privasi</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 50,
        });

        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 20) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });

        // Filter button functionality
        document.querySelectorAll('.filter-btn').forEach(button => {
            button.addEventListener('click', function() {
                const category = this.getAttribute('data-category');
                
                // Update active button
                document.querySelectorAll('.filter-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                this.classList.add('active');
                
                // Show/hide products
                document.querySelectorAll('.product-item').forEach(item => {
                    if (category === 'all' || item.getAttribute('data-category') === category) {
                        item.classList.add('active');
                    } else {
                        item.classList.remove('active');
                    }
                });
            });
        });

        // Profile dropdown functionality
        const profileDropdownBtn = document.getElementById('profileDropdownBtn');
        const profileDropdown = document.getElementById('profileDropdown');

        profileDropdownBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            profileDropdown.classList.remove('show');
        });

        // Prevent dropdown from closing when clicking inside
        profileDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        function alertLogin() {
            Swal.fire({
                icon: 'info',
                title: 'Akses Terbatas',
                text: 'Silakan masuk ke akun Anda untuk melanjutkan.',
                confirmButtonText: 'Login Sekarang',
                background: '#161b22',
                color: '#e5e7eb',
                confirmButtonColor: '#4f46e5',
                showCancelButton: true,
                cancelButtonText: 'Nanti Saja',
                customClass: {
                    popup: 'rounded-2xl border border-gray-700'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('login') }}";
                }
            })
        }
    </script>
</body>
</html>
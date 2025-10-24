{{-- KODE 100% LENGKAP - FUNGSI DIUBAH MENJADI "LIHAT DETAIL" --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>GADGETSTORE | Inovasi dalam Genggaman Anda</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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
            --warning: #f59e0b; /* Amber-500 */
        }
        * { font-family: 'Poppins', sans-serif; }
        body { background-color: var(--body-bg); color: var(--dark); }

        /* --- Utilities --- */
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

        /* --- Hero Banner --- */
        .hero-banner { background: linear-gradient(135deg, #1e293b, #334155); border-radius: 1.5rem; }
        .hero-banner h1 { font-weight: 800; }

        /* --- Product Card (NEW DESIGN) --- */
        .product-card {
            background: var(--card-bg); border-radius: 1rem;
            border: 1px solid var(--border-color);
            transition: transform .2s ease, box-shadow .2s ease;
            display: flex; flex-direction: column; overflow: hidden; position: relative;
        }
        .product-card:hover { transform: translateY(-8px); box-shadow: 0 1rem 2rem rgba(30, 41, 59, 0.1); }
        .product-card .img-wrapper { 
            height: 200px; padding: 1.25rem; background-color: #f8fafc;
            display:flex; align-items:center; justify-content:center;
        }
        .product-card .product-img { max-width: 100%; max-height: 100%; object-fit: contain; transition: transform .3s ease; }
        .product-card:hover .product-img { transform: scale(1.05); }
        .product-card .card-body { padding: 1rem 1.25rem; flex: 1; display: flex; flex-direction: column; }
        .product-card .product-category { font-size: 0.75rem; font-weight: 500; color: var(--primary); background-color: var(--primary-light); padding: 0.2rem 0.6rem; border-radius: 6px; }
        .product-card .product-title { font-size: 1rem; font-weight: 600; color: var(--dark); line-height: 1.4; }
        .product-card .product-rating { color: var(--warning); font-size: 0.9rem; }
        .product-card .product-price { font-size: 1.5rem; color: var(--primary); font-weight: 700; }
        
        /* --- Filter Pills --- */
        .filter-pills .nav-link {
            border: 1px solid var(--border-color); border-radius: 99px;
            padding: 0.5rem 1.25rem; background: var(--card-bg); color: var(--dark);
            font-weight: 500; font-size: 0.9rem; margin-right: 0.75rem; margin-bottom: 0.5rem;
            transition: all .2s ease;
        }
        .filter-pills .nav-link.active { background: var(--primary); color: #fff; border-color: var(--primary); }
        .filter-pills .nav-link:hover:not(.active) { background-color: var(--body-bg); border-color: var(--secondary); }
        .category-count { font-size: 0.75rem; background-color: rgba(0,0,0,0.1); padding: 0.1rem 0.4rem; border-radius: 8px; margin-left: 0.5rem; }

        /* --- Empty State --- */
        .empty-state { text-align:center; padding: 4rem 1rem; border-radius:1rem; background:var(--card-bg); border: 2px dashed var(--border-color); }
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
                    <input id="search-input" type="search" class="form-control" placeholder="Cari gadget impianmu..." aria-label="Search">
                </div>
            </div>
            <div class="header-actions d-flex align-items-center ms-3">
                <a class="btn-icon" href="{{ route('cart.show') }}">
                    <i class="bi bi-cart3"></i>
                </a>
                @auth
                    <div class="dropdown ms-2">
                        <button class="btn-icon" type="button" data-bs-toggle="dropdown"><i class="bi bi-person-circle"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end mt-2">
                            @if(auth()->user()->is_admin)
                                <li><a class="dropdown-item" href="{{-- route('admin.dashboard') --}}">Admin Dashboard</a></li>
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

<div class="container my-5">
    <section class="hero-banner p-5 mb-5 text-white">
        <div class="row align-items-center">
            <div class="col-md-7">
                <h1 class="display-5">Teknologi Terbaru, Harga Terbaik.</h1>
                <p class="lead text-white-50">Temukan smartphone, laptop, dan aksesoris original dengan garansi resmi. Dapatkan penawaran eksklusif hanya di GADGETSTORE.</p>
            </div>
            <div class="col-md-5 d-none d-md-block text-center">
                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='180' height='180' fill='rgba(255,255,255,0.1)'%3E%3Cpath d='M6.5 2h11a2.5 2.5 0 0 1 2.5 2.5v15a2.5 2.5 0 0 1-2.5 2.5h-11a2.5 2.5 0 0 1-2.5-2.5v-15A2.5 2.5 0 0 1 6.5 2zM6 4.5v15a.5.5 0 0 0 .5.5h11a.5.5 0 0 0 .5-.5v-15a.5.5 0 0 0-.5-.5h-11a.5.5 0 0 0-.5.5zM12 20a1 1 0 1 1 0-2 1 1 0 0 1 0 2z'/%3E%3C/svg%3E" alt="Phone Icon">
            </div>
        </div>
    </section>
    
    <section>
        <nav class="filter-pills nav nav-pills flex-nowrap overflow-x-auto pb-2 mb-4">
            <a class="nav-link active" href="#" data-category="all">Semua</a>
            <a class="nav-link" href="#" data-category="HP">HP</a>
            <a class="nav-link" href="#" data-category="Tab">Tab</a>
            <a class="nav-link" href="#" data-category="Laptop">Laptop</a>
            <a class="nav-link" href="#" data-category="Accessories">Aksesoris</a>
        </nav>
        <div id="productsGrid" class="row g-4">
            @forelse ($products as $product)
                <div class="col-6 col-md-4 col-lg-3 product-item" data-category="{{ $product->category ?? 'Lainnya' }}" data-name="{{ strtolower($product->name) }}">
                    {{-- [DIUBAH] Seluruh kartu produk sekarang menjadi link ke halaman detail --}}
                    <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none">
                        <div class="product-card h-100">
                            <div class="img-wrapper">
                                <img src="{{ asset('storage/products/'.$product->image) }}" 
                                     class="product-img" 
                                     alt="{{ $product->name }}"
                                     onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23e0e7ff\' stroke-width=\'1\'%3E%3Crect x=\'3\' y=\'3\' width=\'18\' height=\'18\' rx=\'2\'/%3E%3Ccircle cx=\'8.5\' cy=\'8.5\' r=\'1.5\'/%3E%3Cpath d=\'M21 15l-5-5L5 21\'/%3E%3C/svg%3E';">
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="product-category">{{ $product->category ?? 'Lainnya' }}</span>
                                    <div class="product-rating">
                                        <i class="bi bi-star-fill"></i>
                                        <span>4.8</span>
                                    </div>
                                </div>
                                <h5 class="product-title mb-2">{{ $product->name }}</h5>
                                
                                <div class="mt-auto pt-2">
                                    <p class="product-price mb-3">Rp{{ number_format($product->price, 0, ',', '.') }}</p>
                                    {{-- [DIUBAH] Tombol diubah menjadi "Lihat Detail" dan tidak membuka modal --}}
                                    <div class="btn btn-outline-primary w-100 fw-semibold">
                                        <i class="bi bi-eye-fill"></i> Lihat Detail
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12">
                    <div class="empty-state">
                        <i class="bi bi-box-seam fs-1 text-secondary"></i>
                        <h5 class="mt-3">Oops! Belum Ada Produk</h5>
                        <p class="text-secondary">Silakan kembali lagi nanti untuk melihat koleksi terbaru kami.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // --- Global Variables ---
    const filterPills = document.querySelectorAll('.filter-pills .nav-link');
    const productItems = document.querySelectorAll('.product-item');
    const productsGrid = document.getElementById('productsGrid');
    const searchInput = document.getElementById('search-input');
    
    const emptyStateHTML = `<div class="col-12"><div class="empty-state"><i class="bi bi-search fs-1 text-secondary"></i><h5 class="mt-3">Produk Tidak Ditemukan</h5><p class="text-secondary">Coba gunakan kata kunci atau filter lain.</p></div></div>`;
    
    // --- Core Functions ---
    function applyFilters() {
        const activeCategoryEl = document.querySelector('.filter-pills .nav-link.active');
        if (!activeCategoryEl) return; 

        const activeCategory = activeCategoryEl.dataset.category.toLowerCase();
        const searchTerm = searchInput.value.trim().toLowerCase();
        let hasVisibleItems = false;
        
        productItems.forEach(item => {
            const itemCategory = item.dataset.category.toLowerCase();
            const itemName = item.dataset.name.toLowerCase();
            const showCategory = activeCategory === 'all' || itemCategory === activeCategory;
            const showSearch = !searchTerm || itemName.includes(searchTerm);
            
            if (showCategory && showSearch) {
                item.style.display = ''; hasVisibleItems = true;
            } else {
                item.style.display = 'none';
            }
        });
        
        let emptyEl = productsGrid.querySelector('.empty-state-wrapper');
        if (emptyEl) emptyEl.remove();
        if (!hasVisibleItems && productItems.length > 0) {
            const tempDiv = document.createElement('div');
            tempDiv.className = 'col-12 empty-state-wrapper';
            tempDiv.innerHTML = emptyStateHTML;
            productsGrid.appendChild(tempDiv);
        }
    }

    function updateCategoryCounts() {
        const counts = { all: productItems.length };
        productItems.forEach(item => {
            const category = item.dataset.category;
            counts[category] = (counts[category] || 0) + 1;
        });

        filterPills.forEach(pill => {
            const category = pill.dataset.category;
            const count = counts[category] || (category === 'all' ? counts.all : 0);
            const countEl = pill.querySelector('.category-count') || document.createElement('span');
            countEl.className = 'category-count';
            countEl.textContent = count;
            pill.appendChild(countEl);
        });
    }

    // --- Event Listeners ---
    filterPills.forEach(pill => {
        pill.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector('.filter-pills .nav-link.active')?.classList.remove('active');
            this.classList.add('active');
            applyFilters();
        });
    });

    searchInput.addEventListener('input', applyFilters);

    // --- Initializations ---
    updateCategoryCounts();
});
</script>

</body>
</html>

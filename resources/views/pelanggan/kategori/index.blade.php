<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori - CELVION</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
        }

        .product-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            background: white;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .img-wrapper {
            position: relative;
            padding: 1.5rem;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            text-align: center;
        }

        .product-img {
            width: 100%;
            height: 200px;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-img {
            transform: scale(1.05);
        }

        .discount-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 2rem;
            font-size: 0.875rem;
            font-weight: 600;
            box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.3);
        }

        .card-body {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .product-category {
            font-size: 0.875rem;
            color: #64748b;
            font-weight: 500;
        }

        .product-rating {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            color: #f59e0b;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .product-title {
            color: #1e293b;
            font-weight: 600;
            font-size: 1.125rem;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-price {
            color: #4f46e5;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .btn-outline-purple {
            color: #4f46e5;
            border: 2px solid #4f46e5;
            background-color: transparent;
            font-weight: 600;
            transition: all 0.3s ease;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
        }

        .btn-outline-purple:hover {
            background-color: #4f46e5;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
        }

        .section-header {
            margin-bottom: 2rem;
        }

        .section-title {
            color: #1e293b;
            font-weight: 700;
            font-size: 2rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            border-radius: 2px;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 2rem;
        }

        .breadcrumb-item a {
            color: #4f46e5;
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb-item.active {
            color: #64748b;
        }

        .category-header {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: white;
            padding: 3rem 0;
            margin-bottom: 3rem;
            border-radius: 1rem;
        }

        .category-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .category-description {
            font-size: 1.125rem;
            opacity: 0.9;
            max-width: 600px;
        }

        .filter-section {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .filter-title {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 1rem;
            font-size: 1.125rem;
        }

        .filter-group {
            margin-bottom: 1.5rem;
        }

        .filter-label {
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-check-label {
            color: #64748b;
            font-weight: 400;
        }

        .form-check-input:checked {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }

        .sort-dropdown {
            min-width: 200px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    @include('components.navbar-pelanggan', [
        'orderCount' => $orderCount ?? 0,
        'wishlistCount' => $wishlistCount ?? 0,
        'cartCount' => $cartCount ?? 0,
    ])

    <main class="container py-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('pelanggan/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('kategori.index') }}">Kategori</a></li>
                <li class="breadcrumb-item active">{{ $brandFilter ?? ($typeFilter ?? 'Semua Produk') }}</li>
            </ol>
        </nav>

        <div class="category-header text-center">
            <div class="container">
                <h1 class="category-title">{{ $brandFilter ?? ($typeFilter ?? 'Semua Produk') }}</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <section class="mb-5">
                    <div class="section-header">
                        <h2 class="section-title">Produk {{ $brandFilter ?? ($typeFilter ?? '') }}</h2>

                        <div class="sorting">
                            <form action="{{ route('kategori.index') }}" method="GET" id="sort-form">
                                @if ($brandFilter)
                                    <input type="hidden" name="brand" value="{{ $brandFilter }}">
                                @endif
                                @if ($typeFilter)
                                    <input type="hidden" name="type" value="{{ $typeFilter }}">
                                @endif

                                <select class="form-select sort-dropdown" name="sort" onchange="this.form.submit()">
                                    <option value="newest" @if ($sortFilter == 'newest') selected @endif>Terbaru
                                    </option>
                                    <option value="price-low" @if ($sortFilter == 'price-low') selected @endif>Harga:
                                        Termurah</option>
                                    <option value="price-high" @if ($sortFilter == 'price-high') selected @endif>Harga:
                                        Termahal</option>
                                    {{-- Hapus opsi 'rating' karena tidak ada datanya di Model Product --}}
                                </select>
                            </form>
                        </div>
                    </div>

                    <div class="row g-4">
                        @forelse ($products as $product)
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none">
                                    <div class="product-card h-100">
                                        <div class="img-wrapper">
                                            @php
                                                $productImage = 'https://placehold.co/300x300/e2e8f0/4f46e5?text=N/A'; // Placeholder
                                                if ($product->image) {
                                                    $imageList = json_decode($product->image, true);
                                                    if (is_array($imageList) && !empty($imageList[0])) {
                                                        $productImage = asset('storage/' . $imageList[0]);
                                                    } elseif (is_string($product->image)) {
                                                        // Fallback jika bukan JSON
                                                        $productImage = asset('storage/' . $product->image);
                                                    }
                                                }
                                            @endphp
                                            <img src="{{ $productImage }}" class="product-img"
                                                alt="{{ $product->name }}">
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="product-category">{{ $product->category }}</span>

                                            </div>
                                            <h5 class="product-title mb-2">{{ $product->name }}</h5>

                                            <div class="mt-auto pt-2">
                                                <p class="product-price mb-3">
                                                    Rp{{ number_format($product->master_price, 0, ',', '.') }}</p>
                                                <div class="btn btn-outline-purple w-100 fw-semibold">
                                                    <i class="bi bi-eye-fill"></i> Lihat Detail
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center py-5 bg-light rounded-3">
                                    <i class="bi bi-search fs-1 text-muted"></i>
                                    <h4 class="mt-3 text-dark fw-semibold">Oops! Produk tidak ditemukan</h4>
                                    <p class="text-muted">Coba ubah kata kunci filter Anda.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <nav aria-label="Page navigation" class="mt-5">
                        {{ $products->links() }}
                    </nav>
                </section>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript untuk menangani filter dropdown
        document.querySelector('.sort-dropdown').addEventListener('change', function() {
            const selectedValue = this.value;
            console.log('Filter dipilih:', selectedValue);
        });
    </script>
</body>

</html>

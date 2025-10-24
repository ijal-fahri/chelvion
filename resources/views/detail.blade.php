{{-- KODE 100% LENGKAP - DESAIN BARU SEPERTI E-COMMERCE --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $product->name }} - GADGETSTORE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Poppins', 'sans-serif']
                    },
                    colors: {
                        primary: '#4f46e5',
                        'primary-light': '#eef2ff',
                        secondary: '#64748b',
                        dark: '#1e293b',
                        light: '#f1f5f9',
                        'card-bg': '#ffffff',
                        'border-color': '#e2e8f0',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-light font-sans">

    {{-- Header --}}
    <header class="bg-white/80 sticky top-0 z-40 backdrop-blur-sm border-b border-border-color">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <a class="font-extrabold text-2xl text-dark" href="{{ route('dashboard') }}">
                    <i class="bi bi-phone-vibrate-fill text-primary"></i> GADGETSTORE
                </a>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('cart.show') }}"
                        class="relative w-10 h-10 flex items-center justify-center bg-light rounded-full text-secondary hover:bg-primary-light hover:text-primary transition-colors">
                        <i class="bi bi-cart3 text-xl"></i>
                    </a>
                    @auth
                        <a href="#"
                            class="w-10 h-10 flex items-center justify-center bg-light rounded-full text-secondary hover:bg-primary-light hover:text-primary transition-colors">
                            <i class="bi bi-person-circle text-xl"></i>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-primary">Login</a>
                        <a href="{{ route('register') }}"
                            class="px-4 py-2 text-sm font-semibold text-white bg-primary rounded-full">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 my-8">
        {{-- Main Product Section --}}
        <div class="bg-card-bg p-4 md:p-6 rounded-2xl shadow-sm">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                {{-- Product Image --}}
                <div class="lg:col-span-2 flex justify-center items-center">
                    <div class="bg-gray-100 w-full aspect-square rounded-xl p-4">
                        <img id="main-product-image" src="{{ asset('storage/products/' . $product->image) }}"
                            alt="{{ $product->name }}" class="w-full h-full object-contain">
                    </div>
                </div>

                {{-- Product Details and Actions --}}
                <div class="lg:col-span-3">
                    <h1 class="text-2xl md:text-3xl font-bold text-dark mb-2">{{ $product->name }}</h1>
                    <div class="flex items-center space-x-4 mb-4 text-sm">
                        <div class="flex items-center text-amber-500">
                            <i class="bi bi-star-fill"></i>
                            <span class="ml-1 font-semibold text-gray-600">4.8</span>
                            <span class="text-gray-400 mx-2">|</span>
                            <span class="text-gray-600">2.5rb Terjual</span>
                        </div>
                    </div>

                    <div class="bg-light/80 rounded-lg p-4 mb-4">
                        <p id="product-price-display" class="text-3xl font-bold text-primary">
                            Rp{{ number_format($product->price, 0, ',', '.') }}
                        </p>
                    </div>

                    <form id="add-to-cart-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <div id="variants-container" class="space-y-4"></div>
                        <select name="variant_id" id="final-variant-id" class="hidden" required></select>

                        <div class="mt-4 pt-4 border-t border-border-color">
                            <div class="flex items-center justify-between">
                                <label for="quantity" class="text-md font-semibold text-dark">Jumlah</label>
                                <div class="flex items-center border border-border-color rounded-lg">
                                    <button type="button" onclick="updateQuantity(-1)"
                                        class="w-9 h-9 text-lg font-bold text-secondary hover:text-primary transition">-</button>
                                    <input type="number" name="quantity" id="quantity" value="1" min="1"
                                        class="w-14 h-9 text-center font-bold bg-transparent border-l border-r border-border-color focus:outline-none">
                                    <button type="button" onclick="updateQuantity(1)"
                                        class="w-9 h-9 text-lg font-bold text-secondary hover:text-primary transition">+</button>
                                </div>
                            </div>
                            <div class="text-right text-sm text-secondary mt-2">Stok Tersedia: <span id="stock-display"
                                    class="font-bold">0</span></div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" id="submit-button"
                                class="w-full bg-primary text-white font-bold py-3 rounded-lg hover:bg-indigo-700 transition-all duration-300 disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center justify-center">
                                <span class="button-text"><i class="bi bi-cart-plus-fill mr-2"></i>Tambah ke
                                    Keranjang</span>
                                <span
                                    class="button-spinner hidden animate-spin rounded-full h-5 w-5 border-b-2 border-white"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Description & Specifications --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <div class="lg:col-span-2 bg-card-bg p-6 rounded-2xl shadow-sm">
                <h3 class="text-xl font-bold text-dark mb-4 border-b pb-3">Spesifikasi & Deskripsi</h3>
                <div class="prose max-w-none text-secondary">
                    <h4 class="font-semibold text-dark">Spesifikasi Utama:</h4>
                    <ul class="list-disc pl-5 mb-6">
                        <li><strong>Kategori:</strong> {{ $product->category }}</li>
                        @if ($product->variants->isNotEmpty())
                            <li><strong>Pilihan RAM:</strong>
                                {{ $product->variants->pluck('ram')->unique()->join(', ') }}</li>
                            <li><strong>Pilihan Warna:</strong>
                                {{ $product->variants->pluck('color')->unique()->join(', ') }}</li>
                        @endif
                    </ul>
                    <h4 class="font-semibold text-dark">Deskripsi Lengkap:</h4>
                    <p>{{ $product->description }}</p>
                </div>
            </div>
        </div>

        {{-- Recommended Products --}}
        @if ($recommendedProducts->isNotEmpty())
            <div class="mt-10">
                <h2 class="text-2xl font-bold text-dark mb-4">Mungkin Kamu Suka</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-4">
                    @foreach ($recommendedProducts as $recProduct)
                        <a href="{{ route('products.show', $recProduct->id) }}"
                            class="block bg-card-bg rounded-lg shadow-sm overflow-hidden transform hover:-translate-y-1 transition-transform duration-300">
                            <div class="w-full h-32 sm:h-40 bg-gray-100 p-2">
                                <img src="{{ asset('storage/products/' . $recProduct->image) }}"
                                    alt="{{ $recProduct->name }}" class="w-full h-full object-contain">
                            </div>
                            <div class="p-3">
                                <h5 class="text-sm font-semibold text-dark truncate">{{ $recProduct->name }}</h5>
                                <p class="text-lg font-bold text-primary mt-1">
                                    Rp{{ number_format($recProduct->price, 0, ',', '.') }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const variantsData = @json($product->variants);
            const variantsContainer = document.getElementById('variants-container');
            const finalVariantSelect = document.getElementById('final-variant-id');
            const stockDisplay = document.getElementById('stock-display');
            const quantityInput = document.getElementById('quantity');
            const submitButton = document.getElementById('submit-button');
            const addToCartForm = document.getElementById('add-to-cart-form');
            const priceDisplay = document.getElementById('product-price-display');
            const basePrice = {{ $product->price }};
            let matchedVariant = null;

            function formatCurrency(value) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(value);
            }

            function renderVariants() {
                const grouped = variantsData.reduce((acc, v) => {
                    if (v.color) {
                        acc.color = acc.color || new Set();
                        acc.color.add(v.color);
                    }
                    if (v.ram) {
                        acc.ram = acc.ram || new Set();
                        acc.ram.add(v.ram);
                    }
                    return acc;
                }, {});

                let html = '';
                for (const key in grouped) {
                    html += `<div data-variant-group="${key}">
                                <h4 class="text-md font-semibold text-dark mb-2 capitalize">${key}</h4>
                                <div class="flex flex-wrap gap-2">`;

                    [...grouped[key]].forEach((value, index) => {
                        html += `
                            <div class="relative">
                                <input type="radio" id="${key}-${index}" name="${key}" value="${value}" class="peer absolute opacity-0">
                                <label for="${key}-${index}" class="block cursor-pointer select-none rounded-full px-4 py-2 text-sm font-semibold border-2 border-border-color peer-checked:bg-primary-light peer-checked:text-primary peer-checked:border-primary peer-disabled:bg-gray-100 peer-disabled:text-gray-400 peer-disabled:cursor-not-allowed">
                                    ${value}
                                </label>
                            </div>
                        `;
                    });
                    html += `   </div></div>`;
                }
                variantsContainer.innerHTML = html;
            }

            function updateUI() {
                const selected = {};
                const checkedRadios = document.querySelectorAll('#variants-container input[type="radio"]:checked');
                checkedRadios.forEach(radio => selected[radio.name] = radio.value);

                const totalVariantGroups = document.querySelectorAll('[data-variant-group]').length;
                const isFullySelected = checkedRadios.length === totalVariantGroups;

                matchedVariant = isFullySelected ? variantsData.find(v => Object.keys(selected).every(key => v[
                    key] === selected[key])) : null;

                if (matchedVariant) {
                    const stock = parseInt(matchedVariant.stock);
                    stockDisplay.textContent = stock;
                    quantityInput.max = stock;
                    if (parseInt(quantityInput.value) > stock) quantityInput.value = stock > 0 ? 1 : 0;
                    finalVariantSelect.innerHTML =
                        `<option value="${matchedVariant.id}" selected>${matchedVariant.id}</option>`;
                    priceDisplay.textContent = formatCurrency(matchedVariant.price);

                    const isAvailable = stock > 0;
                    submitButton.disabled = !isAvailable;
                    submitButton.querySelector('.button-text').innerHTML = isAvailable ?
                        '<i class="bi bi-cart-plus-fill mr-2"></i>Tambah ke Keranjang' : 'Stok Habis';
                } else {
                    stockDisplay.textContent = '-';
                    quantityInput.max = 1;
                    finalVariantSelect.innerHTML = '';
                    priceDisplay.textContent = formatCurrency(basePrice);
                    submitButton.disabled = true;
                    submitButton.querySelector('.button-text').innerHTML = 'Pilih Varian';
                }
            }

            window.updateQuantity = function(amount) {
                let currentVal = parseInt(quantityInput.value);
                let newVal = currentVal + amount;
                if (newVal < 1) newVal = 1;
                const maxStock = parseInt(quantityInput.max) || 1;
                if (newVal > maxStock && maxStock > 0) newVal = maxStock;
                quantityInput.value = newVal;
            };

            variantsContainer.addEventListener('change', updateUI);

            addToCartForm.addEventListener('submit', function(e) {
                e.preventDefault();

                if (!matchedVariant) {
                    Swal.fire('Peringatan', 'Silakan pilih semua opsi varian terlebih dahulu.', 'warning');
                    return;
                }

                const formData = new FormData();
                formData.append('_token', this.querySelector('input[name="_token"]').value);
                formData.append('product_id', this.querySelector('input[name="product_id"]').value);
                formData.append('variant_id', this.querySelector('select[name="variant_id"]').value);
                formData.append('quantity', this.querySelector('input[name="quantity"]').value);
                formData.append('color', matchedVariant.color);
                formData.append('ram', matchedVariant.ram);
                formData.append('price', matchedVariant.price);

                const buttonText = submitButton.querySelector('.button-text');
                const buttonSpinner = submitButton.querySelector('.button-spinner');
                submitButton.disabled = true;
                buttonText.classList.add('hidden');
                buttonSpinner.classList.remove('hidden');

                fetch("{{ route('cart.add') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json',
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: data.message
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error details:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Terjadi kesalahan koneksi. Silakan coba lagi.'
                        });
                    })
                    .finally(() => {
                        submitButton.disabled = false;
                        buttonText.classList.remove('hidden');
                        buttonSpinner.classList.add('hidden');
                    });
            });

            renderVariants();
            updateUI();
        });
    </script>
</body>

</html>

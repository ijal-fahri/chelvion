<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Transaksi Penjualan | CELVION</title>

    {{-- Tailwind CSS & jQuery --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- Ikon & Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

    {{-- Animasi AOS & SweetAlert2 --}}
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Swiper --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        .payment-method.selected {
            border-color: #4f46e5;
            background-color: #eef2ff;
            box-shadow: 0 0 0 2px #4f46e5;
            color: #3730a3;
            font-weight: 600;
        }

        .modal-overlay {
            transition: opacity 0.3s ease;
        }

        .modal-content {
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .condition-filter-btn.active {
            background-color: #4f46e5;
            color: white;
        }

        .variant-btn.selected {
            background-color: #4f46e5;
            color: white;
            border-color: #4f46e5;
        }

        .swiper-slide-thumb-active img {
            border: 2px solid #4f46e5;
        }

        .variant-image-indicator {
            position: absolute;
            top: 5px;
            left: 5px;
            background: #4f46e5;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .variant-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>

<body>
    <div class="relative min-h-screen md:flex">
        @include('kasir.partials.sidebar')

        <div class="flex-1 md:ml-64 flex flex-col min-w-0">
            <main class="flex-grow p-4 sm:p-6 lg:p-8">
                @include('kasir.partials.header', [
                    'title' => 'Transaksi Penjualan',
                    'subtitle' => 'Buat dan kelola transaksi penjualan baru.',
                ])

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {{-- Kolom Kiri: Daftar Produk --}}
                    <div class="lg:col-span-2" data-aos="fade-up">
                        <div class="bg-white p-6 rounded-2xl shadow-lg">
                            <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
                                <div class="flex border-2 border-gray-200 rounded-lg p-1 bg-gray-100 w-full md:w-auto">
                                    <button
                                        class="condition-filter-btn active font-semibold text-sm px-4 py-2 rounded-md w-full"
                                        data-condition="all">Semua</button>
                                    <button
                                        class="condition-filter-btn font-semibold text-sm px-4 py-2 rounded-md w-full"
                                        data-condition="Baru">Baru</button>
                                    <button
                                        class="condition-filter-btn font-semibold text-sm px-4 py-2 rounded-md w-full"
                                        data-condition="Second">Second</button>
                                </div>
                                <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
                                    <div class="relative flex-grow">
                                        <i
                                            class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                        <input type="text" id="search-product" placeholder="Cari produk..."
                                            class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                    <select id="category-filter"
                                        class="w-full sm:w-48 border rounded-lg focus:ring-2 focus:ring-indigo-500 px-3 py-2">
                                        <option value="all">Semua Kategori</option>
                                        <option value="Handphone">Handphone</option>
                                        <option value="Aksesori">Aksesori</option>
                                    </select>
                                </div>
                            </div>
                            <div id="product-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
                        </div>
                    </div>

                    {{-- Kolom Kanan: Detail Transaksi --}}
                    <div class="lg:col-span-1" data-aos="fade-up" data-aos-delay="100">
                        <div class="bg-white p-6 rounded-2xl shadow-lg sticky top-8">
                            <h4 class="text-xl font-semibold text-gray-800 border-b pb-3 mb-4">Buat Order</h4>
                            <div class="space-y-3">
                                <div><label for="customer-name" class="text-sm font-medium">Nama Pelanggan</label><input
                                        type="text" id="customer-name" placeholder="Masukkan nama"
                                        class="w-full mt-1 p-2 border rounded-lg" required></div>
                                <div><label for="customer-phone" class="text-sm font-medium">Nomor Telepon</label><input
                                        type="tel" id="customer-phone" placeholder="08xxxx"
                                        class="w-full mt-1 p-2 border rounded-lg" required></div>
                                <div><label for="customer-email" class="text-sm font-medium">Email
                                        (Opsional)</label><input type="email" id="customer-email"
                                        placeholder="email@contoh.com" class="w-full mt-1 p-2 border rounded-lg"></div>
                            </div>
                            <div id="cart-items" class="my-6 space-y-4 max-h-60 overflow-y-auto pr-2">
                                <p id="empty-cart-text" class="text-center text-gray-500 py-8">Keranjang masih kosong.
                                </p>
                            </div>
                            <div class="border-t pt-4 space-y-3">
                                <div class="flex justify-between items-center text-sm"><span
                                        class="text-gray-600">Subtotal</span><span id="subtotal"
                                        class="font-semibold">Rp0</span></div>
                                <div class="flex justify-between items-center font-bold text-xl text-gray-800">
                                    <span>Total</span><span id="grand-total">Rp0</span>
                                </div>
                            </div>
                            <div class="mt-6">
                                <p class="font-semibold mb-2">Metode Pembayaran</p>
                                <div class="grid grid-cols-1 gap-3">
                                    <button
                                        class="payment-method selected border-2 rounded-lg p-3 text-center transition flex items-center justify-center gap-2"
                                        data-method="Tunai"><i class="bi bi-cash-coin"></i>Tunai</button>
                                    <button
                                        class="payment-method border-2 rounded-lg p-3 text-center transition flex items-center justify-center gap-2"
                                        data-method="E-Wallet"><i class="bi bi-wallet2"></i>E-Wallet</button>
                                    <button
                                        class="payment-method border-2 rounded-lg p-3 text-center transition flex items-center justify-center gap-2"
                                        data-method="Virtual Bank"><i class="bi bi-bank"></i>Virtual Bank</button>
                                </div>
                            </div>
                            <button id="process-payment-btn" data-store-url="{{ route('kasir.transaksi.store') }}"
                                class="w-full mt-8 bg-indigo-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-indigo-700 transition shadow-md">
                                Proses Pembayaran
                            </button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    {{-- Modal Pilih Varian --}}
    <div id="variant-modal"
        class="fixed inset-0 bg-black/60 z-50 flex items-start justify-center px-4 py-8 overflow-y-auto hidden modal-overlay opacity-0">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl modal-content transform scale-95 opacity-0">
            <div class="flex justify-between items-center p-5 border-b">
                <h3 id="variant-modal-title" class="text-xl font-bold">Pilih Varian</h3>
                <button class="close-modal p-2"><i class="bi bi-x-lg"></i></button>
            </div>

            <div class="p-6">
                <input type="hidden" id="selected-product-id">
                <div class="flex flex-col md:flex-row gap-6">
                    {{-- Kolom Kiri: Slider Gambar --}}
                    <div class="w-full md:w-5/12">
                        <div class="swiper main-slider rounded-lg aspect-square">
                            <div class="swiper-wrapper" id="main-slider-wrapper"></div>
                            <div class="swiper-button-next text-gray-700"></div>
                            <div class="swiper-button-prev text-gray-700"></div>
                        </div>
                        <div class="swiper thumb-slider mt-2">
                            <div class="swiper-wrapper" id="thumb-slider-wrapper"></div>
                        </div>
                    </div>

                    {{-- Kolom Kanan: Detail & Opsi Varian --}}
                    <div class="w-full md:w-7/12 flex flex-col">
                        <div>
                            <p id="variant-modal-name" class="font-bold text-xl"></p>
                            <div class="flex items-center justify-between mt-1">
                                <p id="variant-modal-price" class="text-indigo-600 font-semibold text-2xl"></p>
                                <p class="text-sm font-medium text-gray-600">Stok: <span id="variant-modal-stock"
                                        class="font-bold"></span></p>
                            </div>
                        </div>
                        <hr class="my-4">
                        <div id="variant-options" class="flex-grow space-y-4"></div>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-gray-50 flex justify-end rounded-b-2xl">
                <button id="add-to-cart-btn"
                    class="px-5 py-2 bg-gray-400 cursor-not-allowed text-white rounded-lg font-bold" disabled>
                    Tambah ke Keranjang
                </button>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        $(document).ready(function() {
            AOS.init({
                duration: 600,
                once: true,
                offset: 20
            });

            const products = @json($products);
            let cart = [];
            const variantModal = $('#variant-modal');
            let mainSlider, thumbSlider;
            let currentProductImages = [];
            let currentProductVariants = [];
            let selectedVariantData = null;

            function renderProducts(filter = 'all', search = '', condition = 'all') {
                const list = $('#product-list');
                list.empty();
                const filtered = products.filter(p =>
                    (filter === 'all' || p.category === filter) &&
                    (p.name.toLowerCase().includes(search.toLowerCase())) &&
                    (condition === 'all' || p.condition === condition)
                );

                if (filtered.length === 0) {
                    list.html(
                        '<p class="col-span-full text-center text-gray-500 py-10">Produk tidak ditemukan.</p>');
                    return;
                }

                filtered.forEach(p => {
                    const conditionBadge = p.condition === 'Baru' ?
                        '<span class="absolute top-2 right-2 text-xs font-semibold px-2 py-1 rounded-full bg-green-100 text-green-800">Baru</span>' :
                        '<span class="absolute top-2 right-2 text-xs font-semibold px-2 py-1 rounded-full bg-yellow-100 text-yellow-800">Second</span>';
                    const displayPrice = p.master_price || p.display_price;
                    const card =
                        `<div class="product-card bg-white border rounded-lg overflow-hidden transition-all cursor-pointer relative" data-id="${p.id}">${conditionBadge}<img src="${p.images[0]}" class="w-full h-40 object-cover bg-gray-100"><div class="p-4"><p class="font-semibold text-sm truncate" title="${p.name}">${p.name}</p><div class="flex justify-between items-center mt-1"><p class="text-indigo-600 font-bold">Rp${parseFloat(displayPrice).toLocaleString('id-ID')}</p><span class="text-xs font-medium text-gray-500">Stok: ${p.total_stock}</span></div></div></div>`;
                    list.append(card);
                });
            }

            function updateCart() {
                const cartItems = $('#cart-items');
                cartItems.empty();
                if (cart.length === 0) {
                    cartItems.html(
                        '<p id="empty-cart-text" class="text-center text-gray-500 py-8">Keranjang masih kosong.</p>'
                    );
                } else {
                    cart.forEach(item => {
                        const itemHtml =
                            `<div class="cart-item flex items-center gap-3"><img src="${item.image}" class="w-12 h-12 rounded-md object-cover"><div class="flex-grow"><p class="font-semibold text-sm">${item.name}</p><p class="text-xs text-gray-500">${item.variant.color} / ${item.variant.ram}</p><p class="text-xs font-bold text-indigo-600">Rp${parseFloat(item.price).toLocaleString('id-ID')}</p></div><div class="flex items-center gap-2"><button class="qty-change h-6 w-6 bg-gray-200 rounded-full flex items-center justify-center" data-variant-id="${item.variant.id}">-</button><span class="min-w-[20px] text-center">${item.qty}</span><button class="qty-change h-6 w-6 bg-gray-200 rounded-full flex items-center justify-center" data-variant-id="${item.variant.id}" data-action="plus">+</button></div><button class="remove-item text-red-500 ml-2" data-variant-id="${item.variant.id}"><i class="bi bi-trash-fill"></i></button></div>`;
                        cartItems.append(itemHtml);
                    });
                }
                updateTotals();
            }

            function updateTotals() {
                const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
                $('#subtotal').text(`Rp${subtotal.toLocaleString('id-ID')}`);
                $('#grand-total').text(`Rp${subtotal.toLocaleString('id-ID')}`);
            }

            function openModal(modal) {
                modal.removeClass('hidden');
                setTimeout(() => {
                    modal.removeClass('opacity-0');
                    modal.find('.modal-content').removeClass('opacity-0 scale-95');
                }, 10);
                $('body').addClass('overflow-hidden');
            }

            function closeModal(modal) {
                modal.addClass('opacity-0').find('.modal-content').addClass('opacity-0 scale-95');
                setTimeout(() => modal.addClass('hidden'), 300);
                $('body').removeClass('overflow-hidden');
            }

            function findImageIndex(imageUrl) {
                return currentProductImages.findIndex(img => img.url === imageUrl);
            }

            function findVariantImages(variant) {
                if (variant.image_urls && variant.image_urls.length > 0) return variant.image_urls;
                if (variant.primary_image_url) return [variant.primary_image_url];
                return [];
            }

            function getVariantThumbnail(variant, product) {
                const variantImages = findVariantImages(variant);
                return variantImages.length > 0 ? variantImages[0] : product.images[0];
            }

            function updateVariantAvailability(product) {
                const selectedColor = $('.variant-btn[data-type="color"].selected').data('value');
                const selectedRam = $('.variant-btn[data-type="ram"].selected').data('value');

                $('.variant-btn[data-type="ram"]').each(function() {
                    const ramValue = $(this).data('value');
                    const isGenerallyAvailable = product.variants.some(v => v.ram === ramValue && v.stock >
                        0);
                    $(this).prop('disabled', !isGenerallyAvailable).toggleClass('disabled', !
                        isGenerallyAvailable);
                });
                $('.variant-btn[data-type="color"]').each(function() {
                    const colorValue = $(this).data('value');
                    const isGenerallyAvailable = product.variants.some(v => v.color === colorValue && v
                        .stock > 0);
                    $(this).prop('disabled', !isGenerallyAvailable).toggleClass('disabled', !
                        isGenerallyAvailable);
                });

                if (selectedColor) {
                    const availableRams = product.variants.filter(v => v.color === selectedColor && v.stock > 0)
                        .map(v => v.ram);
                    $('.variant-btn[data-type="ram"]').each(function() {
                        if (!availableRams.includes($(this).data('value'))) {
                            $(this).addClass('disabled').prop('disabled', true);
                        }
                    });
                }

                if (selectedRam) {
                    const availableColors = product.variants.filter(v => v.ram === selectedRam && v.stock > 0).map(
                        v => v.color);
                    $('.variant-btn[data-type="color"]').each(function() {
                        if (!availableColors.includes($(this).data('value'))) {
                            $(this).addClass('disabled').prop('disabled', true);
                        }
                    });
                }
            }

            renderProducts();
            updateCart();

            $('.condition-filter-btn').on('click', function() {
                $('.condition-filter-btn').removeClass('active');
                $(this).addClass('active');
                renderProducts($('#category-filter').val(), $('#search-product').val(), $(this).data(
                    'condition'));
            });

            $('#category-filter, #search-product').on('change keyup', () => renderProducts($('#category-filter')
                .val(), $('#search-product').val(), $('.condition-filter-btn.active').data('condition')));

            $(document).on('click', '.product-card', function() {
                const productId = $(this).data('id');
                const product = products.find(p => p.id === productId);
                if (!product || product.variants.length === 0) return;

                currentProductImages = [];
                currentProductVariants = product.variants;
                selectedVariantData = null;

                $('#selected-product-id').val(productId);
                $('#variant-modal-name').text(product.name);
                const initialPrice = product.master_price || product.display_price;
                $('#variant-modal-price').text(`Rp${parseFloat(initialPrice).toLocaleString('id-ID')}`);
                $('#variant-modal-stock').text(product.total_stock);

                if (mainSlider) mainSlider.destroy(true, true);
                if (thumbSlider) thumbSlider.destroy(true, true);
                $('#main-slider-wrapper, #thumb-slider-wrapper').empty();

                product.images.forEach(imgUrl => currentProductImages.push({
                    url: imgUrl,
                    type: 'product'
                }));
                product.variants.forEach(variant => {
                    findVariantImages(variant).forEach(imgUrl => {
                        if (!currentProductImages.some(img => img.url === imgUrl)) {
                            currentProductImages.push({
                                url: imgUrl,
                                type: 'variant',
                                ...variant
                            });
                        }
                    });
                });
                if (currentProductImages.length === 0) {
                    currentProductImages.push({
                        url: 'https://placehold.co/400x400/eef2ff/4f46e5?text=N/A'
                    });
                }

                currentProductImages.forEach(img => {
                    $('#main-slider-wrapper').append(
                        `<div class="swiper-slide"><img src="${img.url}" class="w-full h-full object-contain"></div>`
                    );
                    $('#thumb-slider-wrapper').append(
                        `<div class="swiper-slide"><img src="${img.url}" class="w-full h-16 object-cover rounded-md cursor-pointer"></div>`
                    );
                });

                setTimeout(() => {
                    thumbSlider = new Swiper(".thumb-slider", {
                        spaceBetween: 10,
                        slidesPerView: 4,
                        freeMode: true,
                        watchSlidesProgress: true
                    });
                    mainSlider = new Swiper(".main-slider", {
                        spaceBetween: 10,
                        navigation: {
                            nextEl: ".swiper-button-next",
                            prevEl: ".swiper-button-prev"
                        },
                        thumbs: {
                            swiper: thumbSlider
                        }
                    });
                }, 100);

                const optionsContainer = $('#variant-options').empty();
                const colors = [...new Set(product.variants.map(v => v.color).filter(Boolean))];
                if (colors.length > 0) {
                    let colorHtml =
                        '<div class="mb-4"><h5 class="font-semibold mb-2">Warna</h5><div class="flex flex-wrap gap-3">';
                    colors.forEach(c => {
                        const variantForThumb = product.variants.find(v => v.color === c);
                        const thumbnailUrl = getVariantThumbnail(variantForThumb, product);
                        const isAvailable = product.variants.some(v => v.color === c && v.stock >
                            0);
                        colorHtml +=
                            `<button class="variant-btn border rounded-lg text-sm p-1 flex items-center gap-2 transition ${!isAvailable ? 'disabled' : ''}" data-type="color" data-value="${c}" ${!isAvailable ? 'disabled' : ''}><img src="${thumbnailUrl}" class="w-8 h-8 rounded-md object-cover"><span class="px-2">${c}</span></button>`;
                    });
                    optionsContainer.append(colorHtml + '</div></div>');
                }

                const rams = [...new Set(product.variants.map(v => v.ram).filter(Boolean))];
                if (rams.length > 0) {
                    let ramHtml =
                        '<div><h5 class="font-semibold mb-2">RAM/Memori</h5><div class="flex flex-wrap gap-2">';
                    rams.forEach(r => {
                        if (r) {
                            const isAvailable = product.variants.some(v => v.ram === r && v.stock >
                                0);
                            ramHtml +=
                                `<button class="variant-btn border px-4 py-1.5 rounded-lg text-sm ${!isAvailable ? 'disabled' : ''}" data-type="ram" data-value="${r}" ${!isAvailable ? 'disabled' : ''}>${r}</button>`;
                        }
                    });
                    optionsContainer.append(ramHtml + '</div></div>');
                }

                const addToCartBtn = $('#add-to-cart-btn');
                addToCartBtn.prop('disabled', true).removeClass('bg-indigo-600').addClass(
                    'bg-gray-400 cursor-not-allowed');
                openModal(variantModal);
            });

            $(document).on('click', '.variant-btn:not(.disabled)', function() {
                const type = $(this).data('type');
                const wasSelected = $(this).hasClass('selected');

                if (wasSelected) {
                    $(this).removeClass('selected');
                } else {
                    $(`.variant-btn[data-type="${type}"]`).removeClass('selected');
                    $(this).addClass('selected');
                }

                const productId = parseInt($('#selected-product-id').val());
                const product = products.find(p => p.id === productId);

                updateVariantAvailability(product);

                if ($('.variant-btn[data-type="color"].selected').hasClass('disabled')) $(
                    '.variant-btn[data-type="color"].selected').removeClass('selected');
                if ($('.variant-btn[data-type="ram"].selected').hasClass('disabled')) $(
                    '.variant-btn[data-type="ram"].selected').removeClass('selected');

                const selectedColor = $('.variant-btn[data-type="color"].selected').data('value');
                const selectedRam = $('.variant-btn[data-type="ram"].selected').data('value');
                selectedVariantData = product.variants.find(v => v.color == selectedColor && v.ram ==
                    selectedRam);

                if (selectedVariantData) {
                    $('#variant-modal-stock').text(selectedVariantData.stock);
                    $('#variant-modal-price').text(
                        `Rp${parseFloat(selectedVariantData.price).toLocaleString('id-ID')}`);
                    const variantImages = findVariantImages(selectedVariantData);
                    let targetImageIndex = -1;
                    if (variantImages.length > 0) targetImageIndex = findImageIndex(variantImages[0]);
                    if (targetImageIndex !== -1 && mainSlider) mainSlider.slideTo(targetImageIndex);
                } else {
                    $('#variant-modal-stock').text(product.total_stock);
                    const masterPrice = product.master_price || product.display_price;
                    $('#variant-modal-price').text(`Rp${parseFloat(masterPrice).toLocaleString('id-ID')}`);
                }

                const hasColor = $('.variant-btn[data-type="color"]').length === 0 || $(
                    '.variant-btn[data-type="color"].selected').length > 0;
                const hasRam = $('.variant-btn[data-type="ram"]').length === 0 || $(
                    '.variant-btn[data-type="ram"].selected').length > 0;
                const isVariantValid = selectedVariantData && selectedVariantData.stock > 0;

                const canAddToCart = hasColor && hasRam && isVariantValid;
                const addToCartBtn = $('#add-to-cart-btn');
                addToCartBtn.prop('disabled', !canAddToCart);
                if (canAddToCart) {
                    addToCartBtn.removeClass('bg-gray-400 cursor-not-allowed').addClass('bg-indigo-600');
                } else {
                    addToCartBtn.removeClass('bg-indigo-600').addClass('bg-gray-400 cursor-not-allowed');
                }
            });

            $('#add-to-cart-btn').on('click', function() {
                if (!selectedVariantData) return;
                const product = products.find(p => p.id === parseInt($('#selected-product-id').val()));
                if (selectedVariantData.stock <= 0) {
                    Swal.fire('Stok Habis', 'Varian yang dipilih tidak tersedia.', 'warning');
                    return;
                }
                const cartItem = cart.find(item => item.variant.id === selectedVariantData.id);
                const itemImage = findVariantImages(selectedVariantData)[0] || product.images[0];
                if (cartItem) {
                    if (cartItem.qty >= selectedVariantData.stock) {
                        Swal.fire('Stok Tidak Cukup',
                            `Hanya tersedia ${selectedVariantData.stock} stok untuk varian ini.`,
                            'warning');
                        return;
                    }
                    cartItem.qty++;
                } else {
                    cart.push({
                        ...product,
                        qty: 1,
                        variant: selectedVariantData,
                        price: selectedVariantData.price,
                        image: itemImage
                    });
                }
                updateCart();
                closeModal(variantModal);
                Swal.fire('Berhasil', 'Produk telah ditambahkan ke keranjang.', 'success');
            });

            $(document).on('click', '.qty-change', function() {
                const variantId = $(this).data('variant-id');
                const action = $(this).data('action');
                const cartItem = cart.find(item => item.variant.id === variantId);
                if (cartItem) {
                    if (action === 'plus') {
                        if (cartItem.qty >= cartItem.variant.stock) {
                            Swal.fire('Stok Tidak Cukup', `Hanya tersedia ${cartItem.variant.stock} stok.`,
                                'warning');
                            return;
                        }
                        cartItem.qty++;
                    } else if (cartItem.qty > 1) {
                        cartItem.qty--;
                    } else {
                        cart = cart.filter(item => item.variant.id !== variantId);
                    }
                }
                updateCart();
            });

            $(document).on('click', '.remove-item', function() {
                cart = cart.filter(item => item.variant.id !== $(this).data('variant-id'));
                updateCart();
            });

            $('.payment-method').on('click', function() {
                $('.payment-method').removeClass('selected');
                $(this).addClass('selected');
            });

            $('.close-modal, .modal-overlay').on('click', function(e) {
                if ($(e.target).is('.modal-overlay, .close-modal, .close-modal *')) {
                    closeModal(variantModal);
                }
            });
            $(document).on('keydown', e => {
                if (e.key === 'Escape') closeModal(variantModal)
            });

            $('#process-payment-btn').on('click', function() {
                // Ambil URL dari atribut data-* pada tombol yang diklik
                const storeUrl = $(this).data('store-url');

                if (cart.length === 0 || !$('#customer-name').val().trim() || !$('#customer-phone').val()
                    .trim()) {
                    Swal.fire('Data Tidak Lengkap',
                        'Pastikan nama pelanggan, telepon, dan keranjang tidak kosong.', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Proses Transaksi?',
                    html: `<div class="text-left"><p><strong>Pelanggan:</strong> ${$('#customer-name').val()}</p><p><strong>Total:</strong> ${$('#grand-total').text()}</p></div>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Proses!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#4f46e5',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        const transactionData = {
                            customer_name: $('#customer-name').val(),
                            customer_phone: $('#customer-phone').val(),
                            customer_email: $('#customer-email').val(),
                            payment_method: $('.payment-method.selected').data('method'),
                            items: cart,
                            total: cart.reduce((sum, item) => sum + (item.price * item.qty),
                                0)
                        };

                        // Gunakan variabel storeUrl di sini
                        return fetch(storeUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                        'content') 
                                },
                                body: JSON.stringify(transactionData)
                            })
                            .then(response => {
                                if (!response.ok) {
                                    return response.json().then(err => {
                                        throw new Error(err.message ||
                                            'Terjadi kesalahan pada server.')
                                    });
                                }
                                return response.json();
                            })
                            .catch(error => {
                                Swal.showValidationMessage(
                                    `Request gagal: ${error.message}`);
                            });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        cart = [];
                        $('#customer-name, #customer-phone, #customer-email').val('');
                        updateCart();

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Transaksi telah berhasil disimpan.',
                            confirmButtonColor: '#4f46e5'
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>

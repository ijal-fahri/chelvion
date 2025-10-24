<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - CELVION</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <script>
        // Custom Tailwind CSS Configuration to match the store's theme
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif']
                    },
                    colors: {
                        primary: {
                            DEFAULT: '#4f46e5', // indigo-600
                            'light': '#eef2ff', // indigo-50
                        },
                        secondary: '#64748b', // slate-500
                        dark: '#1e293b', // slate-800
                        success: '#10b981', // emerald-500
                        gold: '#f59e0b', // amber-500
                    }
                }
            }
        }
    </script>

    <style>
        /* Custom styles for better form elements and interactivity */
        body {
            font-family: 'Poppins', sans-serif;
        }

        .delivery-option-radio:checked+label {
            border-color: #4f46e5;
            /* primary color */
            box-shadow: 0 0 0 2px #4f46e5;
        }

        .delivery-option-radio:checked+label .icon-circle {
            background-color: #4f46e5;
            color: white;
        }

        .voucher-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .voucher-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .voucher-card.selected {
            border-color: #4f46e5;
            box-shadow: 0 0 0 2px #4f46e5;
        }

        .voucher-premium {
            background: linear-gradient(135deg, #fef3c7 0%, #f59e0b 100%);
            border: 1px solid #f59e0b;
        }

        /* Styling untuk modal voucher */
        .voucher-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .voucher-modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 1rem;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800">

    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-dark">
                <a href="{{ url('/') }}">CELVION</a>
            </h1>
        </div>
    </header>


    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="mb-8">

            @if (session('error'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops! Terjadi Kesalahan',
                            text: "{{ session('error') }}",
                            confirmButtonColor: '#4f46e5'
                        });
                    });
                </script>
            @endif

            @if ($errors->any() && !session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg" role="alert">
                    <p class="font-bold">Oops! Ada kesalahan:</p>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex items-center justify-center text-sm md:text-base">
                <div class="text-secondary flex items-center">
                    <i class="bi bi-cart-check-fill text-xl mr-2"></i> Keranjang
                </div>
                <div class="flex-auto border-t-2 border-slate-300 mx-4"></div>
                <div class="text-primary font-bold flex items-center">
                    <i class="bi bi-shield-check-fill text-xl mr-2"></i> Checkout
                </div>
                <div class="flex-auto border-t-2 border-slate-300 mx-4"></div>
                <div class="text-secondary flex items-center">
                    <i class="bi bi-credit-card-fill text-xl mr-2"></i> Pembayaran
                </div>
            </div>
        </div>

        <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
            @csrf

            <input type="hidden" name="voucher_code" id="input-voucher-code">

            <input type="hidden" name="cart_ids" value="{{ $carts->pluck('id')->implode(',') }}">


            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

                <div class="lg:col-span-2 space-y-8">

                    <div class="bg-white rounded-2xl shadow-md p-6">
                        <h2 class="text-xl font-bold text-dark mb-5 flex items-center gap-3"><i
                                class="bi bi-truck text-primary"></i> Metode Pengambilan</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <input type="radio" name="delivery_method" id="delivery-antar" value="antar"
                                    class="sr-only delivery-option-radio" required
                                    {{ old('delivery_method', 'antar') == 'antar' ? 'checked' : '' }}>
                                <label for="delivery-antar"
                                    class="flex items-center p-4 border-2 border-slate-200 rounded-xl cursor-pointer">
                                    <span
                                        class="icon-circle flex items-center justify-center w-8 h-8 rounded-full border border-slate-300 mr-4"><i
                                            class="bi bi-box-seam"></i></span>
                                    <div>
                                        <p class="font-semibold">Diantar ke Alamat</p>
                                        <p class="text-sm text-secondary">Dikirim langsung ke lokasimu.</p>
                                    </div>
                                </label>
                            </div>
                            <div>
                                <input type="radio" name="delivery_method" id="delivery-ambil" value="ambil"
                                    class="sr-only delivery-option-radio" required
                                    {{ old('delivery_method') == 'ambil' ? 'checked' : '' }}>
                                <label for="delivery-ambil"
                                    class="flex items-center p-4 border-2 border-slate-200 rounded-xl cursor-pointer">
                                    <span
                                        class="icon-circle flex items-center justify-center w-8 h-8 rounded-full border border-slate-300 mr-4"><i
                                            class="bi bi-shop"></i></span>
                                    <div>
                                        <p class="font-semibold">Ambil di Toko</p>
                                        <p class="text-sm text-secondary">Ambil pesanan di cabang terdekat.</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-md p-6 hidden" id="cabang-section">
                        <h2 class="text-xl font-bold text-dark mb-5"><i class="bi bi-shop-window text-primary"></i>
                            Pilih Cabang Pengiriman</h2>
                        <select name="shipping_cabang_id" id="cabangSelect"
                            class="block w-full text-base py-3 px-4 rounded-lg border-slate-300">
                            <option value="">Pilih Cabang Pengiriman</option>
                            @foreach ($cabangs as $cabang)
                                <option value="{{ $cabang->id }}"
                                    {{ old('shipping_cabang_id') == $cabang->id ? 'selected' : '' }}>
                                    {{ $cabang->nama_cabang }} - {{ $cabang->alamat }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="bg-white rounded-2xl shadow-md p-6 hidden" id="alamat-section">
                        <h2 class="text-xl font-bold text-dark mb-5"><i class="bi bi-geo-alt-fill text-primary"></i>
                            Alamat Pengiriman</h2>
                        <div class="space-y-4">
                            <div>
                                <label for="receiver_name" class="block text-sm font-medium">Nama Penerima</label>
                                <input type="text" name="receiver_name" id="receiver_name"
                                    value="{{ old('receiver_name', Auth::user()->name) }}"
                                    class="block w-full text-base py-3 px-4 rounded-lg border-slate-300">
                            </div>
                            <div>
                                <label for="phone_number" class="block text-sm font-medium">Nomor Telepon</label>
                                <input type="text" name="phone_number" id="phone_number"
                                    value="{{ old('phone_number', Auth::user()->phone) }}"
                                    class="block w-full text-base py-3 px-4 rounded-lg border-slate-300">
                            </div>
                            <div>
                                <label for="full_address" class="block text-sm font-medium">Alamat Lengkap</label>
                                <input type="text" name="full_address" id="full_address"
                                    value="{{ old('full_address') }}" placeholder="Nama Jalan, No. Rumah, RT/RW"
                                    class="block w-full text-base py-3 px-4 rounded-lg border-slate-300">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="kecamatan" class="block text-sm font-medium">Kecamatan</label>
                                    <input type="text" name="kecamatan" id="kecamatan"
                                        value="{{ old('kecamatan') }}"
                                        class="block w-full text-base py-3 px-4 rounded-lg border-slate-300">
                                </div>
                                <div>
                                    <label for="city" class="block text-sm font-medium">Kabupaten/Kota</label>
                                    <input type="text" name="city" id="city" value="{{ old('city') }}"
                                        class="block w-full text-base py-3 px-4 rounded-lg border-slate-300">
                                </div>
                            </div>
                            <div>
                                <label for="province" class="block text-sm font-medium">Provinsi</label>
                                <input type="text" name="province" id="province" value="{{ old('province') }}"
                                    class="block w-full text-base py-3 px-4 rounded-lg border-slate-300">
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl shadow-md p-6 hidden" id="pickup-section">
                        <h2 class="text-xl font-bold text-dark mb-5"><i class="bi bi-pin-map-fill text-primary"></i>
                            Pilih Lokasi Pengambilan</h2>
                        <select name="pickup_cabang_id" id="pickupSelect"
                            class="block w-full text-base py-3 px-4 rounded-lg border-slate-300">
                            <option value="">Pilih Lokasi Cabang</option>
                            @foreach ($cabangs as $cabang)
                                <option value="{{ $cabang->id }}"
                                    {{ old('pickup_cabang_id') == $cabang->id ? 'selected' : '' }}>
                                    {{ $cabang->nama_cabang }} - {{ $cabang->alamat }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="bg-white rounded-2xl shadow-md p-6">
                        <h2 class="text-xl font-bold text-dark mb-5"><i class="bi bi-wallet2 text-primary"></i> Metode
                            Pembayaran</h2>
                        <select name="payment_method" id="payment_method"
                            class="block w-full text-base py-3 px-4 rounded-lg border-slate-300" required>
                            <option value="">Pilih Metode Pembayaran</option>
                            <option value="Transfer Bank"
                                {{ old('payment_method') == 'Transfer Bank' ? 'selected' : '' }}>
                                Transfer Bank</option>
                            <option value="E-Wallet" {{ old('payment_method') == 'E-Wallet' ? 'selected' : '' }}>
                                E-Wallet</option>
                            <option value="Tunai" {{ old('payment_method') == 'Tunai' ? 'selected' : '' }}>
                                Tunai (Bayar di Tempat/Toko)</option>
                        </select>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-md p-6 sticky top-8">
                        <h2 class="text-xl font-bold text-dark mb-5 pb-4 border-b">Ringkasan Pesanan</h2>
                        <div class="space-y-4 mb-6">

                            @foreach ($carts as $item)
                                @php
                                    $price = $item->variant->price ?? $item->product->price;
                                    $subtotal = $price * $item->quantity;
                                @endphp
                                <div class="flex items-start gap-4">
                                    @php
                                        // Ambil URL gambar langsung dari accessor di model ProductVariant.
                                        // Jika accessor mengembalikan null (tidak ada gambar), gunakan placeholder.
                                        $imageUrl =
                                            $item->variant->first_image_url ?? 'https://via.placeholder.com/150';
                                    @endphp
                                    <img src="{{ $imageUrl }}" alt="{{ $item->product->name }}"
                                        class="w-20 h-20 object-contain rounded-lg bg-slate-100">
                                    <div class="flex-grow">
                                        <p class="font-semibold">{{ $item->product->name }}</p>
                                        <p class="text-sm text-secondary">Qty: {{ $item->quantity }}</p>
                                    </div>
                                    <p class="font-semibold">Rp{{ number_format($subtotal, 0, ',', '.') }}</p>
                                </div>
                            @endforeach
                        </div>

                        <div class="py-4 border-t border-slate-200">

                            <div class="flex justify-between items-center mb-3">
                                <span class="font-semibold text-dark">Voucher Diskon</span>
                                <button type="button" id="btn-voucher"
                                    class="text-sm bg-primary-light text-primary font-medium py-2 px-4 rounded-lg hover:bg-indigo-100 transition-colors flex items-center gap-2">
                                    <i class="bi bi-ticket-perforated"></i>
                                    Pilih Voucher
                                </button>
                            </div>
                            <div id="voucher-applied" class="hidden">
                                <div
                                    class="flex justify-between items-center bg-green-50 p-3 rounded-lg border border-green-200">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                            <i class="bi bi-ticket-perforated text-white"></i>
                                        </div>
                                        <div>
                                            <span class="font-medium text-green-800" id="voucher-name">DISKON10</span>
                                            <span class="text-sm text-green-600 block" id="voucher-desc">Diskon
                                                10%</span>
                                            <span class="text-xs text-green-500" id="voucher-expiry">Berlaku hingga 31
                                                Des 2024</span>
                                        </div>
                                    </div>
                                    <button type="button" id="btn-remove-voucher"
                                        class="text-red-500 hover:text-red-700 text-lg">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2 py-4 border-t">
                            <div class="flex justify-between"><span class="text-secondary">Subtotal</span><span
                                    class="font-semibold"
                                    id="subtotal-display">Rp{{ number_format($grandTotal, 0, ',', '.') }}</span></div>

                            <div class="flex justify-between hidden" id="diskon-row">
                                <span class="text-secondary">Diskon</span>
                                <span class="font-semibold text-red-500" id="diskon-amount">-Rp0</span>
                            </div>

                            <div class="flex justify-between"><span class="text-secondary">Pengiriman</span><span
                                    class="font-semibold text-dark">Gratis</span></div>
                        </div>
                        <div class="flex justify-between items-center mt-4 pt-4 border-t">
                            <span class="font-bold text-lg">Total Bayar</span>
                            <span class="font-extrabold text-2xl text-primary"
                                id="total-bayar">Rp{{ number_format($grandTotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="mt-8">
                            <button type="submit" id="btn-confirm"
                                class="w-full bg-primary hover:bg-indigo-700 text-white font-bold py-4 rounded-xl">Konfirmasi
                                & Bayar</button>
                        </div>
                    </div>
                    <div class="text-center mt-6">
                        <a href="{{ route('cart.show') }}"
                            class="font-medium text-secondary hover:text-dark transition-colors">
                            &larr; Kembali ke Keranjang
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <div id="voucher-modal" class="voucher-modal">
        <div class="modal-content">
            <div class="flex justify-between items-center p-6 border-b border-slate-200">
                <h3 class="text-xl font-bold text-dark">Pilih Voucher Diskon</h3>
                <button type="button" id="close-voucher-modal" class="text-slate-400 hover:text-slate-600 text-2xl">
                    <i class="bi bi-x"></i>
                </button>
            </div>

            <div class="p-6 overflow-y-auto max-h-[60vh]">
                <div class="grid grid-cols-1 gap-4">

                    @forelse ($vouchers as $voucher)
                        <div class="voucher-card bg-white border-2 border-slate-200 rounded-xl p-4"
                            data-voucher="{{ $voucher->toJson() }}"
                            data-cabang-id="{{ $voucher->cabang_id ?? 'global' }}">
                            <div class="flex items-start gap-4">
                                <div
                                    class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i
                                        class="bi {{ $voucher->type == 'percentage' ? 'bi-percent' : 'bi-cash-coin' }} text-white text-xl"></i>
                                </div>
                                <div class="flex-grow">
                                    <h4 class="font-bold text-dark">{{ $voucher->name }}</h4>
                                    <p class="text-sm text-secondary mb-1">{{ $voucher->description }}</p>
                                    <p class="text-xs text-slate-500">
                                        @if ($voucher->min_purchase > 0)
                                            Min. belanja Rp{{ number_format($voucher->min_purchase, 0, ',', '.') }}
                                        @else
                                            Tanpa min. belanja
                                        @endif
                                    </p>
                                </div>
                                <div class="text-right">
                                    @if ($voucher->type == 'percentage')
                                        <div class="text-lg font-bold text-primary">
                                            {{ $voucher->discount_percentage }}%</div>
                                        @if ($voucher->max_discount > 0)
                                            <div class="text-xs text-secondary">Maks.
                                                Rp{{ number_format($voucher->max_discount, 0, ',', '.') }}</div>
                                        @endif
                                    @else
                                        <div class="text-lg font-bold text-primary">
                                            Rp{{ number_format($voucher->discount_amount, 0, ',', '.') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center p-8 text-secondary">
                            <i class="bi bi-ticket-perforated text-4xl mb-3"></i>
                            <p class->Tidak ada voucher yang tersedia saat ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="flex justify-between items-center p-6 border-t border-slate-200 bg-slate-50">
                <div>
                    <p class="text-sm text-secondary" id="selected-voucher-info">Pilih voucher untuk melihat detail
                    </p>
                </div>
                <div class="flex gap-3">
                    <button type="button" id="cancel-voucher"
                        class="px-6 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-100 transition-colors">
                        Batal
                    </button>
                    <button type="button" id="apply-voucher"
                        class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                        Gunakan Voucher
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('components.footer-pelanggan')

    <script>
        const originalGrandTotal = {{ $grandTotal }};

        document.addEventListener('DOMContentLoaded', function() {
            // ========== VOUCHER MODAL FUNCTIONALITY ==========
            const btnVoucher = document.getElementById('btn-voucher');
            const voucherModal = document.getElementById('voucher-modal');
            const closeVoucherModal = document.getElementById('close-voucher-modal');
            const cancelVoucher = document.getElementById('cancel-voucher');

            // Fungsi untuk membuka modal voucher
            function openVoucherModal() {
                // 1. Cek apakah cabang sudah dipilih
                const isAntar = document.getElementById('delivery-antar').checked;
                const selectedCabangId = isAntar ?
                    document.getElementById('cabangSelect').value :
                    document.getElementById('pickupSelect').value;

                if (!selectedCabangId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Cabang Terlebih Dahulu',
                        text: 'Anda harus memilih cabang pengiriman atau pengambilan sebelum memilih voucher.',
                        confirmButtonColor: '#4f46e5'
                    });
                    return;
                }

                // 2. Filter voucher berdasarkan cabang yang dipilih
                const voucherCards = document.querySelectorAll('#voucher-modal .voucher-card');
                let availableVouchersCount = 0;

                voucherCards.forEach(card => {
                    const voucherCabangId = card.dataset.cabangId;
                    // Tampilkan jika voucher itu 'global' ATAU ID cabangnya cocok
                    if (voucherCabangId === 'global' || voucherCabangId === selectedCabangId) {
                        card.style.display = 'block';
                        availableVouchersCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // (Opsional: Tampilkan pesan jika tidak ada voucher yg cocok)
                // ...

                // 3. Tampilkan modal
                voucherModal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            // Fungsi untuk menutup modal voucher
            function closeVoucherModalFunc() {
                voucherModal.classList.remove('active');
                document.body.style.overflow = 'auto';
                // Jangan reset selection jika modal ditutup (mis. setelah apply)
                // resetVoucherSelection(); // Hapus ini
            }

            // [FIX] Cek null sebelum menambah event listener
            if (btnVoucher) {
                btnVoucher.addEventListener('click', openVoucherModal);
            }
            if (closeVoucherModal) {
                closeVoucherModal.addEventListener('click', closeVoucherModalFunc);
            }
            if (cancelVoucher) {
                cancelVoucher.addEventListener('click', closeVoucherModalFunc);
            }
            if (voucherModal) {
                voucherModal.addEventListener('click', function(e) {
                    if (e.target === voucherModal) closeVoucherModalFunc();
                });
            }


            // ========== VOUCHER SELECTION LOGIC ==========
            const applyVoucher = document.getElementById('apply-voucher');
            const voucherApplied = document.getElementById('voucher-applied');
            const btnRemoveVoucher = document.getElementById('btn-remove-voucher');
            const voucherName = document.getElementById('voucher-name');
            const voucherDesc = document.getElementById('voucher-desc');
            const voucherExpiry = document.getElementById('voucher-expiry');
            const selectedVoucherInfo = document.getElementById('selected-voucher-info');

            const diskonRow = document.getElementById('diskon-row');
            const diskonAmount = document.getElementById('diskon-amount');
            const totalBayar = document.getElementById('total-bayar');
            const inputVoucherCode = document.getElementById('input-voucher-code');

            let selectedVoucher = null;
            let currentDiscount = 0;

            // Fungsi untuk mereset pilihan voucher
            function resetVoucherSelection() {
                const voucherCards = document.querySelectorAll('.voucher-card');
                voucherCards.forEach(card => card.classList.remove('selected'));
                selectedVoucher = null;
                if (applyVoucher) applyVoucher.disabled = true;
                if (selectedVoucherInfo) selectedVoucherInfo.textContent = 'Pilih voucher untuk melihat detail';
            }

            // [MODIFIKASI] Fungsi untuk update total harga
            function updateTotals() {
                let newTotal = originalGrandTotal - currentDiscount;
                if (newTotal < 0) newTotal = 0; // Pastikan total tidak minus

                if (currentDiscount > 0) {
                    diskonAmount.textContent = `-Rp${currentDiscount.toLocaleString('id-ID')}`;
                    diskonRow.classList.remove('hidden');
                } else {
                    diskonRow.classList.add('hidden');
                }

                totalBayar.textContent = `Rp${newTotal.toLocaleString('id-ID')}`;

                // Simpan kode voucher di hidden input
                inputVoucherCode.value = selectedVoucher ? selectedVoucher.code : '';
            }

            // Event listener untuk memilih voucher
            const voucherCards = document.querySelectorAll('.voucher-card');
            voucherCards.forEach(card => {
                card.addEventListener('click', function() {
                    voucherCards.forEach(c => c.classList.remove('selected'));
                    this.classList.add('selected');

                    // Ambil data voucher dari atribut data-voucher
                    selectedVoucher = JSON.parse(this.getAttribute('data-voucher'));

                    if (applyVoucher) applyVoucher.disabled = false;

                    let infoText = selectedVoucher.description;
                    if (selectedVoucher.min_purchase > 0) {
                        infoText +=
                            ` (Min. Rp${parseFloat(selectedVoucher.min_purchase).toLocaleString('id-ID')})`;
                    }
                    if (selectedVoucherInfo) selectedVoucherInfo.textContent = infoText;
                });
            });

            // Event listener untuk menerapkan voucher
            if (applyVoucher) {
                applyVoucher.addEventListener('click', function() {
                    if (!selectedVoucher) return;

                    // 1. Validasi minimal pembelian di frontend
                    if (parseFloat(selectedVoucher.min_purchase) > 0 && originalGrandTotal < parseFloat(
                            selectedVoucher
                            .min_purchase)) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menggunakan Voucher',
                            text: `Total belanja Anda (Rp${originalGrandTotal.toLocaleString('id-ID')}) tidak mencukupi minimal pembelian voucher (Rp${parseFloat(selectedVoucher.min_purchase).toLocaleString('id-ID')}).`,
                            confirmButtonColor: '#4f46e5'
                        });
                        return;
                    }

                    // 2. Kalkulasi diskon di frontend
                    let discount = 0;
                    if (selectedVoucher.type === 'percentage') {
                        discount = originalGrandTotal * (parseFloat(selectedVoucher.discount_percentage) /
                            100);
                        if (parseFloat(selectedVoucher.max_discount) > 0 && discount > parseFloat(
                                selectedVoucher.max_discount)) {
                            discount = parseFloat(selectedVoucher.max_discount);
                        }
                    } else if (selectedVoucher.type === 'fixed') {
                        discount = parseFloat(selectedVoucher.discount_amount);
                    }

                    // Pastikan diskon tidak lebih besar dari total
                    if (discount > originalGrandTotal) {
                        discount = originalGrandTotal;
                    }

                    currentDiscount = discount;

                    // 3. Update UI
                    if (voucherName) voucherName.textContent = selectedVoucher.code;
                    if (voucherDesc) voucherDesc.textContent = selectedVoucher.description;
                    if (voucherExpiry) {
                        // Kita gunakan 'expiry_date_formatted' dari accessor Voucher.php
                        // Isinya akan "Berlaku selamanya" ATAU "22 Oktober 2025"

                        // Cek apakah accessor mengembalikan string "Berlaku selamanya"
                        if (selectedVoucher.expiry_date_formatted === 'Berlaku selamanya') {
                            voucherExpiry.textContent = selectedVoucher.expiry_date_formatted;
                        } else {
                            // Jika tidak, baru tambahkan prefix "Berlaku hingga "
                            voucherExpiry.textContent =
                                `Berlaku hingga ${selectedVoucher.expiry_date_formatted}`;
                        }
                    }

                    if (voucherApplied) voucherApplied.classList.remove('hidden');

                    // 4. Update total harga
                    updateTotals();

                    // 5. Tutup modal
                    closeVoucherModalFunc();

                    Swal.fire({
                        icon: 'success',
                        title: 'Voucher Berhasil Digunakan',
                        text: `Anda mendapat diskon Rp${currentDiscount.toLocaleString('id-ID')}!`,
                        confirmButtonColor: '#4f46e5'
                    });
                });
            }

            // Event listener untuk menghapus voucher
            if (btnRemoveVoucher) {
                btnRemoveVoucher.addEventListener('click', function() {
                    if (voucherApplied) voucherApplied.classList.add('hidden');

                    // Reset diskon dan voucher
                    currentDiscount = 0;
                    selectedVoucher = null;

                    // Update total harga kembali ke awal
                    updateTotals();

                    // Reset pilihan di modal
                    resetVoucherSelection();
                });
            }

            // ========== DELIVERY METHOD TOGGLE ==========
            const deliveryRadios = document.querySelectorAll('input[name="delivery_method"]');
            const alamatSection = document.getElementById('alamat-section');
            const pickupSection = document.getElementById('pickup-section');
            const cabangSection = document.getElementById('cabang-section');

            function toggleSections() {
                const selectedMethod = document.querySelector('input[name="delivery_method"]:checked');
                const isAntar = selectedMethod && selectedMethod.value === 'antar';
                const isAmbil = selectedMethod && selectedMethod.value === 'ambil';

                if (cabangSection) cabangSection.classList.toggle('hidden', !isAntar);
                if (alamatSection) alamatSection.classList.toggle('hidden', !isAntar);
                if (pickupSection) pickupSection.classList.toggle('hidden', !isAmbil);

                // [TAMBAH] Jika metode diganti, hapus voucher terpakai
                if (currentDiscount > 0 && btnRemoveVoucher) {
                    btnRemoveVoucher.click();
                    Swal.fire('Voucher Dihapus',
                        'Metode pengambilan diubah, silakan pilih ulang voucher jika tersedia.', 'info');
                }
            }

            if (document.getElementById('cabangSelect')) {
                document.getElementById('cabangSelect').addEventListener('change', function() {
                    if (currentDiscount > 0 && btnRemoveVoucher) btnRemoveVoucher.click();
                });
            }
            if (document.getElementById('pickupSelect')) {
                document.getElementById('pickupSelect').addEventListener('change', function() {
                    if (currentDiscount > 0 && btnRemoveVoucher) btnRemoveVoucher.click();
                });
            }


            deliveryRadios.forEach(radio => radio.addEventListener('change', toggleSections));
            toggleSections(); // Panggil sekali saat halaman dimuat

            // ========== FORM VALIDATION ==========
            const form = document.getElementById('checkout-form');
            const btnConfirm = document.getElementById('btn-confirm');

            if (btnConfirm) {
                btnConfirm.addEventListener('click', function(e) {
                    if (form && !form.checkValidity()) {
                        e.preventDefault();

                        // Cek input kustom yang mungkin tidak terdeteksi 'checkValidity'
                        let allValid = true;
                        const requiredFields = form.querySelectorAll('[required]');
                        requiredFields.forEach(field => {
                            if (!field.closest('.hidden')) { // Hanya cek field yang terlihat
                                if (!field.value) {
                                    allValid = false;
                                }
                            }
                        });

                        if (!allValid) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops... Ada yang Kurang',
                                text: 'Mohon periksa kembali dan lengkapi semua data yang diperlukan pada formulir.',
                                confirmButtonColor: '#4f46e5'
                            });
                            return;
                        }
                    }

                    // Validasi tambahan berdasarkan metode pengiriman
                    const selectedMethod = document.querySelector('input[name="delivery_method"]:checked');
                    const isAntar = selectedMethod && selectedMethod.value === 'antar';
                    const isAmbil = selectedMethod && selectedMethod.value === 'ambil';

                    // Validasi cabang untuk metode antar
                    if (isAntar) {
                        const cabangSelect = document.getElementById('cabangSelect');
                        if (cabangSelect && !cabangSelect.value) {
                            e.preventDefault();
                            Swal.fire({
                                icon: 'error',
                                title: 'Pilih Cabang Pengiriman',
                                text: 'Mohon pilih cabang yang akan mengirimkan pesanan Anda',
                                confirmButtonColor: '#4f46e5'
                            });
                            cabangSelect.focus();
                            return;
                        }
                    }

                    // Validasi lokasi pengambilan untuk metode ambil di toko
                    if (isAmbil) {
                        const pickupSelect = document.getElementById('pickupSelect');
                        if (pickupSelect && !pickupSelect.value) {
                            e.preventDefault();
                            Swal.fire({
                                icon: 'error',
                                title: 'Pilih Lokasi Pengambilan',
                                text: 'Mohon pilih cabang tempat Anda akan mengambil pesanan',
                                confirmButtonColor: '#4f46e5'
                            });
                            pickupSelect.focus();
                            return;
                        }
                    }

                    // Validasi Metode Pembayaran
                    const paymentMethod = document.getElementById('payment_method');
                    if (paymentMethod && !paymentMethod.value) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Pilih Metode Pembayaran',
                            text: 'Mohon pilih metode pembayaran Anda.',
                            confirmButtonColor: '#4f46e5'
                        });
                        paymentMethod.focus();
                        return;
                    }


                    // Tampilkan konfirmasi sebelum submit
                    e.preventDefault();
                    Swal.fire({
                        title: 'Konfirmasi Pesanan Anda',
                        html: `Pastikan semua detail pesanan dan pengiriman sudah benar sebelum melanjutkan.`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Lanjutkan & Proses',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#4f46e5',
                        cancelButtonColor: '#64748b'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Tampilkan loading state
                            Swal.fire({
                                title: 'Memproses Pesanan...',
                                text: 'Mohon tunggu sebentar.',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            // Submit form
                            if (form) form.submit();
                        }
                    });
                });
            }
        });
    </script>

</body>

</html>

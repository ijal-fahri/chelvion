<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - GADGETSTORE</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
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
                        dark: '#1e293b',      // slate-800
                        success: '#10b981',    // emerald-500
                    }
                }
            }
        }
    </script>
    
    <style>
        /* Custom styles for better form elements and interactivity */
        body { font-family: 'Poppins', sans-serif; }
        .delivery-option-radio:checked + label {
            border-color: #4f46e5; /* primary color */
            box-shadow: 0 0 0 2px #4f46e5;
        }
        .delivery-option-radio:checked + label .icon-circle {
            background-color: #4f46e5;
            color: white;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">

<header class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-dark">
            <a href="{{ url('/') }}">GADGETSTORE</a>
        </h1>
    </div>
</header>

<main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
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
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            <div class="lg:col-span-2 space-y-8">
                
                <div class="bg-white rounded-2xl shadow-md p-6">
                     <h2 class="text-xl font-bold text-dark mb-5 flex items-center gap-3"><i class="bi bi-truck text-primary"></i> Metode Pengambilan</h2>
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="relative">
                            <input type="radio" name="delivery_method" id="delivery-antar" value="antar" class="sr-only delivery-option-radio" required>
                            <label for="delivery-antar" class="flex items-center p-4 border-2 border-slate-200 rounded-xl cursor-pointer transition-all duration-200 hover:border-primary">
                                <span class="icon-circle flex items-center justify-center w-8 h-8 rounded-full border border-slate-300 text-slate-400 mr-4 transition-colors"><i class="bi bi-box-seam"></i></span>
                                <div>
                                    <p class="font-semibold text-dark">Diantar ke Alamat</p>
                                    <p class="text-sm text-secondary">Dikirim langsung ke lokasimu.</p>
                                </div>
                            </label>
                        </div>
                        <div class="relative">
                            <input type="radio" name="delivery_method" id="delivery-ambil" value="ambil" class="sr-only delivery-option-radio" required>
                            <label for="delivery-ambil" class="flex items-center p-4 border-2 border-slate-200 rounded-xl cursor-pointer transition-all duration-200 hover:border-primary">
                                <span class="icon-circle flex items-center justify-center w-8 h-8 rounded-full border border-slate-300 text-slate-400 mr-4 transition-colors"><i class="bi bi-shop"></i></span>
                                <div>
                                    <p class="font-semibold text-dark">Ambil di Toko</p>
                                    <p class="text-sm text-secondary">Ambil pesanan di cabang terdekat.</p>
                                </div>
                            </label>
                        </div>
                     </div>
                </div>

                <div class="bg-white rounded-2xl shadow-md p-6 hidden" id="alamat-section">
                    <h2 class="text-xl font-bold text-dark mb-5 flex items-center gap-3"><i class="bi bi-geo-alt-fill text-primary"></i> Alamat Pengiriman</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="receiver_name" class="block text-sm font-medium text-slate-600 mb-1">Nama Penerima</label>
                            <input type="text" name="receiver_name" id="receiver_name" placeholder="cth: John Doe" class="block w-full text-base py-3 px-4 rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring-primary">
                        </div>
                        <div>
                            <label for="alamat_lengkap" class="block text-sm font-medium text-slate-600 mb-1">Alamat Lengkap</label>
                            <input type="text" name="alamat_lengkap" id="alamat_lengkap" placeholder="Nama Jalan, No. Rumah, RT/RW" class="block w-full text-base py-3 px-4 rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring-primary">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="kecamatan" class="block text-sm font-medium text-slate-600 mb-1">Kecamatan</label>
                                <input type="text" name="kecamatan" id="kecamatan" class="block w-full text-base py-3 px-4 rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring-primary">
                            </div>
                            <div>
                                <label for="kotaSelect" class="block text-sm font-medium text-slate-600 mb-1">Kabupaten/Kota</label>
                                <select name="kota" id="kotaSelect" class="block w-full text-base py-3 px-4 rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring-primary">
                                    <option value="">Pilih Kabupaten/Kota</option>
                                    <option value="Jakarta Pusat">Jakarta Pusat</option>
                                    <option value="Jakarta Utara">Jakarta Utara</option>
                                    <option value="Jakarta Selatan">Jakarta Selatan</option>
                                    <option value="Jakarta Timur">Jakarta Timur</option>
                                    <option value="Jakarta Barat">Jakarta Barat</option>
                                    <option value="Bogor">Bogor</option>
                                    <option value="Depok">Depok</option>
                                    <option value="Tangerang">Tangerang</option>
                                    <option value="Tangerang Selatan">Tangerang Selatan</option>
                                    <option value="Bekasi">Bekasi</option>
                                </select>
                            </div>
                        </div>
                        <div>
                             <label for="provinsiInput" class="block text-sm font-medium text-slate-600 mb-1">Provinsi</label>
                            <input type="text" name="provinsi" id="provinsiInput" class="block w-full text-base py-3 px-4 rounded-lg border-slate-300 shadow-sm bg-slate-100" readonly>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-md p-6 hidden" id="pickup-section">
                    <h2 class="text-xl font-bold text-dark mb-5 flex items-center gap-3"><i class="bi bi-pin-map-fill text-primary"></i> Pilih Lokasi Pengambilan</h2>
                    <select name="pickup_location" id="pickupSelect" class="block w-full text-base py-3 px-4 rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring-primary">
                        <option value="">Pilih Lokasi Cabang</option>
                        <option value="Mall Botani Square">Mall Botani Square, Bogor</option>
                        <option value="Grand Indonesia">Grand Indonesia, Jakarta Pusat</option>
                        <option value="Summarecon Mall Bekasi">Summarecon Mall Bekasi</option>
                        <option value="AEON Mall BSD">AEON Mall BSD, Tangerang</option>
                        <option value="Kota Kasablanka">Kota Kasablanka, Jakarta Selatan</option>
                    </select>
                </div>

                <div class="bg-white rounded-2xl shadow-md p-6">
                     <h2 class="text-xl font-bold text-dark mb-5 flex items-center gap-3"><i class="bi bi-wallet2 text-primary"></i> Metode Pembayaran</h2>
                     <select name="payment_method" id="payment_method" class="block w-full text-base py-3 px-4 rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring-primary" required>
                        <option value="">Pilih Metode Pembayaran</option>
                        <option value="BCA">Transfer Bank BCA</option>
                        <option value="OVO">OVO</option>
                        <option value="GoPay">GoPay</option>
                        <option value="COD">Bayar di Tempat (COD)</option>
                    </select>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-md p-6 sticky top-8">
                    <h2 class="text-xl font-bold text-dark mb-5 pb-4 border-b border-slate-200">Ringkasan Pesanan</h2>
                    <div class="space-y-4 mb-6 max-h-96 overflow-y-auto pr-2">
                        @foreach ($carts as $item)
                            @php
                                $price = $item->variant->price ?? $item->product->price;
                                $subtotal = $price * $item->quantity;
                            @endphp
                            <div class="flex items-start gap-4">
                                <img src="{{ asset('storage/products/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-20 h-20 object-contain rounded-lg bg-slate-50 border border-slate-200">
                                <div class="flex-grow">
                                    <p class="font-semibold text-base text-dark leading-tight">{{ $item->product->name }}</p>
                                    <p class="text-sm text-secondary">
                                       Qty: {{ $item->quantity }}
                                    </p>
                                </div>
                                <p class="text-base font-semibold text-dark whitespace-nowrap">Rp{{ number_format($subtotal, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="space-y-2 py-4 border-t border-slate-200">
                        <div class="flex justify-between text-base">
                            <span class="text-secondary">Subtotal</span>
                            <span class="font-semibold text-dark">Rp{{ number_format($grandTotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-base">
                            <span class="text-secondary">Pengiriman</span>
                            <span class="font-semibold text-dark">Gratis</span>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center mt-4 pt-4 border-t border-slate-200">
                        <span class="font-bold text-lg text-dark">Total Bayar</span>
                        <span class="font-extrabold text-2xl text-primary">Rp{{ number_format($grandTotal, 0, ',', '.') }}</span>
                    </div>

                    <div class="mt-8">
                        <button type="button" id="btn-confirm" class="w-full bg-primary hover:bg-indigo-700 text-white font-bold py-4 px-8 rounded-xl shadow-lg transition-transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-indigo-300">
                            Konfirmasi & Bayar
                        </button>
                    </div>
                </div>
                 <div class="text-center mt-6">
                    <a href="{{ route('cart.show') }}" class="font-medium text-secondary hover:text-dark transition-colors">
                       &larr; Kembali ke Keranjang
                    </a>
                </div>
            </div>
        </div>
    </form>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('checkout-form');
    const deliveryRadios = document.querySelectorAll('input[name="delivery_method"]');
    const alamatSection = document.getElementById('alamat-section');
    const pickupSection = document.getElementById('pickup-section');
    const kotaSelect = document.getElementById('kotaSelect');
    const provinsiInput = document.getElementById('provinsiInput');
    
    // Form field elements
    const receiverName = document.getElementById('receiver_name');
    const alamatLengkap = document.getElementById('alamat_lengkap');
    const kecamatan = document.getElementById('kecamatan');
    const kota = document.getElementById('kotaSelect');
    const pickupLocation = document.getElementById('pickupSelect');

    function toggleSections() {
        const selectedMethod = document.querySelector('input[name="delivery_method"]:checked');
        const isAntar = selectedMethod && selectedMethod.value === 'antar';
        const isAmbil = selectedMethod && selectedMethod.value === 'ambil';

        alamatSection.classList.toggle('hidden', !isAntar);
        pickupSection.classList.toggle('hidden', !isAmbil);

        // Update required attributes for validation
        if(receiverName) receiverName.required = isAntar;
        alamatLengkap.required = isAntar;
        kecamatan.required = isAntar;
        kota.required = isAntar;
        pickupLocation.required = isAmbil;
    }

    deliveryRadios.forEach(radio => radio.addEventListener('change', toggleSections));
    toggleSections(); // Initial check on page load

    kotaSelect.addEventListener('change', function () {
        const selectedKota = this.value;
        const dkiJakartaCities = ['Jakarta Pusat', 'Jakarta Utara', 'Jakarta Selatan', 'Jakarta Timur', 'Jakarta Barat'];
        if (!selectedKota) provinsiInput.value = '';
        else if (dkiJakartaCities.includes(selectedKota)) provinsiInput.value = 'DKI Jakarta';
        else provinsiInput.value = 'Jawa Barat'; // Assumption for other cities in the list
    });

    document.getElementById('btn-confirm').addEventListener('click', function() {
        if (!form.checkValidity()) {
            form.reportValidity();
            Swal.fire({
                icon: 'error',
                title: 'Oops... Ada yang Kurang',
                text: 'Mohon periksa kembali dan lengkapi semua data yang diperlukan pada formulir.',
                confirmButtonColor: '#4f46e5'
            });
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Pesanan Anda',
            text: "Pastikan semua detail pesanan dan pengiriman sudah benar sebelum melanjutkan.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Lanjutkan & Proses',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#64748b'
        }).then((result) => {
            if (result.isConfirmed) {
                // Optional: Show a loading state
                Swal.fire({
                    title: 'Memproses Pesanan...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });
                // Submit the form
                form.submit();
            }
        });
    });
});
</script>

</body>
</html>
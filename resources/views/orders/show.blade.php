{{-- KODE 100% LENGKAP - TANPA MENGGUNAKAN LAYOUT --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Detail Pesanan #{{ $order->id }} - GADGETSTORE</title>
    
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
            fontFamily: { 'sans': ['Poppins', 'sans-serif'] },
            colors: {
              primary: '#4f46e5', 'primary-light': '#eef2ff',
              secondary: '#64748b', dark: '#1e293b',
              light: '#f1f5f9', 'card-bg': '#ffffff',
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
                <div class="flex items-center space-x-4">
                    <a href="{{ route('cart.show') }}" class="relative w-10 h-10 flex items-center justify-center bg-light rounded-full text-secondary hover:bg-primary-light hover:text-primary transition-colors" title="Keranjang">
                        <i class="bi bi-cart3 text-xl"></i>
                    </a>
                    @auth
                        <div class="relative" data-controller="dropdown">
                            <button type="button" class="w-10 h-10 flex items-center justify-center bg-light rounded-full text-secondary hover:bg-primary-light hover:text-primary transition-colors" onclick="toggleDropdown(event)">
                                <i class="bi bi-person-circle text-xl"></i>
                            </button>
                            <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                @if(auth()->user()->is_admin)
                                   <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Admin Dashboard</a>
                                @endif
                                <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Riwayat Pesanan</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil Saya</a>
                                <div class="border-t border-border-color my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-primary">Login</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold text-white bg-primary rounded-full">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 my-8">
        <div class="bg-card-bg p-6 md:p-8 rounded-2xl shadow-sm">
            
            {{-- Header --}}
            <div class="border-b border-border-color pb-4 mb-6">
                <a href="{{ route('orders.index') }}" class="text-sm text-secondary hover:text-primary mb-4 block"><i class="bi bi-arrow-left"></i> Kembali ke Riwayat Pesanan</a>
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-dark">Detail Pesanan</h1>
                        <p class="text-secondary mt-1">Order #{{ $order->id }}</p>
                    </div>
                     @php
                         $statusClasses = [
                             'Pending' => 'bg-yellow-100 text-yellow-800', 'Processing' => 'bg-blue-100 text-blue-800',
                             'Shipped' => 'bg-green-100 text-green-800', 'Completed' => 'bg-emerald-100 text-emerald-800',
                             'Cancelled' => 'bg-red-100 text-red-800',
                         ];
                         $statusClass = $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800';
                     @endphp
                    <span class="px-4 py-2 text-md font-semibold rounded-full {{ $statusClass }}">
                        Status: {{ $order->status }}
                    </span>
                </div>
            </div>
    
            {{-- Order Details Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Info Penerima --}}
                <div>
                    <h3 class="font-bold text-dark mb-2">Info Penerima</h3>
                    <div class="text-secondary text-sm leading-relaxed">
                        <p class="font-semibold">{{ $order->receiver_name }}</p>
                        <p>Metode Pengambilan: {{ $order->delivery_method }}</p>
                    </div>
                </div>
                {{-- Metode Pembayaran --}}
                <div>
                    <h3 class="font-bold text-dark mb-2">Metode Pembayaran</h3>
                    <p class="text-secondary text-sm">{{ $order->payment_method }}</p>
                </div>
                 {{-- Tanggal --}}
                <div>
                    <h3 class="font-bold text-dark mb-2">Tanggal Pemesanan</h3>
                    <p class="text-secondary text-sm">{{ $order->created_at->format('d F Y, H:i') }}</p>
                </div>
            </div>
    
            {{-- Item List --}}
            <div class="mt-8">
                 <h3 class="text-xl font-bold text-dark mb-4">Rincian Produk</h3>
                 <div class="space-y-4">
                    @foreach($order->items as $item)
                    <div class="flex items-center gap-4 p-4 rounded-lg bg-light/60">
                        {{-- [PERBAIKAN] Menggunakan ikon generik karena tidak ada gambar --}}
                        <div class="w-20 h-20 flex items-center justify-center bg-white rounded-md text-4xl text-gray-300">
                            <i class="bi bi-image"></i>
                        </div>
                        <div class="flex-grow">
                            {{-- [PERBAIKAN] Menggunakan kolom 'product_name' --}}
                            <p class="font-semibold text-dark">{{ $item->product_name }}</p>
                            <p class="text-sm text-secondary">
                                {{-- [PERBAIKAN] Menggunakan kolom 'variant_info' --}}
                                Varian: {{ $item->variant_info }}
                            </p>
                            <p class="text-sm text-secondary">{{ $item->quantity }} x Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-dark">Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endforeach
                 </div>
            </div>
    
            {{-- Grand Total --}}
            <div class="mt-8 pt-6 border-t border-border-color flex justify-end">
                <div class="w-full max-w-sm">
                    <div class="flex justify-between text-secondary mb-2">
                        <span>Subtotal</span>
                        {{-- [PERBAIKAN] Menggunakan kolom 'total_price' --}}
                        <span>Rp{{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>
                     <div class="flex justify-between text-secondary mb-2">
                        <span>Pengiriman</span>
                        <span>Rp0</span>
                    </div>
                    <div class="flex justify-between font-bold text-dark text-xl mt-2">
                        <span>Total</span>
                        {{-- [PERBAIKAN] Menggunakan kolom 'total_price' --}}
                        <span>Rp{{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- Footer (Opsional) --}}
    <footer class="bg-dark text-white mt-12 py-8">
        <div class="container mx-auto px-4 text-center text-secondary">
            <p>&copy; {{ date('Y') }} GADGETSTORE. All Rights Reserved.</p>
        </div>
    </footer>
    
    <script>
        function toggleDropdown(event) {
            event.stopPropagation();
            document.getElementById('user-dropdown').classList.toggle('hidden');
        }

        window.onclick = function(event) {
            const dropdown = document.getElementById('user-dropdown');
            if (dropdown && !dropdown.classList.contains('hidden')) {
                dropdown.classList.add('hidden');
            }
        }
    </script>
</body>
</html>


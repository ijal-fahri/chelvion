{{-- KODE 100% LENGKAP - TANPA MENGGUNAKAN LAYOUT --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Riwayat Pesanan - GADGETSTORE</title>
    
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
            <h1 class="text-2xl md:text-3xl font-bold text-dark mb-6 border-b border-border-color pb-4">
                <i class="bi bi-receipt-cutoff mr-2 text-primary"></i>
                Riwayat Pesanan Anda
            </h1>
    
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-6" role="alert">
                    <p class="font-bold">Berhasil!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif
    
            @if($orders->isEmpty())
                <div class="text-center py-16">
                    <i class="bi bi-box2-heart text-6xl text-gray-300"></i>
                    <h3 class="mt-4 text-xl font-semibold text-dark">Anda Belum Memiliki Pesanan</h3>
                    <p class="mt-2 text-secondary">Sepertinya Anda belum melakukan checkout. Yuk, mulai belanja!</p>
                    <a href="{{ route('dashboard') }}" class="mt-6 inline-block bg-primary text-white font-bold py-3 px-6 rounded-lg hover:bg-indigo-700 transition-all duration-300">
                        Mulai Belanja
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-border-color">
                        <thead class="bg-light">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-secondary uppercase tracking-wider">Invoice</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-secondary uppercase tracking-wider">Tanggal</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-secondary uppercase tracking-wider">Total</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-secondary uppercase tracking-wider">Status</th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Aksi</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-border-color">
                            @foreach($orders as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-dark">{{ $order->invoice_number ?? '#' . $order->id }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-secondary">{{ $order->created_at->format('d M Y, H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-dark">Rp{{ number_format($order->total_price, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusClasses = [
                                                'Pending' => 'bg-yellow-100 text-yellow-800',
                                                'Processing' => 'bg-blue-100 text-blue-800',
                                                'Shipped' => 'bg-indigo-100 text-indigo-800',
                                                'Completed' => 'bg-green-100 text-green-800',
                                                'Cancelled' => 'bg-red-100 text-red-800',
                                            ];
                                        @endphp
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('orders.show', $order->id) }}" class="text-primary hover:text-indigo-700 font-bold">Lihat Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            @endif
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


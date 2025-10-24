{{-- [PERBAIKAN] Komponen Sidebar yang sepenuhnya baru dan responsif --}}

{{-- 1. Sidebar untuk Desktop (Tampil di layar medium ke atas) --}}
<aside class="hidden md:flex w-64 bg-gray-800 text-white min-h-screen p-4 flex-col justify-between fixed inset-y-0 left-0 z-40">
    <div>
        {{-- Header Logo dan Nama Website --}}
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 pb-4 border-b border-gray-700">
            <i class="bi bi-hexagon-fill text-3xl text-indigo-400"></i>
            <h2 class="text-xl font-bold tracking-wider">CELVION</h2>
        </a>
        
        <nav class="mt-8">
            <h3 class="px-4 mb-2 text-xs text-gray-400 uppercase tracking-wider font-semibold">Menu Utama</h3>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 shadow-lg text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <i class="bi bi-house-door-fill w-5 text-center"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.orders.*') ? 'bg-indigo-600 shadow-lg text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                         <i class="bi bi-clock-history w-5 text-center"></i> 
                         <span>Riwayat Order</span>
                    </a>
                </li>
            </ul>

            <h3 class="px-4 mt-8 mb-2 text-xs text-gray-400 uppercase tracking-wider font-semibold">Manajemen</h3>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-indigo-600 shadow-lg text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <i class="bi bi-box-seam-fill w-5 text-center"></i> <span>Data Produk</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.employees.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.employees.*') ? 'bg-indigo-600 shadow-lg text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <i class="bi bi-people-fill w-5 text-center"></i> <span>Data Karyawan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.checkouts.index') }}" class="flex items-center justify-between px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.checkouts.*') ? 'bg-indigo-600 shadow-lg text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-receipt w-5 text-center"></i>
                            <span>Data Pesanan</span>
                        </div>
                        @if(isset($pendingCheckoutCount) && $pendingCheckoutCount > 0)
                            <span class="flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 rounded-full animate-pulse">
                                {{ $pendingCheckoutCount }}
                            </span>
                        @endif
                    </a>
                </li>
                {{-- [PERBAIKAN] Link Halaman Permintaan disesuaikan --}}
                <li>
                    <a href="{{ route('admin.requests.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.requests.*') ? 'bg-indigo-600 shadow-lg text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <i class="bi bi-envelope-paper-fill w-5 text-center"></i> <span>Permintaan</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <div class="bg-gray-700/50 p-4 rounded-lg text-center">
        <div class="w-16 h-16 bg-indigo-500/20 rounded-full mx-auto flex items-center justify-center mb-3">
            <i class="bi bi-gift-fill text-3xl text-indigo-300"></i>
        </div>
        <h4 class="font-semibold text-white">Tambah Produk Baru</h4>
        <p class="text-xs text-gray-400 mt-1 mb-3">Tingkatkan penjualan dengan menambahkan variasi produk baru ke toko Anda.</p>
        <a href="{{ route('admin.products.index') }}" class="w-full block text-sm bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded-lg transition-colors">
            Kelola Produk
        </a>
    </div>
</aside>

{{-- 2. Navigasi Bawah untuk Mobile (Tampil di layar kecil) --}}
<footer class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-50">
    <nav class="flex justify-around items-center h-16">
        <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center justify-center w-full transition-colors {{ request()->routeIs('admin.dashboard') ? 'text-indigo-600' : 'text-gray-500 hover:text-indigo-500' }}">
            <i class="bi bi-house-door-fill text-xl"></i>
            <span class="text-xs font-medium mt-1">Home</span>
        </a>
        <a href="{{ route('admin.products.index') }}" class="flex flex-col items-center justify-center w-full transition-colors {{ request()->routeIs('admin.products.*') ? 'text-indigo-600' : 'text-gray-500 hover:text-indigo-500' }}">
            <i class="bi bi-box-seam-fill text-xl"></i>
            <span class="text-xs font-medium mt-1">Produk</span>
        </a>
        <a href="{{ route('admin.checkouts.index') }}" class="relative flex flex-col items-center justify-center w-full transition-colors {{ request()->routeIs('admin.checkouts.*') ? 'text-indigo-600' : 'text-gray-500 hover:text-indigo-500' }}">
            <i class="bi bi-receipt text-xl"></i>
            <span class="text-xs font-medium mt-1">Pesanan</span>
            @if(isset($pendingCheckoutCount) && $pendingCheckoutCount > 0)
                <span class="absolute top-0 right-1/4 -mr-2 flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full">
                    {{ $pendingCheckoutCount }}
                </span>
            @endif
        </a>
        {{-- [PERBAIKAN] Link Halaman Permintaan untuk Mobile disesuaikan --}}
        <a href="{{ route('admin.requests.index') }}" class="flex flex-col items-center justify-center w-full transition-colors {{ request()->routeIs('admin.requests.*') ? 'text-indigo-600' : 'text-gray-500 hover:text-indigo-500' }}">
            <i class="bi bi-envelope-paper-fill text-xl"></i>
            <span class="text-xs font-medium mt-1">Request</span>
        </a>
        <a href="{{ route('admin.orders.index') }}" class="flex flex-col items-center justify-center w-full transition-colors {{ request()->routeIs('admin.orders.*') ? 'text-indigo-600' : 'text-gray-500 hover:text-indigo-500' }}">
            <i class="bi bi-clock-history text-xl"></i>
            <span class="text-xs font-medium mt-1">Riwayat</span>
        </a>
    </nav>
</footer>


{{-- [PENTING] Menyesuaikan padding bawah konten utama untuk mobile agar tidak tertutup navigasi bawah --}}
<style>
    @media (max-width: 767px) {
        main {
            padding-bottom: 80px !important; /* 64px (tinggi nav) + 16px (jarak) */
        }
    }
</style>


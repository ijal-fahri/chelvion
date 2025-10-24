{{-- [DIPERBARUI] Komponen Sidebar untuk Kasir dengan Navigasi Mobile dan Menu Transaksi Online --}}

{{-- 1. Sidebar untuk Desktop --}}
<aside class="hidden md:flex w-64 bg-gray-800 text-white min-h-screen p-4 flex-col justify-between fixed inset-y-0 left-0 z-40">
    <div>
        {{-- Header Logo --}}
        <a href="{{ route('kasir.dashboard') }}" class="flex items-center gap-3 pb-4 border-b border-gray-700">
            <i class="bi bi-hexagon-fill text-3xl text-indigo-400"></i>
            <h2 class="text-xl font-bold tracking-wider">CELVION</h2>
        </a>
        
        {{-- Grup Navigasi untuk Kasir --}}
        <nav class="mt-8">
            <h3 class="px-4 mb-2 text-xs text-gray-400 uppercase tracking-wider font-semibold">Menu Utama</h3>
            <ul class="space-y-2">
                <li><a href="{{ route('kasir.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('kasir.dashboard') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700' }}"><i class="bi bi-grid-1x2-fill w-5 text-center"></i> <span>Dashboard</span></a></li>
                <li><a href="{{ route('kasir.transaksi') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('kasir.transaksi') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700' }}"><i class="bi bi-cart-plus-fill w-5 text-center"></i> <span>Transaksi Penjualan</span></a></li>
                <li><a href="{{ route('kasir.online') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('kasir.online') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700' }}"><i class="bi bi-cloud-check-fill w-5 text-center"></i> <span>Transaksi Online</span></a></li>
            </ul>

            <h3 class="px-4 mt-8 mb-2 text-xs text-gray-400 uppercase tracking-wider font-semibold">Manajemen</h3>
            <ul class="space-y-2">
                {{-- [DIPERBAIKI] Menggunakan route 'kasir.kualitas.index' dan 'kasir.kualitas.*' --}}
                <li><a href="{{ route('kasir.kualitas.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('kasir.kualitas.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700' }}"><i class="bi bi-arrow-left-right w-5 text-center"></i> <span>Tukar Tambah</span></a></li>
                <li><a href="{{ route('kasir.riwayat') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('kasir.riwayat') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700' }}"><i class="bi bi-clock-history w-5 text-center"></i> <span>Riwayat Transaksi</span></a></li>
            </ul>

            <h3 class="px-4 mt-8 mb-2 text-xs text-gray-400 uppercase tracking-wider font-semibold">Laporan</h3>
             <ul class="space-y-2">
                <li><a href="{{ route('kasir.laporan') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('kasir.laporan') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700' }}"><i class="bi bi-file-earmark-bar-graph-fill w-5 text-center"></i> <span>Laporan Kasir</span></a></li>
            </ul>
        </nav>
    </div>
</aside>

{{-- 2. Navigasi Bawah untuk Mobile (Desain Diperbarui) --}}
<footer class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-50">
    <nav class="flex justify-evenly h-16">
        <a href="{{ route('kasir.dashboard') }}" class="flex-1 flex flex-col items-center justify-center pt-2 transition-all duration-200 ease-in-out {{ request()->routeIs('kasir.dashboard') ? 'text-indigo-600 border-t-2 border-indigo-600' : 'text-gray-500 hover:text-indigo-500 border-t-2 border-transparent' }}">
            <i class="bi bi-grid-1x2-fill text-xl"></i>
            <span class="text-xs font-medium text-center leading-tight mt-1">Dashboard</span>
        </a>
        <a href="{{ route('kasir.transaksi') }}" class="flex-1 flex flex-col items-center justify-center pt-2 transition-all duration-200 ease-in-out {{ request()->routeIs('kasir.transaksi') ? 'text-indigo-600 border-t-2 border-indigo-600' : 'text-gray-500 hover:text-indigo-500 border-t-2 border-transparent' }}">
            <i class="bi bi-cart-plus-fill text-xl"></i>
            <span class="text-xs font-medium text-center leading-tight mt-1">Penjualan</span>
        </a>
        <a href="{{ route('kasir.online') }}" class="flex-1 flex flex-col items-center justify-center pt-2 transition-all duration-200 ease-in-out {{ request()->routeIs('kasir.online') ? 'text-indigo-600 border-t-2 border-indigo-600' : 'text-gray-500 hover:text-indigo-500 border-t-2 border-transparent' }}">
            <i class="bi bi-cloud-check-fill text-xl"></i>
            <span class="text-xs font-medium text-center leading-tight mt-1">Online</span>
        </a>
        {{-- [DIPERBAIKI] Menggunakan route 'kasir.kualitas.index' dan 'kasir.kualitas.*' --}}
        <a href="{{ route('kasir.kualitas.index') }}" class="flex-1 flex flex-col items-center justify-center pt-2 transition-all duration-200 ease-in-out {{ request()->routeIs('kasir.kualitas.*') ? 'text-indigo-600 border-t-2 border-indigo-600' : 'text-gray-500 hover:text-indigo-500 border-t-2 border-transparent' }}">
            <i class="bi bi-arrow-left-right text-xl"></i>
            <span class="text-xs font-medium text-center leading-tight mt-1">Tukar</span>
        </a>
        <a href="{{ route('kasir.riwayat') }}" class="flex-1 flex flex-col items-center justify-center pt-2 transition-all duration-200 ease-in-out {{ request()->routeIs('kasir.riwayat') ? 'text-indigo-600 border-t-2 border-indigo-600' : 'text-gray-500 hover:text-indigo-500 border-t-2 border-transparent' }}">
            <i class="bi bi-clock-history text-xl"></i>
            <span class="text-xs font-medium text-center leading-tight mt-1">Riwayat</span>
        </a>
        <a href="{{ route('kasir.laporan') }}" class="flex-1 flex flex-col items-center justify-center pt-2 transition-all duration-200 ease-in-out {{ request()->routeIs('kasir.laporan') ? 'text-indigo-600 border-t-2 border-indigo-600' : 'text-gray-500 hover:text-indigo-500 border-t-2 border-transparent' }}">
            <i class="bi bi-file-earmark-bar-graph-fill text-xl"></i>
            <span class="text-xs font-medium text-center leading-tight mt-1">Laporan</span>
        </a>
    </nav>
</footer>

{{-- Style tambahan untuk memberi ruang di bawah konten pada versi mobile --}}
<style>
    @media (max-width: 767px) {
        body {
            /* Menargetkan body untuk memberi padding bawah agar konten tidak tertutup navigasi */
            padding-bottom: 80px !important;
        }
    }
</style>

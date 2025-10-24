{{-- Sidebar khusus untuk Staf Gudang --}}
<aside class="hidden md:flex w-64 bg-gray-800 text-white min-h-screen p-4 flex-col justify-between fixed inset-y-0 left-0 z-40">
    <div>
        {{-- Header Logo --}}
        <a href="{{ route('staff.dashboard') }}" class="flex items-center gap-3 pb-4 border-b border-gray-700">
            <i class="bi bi-hexagon-fill text-3xl text-indigo-400"></i>
            <h2 class="text-xl font-bold tracking-wider">CELVION</h2>
        </a>
        
        {{-- Grup Navigasi --}}
        <nav class="mt-8">
            <h3 class="px-4 mb-2 text-xs text-gray-400 uppercase tracking-wider font-semibold">Menu Utama</h3>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('staff.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('staff.dashboard') ? 'bg-indigo-600 shadow-lg text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <i class="bi bi-grid-1x2-fill w-5 text-center"></i> 
                        <span>Dashboard Stok</span>
                    </a>
                </li>
                <li>
                    
            </ul>

            <h3 class="px-4 mt-8 mb-2 text-xs text-gray-400 uppercase tracking-wider font-semibold">Operasional Gudang</h3>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('staff.inout') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('staff.inout') ? 'bg-indigo-600 shadow-lg text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <i class="bi bi-arrow-left-right w-5 text-center"></i> 
                        <span>Barang Masuk & Keluar</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('staff.manage.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('staff.manage.*') ? 'bg-indigo-600 shadow-lg text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <i class="bi bi-boxes w-5 text-center"></i> 
                        <span>Manajemen Stok</span>
                    </a>
                </li>
                <li>
                    {{-- [PERBAIKAN] Menggunakan route 'staff.requests.index' (dengan 's') --}}
                    <a href="{{ route('staff.requests.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('staff.requests.*') ? 'bg-indigo-600 shadow-lg text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <i class="bi bi-clipboard-data-fill w-5 text-center"></i> 
                        <span>Permintaan Barang</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('staff.kualitas') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('staff.kualitas') ? 'bg-indigo-600 shadow-lg text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <i class="bi bi-clipboard2-check-fill w-5 text-center"></i> 
                        <span>Kontrol Kualitas</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

{{-- Navigasi Bawah untuk Mobile --}}
<footer class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-50">
    <nav class="flex justify-around items-center h-16">
        <a href="{{ route('staff.dashboard') }}" class="flex flex-col items-center justify-center w-full transition-colors {{ request()->routeIs('staff.dashboard') ? 'text-indigo-600' : 'text-gray-500 hover:text-indigo-500' }}">
            <i class="bi bi-grid-1x2-fill text-2xl"></i>
            <span class="text-xs font-medium">Dashboard</span>
        </a>
        <a href="{{ route('staff.manage.index') }}" class="flex flex-col items-center justify-center w-full transition-colors {{ request()->routeIs('staff.manage.*') ? 'text-indigo-600' : 'text-gray-500 hover:text-indigo-500' }}">
            <i class="bi bi-boxes text-2xl"></i>
            <span class="text-xs font-medium">Stok</span>
        </a>
        <a href="{{ route('staff.inout') }}" class="flex flex-col items-center justify-center w-full transition-colors {{ request()->routeIs('staff.inout') ? 'text-indigo-600' : 'text-gray-500 hover:text-indigo-500' }}">
            <i class="bi bi-arrow-left-right text-2xl"></i>
            <span class="text-xs font-medium">In/Out</span>
        </a>
        {{-- [PERBAIKAN] Menggunakan route 'staff.requests.index' (dengan 's') --}}
        <a href="{{ route('staff.requests.index') }}" class="flex flex-col items-center justify-center w-full transition-colors {{ request()->routeIs('staff.requests.*') ? 'text-indigo-600' : 'text-gray-500 hover:text-indigo-500' }}">
            <i class="bi bi-clipboard-data-fill text-2xl"></i>
            <span class="text-xs font-medium">Permintaan</span>
        </a>
        <a href="{{ route('staff.profile') }}" class="flex flex-col items-center justify-center w-full transition-colors {{ request()->routeIs('staff.profile') ? 'text-indigo-600' : 'text-gray-500 hover:text-indigo-500' }}">
            <i class="bi bi-person-circle text-2xl"></i>
            <span class="text-xs font-medium">Profil</span>
        </a>
    </nav>
</footer>


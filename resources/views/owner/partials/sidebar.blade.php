{{-- Sidebar khusus untuk Owner --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
{{-- 1. Sidebar untuk Desktop --}}
<aside class="hidden md:flex w-64 bg-gray-800 text-white min-h-screen p-4 flex-col justify-between fixed inset-y-0 left-0 z-40">
    <div>
        {{-- Header Logo dan Nama Website --}}
        <a href="{{ route('owner.dashboard') }}" class="flex items-center gap-3 pb-4 border-b border-gray-700">
            <i class="bi bi-hexagon-fill text-3xl text-indigo-400"></i>
            <h2 class="text-xl font-bold tracking-wider">CELVION</h2>
        </a>
        
        {{-- Grup Navigasi --}}
        <nav class="mt-8">
            <h3 class="px-4 mb-2 text-xs text-gray-400 uppercase tracking-wider font-semibold">Menu Utama</h3>
            <ul class="space-y-2">
                <li><a href="{{ route('owner.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('owner.dashboard') ? 'bg-indigo-600 shadow-lg text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}"><i class="bi bi-house-door-fill w-5 text-center"></i> <span>Dashboard</span></a></li>
                <li><a href="{{ route('owner.riwayat') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('owner.riwayat') ? 'bg-indigo-600 shadow-lg text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}"><i class="bi bi-clock-history w-5 text-center"></i> <span>Riwayat Order</span></a></li>
            </ul>

            <h3 class="px-4 mt-8 mb-2 text-xs text-gray-400 uppercase tracking-wider font-semibold">Manajemen</h3>
            <ul class="space-y-2">
                <li><a href="{{ route('owner.dataadmin.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('owner.dataadmin.*') ? 'bg-indigo-600 shadow-lg text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}"><i class="bi bi-person-badge-fill w-5 text-center"></i> <span>Data Admin</span></a></li>
                <li><a href="{{ route('owner.datakaryawan.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('owner.datakaryawan.*') ? 'bg-indigo-600 shadow-lg text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}"><i class="bi bi-people-fill w-5 text-center"></i> <span>Data Karyawan</span></a></li>
                <li><a href="{{ route('owner.datacabang') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('owner.datacabang') ? 'bg-indigo-600 shadow-lg text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}"><i class="bi bi-diagram-3-fill w-5 text-center"></i> <span>Data Cabang</span></a></li>
                
                {{-- [DIPERBAIKI] Ikon voucher diganti dengan 'bi-ticket-detailed-fill' yang sudah terbukti berfungsi --}}
                <li><a href="{{ route('owner.datavoucher.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('owner.datavoucher.*') ? 'bg-indigo-600 shadow-lg text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}"><i class="bi bi-ticket-detailed-fill w-5 text-center"></i> <span>Data Voucher</span></a></li>
            </ul>
        </nav>
    </div>
</aside>

{{-- 2. Navigasi Bawah untuk Mobile --}}
<footer class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-50">
    <nav class="grid grid-cols-6 items-center h-16">
        <a href="{{ route('owner.dashboard') }}" class="flex flex-col items-center justify-center w-full h-full transition-colors {{ request()->routeIs('owner.dashboard') ? 'text-indigo-600' : 'text-gray-500 hover:text-indigo-500' }}">
            <i class="bi bi-house-door-fill text-2xl"></i>
            <span class="text-xs font-medium">Home</span>
        </a>
        <a href="{{ route('owner.riwayat') }}" class="flex flex-col items-center justify-center w-full h-full transition-colors {{ request()->routeIs('owner.riwayat') ? 'text-indigo-600' : 'text-gray-500 hover:text-indigo-500' }}">
            <i class="bi bi-clock-history text-2xl"></i>
            <span class="text-xs font-medium">Riwayat</span>
        </a>
        <a href="{{ route('owner.datakaryawan.index') }}" class="flex flex-col items-center justify-center w-full h-full transition-colors {{ request()->routeIs('owner.datakaryawan.*') ? 'text-indigo-600' : 'text-gray-500 hover:text-indigo-500' }}">
            <i class="bi bi-people-fill text-2xl"></i>
            <span class="text-xs font-medium">Karyawan</span>
        </a>
        <a href="{{ route('owner.datacabang') }}" class="flex flex-col items-center justify-center w-full h-full transition-colors {{ request()->routeIs('owner.datacabang') ? 'text-indigo-600' : 'text-gray-500 hover:text-indigo-500' }}">
            <i class="bi bi-diagram-3-fill text-2xl"></i>
            <span class="text-xs font-medium">Cabang</span>
        </a>
        {{-- [DIPERBAIKI] Ikon voucher untuk mobile juga diganti agar konsisten --}}
        <a href="{{ route('owner.datavoucher.index') }}" class="flex flex-col items-center justify-center w-full h-full transition-colors {{ request()->routeIs('owner.datavoucher.*') ? 'text-indigo-600' : 'text-gray-500 hover:text-indigo-500' }}">
            <i class="bi bi-ticket-detailed-fill text-2xl"></i>
            <span class="text-xs font-medium">Voucher</span>
        </a>
        <a href="{{ route('owner.dataadmin.index') }}" class="flex flex-col items-center justify-center w-full h-full transition-colors {{ request()->routeIs('owner.dataadmin.*') ? 'text-indigo-600' : 'text-gray-500 hover:text-indigo-500' }}">
            <i class="bi bi-person-badge-fill text-2xl"></i>
            <span class="text-xs font-medium">Admin</span>
        </a>
    </nav>
</footer>


{{-- Style padding bawah untuk mobile agar konten tidak tertutup navigasi --}}
<style>
    @media (max-width: 767px) {
        body.has-bottom-nav .main-content {
            padding-bottom: 80px !important;
        }
    }
</style>

<script>
// Menambahkan class ke body jika nav bawah (mobile) ada
document.addEventListener("DOMContentLoaded", function() {
  if (document.querySelector('footer.md\\:hidden')) {
    document.body.classList.add('has-bottom-nav');
    
    // Menambahkan class 'main-content' ke elemen <main> jika ada
    const mainContent = document.querySelector('main'); 
    if(mainContent) {
        mainContent.classList.add('main-content');
    }
  }
});
</script>
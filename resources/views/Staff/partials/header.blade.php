{{-- Header khusus untuk Staf Gudang --}}

<style>
    /* Animasi untuk dropdown menu */
    #profile-dropdown-menu, #notification-dropdown-menu {
        transition: opacity 0.2s ease-out, transform 0.2s ease-out;
        transform-origin: top right;
        opacity: 0;
        transform: scale(0.95);
        pointer-events: none;
    }
    #profile-dropdown-menu.active, #notification-dropdown-menu.active {
        opacity: 1;
        transform: scale(1);
        pointer-events: auto;
    }
    /* Animasi untuk ikon chevron */
    #profile-dropdown-btn.active .chevron-icon {
        transform: rotate(180deg);
    }
</style>

<div class="bg-white p-4 sm:p-6 rounded-2xl shadow-lg mb-8">
    <header class="flex justify-between items-center">
        
        {{-- Judul Halaman (Dinamis dari view utama) --}}
        <div>
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800">{{ $title ?? 'Judul Halaman' }}</h1>
            @if(isset($subtitle) && $subtitle)
            <p class="hidden sm:block text-gray-500 mt-1">{{ $subtitle }}</p>
            @endif
        </div>
        
        {{-- Bagian Kanan Header: Tanggal, Notifikasi & Profil --}}
        <div class="flex items-center gap-2 sm:gap-4">
            {{-- Info Tanggal --}}
            <div class="hidden lg:flex items-center text-sm text-gray-600 bg-gray-50 px-4 py-2 rounded-lg border">
                <i class="bi bi-calendar-week mr-2 text-gray-500"></i>
                <span>{{ \Carbon\Carbon::now()->translatedFormat('l, j F Y') }}</span>
            </div>

            {{-- [BARU] Tombol Notifikasi --}}
            <div class="relative">
                <button id="notification-btn" class="relative w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors">
                    <i class="bi bi-bell-fill text-lg"></i>
                    {{-- [DINAMIS] Badge Notifikasi: Tampilkan span ini jika ada notifikasi baru --}}
                    <span class="absolute top-0 right-0 block h-3 w-3 rounded-full bg-red-500 border-2 border-white"></span>
                </button>

                {{-- [BARU] Dropdown Notifikasi --}}
                <div id="notification-dropdown-menu" class="absolute right-0 mt-2 w-72 sm:w-80 bg-white rounded-lg shadow-xl z-50 border">
                    <div class="px-4 py-3 border-b">
                        <h4 class="text-base font-semibold text-gray-800">Notifikasi</h4>
                    </div>
                    <div class="py-2 max-h-80 overflow-y-auto">
                        {{-- Contoh Item Notifikasi --}}
                        <a href="#" class="flex items-start gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                            <div class="w-10 h-10 flex-shrink-0 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center"><i class="bi bi-box-seam-fill"></i></div>
                            <div>
                                <p class="font-semibold">Stok menipis</p>
                                <p class="text-xs text-gray-500">Produk "iPhone 15 Pro" hanya tersisa 3 unit.</p>
                                <p class="text-xs text-gray-400 mt-1">5 menit yang lalu</p>
                            </div>
                        </a>
                        <a href="#" class="flex items-start gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                            <div class="w-10 h-10 flex-shrink-0 bg-green-100 text-green-600 rounded-full flex items-center justify-center"><i class="bi bi-check-circle-fill"></i></div>
                            <div>
                                <p class="font-semibold">Permintaan Stok Disetujui</p>
                                <p class="text-xs text-gray-500">Permintaan Anda untuk 10 unit "Samsung S24 Ultra" telah disetujui.</p>
                                <p class="text-xs text-gray-400 mt-1">1 jam yang lalu</p>
                            </div>
                        </a>
                        {{-- Akhir Contoh --}}
                    </div>
                    {{-- [UPDATE] Link href mengarah ke halaman notifikasi --}}
                    <div class="px-4 py-2 border-t text-center">
                        <a href="{{ route('staff.notifikasi.index') }}" class="text-sm font-semibold text-indigo-600 hover:underline">Lihat Semua Notifikasi</a>
                    </div>
                </div>
            </div>

            {{-- Dropdown Profil --}}
            <div class="relative">
                <button id="profile-dropdown-btn" class="flex items-center gap-3 text-left rounded-full md:rounded-lg md:p-2 hover:bg-gray-100 transition-colors duration-200">
                    {{-- [DINAMIS] Mengambil data user yang sedang login --}}
                    <img class="w-10 h-10 rounded-full object-cover border-2 border-indigo-100" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=eef2ff&color=4f46e5&font-size=0.5" alt="Avatar">
                    <div class="hidden md:block">
                        <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', Auth::user()->usertype) }}</p>
                    </div>
                    <i class="bi bi-chevron-down hidden md:block text-gray-500 transition-transform duration-200 chevron-icon"></i>
                </button>

                {{-- Menu Dropdown --}}
                <div id="profile-dropdown-menu" class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl z-50 py-2 border">
                    <div class="px-4 py-2 border-b mb-2">
                        <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                    </div>
                    {{-- [PENTING] Link profil menunjuk ke route 'staff.profile' --}}
                    <a href="{{ route('staff.profile') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                        <i class="bi bi-person-circle w-5 text-gray-500"></i>
                        <span>Profil Saya</span>
                    </a>
                    <div class="border-t my-2"></div>
                    {{-- Form Logout yang fungsional --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                            <i class="bi bi-box-arrow-right w-5"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>
</div>

{{-- Script fungsionalitas dropdown --}}
<script>
    $(document).ready(function() {
        // Fungsi untuk mengelola dropdown secara umum
        function setupDropdown(btnId, menuId) {
            const dropdownBtn = $(`#${btnId}`);
            const dropdownMenu = $(`#${menuId}`);

            dropdownBtn.on('click', function(e) {
                e.stopPropagation();
                // Tutup dropdown lain yang mungkin terbuka
                $('.dropdown-menu.active').not(dropdownMenu).removeClass('active');
                $('.dropdown-btn.active').not(dropdownBtn).removeClass('active');
                
                dropdownMenu.toggleClass('active');
                dropdownBtn.toggleClass('active');
            });
        }

        // Inisialisasi dropdown profil dan notifikasi
        setupDropdown('profile-dropdown-btn', 'profile-dropdown-menu');
        setupDropdown('notification-btn', 'notification-dropdown-menu');

        // Menambahkan class untuk identifikasi
        $('#profile-dropdown-btn').addClass('dropdown-btn');
        $('#profile-dropdown-menu').addClass('dropdown-menu');
        $('#notification-btn').addClass('dropdown-btn');
        $('#notification-dropdown-menu').addClass('dropdown-menu');

        // Menutup dropdown ketika klik di luar
        $(window).on('click', function(e) {
            if ($('.dropdown-menu').hasClass('active')) {
                // Cek apakah klik bukan pada tombol atau menu dropdown manapun
                if (!$('.dropdown-btn').is(e.target) && $('.dropdown-btn').has(e.target).length === 0 &&
                    !$('.dropdown-menu').is(e.target) && $('.dropdown-menu').has(e.target).length === 0) {
                    $('.dropdown-menu').removeClass('active');
                    $('.dropdown-btn').removeClass('active');
                }
            }
        });
    });
</script>


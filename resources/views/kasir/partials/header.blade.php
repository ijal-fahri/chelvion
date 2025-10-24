{{-- [DIPERBAIKI] Komponen Header dengan data dinamis dan route yang benar --}}

<style>
    /* Animasi untuk dropdown menu */
    #profile-dropdown-menu {
        transition: opacity 0.2s ease-out, transform 0.2s ease-out;
        transform-origin: top right;
        opacity: 0;
        transform: scale(0.95);
        pointer-events: none;
    }

    #profile-dropdown-menu.active {
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

        {{-- Judul Halaman --}}
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">{{ $title ?? 'Judul Halaman' }}</h1>
            @if (isset($subtitle) && $subtitle)
                <p class="hidden sm:block text-gray-500 mt-1">{{ $subtitle }}</p>
            @endif
        </div>

        {{-- Bagian Kanan Header: Tanggal & Profil --}}
        <div class="flex items-center gap-4">
            {{-- Info Tanggal (Memerlukan library Carbon di Laravel) --}}
            <div class="hidden lg:flex items-center text-sm text-gray-600 bg-gray-50 px-4 py-2 rounded-lg border">
                <i class="bi bi-calendar-week mr-2 text-gray-500"></i>
                {{-- Pastikan locale di config/app.php sudah 'id' --}}
                <span>{{ \Carbon\Carbon::now()->translatedFormat('l, j F Y') }}</span>
            </div>

            {{-- Dropdown Profil --}}
            <div class="relative">
                {{-- ... --}}
                <button id="profile-dropdown-btn"
                    class="flex items-center gap-3 text-left p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    {{-- AVATAR BARU (DINAMIS) --}}
                    <img class="w-10 h-10 rounded-full object-cover border-2 border-indigo-100"
                        src="{{ auth()->user()->photo_url }}" alt="Avatar">

                    <div class="hidden md:block">
                        {{-- ... --}}
                        {{-- Nama pengguna dan role dinamis --}}
                        <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ ucfirst(auth()->user()->usertype) }}</p>
                    </div>
                    <i
                        class="bi bi-chevron-down hidden md:block text-gray-500 transition-transform duration-200 chevron-icon"></i>
                </button>

                {{-- Menu Dropdown --}}
                <div id="profile-dropdown-menu"
                    class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl z-50 py-2 border">
                    <div class="px-4 py-2 border-b mb-2">
                        {{-- Nama dan email dinamis --}}
                        <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <a href="{{ route('kasir.profile') }}"
                        class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                        <i class="bi bi-person-circle w-5 text-gray-500"></i>
                        <span>Profil Saya</span>
                    </a>
                    <div class="border-t my-2"></div>
                    {{-- Form logout yang berfungsi --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
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
    // Memastikan script hanya berjalan setelah DOM siap
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownBtn = document.getElementById('profile-dropdown-btn');
        const dropdownMenu = document.getElementById('profile-dropdown-menu');

        if (dropdownBtn && dropdownMenu) {
            dropdownBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdownMenu.classList.toggle('active');
                dropdownBtn.classList.toggle('active');
            });

            window.addEventListener('click', function(e) {
                if (dropdownMenu.classList.contains('active')) {
                    if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                        dropdownMenu.classList.remove('active');
                        dropdownBtn.classList.remove('active');
                    }
                }
            });
        }
    });
</script>

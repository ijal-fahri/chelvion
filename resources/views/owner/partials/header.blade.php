{{-- resources/views/owner/partials/header.blade.php --}}

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
            @if(isset($subtitle) && $subtitle)
            <p class="hidden sm:block text-gray-500 mt-1">{{ $subtitle }}</p>
            @endif
        </div>
        
        {{-- Bagian Kanan Header: Tanggal & Profil --}}
        <div class="flex items-center gap-4">
            {{-- Info Tanggal (Menggunakan Carbon dari Laravel) --}}
            <div class="hidden lg:flex items-center text-sm text-gray-600 bg-gray-50 px-4 py-2 rounded-lg border">
                <i class="bi bi-calendar-week mr-2 text-gray-500"></i>
                {{-- Pastikan locale Indonesia sudah di-set di AppServiceProvider agar format hari dan bulan menjadi Bahasa Indonesia --}}
                <span>{{ \Carbon\Carbon::now()->translatedFormat('l, j F Y') }}</span>
            </div>

            {{-- Dropdown Profil --}}
            <div class="relative">
                <button id="profile-dropdown-btn" class="flex items-center gap-3 text-left p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    {{-- [DIPERBAIKI] Avatar dinamis berdasarkan nama user --}}
                    <img class="w-10 h-10 rounded-full object-cover border-2 border-indigo-100" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=eef2ff&color=4f46e5&font-size=0.5" alt="Avatar">
                    <div class="hidden md:block">
                        {{-- [DIPERBAIKI] Nama dan usertype dinamis dari user yang login --}}
                        <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ Str::ucfirst(Auth::user()->usertype) }}</p>
                    </div>
                    <i class="bi bi-chevron-down hidden md:block text-gray-500 transition-transform duration-200 chevron-icon"></i>
                </button>

                {{-- Menu Dropdown --}}
                <div id="profile-dropdown-menu" class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl z-50 py-2 border">
                    <div class="px-4 py-2 border-b mb-2">
                         {{-- [DIPERBAIKI] Nama dan email dinamis --}}
                        <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                    </div>
                    {{-- [DIPERBAIKI] Link profil menggunakan route yang benar --}}
                    <a href="{{ route('owner.profile') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                        <i class="bi bi-person-circle w-5 text-gray-500"></i>
                        <span>Profil Saya</span>
                    </a>
                    <div class="border-t my-2"></div>
                    {{-- [DIPERBAIKI] Form logout fungsional --}}
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
        const dropdownBtn = $('#profile-dropdown-btn');
        const dropdownMenu = $('#profile-dropdown-menu');

        dropdownBtn.on('click', function(e) {
            e.stopPropagation();
            dropdownMenu.toggleClass('active');
            dropdownBtn.toggleClass('active'); 
        });

        // Menutup dropdown jika klik di luar area
        $(window).on('click', function(e) {
            if (dropdownMenu.hasClass('active')) {
                if (!dropdownBtn.is(e.target) && dropdownBtn.has(e.target).length === 0 && !dropdownMenu.is(e.target) && dropdownMenu.has(e.target).length === 0) {
                    dropdownMenu.removeClass('active');
                    dropdownBtn.removeClass('active');
                }
            }
        });
    });
</script>


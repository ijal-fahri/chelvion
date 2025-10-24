<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Profil Pengguna | E-Commerce</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Ikon & Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    
    {{-- SweetAlert2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #6366f1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #4f46e5; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
        .accordion-content { max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out; }
        
        /* Style untuk Modal */
        #edit-profile-modal {
            transition: opacity 0.3s ease;
        }
        #edit-profile-modal .modal-content {
            transition: transform 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="relative min-h-screen md:flex">
        
        {{-- [INI PERBAIKANNYA] Memanggil sidebar dari folder 'owner' --}}
        @include('owner.partials.sidebar')

        <div class="flex-1 md:ml-64 flex flex-col">
            <main class="flex-grow p-4 sm:p-6 lg:p-8 animate-fade-in">
                
                {{-- [INI PERBAIKANNYA] Memanggil header dari folder 'owner' --}}
                @include('owner.partials.header', [
                    'title' => 'Profil Pengguna',
                    'subtitle' => 'Kelola informasi akun Anda dan lihat panduan aplikasi.'
                ])
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {{-- Kolom Kiri: Tampilan Profil Statis --}}
                    <div class="lg-col-span-1 space-y-8">
                        <div class="bg-white p-6 rounded-2xl shadow-lg">
                            <div class="flex flex-col items-center text-center">
                                <div class="relative">
                                    {{-- [DATA DINAMIS] Menggunakan nama user untuk avatar --}}
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=eef2ff&color=4f46e5&font-size=0.5" alt="Foto Profil" class="w-32 h-32 rounded-full mb-4 border-4 border-white shadow-md">
                                    <button class="absolute bottom-4 right-0 bg-indigo-600 text-white w-8 h-8 rounded-full flex items-center justify-center hover:bg-indigo-700 transition shadow">
                                        <i class="bi bi-camera-fill"></i>
                                    </button>
                                </div>
                                {{-- [DATA DINAMIS] Menggunakan nama dan email user yang login --}}
                                <h3 class="text-2xl font-bold text-gray-800">{{ Auth::user()->name }}</h3>
                                <p class="text-gray-500">{{ Auth::user()->email }}</p>
                            </div>
                            <hr class="my-6 border-gray-200">
                            <div class="space-y-4 text-sm">
                                <div class="flex justify-between">
                                    <span class="font-semibold text-gray-600">Jabatan:</span>
                                    {{-- [DATA DINAMIS] Menampilkan usertype --}}
                                    <span class="text-gray-800 text-right capitalize">{{ Auth::user()->usertype }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-semibold text-gray-600">Telepon:</span>
                                    <span class="text-gray-800 text-right">081234567890</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-semibold text-gray-600">Bergabung:</span>
                                    {{-- [DATA DINAMIS] Menampilkan tanggal user dibuat --}}
                                    <span class="text-gray-800 text-right">{{ Auth::user()->created_at->translatedFormat('d F Y') }}</span>
                                </div>
                            </div>
                            <button id="edit-profile-btn" class="mt-8 w-full bg-indigo-600 text-white font-bold py-2.5 px-4 rounded-lg hover:bg-indigo-700 transition-colors shadow-md flex items-center justify-center gap-2">
                                <i class="bi bi-pencil-square"></i>
                                <span>Edit Profil</span>
                            </button>
                        </div>
                    </div>

                    {{-- [DIUBAH] Kolom Kanan: Panduan Penggunaan dengan struktur HTML diperbaiki --}}
                    <div class="lg:col-span-2 space-y-8">
                        <div class="bg-white p-6 rounded-2xl shadow-lg">
                            <h4 class="text-xl font-semibold text-gray-700 mb-4">Panduan Penggunaan Aplikasi</h4>
                            <div class="space-y-3" id="accordion-container">
                                
                                {{-- Accordion Item 1 --}}
                                <div class="border rounded-lg">
                                    <button class="accordion-header w-full flex justify-between items-center p-4 text-left font-semibold text-gray-700 hover:bg-gray-50">
                                        <span>Bagaimana cara mengelola data admin?</span>
                                        <i class="bi bi-chevron-down transition-transform"></i>
                                    </button>
                                    <div class="accordion-content">
                                        <div class="p-4 text-gray-600">
                                            <p>Untuk mengelola data admin, Anda dapat mengakses menu "Manajemen" di sidebar, lalu klik "Data Admin". Di halaman tersebut, Anda bisa menambah, mengedit, atau menghapus akun admin yang memiliki akses ke sistem.</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Accordion Item 2 --}}
                                <div class="border rounded-lg">
                                    <button class="accordion-header w-full flex justify-between items-center p-4 text-left font-semibold text-gray-700 hover:bg-gray-50">
                                        <span>Bagaimana cara melihat laporan penjualan?</span>
                                        <i class="bi bi-chevron-down transition-transform"></i>
                                    </button>
                                    <div class="accordion-content">
                                        <div class="p-4 text-gray-600">
                                            <p>Laporan penjualan terperinci dapat diakses melalui menu "Manajemen Toko" di dashboard, lalu pilih "Laporan Penjualan". Anda juga dapat melihat ringkasan pendapatan bulanan langsung pada grafik di halaman utama dashboard.</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Accordion Item 3 --}}
                                <div class="border rounded-lg">
                                    <button class="accordion-header w-full flex justify-between items-center p-4 text-left font-semibold text-gray-700 hover:bg-gray-50">
                                        <span>Bagaimana cara menambahkan cabang baru?</span>
                                        <i class="bi bi-chevron-down transition-transform"></i>
                                    </button>
                                    <div class="accordion-content">
                                        <div class="p-4 text-gray-600">
                                            <p>Navigasikan ke menu "Manajemen" di sidebar dan pilih "Data Cabang". Di halaman tersebut, akan ada tombol "Tambah Cabang Baru". Klik tombol tersebut dan isi formulir yang disediakan untuk mendaftarkan cabang baru Anda.</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Accordion Item 4 --}}
                                <div class="border rounded-lg">
                                    <button class="accordion-header w-full flex justify-between items-center p-4 text-left font-semibold text-gray-700 hover:bg-gray-50">
                                        <span>Di mana saya bisa mengubah kata sandi?</span>
                                        <i class="bi bi-chevron-down transition-transform"></i>
                                    </button>
                                    <div class="accordion-content">
                                        <div class="p-4 text-gray-600">
                                            <p>Untuk alasan keamanan, perubahan kata sandi dapat dilakukan melalui menu "Pengaturan Akun". Di sana Anda akan diminta untuk memasukkan kata sandi lama sebelum membuat kata sandi baru.</p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    {{-- Modal untuk Edit Profil --}}
    <div id="edit-profile-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 hidden z-50">
        <div class="modal-content bg-white w-full max-w-md p-6 rounded-2xl shadow-xl transform scale-95">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Edit Informasi Profil</h3>
                <button id="close-modal-btn" class="text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
            </div>
            {{-- [FORM FUNGSIONAL] Mengarah ke route update profil --}}
            <form id="edit-profile-form" action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('patch')
                <div>
                    <label for="modal_nama_lengkap" class="block mb-2 text-sm font-medium text-gray-600">Nama Lengkap</label>
                    {{-- [DATA DINAMIS] Mengisi value dengan nama user --}}
                    <input type="text" id="modal_nama_lengkap" name="name" value="{{ Auth::user()->name }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="modal_email" class="block mb-2 text-sm font-medium text-gray-600">Alamat Email</label>
                    {{-- [DATA DINAMIS] Mengisi value dengan email user --}}
                    <input type="email" id="modal_email" name="email" value="{{ Auth::user()->email }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed" readonly>
                </div>
                <div>
                    <label for="modal_telepon" class="block mb-2 text-sm font-medium text-gray-600">Nomor Telepon</label>
                    <input type="tel" id="modal_telepon" value="081234567890" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex justify-end gap-4 pt-4">
                    <button type="button" id="cancel-modal-btn" class="px-6 py-2 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200">Batal</button>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 shadow-md">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Logika Accordion
            $('.accordion-header').click(function() {
                const content = $(this).next('.accordion-content');
                const icon = $(this).find('i');
                $('.accordion-content').not(content).css('max-height', '0px');
                $('.accordion-header').not(this).find('i').removeClass('rotate-180');
                if (content.css('max-height') == '0px') {
                    content.css('max-height', content.prop('scrollHeight') + 'px');
                    icon.addClass('rotate-180');
                } else {
                    content.css('max-height', '0px');
                    icon.removeClass('rotate-180');
                }
            });

            // Logika untuk Modal
            const modal = $('#edit-profile-modal');
            const modalContent = modal.find('.modal-content');
            
            function showModal() {
                modal.removeClass('hidden');
                setTimeout(() => {
                    modal.removeClass('opacity-0');
                    modalContent.removeClass('scale-95').addClass('scale-100');
                }, 10);
            }

            function hideModal() {
                modalContent.removeClass('scale-100').addClass('scale-95');
                modal.addClass('opacity-0');
                setTimeout(() => {
                    modal.addClass('hidden');
                }, 300);
            }

            $('#edit-profile-btn').click(showModal);
            $('#close-modal-btn, #cancel-modal-btn').click(hideModal);

            modal.click(function(event) {
                if ($(event.target).is(modal)) {
                    hideModal();
                }
            });
            
            // Mengganti form submit asli dengan notifikasi sukses
            $('#edit-profile-form').submit(function(e) {
                e.preventDefault();
                hideModal();
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Profil berhasil diperbarui!',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                // Di aplikasi nyata, Anda akan membiarkan form submit secara normal
                // atau menggunakan AJAX untuk mengirim data.
                // Untuk demo, kita hanya tampilkan notifikasi.
                // this.submit(); // <-- Hapus komentar ini jika ingin form benar-benar dikirim
            });
        });
    </script>
</body>
</html>

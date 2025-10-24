<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Profil Kasir | CELVION</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Ikon & Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

    {{-- SweetAlert2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
        }

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #6366f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #4f46e5;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }

        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        #edit-profile-modal {
            transition: opacity 0.3s ease;
        }

        #edit-profile-modal .modal-content {
            transition: transform 0.3s ease;
        }

        .validation-error {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        /* [BARU] Style untuk preview gambar */
        .img-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid #e0e7ff;
            object-fit: cover;
            display: block;
            margin: 0 auto 1rem;
        }
    </style>
</head>

<body>
    <div class="relative min-h-screen md:flex">

        @include('kasir.partials.sidebar')

        <div class="flex-1 md:ml-64 flex flex-col">
            <main class="flex-grow p-4 sm:p-6 lg:p-8 animate-fade-in">

                @include('kasir.partials.header', [
                    'title' => 'Profil Pengguna',
                    'subtitle' => 'Kelola informasi akun Anda dan lihat panduan aplikasi.',
                ])

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {{-- Kolom Kiri: Tampilan Profil Dinamis --}}
                    <div class="lg-col-span-1 space-y-8">
                        <div class="bg-white p-6 rounded-2xl shadow-lg">
                            <div class="flex flex-col items-center text-center">
                                <div class="relative">
                                    {{-- [DIUBAH] Gunakan photo_url dari Accessor. Tambah class 'profile-photo-img' --}}
                                    <img src="{{ $user->photo_url }}" alt="Foto Profil"
                                        class="profile-photo-img w-32 h-32 rounded-full mb-4 border-4 border-white shadow-md object-cover">
                                    {{-- Tombol ini sekarang bisa memicu file input di modal --}}
                                    <button onclick="$('#modal_photo').click()"
                                        class="absolute bottom-4 right-0 bg-indigo-600 text-white w-8 h-8 rounded-full flex items-center justify-center hover:bg-indigo-700 transition shadow">
                                        <i class="bi bi-camera-fill"></i>
                                    </button>
                                </div>
                                <h3 id="profile-name" class="text-2xl font-bold text-gray-800">{{ $user->name }}</h3>
                                {{-- [DIUBAH] Tambahkan id 'profile-email' --}}
                                <p id="profile-email" class="text-gray-500">{{ $user->email }}</p>
                            </div>
                            <hr class="my-6 border-gray-200">
                            <div class="space-y-4 text-sm">
                                <div class="flex justify-between">
                                    <span class="font-semibold text-gray-600">Jabatan:</span>
                                    <span class="text-gray-800 text-right capitalize">{{ $user->usertype }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-semibold text-gray-600">Telepon:</span>
                                    <span id="profile-phone"
                                        class="text-gray-800 text-right">{{ $user->phone ?? 'Belum diatur' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-semibold text-gray-600">Bergabung:</span>
                                    <span class="text-gray-800 text-right">
                                        {{-- Ini adalah logikanya --}}
                                        {{ $user->joining_date ? $user->joining_date->isoFormat('D MMMM YYYY') : 'N/A' }}
                                    </span>
                                </div>
                            </div>
                            <button id="edit-profile-btn"
                                class="mt-8 w-full bg-indigo-600 text-white font-bold py-2.5 px-4 rounded-lg hover:bg-indigo-700 transition-colors shadow-md flex items-center justify-center gap-2">
                                <i class="bi bi-pencil-square"></i>
                                <span>Edit Profil</span>
                            </button>
                        </div>
                    </div>

                    {{-- Kolom Kanan: Panduan Penggunaan --}}
                    <div class="lg:col-span-2 space-y-8">
                        <div class="bg-white p-6 rounded-2xl shadow-lg">
                            <h4 class="text-xl font-semibold text-gray-700 mb-4">Panduan Penggunaan Aplikasi Kasir</h4>
                            <div class="space-y-3" id="accordion-container">
                                {{-- ... Accordion items tidak berubah ... --}}
                                <div class="border rounded-lg">
                                    <button
                                        class="accordion-header w-full flex justify-between items-center p-4 text-left font-semibold text-gray-700 hover:bg-gray-50">
                                        <span>Bagaimana cara membuat transaksi baru?</span>
                                        <i class="bi bi-chevron-down transition-transform"></i>
                                    </button>
                                    <div class="accordion-content">
                                        <div class="p-4 text-gray-600">
                                            <p>...</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="border rounded-lg">
                                    <button
                                        class="accordion-header w-full flex justify-between items-center p-4 text-left font-semibold text-gray-700 hover:bg-gray-50">
                                        <span>Bagaimana cara melihat riwayat transaksi?</span>
                                        <i class="bi bi-chevron-down transition-transform"></i>
                                    </button>
                                    <div class="accordion-content">
                                        <div class="p-4 text-gray-600">
                                            <p>...</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="border rounded-lg">
                                    <button
                                        class="accordion-header w-full flex justify-between items-center p-4 text-left font-semibold text-gray-700 hover:bg-gray-50">
                                        <span>Bagaimana cara memproses tukar tambah?</span>
                                        <i class="bi bi-chevron-down transition-transform"></i>
                                    </button>
                                    <div class="accordion-content">
                                        <div class="p-4 text-gray-600">
                                            <p>...</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="border rounded-lg">
                                    <button
                                        class="accordion-header w-full flex justify-between items-center p-4 text-left font-semibold text-gray-700 hover:bg-gray-50">
                                        <span>Di mana saya bisa melihat laporan penjualan harian?</span>
                                        <i class="bi bi-chevron-down transition-transform"></i>
                                    </button>
                                    <div class="accordion-content">
                                        <div class="p-4 text-gray-600">
                                            <p>...</p>
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
    <div id="edit-profile-modal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 hidden z-50">
        <div class="modal-content bg-white w-full max-w-md p-6 rounded-2xl shadow-xl transform scale-95">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Edit Informasi Profil</h3>
                <button id="close-modal-btn" class="text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
            </div>

            {{-- [DIUBAH] Tambahkan enctype untuk file upload --}}
            <form id="edit-profile-form" action="{{ route('kasir.profile.update') }}" method="POST" class="space-y-4"
                enctype="multipart/form-data">
                @csrf

                {{-- [BARU] Input Foto Profil --}}
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-600">Foto Profil</label>
                    <div class="flex flex-col items-center">
                        <img id="modal-img-preview" src="{{ $user->photo_url }}" alt="Preview"
                            class="img-preview profile-photo-img">
                        <input type="file" id="modal_photo" name="photo"
                            class="w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-indigo-50 file:text-indigo-700
                            hover:file:bg-indigo-100
                        ">
                        <span id="error_photo" class="validation-error"></span>
                    </div>
                </div>

                <div>
                    <label for="modal_nama_lengkap" class="block mb-2 text-sm font-medium text-gray-600">Nama
                        Lengkap</label>
                    <input type="text" id="modal_nama_lengkap" name="name" value="{{ $user->name }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <span id="error_name" class="validation-error"></span>
                </div>
                <div>
                    <label for="modal_email" class="block mb-2 text-sm font-medium text-gray-600">Alamat Email</label>
                    {{-- [DIUBAH] Hapus readonly, tambahkan name="email" --}}
                    <input type="email" id="modal_email" name="email" value="{{ $user->email }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <span id="error_email" class="validation-error"></span>
                </div>
                <div>
                    <label for="modal_telepon" class="block mb-2 text-sm font-medium text-gray-600">Nomor
                        Telepon</label>
                    <input type="tel" id="modal_telepon" name="phone" value="{{ $user->phone }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <span id="error_phone" class="validation-error"></span>
                </div>
                <div class="flex justify-end gap-4 pt-4">
                    <button type="button" id="cancel-modal-btn"
                        class="px-6 py-2 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200">Batal</button>
                    <button type="submit" id="save-profile-btn"
                        class="px-6 py-2 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 shadow-md">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Logika Accordion (Tidak berubah)
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

            // Logika Modal
            const modal = $('#edit-profile-modal');
            const modalContent = modal.find('.modal-content');

            function showModal() {
                // Bersihkan error lama & reset preview
                $('.validation-error').text('');
                // Reset preview ke foto user saat ini
                $('#modal-img-preview').attr('src', '{{ $user->photo_url }}');
                $('#modal_photo').val(''); // Hapus file yang mungkin dipilih sebelumnya

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

            // [BARU] Fungsi untuk image preview
            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#modal-img-preview').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
            $("#modal_photo").change(function() {
                readURL(this);
            });

            // ==========================================================
            // [LOGIKA UTAMA DIPERBARUI] Penanganan Submit Form via AJAX
            // ==========================================================
            $('#edit-profile-form').submit(function(e) {
                e.preventDefault();

                const submitButton = $('#save-profile-btn');
                submitButton.prop('disabled', true).text('Menyimpan...');
                $('.validation-error').text('');

                // [DIUBAH] Gunakan FormData untuk mengirim file
                let formData = new FormData(this);

                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: formData, // [DIUBAH]
                    dataType: 'json',
                    processData: false, // [BARU] Wajib untuk FormData
                    contentType: false, // [BARU] Wajib untuk FormData
                    success: function(response) {
                        hideModal();

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: response.success,
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });

                        // [DIUBAH] Update semua data dinamis di halaman
                        $('#profile-name').text(response.user.name);
                        $('#profile-email').text(response.user.email); // [BARU]
                        $('#profile-phone').text(response.user.phone || 'Belum diatur');

                        // [BARU] Update semua gambar profil di halaman
                        $('.profile-photo-img').attr('src', response.user.photo_url);

                        // [BARU] Update foto di header (ganti '.header-profile-img' dengan class Anda)
                        // $('.header-profile-img').attr('src', response.user.photo_url);

                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;

                            if (errors.name) {
                                $('#error_name').text(errors.name[0]);
                            }
                            if (errors.email) { // [BARU]
                                $('#error_email').text(errors.email[0]);
                            }
                            if (errors.phone) {
                                $('#error_phone').text(errors.phone[0]);
                            }
                            if (errors.photo) { // [BARU]
                                $('#error_photo').text(errors.photo[0]);
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Terjadi kesalahan. Silakan coba lagi.',
                            });
                        }
                    },
                    complete: function() {
                        submitButton.prop('disabled', false).text('Simpan');
                    }
                });
            });
        });
    </script>
</body>

</html>

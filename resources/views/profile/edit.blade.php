<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Profil Pengguna | E-Commerce</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    {{-- Font Poppins --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    {{-- SweetAlert2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            padding-top: 0;
            margin: 0;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #6366f1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
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

        .profile-card,
        .guide-card,
        .form-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .btn-indigo {
            background-color: #6366f1;
            border-color: #6366f1;
            color: white;
        }

        .btn-indigo:hover {
            background-color: #4f46e5;
            border-color: #4f46e5;
            color: white;
        }

        .profile-img {
            width: 128px;
            height: 128px;
            border: 4px solid white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            object-fit: cover;
            /* [BARU] Memastikan foto tidak terdistorsi */
        }

        .camera-btn {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            /* [BARU] */
        }

        /* ... (sisa style navbar Anda tidak berubah) ... */
        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            padding: 0.8rem 0;
            position: relative;
            background-color: white;
        }

        /* ... (sisa style lainnya) ... */
        .main-content {
            margin-top: 0;
        }

        /* [BARU] Style untuk form toggle */
        .form-toggle .bi-chevron-down {
            transition: transform 0.3s ease;
        }

        .form-toggle[aria-expanded="true"] .bi-chevron-down {
            transform: rotate(180deg);
        }
    </style>
</head>

<body class="custom-scrollbar">
    @include('components.navbar-pelanggan', [
        'orderCount' => $orderCount ?? 0,
        'wishlistCount' => $wishlistCount ?? 0,
        'cartCount' => $cartCount ?? 0,
    ])

    <div class="container-fluid main-content">
        <div class="row">
            {{-- Main Content --}}
            <main class="col-12 px-md-4 py-4 animate-fade-in">
                {{-- Header --}}
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
                    <div>
                        <h1 class="h2 fw-bold text-gray-800 mb-1">Profil Pengguna</h1>
                        <p class="text-muted">Kelola informasi akun Anda dan lihat panduan aplikasi.</p>
                    </div>
                </div>

                <div class="row g-4">
                    {{-- Kolom Kiri: Profil Pengguna --}}
                    <div class="col-lg-4">
                        <div class="card profile-card">
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <div class="position-relative d-inline-block">
                                        {{-- [DIUBAH] Foto profil dinamis --}}
                                        <img id="profile-img-preview" src="{{ $user->photo_url }}" alt="Foto Profil"
                                            class="profile-img rounded-circle">
                                        {{-- [DIUBAH] Tombol ini sekarang memicu input file --}}
                                        <button type="button" onclick="document.getElementById('photo').click()"
                                            class="btn btn-indigo camera-btn rounded-circle position-absolute bottom-0 end-0"
                                            data-bs-toggle="tooltip" title="Ubah foto profil">
                                            <i class="bi bi-camera-fill"></i>
                                        </button>
                                    </div>
                                    <h3 class="h4 fw-bold text-gray-800 mt-3 mb-1">{{ $user->name }}</h3>
                                    <p class="text-muted mb-0">{{ $user->email }}</p>
                                </div>

                                <hr class="my-4">
                            </div>
                        </div>
                    </div>

                    {{-- Kolom Kanan: Formulir Profil --}}
                    <div class="col-lg-8">
                        {{-- Update Profile Information Form --}}
                        <div class="card form-card mb-4">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center form-toggle"
                                    data-bs-toggle="collapse" data-bs-target="#profileForm" aria-expanded="true">
                                    <h4 class="h5 fw-semibold text-gray-700 mb-0">Informasi Profil</h4>
                                    <i class="bi bi-chevron-down text-gray-500 fs-5"></i>
                                </div>
                                <div class="form-content collapse show mt-4" id="profileForm">
                                    {{-- [DIUBAH] Tambahkan enctype untuk upload file --}}
                                    <form method="post" action="{{ route('profile.update') }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        @method('patch')

                                        {{-- [BARU] Input File Foto --}}
                                        <div class="mb-3">
                                            <label for="photo" class="form-label fw-medium text-gray-600">Ubah Foto
                                                Profil</label>
                                            <input type="file" id="photo" name="photo" class="form-control"
                                                onchange="previewImage()">
                                            @error('photo')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6">
                                                <label for="name" class="form-label fw-medium text-gray-600">Nama
                                                    Lengkap</label>
                                                <input type="text" id="name" name="name"
                                                    value="{{ old('name', $user->name) }}" required autofocus
                                                    autocomplete="name" class="form-control">
                                                @error('name')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label for="email" class="form-label fw-medium text-gray-600">Alamat
                                                    Email</label>
                                                <input type="email" id="email" name="email"
                                                    value="{{ old('email', $user->email) }}" required
                                                    autocomplete="username" class="form-control">
                                                @error('email')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                                            {{-- ... (blok verifikasi email tidak berubah) ... --}}
                                        @endif

                                        <div class="d-flex align-items-center gap-3">
                                            <button type="submit"
                                                class="btn btn-indigo d-flex align-items-center gap-2">
                                                <i class="bi bi-check-lg"></i>
                                                <span>Simpan Perubahan</span>
                                            </button>

                                            {{-- [DIUBAH] Pesan 'Saved.' akan muncul di sini --}}
                                            @if (session('status') === 'profile-updated')
                                                <div id="profile-saved-alert" class="text-success small fw-medium">
                                                    {{ __('Berhasil disimpan.') }}
                                                </div>
                                            @endif
                                        </div>
                                    </form>

                                    {{-- Form tersembunyi untuk kirim verifikasi --}}
                                    <form id="send-verification" method="post"
                                        action="{{ route('verification.send') }}" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Update Password Form --}}
                        <div class="card form-card mb-4">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center form-toggle"
                                    data-bs-toggle="collapse" data-bs-target="#passwordForm" aria-expanded="false">
                                    <h4 class="h5 fw-semibold text-gray-700 mb-0">Ubah Kata Sandi</h4>
                                    <i class="bi bi-chevron-down text-gray-500 fs-5"></i>
                                </div>
                                <div class="form-content collapse mt-4" id="passwordForm">
                                    {{-- Form Ubah Password (Kode dari file Anda, sudah benar) --}}
                                    <form method="post" action="{{ route('password.update') }}">
                                        @csrf
                                        @method('put')
                                        <div class="row g-3 mb-4">
                                            <div class="col-12">
                                                <label for="current_password"
                                                    class="form-label fw-medium text-gray-600">Kata Sandi Saat
                                                    Ini</label>
                                                <input type="password" id="current_password" name="current_password"
                                                    autocomplete="current-password" class="form-control">
                                                @error('current_password', 'updatePassword')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="password" class="form-label fw-medium text-gray-600">Kata
                                                    Sandi Baru</label>
                                                <input type="password" id="password" name="password"
                                                    autocomplete="new-password" class="form-control">
                                                @error('password', 'updatePassword')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="password_confirmation"
                                                    class="form-label fw-medium text-gray-600">Konfirmasi Kata Sandi
                                                    Baru</label>
                                                <input type="password" id="password_confirmation"
                                                    name="password_confirmation" autocomplete="new-password"
                                                    class="form-control">
                                                @error('password_confirmation', 'updatePassword')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-3">
                                            <button type="submit"
                                                class="btn btn-indigo d-flex align-items-center gap-2">
                                                <i class="bi bi-key"></i>
                                                <span>Perbarui Kata Sandi</span>
                                            </button>
                                            @if (session('status') === 'password-updated')
                                                <div id="password-saved-alert" class="text-success small fw-medium">
                                                    {{ __('Berhasil disimpan.') }}
                                                </div>
                                            @endif
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Delete User Form --}}
                        <div class="card form-card">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center form-toggle"
                                    data-bs-toggle="collapse" data-bs-target="#deleteForm" aria-expanded="false">
                                    <h4 class="h5 fw-semibold text-gray-700 mb-0">Hapus Akun</h4>
                                    <i class="bi bi-chevron-down text-gray-500 fs-5"></i>
                                </div>
                                <div class="form-content collapse mt-4" id="deleteForm">
                                    {{-- Form Hapus Akun (Kode dari file Anda, sudah benar) --}}
                                    <div class="alert alert-danger border-start-4 border-danger mb-4">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <i class="bi bi-exclamation-triangle text-danger fs-5"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <p class="mb-0 small">
                                                    {{ __('Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen.') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-danger d-flex align-items-center gap-2"
                                        data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                                        <i class="bi bi-trash"></i>
                                        <span>Hapus Akun</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    {{-- Modal Konfirmasi Penghapusan Akun (Kode dari file Anda, sudah benar) --}}
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteAccountModalLabel">Konfirmasi Penghapusan Akun</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small mb-3">
                            {{ __('Masukkan kata sandi Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun Anda secara permanen.') }}
                        </p>

                        <div class="mb-3">
                            <label for="modal_password" class="form-label">Password</label>
                            <input type="password" id="modal_password" name="password" class="form-control"
                                placeholder="Masukkan password Anda" required>
                            <x-input-error :messages="$errors->userDeletion->get('password')" class="text-danger small mt-1" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus Akun</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- [BARU] Skrip untuk pratinjau gambar dan auto-hide alert --}}
    <script>
        // Fungsi untuk pratinjau gambar
        function previewImage() {
            const fileInput = document.getElementById('photo');
            const preview = document.getElementById('profile-img-preview');

            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(fileInput.files[0]);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi tooltip untuk tombol kamera
            const cameraBtn = document.querySelector('.camera-btn');
            if (cameraBtn) {
                new bootstrap.Tooltip(cameraBtn);
            }

            // Sembunyikan pesan 'Saved.' setelah 3 detik
            const profileAlert = document.getElementById('profile-saved-alert');
            if (profileAlert) {
                setTimeout(() => {
                    profileAlert.style.transition = 'opacity 0.5s ease';
                    profileAlert.style.opacity = '0';
                }, 3000);
            }

            const passwordAlert = document.getElementById('password-saved-alert');
            if (passwordAlert) {
                setTimeout(() => {
                    passwordAlert.style.transition = 'opacity 0.5s ease';
                    passwordAlert.style.opacity = '0';
                }, 3000);
            }

            // Tampilkan modal jika ada error password saat hapus akun
            @if ($errors->userDeletion->isNotEmpty())
                var deleteModal = new bootstrap.Modal(document.getElementById('deleteAccountModal'));
                deleteModal.show();
            @endif
        });
    </script>
</body>

</html>

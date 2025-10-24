<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF--8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Notifikasi | Staf Gudang</title>

    {{-- Tailwind CSS & jQuery --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- Ikon & Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; }
        .notification-item:not(:last-child) {
            border-bottom: 1px solid #e5e7eb;
        }
        .notification-item.unread {
            background-color: #f0f5ff;
        }
    </style>
</head>
<body>
    <div class="relative min-h-screen md:flex">
        
        {{-- Menggunakan sidebar yang sama dengan halaman gudang lainnya --}}
        @include('staff.partials.sidebar')

        <div class="flex-1 md:ml-64 flex flex-col min-w-0">
            <main class="flex-grow p-4 sm:p-6 lg:p-8">
                
                {{-- Menggunakan header yang sudah ada --}}
                @include('staff.partials.header', [
                    'title' => 'Pusat Notifikasi',
                    'subtitle' => 'Lihat semua pemberitahuan terkait aktivitas gudang.'
                ])

                <div class="bg-white p-4 md:p-6 rounded-2xl shadow-lg">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6 border-b pb-4">
                        <h4 class="text-xl font-semibold text-gray-700">Daftar Notifikasi</h4>
                        <div class="flex items-center gap-2">
                            <button class="px-4 py-2 text-sm font-semibold rounded-lg bg-indigo-600 text-white transition">Semua</button>
                            <button class="px-4 py-2 text-sm font-semibold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">Belum Dibaca</button>
                        </div>
                    </div>

                    <div class="space-y-2">
                        {{-- Contoh Item Notifikasi - Belum Dibaca --}}
                        <a href="#" class="block p-4 rounded-lg notification-item unread hover:bg-indigo-50 transition-colors duration-200">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 flex-shrink-0 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-xl"><i class="bi bi-exclamation-triangle-fill"></i></div>
                                <div class="flex-grow">
                                    <p class="font-semibold text-gray-800">Peringatan: Stok Kritis</p>
                                    <p class="text-sm text-gray-600">Produk "Adapter Charger 25W" telah mencapai batas stok minimum.</p>
                                    <p class="text-xs text-gray-400 mt-1">2 menit yang lalu</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="block h-3 w-3 rounded-full bg-blue-500" title="Belum dibaca"></span>
                                </div>
                            </div>
                        </a>

                        {{-- Contoh Item Notifikasi - Sudah Dibaca --}}
                        <a href="#" class="block p-4 rounded-lg notification-item hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 flex-shrink-0 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-xl"><i class="bi bi-check-circle-fill"></i></div>
                                <div class="flex-grow">
                                    <p class="font-semibold text-gray-800">Permintaan Stok Disetujui</p>
                                    <p class="text-sm text-gray-600">Permintaan Anda untuk 10 unit "Samsung S24 Ultra" telah disetujui oleh Owner.</p>
                                    <p class="text-xs text-gray-400 mt-1">1 jam yang lalu</p>
                                </div>
                            </div>
                        </a>
                        
                        <a href="#" class="block p-4 rounded-lg notification-item hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 flex-shrink-0 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xl"><i class="bi bi-box-seam-fill"></i></div>
                                <div class="flex-grow">
                                    <p class="font-semibold text-gray-800">Stok menipis</p>
                                    <p class="text-sm text-gray-600">Produk "iPhone 15 Pro" hanya tersisa 3 unit di gudang.</p>
                                    <p class="text-xs text-gray-400 mt-1">5 jam yang lalu</p>
                                </div>
                            </div>
                        </a>
                        
                        <a href="#" class="block p-4 rounded-lg notification-item hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 flex-shrink-0 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center text-xl"><i class="bi bi-info-circle-fill"></i></div>
                                <div class="flex-grow">
                                    <p class="font-semibold text-gray-800">Update Sistem</p>
                                    <p class="text-sm text-gray-600">Sistem akan melakukan pemeliharaan pada pukul 23:00 WIB.</p>
                                    <p class="text-xs text-gray-400 mt-1">1 hari yang lalu</p>
                                </div>
                            </div>
                        </a>

                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>

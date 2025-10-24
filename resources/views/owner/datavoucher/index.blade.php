    <!DOCTYPE html>
    <html lang="id" class="scroll-smooth">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Manajemen Data Voucher | CELVION</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
            rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <style>
            body {
                font-family: 'Poppins', sans-serif;
                background-color: #f0f2f5;
            }

            .badge {
                padding: 4px 12px;
                border-radius: 9999px;
                font-size: 0.75rem;
                font-weight: 600;
            }

            .status-aktif {
                background-color: #dcfce7;
                color: #166534;
            }

            .status-habis {
                background-color: #fef9c3;
                color: #854d0e;
            }

            .status-kedaluwarsa {
                background-color: #fee2e2;
                color: #991b1b;
            }

            .card-hover {
                transition: all 0.3s ease-in-out;
            }

            .card-hover:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            }

            .modal-overlay {
                transition: opacity 0.3s ease;
            }

            .modal-content {
                transition: transform 0.3s ease, opacity 0.3s ease;
            }

            .toggle-checkbox:checked {
                right: 0;
                border-color: #4f46e5;
            }

            .toggle-checkbox:checked+.toggle-label {
                background-color: #4f46e5;
            }

            .hero-card-bg {
                background-color: #4338ca;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cg fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.08'%3E%3Cpath opacity='.5' d='M96 95h4v1h-4v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9zm-1 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm9-10v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm9-10v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm9-10v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9z'/%3E%3Cpath d='M6 5V0h1v5h9V0h1v5h9V0h1v5h9V0h1v5h9V0h1v5h9V0h1v5h9V0h1v5h9V0h1v5h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9zm-1 0V0h-1v5H0v1h5v9H0v1h5v9H0v1h5v9H0v1h5v9H0v1h5v9H0v1h5v9H0v1h5v9H0v1h5v9h1v-9h9v9h1v-9h9v9h1v-9h9v9h1v-9h9v9h1v-9h9v9h1v-9h9v9h1v-9h9v9h1v-9h9v9h1v-9h4v-1h-4v-9h4v-1h-4v-9h4v-1h-4v-9h4v-1h-4v-9h4v-1h-4v-9h4v-1h-4v-9h4v-1h-4V5H6zm10 0V0h-1v5h-9V0h-1v5H6v1h9v9H6v1h9v9H6v1h9v9H6v1h9v9H6v1h9v9H6v1h9v9H6v1h9v9H6v1h9v9h1v-9h9v9h1v-9h9v9h1v-9h9v9h1v-9h9v9h1v-9h9v9h1v-9h9v9h1v-9h9v9h1v-9h4v-1h-4v-9h4v-1h-4v-9h4v-1h-4v-9h4v-1h-4v-9h4v-1h-4v-9h4v-1h-4v-9h4v-1h-4V5h-9zm10 0V0h-1v5h-9V0h-1v5h-9V0h-1v5h-9V0H6v1h9v9H6v1h9v9H6v1h9v9H6v1h9v9H6v1h9v9H6v1h9v9H6v1h9v9H6v1h9v9h1v-9h9v9h1v-9h9v9h1v-9h9v9h1v-9h9v9h1v-9h9v9h1v-9h9v9h1v-9h4v-1h-4v-9h4v-1h-4v-9h4v-1h-4v-9h4v-1h-4v-9h4v-1h-4v-9h4v-1h-4v-9h4v-1h-4V5h-9zm10 0V0h-1v5h-9V0h-1v5h-9V0h-1v5h-9V0h-1v5h-9V0H6v1h9v9H6v1h9v9H6v1h9v9H6v1h9v9H6v1h9v9H6v1h9v9H6v1h9v9H6v1h9v9h1v-9h9v9h1v-9h9v9h1v-9h9v9h1v-9h9v9h1v-9h9v9h1v-9h9v9h1v-9h4v-1h-4v-9h4v-1h-4v-9h4v-1h-4v-9h4v-1h-4v-9h4v-1h-4v-9h4v-1h-4v-9h4v-1h-4V5h-9zm10 0V0h-1v5h-9V0h-1v5h-9V0h-1v5h-9V0h-1v5h-9V0h-1v5h-9V0H6v1h9v9H6v1h9v9H6v1h9v9H6v1h9v9H6v1h9v9H6v1h9v9H6v1h9v9H6v1h9v9h1v-9h9v9h1v-9h9v9h1v-9h9v9h1v-9h9v9h1v-9h9v9h1v-9h9v9h1v-9h9v9h1v-9h4v-1h-4v-9h4v-1h-4v-9h4v-1h-4v-9h4v-1h-4v-9h4v-1h-4v-9h4v-1h-4v-9h4v-1h-4V5h-9zm10 0V0h-1v5h-9V0h-1v5h-9V0h-1v5h-9V0h-1v5h-9V0h-1v5h-9V0h-1v5H6v1h9v9H6v1h9v9H6v1h9v9H6v1h9v9H6v1h9v9H6v1h9v9H6v1h9v9H6v1h9v9h1v-9h9v9h1v-9h9v9h1v-9h9v9h1v-9h9v9h1v-9h9v9h1v-9h9v9h1v-9h9v9h1v-9h4v-1h-4v-9h4v-1h-4v-9h4v-1h-4v-9h4v-1h-4v-9h4v-1h-4v-9h4v-1h-4v-9h4v-1h-4V5h-9z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            }

            @media (max-width: 767px) {
                .responsive-table thead {
                    display: none;
                }

                .responsive-table tr {
                    display: block;
                    margin-bottom: 1rem;
                    border: 1px solid #e5e7eb;
                    border-radius: 0.75rem;
                    overflow: hidden;
                    background-color: white;
                }

                .responsive-table td {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 0.75rem 1rem;
                    border-bottom: 1px solid #f3f4f6;
                }

                .responsive-table td:last-child {
                    border-bottom: none;
                }

                .responsive-table td::before {
                    content: attr(data-label);
                    font-weight: 600;
                    margin-right: 1rem;
                    color: #4b5563;
                }
            }
        </style>
    </head>

    <body>
        <div class="relative min-h-screen md:flex">
            @include('owner.partials.sidebar')

            <div class="flex-1 md:ml-64 flex flex-col">
                <main class="flex-grow p-4 sm:p-6 lg:p-8">
                    @include('owner.partials.header', [
                        'title' => 'Manajemen Data Voucher',
                        'subtitle' => 'Buat dan kelola voucher diskon untuk semua cabang.',
                    ])

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

                        <div
                            class="lg:col-span-2 lg:row-span-2 bg-indigo-700 p-6 rounded-2xl shadow-lg flex flex-col hero-card-bg text-white card-hover">
                            <div class="flex-grow">
                                <h3 class="text-xl font-bold text-indigo-200">Ringkasan Voucher Aktif</h3>
                                <p id="active-voucher-hero-count" class="text-6xl font-bold mt-4">0</p>
                                <p class="text-indigo-300">Total voucher yang dapat digunakan pelanggan.</p>

                                <div class="mt-8">
                                    <h4 class="font-semibold mb-3 text-indigo-200">Distribusi Diskon</h4>
                                    <div id="discount-distribution-bars" class="space-y-3">
                                        <p class="text-indigo-300 text-sm">Belum ada data</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-6 pt-4 border-t border-white/10">
                                <button id="add-voucher-btn-hero"
                                    class="w-full bg-white text-indigo-700 font-bold px-4 py-3 rounded-lg hover:bg-indigo-100 flex items-center justify-center gap-2 transition-colors">
                                    <i class="bi bi-plus-circle-fill"></i>
                                    <span>Buat Voucher Baru</span>
                                </button>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-2xl shadow-lg card-hover">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="font-bold text-gray-700">Segera Berakhir</h4>
                                    <p class="text-sm text-gray-500">Dalam 7 hari ke depan</p>
                                </div>
                                <div class="bg-amber-100 text-amber-600 p-2 rounded-full text-lg"><i
                                        class="bi bi-hourglass-split"></i></div>
                            </div>
                            <ul id="expiring-soon-list" class="mt-4 space-y-2 text-sm">
                                <li class="text-gray-400">Tidak ada voucher.</li>
                            </ul>
                        </div>

                        <div class="bg-white p-6 rounded-2xl shadow-lg card-hover">
                            <h4 class="font-bold text-gray-700 mb-4">Kinerja Cabang</h4>
                            <ul id="branch-performance-list" class="space-y-3">
                                <li class="text-gray-400 text-sm">Belum ada data cabang.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="bg-white p-4 md:p-6 rounded-2xl shadow-lg">
                        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
                            <h3 class="text-xl font-bold text-gray-800">Semua Voucher</h3>
                            <div class="relative w-full sm:max-w-xs">
                                <input type="text" id="searchInput" placeholder="Cari nama voucher..."
                                    class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm responsive-table">
                                <thead class="bg-gray-50 text-gray-600 uppercase">
                                    <tr>
                                        <th class="p-4 text-left">Info Voucher</th>
                                        <th class="p-4 text-left">Diskon</th>
                                        <th class="p-4 text-left">Cabang</th>
                                        <th class="p-4 text-left">Status / Masa Berlaku</th>
                                        <th class="p-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="voucher-table-body"></tbody>
                            </table>
                            <p id="noResults" class="text-center text-gray-500 py-8 hidden">Voucher tidak ditemukan.</p>
                        </div>
                    </div>
                </main>
            </div>
        </div>

        {{-- Modal Form --}}
        <div id="voucher-modal"
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 hidden modal-overlay">
            <div
                class="bg-white rounded-2xl shadow-xl w-full max-w-lg modal-content opacity-0 transform -translate-y-10 max-h-[90vh] overflow-y-auto">
                <form id="voucher-form">
                    <div class="flex justify-between items-center p-5 border-b sticky top-0 bg-white z-10">
                        <h3 id="modal-title" class="text-xl font-bold"></h3><button type="button"
                            class="close-modal p-2"><i class="bi bi-x-lg"></i></button>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                            <input type="hidden" id="voucherId">
                            <div class="md:col-span-2">
                                <label for="name" class="block mb-1 font-semibold text-sm">Nama Voucher</label>
                                <input type="text" id="name" name="name"
                                    class="w-full px-3 py-2 border rounded-lg" placeholder="Contoh: Diskon Kemerdekaan"
                                    required>
                            </div>

                            <div class="md:col-span-2">
                                <label for="description" class="block mb-1 font-semibold text-sm">Deskripsi</label>
                                <textarea id="description" name="description" rows="2" class="w-full px-3 py-2 border rounded-lg"
                                    placeholder="Contoh: Berlaku untuk semua produk"></textarea>
                            </div>

                            <div>
                                <label for="type" class="block mb-1 font-semibold text-sm">Tipe Voucher</label>
                                <select id="type" name="type"
                                    class="w-full px-3 py-2 border rounded-lg bg-white" required>
                                    <option value="percentage">Persentase (%)</option>
                                    <option value="fixed">Potongan Tetap (Rp)</option>
                                </select>
                            </div>

                            <div>
                                <label for="cabang" class="block mb-1 font-semibold text-sm">Berlaku di
                                    Cabang</label>
                                <select id="cabang" name="cabang_id"
                                    class="w-full px-3 py-2 border rounded-lg bg-white" required>
                                    <option value="all">Semua Cabang</option>
                                    @foreach ($cabangs as $cabang)
                                        <option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="group-percentage" class="md:col-span-2 grid grid-cols-2 gap-x-6 gap-y-5">
                                <div>
                                    <label for="discount_percentage" class="block mb-1 font-semibold text-sm">Besar
                                        Diskon</label>
                                    <div class="relative"><input type="number" step="0.01"
                                            id="discount_percentage" name="discount_percentage"
                                            class="w-full pl-3 pr-8 py-2 border rounded-lg" min="0.01"
                                            max="100" placeholder="15"><span
                                            class="absolute right-3 top-1/2 -translate-y-1/2 font-bold text-gray-500">%</span>
                                    </div>
                                </div>
                                <div>
                                    <label for="max_discount" class="block mb-1 font-semibold text-sm">Maks. Diskon
                                        (Rp)</label>
                                    <div class="relative"><span
                                            class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-gray-500">Rp</span><input
                                            type="number" id="max_discount" name="max_discount"
                                            class="w-full pl-8 pr-3 py-2 border rounded-lg" min="0"
                                            placeholder="50000"></div>
                                    <label for="max_discount" class="block mt-1 text-xs text-gray-500">Kosongkan jika
                                        tanpa batas.</label>
                                </div>
                            </div>

                            <div id="group-fixed" class="md:col-span-2 hidden">
                                <label for="discount_amount" class="block mb-1 font-semibold text-sm">Jumlah Potongan
                                    (Rp)</label>
                                <div class="relative"><span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-gray-500">Rp</span><input
                                        type="number" id="discount_amount" name="discount_amount"
                                        class="w-full pl-8 pr-3 py-2 border rounded-lg" min="1"
                                        placeholder="100000"></div>
                            </div>

                            <div class="md:col-span-2 pt-3 border-t">
                                <label for="min_purchase" class="block mb-1 font-semibold text-sm">Minimal Pembelian
                                    (Rp)</label>
                                <div class="relative"><span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-gray-500">Rp</span><input
                                        type="number" id="min_purchase" name="min_purchase"
                                        class="w-full pl-8 pr-3 py-2 border rounded-lg" min="0"
                                        placeholder="0">
                                </div>
                                <label for="min_purchase" class="block mt-1 text-xs text-gray-500">Isi 0 jika tanpa
                                    minimal pembelian.</label>
                            </div>

                            <div class="md:col-span-2 pt-3 border-t">
                                <label class="block mb-1 font-semibold text-sm">Batas Stok</label>
                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-gray-600">Aktifkan batas jumlah voucher?</p>
                                    <div
                                        class="relative inline-block w-12 mr-2 align-middle select-none transition duration-200 ease-in">
                                        <input type="checkbox" name="has_stock" id="has_stock"
                                            class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer" />
                                        <label for="has_stock"
                                            class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                                    </div>
                                </div>
                            </div>
                            <div id="stock-group" class="md:col-span-2 hidden">
                                <div class="relative">
                                    <i
                                        class="bi bi-box-seam absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <input type="number" id="stock" name="stock" min="0"
                                        class="w-full px-3 pl-10 py-2 border rounded-lg"
                                        placeholder="Masukkan jumlah stok">
                                </div>
                            </div>

                            <div class="md:col-span-2 pt-3 border-t">
                                <label class="block mb-1 font-semibold text-sm">Masa Berlaku</label>
                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-gray-600">Aktifkan tanggal kedaluwarsa?</p>
                                    <div
                                        class="relative inline-block w-12 mr-2 align-middle select-none transition duration-200 ease-in">
                                        <input type="checkbox" name="has_expiry" id="has_expiry"
                                            class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer" />
                                        <label for="has_expiry"
                                            class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                                    </div>
                                </div>
                            </div>
                            <div id="expiry-date-group" class="md:col-span-2 hidden">
                                <div class="relative">
                                    <i
                                        class="bi bi-calendar-event absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <input type="date" id="expiry_date" name="expiry_date"
                                        class="w-full px-3 pl-10 py-2 border rounded-lg">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-5 border-t bg-gray-50 flex justify-end gap-3 sticky bottom-0 z-10"><button
                            type="button" class="close-modal px-4 py-2 bg-gray-200 rounded-lg">Batal</button><button
                            type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Simpan</button></div>
                </form>
            </div>
        </div>

        <!-- [BARU] Modal Detail Voucher -->
        <div id="detail-modal"
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 hidden modal-overlay">
            <div
                class="bg-white rounded-2xl shadow-xl w-full max-w-lg modal-content opacity-0 transform -translate-y-10">
                <div class="flex justify-between items-center p-5 border-b">
                    <h3 class="text-xl font-bold text-gray-800">Detail Voucher</h3>
                    <button type="button" class="close-modal p-2"><i class="bi bi-x-lg"></i></button>
                </div>
                <div id="detail-modal-content" class="p-6 space-y-4">
                    {{-- Konten detail akan diisi oleh JavaScript --}}
                </div>
                <div class="p-5 border-t bg-gray-50 flex justify-end">
                    <button type="button" class="close-modal px-4 py-2 bg-gray-200 rounded-lg">Tutup</button>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                let allVoucherData = {};

                function formatDate(dateString) {
                    if (!dateString) return 'Selamanya';
                    const options = {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    };
                    return new Date(dateString).toLocaleDateString('id-ID', options);
                }

                function formatCurrency(number) {
                    if (number === null || number === undefined) return '0';
                    return new Intl.NumberFormat('id-ID', {
                        maximumFractionDigits: 0
                    }).format(number);
                }

                function createVoucherSVG(data) {
                    const isPercentage = data.type === 'percentage';
                    const colors = ['#4f46e5', '#db2777', '#059669', '#d97706', '#2563eb'];
                    const color = colors[data.name.length % colors.length];
                    const text = isPercentage ? `${parseFloat(data.discount_percentage)}%` :
                        `Rp${formatCurrency(data.discount_amount).slice(0, -4)}K`; // 50K
                    const subtext = isPercentage ? 'OFF' : 'POTONGAN';

                    return `<div class="w-24 h-16 rounded-lg flex-shrink-0" style="background-color: ${color}; color: white; padding: 8px; position: relative; overflow: hidden;"><svg width="100%" height="100%" viewBox="0 0 96 64"><path d="M10 0 H86 A10 10 0 0 1 96 10 V22 A6 6 0 0 0 96 34 V54 A10 10 0 0 1 86 64 H10 A10 10 0 0 1 0 54 V34 A6 6 0 0 0 0 22 V10 A10 10 0 0 1 10 0 Z" fill="${color}"/><circle cx="0" cy="28" r="6" fill="#f0f2f5"/><circle cx="96" cy="28" r="6" fill="#f0f2f5"/><text x="50%" y="30" dominant-baseline="middle" text-anchor="middle" font-size="${text.length > 4 ? 16 : 20}" font-weight="bold" fill="white">${text}</text><text x="50%" y="52" dominant-baseline="middle" text-anchor="middle" font-size="8" fill="white" style="opacity:0.8">${subtext}</text></svg></div>`;
                }

                function renderTable(dataToRender) {
                    const tableBody = $('#voucher-table-body').empty();
                    $('#noResults').toggleClass('hidden', Object.keys(dataToRender).length > 0);
                    const sortedData = Object.entries(dataToRender).sort((a, b) => b[0] - a[0]); // Urutkan terbaru

                    for (const [id, data] of sortedData) {
                        const statusClass = `status-${data.status_label.toLowerCase().replace(' ', '-')}`;
                        const expiryText = `s/d ${formatDate(data.expiry_date)}`;
                        const stockText = data.stock === null ? `Stok: Tak Terbatas` :
                            `Sisa Stok: <strong>${data.stock - data.times_used}</strong>`;

                        // Tampilkan diskon berdasarkan tipe
                        const discountText = data.type === 'percentage' ?
                            `<p class="font-bold text-lg text-indigo-600">${parseFloat(data.discount_percentage)}%</p><p class="text-xs text-gray-500">Maks. Rp${formatCurrency(data.max_discount)}</p>` :
                            `<p class="font-bold text-lg text-indigo-600">Rp${formatCurrency(data.discount_amount)}</p>`;

                        const row = `
                        <tr class="border-b hover:bg-gray-50">
                            <td data-label="Info Voucher" class="p-4">
                                <div class="flex items-center gap-4">
                                    ${createVoucherSVG(data)}
                                    <div>
                                        <p class="font-semibold text-gray-800">${data.name}</p>
                                        <p class="text-xs text-gray-500 mt-1 italic">"${data.description || 'Tidak ada deskripsi'}"</p>
                                        <p class="text-xs text-gray-600 mt-1">${stockText}</p>
                                        <p class="text-xs text-gray-600 mt-1">Min. Belanja: <strong>Rp${formatCurrency(data.min_purchase)}</strong></p>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Diskon" class="p-4">${discountText}</td>
                            <td data-label="Cabang" class="p-4 text-gray-500">${data.applicable_branch}</td>
                            <td data-label="Status / Masa Berlaku" class="p-4"><span class="badge ${statusClass}">${data.status_label}</span><p class="text-xs text-gray-500 mt-1">${expiryText}</p></td>
                            <td data-label="Aksi" class="p-4">
                                <div class="flex justify-center gap-2">
                                    <button class="detail-voucher w-9 h-9 flex items-center justify-center rounded-md bg-sky-500 text-white hover:bg-sky-600" data-id="${id}" title="Lihat Detail"><i class="bi bi-eye-fill"></i></button>
                                    <button class="edit-voucher w-9 h-9 flex items-center justify-center rounded-md bg-amber-500 text-white hover:bg-amber-600" data-id="${id}" title="Edit"><i class="bi bi-pencil-fill"></i></button>
                                    <button class="delete-voucher w-9 h-9 flex items-center justify-center rounded-md bg-red-500 text-white hover:bg-red-600" data-id="${id}" title="Hapus"><i class="bi bi-trash-fill"></i></button>
                                </div>
                            </td>
                        </tr>`;
                        tableBody.append(row);
                    }
                }

                function updateDashboard(data) {
                    $('#active-voucher-hero-count').text(data.active_voucher_count);

                    const barsContainer = $('#discount-distribution-bars').empty();
                    if (data.active_voucher_count > 0) {
                        for (const [label, count] of Object.entries(data.discount_distribution)) {
                            if (count === 0) continue; // Jangan tampilkan jika 0
                            const percentage = (count / data.active_voucher_count) * 100;
                            const bar =
                                `<div class="flex items-center gap-3"><span class="text-sm text-indigo-300 w-24 text-right">${label}</span><div class="w-full bg-black/20 rounded-full h-2.5"><div class="bg-white h-2.5 rounded-full" style="width: ${percentage}%"></div></div><span class="text-sm font-semibold w-8">${count}</span></div>`;
                            barsContainer.append(bar);
                        }
                    } else {
                        barsContainer.html('<p class="text-indigo-300 text-sm">Belum ada data</p>');
                    }

                    const expiringSoonList = $('#expiring-soon-list').empty();
                    if (data.expiring_soon.length > 0) {
                        data.expiring_soon.slice(0, 3).forEach(v => {
                            expiringSoonList.append(
                                `<li class="flex justify-between items-center"><p class="truncate font-semibold">${v.name}</p><p class="text-amber-700 bg-amber-100 text-xs font-bold px-2 py-1 rounded-md">${formatDate(v.expiry_date).split(' ')[0]} ${formatDate(v.expiry_date).split(' ')[1]}</p></li>`
                            );
                        });
                    } else {
                        expiringSoonList.html('<li class="text-gray-400">Tidak ada voucher.</li>');
                    }

                    const branchPerformanceList = $('#branch-performance-list').empty();
                    const branchData = data.branch_performance;
                    if (branchData.length > 0) {
                        branchData.forEach(branch => {
                            const totalVouchers = branch.vouchers_count + data.all_branch_vouchers;
                            branchPerformanceList.append(
                                `<li class="flex items-center justify-between"><div class="flex items-center gap-3"><i class="bi bi-geo-alt-fill text-gray-400"></i><div><p class="font-bold">${branch.nama_cabang}</p><p class="text-xs text-gray-500">${totalVouchers} Voucher Tersedia</p></div></div></li>`
                            );
                        });
                    } else {
                        branchPerformanceList.html('<li class="text-gray-400 text-sm">Belum ada data cabang.</li>');
                    }
                }

                function loadData() {
                    $.ajax({
                        url: "{{ route('owner.datavoucher.data') }}",
                        type: 'GET',
                        success: function(response) {
                            allVoucherData = response.vouchers;
                            renderTable(allVoucherData);
                            updateDashboard(response);
                        },
                        error: function() {
                            Swal.fire('Error', 'Gagal memuat data voucher.', 'error');
                        }
                    });
                }

                $('#searchInput').on('keyup', function() {
                    const searchTerm = $(this).val().toLowerCase();
                    const filtered = Object.fromEntries(Object.entries(allVoucherData).filter(([id, v]) => v
                        .name.toLowerCase().includes(searchTerm)));
                    renderTable(filtered);
                });

                const voucherModal = $('#voucher-modal');
                const detailModal = $('#detail-modal');
                const openModal = (modal) => {
                    modal.removeClass('hidden');
                    setTimeout(() => modal.find('.modal-content').removeClass('opacity-0 -translate-y-10'), 10);
                };
                const closeModal = (modal) => {
                    modal.find('.modal-content').addClass('opacity-0 -translate-y-10');
                    setTimeout(() => modal.addClass('hidden'), 300);
                };

                // --- [BARU] Toggling input diskon ---
                function toggleDiscountInputs(type) {
                    if (type === 'percentage') {
                        $('#group-percentage').removeClass('hidden');
                        $('#group-fixed').addClass('hidden');
                        $('#discount_percentage').prop('required', true);
                        $('#discount_amount').prop('required', false);
                    } else { // 'fixed'
                        $('#group-percentage').addClass('hidden');
                        $('#group-fixed').removeClass('hidden');
                        $('#discount_percentage').prop('required', false);
                        $('#discount_amount').prop('required', true);
                    }
                }

                $('#type').on('change', function() {
                    toggleDiscountInputs($(this).val());
                });

                $('#has_stock').on('change', function() {
                    $('#stock-group').toggleClass('hidden', !this.checked);
                    $('#stock').prop('required', this.checked);
                });

                $('#has_expiry').on('change', function() {
                    $('#expiry-date-group').toggleClass('hidden', !this.checked);
                    $('#expiry_date').prop('required', this.checked);
                });

                $('body').on('click', '#add-voucher-btn-hero, #add-voucher-btn', () => {
                    $('#modal-title').text('Buat Voucher Baru');
                    $('#voucher-form')[0].reset();
                    $('#voucherId').val('');
                    $('#has_expiry').prop('checked', false).trigger('change');
                    $('#has_stock').prop('checked', false).trigger('change');
                    $('#type').val('percentage').trigger('change'); // Set default ke percentage
                    openModal(voucherModal);
                });

                $('body').on('click', '.edit-voucher', function() {
                    const id = $(this).data('id');
                    const data = allVoucherData[id];
                    if (data) {
                        $('#modal-title').text('Edit Data Voucher');
                        $('#voucherId').val(id);
                        $('#name').val(data.name);
                        $('#description').val(data.description);
                        $('#cabang').val(data.cabang_id || 'all');

                        // Isi data baru
                        $('#type').val(data.type).trigger(
                            'change'); // Trigger change untuk tampilkan input yg benar
                        $('#discount_percentage').val(data.discount_percentage);
                        $('#max_discount').val(data.max_discount);
                        $('#discount_amount').val(data.discount_amount);
                        $('#min_purchase').val(data.min_purchase);

                        $('#has_stock').prop('checked', data.stock !== null).trigger('change');
                        if (data.stock !== null) $('#stock').val(data.stock);

                        $('#has_expiry').prop('checked', !!data.expiry_date).trigger('change');
                        if (data.expiry_date) $('#expiry_date').val(data.expiry_date);

                        openModal(voucherModal);
                    }
                });

                $('body').on('click', '.delete-voucher', function() {
                    const id = $(this).data('id');
                    const name = allVoucherData[id].name;
                    Swal.fire({
                        title: 'Anda yakin?',
                        html: `Voucher "<b>${name}</b>" akan dihapus.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/owner/datavoucher/${id}`,
                                type: 'DELETE',
                                success: function(response) {
                                    loadData();
                                    Swal.fire('Terhapus!', response.success, 'success');
                                },
                                error: function(xhr) {
                                    Swal.fire('Gagal!', xhr.responseJSON.error ||
                                        'Gagal menghapus voucher.', 'error');
                                }
                            });
                        }
                    });
                });

                $('body').on('click', '.detail-voucher', function() {
                    const id = $(this).data('id');
                    const data = allVoucherData[id];
                    if (data) {
                        const statusClass = `status-${data.status_label.toLowerCase().replace(' ', '-')}`;
                        const stockInfo = data.stock === null ? 'Tak Terbatas' :
                            `${data.stock - data.times_used} / ${data.stock}`;

                        let discountDetail = '';
                        if (data.type === 'percentage') {
                            discountDetail =
                                `<p class="font-bold text-lg">${parseFloat(data.discount_percentage)}%</p><p class="text-xs text-gray-500">Maks. diskon Rp${formatCurrency(data.max_discount)}</p>`;
                        } else {
                            discountDetail =
                                `<p class="font-bold text-lg">Rp${formatCurrency(data.discount_amount)}</p>`;
                        }

                        const content = `
                        <div class="space-y-4">
                            <div class="flex items-center gap-4">
                                ${createVoucherSVG(data)}
                                <div>
                                    <h4 class="text-xl font-bold">${data.name}</h4>
                                    <p class="text-sm text-gray-500">${data.description || 'Tidak ada deskripsi.'}</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 pt-4 border-t">
                                <div><p class="text-xs text-gray-500">Kode Voucher</p><p class="font-mono font-bold text-lg text-indigo-600 bg-indigo-50 px-2 py-1 rounded-md inline-block">${data.code}</p></div>
                                <div><p class="text-xs text-gray-500">Tipe</p><p class="font-semibold">${data.type === 'percentage' ? 'Persentase' : 'Potongan Tetap'}</p></div>
                                
                                <div><p class="text-xs text-gray-500">Diskon</p>${discountDetail}</div>
                                <div><p class="text-xs text-gray-500">Min. Pembelian</p><p class="font-semibold">Rp${formatCurrency(data.min_purchase)}</p></div>

                                <div><p class="text-xs text-gray-500">Cabang</p><p class="font-semibold">${data.applicable_branch}</p></div>
                                <div><p class="text-xs text-gray-500">Status</p><p><span class="badge ${statusClass}">${data.status_label}</span></p></div>
                                <div><p class="text-xs text-gray-500">Masa Berlaku</p><p class="font-semibold">${formatDate(data.expiry_date)}</p></div>
                                <div><p class="text-xs text-gray-500">Sisa Stok (Terpakai/Total)</p><p class="font-semibold">${stockInfo}</p></div>
                            </div>
                        </div>
                    `;
                        $('#detail-modal-content').html(content);
                        openModal(detailModal);
                    }
                });

                $('.close-modal').on('click', function() {
                    closeModal($(this).closest('.modal-overlay'));
                });

                $('#voucher-form').on('submit', function(e) {
                    e.preventDefault();
                    const id = $('#voucherId').val();
                    const url = id ? `/owner/datavoucher/${id}` : "{{ route('owner.datavoucher.store') }}";
                    const method = 'POST';

                    const formData = new FormData(this);
                    if (id) {
                        formData.append('_method', 'POST');
                    }

                    $.ajax({
                        url: url,
                        method: method,
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            closeModal(voucherModal);
                            loadData();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.success,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        },
                        error: function(xhr) {
                            let errorMsg = '<ul>';
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                $.each(xhr.responseJSON.errors, function(key, value) {
                                    errorMsg += `<li>${value[0]}</li>`;
                                });
                            } else {
                                errorMsg +=
                                    `<li>${xhr.responseJSON.error || 'Terjadi kesalahan. Silakan coba lagi.'}</li>`;
                            }
                            errorMsg += '</ul>';
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                html: errorMsg
                            });
                        }
                    });
                });

                loadData();
            });
        </script>
    </body>

    </html>

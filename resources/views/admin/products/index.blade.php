<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manajemen Produk | CELVION</title>

    {{-- Tailwind CSS & jQuery --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- Ikon & Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    
    {{-- Animasi AOS & SweetAlert2 --}}
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    {{-- DataTables CSS --}}
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css" rel="stylesheet" />
    
    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; }
        .variant-tag { padding: 4px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 500; display: inline-flex; align-items: center; margin: 2px; line-height: 1.5; }
        
        /* [BARU] Class untuk stok aman (biru) */
        .variant-tag-stok-aman {
            background-color: #dbeafe; /* blue-100 */
            color: #1e40af; /* blue-800 */
            border: 1px solid #60a5fa; /* blue-400 */
        }
        .variant-tag-stok-menipis { 
            background-color: #fef3c7; /* amber-100 */
            color: #92400e; /* amber-800 */
            border: 1px solid #f59e0b; /* amber-500 */
        }
        .variant-tag-stok-habis { 
            background-color: #fee2e2; /* red-100 */
            color: #991b1b; /* red-800 */
            border: 1px solid #ef4444; /* red-500 */
        }

        .modal-overlay { transition: opacity 0.3s ease; }
        .modal-content { transition: transform 0.3s ease, opacity 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
        .status-badge { padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; text-transform: capitalize; }
        .status-baru { background-color: #dcfce7; color: #166534; }
        .status-second { background-color: #fef9c3; color: #854d0e; }
        
        .summary-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .summary-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.1);
        }

        #productTable_wrapper .dt-search input,
        #productTable_wrapper .dt-length select,
        .custom-filter {
            background-color: white !important; color: #1e293b !important; border: 1px solid #cbd5e1 !important;
            border-radius: 0.5rem !important; padding: 0.5rem 0.75rem !important; outline: none;
        }
        #productTable_wrapper .dt-search input:focus,
        #productTable_wrapper .dt-length select:focus,
        .custom-filter:focus {
            border-color: #6366f1 !important; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2) !important;
        }
        #productTable_wrapper .dt-search input { padding-left: 2.25rem !important; }
        #productTable_wrapper .dt-paging .dt-paging-button.current { background: #4f46e5 !important; color: #ffffff !important; }

        @media (max-width: 767px) {
            .mobile-card-view thead { display: none; }
            .mobile-card-view tr { background-color: white; margin-bottom: 1rem; border: 1px solid #e2e8f0; border-radius: 0.75rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); overflow: hidden; display: block; }
            .mobile-card-view td { padding: 0.75rem 1rem; border-bottom: 1px solid #f1f5f9; text-align: right; display: block; }
            .mobile-card-view td[data-label]::before { content: attr(data-label); float: left; font-weight: 600; color: #475569; }
            .mobile-card-view td[data-label="Produk"] { padding: 1rem; border-bottom: 1px solid #e2e8f0; }
            .mobile-card-view td[data-label="Produk"]::before { display: none; }
        }
    </style>
</head>
<body>
    <div class="relative min-h-screen md:flex">
        
        @include('admin.partials.sidebar')

        <div class="flex-1 md:ml-64 flex flex-col min-w-0">
            <main class="flex-grow p-4 sm:p-6 lg:p-8 animate-fade-in">
                
                @include('admin.partials.header', [
                    'title' => 'Manajemen Produk',
                    'subtitle' => 'Kelola semua produk dan varian yang tersedia di toko.'
                ])

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8" data-aos="fade-up">
                    <div class="summary-card bg-white p-6 rounded-2xl shadow-lg flex items-center justify-between">
                        <div><p class="text-gray-500 font-medium">Total Produk</p><h3 id="total-produk" class="text-3xl font-bold text-gray-800 mt-1">0</h3></div>
                        <div class="bg-blue-100 text-blue-600 p-4 rounded-full text-2xl"><i class="bi bi-box-seam"></i></div>
                    </div>
                    <div class="summary-card bg-white p-6 rounded-2xl shadow-lg flex items-center justify-between">
                        <div><p class="text-gray-500 font-medium">Total Varian</p><h3 id="total-varian" class="text-3xl font-bold text-gray-800 mt-1">0</h3></div>
                        <div class="bg-green-100 text-green-600 p-4 rounded-full text-2xl"><i class="bi bi-stack"></i></div>
                    </div>
                    <div class="summary-card bg-white p-6 rounded-2xl shadow-lg flex items-center justify-between">
                        <div><p class="text-gray-500 font-medium">Stok Menipis</p><h3 id="stok-menipis" class="text-3xl font-bold text-gray-800 mt-1">0</h3></div>
                        <div class="bg-amber-100 text-amber-600 p-4 rounded-full text-2xl"><i class="bi bi-exclamation-triangle"></i></div>
                    </div>
                    <div class="summary-card bg-white p-6 rounded-2xl shadow-lg flex items-center justify-between">
                        <div><p class="text-gray-500 font-medium">Stok Habis</p><h3 id="stok-habis" class="text-3xl font-bold text-gray-800 mt-1">0</h3></div>
                        <div class="bg-red-100 text-red-600 p-4 rounded-full text-2xl"><i class="bi bi-x-octagon"></i></div>
                    </div>
                </div>
                
                <div class="bg-white p-4 md:p-6 rounded-2xl shadow-lg" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
                        <h4 class="text-xl font-semibold text-gray-700 w-full md:w-auto">Daftar Produk</h4>
                        <button id="add-product-btn" class="w-full md:w-auto flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg bg-indigo-600 text-white shadow-md hover:bg-indigo-700 transition">
                            <i class="bi bi-plus-circle-fill"></i><span>Tambah Produk</span>
                        </button>
                    </div>

                    <table id="productTable" class="w-full text-sm mobile-card-view" style="width:100%">
                        <thead class="bg-gray-50 text-gray-600 uppercase">
                            <tr>
                                <th class="p-4 text-left">Produk</th>
                                <th class="p-4 text-left">Kategori</th>
                                <th class="p-4 text-center">Status</th>
                                <th class="p-4 text-left">Harga Jual</th>
                                <th class="p-4 text-left">Varian & Stok</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    {{-- Modal Edit Produk --}}
    <div id="product-modal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden modal-overlay opacity-0">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl modal-content transform scale-95 opacity-0 max-h-[90vh] overflow-y-auto">
            <form id="product-form">
                <input type="hidden" id="product-id">
                <div class="flex justify-between items-center p-5 border-b sticky top-0 bg-white z-10"><h3 id="modal-title" class="text-xl font-bold"></h3><button type="button" class="close-modal p-2"><i class="bi bi-x-lg"></i></button></div>
                <div class="p-6 space-y-6">
                    <div class="border-b pb-6">
                        <label class="block font-bold text-slate-700 mb-3">Informasi Produk Utama</label>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div><label for="name" class="block mb-1 text-sm font-medium">Nama Produk</label><input type="text" id="name" required class="w-full px-3 py-2 border rounded-lg"></div>
                                    <div><label for="category" class="block mb-1 text-sm font-medium">Kategori</label><select id="category" required class="w-full px-3 py-2 border rounded-lg"><option value="">Pilih</option><option value="Handphone">Handphone</option><option value="Aksesori">Aksesori</option></select></div>
                                </div>
                                <div class="mt-4"><label for="master-price" class="block mb-1 text-sm font-medium">Harga Jual Utama</label><input type="text" id="master-price" required class="w-full px-3 py-2 border rounded-lg" placeholder="Contoh: 18.999.000"></div>
                                <div class="mt-4"><label for="description" class="block mb-1 text-sm font-medium">Deskripsi</label><textarea id="description" rows="5" class="w-full px-3 py-2 border rounded-lg" placeholder="Jelaskan detail produk, spesifikasi, kondisi, dan kelengkapannya..."></textarea></div>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium">Galeri Foto Produk</label>
                                <div id="master-images-preview-container" class="grid grid-cols-3 sm:grid-cols-4 gap-2 mb-2"></div>
                                <label for="master-images-upload" class="cursor-pointer bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold py-2 px-4 rounded-lg text-sm block text-center transition">
                                    <i class="bi bi-upload mr-2"></i>Tambah Foto
                                </label>
                                <input type="file" id="master-images-upload" class="hidden" accept="image/*" multiple>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="block font-bold text-slate-700">Varian Produk</label>
                            
                        </div>
                        <div id="variants-container" class="space-y-4"></div>
                    </div>
                </div>
                <div class="p-5 border-t bg-gray-50 flex justify-end gap-3 sticky bottom-0 z-10"><button type="button" class="close-modal px-4 py-2 bg-gray-200 rounded-lg font-semibold hover:bg-gray-300">Batal</button><button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700">Simpan</button></div>
            </form>
        </div>
    </div>
    
    <div id="variant-template" class="hidden">
        <div class="variant-row grid grid-cols-12 gap-4 items-start bg-slate-50 p-4 rounded-lg border">
            <input type="hidden" class="variant-id">
            <div class="col-span-12 md:col-span-3">
                <img class="variant-image-preview w-full aspect-square rounded-lg object-cover border-2 mb-2" src="https://placehold.co/300x300/e2e8f0/64748b?text=Pilih+Gambar">
                <label class="variant-image-label cursor-pointer bg-slate-200 text-slate-800 font-semibold py-2 px-4 rounded-lg text-sm block text-center">Pilih Gambar</label>
                <input type="file" class="variant-image-upload hidden" accept="image/*">
            </div>
            <div class="col-span-12 md:col-span-8 space-y-3">
                 <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium mb-1 variant-color-label">Warna</label><input type="text" class="variant-color w-full px-3 py-2 border rounded-md text-sm" placeholder="e.g., Hitam" required></div>
                    <div class="variant-ram-group"><label class="block text-xs font-medium mb-1">RAM/ROM</label><input type="text" class="variant-ram w-full px-3 py-2 border rounded-md text-sm" placeholder="e.g., 8/128GB" required></div>
                 </div>
                 <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium mb-1">Harga Varian</label><input type="text" class="variant-price w-full px-3 py-2 border rounded-md text-sm" placeholder="Contoh: 15.000.000" required></div>
                    <div><label class="block text-xs font-medium mb-1">Stok</label><input type="number" class="variant-stock w-full px-3 py-2 border rounded-md text-sm disabled:bg-gray-100" placeholder="0" min="0" disabled></div>
                 </div>
            </div>
            <div class="col-span-12 md:col-span-1 flex md:flex-col items-center justify-end h-full">
                <button type="button" class="remove-variant-btn w-8 h-8 flex items-center justify-center rounded-lg bg-red-100 text-red-600 hover:bg-red-200"><i class="bi bi-trash"></i></button>
            </div>
        </div>
    </div>

    <!-- Modal Detail Produk -->
    <div id="detail-product-modal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden modal-overlay opacity-0">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl modal-content transform scale-95 opacity-0 max-h-[90vh] flex flex-col">
            <div class="flex justify-between items-center p-5 border-b sticky top-0 bg-white z-10">
                <h3 class="text-xl font-bold text-gray-800">Detail Produk</h3>
                <button type="button" class="close-modal p-2 rounded-full hover:bg-gray-100"><i class="bi bi-x-lg text-xl"></i></button>
            </div>
            <div class="p-6 overflow-y-auto space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Kolom Kiri: Galeri Foto -->
                    <div>
                        <div id="detail-gallery-main" class="mb-4 rounded-lg overflow-hidden border">
                            <img src="https://placehold.co/600x600/e2e8f0/64748b?text=..." alt="Foto Produk Utama" class="w-full h-auto object-cover aspect-square">
                        </div>
                        <div id="detail-gallery-thumbnails" class="grid grid-cols-5 gap-2">
                        </div>
                    </div>
                    <!-- Kolom Kanan: Detail Info -->
                    <div>
                        <h2 id="detail-name" class="text-3xl font-bold text-gray-800 mb-2">Nama Produk</h2>
                        <div class="flex items-center gap-3 mb-4">
                            <span id="detail-condition" class="status-badge">Baru</span>
                            <span id="detail-display-status" class="text-xs font-semibold px-2.5 py-1 rounded-full"></span>
                        </div>
                        <div class="mb-6">
                            <span class="text-sm text-gray-500">Harga Jual Mulai dari</span>
                            <p id="detail-master-price" class="text-4xl font-extrabold text-indigo-600">Rp0</p>
                        </div>
                        <div class="prose prose-sm max-w-none text-gray-600">
                            <h4 class="font-semibold text-gray-700">Deskripsi</h4>
                            <p id="detail-description"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Daftar Varian -->
                <div>
                    <h4 class="font-semibold text-gray-700 mb-3 text-lg border-t pt-6">Varian yang Tersedia</h4>
                    <div id="detail-variants-container" class="space-y-3">
                    </div>
                </div>
            </div>
            <div class="p-5 border-t bg-gray-50 flex justify-end gap-3 sticky bottom-0 z-10">
                <button type="button" class="close-modal px-5 py-2 bg-gray-200 text-gray-800 rounded-lg font-semibold hover:bg-gray-300">Tutup</button>
            </div>
        </div>
    </div>
    
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script>
    $(document).ready(function() {
        AOS.init({ duration: 600, once: true, offset: 20 });

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $('#add-product-btn').hide();

        let productData = [];
        let table;

        function formatRupiah(number) {
            if (isNaN(number) || number === null) return 'Rp 0';
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(number);
        }

        function formatNumberInput(input) {
            let value = input.val().replace(/[^0-9]/g, '');
            input.val(value ? Number(value).toLocaleString('id-ID') : '');
        }

        function parseCurrency(value) {
            return Number(String(value).replace(/[^0-9]/g, ''));
        }

        function loadProductsData() {
            $.ajax({
                url: '{{ route("admin.products.api.get") }}',
                method: 'GET',
                success: function(response) {
                    productData = response;
                    if ($.fn.DataTable.isDataTable('#productTable')) {
                        table.clear().rows.add(productData).draw();
                    } else {
                        initializeDataTable();
                    }
                    updateSummaryCards();
                },
                error: function(xhr) {
                    console.error('Gagal memuat data produk:', xhr);
                    Swal.fire('Error', 'Gagal memuat data produk', 'error');
                }
            });
        }

        const productModal = $('#product-modal');
        
        function initializeDataTable() {
            table = $('#productTable').DataTable({
                data: productData,
                dom: "<'flex flex-col md:flex-row items-center justify-between gap-4 mb-6'<'dt-length'l><'flex flex-wrap items-center gap-2'<'#custom-filter-slot'><'dt-search'f>>>t<'flex flex-col md:flex-row items-center justify-between gap-4 mt-4'<'dt-info'i><'dt-paging'p>>",
                columns: [
                    { data: 'name', render: (data, type, row) => `<div class="flex items-center gap-4"><img src="${row.master_images[0] || 'https://placehold.co/80x80/e2e8f0/64748b?text=N/A'}" class="w-12 h-12 rounded-md object-cover"><span class="font-semibold text-gray-800">${data}</span></div>` },
                    { data: 'category' },
                    { data: 'condition', className: 'text-center', render: data => `<span class="status-badge status-${String(data).toLowerCase()}">${data}</span>` },
                    { data: 'master_price', render: data => `<span class="font-medium text-gray-700">${formatRupiah(data)}</span>` },
                    { 
                        data: 'variants', 
                        orderable: false, 
                        searchable: false, 
                        render: data => data.map(v => {
                            // --- [UPDATE] LOGIC WARNA STOK ---
                            let stockClass = '';
                            let stockText;
                            if (v.stock === 0) {
                                stockClass = 'variant-tag-stok-habis'; // Merah
                                stockText = `<b>Stok Habis</b>`;
                            } else if (v.stock > 0 && v.stock <= 5) {
                                stockClass = 'variant-tag-stok-menipis'; // Kuning
                                stockText = `<b>Stok Menipis: ${v.stock}</b>`;
                            } else {
                                stockClass = 'variant-tag-stok-aman'; // Biru
                                stockText = `Stok: ${v.stock}`;
                            }
                            return `<span class="variant-tag ${stockClass}">${v.color}${v.ram ? '/' + v.ram : ''} - ${stockText}</span>`;
                            // --- AKHIR UPDATE ---
                        }).join('') 
                    },
                    { 
                        data: 'id', 
                        orderable: false, 
                        searchable: false, 
                        className: 'text-center',
                        render: function(data, type, row) {
                            let toggleButtonText = row.display_status === 'live' ? 'Sembunyikan' : 'Tampilkan';
                            let toggleButtonClass = row.display_status === 'live' ? 'bg-gray-500 hover:bg-gray-600' : 'bg-green-500 hover:bg-green-600';
                            return `<div class="flex justify-center items-center gap-2"><button class="toggle-display-btn px-3 py-1 flex items-center justify-center rounded-md ${toggleButtonClass} text-white text-xs font-bold transition-colors" data-id="${data}" title="${toggleButtonText === 'Sembunyikan' ? 'Sembunyikan dari Toko' : 'Tampilkan di Toko'}">${toggleButtonText}</button><button class="detail-btn w-8 h-8 flex items-center justify-center rounded-md bg-sky-500 text-white hover:bg-sky-600 transition-colors" data-id="${data}" title="Lihat Detail Produk"><i class="bi bi-eye-fill"></i></button><button class="edit-btn w-8 h-8 flex items-center justify-center rounded-md bg-amber-500 text-white hover:bg-amber-600 transition-colors" data-id="${data}" title="Edit Detail Produk"><i class="bi bi-pencil-fill"></i></button><button class="delete-btn w-8 h-8 flex items-center justify-center rounded-md bg-red-500 text-white hover:bg-red-600 transition-colors" data-id="${data}" title="Hapus Produk"><i class="bi bi-trash-fill"></i></button></div>`;
                        }
                    }
                ],
                createdRow: function(row, data, dataIndex) {
                    $('td', row).eq(0).attr('data-label', 'Produk');
                    $('td', row).eq(1).attr('data-label', 'Kategori');
                    $('td', row).eq(2).attr('data-label', 'Status');
                    $('td', row).eq(3).attr('data-label', 'Harga Jual');
                    $('td', row).eq(4).attr('data-label', 'Varian & Stok');
                    $('td', row).eq(5).attr('data-label', 'Aksi');
                },
                language: { search: "", searchPlaceholder: "Cari...", lengthMenu: "Tampil _MENU_", info: "Menampilkan _START_ - _END_ dari _TOTAL_ data" }
            });

            const customFilters = `<select id="categoryFilter" class="custom-filter w-full md:w-auto"><option value="">Semua Kategori</option><option value="Handphone">Handphone</option><option value="Aksesori">Aksesori</option></select><select id="statusFilter" class="custom-filter w-full md:w-auto"><option value="">Semua Status</option><option value="Baru">Baru</option><option value="Second">Second</option></select>`;
            $(customFilters).appendTo("#custom-filter-slot");
            $('#productTable_wrapper .dt-search').addClass('relative').find('input').before('<i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>');
        
            $('#productTable_wrapper').on('change', '#categoryFilter', function() { table.column(1).search(this.value).draw(); });
            $('#productTable_wrapper').on('change', '#statusFilter', function() { table.column(2).search(this.value).draw(); });
        }
        
        function updateSummaryCards() {
            let totalVarian = 0, stokMenipis = 0, stokHabis = 0;
            productData.forEach(p => p.variants.forEach(v => {
                totalVarian++;
                if (v.stock === 0) stokHabis++;
                else if (v.stock > 0 && v.stock <= 5) stokMenipis++;
            }));
            $('#total-produk').text(productData.length);
            $('#total-varian').text(totalVarian);
            $('#stok-menipis').text(stokMenipis);
            $('#stok-habis').text(stokHabis);
        }

        function openModal(modal) { modal.removeClass('hidden'); setTimeout(() => modal.removeClass('opacity-0').find('.modal-content').removeClass('opacity-0 scale-95'), 10); $('body').addClass('overflow-hidden'); }
        function closeModal(modal) { modal.addClass('opacity-0').find('.modal-content').addClass('opacity-0 scale-95'); setTimeout(() => modal.addClass('hidden'), 300); $('body').removeClass('overflow-hidden'); }
        
        function updateVariantFields(category) {
            const isHandphone = category === 'Handphone';
            $('#variants-container .variant-row').each(function() {
                $(this).find('.variant-ram-group').toggle(isHandphone);
                $(this).find('.variant-ram').prop('required', isHandphone);
                const colorLabel = isHandphone ? 'Warna' : 'Warna / Varian';
                $(this).find('.variant-color-label').text(colorLabel);
            });
        }
        
        function addVariantRow(data = {}) {
            const newRow = $($('#variant-template').html());
            const uniqueId = `variant-image-upload-${Date.now()}-${Math.random()}`;
            newRow.find('.variant-image-upload').attr('id', uniqueId);
            newRow.find('.variant-image-label').attr('for', uniqueId);
            if (data.id) newRow.find('.variant-id').val(data.id);
            if (data.color) newRow.find('.variant-color').val(data.color);
            if (data.ram) newRow.find('.variant-ram').val(data.ram);
            if (data.price) newRow.find('.variant-price').val(Number(data.price).toLocaleString('id-ID'));
            if (data.image) newRow.find('.variant-image-preview').attr('src', data.image);
            if (data.stock != null) newRow.find('.variant-stock').val(data.stock);
            $('#variants-container').append(newRow);
            
            const currentCategory = $('#category').val();
            updateVariantFields(currentCategory);
        }
        
        $('#add-variant-btn').on('click', () => addVariantRow());
        $(document).on('click', '.remove-variant-btn', function() { $(this).closest('.variant-row').remove(); });
        
        $(document).on('change', '.variant-image-upload', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                const preview = $(this).closest('.variant-row').find('.variant-image-preview');
                reader.onload = (event) => preview.attr('src', event.target.result);
                reader.readAsDataURL(e.target.files[0]);
            }
        });
        
        $('#master-images-upload').on('change', function(e) {
            if (e.target.files) {
                Array.from(e.target.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const previewHtml = `<div class="relative group"><img src="${event.target.result}" class="w-full h-20 object-cover rounded-md border"><button type="button" class="remove-master-image absolute top-1 right-1 w-5 h-5 bg-red-600 text-white rounded-full flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition">&times;</button></div>`;
                        $('#master-images-preview-container').append(previewHtml);
                    }
                    reader.readAsDataURL(file);
                });
            }
        });
        
        $(document).on('click', '.remove-master-image', function() { $(this).parent().remove(); });

        $('#product-modal').on('input', '#master-price, .variant-price', function() { formatNumberInput($(this)); });
        
        $('#product-modal').on('change', '#category', function() {
            updateVariantFields($(this).val());
        });
        
        $('#productTable tbody').on('click', '.edit-btn', function() {
            const productId = parseInt($(this).data('id'));
            
            $.ajax({
                url: `/admin/products/${productId}/edit`,
                method: 'GET',
                success: function(data) {
                    $('#product-form')[0].reset();
                    $('#modal-title').text('Edit Produk');
                    $('#product-id').val(data.id); 
                    $('#name').val(data.name); 
                    $('#category').val(data.category);
                    
                    // [DIUBAH] Kosongkan deskripsi jika berisi teks default
                    const defaultDesc = 'Deskripsi akan dilengkapi oleh admin';
                    if (data.description && data.description.trim() === defaultDesc) {
                        $('#description').val('');
                    } else {
                        $('#description').val(data.description);
                    }
                    
                    $('#master-price').val(data.master_price ? Number(data.master_price).toLocaleString('id-ID') : '');
                    
                    const previewContainer = $('#master-images-preview-container').empty();
                    if (data.master_images && Array.isArray(data.master_images)) {
                        data.master_images.forEach(imgUrl => {
                            if (imgUrl && !imgUrl.includes('placehold.co')) { // Jangan tampilkan placeholder di preview
                                previewContainer.append(`<div class="relative group" data-existing-image="${imgUrl}"><img src="${imgUrl}" class="w-full h-20 object-cover rounded-md border"><button type="button" class="remove-master-image absolute top-1 right-1 w-5 h-5 bg-red-600 text-white rounded-full flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition">&times;</button></div>`);
                            }
                        });
                    }
                    
                    $('#variants-container').empty();
                    data.variants.forEach(v => {
                        const variantImg = v.image ? (v.image.startsWith('http') ? v.image : `/storage/${v.image}`) : 'https://placehold.co/300x300/e2e8f0/64748b?text=Pilih+Gambar';
                        addVariantRow({ id: v.id, color: v.color, ram: v.ram, price: v.price, stock: v.stock, image: variantImg });
                    });
                    
                    openModal(productModal);
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Gagal memuat data produk', 'error');
                }
            });
        });

        $('#productTable tbody').on('click', '.delete-btn', function() {
            const productId = parseInt($(this).data('id'));
            const product = productData.find(p => p.id === productId);
            
            Swal.fire({ 
                title: 'Anda Yakin?', text: `Produk "${product.name}" akan dihapus permanen!`, icon: 'warning', showCancelButton: true, 
                confirmButtonColor: '#e11d48', cancelButtonColor: '#6b7280', confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal' 
            }).then((result) => { 
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/products/${productId}`,
                        method: 'DELETE',
                        success: function(response) {
                            Swal.fire('Terhapus!', response.success, 'success');
                            loadProductsData();
                        },
                        error: function(xhr) {
                            Swal.fire('Error', xhr.responseJSON.error || 'Gagal menghapus produk', 'error');
                        }
                    });
                } 
            });
        });
        
        $('#productTable tbody').on('click', '.toggle-display-btn', function() {
            const productId = $(this).data('id');
            const product = productData.find(p => p.id === productId);
            const actionText = product.display_status === 'live' ? 'menyembunyikan' : 'menampilkan';

            Swal.fire({
                title: `Anda yakin ingin ${actionText} produk ini?`,
                text: `Produk "${product.name}" akan ${actionText} dari halaman pelanggan.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: `Ya, ${actionText}!`,
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/products/${productId}/toggle-display`,
                        method: 'PATCH',
                        success: function(response) {
                            Swal.fire('Berhasil!', response.success, 'success');
                            loadProductsData();
                        },
                        error: function(xhr) {
                            const errorMessage = xhr.responseJSON && xhr.responseJSON.error 
                                                 ? xhr.responseJSON.error 
                                                 : 'Terjadi kesalahan.';
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: errorMessage,
                                footer: `<a href="#" class="edit-btn-from-swal" data-id="${productId}">Klik di sini untuk melengkapi data.</a>`
                            });
                        }
                    });
                }
            });
        });

        $(document).on('click', '.edit-btn-from-swal', function(e) {
            e.preventDefault();
            const productId = $(this).data('id');
            Swal.close();
            $(`.edit-btn[data-id="${productId}"]`).trigger('click');
        });

        $('#productTable tbody').on('click', '.detail-btn', function() {
            const productId = parseInt($(this).data('id'));
            const product = productData.find(p => p.id === productId);

            if (product) {
                const detailModal = $('#detail-product-modal');
                detailModal.find('#detail-name').text(product.name);
                detailModal.find('#detail-master-price').text(formatRupiah(product.master_price));
                detailModal.find('#detail-description').html(product.description.replace(/\n/g, '<br>'));
                const conditionBadge = detailModal.find('#detail-condition');
                conditionBadge.text(product.condition).removeClass('status-baru status-second').addClass(`status-${String(product.condition).toLowerCase()}`);
                const displayBadge = detailModal.find('#detail-display-status');
                if (product.display_status === 'live') {
                    displayBadge.text('Ditampilkan').removeClass('bg-gray-100 text-gray-800').addClass('bg-green-100 text-green-800');
                } else {
                    displayBadge.text('Disembunyikan').removeClass('bg-green-100 text-green-800').addClass('bg-gray-100 text-gray-800');
                }
                const mainGallery = detailModal.find('#detail-gallery-main');
                const thumbnailsContainer = detailModal.find('#detail-gallery-thumbnails').empty();
                if (product.master_images && product.master_images.length > 0 && product.master_images[0] && !product.master_images[0].includes('placehold.co')) {
                    mainGallery.find('img').attr('src', product.master_images[0]);
                    product.master_images.forEach((imgUrl, index) => {
                        const activeClass = index === 0 ? 'border-indigo-500' : 'border-transparent';
                        thumbnailsContainer.append(`<img src="${imgUrl}" class="cursor-pointer w-full aspect-square object-cover rounded border-2 ${activeClass} hover:border-indigo-500 transition">`);
                    });
                } else {
                    mainGallery.find('img').attr('src', 'https://placehold.co/600x600/e2e8f0/64748b?text=No+Image');
                }
                const variantsContainer = detailModal.find('#detail-variants-container').empty();
                if (product.variants && product.variants.length > 0) {
                    product.variants.forEach(variant => {
                        variantsContainer.append(`<div class="flex items-center gap-4 p-3 border rounded-lg bg-slate-50"><img src="${variant.image || 'https://placehold.co/80x80/e2e8f0/64748b?text=N/A'}" class="w-16 h-16 rounded-md object-cover flex-shrink-0"><div class="flex-grow grid grid-cols-2 md:grid-cols-4 gap-4 items-center"><div><div class="text-xs text-gray-500">Warna</div><div class="font-semibold text-gray-800">${variant.color}</div></div><div><div class="text-xs text-gray-500">Spek</div><div class="font-semibold text-gray-800">${variant.ram || '-'}</div></div><div><div class="text-xs text-gray-500">Stok</div><div class="font-bold text-lg ${variant.stock > 0 ? 'text-blue-600' : 'text-red-600'}">${variant.stock}</div></div><div><div class="text-xs text-gray-500">Harga</div><div class="font-bold text-gray-800">${formatRupiah(variant.price)}</div></div></div></div>`);
                    });
                } else {
                    variantsContainer.html('<p class="text-center text-gray-500">Tidak ada varian untuk produk ini.</p>');
                }
                openModal(detailModal);
            }
        });

        $(document).on('click', '#detail-gallery-thumbnails img', function() {
            const newSrc = $(this).attr('src');
            $('#detail-gallery-main img').attr('src', newSrc);
            $('#detail-gallery-thumbnails img').removeClass('border-indigo-500').addClass('border-transparent');
            $(this).removeClass('border-transparent').addClass('border-indigo-500');
        });

        $('#product-form').on('submit', function(e) {
            e.preventDefault();
            const id = $('#product-id').val();
            const url = id ? `/admin/products/${id}` : ""; // Ganti dengan route store jika ada
            const method = 'POST';
            
            const formData = new FormData(this);
            formData.append('_method', 'PUT');
            formData.append('name', $('#name').val());
            formData.append('category', $('#category').val());
            formData.append('description', $('#description').val());
            formData.append('master_price', parseCurrency($('#master-price').val()));
            const masterImageFiles = $('#master-images-upload')[0].files;
            if (masterImageFiles.length > 0) {
                for (let i = 0; i < masterImageFiles.length; i++) {
                    formData.append('master_images[]', masterImageFiles[i]);
                }
            }
            const existingImages = [];
            $('#master-images-preview-container .group').each(function() {
                const existingUrl = $(this).data('existing-image');
                if (existingUrl) {
                    existingImages.push(existingUrl);
                }
            });
            formData.append('existing_images_json', JSON.stringify(existingImages));
            $('.variant-row').each(function(index) {
                const variantId = $(this).find('.variant-id').val();
                const variantImageFile = $(this).find('.variant-image-upload')[0].files[0];
                formData.append(`variants[${index}][id]`, variantId ? parseInt(variantId) : 0);
                formData.append(`variants[${index}][color]`, $(this).find('.variant-color').val());
                formData.append(`variants[${index}][ram]`, $(this).find('.variant-ram').val());
                formData.append(`variants[${index}][price]`, parseCurrency($(this).find('.variant-price').val()) || 0);
                if (variantImageFile) {
                    formData.append(`variants[${index}][image]`, variantImageFile);
                }
            });
            
            $.ajax({
                url: url,
                method: method,
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    loadProductsData();
                    closeModal(productModal);
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Data produk berhasil disimpan!', showConfirmButton: false, timer: 3000 });
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        let errorMessage = 'Perbaiki error berikut:\n\n';
                        Object.values(errors).forEach(error => {
                            errorMessage += `• ${Array.isArray(error) ? error.join('\n• ') : error}\n`;
                        });
                        Swal.fire('Error', errorMessage, 'error');
                    } else {
                        Swal.fire('Error', 'Gagal menyimpan produk. Silakan coba lagi.', 'error');
                    }
                }
            });
        });

        $('.close-modal').on('click', function() { 
            closeModal($(this).closest('.modal-overlay')); 
        });

        loadProductsData();
    });
    </script>
</body>
</html>

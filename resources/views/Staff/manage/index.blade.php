<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manajemen Stok | CELVION</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Assets --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css" rel="stylesheet" />
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; }
        .variant-tag { 
            padding: 4px 8px; 
            border-radius: 6px; 
            font-size: 0.75rem; 
            font-weight: 500; 
            display: inline-flex; 
            align-items: center;
            margin: 2px; 
            border: 1px solid transparent;
        }

        /* [BARU] Warna untuk Stok Staff (Draft) */
        .variant-tag-draft-aman { background-color: #dcfce7; color: #166534; border-color: #4ade80; }
        .variant-tag-draft-menipis { background-color: #fef3c7; color: #92400e; border-color: #f59e0b; }
        .variant-tag-draft-habis { background-color: #fee2e2; color: #991b1b; border-color: #ef4444; }

        /* [BARU] Warna untuk Stok Admin (Published) */
        .variant-tag-published-aman { background-color: #e0e7ff; color: #3730a3; border-color: #818cf8; }
        .variant-tag-published-menipis { background-color: #fef3c7; color: #92400e; border-color: #f59e0b; }
        .variant-tag-published-habis { background-color: #fee2e2; color: #991b1b; border-color: #ef4444; }
        
        .status-badge { padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; text-transform: capitalize; }
        .status-baru { background-color: #dcfce7; color: #166534; }
        .status-second { background-color: #fef9c3; color: #854d0e; }
        .modal-overlay { transition: opacity 0.3s ease; }
        .modal-content { transition: transform 0.3s ease, opacity 0.3s ease; }
        
        .modal-scroll-container { display: flex; flex-direction: column; max-height: 85vh; height: 85vh; }
        .modal-scroll-header { flex-shrink: 0; }
        .modal-scroll-body { flex: 1; overflow-y: auto; min-height: 0; }
        .modal-scroll-footer { flex-shrink: 0; }
        
        .modal-scroll-body::-webkit-scrollbar { width: 6px; }
        .modal-scroll-body::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 3px; }
        .modal-scroll-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="relative min-h-screen md:flex">
        
        @include('staff.partials.sidebar')

        <div class="flex-1 md:ml-64 flex flex-col min-w-0">
            <main class="flex-grow p-4 sm:p-6 lg:p-8">
                
                @include('staff.partials.header', [
                    'title' => 'Manajemen Stok Gudang',
                    'subtitle' => 'Kelola stok produk dan varian di cabang Anda'
                ])

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center justify-between">
                        <div><p class="text-gray-500 font-medium">Total Produk</p><h3 id="total-produk" class="text-3xl font-bold text-gray-800 mt-1">{{ $summary['totalProduk'] }}</h3></div>
                        <div class="bg-blue-100 text-blue-600 p-4 rounded-full text-2xl"><i class="bi bi-box-seam"></i></div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center justify-between">
                        <div><p class="text-gray-500 font-medium">Total Varian</p><h3 id="total-varian" class="text-3xl font-bold text-gray-800 mt-1">{{ $summary['totalVarian'] }}</h3></div>
                        <div class="bg-green-100 text-green-600 p-4 rounded-full text-2xl"><i class="bi bi-stack"></i></div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center justify-between">
                        <div><p class="text-gray-500 font-medium">Stok Menipis (Draft)</p><h3 id="stok-menipis" class="text-3xl font-bold text-gray-800 mt-1">{{ $summary['stokMenipis'] }}</h3></div>
                        <div class="bg-amber-100 text-amber-600 p-4 rounded-full text-2xl"><i class="bi bi-exclamation-triangle"></i></div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center justify-between">
                        <div><p class="text-gray-500 font-medium">Stok Habis (Draft)</p><h3 id="stok-habis" class="text-3xl font-bold text-gray-800 mt-1">{{ $summary['stokHabis'] }}</h3></div>
                        <div class="bg-red-100 text-red-600 p-4 rounded-full text-2xl"><i class="bi bi-x-octagon"></i></div>
                    </div>
                </div>
                
                <div class="bg-white p-4 md:p-6 rounded-2xl shadow-lg">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
                        <h4 class="text-xl font-semibold text-gray-700">Daftar Stok Cabang</h4>
                        <div class="flex flex-col md:flex-row gap-3 items-center w-full md:w-auto">
                            <select id="categoryFilter" class="w-full md:w-auto px-3 py-2 border rounded-lg text-sm">
                                <option value="all">Semua Kategori</option>
                                <option value="Handphone">Handphone</option>
                                <option value="Aksesori">Aksesori</option>
                            </select>
                            <select id="statusFilter" class="w-full md:w-auto px-3 py-2 border rounded-lg text-sm">
                                <option value="all">Semua Status</option>
                                <option value="Baru">Baru</option>
                                <option value="Second">Second</option>
                            </select>
                            <button id="add-stock-btn" class="w-full md:w-auto flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg bg-indigo-600 text-white shadow-md hover:bg-indigo-700 transition">
                                <i class="bi bi-plus-circle-fill"></i><span>Tambah Stok</span>
                            </button>
                        </div>
                    </div>

                    <table id="productTable" class="w-full text-sm" style="width:100%">
                        <thead class="bg-gray-50 text-gray-600 uppercase">
                            <tr>
                                <th class="p-4 text-left">Produk</th>
                                <th class="p-4 text-center">Status</th>
                                <th class="p-4 text-left">Stok Staff (Draft)</th>
                                <th class="p-4 text-left">Stok Admin (Published)</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr data-product-id="{{ $product->id }}" data-category="{{ $product->category }}" data-status="{{ $product->status }}">
                                <td class="p-4 font-medium text-gray-800">
                                    {{-- [DIUBAH] Menghapus foto dari tabel --}}
                                    <span>{{ $product->name }}</span>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="status-badge status-{{ strtolower($product->status) }}">
                                        {{ $product->status }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($product->draftVariants as $variant)
                                            @php
                                                $stockClass = 'variant-tag-draft-aman'; // Default: Hijau
                                                if ($variant->stock === 0) $stockClass = 'variant-tag-draft-habis'; // Merah
                                                elseif ($variant->stock <= 5) $stockClass = 'variant-tag-draft-menipis'; // Kuning
                                            @endphp
                                            <span class="variant-tag {{ $stockClass }}">
                                                {{ $variant->color }}{{ $variant->ram ? ' / ' . $variant->ram : '' }} - 
                                                @if($variant->stock === 0) <b>Stok Habis</b>
                                                @elseif($variant->stock <= 5) <b>Stok Menipis: {{ $variant->stock }}</b>
                                                @else Stok: {{ $variant->stock }}
                                                @endif
                                            </span>
                                        @empty
                                            <span class="text-xs text-gray-400 italic">Tidak ada stok draft</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="p-4">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($product->publishedVariants as $variant)
                                             @php
                                                $stockClass = 'variant-tag-published-aman'; // Default: Biru
                                                if ($variant->stock === 0) $stockClass = 'variant-tag-published-habis'; // Merah
                                                elseif ($variant->stock <= 5) $stockClass = 'variant-tag-published-menipis'; // Kuning
                                            @endphp
                                            <span class="variant-tag {{ $stockClass }}">
                                                {{ $variant->color }}{{ $variant->ram ? ' / ' . $variant->ram : '' }} - Stok: {{ $variant->stock }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-gray-400 italic">Belum ada stok di admin</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="p-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <button class="detail-product-btn w-9 h-9 flex items-center justify-center rounded-md bg-sky-500 text-white hover:bg-sky-600 transition"
                                                data-product-id="{{ $product->id }}" title="Lihat Detail Stok">
                                            <i class="bi bi-eye-fill"></i>
                                        </button>
                                        <button class="edit-product-btn w-9 h-9 flex items-center justify-center rounded-md bg-amber-500 text-white hover:bg-amber-600 transition" 
                                                data-product-id="{{ $product->id }}"
                                                data-product-name="{{ $product->name }}" title="Edit Stok Draft">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        <button class="delete-product-btn w-9 h-9 flex items-center justify-center rounded-md bg-red-500 text-white hover:bg-red-600 transition" 
                                                data-product-id="{{ $product->id }}"
                                                data-product-name="{{ $product->name }}" title="Hapus Produk">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    {{-- Modal Tambah Stok --}}
    <div id="stock-modal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden modal-overlay opacity-0">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl modal-content transform scale-95 opacity-0 modal-scroll-container">
            <form id="stock-form" class="flex flex-col h-full">
                <div class="modal-scroll-header flex justify-between items-center p-5 border-b bg-white rounded-t-2xl">
                    <h3 class="text-xl font-bold">Tambah Stok Baru</h3>
                    <button type="button" class="close-modal p-2 hover:bg-gray-100 rounded-lg transition">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-scroll-body p-6 space-y-5">
                    <div>
                        <label class="block mb-3 text-sm font-medium">Jenis Produk</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-indigo-300 transition product-type-label">
                                <input type="radio" name="product_type" value="handphone" class="hidden product-type-radio" checked>
                                <div class="flex items-center gap-3 w-full">
                                    <div class="w-6 h-6 border-2 border-gray-300 rounded-full flex items-center justify-center product-type-indicator">
                                        <div class="w-3 h-3 bg-indigo-600 rounded-full hidden"></div>
                                    </div>
                                    <div>
                                        <div class="font-medium">Handphone</div>
                                        <div class="text-sm text-gray-500">Dengan varian warna & RAM</div>
                                    </div>
                                </div>
                            </label>
                            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-indigo-300 transition product-type-label">
                                <input type="radio" name="product_type" value="aksesori" class="hidden product-type-radio">
                                <div class="flex items-center gap-3 w-full">
                                    <div class="w-6 h-6 border-2 border-gray-300 rounded-full flex items-center justify-center product-type-indicator">
                                        <div class="w-3 h-3 bg-indigo-600 rounded-full hidden"></div>
                                    </div>
                                    <div>
                                        <div class="font-medium">Aksesori</div>
                                        <div class="text-sm text-gray-500">Hanya warna & stok</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block mb-1 text-sm font-medium">Pilih Produk</label>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="radio" name="product_selection" value="existing" checked class="mr-3 product-selection-radio">
                                    <span>Pilih dari produk yang sudah ada</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="product_selection" value="new" class="mr-3 product-selection-radio">
                                    <span>Buat produk baru</span>
                                </label>
                            </div>
                        </div>
                        <div id="existing-product-fields">
                            <select id="product_id" name="product_id" class="w-full px-3 py-2 border rounded-lg">
                                <option value="">-- Pilih Produk --</option>
                                @foreach($allProducts as $product)
                                    <option value="{{ $product->id }}" data-category="{{ $product->category }}">
                                        {{ $product->name }} ({{ $product->category }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div id="new-product-fields" class="hidden space-y-3 border-t pt-4">
                            <div>
                                <label class="block mb-1 text-sm font-medium">Nama Produk Baru</label>
                                <input type="text" name="new_product_name" class="w-full px-3 py-2 border rounded-lg" placeholder="Contoh: iPhone 16 atau Charger 25W">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium">Kategori</label>
                                <select name="new_product_category" class="w-full px-3 py-2 border rounded-lg">
                                    <option value="Handphone">Handphone</option>
                                    <option value="Aksesori">Aksesori</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="border-t pt-5">
                        <div class="flex justify-between items-center mb-3">
                            <label class="block font-bold text-slate-700">Varian & Stok</label>
                            <button type="button" id="add-variant-btn" class="flex items-center gap-2 px-3 py-1.5 text-xs font-semibold rounded-lg bg-indigo-100 text-indigo-700 hover:bg-indigo-200">
                                <i class="bi bi-plus-circle"></i>Tambah Varian
                            </button>
                        </div>
                        <div id="variants-container" class="space-y-3"></div>
                    </div>
                </div>
                <div class="modal-scroll-footer p-5 border-t bg-gray-50 flex justify-end gap-3 rounded-b-2xl">
                    <button type="button" class="close-modal px-5 py-2 bg-gray-200 rounded-lg font-semibold hover:bg-gray-300">Batal</button>
                    <button type="submit" id="submit-btn" class="px-5 py-2 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700">Simpan Stok</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Stok --}}
    <div id="edit-modal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden modal-overlay opacity-0">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl modal-content transform scale-95 opacity-0 modal-scroll-container">
            <form id="edit-form" class="flex flex-col h-full">
                <input type="hidden" id="edit-product-id">
                <div class="modal-scroll-header flex justify-between items-center p-5 border-b bg-white rounded-t-2xl">
                    <h3 class="text-xl font-bold">Edit Stok - <span id="edit-product-name"></span></h3>
                    <button type="button" class="close-edit-modal p-2 hover:bg-gray-100 rounded-lg transition">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-scroll-body p-6 space-y-5">
                    <div id="edit-variants-container"></div>
                </div>
                <div class="modal-scroll-footer p-5 border-t bg-gray-50 flex justify-end gap-3 rounded-b-2xl">
                    <button type="button" class="close-edit-modal px-5 py-2 bg-gray-200 rounded-lg font-semibold hover:bg-gray-300">Tutup</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal Detail Produk -->
    <div id="detail-modal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden modal-overlay opacity-0">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl modal-content transform scale-95 opacity-0 modal-scroll-container">
            <div class="modal-scroll-header flex justify-between items-center p-5 border-b bg-white rounded-t-2xl">
                <h3 class="text-xl font-bold">Detail Stok Produk</h3>
                <button type="button" class="close-detail-modal p-2 hover:bg-gray-100 rounded-lg transition">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-scroll-body p-6">
                <div class="flex items-center gap-4 mb-6">
                    <div id="detail-product-image" class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0"></div>
                    <div>
                        <h2 id="detail-product-name" class="text-2xl font-bold text-gray-800"></h2>
                        <div class="flex items-center gap-2 mt-1">
                            <p id="detail-product-category" class="text-sm text-gray-500"></p>
                            <span id="detail-product-status" class="status-badge"></span>
                        </div>
                    </div>
                </div>
                <div id="detail-variants-container" class="space-y-4">
                </div>
            </div>
            <div class="modal-scroll-footer p-5 border-t bg-gray-50 flex justify-end gap-3 rounded-b-2xl">
                <button type="button" class="close-detail-modal px-5 py-2 bg-gray-200 rounded-lg font-semibold hover:bg-gray-300">Tutup</button>
            </div>
        </div>
    </div>

    {{-- Template Varian Handphone --}}
    <div id="variant-template-handphone" class="hidden">
        <div class="variant-row bg-slate-50 p-4 rounded-lg border relative">
            <button type="button" class="remove-variant-btn absolute top-3 right-3 w-7 h-7 flex items-center justify-center rounded-lg bg-red-100 text-red-600 hover:bg-red-200">
                <i class="bi bi-x"></i>
            </button>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-medium mb-1">Warna</label>
                    <input type="text" class="variant-color w-full px-3 py-2 border rounded-md text-sm" placeholder="Hitam, Putih, dll" required>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">RAM/ROM</label>
                    <input type="text" class="variant-ram w-full px-3 py-2 border rounded-md text-sm" placeholder="8/256GB" required>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">Stok Masuk</label>
                    <input type="number" class="variant-stock w-full px-3 py-2 border rounded-md text-sm" placeholder="10" min="1" required>
                </div>
            </div>
        </div>
    </div>

    {{-- Template Varian Aksesori --}}
    <div id="variant-template-aksesori" class="hidden">
        <div class="variant-row bg-slate-50 p-4 rounded-lg border relative">
            <button type="button" class="remove-variant-btn absolute top-3 right-3 w-7 h-7 flex items-center justify-center rounded-lg bg-red-100 text-red-600 hover:bg-red-200">
                <i class="bi bi-x"></i>
            </button>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium mb-1">Warna/Varian</label>
                    <input type="text" class="variant-color w-full px-3 py-2 border rounded-md text-sm" placeholder="Hitam, Putih, 25W, dll" required>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">Stok Masuk</label>
                    <input type="number" class="variant-stock w-full px-3 py-2 border rounded-md text-sm" placeholder="10" min="1" required>
                </div>
            </div>
        </div>
    </div>
    
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    const table = $('#productTable').DataTable({
        responsive: true,
        language: {
            search: "Cari:",
            lengthMenu: "Tampil _MENU_ entri",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            zeroRecords: "Tidak ada data yang ditemukan",
            infoEmpty: "Menampilkan 0 dari 0 entri",
            infoFiltered: "(disaring dari _MAX_ total entri)",
            paginate: { first: "Awal", last: "Akhir", next: "Berikutnya", previous: "Sebelumnya" },
        }
    });

    const stockModal = $('#stock-modal');
    const editModal = $('#edit-modal');
    const detailModal = $('#detail-modal');
    
    function openModal(modal) { 
        modal.removeClass('hidden'); 
        setTimeout(() => modal.removeClass('opacity-0').find('.modal-content').removeClass('opacity-0 scale-95'), 10); 
        $('body').addClass('overflow-hidden'); 
    }
    
    function closeModal(modal) { 
        modal.addClass('opacity-0').find('.modal-content').addClass('opacity-0 scale-95'); 
        setTimeout(() => { modal.addClass('hidden'); $('body').removeClass('overflow-hidden'); }, 300); 
    }

    function applyFilters() {
        const category = $('#categoryFilter').val();
        const status = $('#statusFilter').val();

        $.fn.dataTable.ext.search.pop(); 
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                const rowNode = $(table.row(dataIndex).node());
                const rowCategory = rowNode.data('category');
                const rowStatus = rowNode.data('status');

                const categoryMatch = (category === 'all' || rowCategory === category);
                const statusMatch = (status === 'all' || rowStatus === status);
                
                return categoryMatch && statusMatch;
            }
        );
        table.draw();
    }

    $('#categoryFilter').on('change', applyFilters);
    $('#statusFilter').on('change', applyFilters);

    $('.product-type-radio').on('change', function() {
        $('.product-type-label').removeClass('border-indigo-500 bg-indigo-50');
        $(this).closest('.product-type-label').addClass('border-indigo-500 bg-indigo-50');
        $('.product-type-indicator').find('div').addClass('hidden');
        $(this).closest('.product-type-label').find('.product-type-indicator div').removeClass('hidden');
        refreshVariantsContainer();
    });

    $('.product-selection-radio').on('change', function() {
        const selection = $(this).val();
        if (selection === 'existing') {
            $('#existing-product-fields').show();
            $('#new-product-fields').hide();
        } else {
            $('#existing-product-fields').hide();
            $('#new-product-fields').show();
        }
    });

    function addVariantRow() {
        const productType = $('input[name="product_type"]:checked').val();
        const templateId = productType === 'handphone' ? '#variant-template-handphone' : '#variant-template-aksesori';
        const newRowHtml = $(templateId).html();
        $('#variants-container').append(newRowHtml);
        renameVariantInputs();
    }
    
    function renameVariantInputs() {
        $('#variants-container .variant-row').each(function(index) {
            $(this).find('.variant-color').attr('name', `variants[${index}][color]`);
            $(this).find('.variant-ram').attr('name', `variants[${index}][ram]`);
            $(this).find('.variant-stock').attr('name', `variants[${index}][stock]`);
        });
    }

    function refreshVariantsContainer() {
        $('#variants-container').empty();
        addVariantRow();
    }

    $('#add-stock-btn').on('click', function() {
        $('#stock-form')[0].reset();
        $('#variants-container').empty();
        $('.product-type-label').removeClass('border-indigo-500 bg-indigo-50');
        $('.product-type-label:first').addClass('border-indigo-500 bg-indigo-50');
        $('.product-type-indicator div').addClass('hidden');
        $('.product-type-label:first').find('.product-type-indicator div').removeClass('hidden');
        $('input[name="product_type"][value="handphone"]').prop('checked', true);
        $('input[name="product_selection"][value="existing"]').prop('checked', true);
        $('#existing-product-fields').show();
        $('#new-product-fields').hide();
        addVariantRow();
        openModal(stockModal);
    });

    $('#add-variant-btn').on('click', addVariantRow);
    
    $(document).on('click', '.remove-variant-btn', function() { 
        if ($('#variants-container .variant-row').length > 1) {
            $(this).closest('.variant-row').remove(); 
            renameVariantInputs();
        } else {
            Swal.fire('Peringatan', 'Minimal harus ada satu varian.', 'warning');
        }
    });

    $('.close-modal').on('click', function() { closeModal(stockModal); });
    $('.close-edit-modal').on('click', function() { closeModal(editModal); });
    $('.close-detail-modal').on('click', function() { closeModal(detailModal); });

    $(document).on('click', '.detail-product-btn', function() {
        const productId = $(this).data('product-id');
        const productsData = @json($products);
        const product = productsData.find(p => p.id == productId);

        if (product) {
            $('#detail-product-name').text(product.name);
            $('#detail-product-category').text(product.category);

            const statusBadge = $('#detail-product-status');
            statusBadge.text(product.status)
                       .removeClass('status-baru status-second')
                       .addClass(`status-${String(product.status).toLowerCase()}`);

            if (product.image) {
                $('#detail-product-image').html(`<img src="/storage/${product.image}" class="w-full h-full object-cover rounded-lg">`);
            } else {
                $('#detail-product-image').html('<i class="bi bi-image text-3xl text-gray-400"></i>');
            }

            const variantsContainer = $('#detail-variants-container').empty();
            let hasVariants = false;

            if (product.draft_variants && product.draft_variants.length > 0) {
                hasVariants = true;
                variantsContainer.append('<h5 class="text-sm font-bold text-gray-600 uppercase tracking-wider">Stok Staff (Draft)</h5>');
                product.draft_variants.forEach(variant => {
                    const statusClass = 'status-draft';
                    const variantHtml = `
                        <div class="flex items-center justify-between gap-4 p-3 border rounded-lg bg-gray-50">
                            <div>
                                <div class="font-semibold text-gray-800">${variant.color} ${variant.ram ? '/ ' + variant.ram : ''}</div>
                                <div class="text-sm text-gray-500">Stok: <span class="font-bold">${variant.stock}</span></div>
                            </div>
                            <div>
                                <span class="status-badge ${statusClass}">${variant.status}</span>
                            </div>
                        </div>
                    `;
                    variantsContainer.append(variantHtml);
                });
            }

            if (product.published_variants && product.published_variants.length > 0) {
                hasVariants = true;
                variantsContainer.append('<h5 class="text-sm font-bold text-gray-600 uppercase tracking-wider mt-4">Stok Admin (Published)</h5>');
                product.published_variants.forEach(variant => {
                    const statusClass = 'status-published';
                    const variantHtml = `
                        <div class="flex items-center justify-between gap-4 p-3 border rounded-lg bg-gray-50">
                            <div>
                                <div class="font-semibold text-gray-800">${variant.color} ${variant.ram ? '/ ' + variant.ram : ''}</div>
                                <div class="text-sm text-gray-500">Stok: <span class="font-bold">${variant.stock}</span></div>
                            </div>
                            <div>
                                <span class="status-badge ${statusClass}">${variant.status}</span>
                            </div>
                        </div>
                    `;
                    variantsContainer.append(variantHtml);
                });
            }

            if (!hasVariants) {
                variantsContainer.html('<p class="text-center text-gray-500">Tidak ada varian untuk produk ini.</p>');
            }
            openModal(detailModal);
        }
    });

    $(document).on('click', '.edit-product-btn', function() {
        const productId = $(this).data('product-id');
        const productName = $(this).data('product-name');
        
        $('#edit-product-id').val(productId);
        $('#edit-product-name').text(productName);
        $('#edit-variants-container').html('<p class="text-center text-gray-500">Memuat data varian...</p>');
        openModal(editModal);

        $.ajax({
            url: `/staff/manage/products/${productId}/edit`,
            type: 'GET',
            success: function(response) {
                const container = $('#edit-variants-container');
                container.empty();
                
                if (response.variants && response.variants.length > 0) {
                    response.variants.forEach(variant => {
                        const variantHtml = `
                        <div class="edit-variant-row bg-slate-50 p-4 rounded-lg border" data-variant-id="${variant.id}">
                             <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                                 <div class="font-medium text-sm">
                                     ${variant.color} ${variant.ram ? '/ ' + variant.ram : ''}
                                 </div>
                                 <div>
                                     <label class="block text-xs font-medium mb-1">Stok Baru</label>
                                     <input type="number" class="edit-variant-stock w-full px-3 py-2 border rounded-md text-sm" value="${variant.stock}" min="0">
                                 </div>
                                 <div class="flex justify-end gap-2">
                                      <button type="button" class="update-stock-btn px-3 py-2 bg-green-100 text-green-700 rounded-lg text-sm font-semibold hover:bg-green-200" data-variant-id="${variant.id}">Update</button>
                                 </div>
                             </div>
                        </div>`;
                        container.append(variantHtml);
                    });
                } else {
                    container.html('<p class="text-center text-gray-500">Tidak ada varian draft untuk diedit.</p>');
                }
            },
            error: function() {
                $('#edit-variants-container').html('<p class="text-center text-red-500">Gagal memuat data.</p>');
            }
        });
    });

    $(document).on('click', '.update-stock-btn', function() {
        const btn = $(this);
        const variantId = btn.data('variant-id');
        const stock = btn.closest('.edit-variant-row').find('.edit-variant-stock').val();
        
        btn.prop('disabled', true).html('...');
        
        $.ajax({
            url: `/staff/manage/variants/${variantId}`,
            type: 'PUT',
            data: { stock: stock },
            success: function(response) {
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.success, timer: 1500, showConfirmButton: false }).then(() => location.reload());
            },
            error: function(xhr) {
                Swal.fire('Gagal!', xhr.responseJSON.error || 'Terjadi kesalahan', 'error');
                btn.prop('disabled', false).text('Update');
            }
        });
    });
    
    $(document).on('click', '.delete-variant-btn', function() {
        const variantId = $(this).data('variant-id');
        const variantName = $(this).data('variant-name');
        
        Swal.fire({
            title: 'Hapus Varian?',
            html: `Anda yakin ingin menghapus varian "<strong>${variantName}</strong>"? Stok akan dihapus permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/staff/manage/variants/${variantId}`,
                    type: 'DELETE',
                    success: function(response) {
                        Swal.fire('Berhasil!', response.success, 'success').then(() => location.reload());
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal!', xhr.responseJSON.error || 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        });
    });

    $(document).on('click', '.delete-product-btn', function() {
        const productId = $(this).data('product-id');
        const productName = $(this).data('product-name');

        Swal.fire({
            title: 'Hapus Produk?',
            html: `Anda akan menghapus produk <strong>${productName}</strong> dan SEMUA variannya dari cabang ini.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus Semua!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/staff/manage/products/${productId}`,
                    type: 'DELETE',
                    success: function(response) {
                        Swal.fire('Berhasil!', response.success, 'success').then(() => location.reload());
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal!', xhr.responseJSON.error || 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        });
    });

    $('#stock-form').on('submit', function(e) {
        e.preventDefault();
        const submitBtn = $('#submit-btn');
        submitBtn.prop('disabled', true).html('Menyimpan...');

        $.ajax({
            url: "{{ route('staff.manage.store') }}",
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                closeModal(stockModal);
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.success,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => location.reload());
            },
            error: function(xhr) {
                let errorMsg = 'Terjadi kesalahan. Silakan coba lagi.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = '<ul>';
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        errorMsg += `<li>${value[0]}</li>`;
                    });
                    errorMsg += '</ul>';
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    html: errorMsg
                });
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('Simpan Stok');
            }
        });
    });

    $('.product-type-label:first').addClass('border-indigo-500 bg-indigo-50');
    $('.product-type-label:first').find('.product-type-indicator div').removeClass('hidden');
});
</script>
</body>
</html>


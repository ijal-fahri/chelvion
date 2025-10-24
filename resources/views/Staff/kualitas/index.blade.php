<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manajemen Stok Second | CELVION</title>

    {{-- [BARU] CSRF Token untuk AJAX --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; }
        .modal-overlay { transition: opacity 0.3s ease; }
        .modal-content { transition: transform 0.3s ease, opacity 0.3s ease; }
        
        #stockTable_wrapper .dt-search input, #stockTable_wrapper .dt-length select { background-color: white !important; color: #1e293b !important; border: 1px solid #cbd5e1 !important; border-radius: 0.5rem !important; padding: 0.5rem 0.75rem !important; transition: all 0.2s ease-in-out; outline: none; }
        #stockTable_wrapper .dt-search input:focus, #stockTable_wrapper .dt-length select:focus { border-color: #6366f1 !important; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2) !important; }
        #stockTable_wrapper .dt-search input { padding-left: 2.25rem !important; }
        #stockTable_wrapper .dt-paging .dt-paging-button.current { background: #4f46e5 !important; color: #ffffff !important; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1); }

        @media (max-width: 767px) {
            .mobile-card-view thead { display: none; }
            .mobile-card-view tbody, .mobile-card-view tr, .mobile-card-view td { display: block; width: 100%; }
            .mobile-card-view tr { background-color: white; margin-bottom: 1rem; border: 1px solid #e2e8f0; border-radius: 0.75rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); overflow: hidden; }
            .mobile-card-view td { padding: 0.75rem 1rem; border-bottom: 1px solid #f1f5f9; text-align: right; }
            .mobile-card-view td:last-child { border-bottom: none; background-color: #f8fafc; padding-top: 1rem; padding-bottom: 1rem;}
            .mobile-card-view td[data-label]::before { content: attr(data-label); float: left; font-weight: 600; color: #475569; }
            .mobile-card-view td[data-label="Produk"] { padding: 1rem; text-align: left; }
            .mobile-card-view td[data-label="Produk"]::before { display: none; }
            .mobile-card-view td[data-label="Aksi"] .flex { flex-direction: column; gap: 0.5rem !important; }
        }
    </style>
</head>
<body>
    <div class="relative min-h-screen md:flex">
        
        @include('staff.partials.sidebar')

        <div class="flex-1 md:ml-64 flex flex-col min-w-0">
            <main class="flex-grow p-4 sm:p-6 lg:p-8">
                
                @include('staff.partials.header', [
                    'title' => 'Manajemen Stok Second',
                    'subtitle' => 'Atur harga jual perangkat second berdasarkan hasil QC.'
                ])

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8" data-aos="fade-up">
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center justify-between">
                        <div><p class="text-gray-500 font-medium">Stok Masuk (Perlu QC)</p><h3 id="card-stock-in" class="text-3xl font-bold text-gray-800 mt-1">0</h3></div>
                        <div class="bg-amber-100 text-amber-600 p-4 rounded-full text-2xl"><i class="bi bi-box-arrow-in-right"></i></div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center justify-between">
                        <div><p class="text-gray-500 font-medium">Selesai Diproses</p><h3 id="card-processed" class="text-3xl font-bold text-gray-800 mt-1">0</h3></div>
                        <div class="bg-blue-100 text-blue-600 p-4 rounded-full text-2xl"><i class="bi bi-check2-all"></i></div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center justify-between">
                        <div><p class="text-gray-500 font-medium">Total Nilai Stok Masuk</p><h3 id="card-stock-value" class="text-3xl font-bold text-gray-800 mt-1">Rp0</h3></div>
                        <div class="bg-green-100 text-green-600 p-4 rounded-full text-2xl"><i class="bi bi-wallet2"></i></div>
                    </div>
                </div>
                
                <div class="bg-white p-4 md:p-6 rounded-2xl shadow-lg" data-aos="fade-up" data-aos-delay="100">
                    <h4 class="text-xl font-semibold text-gray-700 mb-4">Daftar Stok Perangkat Second</h4>
                    <table id="stockTable" class="w-full text-sm mobile-card-view" style="width:100%">
                        <thead class="bg-gray-50 text-gray-600 uppercase">
                            <tr>
                                <th class="p-4 text-left">Produk</th>
                                <th class="p-4 text-left">Harga Beli</th>
                                <th class="p-4 text-left">Biaya Perbaikan</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <div id="detail-modal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden modal-overlay opacity-0">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg modal-content transform scale-95 opacity-0 flex flex-col max-h-[90vh]">
            <div class="flex justify-between items-center p-5 border-b sticky top-0 bg-white z-10">
                <h3 class="text-xl font-bold text-gray-800">Detail Perangkat</h3>
                <button type="button" class="close-modal p-2 rounded-full hover:bg-gray-100"><i class="bi bi-x-lg text-xl"></i></button>
            </div>
            <div class="p-6 space-y-5 overflow-y-auto">
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Kondisi Fisik</h4>
                    <div id="detail-condition" class="flex items-center gap-2 text-sm p-3 bg-gray-100 text-gray-800 rounded-lg font-semibold"></div>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Kelengkapan</h4>
                    <div id="detail-completeness" class="flex items-center gap-2 text-sm p-3 bg-gray-100 text-gray-800 rounded-lg font-semibold"></div>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Hasil Pengecekan Kualitas (QC)</h4>
                    <div id="detail-qc-details" class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm"></div>
                </div>
            </div>
            <div class="p-5 border-t bg-gray-50 flex justify-end">
                <button type="button" class="close-modal px-5 py-2 bg-gray-200 text-gray-800 rounded-lg font-semibold hover:bg-gray-300">Tutup</button>
            </div>
        </div>
    </div>

    <div id="submit-admin-modal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden modal-overlay opacity-0">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg modal-content transform scale-95 opacity-0 flex flex-col max-h-[90vh]">
            <div class="flex justify-between items-center p-5 border-b sticky top-0 bg-white z-10">
                <h3 class="text-xl font-bold text-gray-800">Kirim untuk Penentuan Harga</h3>
                <button type="button" class="close-modal p-2 rounded-full hover:bg-gray-100"><i class="bi bi-x-lg text-xl"></i></button>
            </div>
            <div class="p-6 space-y-4 overflow-y-auto">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p id="admin-product-name" class="font-bold text-lg text-gray-800"></p>
                    <p id="admin-product-specs" class="text-sm text-gray-500"></p>
                </div>
                <div class="grid grid-cols-2 gap-4 text-center border-t pt-4">
                    <div><label class="block text-sm font-medium text-gray-500">Harga Beli</label><p id="admin-cost-price" class="text-lg font-bold text-gray-700 mt-1">Rp0</p></div>
                    <div><label class="block text-sm font-medium text-gray-500">Total Biaya Perbaikan</label><p id="admin-repair-cost" class="text-lg font-bold text-amber-600 mt-1">Rp0</p></div>
                </div>
                <div id="admin-repair-details-container" class="hidden">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Rincian Perbaikan:</label>
                    <div id="admin-repair-details" class="text-xs text-gray-600 bg-amber-50 p-3 rounded-lg space-y-1"></div>
                </div>
                <div>
                    <label for="admin-notes" class="block mb-1 font-medium">Catatan untuk Admin (Opsional)</label>
                    <textarea id="admin-notes" placeholder="Contoh: Perlu ganti casing, biaya tambahan estimasi Rp150.000" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" rows="3"></textarea>
                </div>
            </div>
            <div class="p-5 border-t bg-gray-50 flex justify-end gap-3 sticky bottom-0 z-10">
                <button type="button" class="close-modal px-5 py-2 bg-gray-200 text-gray-800 rounded-lg font-semibold hover:bg-gray-300">Batal</button>
                <button type="button" id="submit-to-admin-btn" class="px-5 py-2 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700">Kirim & Selesaikan</button>
            </div>
        </div>
    </div>
    
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script>
    $(document).ready(function() {
        AOS.init({ duration: 600, once: true, offset: 20 });

        // [DIUBAH] Setup CSRF Token untuk semua request AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let currentTradeInId = null;
        let table;

        const detailModal = $('#detail-modal');
        const submitAdminModal = $('#submit-admin-modal');
        const formatCurrency = (number) => number ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number) : 'Rp0';

        // [DIUBAH] Fungsi untuk mengambil data summary dari controller
        function loadSummaryCards() {
            $.ajax({
                url: "{{ route('staff.second.summary') }}",
                method: 'GET',
                success: function(data) {
                    $('#card-stock-in').text(data.stock_in_count || 0);
                    $('#card-processed').text(data.processed_count || 0);
                    $('#card-stock-value').text(formatCurrency(data.stock_in_value || 0));
                },
                error: function(xhr) {
                    console.error('Gagal memuat data summary:', xhr.responseText);
                }
            });
        }

        // [DIUBAH] Inisialisasi DataTables dengan AJAX dari controller
        table = $('#stockTable').DataTable({
            processing: true,
            serverSide: false, // Data dimuat sekali, lalu dihandle client-side
            ajax: {
                url: "{{ route('staff.second.data') }}",
                type: "GET",
                dataSrc: "data" 
            },
            dom: "<'flex flex-col md:flex-row items-center justify-between gap-4 mb-4'<'dt-length'l><'dt-search'f>>t<'flex flex-col md:flex-row items-center justify-between gap-4 mt-4'<'dt-info'i><'dt-paging'p>>",
            columns: [
                { 
                    data: 'product_name', 
                    render: (data, type, row) => `<div class="flex items-center gap-4"><img src="${row.image || 'https://placehold.co/80x80/e0f2fe/0891b2?text=QC'}" class="w-16 h-16 rounded-lg object-cover flex-shrink-0"><div><p class="font-bold text-base text-gray-800">${data}</p><p class="text-sm text-gray-500">${row.specs}</p></div></div>` 
                },
                { 
                    data: 'cost_price', 
                    render: data => `<p class="font-semibold text-gray-600">${formatCurrency(data)}</p>` 
                },
                { 
                    data: 'qc_details', 
                    render: (data) => {
                        if (!data || typeof data !== 'object') return `<p class="font-semibold text-gray-500">Belum di-QC</p>`;
                        
                        // Menghitung total biaya dari JSON
                        const totalRepairCost = Object.values(data).reduce((sum, item) => sum + (parseInt(item.cost) || 0), 0);
                        
                        if (totalRepairCost > 0) return `<p class="font-bold text-red-600">${formatCurrency(totalRepairCost)}</p>`;
                        return `<p class="font-semibold text-green-600">Aman</p>`;
                    }
                },
                { 
                    data: 'id', 
                    orderable: false, 
                    searchable: false, 
                    render: (data, type, row) => {
                        // Tombol dinamis berdasarkan status dari database
                        if (row.status === 'selesai') {
                            return `<div class="flex items-center justify-center gap-2">
                                        <span class="bg-blue-100 text-blue-800 text-sm font-bold px-4 py-2 rounded-full"><i class="bi bi-check-all"></i> Selesai</span>
                                        <button data-id="${data}" class="detail-btn w-10 h-10 flex items-center justify-center bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition"><i class="bi bi-eye-fill"></i></button>
                                    </div>`;
                        }
                        return `<div class="flex items-center justify-center gap-2">
                                    <button data-id="${data}" class="detail-btn flex-1 bg-gray-200 text-gray-800 font-bold py-2 px-3 rounded-lg hover:bg-gray-300 transition text-xs">Detail</button>
                                    <button data-id="${data}" class="submit-admin-btn-table flex-1 bg-indigo-600 text-white font-bold py-2 px-3 rounded-lg hover:bg-indigo-700 transition text-xs">Kirim ke Admin</button>
                                </div>`;
                    }
                }
            ],
            createdRow: (row, data) => { 
                $(row).find('td:eq(0)').attr('data-label', 'Produk'); 
                $(row).find('td:eq(1)').attr('data-label', 'Harga Beli'); 
                $(row).find('td:eq(2)').attr('data-label', 'Biaya Perbaikan'); 
                $(row).find('td:eq(3)').attr('data-label', 'Aksi'); 
            },
            language: { search: "", searchPlaceholder: "Cari produk...", lengthMenu: "Tampil _MENU_", info: "Menampilkan _START_ - _END_ dari _TOTAL_ data" }
        });
        
        $('#stockTable_wrapper .dt-search').addClass('relative').find('input').before('<i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>');

        const openModal = (modal) => { modal.removeClass('hidden'); setTimeout(() => modal.removeClass('opacity-0').find('.modal-content').removeClass('opacity-0 scale-95'), 10); $('body').addClass('overflow-hidden'); };
        const closeModal = (modal) => { modal.addClass('opacity-0').find('.modal-content').addClass('opacity-0 scale-95'); setTimeout(() => modal.addClass('hidden'), 300); $('body').removeClass('overflow-hidden'); };

        $('.close-modal').on('click', function() { closeModal($(this).closest('.modal-overlay')); });

        // [DIUBAH] Fungsi untuk mendapatkan data baris dari instance DataTables
        function getRowData(id) {
            return table.rows().data().toArray().find(p => p.id === id);
        }

        $('#stockTable tbody').on('click', '.detail-btn', function() {
            const product = getRowData($(this).data('id'));
            if(product) {
                $('#detail-condition').html(`<i class="bi bi-phone-fill text-xl mr-2"></i> ${product.condition}`);
                $('#detail-completeness').html(`<i class="bi bi-box-seam-fill text-xl mr-2"></i> ${product.completeness}`);
                const qcContainer = $('#detail-qc-details').empty();
                
                if (product.qc_details && typeof product.qc_details === 'object') {
                    for (const key in product.qc_details) {
                        const detail = product.qc_details[key];
                        const isNormal = !detail.cost || parseInt(detail.cost) === 0;
                        let content = isNormal 
                            ? `<i class="bi bi-check-circle-fill text-xl text-green-500"></i> <span class="text-green-700">${detail.status}</span>` 
                            : `<div class="flex items-center gap-2"><i class="bi bi-x-circle-fill text-xl text-red-500"></i><span class="font-semibold text-red-700">${detail.status}</span></div>`;
                        qcContainer.append(`<div class="flex justify-between items-center p-3 rounded-lg border bg-gray-50"><span class="font-medium text-gray-600">${key}</span>${content}</div>`);
                    }
                } else {
                    qcContainer.html('<p class="text-gray-500 col-span-2">Belum ada detail QC dari kasir.</p>');
                }
                openModal(detailModal);
            }
        });
        
        $('#stockTable tbody').on('click', '.submit-admin-btn-table', function() {
            currentTradeInId = $(this).data('id');
            const product = getRowData(currentTradeInId);
            if(product) {
                $('#admin-product-name').text(product.product_name);
                $('#admin-product-specs').text(product.specs);
                $('#admin-cost-price').text(formatCurrency(product.cost_price));
                $('#admin-notes').val(product.staff_notes || '');
                
                let totalRepairCost = 0;
                let repairs = [];
                if(product.qc_details && typeof product.qc_details === 'object') {
                     for(const key in product.qc_details) {
                        const detail = product.qc_details[key];
                        const cost = parseInt(detail.cost) || 0;
                        if (cost > 0) {
                            totalRepairCost += cost;
                            repairs.push({ item: key, status: detail.status, cost: cost });
                        }
                    }
                }
                $('#admin-repair-cost').text(formatCurrency(totalRepairCost));

                const repairDetailsContainer = $('#admin-repair-details-container');
                const repairDetailsList = $('#admin-repair-details').empty();
                
                if (repairs.length > 0) {
                    repairs.forEach(repair => { repairDetailsList.append(`<div>- ${repair.item} (${repair.status}): <b>${formatCurrency(repair.cost)}</b></div>`); });
                    repairDetailsContainer.removeClass('hidden');
                } else {
                    repairDetailsContainer.addClass('hidden');
                }
                
                openModal(submitAdminModal);
            }
        });

        $('#submit-to-admin-btn').on('click', function() {
            const product = getRowData(currentTradeInId);
            if (product) {
                Swal.fire({
                    title: 'Kirim ke Admin?',
                    text: `Data untuk ${product.product_name} akan diproses dan dibuatkan stok draft.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Kirim!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // [DIUBAH] Mengirim data ke controller
                        $.ajax({
                            url: `/staff/second-stock/${currentTradeInId}/submit`,
                            method: 'POST',
                            data: {
                                staff_notes: $('#admin-notes').val()
                            },
                            success: function(response) {
                                closeModal(submitAdminModal);
                                Swal.fire('Berhasil!', response.success, 'success');
                                table.ajax.reload(); // Muat ulang data tabel dari server
                                loadSummaryCards();  // Update kartu summary
                            },
                            error: function(xhr) {
                                const errorMsg = xhr.responseJSON?.error || 'Terjadi kesalahan. Silakan coba lagi.';
                                Swal.fire('Gagal!', errorMsg, 'error');
                            }
                        });
                    }
                });
            }
        });

        // [DIUBAH] Memanggil fungsi untuk memuat kartu summary saat halaman siap
        loadSummaryCards();
    });
    </script>
</body>
</html>


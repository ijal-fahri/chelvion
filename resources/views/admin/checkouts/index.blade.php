<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Data Pesanan | Admin Panel</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Tailwind CSS & jQuery --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- Ikon & Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    
    {{-- DataTables CSS & SweetAlert2 --}}
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #6366f1; border-radius: 10px; }
        
        .status-badge { padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; text-transform: capitalize; }
        .status-pending { background-color: #fef9c3; color: #854d0e; }
        .status-diproses { background-color: #dbeafe; color: #1e40af; }
        .status-dikirim { background-color: #cffafe; color: #0891b2; }
        .status-selesai { background-color: #dcfce7; color: #166534; }
        .status-dibatalkan { background-color: #fee2e2; color: #991b1b; }

        #checkoutTable_wrapper .dt-search input, #checkoutTable_wrapper .dt-length select, #status-filter {
            background-color: white !important; color: #1e293b !important; border: 1px solid #cbd5e1 !important;
            border-radius: 0.5rem !important; padding: 0.5rem 0.75rem !important; outline: none;
        }
        #checkoutTable_wrapper .dt-search input:focus, #checkoutTable_wrapper .dt-length select:focus, #status-filter:focus {
            border-color: #6366f1 !important; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2) !important;
        }
        #checkoutTable_wrapper .dt-search { position: relative; }
        #checkoutTable_wrapper .dt-search input { padding-left: 2.25rem !important; }
        #checkoutTable_wrapper .dt-paging .dt-paging-button.current { background: #4f46e5 !important; color: #ffffff !important; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }

        .modal-overlay { transition: opacity 0.3s ease; }
        /* [DIUBAH] Mengganti transisi dengan keyframe animasi */
        @keyframes modal-in { from { opacity: 0; transform: translateY(-20px) scale(0.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
        .animate-modal-in { animation: modal-in 0.3s ease-out forwards; }
        
        @media (max-width: 767px) {
            .mobile-card-view thead { display: none; }
            .mobile-card-view tr { background-color: white; margin-bottom: 1rem; border: 1px solid #e2e8f0; border-radius: 0.75rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); overflow: hidden; display: block; }
            .mobile-card-view td { padding: 0.75rem 1rem; border-bottom: 1px solid #f1f5f9; text-align: right; display: flex; justify-content: space-between; align-items: center; }
            .mobile-card-view td:last-child { border-bottom: none; }
            .mobile-card-view td[data-label]::before { content: attr(data-label); font-weight: 600; color: #475569; text-align: left; }
        }
    </style>
</head>

<body>
    <div class="relative min-h-screen md:flex">
        
        @include('admin.partials.sidebar')

        <div class="flex-1 md:ml-64 flex flex-col">
            <main class="flex-grow p-4 sm:p-6 lg:p-8 animate-fade-in">
                
                @include('admin.partials.header', [
                    'title' => 'Data Pesanan',
                    'subtitle' => 'Kelola semua pesanan yang masuk dari pelanggan.'
                ])

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center justify-between transition-transform duration-300 hover:-translate-y-1">
                        <div><p class="text-gray-500">Total Pesanan</p><h3 id="summary-total" class="text-3xl font-bold text-gray-800 mt-1">0</h3></div>
                        <div class="bg-blue-100 text-blue-600 p-4 rounded-full text-2xl"><i class="bi bi-receipt-cutoff"></i></div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center justify-between transition-transform duration-300 hover:-translate-y-1">
                        <div><p class="text-gray-500">Pendapatan Hari Ini</p><h3 id="summary-revenue" class="text-2xl font-bold text-gray-800 mt-1">Rp0</h3></div>
                        <div class="bg-green-100 text-green-600 p-4 rounded-full text-2xl"><i class="bi bi-cash-stack"></i></div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center justify-between transition-transform duration-300 hover:-translate-y-1">
                        <div><p class="text-gray-500">Pesanan Diproses</p><h3 id="summary-processing" class="text-3xl font-bold text-gray-800 mt-1">0</h3></div>
                        <div class="bg-purple-100 text-purple-600 p-4 rounded-full text-2xl"><i class="bi bi-arrow-repeat"></i></div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center justify-between transition-transform duration-300 hover:-translate-y-1">
                        <div><p class="text-gray-500">Pesanan Pending</p><h3 id="summary-pending" class="text-3xl font-bold text-gray-800 mt-1">0</h3></div>
                        <div class="bg-amber-100 text-amber-600 p-4 rounded-full text-2xl"><i class="bi bi-hourglass-split"></i></div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-lg">
                    <table id="checkoutTable" class="w-full text-sm mobile-card-view" style="width:100%">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-4 font-semibold text-gray-600 text-left">ID Pesanan</th>
                                <th class="p-4 font-semibold text-gray-600 text-left">Pelanggan</th>
                                <th class="p-4 font-semibold text-gray-600 text-left">Detail Pesanan</th>
                                <th class="p-4 font-semibold text-gray-600 text-left">Total</th>
                                <th class="p-4 font-semibold text-gray-600 text-center">Status</th>
                                <th class="p-4 font-semibold text-gray-600 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>
    
    <div id="detail-modal" class="hidden fixed inset-0 bg-black/60 z-50 flex justify-center items-center p-4 overflow-y-auto modal-overlay opacity-0">
        {{-- [DIUBAH] Menambahkan class `animate-modal-in` --}}
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl relative my-8 modal-content animate-modal-in">
             <div class="flex justify-between items-center p-5 border-b sticky top-0 bg-white z-10 rounded-t-2xl">
                <div><h3 class="text-xl font-bold text-gray-800">Detail Pesanan</h3><p id="modal-order-id" class="text-sm text-gray-500"></p></div>
                <button class="close-modal p-2 rounded-full hover:bg-gray-200"><i class="bi bi-x-lg text-xl text-gray-600"></i></button>
            </div>
            <div class="p-6 space-y-6 max-h-[60vh] overflow-y-auto">
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold text-gray-700 mb-2 border-b pb-2">Info Pelanggan</h4>
                        <div class="space-y-1 text-sm">
                            <p><strong class="w-24 inline-block">Nama:</strong> <span id="modal-customer-name"></span></p>
                            <p><strong class="w-24 inline-block">Telepon:</strong> <span id="modal-customer-phone"></span></p>
                            <p><strong class="w-24 inline-block">Email:</strong> <span id="modal-customer-email"></span></p>
                            <p><strong class="w-24 inline-block">Alamat:</strong> <span id="modal-shipping-address" class="text-gray-600"></span></p>
                        </div>
                    </div>
                    <div>
                         <h4 class="font-semibold text-gray-700 mb-2 border-b pb-2">Ringkasan Pembayaran</h4>
                         <div id="modal-payment-summary" class="space-y-2 text-sm"></div>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2 border-b pb-2">Rincian Barang</h4>
                    <div id="modal-items-list" class="space-y-3"></div>
                </div>
            </div>
            <div class="p-4 bg-gray-50 flex justify-end gap-3 border-t rounded-b-2xl">
                <button class="close-modal px-5 py-2 bg-gray-200 text-gray-800 rounded-lg font-semibold hover:bg-gray-300">Tutup</button>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>

    <script>
        $(document).ready(function () {
            const checkoutData = [
                { id: 'ORD-001', customer: 'Budi Santoso', phone: '081234567890', email: 'budi.s@example.com', address: 'Jl. Merdeka No. 1, Jakarta', date: '2025-10-03', total: 18999000, status: 'Pending', items: [{name: 'iPhone 15 Pro (256GB)', qty: 1, price: 18999000, image: 'https://placehold.co/80x80/eef2ff/4f46e5?text=IP15'}] },
                { id: 'ORD-002', customer: 'Citra Lestari', phone: '081234567891', email: 'citra.l@example.com', address: 'Jl. Sudirman No. 2, Bandung', date: '2025-10-03', total: 21999000, status: 'Diproses', items: [{name: 'Samsung S24 Ultra (512GB)', qty: 1, price: 21999000, image: 'https://placehold.co/80x80/f1f5f9/10b981?text=S24'}] },
                { id: 'ORD-003', customer: 'Ahmad Fauzi', phone: '081234567892', email: 'ahmad.f@example.com', address: 'Jl. Pahlawan No. 3, Surabaya', date: '2025-10-02', total: 12549000, status: 'Dikirim', items: [{name: 'iPhone 14 (128GB)', qty: 1, price: 12499000, image: 'https://placehold.co/80x80/dbeafe/1e40af?text=IP14'}, {name: 'Case Pelindung', qty:1, price: 50000}]},
                { id: 'ORD-004', customer: 'Dewi Anggraini', phone: '081234567893', email: 'dewi.a@example.com', address: 'Jl. Diponegoro No. 4, Medan', date: '2025-10-01', total: 6500000, status: 'Selesai', items: [{name: 'iPhone 11 (64GB, Second)', qty: 1, price: 6500000, image: 'https://placehold.co/80x80/fecaca/7f1d1d?text=IP11'}] },
            ];

            const table = $('#checkoutTable').DataTable({
                data: checkoutData,
                dom: "<'flex flex-col md:flex-row items-center justify-between gap-4 mb-6'<'#custom-filter-slot'><'dt-search'f>>t<'flex flex-col md:flex-row items-center justify-between gap-4 mt-4'<'dt-info'i><'dt-paging'p>>",
                columns: [
                    { data: 'id', className: 'font-bold text-indigo-600' },
                    { data: 'customer', render: (data, type, row) => `<div><p class="font-semibold">${data}</p><p class="text-xs text-gray-500">${row.email}</p></div>` },
                    { data: 'items', render: data => `<div class="flex items-center gap-3"><img src="${data[0].image}" class="w-12 h-12 rounded-lg object-cover"><p>${data[0].name}${data.length > 1 ? ` (+${data.length - 1} item)` : ''}</p></div>` },
                    { data: 'total', className: 'font-semibold', render: data => `Rp${data.toLocaleString('id-ID')}` },
                    { data: 'status', className: 'text-center', render: data => `<span class="status-badge status-${data.toLowerCase()}">${data}</span>` },
                    { data: 'id', orderable: false, searchable: false, className: 'text-center', render: data => `<button data-id="${data}" class="detail-btn bg-indigo-100 text-indigo-700 font-semibold hover:bg-indigo-200 transition-colors px-3 py-1.5 rounded-lg text-sm flex items-center gap-2 mx-auto"><i class="bi bi-eye-fill"></i><span>Lihat Detail</span></button>` }
                ],
                order: [[0, 'desc']],
                createdRow: function(row) {
                    $(row).addClass('hover:bg-gray-50 transition-colors');
                    $('td', row).eq(0).attr('data-label', 'ID Pesanan');
                    $('td', row).eq(1).attr('data-label', 'Pelanggan');
                    $('td', row).eq(2).attr('data-label', 'Detail Pesanan');
                    $('td', row).eq(3).attr('data-label', 'Total');
                    $('td', row).eq(4).attr('data-label', 'Status');
                    $('td', row).eq(5).attr('data-label', 'Aksi');
                },
                 language: { 
                    search: "", searchPlaceholder: "Cari pesanan...", lengthMenu: "Tampil _MENU_",
                    zeroRecords: "<div class='text-center p-10'><p>Pesanan Tidak Ditemukan</p></div>",
                    infoEmpty: "<div class='text-center p-10'><p>Belum Ada Pesanan</p></div>",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data", paginate: { next: ">", previous: "<" }
                }
            });

            $(`<select id="status-filter" class="w-full md:w-auto"><option value="">Semua Status</option><option value="Pending">Pending</option><option value="Diproses">Diproses</option><option value="Dikirim">Dikirim</option><option value="Selesai">Selesai</option><option value="Dibatalkan">Dibatalkan</option></select>`).appendTo("#custom-filter-slot");
            
            $('#checkoutTable_wrapper .dt-search input').before('<i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>');
            $('#status-filter').on('change', function() { table.column(4).search(this.value ? `^${this.value}$` : '', true, false).draw(); });

            function updateSummaryCards() {
                const today = new Date().toISOString().slice(0, 10);
                const revenueToday = checkoutData.filter(d => d.date === today).reduce((sum, d) => sum + d.total, 0);
                $('#summary-total').text(checkoutData.length);
                $('#summary-revenue').text(`Rp${revenueToday.toLocaleString('id-ID')}`);
                $('#summary-processing').text(checkoutData.filter(d => d.status === 'Diproses').length);
                $('#summary-pending').text(checkoutData.filter(d => d.status === 'Pending').length);
            }
            updateSummaryCards();

            const modal = $('#detail-modal');
            const openModal = () => { modal.removeClass('hidden').removeClass('opacity-0'); };
            const closeModal = () => { modal.addClass('opacity-0'); setTimeout(() => modal.addClass('hidden'), 300); };

            $('#checkoutTable tbody').on('click', '.detail-btn', function() {
                const orderId = $(this).data('id');
                const data = checkoutData.find(d => d.id === orderId);
                if (data) {
                    $('#modal-order-id').text(data.id);
                    $('#modal-customer-name').text(data.customer);
                    $('#modal-customer-phone').text(data.phone);
                    $('#modal-customer-email').text(data.email);
                    $('#modal-shipping-address').text(data.address);
                    
                    let subtotal = data.items.reduce((sum, item) => sum + (item.price * item.qty), 0);
                    let itemsHtml = data.items.map(item => `<div class="flex justify-between text-sm"><p>${item.name} (x${item.qty})</p><p>Rp${(item.price * item.qty).toLocaleString('id-ID')}</p></div>`).join('');
                    $('#modal-items-list').html(itemsHtml);

                    let summaryHtml = `<div class="flex justify-between"><span class="text-gray-600">Subtotal:</span><span>Rp${subtotal.toLocaleString('id-ID')}</span></div><div class="border-t my-2"></div><div class="flex justify-between font-bold"><span class="text-gray-800">Total:</span><span>Rp${data.total.toLocaleString('id-ID')}</span></div>`;
                    $('#modal-payment-summary').html(summaryHtml);
                    
                    openModal();
                }
            });
            $('.close-modal').on('click', closeModal);
        });
    </script>
</body>
</html>
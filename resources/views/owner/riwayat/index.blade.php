<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Riwayat Order Responsif | CELVION</title>

    {{-- Tailwind CSS & jQuery --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- DataTables.net Assets --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    {{-- Ikon & Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; }
        .status-badge { padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; text-transform: capitalize; }
        .status-pending { background-color: #fef9c3; color: #854d0e; }
        .status-proses { background-color: #dbeafe; color: #1e40af; }
        .status-diambil { background-color: #e0e7ff; color: #3730a3; }
        .status-diantar { background-color: #cffafe; color: #0891b2; }
        .status-selesai { background-color: #dcfce7; color: #166534; }
        
        .modal-overlay { transition: opacity 0.3s ease; }
        
        .filter-card { transition: all 0.2s ease-in-out; border: 2px solid transparent; }
        .filter-card:hover { transform: translateY(-4px); box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08); border-color: #ddd6fe; }
        .filter-card.active-filter { background-color: #eef2ff; border-color: #6366f1; transform: translateY(-4px); box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2); }
        .filter-card.active-filter p, .filter-card.active-filter h3 { color: #4338ca; }
        .filter-card.active-filter .text-gray-600 { color: #4338ca; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
        @keyframes modal-in { from { opacity: 0; transform: translateY(-20px) scale(0.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
        .animate-modal-in { animation: modal-in 0.3s ease-out forwards; }
        
        /* Styling untuk DataTables agar sesuai tema Tailwind */
        .dataTables_wrapper .dataTables_length select, .dataTables_wrapper .dataTables_filter input {
            padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; background-color: white;
            transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .dataTables_wrapper .dataTables_length select:focus, .dataTables_wrapper .dataTables_filter input:focus {
            outline: none; border-color: #4f46e5; box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.5);
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.5em 1em; margin-left: 2px; border-radius: 0.5rem; transition: background-color 0.2s, border-color 0.2s;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background-color: #4f46e5; color: white !important; border: 1px solid #4f46e5;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background-color: #eef2ff; border-color: #a5b4fc;
        }
        /* [BARU] Menghapus border bawah default DataTable agar bisa di-style manual */
        table.dataTable.no-footer { border-bottom: none; }
        table.dataTable thead th { border-bottom: 2px solid #e5e7eb; }


        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner {
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-left-color: #4f46e5;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
    </style>
</head>
<body>
    <div class="relative min-h-screen md:flex">
        @include('owner.partials.sidebar')
        <div class="flex-1 md:ml-64 flex flex-col">
            <main class="flex-grow p-4 sm:p-6 lg:p-8 animate-fade-in">
                @include('owner.partials.header', ['title' => 'Riwayat Pengiriman', 'subtitle' => 'Kelola semua riwayat pengiriman dalam tampilan yang responsif.'])
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                    <div data-status="all" class="filter-card active-filter bg-white p-4 rounded-xl shadow-sm flex flex-col cursor-pointer"><div class="flex justify-between items-center w-full"><div class="bg-gray-100 text-gray-600 p-3 rounded-full text-xl"><i class="bi bi-list-ul"></i></div><div class="text-right"><p class="text-gray-500 font-medium text-sm">Semua Order</p><h3 id="count-all" class="text-2xl font-bold text-gray-800">0</h3></div></div></div>
                    <div data-status="pending" class="filter-card bg-white p-4 rounded-xl shadow-sm flex flex-col justify-between cursor-pointer"><div class="flex justify-between items-center w-full"><div class="bg-amber-100 text-amber-600 p-3 rounded-full text-xl"><i class="bi bi-hourglass-split"></i></div><div class="text-right"><p class="text-gray-500 font-medium text-sm">Pending</p><h3 id="count-pending" class="text-2xl font-bold text-gray-800">0</h3></div></div><div class="w-full bg-gray-200 rounded-full h-1.5 mt-2"><div id="progress-pending" class="bg-amber-500 h-1.5 rounded-full" style="width: 0%; transition: width 0.5s ease;"></div></div></div>
                    <div data-status="proses" class="filter-card bg-white p-4 rounded-xl shadow-sm flex flex-col justify-between cursor-pointer"><div class="flex justify-between items-center w-full"><div class="bg-blue-100 text-blue-600 p-3 rounded-full text-xl"><i class="bi bi-arrow-repeat"></i></div><div class="text-right"><p class="text-gray-500 font-medium text-sm">Proses</p><h3 id="count-proses" class="text-2xl font-bold text-gray-800">0</h3></div></div><div class="w-full bg-gray-200 rounded-full h-1.5 mt-2"><div id="progress-proses" class="bg-blue-500 h-1.5 rounded-full" style="width: 0%; transition: width 0.5s ease;"></div></div></div>
                    <div data-status="diambil" class="filter-card bg-white p-4 rounded-xl shadow-sm flex flex-col justify-between cursor-pointer"><div class="flex justify-between items-center w-full"><div class="bg-indigo-100 text-indigo-600 p-3 rounded-full text-xl"><i class="bi bi-bag-check-fill"></i></div><div class="text-right"><p class="text-gray-500 font-medium text-sm">Siap Diambil</p><h3 id="count-diambil" class="text-2xl font-bold text-gray-800">0</h3></div></div><div class="w-full bg-gray-200 rounded-full h-1.5 mt-2"><div id="progress-diambil" class="bg-indigo-500 h-1.5 rounded-full" style="width: 0%; transition: width 0.5s ease;"></div></div></div>
                    <div data-status="diantar" class="filter-card bg-white p-4 rounded-xl shadow-sm flex flex-col justify-between cursor-pointer"><div class="flex justify-between items-center w-full"><div class="bg-sky-100 text-sky-600 p-3 rounded-full text-xl"><i class="bi bi-truck"></i></div><div class="text-right"><p class="text-gray-500 font-medium text-sm">Siap Diantar</p><h3 id="count-diantar" class="text-2xl font-bold text-gray-800">0</h3></div></div><div class="w-full bg-gray-200 rounded-full h-1.5 mt-2"><div id="progress-diantar" class="bg-sky-500 h-1.5 rounded-full" style="width: 0%; transition: width 0.5s ease;"></div></div></div>
                </div>

                <div class="bg-white p-4 md:p-6 rounded-2xl shadow-lg relative">
                    <div id="table-loader" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center hidden z-10 rounded-2xl">
                        <div class="spinner"></div>
                    </div>
                    <div class="overflow-x-auto">
                        <table id="orders-table" class="w-full text-sm">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50">
                                <tr>
                                    <th class="p-4 text-left font-semibold tracking-wider">ID Order</th>
                                    <th class="p-4 text-left font-semibold tracking-wider">Pelanggan</th>
                                    <th class="p-4 text-left font-semibold tracking-wider">Tanggal Order</th>
                                    <th class="p-4 text-left font-semibold tracking-wider">Cabang</th>
                                    <th class="p-4 text-center font-semibold tracking-wider">Status</th>
                                    <th class="p-4 text-center font-semibold tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div id="order-detail-modal" class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 hidden modal-overlay opacity-0">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto modal-content animate-modal-in"><div class="flex justify-between items-center p-5 border-b sticky top-0 bg-white z-10 rounded-t-2xl"><div><h3 class="text-xl font-bold text-gray-800">Detail Order</h3><p class="text-sm text-gray-500" id="modal-order-id"></p></div><button class="close-modal p-2 rounded-full hover:bg-gray-200"><i class="bi bi-x-lg text-xl text-gray-600"></i></button></div><div class="p-6 space-y-6"><div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6"><div><h4 class="font-semibold text-gray-700 mb-2 border-b pb-2">Info Pelanggan</h4><div class="space-y-1 text-sm"><p><strong class="w-24 inline-block">Nama:</strong> <span id="modal-customer-name"></span></p><p><strong class="w-24 inline-block">Telepon:</strong> <span id="modal-customer-phone"></span></p><p><strong class="w-24 inline-block">Email:</strong> <span id="modal-customer-email"></span></p><p><strong class="w-24 inline-block">Alamat:</strong> <span id="modal-shipping-address" class="text-gray-600"></span></p><p><strong class="w-24 inline-block">Tanggal:</strong> <span id="modal-order-date"></span></p></div></div><div><h4 class="font-semibold text-gray-700 mb-2 border-b pb-2">Ringkasan Pembayaran</h4><div class="space-y-2 text-sm" id="modal-payment-summary"></div></div></div><div><h4 class="font-semibold text-gray-700 mb-2 border-b pb-2">Rincian Barang</h4><div class="space-y-3" id="modal-items-list"></div></div></div><div class="p-4 bg-gray-50 flex justify-end gap-3 border-t rounded-b-2xl"><button class="close-modal px-5 py-2 bg-gray-200 text-gray-800 rounded-lg font-semibold hover:bg-gray-300">Tutup</button></div></div>
    </div>

    <script>
    $(document).ready(function() {
        const dummyOrderData = [{ id: "ORD-001", date: "01 Okt 2025", customer: "Rusqi Yudha Wastu", phone: "081234567890", email: "rusqi.y@example.com", branch: "Cabang Bogor", status: "diantar", address: "Alam Tirta Lestari, Ciomas, Bogor", items: [{ name: "Keyboard Mechanical", qty: 1, price: 850000 }], shipping: 15000 },{ id: "ORD-002", date: "30 Sep 2025", customer: "Citra Lestari", phone: "081234567891", email: "citra.l@example.com", branch: "Cabang Jakarta", status: "selesai", address: "Jl. Margonda Raya No. 100, Depok", items: [{ name: "Webcam HD 1080p", qty: 1, price: 550000 }], shipping: 12000 },{ id: "ORD-004", date: "29 Sep 2025", customer: "Budi Santoso", phone: "081234567892", email: "budi.s@example.com", branch: "Cabang Bogor", status: "pending", address: "Jl. Veteran No. 12, Bogor", items: [{ name: "Monitor Gaming 24 inch", qty: 1, price: 2500000 }], shipping: 25000 },{ id: "ORD-005", date: "28 Sep 2025", customer: "Dewi Anggraini", phone: "081234567893", email: "dewi.a@example.com", branch: "Cabang Jakarta", status: "proses", address: "Jl. Gatot Subroto No. 34, Jakarta", items: [{ name: "Laptop Ultrabook", qty: 1, price: 12500000 }], shipping: 50000 },{ id: "ORD-006", date: "27 Sep 2025", customer: "Eko Prasetyo", phone: "081234567894", email: "eko.p@example.com", branch: "Cabang Depok", status: "diambil", address: "Jl. Cinere Raya No. 1, Depok", items: [{ name: "Printer All-in-One", qty: 1, price: 1200000 }], shipping: 0 },{ id: "ORD-007", date: "26 Sep 2025", customer: "Fitri Handayani", phone: "081234567895", email: "fitri.h@example.com", branch: "Cabang Bogor", status: "diantar", address: "Jl. Pajajaran No. 1, Bogor", items: [{ name: "Mouse Wireless", qty: 2, price: 150000 }], shipping: 15000 },{ id: "ORD-008", date: "25 Sep 2025", customer: "Gilang Ramadhan", phone: "081234567896", email: "gilang.r@example.com", branch: "Cabang Jakarta", status: "proses", address: "Jl. Sudirman Kav. 52-53, Jakarta", items: [{ name: "SSD NVMe 1TB", qty: 1, price: 1800000 }], shipping: 12000 }];
        const modal = $('#order-detail-modal');
        let ordersTable;

        ordersTable = $('#orders-table').DataTable({
            data: dummyOrderData,
            // [DIUBAH] Tambahkan callback createdRow untuk zebra-striping
            createdRow: function(row, data, dataIndex) {
                if (dataIndex % 2 == 0) {
                    $(row).addClass('bg-slate-50');
                }
                $(row).addClass('hover:bg-indigo-50 transition-colors');
            },
            columns: [
                { data: 'id', className: 'p-4 font-semibold text-indigo-600' },
                { data: 'customer', className: 'p-4 text-slate-700' },
                { data: 'date', className: 'p-4 text-slate-500' },
                { data: 'branch', className: 'p-4 text-slate-500' },
                {
                    data: 'status',
                    className: 'p-4 text-center',
                    render: function (data, type, row) {
                        return `<span class="status-badge status-${data}">${data.replace('-', ' ')}</span>`;
                    }
                },
                {
                    data: 'id',
                    className: 'p-4 text-center',
                    orderable: false,
                    // [DIUBAH] Render tombol aksi menjadi badge
                    render: function (data, type, row) {
                        return `<button data-order-id="${data}" class="bg-indigo-100 text-indigo-700 hover:bg-indigo-200 font-semibold px-3 py-1 rounded-full text-xs transition-colors open-detail-modal">Lihat Detail</button>`;
                    }
                }
            ],
            language: {
                lengthMenu: "Tampil _MENU_ entri", zeroRecords: "Tidak ada data yang ditemukan", info: "Menampilkan _START_ hingga _END_ dari _TOTAL_ entri", infoEmpty: "Menampilkan 0 hingga 0 dari 0 entri", infoFiltered: "(disaring dari _MAX_ total entri)", search: "Cari:",
                paginate: { first: "Pertama", last: "Terakhir", next: "Berikutnya", previous: "Sebelumnya" }
            },
            dom: '<"flex flex-col md:flex-row justify-between items-center mb-4"lf>rt<"flex flex-col md:flex-row justify-between items-center mt-4"ip>',
        });

        function updateCardCounts() {
            const counts = { all: 0, pending: 0, proses: 0, diambil: 0, diantar: 0, selesai: 0 };
            dummyOrderData.forEach(order => {
                counts.all++;
                if(counts.hasOwnProperty(order.status)) counts[order.status]++;
            });

            for (const status in counts) {
                const countElement = $(`#count-${status}`);
                if (countElement.length) countElement.text(counts[status]);
                
                const progressElement = $(`#progress-${status}`);
                if (progressElement.length) {
                    const percentage = counts.all > 0 ? (counts[status] / counts.all) * 100 : 0;
                    progressElement.css('width', percentage + '%');
                }
            }
        }
        
        $('.filter-card').on('click', function() {
            const card = $(this);
            if (card.hasClass('active-filter')) return;

            $('.filter-card').removeClass('active-filter');
            card.addClass('active-filter');
            const selectedStatus = card.data('status');
            
            $('#table-loader').removeClass('hidden');

            setTimeout(() => {
                const filteredData = selectedStatus === 'all' 
                    ? dummyOrderData 
                    : dummyOrderData.filter(order => order.status === selectedStatus);

                ordersTable.clear().rows.add(filteredData).draw();
                
                $('#table-loader').addClass('hidden');
            }, 500);
        });

        const openModal = () => modal.removeClass('hidden').removeClass('opacity-0');
        const closeModal = () => {
            modal.addClass('opacity-0');
            setTimeout(() => modal.addClass('hidden'), 300);
        };

        $('body').on('click', '.open-detail-modal', function() {
            const orderId = $(this).data('order-id');
            const data = dummyOrderData.find(order => order.id === orderId);
            
            if (data) {
                $('#modal-order-id').text(data.id);
                $('#modal-customer-name').text(data.customer);
                $('#modal-customer-phone').text(data.phone);
                $('#modal-customer-email').text(data.email);
                $('#modal-shipping-address').text(data.address);
                $('#modal-order-date').text(data.date);
                
                let itemsHtml = '', subtotal = 0;
                data.items.forEach(item => {
                    const itemTotal = item.qty * item.price;
                    subtotal += itemTotal;
                    itemsHtml += `<div class="flex justify-between items-center text-sm"><p class="text-gray-800">${item.name} (x${item.qty})</p><p class="font-medium">Rp ${itemTotal.toLocaleString('id-ID')}</p></div>`;
                });
                $('#modal-items-list').html(itemsHtml);

                const total = subtotal + data.shipping;
                let paymentHtml = `<div class="flex justify-between"><span class="text-gray-600">Subtotal:</span><span>Rp ${subtotal.toLocaleString('id-ID')}</span></div>`;
                if(data.shipping > 0) paymentHtml += `<div class="flex justify-between"><span class="text-gray-600">Ongkos Kirim:</span><span>Rp ${data.shipping.toLocaleString('id-ID')}</span></div>`;
                paymentHtml += `<div class="border-t my-2"></div><div class="flex justify-between font-bold text-base"><span class="text-gray-800">Total:</span><span class="text-indigo-600">Rp ${total.toLocaleString('id-ID')}</span></div>`;
                $('#modal-payment-summary').html(paymentHtml);

                openModal();
            }
        });

        $('.close-modal').on('click', closeModal);
        modal.on('click', function(e) { if (e.target === this) closeModal(); });
        
        updateCardCounts();
    });
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Laporan & Riwayat Order | CELVION</title>

    {{-- Tailwind CSS & jQuery --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- Ikon & Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    
    {{-- Animasi AOS --}}
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; }
        .status-badge { padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; text-transform: capitalize; }
        .status-pending { background-color: #fef9c3; color: #854d0e; }
        .status-proses { background-color: #dbeafe; color: #1e40af; }
        .status-diambil { background-color: #e0e7ff; color: #3730a3; }
        .status-diantar { background-color: #cffafe; color: #0891b2; }
        .status-selesai { background-color: #dcfce7; color: #166534; }
        
        .modal-overlay { transition: opacity 0.3s ease; }
        .modal-content { transition: transform 0.3s ease, opacity 0.3s ease; }

        @media (max-width: 767px) {
            .responsive-table thead { display: none; }
            .responsive-table tr { display: block; margin-bottom: 1rem; border: 1px solid #e5e7eb; border-radius: 0.75rem; box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.05); overflow: hidden; background: white; }
            .responsive-table td { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; text-align: right; border-bottom: 1px solid #f3f4f6; }
            .responsive-table td:last-child { border-bottom: none; }
            .responsive-table td::before { content: attr(data-label); font-weight: 600; text-align: left; margin-right: 1rem; color: #4b5563; }
        }
    </style>
</head>
<body>
    <div class="relative min-h-screen md:flex">
        
        @include('Staff.partials.sidebar')

        <div class="flex-1 md:ml-64 flex flex-col min-w-0">
            <main class="flex-grow p-4 sm:p-6 lg:p-8">
                
                @include('Staff.partials.header', [
                    'title' => 'Riwayat & Laporan',
                    'subtitle' => 'Analisis dan kelola semua riwayat pengiriman.'
                ])

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8" data-aos="fade-up">
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center justify-between border-l-4 border-blue-500">
                        <div><p class="text-gray-500 font-medium">Total Pesanan</p><h3 id="summary-total-orders" class="text-2xl font-bold text-gray-800">0</h3></div>
                        <div class="bg-blue-100 text-blue-600 p-3 rounded-full text-2xl"><i class="bi bi-receipt"></i></div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center justify-between border-l-4 border-green-500">
                        <div><p class="text-gray-500 font-medium">Total Pendapatan</p><h3 id="summary-total-revenue" class="text-xl lg:text-2xl font-bold text-gray-800">Rp0</h3></div>
                        <div class="bg-green-100 text-green-600 p-3 rounded-full text-2xl"><i class="bi bi-cash-coin"></i></div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center justify-between border-l-4 border-indigo-500">
                        <div><p class="text-gray-500 font-medium">Pesanan Selesai</p><h3 id="summary-completed-orders" class="text-2xl font-bold text-gray-800">0</h3></div>
                        <div class="bg-indigo-100 text-indigo-600 p-3 rounded-full text-2xl"><i class="bi bi-check2-circle"></i></div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center justify-between border-l-4 border-amber-500">
                        <div><p class="text-gray-500 font-medium">Pesanan Pending</p><h3 id="summary-pending-orders" class="text-2xl font-bold text-gray-800">0</h3></div>
                        <div class="bg-amber-100 text-amber-600 p-3 rounded-full text-2xl"><i class="bi bi-hourglass-split"></i></div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                    <div class="lg:col-span-1" data-aos="fade-right" data-aos-delay="100">
                        <div class="bg-white p-6 rounded-2xl shadow-lg space-y-6 sticky top-8">
                            <h4 class="text-lg font-semibold text-gray-800 border-b pb-3">Filter Laporan</h4>
                            <div>
                                <label for="search-input" class="text-sm font-medium text-gray-600">Cari ID/Nama</label>
                                <div class="relative mt-1">
                                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <input type="text" id="search-input" placeholder="e.g., ORD-001" class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>
                            </div>
                            <div>
                                <label for="status-filter" class="text-sm font-medium text-gray-600">Status</label>
                                <select id="status-filter" class="w-full mt-1 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="all">Semua Status</option>
                                    <option value="pending">Pending</option><option value="proses">Proses</option>
                                    <option value="diambil">Siap Diambil</option><option value="diantar">Siap Diantar</option>
                                    <option value="selesai">Selesai</option>
                                </select>
                            </div>
                            <div>
                                <label for="branch-filter" class="text-sm font-medium text-gray-600">Cabang</label>
                                <select id="branch-filter" class="w-full mt-1 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="all">Semua Cabang</option>
                                    <option value="Cabang Bogor">Bogor</option><option value="Cabang Jakarta">Jakarta</option><option value="Cabang Depok">Depok</option>
                                </select>
                            </div>
                             <button id="apply-filter" class="w-full bg-indigo-600 text-white font-bold py-2.5 px-4 rounded-lg hover:bg-indigo-700 transition-colors shadow-md flex items-center justify-center gap-2">
                                <i class="bi bi-funnel-fill"></i> Terapkan Filter
                            </button>
                        </div>
                    </div>

                    <div class="lg:col-span-3" data-aos="fade-up" data-aos-delay="200">
                         <div class="bg-white p-4 md:p-6 rounded-2xl shadow-lg">
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm responsive-table">
                                    <thead class="bg-gray-50 text-gray-600 uppercase">
                                        <tr>
                                            <th class="p-4 text-left">ID Order</th>
                                            <th class="p-4 text-left">Detail Produk</th>
                                            <th class="p-4 text-left">Tanggal</th>
                                            <th class="p-4 text-center">Status</th>
                                            <th class="p-4 text-right">Total</th>
                                            <th class="p-4 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="order-table-body"></tbody>
                                </table>
                                 <p id="no-results" class="text-center text-gray-500 py-16 hidden"><i class="bi bi-search text-5xl block mb-4"></i>Tidak ada hasil ditemukan.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div id="order-detail-modal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden modal-overlay opacity-0">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto modal-content transform scale-95 opacity-0">
            <div class="flex justify-between items-center p-5 border-b sticky top-0 bg-white z-10">
                <div><h3 class="text-xl font-bold text-gray-800">Detail Order</h3><p class="text-sm text-gray-500" id="modal-order-id"></p></div>
                <button class="close-modal p-2 rounded-full hover:bg-gray-200"><i class="bi bi-x-lg text-xl text-gray-600"></i></button>
            </div>
            
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                     <div>
                        <h4 class="font-semibold text-gray-700 mb-2 border-b pb-2">Info Pelanggan</h4>
                        <div class="space-y-1 text-sm">
                            <p><strong class="w-24 inline-block">Nama:</strong> <span id="modal-customer-name"></span></p>
                            <p><strong class="w-24 inline-block">Telepon:</strong> <span id="modal-customer-phone"></span></p>
                            <p><strong class="w-24 inline-block">Email:</strong> <span id="modal-customer-email"></span></p>
                            <p><strong class="w-24 inline-block">Alamat:</strong> <span id="modal-shipping-address" class="text-gray-600"></span></p>
                            <p><strong class="w-24 inline-block">Cabang:</strong> <span id="modal-branch-name" class="text-gray-600"></span></p>
                        </div>
                    </div>
                    <div>
                         <h4 class="font-semibold text-gray-700 mb-2 border-b pb-2">Ringkasan Pembayaran</h4>
                         <div class="space-y-2 text-sm" id="modal-payment-summary"></div>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2 border-b pb-2">Rincian Barang</h4>
                    <div class="space-y-3" id="modal-items-list"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
    $(document).ready(function() {
        AOS.init({ duration: 600, once: true, offset: 20 });

        // [DIUBAH] Menghapus statusHistory dari data
        const dummyOrderData = [
            { id: "ORD-001", date: "01 Okt 2025", customer: "Ahmad Fauzi", phone: "081234567892", email: "ahmad.f@example.com", branch: "Cabang Jakarta", status: "selesai", address: "Jl. Margonda Raya, Depok", items: [{ name: "Samsung S24 Ultra", qty: 1, price: 21999000, image: "https://placehold.co/80x80/f1f5f9/10b981?text=S24" }], shipping: 0 },
            { id: "ORD-002", date: "30 Sep 2025", customer: "Citra Lestari", phone: "081234567891", email: "citra.l@example.com", branch: "Cabang Jakarta", status: "diantar", address: "Jl. Gatot Subroto, Jakarta", items: [{ name: "iPhone 14", qty: 1, price: 12499000, image: "https://placehold.co/80x80/dbeafe/1e40af?text=IP14" }], shipping: 25000 },
            { id: "ORD-003", date: "29 Sep 2025", customer: "Budi Santoso", phone: "081234567890", email: "budi.s@example.com", branch: "Cabang Bogor", status: "pending", address: "Jl. Veteran, Bogor", items: [{ name: "iPhone 15 Pro", qty: 1, price: 18999000, image: "https://placehold.co/80x80/eef2ff/4f46e5?text=IP15" }], shipping: 25000 },
        ];

        const modal = $('#order-detail-modal'), tableBody = $('#order-table-body'), noResults = $('#no-results');

        function renderTable(orders) {
            tableBody.empty();
            noResults.toggleClass('hidden', orders.length > 0);
            orders.forEach(order => {
                const total = order.items.reduce((sum, item) => sum + (item.qty * item.price), 0) + order.shipping;
                const firstItem = order.items[0];
                const rowHtml = `
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td data-label="ID Order" class="p-4 font-bold text-indigo-600">${order.id}</td>
                        <td data-label="Detail Produk" class="p-4"><div class="flex items-center gap-4"><img src="${firstItem.image}" alt="${firstItem.name}" class="w-14 h-14 rounded-md object-cover flex-shrink-0"><div><p class="font-semibold text-gray-800">${firstItem.name}</p><p class="text-xs text-gray-500">Oleh: ${order.customer}</p><p class="text-xs text-gray-500">${order.branch}</p></div></div></td>
                        <td data-label="Tanggal" class="p-4 text-gray-500">${order.date}</td>
                        <td data-label="Status" class="p-4 text-center"><span class="status-badge status-${order.status}">${order.status.replace('-', ' ')}</span></td>
                        <td data-label="Total" class="p-4 text-right font-semibold text-gray-700">Rp ${total.toLocaleString('id-ID')}</td>
                        <td data-label="Aksi" class="p-4 text-center"><button data-order-id="${order.id}" class="open-detail-modal text-indigo-600 hover:bg-indigo-100 font-semibold px-3 py-1.5 rounded-lg transition-colors text-sm">Lihat Detail</button></td>
                    </tr>`;
                tableBody.append(rowHtml);
            });
        }

        function filterAndRender() {
            const searchTerm = $('#search-input').val().toLowerCase(), statusFilter = $('#status-filter').val(), branchFilter = $('#branch-filter').val();
            const filteredData = dummyOrderData.filter(order => (order.id.toLowerCase().includes(searchTerm) || order.customer.toLowerCase().includes(searchTerm)) && (statusFilter === 'all' || order.status === statusFilter) && (branchFilter === 'all' || order.branch === branchFilter));
            renderTable(filteredData);
        }

        function updateSummaryCards() {
            let totalOrders = dummyOrderData.length, totalRevenue = 0, completedOrders = 0, pendingOrders = 0;
            dummyOrderData.forEach(order => {
                totalRevenue += order.items.reduce((sum, item) => sum + (item.qty * item.price), 0) + order.shipping;
                if (order.status === 'selesai') completedOrders++;
                if (order.status === 'pending') pendingOrders++;
            });
            $('#summary-total-orders').text(totalOrders);
            $('#summary-total-revenue').text('Rp' + totalRevenue.toLocaleString('id-ID'));
            $('#summary-completed-orders').text(completedOrders);
            $('#summary-pending-orders').text(pendingOrders);
        }

        $('#apply-filter').on('click', filterAndRender);

        function openModal() {
            modal.removeClass('hidden');
            setTimeout(() => modal.removeClass('opacity-0').find('.modal-content').removeClass('opacity-0 scale-95'), 10);
            $('body').addClass('overflow-hidden');
        }

        function closeModal() {
            modal.addClass('opacity-0').find('.modal-content').addClass('opacity-0 scale-95');
            setTimeout(() => modal.addClass('hidden'), 300);
            $('body').removeClass('overflow-hidden');
        }

        $(document).on('click', '.open-detail-modal', function() {
            const orderId = $(this).data('order-id');
            const data = dummyOrderData.find(order => order.id === orderId);
            if (data) {
                $('#modal-order-id').text(data.id);
                $('#modal-customer-name').text(data.customer);
                $('#modal-customer-phone').text(data.phone);
                $('#modal-customer-email').text(data.email);
                $('#modal-shipping-address').text(data.address);
                $('#modal-branch-name').text(data.branch);
                
                // [DIHAPUS] Logika untuk render timeline
                
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
                paymentHtml += `<div class="border-t my-2"></div><div class="flex justify-between font-bold text-base"><span class="text-gray-800">Total Pembayaran:</span><span class="text-indigo-600">Rp ${total.toLocaleString('id-ID')}</span></div>`;
                $('#modal-payment-summary').html(paymentHtml);

                openModal();
            }
        });

        $('.close-modal').on('click', closeModal);
        modal.on('click', (e) => { if (e.target === modal[0]) closeModal(); });
        
        updateSummaryCards();
        renderTable(dummyOrderData);
    });
    </script>
</body>
</html>
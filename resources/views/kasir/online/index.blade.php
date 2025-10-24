<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Transaksi Online | CELVION</title>

    {{-- Tailwind CSS & jQuery --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- Ikon & Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

    {{-- Animasi AOS & SweetAlert2 --}}
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- DataTables CSS --}}
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css" rel="stylesheet" />

    {{-- jsPDF & html2canvas untuk Modal --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
        }

        .modal-overlay {
            transition: opacity 0.3s ease;
        }

        .modal-content {
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-diproses {
            background-color: #fef9c3;
            color: #854d0e;
        }

        .status-dikirim {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-selesai {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-menunggudiambil {
            background-color: #e9d5ff;
            color: #581c87;
        }

        .dt-search input,
        .dt-length select {
            background-color: white !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 0.5rem !important;
            padding: 0.5rem 0.75rem !important;
            outline: none;
        }

        .dt-search input {
            padding-left: 2.25rem !important;
        }

        .dt-paging .dt-paging-button.current {
            background: #4f46e5 !important;
            color: #ffffff !important;
        }

        /* [BARU] CSS untuk Tabel Responsif */
        @media (max-width: 767px) {
            .responsive-table thead {
                display: none;
            }

            .responsive-table tbody,
            .responsive-table td {
                display: block;
                width: 100%;
            }

            .responsive-table tr {
                display: block;
                background-color: white;
                margin-bottom: 1.25rem;
                border: 1px solid #e2e8f0;
                border-radius: 0.75rem;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
                overflow: hidden;
            }

            .responsive-table td {
                padding: 0.75rem 1rem;
                border-bottom: 1px solid #f1f5f9;
                text-align: right;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .responsive-table td:last-child {
                border-bottom: none;
            }

            .responsive-table td[data-label]::before {
                content: attr(data-label);
                font-weight: 600;
                color: #475569;
                text-align: left;
                margin-right: 1rem;
            }

            /* Styling khusus untuk kolom detail dan aksi */
            .responsive-table td[data-label="Detail Pesanan"] {
                padding: 1rem;
                border-bottom: 1px solid #e2e8f0;
            }

            .responsive-table td[data-label="Detail Pesanan"]::before {
                display: none;
            }

            .responsive-table td[data-label="Aksi"] {
                justify-content: center;
                background-color: #f8fafc;
                padding: 1rem;
            }

            .responsive-table td[data-label="Aksi"]::before {
                display: none;
            }

            /* Menyesuaikan layout filter datatables di mobile */
            #deliveryOrdersTable_wrapper .dt-layout-row,
            #pickupOrdersTable_wrapper .dt-layout-row {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="relative min-h-screen md:flex">

        @include('kasir.partials.sidebar')

        <div class="flex-1 md:ml-64 flex flex-col min-w-0">
            <main class="flex-grow p-4 sm:p-6 lg:p-8">

                @include('kasir.partials.header', [
                    'title' => 'Transaksi Online',
                    'subtitle' => 'Kelola pesanan online yang masuk, baik untuk diantar maupun diambil di tempat.',
                ])

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-2xl shadow-lg" data-aos="fade-up">
                        <div
                            class="bg-blue-100 text-blue-600 w-12 h-12 flex items-center justify-center rounded-full text-2xl mb-4">
                            <i class="bi bi-box-arrow-in-down"></i>
                        </div>
                        <p class="text-gray-500 text-sm">Pesanan Baru (Hari Ini)</p>
                        <h3 id="card-new-orders" class="text-3xl font-bold text-gray-800 mt-1">0</h3>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-lg" data-aos="fade-up" data-aos-delay="100">
                        <div
                            class="bg-yellow-100 text-yellow-600 w-12 h-12 flex items-center justify-center rounded-full text-2xl mb-4">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <p class="text-gray-500 text-sm">Perlu Dikirim</p>
                        <h3 id="card-to-ship" class="text-3xl font-bold text-gray-800 mt-1">0</h3>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-lg" data-aos="fade-up" data-aos-delay="200">
                        <div
                            class="bg-purple-100 text-purple-600 w-12 h-12 flex items-center justify-center rounded-full text-2xl mb-4">
                            <i class="bi bi-bag-check"></i>
                        </div>
                        <p class="text-gray-500 text-sm">Siap Diambil</p>
                        <h3 id="card-for-pickup" class="text-3xl font-bold text-gray-800 mt-1">0</h3>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-lg" data-aos="fade-up" data-aos-delay="300">
                        <div
                            class="bg-green-100 text-green-600 w-12 h-12 flex items-center justify-center rounded-full text-2xl mb-4">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <p class="text-gray-500 text-sm">Pemasukan Online (Hari Ini)</p>
                        <h3 id="card-online-revenue" class="text-3xl font-bold text-gray-800 mt-1">Rp0</h3>
                    </div>
                </div>

                <div class="bg-white p-2 sm:p-4 rounded-2xl shadow-lg" data-aos="fade-up">
                    <div class="border-b border-gray-200">
                        <nav class="flex flex-wrap -mb-px" aria-label="Tabs">
                            <button
                                class="tab-btn w-1/2 sm:w-auto text-center sm:text-left whitespace-nowrap py-4 px-1 sm:px-6 border-b-2 font-medium text-sm border-indigo-600 text-indigo-600"
                                data-tab="delivery">
                                <i class="bi bi-truck mr-2"></i>Pesanan Di Antar
                            </button>
                            <button
                                class="tab-btn w-1/2 sm:w-auto text-center sm:text-left whitespace-nowrap py-4 px-1 sm:px-6 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300"
                                data-tab="pickup">
                                <i class="bi bi-shop-window mr-2"></i>Ambil di Tempat
                            </button>
                        </nav>
                    </div>

                    <div class="pt-4">
                        <div id="delivery" class="tab-content p-2">
                            <table id="deliveryOrdersTable" class="w-full text-sm responsive-table" style="width:100%">
                                <thead class="bg-gray-50 text-gray-600 uppercase">
                                    <tr>
                                        <th class="p-4 text-left">ID Pesanan</th>
                                        <th class="p-4 text-left">Detail Pesanan</th>
                                        <th class="p-4 text-left">Tanggal</th>
                                        <th class="p-4 text-center">Status</th>
                                        <th class="p-4 text-right">Total</th>
                                        <th class="p-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div id="pickup" class="tab-content p-2 hidden">
                            <table id="pickupOrdersTable" class="w-full text-sm responsive-table" style="width:100%">
                                <thead class="bg-gray-50 text-gray-600 uppercase">
                                    <tr>
                                        <th class="p-4 text-left">ID Pesanan</th>
                                        <th class="p-4 text-left">Detail Pesanan</th>
                                        <th class="p-4 text-left">Tanggal</th>
                                        <th class="p-4 text-center">Status</th>
                                        <th class="p-4 text-right">Total</th>
                                        <th class="p-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div id="detail-modal"
        class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden modal-overlay opacity-0">
        <div
            class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col modal-content transform scale-95 opacity-0">
            <div class="overflow-y-auto">
                <div id="modal-printable-content">
                    <div class="flex justify-between items-center p-5 border-b">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Detail Pesanan Online</h3>
                            <p id="modal-id" class="text-sm text-gray-500"></p>
                        </div>
                        <button class="close-modal p-2 print:hidden"><i class="bi bi-x-lg text-xl"></i></button>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-semibold text-gray-700 mb-2 border-b pb-2">Info Pelanggan & Pengiriman
                                </h4>
                                <div class="space-y-1 text-sm">
                                    <p><strong class="w-24 inline-block">Nama:</strong> <span
                                            id="modal-customer"></span></p>
                                    <p><strong class="w-24 inline-block">Telepon:</strong> <span
                                            id="modal-phone"></span></p>
                                    <p><strong class="w-24 inline-block">Tipe:</strong> <span id="modal-type"></span>
                                    </p>
                                    <div id="modal-address-container" class="hidden">
                                        <p><strong class="w-24 inline-block align-top">Alamat:</strong> <span
                                                id="modal-address" class="inline-block max-w-xs"></span></p>
                                    </div>
                                    <p><strong class="w-24 inline-block">Tanggal:</strong> <span
                                            id="modal-date"></span></p>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-700 mb-2 border-b pb-2">Ringkasan Pembayaran</h4>
                                <div id="modal-summary" class="space-y-2 text-sm"></div>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-700 mb-2 border-b pb-2">Rincian Barang</h4>
                            <div id="modal-items" class="space-y-3"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-4 bg-gray-50 flex justify-end gap-3 border-t print:hidden sticky bottom-0">
                <button id="download-pdf-btn"
                    class="px-5 py-2 bg-gray-600 text-white rounded-lg font-semibold flex items-center gap-2 hover:bg-gray-700"><i
                        class="bi bi-file-earmark-arrow-down-fill"></i> Download PDF</button>
                <button id="print-invoice-btn"
                    class="px-5 py-2 bg-indigo-600 text-white rounded-lg font-semibold flex items-center gap-2 hover:bg-indigo-700"><i
                        class="bi bi-printer-fill"></i> Cetak Struk</button>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi animasi AOS
            AOS.init({
                duration: 600,
                once: true,
                offset: 20
            });

            // Setup CSRF token untuk semua request AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            // === VARIABEL GLOBAL & FUNGSI UTAMA ===
            const detailModal = $('#detail-modal');
            let allOrdersData = []; // Cache untuk menyimpan semua data pesanan

            // Fungsi untuk format mata uang
            const formatCurrency = (amount) => `Rp${(amount || 0).toLocaleString('id-ID')}`;

            // Fungsi untuk memuat data summary cards
            function fetchSummary() {
                $.ajax({
                    url: "{{ route('kasir.online.summary') }}",
                    method: 'GET',
                    success: function(data) {
                        $('#card-new-orders').text(data.new_orders);
                        $('#card-to-ship').text(data.to_ship);
                        $('#card-for-pickup').text(data.for_pickup);
                        $('#card-online-revenue').text(formatCurrency(data.online_revenue));
                    },
                    error: function(err) {
                        console.error("Gagal memuat summary:", err);
                    }
                });
            }

            // === INISIALISASI DATATABLES ===
            const commonTableOptions = {
                dom: "<'flex flex-col lg:flex-row items-center justify-between gap-4 mb-4'<'dt-length'l><'dt-search'f>>" +
                    "<'w-full't>" +
                    "<'flex flex-col md:flex-row items-center justify-between gap-4 mt-4'<'dt-info'i><'dt-paging'p>>",
                language: {
                    search: "",
                    searchPlaceholder: "Cari ID/Nama...",
                    lengthMenu: "Tampil _MENU_",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: {
                        next: ">",
                        previous: "<"
                    }
                },
                processing: true,
                serverSide: false, // Data di-handle client-side setelah AJAX call
                columnDefs: [{
                        targets: 0,
                        data: 'id',
                        className: 'font-bold text-indigo-600'
                    },
                    {
                        targets: 1,
                        data: 'customer',
                        render: (data, type, row) =>
                            `<div class="flex items-center gap-3"><img src="${row.image}" class="w-12 h-12 rounded-md object-cover"><div><p class="font-semibold text-gray-800">${row.items[0]?.name || 'Produk'}</p><p class="text-xs text-gray-500">Oleh: ${data}</p></div></div>`
                    },
                    {
                        targets: 2,
                        data: 'date'
                    },
                    {
                        targets: 3,
                        data: 'status',
                        className: 'text-center',
                        render: data =>
                            `<span class="status-badge status-${(data || '').toLowerCase().replace(/ /g, '')}">${data}</span>`
                    },
                    {
                        targets: 4,
                        data: 'total',
                        className: 'text-right font-semibold',
                        render: data => formatCurrency(data)
                    },
                    {
                        targets: 5,
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                createdRow: function(row, data, dataIndex) {
                    $('td', row).eq(0).attr('data-label', 'ID Pesanan');
                    $('td', row).eq(1).attr('data-label', 'Detail Pesanan');
                    $('td', row).eq(2).attr('data-label', 'Tanggal');
                    $('td', row).eq(3).attr('data-label', 'Status');
                    $('td', row).eq(4).attr('data-label', 'Total');
                    $('td', row).eq(5).attr('data-label', 'Aksi');
                }
            };

            const deliveryColumns = [...commonTableOptions.columnDefs];
            deliveryColumns[5] = {
                ...deliveryColumns[5],
                render: function(data, type, row) {
                    const detailBtn =
                        `<button data-id="${row.id}" class="detail-btn text-indigo-600 font-semibold hover:underline text-xs px-2">Detail</button>`;
                    let statusAction = '';
                    if (row.status === 'Diproses') statusAction =
                        `<button data-id="${row.id}" data-action="kirim" class="change-status-btn bg-blue-500 text-white text-xs font-semibold px-3 py-1 rounded-md hover:bg-blue-600">Kirim</button>`;
                    else if (row.status === 'Dikirim') statusAction =
                        `<button data-id="${row.id}" data-action="selesai-kirim" class="change-status-btn bg-green-500 text-white text-xs font-semibold px-3 py-1 rounded-md hover:bg-green-600">Selesaikan</button>`;
                    return `<div class="flex items-center justify-center gap-2">${statusAction}${detailBtn}</div>`;
                }
            };

            const pickupColumns = [...commonTableOptions.columnDefs];
            pickupColumns[5] = {
                ...pickupColumns[5],
                render: function(data, type, row) {
                    const detailBtn =
                        `<button data-id="${row.id}" class="detail-btn text-indigo-600 font-semibold hover:underline text-xs px-2">Detail</button>`;
                    let statusAction = '';
                    if (row.status === 'Menunggu Diambil') statusAction =
                        `<button data-id="${row.id}" data-action="selesai-ambil" class="change-status-btn bg-green-500 text-white text-xs font-semibold px-3 py-1 rounded-md hover:bg-green-600">Diambil</button>`;
                    return `<div class="flex items-center justify-center gap-2">${statusAction}${detailBtn}</div>`;
                }
            };

            let deliveryTable = new DataTable('#deliveryOrdersTable', {
                ...commonTableOptions,
                columns: deliveryColumns,
                data: []
            });
            let pickupTable = new DataTable('#pickupOrdersTable', {
                ...commonTableOptions,
                columns: pickupColumns,
                data: []
            });

            // Fungsi untuk memuat ulang data tabel
            function refreshTables() {
                $.ajax({
                    url: "{{ route('kasir.online.data') }}",
                    method: 'GET',
                    success: function(data) {
                        allOrdersData = [...data.delivery, ...data.pickup];
                        deliveryTable.clear().rows.add(data.delivery).draw();
                        pickupTable.clear().rows.add(data.pickup).draw();
                        fetchSummary(); // Panggil ulang summary setelah data diperbarui
                    },
                    error: function(err) {
                        console.error("Gagal memuat data tabel:", err);
                        Swal.fire('Error!', 'Gagal memuat data pesanan.', 'error');
                    }
                });
            }

            // Tambahkan ikon search ke input filter
            $('.dt-search').addClass('relative').find('input').before(
                '<i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>');

            // === EVENT LISTENERS ===

            // Event listener untuk tombol Tab
            $('.tab-btn').on('click', function() {
                const tabId = $(this).data('tab');
                $('.tab-btn').removeClass('border-indigo-600 text-indigo-600').addClass(
                    'border-transparent text-gray-500');
                $(this).addClass('border-indigo-600 text-indigo-600').removeClass('border-transparent');
                $('.tab-content').addClass('hidden');
                $('#' + tabId).removeClass('hidden');
                $.fn.dataTable.tables({
                    visible: true,
                    api: true
                }).columns.adjust();
            });

            // Event listener untuk tombol ubah status
            $('tbody').on('click', '.change-status-btn', function() {
                const orderId = $(this).data('id');
                const action = $(this).data('action');
                let nextStatus = '',
                    confirmationText = '';

                if (action === 'kirim') {
                    nextStatus = 'Dikirim';
                    confirmationText = 'Mengubah status menjadi "Dikirim"?';
                } else if (action === 'selesai-kirim') {
                    nextStatus = 'Selesai';
                    confirmationText = 'Menyelesaikan pesanan ini?';
                } else if (action === 'selesai-ambil') {
                    nextStatus = 'Selesai';
                    confirmationText = 'Menyelesaikan pesanan ini?';
                }

                Swal.fire({
                    title: 'Konfirmasi',
                    text: confirmationText,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Ubah!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const url = `{{ url('kasir/online') }}/${orderId}/status`;
                        $.ajax({
                            url: url,
                            method: 'PATCH',
                            data: {
                                status: nextStatus
                            },
                            success: function(response) {
                                Swal.fire('Berhasil!',
                                    `Status pesanan ${orderId} telah diubah.`,
                                    'success');
                                refreshTables();
                            },
                            error: function(err) {
                                Swal.fire('Error!', 'Gagal mengubah status pesanan.',
                                    'error');
                            }
                        });
                    }
                });
            });

            // === FUNGSI & EVENT LISTENER MODAL ===
            function openModal() {
                detailModal.removeClass('hidden');
                setTimeout(() => detailModal.removeClass('opacity-0').find('.modal-content').removeClass(
                    'opacity-0 scale-95'), 10);
                $('body').addClass('overflow-hidden');
            }

            function closeModal() {
                detailModal.addClass('opacity-0').find('.modal-content').addClass('opacity-0 scale-95');
                setTimeout(() => detailModal.addClass('hidden'), 300);
                $('body').removeClass('overflow-hidden');
            }

            $('.close-modal').on('click', closeModal);

            // Event listener untuk tombol "Detail"
            $('tbody').on('click', '.detail-btn', function() {
                const orderId = $(this).data('id');
                const data = allOrdersData.find(o => o.id === orderId);
                if (data) {
                    // Mengisi data umum modal
                    $('#modal-id').text(data.id);
                    $('#modal-customer').text(data.customer);
                    $('#modal-phone').text(data.phone);
                    $('#modal-type').text(data.type === 'delivery' ? 'Di Antar' : 'Ambil di Tempat');
                    $('#modal-date').text(new Date(data.date).toLocaleString('id-ID', {
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric'
                    }));

                    if (data.type === 'delivery' && data.address) {
                        $('#modal-address-container').removeClass('hidden');
                        $('#modal-address').text(data.address);
                    } else {
                        $('#modal-address-container').addClass('hidden');
                    }

                    // Mengisi rincian barang dengan info varian
                    let itemsHtml = data.items.map(item => {
                        return `
                        <div class="flex justify-between items-start text-sm mb-2">
                            <div>
                                <p class="font-semibold text-gray-800">${item.name} (x${item.qty})</p>
                                <p class="text-xs text-gray-500">${item.variant_info}</p>
                            </div>
                            <p class="font-semibold">${formatCurrency(item.price * item.qty)}</p>
                        </div>
                    `;
                    }).join('');
                    $('#modal-items').html(itemsHtml);

                    // Mengisi ringkasan pembayaran
                    let subtotal = data.items.reduce((sum, item) => sum + (item.price * item.qty), 0);
                    let summaryHtml = `<div class="flex justify-between text-sm"><span class="text-gray-600">Subtotal:</span><span>${formatCurrency(data.subtotal)}</span></div>`; // Gunakan subtotal dari data

                    if (data.discount_amount && data.discount_amount > 0) {
                        summaryHtml += `<div class="flex justify-between text-sm text-red-600"><span class="text-gray-600">Diskon ${data.voucher_code ? '('+data.voucher_code+')' : ''}:</span><span>- ${formatCurrency(data.discount_amount)}</span></div>`;
                    }
                     summaryHtml += `<div class="flex justify-between text-sm"><span class="text-gray-600">Pengiriman:</span><span>Gratis</span></div>`; // Asumsi pengiriman gratis
                    summaryHtml += `<div class="border-t my-2"></div><div class="flex justify-between font-bold"><span class="text-gray-800">Total:</span><span class="text-indigo-600">${formatCurrency(data.total)}</span></div>`;
                    $('#modal-summary').html(summaryHtml);

                    openModal();
                }
            });

            // Event listener untuk tombol "Download PDF"
            $('#download-pdf-btn').on('click', function() {
                const button = $(this);
                const originalContent = button.html();
                const printableContent = document.getElementById('modal-printable-content');
                const orderId = $('#modal-id').text().trim();
                const filename = `struk-${orderId}.pdf`;

                button.html('<i class="bi bi-hourglass-split"></i> Mengunduh...').prop('disabled', true);

                html2canvas(printableContent, {
                    scale: 2,
                    useCORS: true
                }).then(canvas => {
                    const imgData = canvas.toDataURL('image/png');
                    const pdf = new jspdf.jsPDF({
                        orientation: 'portrait',
                        unit: 'mm',
                        format: 'a4'
                    });
                    const pdfWidth = pdf.internal.pageSize.getWidth();
                    const pdfHeight = (canvas.height * pdfWidth) / canvas.width;
                    pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                    pdf.save(filename);
                }).catch(err => {
                    console.error("Gagal membuat PDF:", err);
                    Swal.fire('Error!', 'Gagal membuat file PDF.', 'error');
                }).finally(() => {
                    button.html(originalContent).prop('disabled', false);
                });
            });

            // Event listener untuk tombol "Cetak Struk"
            $('#print-invoice-btn').on('click', function() {
                window.print();
            });

            // === INISIALISASI HALAMAN ===
            function initializePage() {
                fetchSummary();
                refreshTables();
            }
            initializePage();
        });
    </script>
</body>

</html>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Riwayat Transaksi | CELVION</title>

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

    {{-- jsPDF & html2canvas untuk Cetak PDF --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    {{-- Chart.js untuk Grafik --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

        .status-tunai {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-ewallet {
            background-color: #e0e7ff;
            color: #3730a3;
        }

        .status-transfer {
            background-color: #dbeafe;
            color: #1e40af;
        }

        #transactionTable_wrapper .dt-search input,
        #transactionTable_wrapper .dt-length select,
        #payment-filter,
        #period-filter {
            background-color: white !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 0.5rem !important;
            padding: 0.5rem 0.75rem !important;
            outline: none;
        }

        #transactionTable_wrapper .dt-search input {
            padding-left: 2.25rem !important;
        }

        #transactionTable_wrapper .dt-paging .dt-paging-button.current {
            background: #4f46e5 !important;
            color: #ffffff !important;
        }

        #daily-summary-list::-webkit-scrollbar {
            width: 5px;
        }

        #daily-summary-list::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        #daily-summary-list::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 10px;
        }

        #daily-summary-list::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        /* PERBAIKAN: Styling untuk tombol detail */
        .detail-btn {
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 6px;
            transition: background-color 0.2s;
            display: inline-block;
        }

        .detail-btn:hover {
            background-color: #f3f4f6;
        }

        /* Pastikan elemen tabel responsive tidak menghalangi klik */
        .responsive-table td[data-label="Aksi"] {
            pointer-events: auto !important;
        }

        .responsive-table td[data-label="Aksi"] .detail-btn {
            display: inline-block;
            width: auto;
        }

        @media (max-width: 1023px) {
            #transactionTable_wrapper .dt-layout-row {
                flex-direction: column;
                gap: 1rem;
            }

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
                margin-bottom: 1rem;
                border: 1px solid #e2e8f0;
                border-radius: 0.75rem;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
                overflow: hidden;
                max-width: 400px;
                margin-left: auto;
                margin-right: auto;
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
            }

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

            #custom-filters,
            #transactionTable_wrapper .dt-search {
                width: 100%;
            }

            #custom-filters .flex-col,
            #custom-filters .filter-select {
                width: 100%;
            }

            #transactionTable_wrapper .dt-layout-cell.dt-start {
                width: 100%;
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
                    'title' => 'Riwayat Transaksi',
                    'subtitle' => 'Kelola dan lihat semua riwayat transaksi penjualan.',
                ])

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-8">

                    <div class="lg:col-span-3 flex flex-col gap-6">
                        <div class="bg-white p-6 rounded-2xl shadow-lg flex-grow flex flex-col" data-aos="fade-up">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-lg font-semibold text-gray-800">Tren Pemasukan</h4>
                                <div class="text-right">
                                    <p class="text-gray-500 text-sm">Total Pemasukan Periode Ini</p>
                                    <h3 id="summary-total-revenue" class="text-2xl font-bold text-gray-800">Rp0</h3>
                                </div>
                            </div>
                            <div class="relative flex-grow h-64">
                                <canvas id="revenueTrendChart"></canvas>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-2 gap-6" data-aos="fade-up" data-aos-delay="100">
                            <div class="bg-white p-5 rounded-2xl shadow-lg">
                                <div
                                    class="bg-blue-100 text-blue-600 w-12 h-12 flex items-center justify-center rounded-full text-2xl mb-3">
                                    <i class="bi bi-receipt"></i>
                                </div>
                                <p class="text-gray-500 text-sm">Total Transaksi</p>
                                <h3 id="summary-total-trx" class="text-2xl font-bold text-gray-800 mt-1">0</h3>
                            </div>
                            <div class="bg-white p-5 rounded-2xl shadow-lg">
                                <div
                                    class="bg-orange-100 text-orange-600 w-12 h-12 flex items-center justify-center rounded-full text-2xl mb-3">
                                    <i class="bi bi-arrow-left-right"></i>
                                </div>
                                <p class="text-gray-500 text-sm">Trade-In</p>
                                <h3 id="summary-trade-in-trx" class="text-2xl font-bold text-gray-800 mt-1">0</h3>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-2 flex flex-col gap-6">
                        <div class="bg-white p-6 rounded-2xl shadow-lg flex-grow flex flex-col" data-aos="fade-up"
                            data-aos-delay="200">
                            <h4 class="text-lg font-semibold text-gray-800 mb-2">Ringkasan Harian</h4>
                            <div id="daily-summary-list" class="flex-grow space-y-2 pr-2 overflow-y-auto max-h-[280px]">
                                {{-- Konten diisi oleh JavaScript --}}
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-2xl shadow-lg" data-aos="fade-up" data-aos-delay="300">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Metode Pembayaran</h4>
                            <div class="relative h-48 flex items-center justify-center">
                                <canvas id="paymentMethodChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-4 md:p-6 rounded-2xl shadow-lg" data-aos="fade-up" data-aos-delay="100">
                    <table id="transactionTable" class="w-full text-sm responsive-table" style="width:100%">
                        <thead class="bg-gray-50 text-gray-600 uppercase">
                            <tr>
                                <th class="p-4 text-left">ID Transaksi</th>
                                <th class="p-4 text-left">Detail Pesanan</th>
                                <th class="p-4 text-left">Tanggal</th>
                                <th class="p-4 text-center">Metode Bayar</th>
                                <th class="p-4 text-right">Total</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
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
                            <h3 class="text-xl font-bold text-gray-800">Detail Transaksi</h3>
                            <p id="modal-id" class="text-sm text-gray-500"></p>
                        </div>
                        <button class="close-modal p-2 print:hidden"><i class="bi bi-x-lg text-xl"></i></button>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-semibold text-gray-700 mb-2 border-b pb-2">Info Pelanggan</h4>
                                <div class="space-y-1 text-sm">
                                    <p><strong class="w-24 inline-block">Nama:</strong> <span
                                            id="modal-customer"></span></p>
                                    <p><strong class="w-24 inline-block">Telepon:</strong> <span
                                            id="modal-phone"></span></p>
                                    <p><strong class="w-24 inline-block">Email:</strong> <span id="modal-email"></span>
                                    </p>
                                    <p><strong class="w-24 inline-block">Metode:</strong> <span
                                            id="modal-method"></span></p>
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
            // Inisialisasi plugin dan variabel global
            AOS.init({
                duration: 600,
                once: true,
                offset: 20
            });
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const detailModal = $('#detail-modal');
            let transactionsData = []; // Variabel untuk menyimpan data dari server
            let revenueTrendChart, paymentMethodChart;

            // Fungsi untuk format currency - TAMBAHKAN INI
            function formatCurrency(amount) {
                return `Rp${parseFloat(amount || 0).toLocaleString('id-ID')}`;
            }

            // Inisialisasi DataTable
            const table = $('#transactionTable').DataTable({
                data: [], // Mulai dengan tabel kosong, akan diisi via AJAX
                dom: "<'flex flex-col lg:flex-row items-center justify-between gap-4 mb-4'<'dt-length'l><'flex flex-col sm:flex-row items-center gap-2'<'#custom-filters'><'dt-search'f>>>" +
                    "<'w-full't>" +
                    "<'flex flex-col md:flex-row items-center justify-between gap-4 mt-4'<'dt-info'i><'dt-paging'p>>",
                columns: [{
                        data: 'id',
                        className: 'font-bold text-indigo-600'
                    },
                    {
                        data: 'customer',
                        render: (data, type, row) =>
                            `<div class="flex items-center gap-3"><img src="${row.image}" class="w-12 h-12 rounded-md object-contain bg-gray-100"><div><p class="font-semibold text-gray-800">${row.items[0]?.name || 'N/A'}</p><p class="text-xs text-gray-500">Oleh: ${data}</p></div></div>`
                    },
                    {
                        data: 'date'
                    },
                    {
                        data: 'method'
                    },
                    {
                        data: 'total'
                    },
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false
                    }
                ],
                columnDefs: [{
                        targets: 3,
                        className: 'text-center',
                        render: data =>
                            `<span class="status-badge status-${(data || '').toLowerCase().replace(/[^a-z0-9]/g, '')}">${data}</span>`
                    },
                    {
                        targets: 4,
                        className: 'text-right font-semibold',
                        render: data => formatCurrency(data)
                    },
                    {
                        targets: 5,
                        className: 'text-center',
                        render: data =>
                            `<button data-id="${data}" class="detail-btn text-indigo-600 font-semibold hover:underline px-3 py-1 rounded-lg border border-indigo-200 hover:bg-indigo-50 transition-colors">Detail</button>`
                    }
                ],
                createdRow: function(row, data, dataIndex) {
                    $('td', row).eq(0).attr('data-label', 'ID Transaksi');
                    $('td', row).eq(1).attr('data-label', 'Detail Pesanan');
                    $('td', row).eq(2).attr('data-label', 'Tanggal');
                    $('td', row).eq(3).attr('data-label', 'Metode Bayar');
                    $('td', row).eq(4).attr('data-label', 'Total');
                    $('td', row).eq(5).attr('data-label', 'Aksi');
                },
                language: {
                    search: "",
                    searchPlaceholder: "Cari ID/Nama...",
                    lengthMenu: "Tampil _MENU_",
                    zeroRecords: "<div class='text-center p-10'><p>Tidak ada transaksi untuk periode ini.</p></div>",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 dari 0 data",
                    paginate: {
                        next: ">",
                        previous: "<"
                    }
                }
            });

            // --- FUNGSI DINAMISASI DATA ---

            // Menambahkan filter periode ke layout DataTable
            const customFilters =
                `<select id="period-filter" class="filter-select w-full sm:w-auto"><option value="today">Hari Ini</option><option value="7days" selected>7 Hari Terakhir</option><option value="this_month">Bulan Ini</option><option value="all">Semua Waktu</option></select>`;
            $(customFilters).appendTo("#custom-filters");
            $('#transactionTable_wrapper .dt-search').addClass('relative').find('input').before(
                '<i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>');

            // Fungsi utama untuk mengambil data dari server
            function fetchData(period = '7days') {
                Swal.fire({
                    title: 'Memuat Data...',
                    didOpen: () => Swal.showLoading(),
                    allowOutsideClick: false,
                    showConfirmButton: false
                });

                $.ajax({
                    url: "{{ route('kasir.riwayat.data') }}",
                    data: {
                        period: period
                    },
                    success: function(response) {
                        transactionsData = response.transactions;

                        // Update Summary Cards
                        $('#summary-total-revenue').text(formatCurrency(response.summary.totalRevenue ||
                            0));
                        $('#summary-total-trx').text(response.summary.totalTransactions || 0);
                        $('#summary-trade-in-trx').text(response.summary.tradeInCount || 0);

                        // Update Tabel DataTable
                        table.clear().rows.add(response.transactions).draw();

                        // Update semua grafik dan ringkasan harian
                        updateRevenueTrendChart(response.charts.dailySummaries);
                        updatePaymentMethodChart(response.charts.paymentMethods);
                        updateDailySummary(response.charts.dailySummaries);

                        Swal.close();
                    },
                    error: function(err) {
                        console.error("Gagal memuat data:", err);
                        Swal.fire('Error', 'Gagal memuat data riwayat dari server.', 'error');
                    }
                });
            }

            // --- FUNGSI UNTUK UPDATE UI (GRAFIK, DLL) ---

            function updateRevenueTrendChart(dailyData) {
                if (!dailyData) return;
                const chartLabels = dailyData.map(d => new Date(d.date).toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short'
                })).reverse();
                const chartData = dailyData.map(d => d.totalRevenue).reverse();

                const ctx = document.getElementById('revenueTrendChart').getContext('2d');
                if (revenueTrendChart) revenueTrendChart.destroy();

                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(79, 70, 229, 0.4)');
                gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');

                revenueTrendChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            label: 'Total Pemasukan',
                            data: chartData,
                            backgroundColor: gradient,
                            borderColor: '#4F46E5',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#4F46E5',
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx =>
                                        ` ${ctx.dataset.label}: ${formatCurrency(ctx.parsed.y)}`
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: val => formatCurrency(val)
                                }
                            }
                        }
                    }
                });
            }

            function updatePaymentMethodChart(paymentMethods) {
                if (!paymentMethods) return;
                const chartLabels = Object.keys(paymentMethods);
                const chartData = Object.values(paymentMethods);

                const ctx = document.getElementById('paymentMethodChart').getContext('2d');
                if (paymentMethodChart) paymentMethodChart.destroy();

                paymentMethodChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            data: chartData,
                            backgroundColor: ['#10B981', '#6366F1', '#3B82F6', '#F59E0B'],
                            borderColor: '#FFFFFF',
                            borderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    boxWidth: 8
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx => ` ${ctx.label}: ${ctx.raw} Transaksi`
                                }
                            }
                        }
                    }
                });
            }

            function updateDailySummary(dailySummaries) {
                const listContainer = $('#daily-summary-list');
                listContainer.empty();

                if (!dailySummaries || dailySummaries.length === 0) {
                    listContainer.html(
                        '<div class="text-center text-gray-500 py-10">Tidak ada data untuk periode ini.</div>');
                    return;
                }

                dailySummaries.forEach(stats => {
                    const formattedDate = new Date(stats.date).toLocaleDateString('id-ID', {
                        weekday: 'long',
                        day: 'numeric',
                        month: 'short'
                    });
                    const itemHtml = `
                <div class="flex justify-between items-center py-3 border-b border-gray-100 last:border-b-0">
                    <div><p class="font-semibold text-gray-800 text-sm">${formattedDate}</p><p class="text-xs text-gray-500">${stats.transactionCount} Transaksi</p></div>
                    <p class="font-bold text-green-600 text-sm">${formatCurrency(stats.totalRevenue)}</p>
                </div>`;
                    listContainer.append(itemHtml);
                });
            }

            // --- EVENT LISTENERS ---

            $('#period-filter').on('change', function() {
                fetchData($(this).val());
            });

            // Fungsi untuk menampilkan dan menyembunyikan modal
            function openModal() {
                detailModal.removeClass('hidden');
                setTimeout(() => {
                    detailModal.removeClass('opacity-0');
                    detailModal.find('.modal-content').removeClass('opacity-0 scale-95');
                }, 10);
                $('body').addClass('overflow-hidden');
            }

            function closeModal() {
                detailModal.addClass('opacity-0');
                detailModal.find('.modal-content').addClass('opacity-0 scale-95');
                setTimeout(() => {
                    detailModal.addClass('hidden');
                }, 300);
                $('body').removeClass('overflow-hidden');
            }

            // PERBAIKAN: Event listener untuk tombol Detail di tabel - menggunakan event delegation
            $(document).on('click', '.detail-btn', function() {
                console.log("Tombol Detail diklik!"); // Log 1: Cek apakah event terpicu

                const trxId = $(this).data('id');
                console.log("Mencari transaksi dengan ID:", trxId); // Log 2: Cek ID yang diambil

                // Pastikan transactionsData sudah terisi
                if (!transactionsData || transactionsData.length === 0) {
                    console.error("transactionsData kosong atau belum dimuat!");
                    Swal.fire('Error', 'Data transaksi belum siap. Silakan coba lagi.', 'warning');
                    return; // Hentikan jika data belum ada
                }
                console.log("Data Tersedia:", transactionsData); // Log 3: Lihat isi data

                // Cari data berdasarkan ID unik (bisa invoice number atau order number)
                const data = transactionsData.find(t => t.id === trxId);

                if (data) {
                    console.log("Data ditemukan:", data); // Log 4: Cek data yang ditemukan

                    // Mengisi data umum modal
                    $('#modal-id').text(data.id);
                    $('#modal-customer').text(data.customer || '-');
                    $('#modal-phone').text(data.phone || '-');
                    $('#modal-email').text(data.email || '-');
                    $('#modal-date').text(new Date(data.date).toLocaleString('id-ID', {
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    }));
                    $('#modal-method').text(data.method);

                    // Mengisi rincian barang
                    let itemsHtml = data.items.map(item => `
                        <div class="flex justify-between items-start text-sm mb-2">
                            <div>
                                <p class="font-semibold text-gray-800">${item.name} (x${item.qty})</p>
                                <p class="text-xs text-gray-500">${item.variant_info || ''}</p>
                            </div>
                            <p class="font-semibold text-gray-800">${formatCurrency(item.subtotal)}</p>
                        </div>`).join('');
                    $('#modal-items').html(itemsHtml);

                    // Mengisi ringkasan pembayaran
                    let summaryHtml =
                        `<div class="flex justify-between text-sm"><span class="text-gray-600">Subtotal:</span><span>${formatCurrency(data.subtotal)}</span></div>`;
                    if (data.discount_amount && data.discount_amount > 0) {
                        summaryHtml +=
                            `<div class="flex justify-between text-sm text-red-600"><span class="text-gray-600">Diskon ${data.voucher_code ? '('+data.voucher_code+')' : ''}:</span><span>- ${formatCurrency(data.discount_amount)}</span></div>`;
                    }
                    summaryHtml +=
                        `<div class="flex justify-between text-sm"><span class="text-gray-600">Pengiriman:</span><span>Gratis</span></div>`; // Asumsi
                    summaryHtml +=
                        `<div class="border-t my-2"></div><div class="flex justify-between font-bold"><span class="text-gray-800">Total:</span><span class="text-indigo-600">${formatCurrency(data.total)}</span></div>`;
                    $('#modal-summary').html(summaryHtml);

                    openModal(); // Buka modal
                } else {
                    console.error("Data transaksi TIDAK ditemukan untuk ID:",
                    trxId); // Log 5: Jika tidak ketemu
                    Swal.fire('Error', `Detail transaksi untuk ID ${trxId} tidak ditemukan.`, 'error');
                }
            });

            $('.close-modal').on('click', closeModal);

            $('#print-invoice-btn').on('click', () => window.print());

            $('#download-pdf-btn').on('click', function() {
                const btn = $(this);
                const originalText = btn.html();
                btn.html('<i class="bi bi-hourglass-split"></i> Mengunduh...').prop('disabled', true);

                const printContent = document.getElementById('modal-printable-content');
                html2canvas(printContent, {
                    scale: 2,
                    useCORS: true
                }).then(canvas => {
                    const imgData = canvas.toDataURL('image/png');
                    const {
                        jsPDF
                    } = window.jspdf;
                    const pdf = new jsPDF({
                        orientation: 'p',
                        unit: 'mm',
                        format: 'a5'
                    });
                    const pdfWidth = pdf.internal.pageSize.getWidth();
                    const pdfHeight = (canvas.height * pdfWidth) / canvas.width;
                    pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                    pdf.save(`struk-${$('#modal-id').text()}.pdf`);
                }).finally(() => {
                    btn.html(originalText).prop('disabled', false);
                });
            });

            // Panggil data pertama kali saat halaman dimuat
            fetchData($('#period-filter').val());
        });
    </script>
</body>

</html>

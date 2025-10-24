<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Laporan Kasir | CELVION</title>

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

        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s cubic-bezier(0, 1, 0, 1);
        }

        .accordion-header.active+.accordion-content {
            max-height: 5000px;
            /* Nilai besar agar cukup untuk konten */
            transition: max-height 1s ease-in-out;
        }

        .accordion-header.active .chevron-icon {
            transform: rotate(180deg);
        }

        .chevron-icon {
            transition: transform 0.3s ease;
        }

        @media print {

            /* Sembunyikan semua elemen di body secara default */
            body>*:not(#detail-modal) {
                display: none;
            }

            /* Tampilkan hanya modal dan kontennya */
            #detail-modal {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                overflow: visible;
                background-color: white;
                display: block !important;
                opacity: 1 !important;
            }

            /* Hapus bayangan dan batasan tinggi pada modal */
            .modal-content {
                box-shadow: none;
                border: none;
                max-height: none;
                transform: none !important;
                opacity: 1 !important;
            }
        }
    </style>
</head>

<body>
    <div class="relative min-h-screen md:flex">

        @include('kasir.partials.sidebar')

        <div class="flex-1 md:ml-64 flex flex-col min-w-0">
            <main class="flex-grow p-4 sm:p-6 lg:p-8">

                <div class="mb-8" data-aos="fade-down">
                    @include('kasir.partials.header', [
                        'title' => 'Laporan Kasir',
                        'subtitle' => 'Lihat ringkasan dan detail laporan penjualan per periode.',
                    ])
                    <button id="download-report-btn"
                        class="mt-4 px-5 py-2.5 bg-indigo-600 text-white rounded-lg font-semibold flex items-center gap-2 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="bi bi-file-earmark-arrow-down-fill"></i>
                        <span>Download Laporan (PDF)</span>
                    </button>
                </div>

                {{-- Filter Tahun & Bulan --}}
                <div class="mb-6" data-aos="fade-up">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="w-full sm:w-1/3">
                            <label for="year-filter" class="block text-sm font-medium text-gray-700 mb-1">Pilih
                                Tahun</label>
                            <select id="year-filter"
                                class="w-full bg-white border border-gray-300 rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"></select>
                        </div>
                        <div class="w-full sm:w-1/3">
                            <label for="month-filter" class="block text-sm font-medium text-gray-700 mb-1">Pilih
                                Bulan</label>
                            <select id="month-filter"
                                class="w-full bg-white border border-gray-300 rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"></select>
                        </div>
                    </div>
                </div>

                {{-- Kontainer untuk Laporan Bulanan --}}
                <div id="laporan-container" class="space-y-6">
                    {{-- Konten laporan akan di-generate oleh JavaScript --}}
                </div>

            </main>
        </div>
    </div>

    {{-- Modal Detail Transaksi (Tidak diubah, tetap sama) --}}
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
                                    <p><strong class="w-24 inline-block">Tanggal:</strong> <span id="modal-date"></span>
                                    </p>
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
    <script>
        $(document).ready(function() {
            AOS.init({
                duration: 600,
                once: true,
                offset: 20
            });

            // Menerima kedua set data dari controller
            const transactions = @json($transactions);
            const tradeIns = @json($tradeIns);

            const detailModal = $('#detail-modal');

            const formatCurrency = (amount) => `Rp${Number(amount).toLocaleString('id-ID')}`;

            function generateReport(selectedYear, selectedMonth = 'all') {
                const container = $('#laporan-container');
                container.empty();

                // Filter data berdasarkan periode yang dipilih
                let filteredTransactions = transactions.filter(t => new Date(t.date).getFullYear() == selectedYear);
                let filteredTradeIns = tradeIns.filter(ti => new Date(ti.date).getFullYear() == selectedYear);

                if (selectedMonth !== 'all') {
                    filteredTransactions = filteredTransactions.filter(t => new Date(t.date).getMonth() ==
                        selectedMonth);
                    filteredTradeIns = filteredTradeIns.filter(ti => new Date(ti.date).getMonth() == selectedMonth);
                }

                // Tampilkan pesan jika tidak ada data sama sekali
                if (filteredTransactions.length === 0 && filteredTradeIns.length === 0) {
                    container.html(
                        `<div class="text-center bg-white p-10 rounded-2xl shadow-lg"><p class="text-gray-500">Tidak ada data untuk periode yang dipilih.</p></div>`
                    );
                    return;
                }

                // Kelompokkan data transaksi per bulan
                const monthlyData = filteredTransactions.reduce((acc, trx) => {
                    const month = new Date(trx.date).getMonth();
                    if (!acc[month]) {
                        acc[month] = {
                            transactions: [],
                            totalRevenue: 0,
                            // [PERBAIKAN] Menambahkan semua kemungkinan metode pembayaran untuk dihitung
                            paymentMethods: {
                                'Tunai': 0,
                                'E-Wallet': 0,
                                'Transfer Bank': 0,
                                'COD': 0
                            }
                        };
                    }
                    acc[month].transactions.push(trx);
                    acc[month].totalRevenue += trx.total;

                    let normalizedMethod = trx.method;
                    if (['Transfer', 'Virtual Bank'].includes(normalizedMethod)) {
                        normalizedMethod = 'Transfer Bank';
                    }

                    if (acc[month].paymentMethods.hasOwnProperty(normalizedMethod)) {
                        acc[month].paymentMethods[normalizedMethod]++;
                    }
                    return acc;
                }, {});

                // [PERBAIKAN] Dapatkan semua bulan unik dari GABUNGAN transaksi dan trade-in
                const transactionMonths = filteredTransactions.map(t => new Date(t.date).getMonth());
                const tradeInMonths = filteredTradeIns.map(t => new Date(t.date).getMonth());
                const allMonths = [...new Set([...transactionMonths, ...tradeInMonths])];
                const sortedMonths = allMonths.sort((a, b) => b - a);

                sortedMonths.forEach(monthIndex => {
                    // [PERBAIKAN] Ambil data secara aman, tangani kasus jika bulan hanya memiliki trade-in
                    const data = monthlyData[monthIndex] || {
                        transactions: [],
                        totalRevenue: 0,
                        paymentMethods: {}
                    };
                    const monthName = new Date(selectedYear, monthIndex).toLocaleDateString('id-ID', {
                        month: 'long'
                    });
                    const chartId = `paymentChart-${selectedYear}-${monthIndex}`;
                    const tradeInCountForMonth = filteredTradeIns.filter(ti => new Date(ti.date)
                        .getMonth() == monthIndex).length;

                    const reportHtml = `
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden" data-aos="fade-up">
                    <div class="accordion-header p-5 cursor-pointer flex justify-between items-center border-b border-gray-200">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">${monthName} ${selectedYear}</h3>
                            <p class="text-sm text-gray-500">Total Pemasukan: <span class="font-semibold text-green-600">${formatCurrency(data.totalRevenue)}</span></p>
                        </div>
                        <i class="bi bi-chevron-down text-xl text-gray-500 chevron-icon"></i>
                    </div>
                    <div class="accordion-content">
                        <div class="p-5">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                                <div class="md:col-span-2 grid grid-cols-2 gap-6">
                                    <div class="bg-gray-50 p-4 rounded-xl"><div class="bg-blue-100 text-blue-600 w-10 h-10 flex items-center justify-center rounded-full text-xl mb-2"><i class="bi bi-receipt"></i></div><p class="text-gray-500 text-xs">Total Transaksi</p><h3 class="text-xl font-bold text-gray-800">${data.transactions.length}</h3></div>
                                    <div class="bg-gray-50 p-4 rounded-xl"><div class="bg-orange-100 text-orange-600 w-10 h-10 flex items-center justify-center rounded-full text-xl mb-2"><i class="bi bi-arrow-left-right"></i></div><p class="text-gray-500 text-xs">Trade-In</p><h3 class="text-xl font-bold text-gray-800">${tradeInCountForMonth}</h3></div>
                                </div>
                                <div class="md:col-span-2 bg-gray-50 p-4 rounded-xl"><h4 class="text-sm font-semibold text-gray-700 mb-2 text-center">Metode Pembayaran</h4><div class="relative h-32"><canvas id="${chartId}"></canvas></div></div>
                            </div>
                            <h4 class="font-semibold text-gray-700 mb-3">Detail Transaksi Bulan Ini</h4>
                            <div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-gray-100 text-gray-600"><tr><th class="p-3 text-left">ID</th><th class="p-3 text-left">Tanggal</th><th class="p-3 text-left">Pelanggan</th><th class="p-3 text-center">Metode</th><th class="p-3 text-right">Total</th><th class="p-3 text-center">Aksi</th></tr></thead>
                            <tbody>
                                ${data.transactions.sort((a, b) => new Date(b.date) - new Date(a.date)).map(trx => `
                                            <tr class="border-b">
                                                <td class="p-3 font-medium text-indigo-600">${trx.id}</td>
                                                <td class="p-3">${new Date(trx.date).toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric'})}</td>
                                                <td class="p-3">${trx.customer}</td>
                                                <td class="p-3 text-center"><span class="status-badge status-${trx.method.toLowerCase().replace(/[^a-z0-9]/g, '')}">${trx.method}</span></td>
                                                <td class="p-3 text-right font-semibold">${formatCurrency(trx.total)}</td>
                                                <td class="p-3 text-center"><button data-id="${trx.id}" class="detail-btn text-indigo-600 font-semibold hover:underline text-xs">Lihat Detail</button></td>
                                            </tr>
                                        `).join('')}
                            </tbody></table></div>
                        </div>
                    </div>
                </div>
                `;
                    container.append(reportHtml);
                    renderPaymentMethodChart(chartId, data.paymentMethods);
                });
                AOS.refresh();
            }

            function renderPaymentMethodChart(canvasId, data) {
                const ctx = document.getElementById(canvasId);
                if (!ctx) return;
                new Chart(ctx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(data),
                        datasets: [{
                            data: Object.values(data),
                            backgroundColor: ['#10B981', '#6366F1', '#3B82F6', '#F59E0B'],
                            borderColor: '#f9fafb',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                display: false
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

            function updateMonthFilter(selectedYear) {
                const monthFilter = $('#month-filter');
                monthFilter.empty().append('<option value="all">Semua Bulan</option>');

                // [PERBAIKAN] Ambil bulan unik dari gabungan transaksi dan trade-in
                const transactionMonths = transactions.filter(t => new Date(t.date).getFullYear() == selectedYear)
                    .map(t => new Date(t.date).getMonth());
                const tradeInMonths = tradeIns.filter(t => new Date(t.date).getFullYear() == selectedYear).map(t =>
                    new Date(t.date).getMonth());
                const monthsInYear = [...new Set([...transactionMonths, ...tradeInMonths])].sort((a, b) => a - b);

                monthsInYear.forEach(monthIndex => {
                    const monthName = new Date(selectedYear, monthIndex).toLocaleDateString('id-ID', {
                        month: 'long'
                    });
                    monthFilter.append(`<option value="${monthIndex}">${monthName}</option>`);
                });
            }

            // --- EVENT HANDLERS ---
            $('#laporan-container').on('click', '.accordion-header', function() {
                $(this).toggleClass('active');
            });
            $('#year-filter').on('change', function() {
                const selectedYear = $(this).val();
                updateMonthFilter(selectedYear);
                generateReport(selectedYear);
            });
            $('#month-filter').on('change', function() {
                generateReport($('#year-filter').val(), $(this).val());
            });
            $('#download-report-btn').on('click', function() {
                const year = $('#year-filter').val();
                const month = $('#month-filter').val();
                if (!year) {
                    Swal.fire('Error', 'Silakan pilih tahun terlebih dahulu.', 'error');
                    return;
                }
                window.open(`{{ route('kasir.laporan.download') }}?year=${year}&month=${month}`, '_blank');
            });

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

            $('#laporan-container').on('click', '.detail-btn', function() {
                const trxId = $(this).data('id');
                const data = transactions.find(t => t.id === trxId);
                if (data) {
                    $('#modal-id').text(data.id);
                    $('#modal-customer').text(data.customer || '-');
                    $('#modal-phone').text(data.phone || '-');
                    $('#modal-email').text(data.email || '-');
                    $('#modal-date').text(new Date(data.date).toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric'
                    }));
                    $('#modal-method').text(data.method);

                    let subtotal = data.items.reduce((sum, item) => sum + (item.price * item.qty), 0);

                    $('#modal-items').html(data.items.map(item =>
                        `<div class="flex justify-between text-sm"><p>${item.name} (x${item.qty})</p><p>${formatCurrency(item.price * item.qty)}</p></div>`
                    ).join(''));

                    // [PERBAIKAN] Menghapus sisa kode trade-in dari detail transaksi individu
                    let summaryHtml =
                        `<div class="flex justify-between text-sm"><span class="text-gray-600">Subtotal:</span><span>${formatCurrency(subtotal)}</span></div>`;
                    summaryHtml +=
                        `<div class="border-t my-2"></div><div class="flex justify-between font-bold"><span class="text-gray-800">Total:</span><span>${formatCurrency(data.total)}</span></div>`;
                    $('#modal-summary').html(summaryHtml);

                    openModal();
                }
            });

            $('.close-modal').on('click', closeModal);
            $('#download-pdf-btn').on('click', function() {
                const printContent = document.getElementById('modal-printable-content');
                html2canvas(printContent).then(canvas => {
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
                });
            });
            $('#print-invoice-btn').on('click', () => window.print());

            function initializePage() {
                const yearFilter = $('#year-filter');
                // [PERBAIKAN] Ambil tahun unik dari gabungan transaksi dan trade-in
                const transactionYears = transactions.map(t => new Date(t.date).getFullYear());
                const tradeInYears = tradeIns.map(t => new Date(t.date).getFullYear());
                const years = [...new Set([...transactionYears, ...tradeInYears])].sort((a, b) => b - a);

                years.forEach(year => yearFilter.append(`<option value="${year}">${year}</option>`));

                if (years.length > 0) {
                    const latestYear = years[0];
                    yearFilter.val(latestYear); // Set nilai dropdown ke tahun terbaru
                    updateMonthFilter(latestYear);
                    generateReport(latestYear); // Tampilkan laporan untuk tahun terbaru
                } else {
                    $('#laporan-container').html(
                        '<div class="text-center bg-white p-10 rounded-2xl shadow-lg"><p class="text-gray-500">Belum ada data transaksi atau trade-in.</p></div>'
                    );
                }
            }

            initializePage();
        });
    </script>
</body>

</html>

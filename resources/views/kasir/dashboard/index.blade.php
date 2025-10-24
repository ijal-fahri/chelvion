<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Kasir | CELVION</title>

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

    {{-- jsPDF & html2canvas untuk Cetak PDF (disimpan untuk modal) --}}
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
    </style>
</head>

<body>
    <div class="relative min-h-screen md:flex">

        @include('kasir.partials.sidebar')

        <div class="flex-1 md:ml-64 flex flex-col min-w-0">
            <main class="flex-grow p-4 sm:p-6 lg:p-8">

                @include('kasir.partials.header', [
                    'title' => 'Dashboard Kasir',
                    'subtitle' => 'Selamat Datang! Berikut adalah ringkasan aktivitas penjualan Anda hari ini.',
                ])

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="lg:col-span-3 grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center gap-5">
                            <div class="bg-green-100 text-green-600 p-3 rounded-full"><i
                                    class="bi bi-wallet2 text-2xl"></i></div>
                            <div>
                                <p class="text-sm text-gray-500">Pendapatan Hari Ini</p>
                                <p class="text-2xl font-bold text-gray-800">
                                    {{ 'Rp' . number_format($todaysRevenue, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center gap-5">
                            <div class="bg-blue-100 text-blue-600 p-3 rounded-full"><i
                                    class="bi bi-receipt-cutoff text-2xl"></i></div>
                            <div>
                                <p class="text-sm text-gray-500">Transaksi Hari Ini</p>
                                <p class="text-2xl font-bold text-gray-800">
                                    {{ $todaysTransactions }}
                                </p>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center gap-5">
                            <div class="bg-purple-100 text-purple-600 p-3 rounded-full"><i
                                    class="bi bi-box-seam text-2xl"></i></div>
                            <div>
                                <p class="text-sm text-gray-500">Produk Terjual Hari Ini</p>
                                <p class="text-2xl font-bold text-gray-800">
                                    {{ $todaysProductsSold }}
                                </p>
                            </div>
                        </div>

                    </div>
                    <a href="{{ url('/kasir/transaksi') }}" class="block" data-aos="fade-up" data-aos-delay="300">
                        <div
                            class="bg-indigo-600 h-full p-6 rounded-2xl shadow-lg text-white flex flex-col items-center justify-center text-center hover:bg-indigo-700 transition-colors">
                            <i class="bi bi-plus-circle-fill text-5xl mb-3"></i>
                            <h4 class="text-lg font-semibold">Buat Transaksi Baru</h4>
                            <p class="text-sm opacity-80">Mulai proses penjualan untuk pelanggan.</p>
                        </div>
                    </a>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 flex flex-col gap-6">
                        <div class="bg-white p-6 rounded-2xl shadow-lg" data-aos="fade-up">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Tren Penjualan 7 Hari Terakhir </h4>
                            <div class="relative h-72">
                                <canvas id="salesTrendChart"></canvas>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-2xl shadow-lg" data-aos="fade-up" data-aos-delay="100">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Transaksi Terakhir</h4>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50 text-gray-600">
                                        <tr>
                                            <th class="p-3 text-left">ID Transaksi</th>
                                            <th class="p-3 text-left">Pelanggan</th>
                                            <th class="p-3 text-left">Tanggal</th>
                                            <th class="p-3 text-right">Total</th>
                                            <th class="p-3 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recent-transactions-body">
                                        {{-- Konten diisi oleh JavaScript --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-1 flex flex-col gap-6">
                        <div class="bg-white p-6 rounded-2xl shadow-lg" data-aos="fade-up" data-aos-delay="150">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Metode Pembayaran Hari Ini</h4>
                            <div class="relative h-48 flex items-center justify-center">
                                <canvas id="paymentMethodChart"></canvas>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-2xl shadow-lg" data-aos="fade-up" data-aos-delay="250">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Produk Terlaris Hari Ini</h4>
                            <ul id="top-products-list" class="space-y-4">
                                {{-- Konten diisi oleh JavaScript --}}
                            </ul>
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
    <script>
        $(document).ready(function() {
            AOS.init({
                duration: 600,
                once: true,
                offset: 20
            });

            // Ambil data dinamis dari Controller yang di-pass ke view
            const salesChartLabels = @json($chartLabels);
            const salesChartData = @json($chartData);
            const paymentMethodData = @json($paymentMethodData);
            const topProducts = @json($topProducts);
            const recentTransactions = @json($recentTransactions);
            const detailModal = $('#detail-modal');

            // Fungsi helper untuk format mata uang Rupiah
            const formatCurrency = (amount) => {
                // Mengubah amount menjadi number untuk memastikan formatnya benar
                const numericAmount = Number(amount) || 0;
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(numericAmount);
            };

            // --- FUNGSI UNTUK MERENDER GRAFIK ---

            function renderSalesTrendChart(labels, data) {
                const salesChartCanvas = document.getElementById('salesTrendChart');
                if (!salesChartCanvas) {
                    console.error("Elemen canvas #salesTrendChart tidak ditemukan.");
                    return;
                }
                const ctx = salesChartCanvas.getContext('2d');

                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(79, 70, 229, 0.4)');
                gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Penjualan',
                            data: data,
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
                                    label: ctx => ` ${ctx.dataset.label}: ${formatCurrency(ctx.parsed.y)}`
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: val => (val >= 1e6) ? `${val / 1e6} Jt` : `${val / 1e3} Rb`
                                }
                            }
                        }
                    }
                });
            }

            function renderPaymentMethodChart(data) {
                const paymentChartCanvas = document.getElementById('paymentMethodChart');
                if (!paymentChartCanvas) {
                    console.error("Elemen canvas #paymentMethodChart tidak ditemukan.");
                    return;
                }

                if (!data || Object.keys(data).length === 0) {
                    $(paymentChartCanvas).parent().html(
                        '<p class="text-center text-sm text-gray-500">Belum ada data pembayaran hari ini.</p>');
                    return;
                }
                const ctx = paymentChartCanvas.getContext('2d');

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(data),
                        datasets: [{
                            data: Object.values(data),
                            backgroundColor: ['#10B981', '#6366F1', '#3B82F6', '#F59E0B',
                                '#EF4444'
                            ],
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
                                    boxWidth: 8,
                                    padding: 15
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

            // --- FUNGSI UNTUK MENAMPILKAN DATA TABEL & LIST ---

            function displayRecentTransactions(transactions) {
                const container = $('#recent-transactions-body');
                container.empty();
                if (!transactions || transactions.length === 0) {
                    container.html(
                        '<tr><td colspan="5" class="text-center p-10 text-gray-500">Belum ada transaksi hari ini.</td></tr>'
                    );
                    return;
                }
                transactions.forEach(trx => {
                    const transactionDate = new Date(trx.created_at);
                    const formattedDate = transactionDate.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });
                    const rowHtml = `
                    <tr class="border-b last:border-0 hover:bg-gray-50">
                        <td class="p-3 font-medium text-indigo-600">${trx.invoice_number}</td>
                        <td class="p-3 text-gray-700">${trx.customer_name}</td>
                        <td class="p-3 text-gray-500">${formattedDate}</td>
                        <td class="p-3 text-right font-semibold">${formatCurrency(trx.total_amount)}</td>
                        <td class="p-3 text-center"><button data-id="${trx.id}" class="detail-btn text-indigo-600 font-semibold hover:underline text-xs">Detail</button></td>
                    </tr>`;
                    container.append(rowHtml);
                });
            }

            function displayTopProducts(products) {
                const container = $('#top-products-list');
                container.empty();
                if (!products || products.length === 0) {
                    container.html(
                        '<p class="text-center text-gray-500 py-4">Belum ada produk terjual hari ini.</p>');
                    return;
                }
                products.forEach(product => {
                    const itemHtml =
                        `<li class="flex items-center justify-between"><p class="text-gray-700 font-medium text-sm">${product.product_name}</p><span class="bg-gray-100 text-gray-800 font-bold text-xs px-2 py-1 rounded-md">${product.total_sold} Terjual</span></li>`;
                    container.append(itemHtml);
                });
            }

            // --- FUNGSI UNTUK MODAL DETAIL ---

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

            // [+++ PERUBAHAN DI SINI +++] Event listener tombol Detail
            $('#recent-transactions-body').on('click', '.detail-btn', function() {
                const trxId = $(this).data('id');
                // Cari data berdasarkan ID asli (trx.id), bukan invoice_number
                const data = recentTransactions.find(t => t.id === trxId);
                if (data) {
                    // Mengisi data umum modal (tidak berubah)
                    $('#modal-id').text(data.invoice_number); // Tampilkan invoice/order number
                    $('#modal-customer').text(data.customer_name);
                    $('#modal-phone').text(data.customer_phone || '-');
                    $('#modal-email').text(data.customer_email || '-');
                    $('#modal-method').text(data.payment_method);
                    const transactionDate = new Date(data.created_at);
                    $('#modal-date').text(transactionDate.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    }));

                    // Mengisi rincian barang (tidak berubah)
                    let itemsHtml = data.items.map(item => `
                        <div class="flex justify-between items-start text-sm mb-2">
                            <div>
                                <p class="font-semibold text-gray-800">${item.product_name} (x${item.quantity})</p>
                                <p class="text-xs text-gray-500">${item.variant_info || ''}</p>
                            </div>
                            <p class="font-semibold text-gray-800">${formatCurrency(item.subtotal)}</p>
                        </div>`).join('');
                    $('#modal-items').html(itemsHtml);

                    // [+++ PERUBAHAN DI SINI +++] Mengisi ringkasan pembayaran DENGAN DISKON
                    let summaryHtml =
                        `<div class="flex justify-between text-sm"><span class="text-gray-600">Subtotal:</span><span>${formatCurrency(data.subtotal)}</span></div>`; // Gunakan subtotal dari data

                    if (data.discount_amount && data.discount_amount > 0) {
                        summaryHtml +=
                            `<div class="flex justify-between text-sm text-red-600"><span class="text-gray-600">Diskon ${data.voucher_code ? '('+data.voucher_code+')' : ''}:</span><span>- ${formatCurrency(data.discount_amount)}</span></div>`;
                    }
                    summaryHtml +=
                        `<div class="flex justify-between text-sm"><span class="text-gray-600">Pengiriman:</span><span>Gratis</span></div>`; // Asumsi pengiriman gratis

                    summaryHtml +=
                        `<div class="border-t my-2"></div><div class="flex justify-between font-bold"><span class="text-gray-800">Total:</span><span class="text-indigo-600">${formatCurrency(data.total_amount)}</span></div>`; // Total akhir (gunakan nama field yang konsisten)
                    // [+++ AKHIR PERUBAHAN +++]

                    $('#modal-summary').html(summaryHtml);
                    openModal();
                } else {
                    console.error("Data transaksi tidak ditemukan untuk ID:", trxId);
                    Swal.fire('Error', 'Detail transaksi tidak ditemukan.', 'error');
                }
            });
            
            $('.close-modal').on('click', closeModal);

            // --- FUNGSI CETAK & DOWNLOAD PDF ---

            $('#download-pdf-btn').on('click', function() {
                const {
                    jsPDF
                } = window.jspdf;
                const pdf = new jsPDF({
                    orientation: 'p',
                    unit: 'mm',
                    format: 'a5'
                });
                const printContent = document.getElementById('modal-printable-content');
                html2canvas(printContent, {
                    scale: 2
                }).then(canvas => {
                    const imgData = canvas.toDataURL('image/png');
                    const pdfWidth = pdf.internal.pageSize.getWidth();
                    const pdfHeight = (canvas.height * pdfWidth) / canvas.width;
                    pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                    pdf.save(`struk-${$('#modal-id').text()}.pdf`);
                });
            });

            $('#print-invoice-btn').on('click', () => Swal.fire({
                icon: 'info',
                title: 'Fitur Dalam Pengembangan',
                text: 'Fitur cetak struk langsung ke printer akan segera tersedia.',
                confirmButtonColor: '#4f46e5'
            }));

            // --- INISIALISASI SEMUA FUNGSI SAAT HALAMAN SIAP ---

            renderSalesTrendChart(salesChartLabels, salesChartData);
            renderPaymentMethodChart(paymentMethodData);
            displayRecentTransactions(recentTransactions);
            displayTopProducts(topProducts);
        });
    </script>
</body>

</html>

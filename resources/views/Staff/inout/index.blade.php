<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Monitoring Logistik Barang | CELVION</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Tailwind CSS & jQuery --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- Ikon & Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

    {{-- Animasi AOS & SweetAlert2 --}}
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
        }

        .tab-button.active {
            border-color: #4f46e5;
            color: #4f46e5;
            background-color: #eef2ff;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-masuk {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-keluar {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-permintaan {
            background-color: #eef2ff;
            color: #4338ca;
        }

        .status-ditolak {
            background-color: #e2e8f0;
            color: #475569;
        }

        .modal-overlay {
            transition: opacity 0.3s ease;
        }

        .modal-content {
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        @media (max-width: 767px) {
            .responsive-table thead {
                display: none;
            }

            .responsive-table tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid #e5e7eb;
                border-radius: 0.75rem;
                box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.05);
                overflow: hidden;
                background: white;
            }

            .responsive-table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.75rem 1rem;
                text-align: right;
                border-bottom: 1px solid #f3f4f6;
            }

            .responsive-table td:last-child {
                border-bottom: none;
            }

            .responsive-table td::before {
                content: attr(data-label);
                font-weight: 600;
                text-align: left;
                margin-right: 1rem;
                color: #4b5563;
            }
        }
    </style>
</head>

<body>
    <div class="relative min-h-screen md:flex">

        @include('staff.partials.sidebar')

        <div class="flex-1 md:ml-64 flex flex-col min-w-0">
            <main class="flex-grow p-4 sm:p-6 lg:p-8">

                @include('staff.partials.header', [
                    'title' => 'Monitoring Logistik',
                    'subtitle' => 'Pantau semua barang yang masuk dan keluar dari gudang.',
                ])

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8" data-aos="fade-up">
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 font-medium">Masuk (Hari Ini)</p>
                            <h3 id="summary-masuk" class="text-3xl font-bold text-gray-800 mt-1">0</h3>
                        </div>
                        <div class="bg-green-100 text-green-600 p-4 rounded-full text-2xl"><i
                                class="bi bi-box-arrow-in-down"></i></div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 font-medium">Keluar (Hari Ini)</p>
                            <h3 id="summary-keluar" class="text-3xl font-bold text-gray-800 mt-1">0</h3>
                        </div>
                        <div class="bg-red-100 text-red-600 p-4 rounded-full text-2xl"><i
                                class="bi bi-box-arrow-up"></i></div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 font-medium">Stok Kritis</p>
                            <h3 id="summary-kritis" class="text-3xl font-bold text-gray-800 mt-1">0</h3>
                        </div>
                        <div class="bg-amber-100 text-amber-600 p-4 rounded-full text-2xl"><i
                                class="bi bi-exclamation-triangle-fill"></i></div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 font-medium">Perlu Diproses</p>
                            <h3 id="summary-proses" class="text-3xl font-bold text-gray-800 mt-1">0</h3>
                        </div>
                        <div class="bg-indigo-100 text-indigo-600 p-4 rounded-full text-2xl"><i
                                class="bi bi-hourglass-split"></i></div>
                    </div>
                </div>

                <div class="bg-white p-4 md:p-6 rounded-2xl shadow-lg" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
                        <div class="flex border-2 border-gray-200 rounded-lg p-1 bg-gray-100 flex-wrap">
                            <button class="tab-button active font-semibold text-sm px-5 py-2 rounded-md"
                                data-tab="all">Semua</button>
                            <button class="tab-button font-semibold text-sm px-5 py-2 rounded-md"
                                data-tab="masuk">Masuk</button>
                            <button class="tab-button font-semibold text-sm px-5 py-2 rounded-md"
                                data-tab="keluar">Keluar</button>
                            <button class="tab-button font-semibold text-sm px-5 py-2 rounded-md"
                                data-tab="permintaan">Permintaan</button>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                            <input type="date" id="date-filter"
                                class="w-full sm:w-auto px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                            <div class="relative w-full sm:w-auto">
                                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input type="text" id="search-filter" placeholder="Cari..."
                                    class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm responsive-table">
                            <thead class="bg-gray-50 text-gray-600 uppercase">
                                <tr>
                                    <th class="p-4 text-left">Detail Produk</th>
                                    <th class="p-4 text-left">Kategori</th>
                                    <th class="p-4 text-center">Tipe</th>
                                    <th class="p-4 text-center">Jumlah</th>
                                    <th class="p-4 text-left">Tanggal</th>
                                    <th class="p-4 text-left">Catatan</th>
                                    <th class="p-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="log-table-body"></tbody>
                        </table>
                        <p id="no-results" class="text-center text-gray-500 py-16 hidden"><i
                                class="bi bi-search text-5xl block mb-4"></i>Tidak ada data ditemukan.</p>
                    </div>
                </div>
            </main>
        </div>
    </div>

    {{-- Modal Detail Logistik --}}
    <div id="detail-modal"
        class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden modal-overlay opacity-0">
        <div
            class="bg-white rounded-2xl shadow-xl w-full max-w-2xl modal-content transform scale-95 opacity-0 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-5 border-b sticky top-0 bg-white z-10">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Detail Aktivitas</h3>
                    <p id="modal-id" class="text-sm text-gray-500"></p>
                </div>
                <button type="button" class="close-modal p-2 rounded-full hover:bg-gray-100"><i
                        class="bi bi-x-lg text-xl"></i></button>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold text-gray-700 mb-2 border-b pb-2">Informasi Produk</h4>
                        {{-- [DIUBAH] Gambar dihapus dari modal detail --}}
                        <div>
                            <p id="modal-product-name" class="font-bold text-lg text-gray-800"></p>
                            <p id="modal-variant" class="text-sm text-gray-500"></p>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-700 mb-2 border-b pb-2">Detail Transaksi</h4>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between"><span
                                    class="font-semibold text-gray-600">Kategori:</span><span id="modal-category"
                                    class="text-gray-800 text-right"></span></div>
                            <div class="flex justify-between"><span class="font-semibold text-gray-600">Tipe
                                    Transaksi:</span><span id="modal-type" class="text-gray-800 text-right"></span>
                            </div>
                            <div class="flex justify-between"><span
                                    class="font-semibold text-gray-600">Jumlah:</span><span id="modal-qty"
                                    class="text-gray-800 text-right"></span></div>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2 border-b pb-2">Informasi Tambahan</h4>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between"><span
                                class="font-semibold text-gray-600">Tanggal:</span><span id="modal-date"
                                class="text-gray-800 text-right"></span></div>
                        <div class="flex justify-between"><span
                                class="font-semibold text-gray-600">Catatan:</span><span id="modal-note"
                                class="text-gray-800 text-right"></span></div>
                    </div>
                </div>
            </div>
            <div class="p-5 border-t bg-gray-50 flex justify-end gap-3"><button type="button"
                    class="close-modal px-4 py-2 bg-gray-200 rounded-lg">Tutup</button></div>
        </div>
    </div>

    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            AOS.init({
                duration: 600,
                once: true,
                offset: 20
            });

            let allLogData = [];
            const tableBody = $('#log-table-body');
            const noResults = $('#no-results');
            const detailModal = $('#detail-modal');

            function loadData() {
                $.ajax({
                    url: "{{ route('staff.inout.data') }}",
                    method: 'GET',
                    success: function(response) {
                        allLogData = response.logs;
                        updateSummaryCards(response.summary);
                        filterAndRender(); // Render tabel setelah data berhasil dimuat
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON.error || 'Gagal memuat data.', 'error');
                    }
                });
            }

            function updateSummaryCards(summary) {
                $('#summary-masuk').text(summary.masuk_hari_ini);
                $('#summary-keluar').text(summary.keluar_hari_ini);
                $('#summary-kritis').text(summary.stok_kritis);
                $('#summary-proses').text(summary.perlu_diproses);
            }

            function renderTable(logs) {
                tableBody.empty();
                noResults.toggleClass('hidden', logs.length > 0);

                logs.forEach(log => {
                    const formattedDate = new Date(log.date).toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });
                    const rowHtml = `
                    <tr class="hover:bg-gray-50 transition-colors" data-aos="fade-up">
                        <td data-label="Detail Produk" class="p-4">
                            <div>
                                <p class="font-semibold text-gray-800">${log.product_name}</p>
                                <p class="text-xs text-gray-500">${log.variant_name}</p>
                            </div>
                        </td>
                        <td data-label="Kategori" class="p-4 text-gray-600">${log.category}</td>
                        <td data-label="Tipe" class="p-4 text-center"><span class="status-badge status-${log.type}">${log.type}</span></td>
                        <td data-label="Jumlah" class="p-4 text-center font-semibold">${log.quantity}</td>
                        <td data-label="Tanggal" class="p-4 text-gray-500">${formattedDate}</td>
                        <td data-label="Catatan" class="p-4 text-gray-600">${log.notes}</td>
                        <td data-label="Aksi" class="p-4 text-center">
                            <button data-id="${log.id}" class="detail-btn text-indigo-600 hover:bg-indigo-100 font-semibold px-3 py-1.5 rounded-lg transition-colors text-sm">Detail</button>
                        </td>
                    </tr>`;
                    tableBody.append(rowHtml);
                });
            }

            function filterAndRender() {
                const tab = $('.tab-button.active').data('tab');
                const dateFilter = $('#date-filter').val();
                const searchTerm = $('#search-filter').val().toLowerCase();

                const filteredData = allLogData.filter(log => {
                    const tabMatch = tab === 'all' || log.type === tab;
                    const dateMatch = !dateFilter || log.date.startsWith(dateFilter);
                    const searchMatch = !searchTerm || log.product_name.toLowerCase().includes(
                        searchTerm) || log.notes.toLowerCase().includes(searchTerm) || log.category
                        .toLowerCase().includes(searchTerm);
                    return tabMatch && dateMatch && searchMatch;
                });
                renderTable(filteredData);
            }

            $('.tab-button').on('click', function() {
                $('.tab-button').removeClass('active');
                $(this).addClass('active');
                filterAndRender();
            });
            $('#date-filter, #search-filter').on('input', filterAndRender);

            function openModal(modal) {
                modal.removeClass('hidden');
                setTimeout(() => modal.removeClass('opacity-0').find('.modal-content').removeClass(
                    'opacity-0 scale-95'), 10);
                $('body').addClass('overflow-hidden');
            }

            function closeModal(modal) {
                modal.addClass('opacity-0').find('.modal-content').addClass('opacity-0 scale-95');
                setTimeout(() => modal.addClass('hidden'), 300);
                $('body').removeClass('overflow-hidden');
            }

            $(document).on('click', '.detail-btn', function() {
                const logId = $(this).data('id');
                const data = allLogData.find(log => log.id === logId);
                if (data) {
                    $('#modal-product-name').text(data.product_name);
                    $('#modal-variant').text(data.variant_name);
                    $('#modal-id').text('Log ID: #' + data.id);
                    $('#modal-category').text(data.category);
                    $('#modal-type').html(
                        `<span class="status-badge status-${data.type}">${data.type}</span>`);
                    $('#modal-qty').text(`${data.quantity} Unit`);
                    $('#modal-date').text(new Date(data.date).toLocaleString('id-ID', {
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    }));
                    $('#modal-note').text(data.notes);
                    openModal(detailModal);
                }
            });

            $('.close-modal').on('click', function() {
                closeModal($(this).closest('.modal-overlay'));
            });

            loadData();
        });
    </script>
</body>

</html>

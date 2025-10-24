<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tukar Tambah | CELVION</title>

    {{-- [BARU] CSRF Token untuk AJAX --}}
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

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- DataTables CSS --}}
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css" rel="stylesheet" />

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

        .step,
        .step-connector {
            transition: all 0.3s ease;
        }

        .step {
            background-color: #e0e7ff;
            color: #4f46e5;
        }

        .step-connector {
            background-color: #e0e7ff;
        }

        .step.active {
            background-color: #4f46e5;
            color: white;
        }

        .step.completed {
            background-color: #4f46e5;
            color: white;
        }

        .step-connector.completed {
            background-color: #4f46e5;
        }

        #productTable_wrapper .dt-search input,
        #productTable_wrapper .dt-length select {
            background-color: white !important;
            color: #1e293b !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 0.5rem !important;
            padding: 0.5rem 0.75rem !important;
            outline: none;
        }

        #productTable_wrapper .dt-search input:focus,
        #productTable_wrapper .dt-length select:focus {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2) !important;
        }

        #productTable_wrapper .dt-search input {
            padding-left: 2.25rem !important;
        }

        .qc-item {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1rem;
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            padding: 0.75rem;
            transition: all 0.2s ease;
        }

        .qc-item.fail {
            border-color: #fca5a5;
            background-color: #fee2e2;
        }

        .qc-item.pass {
            border-color: #a7f3d0;
            background-color: #f0fdf4;
        }

        .qc-item.error-highlight {
            border: 2px solid #ef4444;
        }

        .qc-label {
            flex-grow: 1;
            font-weight: 500;
            color: #374151;
        }

        .qc-options button {
            background: transparent;
            border: none;
            font-size: 1.75rem;
            line-height: 1;
            cursor: pointer;
            color: #d1d5db;
            transition: all 0.2s ease;
        }

        .qc-options button.active.pass {
            color: #22c55e;
            transform: scale(1.1);
        }

        .qc-options button.active.fail {
            color: #ef4444;
            transform: scale(1.1);
        }

        .qc-desc-input,
        .qc-cost-input-wrapper {
            flex-basis: 100%;
            margin-top: 0.5rem;
        }

        @media (max-width: 767px) {
            .responsive-table thead {
                display: none;
            }

            .responsive-table tbody {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 1rem;
            }

            .responsive-table tr {
                display: block;
                width: 100%;
                margin-bottom: 0;
                border: 1px solid #e2e8f0;
                border-radius: 0.75rem;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
                overflow: hidden;
                max-width: 400px;
            }

            .responsive-table td {
                display: flex;
                padding: 0.75rem 1rem;
                border-bottom: 1px solid #f1f5f9;
                text-align: right;
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

            .responsive-table td[data-label="Aksi"] {
                justify-content: center;
                background-color: #f8fafc;
                padding: 1rem;
            }

            .responsive-table td[data-label="Aksi"]::before {
                display: none;
            }

            .responsive-table td[data-label="Produk"] {
                display: block;
                padding: 1rem;
            }

            .responsive-table td[data-label="Produk"]::before {
                display: none;
            }

            .responsive-table td[data-label="Aksi"] .start-trade-in-btn {
                width: 100%;
            }

            #productTable_wrapper .flex.items-center.justify-between {
                flex-direction: column;
                align-items: stretch;
                gap: 1rem;
            }

            #productTable_wrapper .dt-search {
                width: 100%;
            }

            #productTable_wrapper .dt-length {
                display: flex;
                justify-content: flex-start;
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
                    'title' => 'Tukar Tambah',
                    'subtitle' => 'Proses transaksi tukar tambah perangkat pelanggan.',
                ])

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white p-4 md:p-6 rounded-2xl shadow-lg" data-aos="fade-up">
                        <h4 class="text-xl font-semibold text-gray-700 mb-1">Volume Transaksi</h4>
                        <p class="text-sm text-gray-500 mb-4">Jumlah tukar tambah 7 hari terakhir.</p>
                        <div class="h-64">
                            <canvas id="transactionCountChart"></canvas>
                        </div>
                    </div>

                    <div class="bg-white p-4 md:p-6 rounded-2xl shadow-lg" data-aos="fade-up" data-aos-delay="100">
                        <h4 class="text-xl font-semibold text-gray-700 mb-1">Nilai Transaksi</h4>
                        <p class="text-sm text-gray-500 mb-4">Total nilai tukar tambah 7 hari terakhir.</p>
                        <div class="h-64">
                            <canvas id="transactionValueChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-4 md:p-6 rounded-2xl shadow-lg" data-aos="fade-up" data-aos-delay="200">
                    <h4 class="text-xl font-semibold text-gray-700 mb-4">Pilih Produk Baru</h4>
                    <table id="productTable" class="w-full text-sm responsive-table" style="width:100%">
                        <thead>
                            <tr>
                                <th class="p-4 text-left">Produk</th>
                                <th class="p-4 text-left">Harga</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <div id="trade-in-modal"
        class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden modal-overlay opacity-0">
        <div
            class="bg-white rounded-2xl shadow-xl w-full max-w-lg lg:max-w-2xl modal-content transform scale-95 opacity-0 max-h-[90vh] flex flex-col">
            <div class="flex justify-between items-center p-5 border-b sticky top-0 bg-white z-10">
                <h3 class="text-xl font-bold text-gray-800">Proses Tukar Tambah</h3>
                <button type="button" class="close-modal p-2"><i class="bi bi-x-lg text-xl"></i></button>
            </div>

            <div class="p-6 overflow-y-auto">
                <div class="flex items-center justify-center mb-8">
                    <div class="step w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg">1</div>
                    <div class="step-connector flex-1 h-1"></div>
                    <div class="step w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg">2</div>
                    <div class="step-connector flex-1 h-1"></div>
                    <div class="step w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg">3</div>
                </div>

                <div id="step-1" class="step-content">
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-700 mb-2 text-lg">Pilih Varian Produk Baru (<span
                                id="new-product-name-title"></span>)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><label for="new-product-storage"
                                    class="block mb-1 font-medium text-sm">Memori</label><select
                                    id="new-product-storage" class="w-full px-3 py-2 border rounded-lg"
                                    required></select></div>
                            <div><label for="new-product-color"
                                    class="block mb-1 font-medium text-sm">Warna</label><select id="new-product-color"
                                    class="w-full px-3 py-2 border rounded-lg" required></select></div>
                        </div>
                    </div>
                    <hr class="my-6">
                    <h4 class="font-semibold text-gray-700 mb-4 text-lg">Informasi Perangkat Lama</h4>
                    <form id="old-device-form" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><label for="old-device-brand"
                                    class="block mb-1 font-medium text-sm">Brand</label><select id="old-device-brand"
                                    class="w-full px-3 py-2 border rounded-lg" required>
                                    <option value="">Pilih Brand</option>
                                </select></div>
                            <div><label for="old-device-model"
                                    class="block mb-1 font-medium text-sm">Model</label><input type="text"
                                    id="old-device-model" placeholder="cth: iPhone 11 Pro"
                                    class="w-full px-3 py-2 border rounded-lg" required></div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><label for="old-device-ram" class="block mb-1 font-medium text-sm">RAM /
                                    Memori</label><input type="text" id="old-device-ram" placeholder="cth: 128GB"
                                    class="w-full px-3 py-2 border rounded-lg" required></div>
                            <div><label for="old-device-color"
                                    class="block mb-1 font-medium text-sm">Warna</label><input type="text"
                                    id="old-device-color" placeholder="cth: Sierra Blue"
                                    class="w-full px-3 py-2 border rounded-lg" required></div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><label for="old-device-completeness"
                                    class="block mb-1 font-medium text-sm">Kelengkapan</label><select
                                    id="old-device-completeness" class="w-full px-3 py-2 border rounded-lg" required>
                                    <option value="Fullset (Lengkap)">Fullset (Lengkap)</option>
                                    <option value="Unit & Box Saja">Unit & Box Saja</option>
                                    <option value="Unit Saja (Batangan)">Unit Saja (Batangan)</option>
                                </select></div>
                            <div><label for="old-device-condition" class="block mb-1 font-medium text-sm">Kondisi
                                    Fisik</label><select id="old-device-condition"
                                    class="w-full px-3 py-2 border rounded-lg" required>
                                    <option value="Mulus">Mulus</option>
                                    <option value="Goresan Halus">Goresan Halus</option>
                                    <option value="Retak/Penyok">Retak/Penyok</option>
                                </select></div>
                        </div>
                        <div class="pt-4">
                            <label for="manual-appraisal-price"
                                class="block mb-2 font-semibold text-gray-700">Taksiran Harga Awal (Manual)</label>
                            <div class="relative"><span
                                    class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span><input
                                    type="text" id="manual-appraisal-price"
                                    class="w-full pl-8 pr-3 py-2 border rounded-lg font-bold text-lg" required></div>
                        </div>
                    </form>
                </div>

                <div id="step-2" class="step-content hidden">
                    <h4 class="font-semibold text-gray-700 mb-2 text-lg">Langkah 2: Pengecekan Kualitas (QC)</h4>
                    <p class="text-sm text-gray-500 mb-4">Pilih status untuk setiap item. Klik <i
                            class="bi bi-x-circle-fill text-red-500"></i> jika ada minus/kerusakan.</p>
                    <form id="qc-checklist" class="space-y-3"></form>
                    <div id="repair-cost-summary" class="mt-6">
                        <hr class="my-4">
                        <div class="flex justify-between items-center bg-red-50 p-4 rounded-lg">
                            <span class="font-semibold text-red-700">Total Biaya Perbaikan (Manual):</span>
                            <span id="total-repair-cost" class="font-bold text-red-700 text-lg">Rp0</span>
                        </div>
                    </div>
                </div>

                <div id="step-3" class="step-content hidden">
                    <h4 class="font-semibold text-gray-700 mb-2 text-lg">Langkah 3: Hasil Penilaian</h4>
                    <p class="text-sm text-gray-500 mb-6">Berikut adalah hasil perhitungan tukar tambah.</p>
                    <div class="bg-slate-50 rounded-lg p-6 space-y-3 text-left">
                        <div class="flex justify-between items-center"><span class="text-gray-600">Harga Produk
                                Baru:</span><span id="new-product-price"
                                class="font-semibold text-gray-800">Rp0</span></div>
                        <div class="flex justify-between items-center"><span class="text-gray-600">Taksiran Harga
                                Awal:</span><span id="initial-appraisal-value"
                                class="font-semibold text-gray-800">Rp0</span></div>
                        <div id="repair-deduction-row" class="hidden">
                            <div class="flex justify-between items-center"><span class="text-gray-600">Potongan Biaya
                                    Perbaikan:</span><span id="repair-deduction" class="font-semibold text-red-600">-
                                    Rp0</span></div>
                            <div id="qc-failures-breakdown" class="pl-4 mt-1 space-y-1"></div>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t mt-2"><span
                                class="text-gray-600 font-bold">Nilai Taksiran Akhir:</span><span
                                id="final-appraisal-value" class="font-bold text-green-600">- Rp0</span></div>
                        <hr class="my-2">
                        <div class="flex justify-between items-center text-xl"><span
                                class="font-bold text-gray-800">Total Tambah:</span><span id="final-price"
                                class="font-extrabold text-indigo-600">Rp0</span></div>
                    </div>
                </div>
            </div>

            <div class="p-5 border-t bg-gray-50 flex justify-between gap-3 sticky bottom-0 z-10">
                <button type="button" id="prev-step-btn"
                    class="px-5 py-2 bg-gray-200 rounded-lg font-semibold hidden">Kembali</button>
                <button type="button" id="next-step-btn"
                    class="w-full px-5 py-2 bg-indigo-600 text-white rounded-lg font-bold">Lanjutkan ke QC</button>
                <button type="button" id="finish-btn"
                    class="w-full px-5 py-2 bg-green-600 text-white rounded-lg font-bold hidden">Selesaikan
                    Transaksi</button>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            AOS.init({
                duration: 600,
                once: true,
                offset: 20
            });

            // [BARU] Setup CSRF Token untuk semua request AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // [DIUBAH] Mengambil data dari variabel Blade yang dikirim controller
            const brands = @json($brands);
            const qcItems = @json($qcItems);
            let currentStep = 1,
                selectedNewProduct = null,
                productTable;

            // [DIUBAH] Inisialisasi DataTables dengan AJAX
            productTable = $('#productTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('kasir.kualitas.products') }}",
                    dataSrc: "data"
                },
                dom: "<'flex items-center justify-between mb-4'<'dt-length'l><'dt-search'f>>t<'flex items-center justify-between mt-4'<'dt-info'i><'dt-paging'p>>",
                columns: [{
                        data: 'name',
                        render: (data, type, row) =>
                            `<div class="flex flex-col items-center text-center gap-3 md:flex-row md:text-left md:items-start"><img src="${row.image}" class="w-20 h-20 md:w-16 md:h-16 rounded-lg object-cover flex-shrink-0"><div class="flex-grow"><p class="font-bold text-lg text-gray-800">${data}</p><p class="text-sm text-gray-500">${row.description}</p></div></div>`
                    },
                    {
                        data: 'price',
                        render: data =>
                            `<p class="font-semibold text-indigo-600">Rp ${parseInt(data).toLocaleString('id-ID')}</p>`
                    },
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        render: data =>
                            `<button data-id="${data}" class="start-trade-in-btn w-full bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-indigo-700">Mulai Tukar Tambah</button>`
                    }
                ],
                language: {
                    search: "",
                    searchPlaceholder: "Cari produk...",
                    lengthMenu: "Tampil _MENU_",
                    zeroRecords: "Produk tidak ditemukan.",
                    infoEmpty: "Belum ada produk.",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: {
                        next: ">",
                        previous: "<"
                    }
                },
                createdRow: function(row, data, dataIndex) {
                    $('td', row).eq(0).attr('data-label', 'Produk');
                    $('td', row).eq(1).attr('data-label', 'Harga');
                    $('td', row).eq(2).attr('data-label', 'Aksi');
                }
            });
            $('#productTable_wrapper .dt-search').addClass('relative').find('input').before(
                '<i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>');

            const brandSelect = $('#old-device-brand');
            brands.forEach(brand => brandSelect.append(`<option value="${brand}">${brand}</option>`));

            const qcContainer = $('#qc-checklist');
            qcItems.forEach(item => {
                const qcElement = $(
                    `<div class="qc-item" data-label="${item}"><div class="flex-grow flex items-center justify-between"><span class="qc-label">${item}</span></div><div class="qc-options flex items-center gap-2"><button type="button" class="qc-option-btn pass" data-value="pass"><i class="bi bi-check-circle-fill"></i></button><button type="button" class="qc-option-btn fail" data-value="fail"><i class="bi bi-x-circle-fill"></i></button></div><input type="text" placeholder="Deskripsi minus..." class="qc-desc-input w-full border rounded-lg text-sm hidden px-3 py-1.5"><div class="qc-cost-input-wrapper w-full mt-2 hidden"><label class="block text-xs font-medium text-gray-500">Estimasi Biaya Perbaikan</label><div class="relative"><span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span><input type="text" class="qc-cost-input w-full pl-8 pr-3 py-1.5 border rounded-lg text-sm" placeholder="0"></div></div></div>`
                );
                qcContainer.append(qcElement);
            });

            const formatNumberInput = (input) => {
                let value = input.val().replace(/[^0-9]/g, '');
                input.val(value ? Number(value).toLocaleString('id-ID') : '');
            };
            const parseCurrency = (string) => Number(String(string).replace(/[^0-9]/g, ''));

            $('#manual-appraisal-price').on('keyup', function() {
                formatNumberInput($(this));
            });
            qcContainer.on('keyup', '.qc-cost-input', function() {
                formatNumberInput($(this));
                updateTotalRepairCostSummary();
            });

            qcContainer.on('click', '.qc-option-btn', function() {
                const button = $(this),
                    parentItem = button.closest('.qc-item'),
                    status = button.data('value');
                parentItem.attr('data-status', status).removeClass('pass fail error-highlight').addClass(
                    status);
                button.siblings().removeClass('active');
                button.addClass('active');
                parentItem.find('.qc-desc-input').toggleClass('hidden', status === 'pass');
                parentItem.find('.qc-cost-input-wrapper').toggleClass('hidden', status === 'pass');
                parentItem.find('.qc-desc-input, .qc-cost-input').prop('required', status === 'fail');
                if (status === 'pass') {
                    parentItem.find('.qc-desc-input, .qc-cost-input').val('');
                }
                updateTotalRepairCostSummary();
            });

            function updateTotalRepairCostSummary() {
                let totalCost = 0;
                $('.qc-cost-input:visible').each(function() {
                    totalCost += parseCurrency($(this).val());
                });
                $('#total-repair-cost').text('Rp' + totalCost.toLocaleString('id-ID'));
            }

            const goToStep = (step) => {
                currentStep = step;
                $('.step-content').addClass('hidden');
                $(`#step-${step}`).removeClass('hidden');

                const steps = $('.step');
                const connectors = $('.step-connector');

                // 1. Reset semua status
                steps.removeClass('active completed');
                connectors.removeClass('completed');

                // 2. Tandai langkah sebelumnya sebagai 'completed'
                for (let i = 0; i < step - 1; i++) {
                    $(steps[i]).addClass('completed');
                    if (connectors[i]) {
                        $(connectors[i]).addClass('completed');
                    }
                }

                // 3. Tandai langkah saat ini sebagai 'active'
                $(steps[step - 1]).addClass('active');

                // Perbarui tombol (logika ini sudah benar)
                $('#prev-step-btn').toggleClass('hidden', step === 1).css('width', step > 1 ? 'auto' : '0');
                $('#next-step-btn').toggleClass('hidden', step === 3).text(step === 1 ? 'Lanjutkan ke QC' :
                    'Selesai QC & Nilai');
                $('#finish-btn').toggleClass('hidden', step !== 3);
            };

            $('#next-step-btn').on('click', function() {
                if (currentStep === 1) {
                    if ($('#old-device-form')[0].checkValidity()) goToStep(2);
                    else $('#old-device-form')[0].reportValidity();
                } else if (currentStep === 2) {
                    let allSelected = true;
                    $('.qc-item').each(function() {
                        if (!$(this).data('status')) {
                            $(this).addClass('error-highlight');
                            allSelected = false;
                        } else {
                            $(this).removeClass('error-highlight');
                        }
                    });
                    if (!allSelected) {
                        Swal.fire('Pengecekan Belum Selesai',
                            'Harap tentukan status (lolos/tidak) untuk semua item QC.', 'warning');
                        return;
                    }
                    if (qcContainer[0].checkValidity()) {
                        calculateFinalValuation();
                        goToStep(3);
                    } else {
                        qcContainer[0].reportValidity();
                    }
                }
            });

            $('#prev-step-btn').on('click', () => goToStep(currentStep - 1));

            function calculateFinalValuation() {
                // [PERBAIKAN] Mengambil harga terbaru dari data produk yang dipilih
                const newPrice = productTable.row($(`button[data-id="${selectedNewProduct.id}"]`).closest('tr'))
                    .data().price;
                const initialAppraisal = parseCurrency($('#manual-appraisal-price').val());
                let totalDeduction = 0;
                let failuresBreakdown = '';

                $('.qc-item[data-status="fail"]').each(function() {
                    const item = $(this);
                    const cost = parseCurrency(item.find('.qc-cost-input').val());
                    const desc = item.find('.qc-desc-input').val();
                    totalDeduction += cost;
                    failuresBreakdown +=
                        `<div class="flex justify-between text-xs text-red-500/80"><p>- ${item.data('label')}: ${desc}</p><p>-Rp${cost.toLocaleString('id-ID')}</p></div>`;
                });

                const finalAppraisal = Math.max(0, initialAppraisal - totalDeduction);
                const finalPrice = newPrice - finalAppraisal;

                $('#new-product-price').text('Rp ' + parseInt(newPrice).toLocaleString('id-ID'));
                $('#initial-appraisal-value').text('Rp' + initialAppraisal.toLocaleString('id-ID'));
                $('#repair-deduction').text('- Rp' + totalDeduction.toLocaleString('id-ID'));
                $('#qc-failures-breakdown').html(failuresBreakdown);
                $('#repair-deduction-row').toggleClass('hidden', totalDeduction === 0);
                $('#final-appraisal-value').text('- Rp' + finalAppraisal.toLocaleString('id-ID'));
                $('#final-price').text('Rp' + finalPrice.toLocaleString('id-ID'));
            }

            const openModal = () => {
                $('#trade-in-modal').removeClass('hidden');
                setTimeout(() => $('#trade-in-modal').removeClass('opacity-0').find('.modal-content')
                    .removeClass('opacity-0 scale-95'), 10);
                $('body').addClass('overflow-hidden');
            };
            const closeModal = () => {
                const modal = $('#trade-in-modal');
                modal.addClass('opacity-0').find('.modal-content').addClass('opacity-0 scale-95');
                setTimeout(() => modal.addClass('hidden'), 300);
                $('body').removeClass('overflow-hidden');
            };

            $('#productTable tbody').on('click', '.start-trade-in-btn', function() {
                selectedNewProduct = productTable.row($(this).closest('tr')).data();
                if (selectedNewProduct) {
                    $('#new-product-name-title').text(selectedNewProduct.name);
                    const storageSelect = $('#new-product-storage').empty(),
                        colorSelect = $('#new-product-color').empty();
                    selectedNewProduct.variants.storage.forEach(s => storageSelect.append(
                        `<option value="${s}">${s}</option>`));
                    selectedNewProduct.variants.colors.forEach(c => colorSelect.append(
                        `<option value="${c}">${c}</option>`));
                    $('#old-device-form')[0].reset();
                    goToStep(1);
                    $('.qc-item').each(function() {
                        $(this).removeData('status').removeClass('pass fail error-highlight').find(
                            '.qc-option-btn').removeClass('active');
                        $(this).find('.qc-desc-input, .qc-cost-input-wrapper').addClass('hidden')
                            .find('input').val('').prop('required', false);
                    });
                    updateTotalRepairCostSummary();
                    openModal();
                }
            });

            $('#finish-btn').on('click', function() {
                // [DIUBAH] Mengumpulkan semua data untuk dikirim ke controller
                const oldDeviceForm = $('#old-device-form');
                const qcDetails = {};
                $('.qc-item').each(function() {
                    const item = $(this);
                    const label = item.data('label');
                    const status = item.data('status');
                    if (status) {
                        qcDetails[label] = {
                            status: status === 'pass' ? 'Aman' : item.find('.qc-desc-input')
                                .val(),
                            cost: parseCurrency(item.find('.qc-cost-input').val())
                        };
                    }
                });

                const dataToSend = {
                    product_name: oldDeviceForm.find('#old-device-brand').val() + ' ' + oldDeviceForm
                        .find('#old-device-model').val(),
                    specs: oldDeviceForm.find('#old-device-ram').val() + ' | ' + oldDeviceForm.find(
                        '#old-device-color').val(),
                    cost_price: parseCurrency($('#manual-appraisal-price').val()),
                    completeness: oldDeviceForm.find('#old-device-completeness').val(),
                    condition: oldDeviceForm.find('#old-device-condition').val(),
                    qc_details: JSON.stringify(qcDetails),
                    new_product_id: selectedNewProduct.id,
                    new_product_storage: $('#new-product-storage').val(),
                    new_product_color: $('#new-product-color').val()
                };

                $.ajax({
                    url: "{{ route('kasir.kualitas.store') }}",
                    method: 'POST',
                    data: dataToSend,
                    success: function(response) {
                        closeModal();
                        Swal.fire({
                            icon: 'success',
                            title: 'Transaksi Berhasil!',
                            text: response.success,
                            confirmButtonColor: '#4f46e5'
                        });
                        // Muat ulang data chart
                        loadChartData();
                    },
                    error: function(xhr) {
                        let errorMsg = 'Gagal menyimpan transaksi. Periksa kembali data Anda.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMsg = Object.values(xhr.responseJSON.errors).join('\n');
                        } else if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMsg = xhr.responseJSON.error;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: errorMsg
                        });
                    }
                });
            });

            $('.close-modal').on('click', closeModal);

            const chartDefaults = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: {
                            family: "'Poppins', sans-serif"
                        },
                        bodyFont: {
                            family: "'Poppins', sans-serif"
                        },
                        padding: 10,
                        cornerRadius: 8,
                        boxPadding: 4
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: "'Poppins', sans-serif"
                            }
                        }
                    }
                }
            };

            let transactionCountChart, transactionValueChart;

            // [DIUBAH] Fungsi untuk memuat dan menggambar chart
            function loadChartData() {
                $.ajax({
                    url: "{{ route('kasir.kualitas.chart-data') }}",
                    method: 'GET',
                    success: function(data) {
                        // Chart Volume Transaksi
                        const ctxCount = document.getElementById('transactionCountChart').getContext(
                            '2d');
                        if (transactionCountChart) transactionCountChart.destroy();
                        transactionCountChart = new Chart(ctxCount, {
                            type: 'bar',
                            data: {
                                labels: data.labels,
                                datasets: [{
                                    label: 'Jumlah Transaksi',
                                    data: data.count_data,
                                    backgroundColor: '#c7d2fe',
                                    borderColor: '#a5b4fc',
                                    borderWidth: 1,
                                    borderRadius: 4,
                                    hoverBackgroundColor: '#a5b4fc'
                                }]
                            },
                            options: {
                                ...chartDefaults,
                                plugins: {
                                    ...chartDefaults.plugins,
                                    tooltip: {
                                        ...chartDefaults.plugins.tooltip,
                                        callbacks: {
                                            label: (context) => `${context.parsed.y} transaksi`
                                        }
                                    }
                                },
                                scales: {
                                    ...chartDefaults.scales,
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            stepSize: 2,
                                            font: {
                                                family: "'Poppins', sans-serif"
                                            }
                                        }
                                    }
                                }
                            }
                        });

                        // Chart Nilai Transaksi
                        const ctxValue = document.getElementById('transactionValueChart').getContext(
                            '2d');
                        if (transactionValueChart) transactionValueChart.destroy();
                        const gradientValue = ctxValue.createLinearGradient(0, 0, 0, 250);
                        gradientValue.addColorStop(0, 'rgba(79, 70, 229, 0.5)');
                        gradientValue.addColorStop(1, 'rgba(79, 70, 229, 0)');
                        transactionValueChart = new Chart(ctxValue, {
                            type: 'line',
                            data: {
                                labels: data.labels,
                                datasets: [{
                                    label: 'Nilai Transaksi',
                                    data: data.value_data,
                                    fill: true,
                                    backgroundColor: gradientValue,
                                    borderColor: '#4f46e5',
                                    tension: 0.4,
                                    pointBackgroundColor: '#4f46e5',
                                    pointHoverBackgroundColor: 'white',
                                    pointHoverBorderColor: '#4f46e5',
                                    pointHoverBorderWidth: 2,
                                    pointRadius: 4,
                                    pointHoverRadius: 6,
                                }]
                            },
                            options: {
                                ...chartDefaults,
                                plugins: {
                                    ...chartDefaults.plugins,
                                    tooltip: {
                                        ...chartDefaults.plugins.tooltip,
                                        callbacks: {
                                            label: (context) => new Intl.NumberFormat('id-ID', {
                                                style: 'currency',
                                                currency: 'IDR',
                                                minimumFractionDigits: 0
                                            }).format(context.parsed.y)
                                        }
                                    }
                                },
                                scales: {
                                    ...chartDefaults.scales,
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            font: {
                                                family: "'Poppins', sans-serif"
                                            },
                                            callback: (value) => 'Rp' + (value / 1000000) +
                                                ' Jt'
                                        }
                                    }
                                }
                            }
                        });
                    }
                });
            }

            // Memuat chart saat halaman pertama kali dibuka
            loadChartData();
        });
    </script>
</body>

</html>

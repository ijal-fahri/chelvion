<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manajemen Permintaan | CELVION</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Tailwind CSS & jQuery --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- Ikon & Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    
    {{-- Animasi AOS, SweetAlert2 & Chart.js --}}
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    {{-- DataTables CSS --}}
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css" rel="stylesheet" />
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; }
        .modal-overlay { transition: opacity 0.3s ease; }
        .modal-content { transition: transform 0.3s ease, opacity 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
        
        .kpi-card { transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out; }
        .kpi-card:hover { transform: translateY(-4px); box-shadow: 0 10px 15px -3px rgba(0,0,0,.07), 0 4px 6px -4px rgba(0,0,0,.07); }
        
        .status-badge { padding: 4px 12px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; text-transform: capitalize; display: inline-block; }
        .status-pending { background-color: #fef9c3; color: #854d0e; }
        .status-approved, .status-disetujui { background-color: #dcfce7; color: #166534; }
        .status-rejected { background-color: #fee2e2; color: #991b1b; }
        
        #requestTable_wrapper .dt-search input,
        #requestTable_wrapper .dt-length select,
        #statusFilter {
            background-color: white !important; color: #1e293b !important; border: 1px solid #cbd5e1 !important;
            border-radius: 0.5rem !important; padding: 0.5rem 0.75rem !important; outline: none; transition: border-color 0.2s, box-shadow 0.2s;
        }
        #requestTable_wrapper .dt-search input:focus,
        #requestTable_wrapper .dt-length select:focus,
        #statusFilter:focus {
            border-color: #6366f1 !important; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2) !important;
        }
        #requestTable_wrapper .dt-search input { padding-left: 2.25rem !important; }
        #requestTable_wrapper .dt-paging .dt-paging-button.current { background: #4f46e5 !important; color: #ffffff !important; border: 1px solid #4f46e5 !important; }

        @media (max-width: 767px) {
            .mobile-card-view thead { display: none; }
            .mobile-card-view tr { background-color: white; margin-bottom: 1rem; border: 1px solid #e2e8f0; border-radius: 0.75rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); overflow: hidden; display: block; }
            .mobile-card-view td { padding: 0.75rem 1rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; gap: 1rem; }
            .mobile-card-view td:last-child { border-bottom: none; }
            .mobile-card-view td[data-label]::before { content: attr(data-label); font-weight: 600; color: #475569; text-align: left; }
            body .mobile-card-view td { text-align: right; }
            .mobile-card-view td .flex { justify-content: flex-end; }
        }
    </style>
</head>
<body>
    <div class="relative min-h-screen md:flex">
        
        @include('staff.partials.sidebar')

        <div class="flex-1 md:ml-64 flex flex-col min-w-0">
            <main class="flex-grow p-4 sm:p-6 lg:p-8 animate-fade-in">
                
                @include('staff.partials.header', [
                    'title' => 'Manajemen Permintaan Produk',
                    'subtitle' => 'Tinjau dan setujui permintaan stok dari cabang.'
                ])

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-5 rounded-2xl shadow-lg kpi-card flex flex-col justify-between" data-aos="fade-up">
                        <div>
                            <div class="bg-blue-100 text-blue-600 text-2xl w-12 h-12 flex items-center justify-center rounded-xl mb-3"><i class="bi bi-clipboard-data"></i></div>
                            <p class="text-gray-500 font-medium">Total Permintaan</p>
                            <h3 id="kpi-total" class="text-3xl font-bold text-gray-800 mt-1">{{ $summary['total'] }}</h3>
                        </div>
                        <div class="h-16 mt-4">
                            <canvas id="trendSparklineChart"></canvas>
                        </div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl shadow-lg kpi-card" data-aos="fade-up" data-aos-delay="100">
                        <div class="bg-amber-100 text-amber-600 text-2xl w-12 h-12 flex items-center justify-center rounded-xl mb-3"><i class="bi bi-hourglass-split"></i></div>
                        <p class="text-gray-500 font-medium">Menunggu Persetujuan</p>
                        <div class="flex items-baseline gap-2">
                            <h3 id="kpi-pending" class="text-3xl font-bold text-gray-800 mt-1">{{ $summary['pending'] }}</h3>
                            <span id="kpi-pending-percent" class="text-sm font-semibold text-amber-600">({{ $summary['total'] > 0 ? round(($summary['pending'] / $summary['total']) * 100) : 0 }}%)</span>
                        </div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl shadow-lg kpi-card" data-aos="fade-up" data-aos-delay="200">
                         <div class="bg-green-100 text-green-600 text-2xl w-12 h-12 flex items-center justify-center rounded-xl mb-3"><i class="bi bi-check-circle"></i></div>
                        <p class="text-gray-500 font-medium">Sudah Disetujui</p>
                        <div class="flex items-baseline gap-2">
                            <h3 id="kpi-approved" class="text-3xl font-bold text-gray-800 mt-1">{{ $summary['approved'] }}</h3>
                            <span id="kpi-approved-percent" class="text-sm font-semibold text-green-600">({{ $summary['total'] > 0 ? round(($summary['approved'] / $summary['total']) * 100) : 0 }}%)</span>
                        </div>
                    </div>
                     <div class="bg-white p-5 rounded-2xl shadow-lg kpi-card" data-aos="fade-up" data-aos-delay="300">
                         <div class="bg-red-100 text-red-600 text-2xl w-12 h-12 flex items-center justify-center rounded-xl mb-3"><i class="bi bi-x-circle"></i></div>
                        <p class="text-gray-500 font-medium">Ditolak</p>
                        <div class="flex items-baseline gap-2">
                            <h3 id="kpi-rejected" class="text-3xl font-bold text-gray-800 mt-1">{{ $summary['rejected'] }}</h3>
                            <span id="kpi-rejected-percent" class="text-sm font-semibold text-red-600">({{ $summary['total'] > 0 ? round(($summary['rejected'] / $summary['total']) * 100) : 0 }}%)</span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-4 md:p-6 rounded-2xl shadow-lg" data-aos="fade-up" data-aos-delay="350">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
                        <h4 class="text-xl font-semibold text-gray-700 w-full md:w-auto">Daftar Permintaan Masuk</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table id="requestTable" class="w-full text-sm mobile-card-view" style="width:100%">
                            <thead class="bg-gray-50 text-gray-600 uppercase">
                                <tr>
                                    <th class="p-4 text-left">Kode Req.</th>
                                    <th class="p-4 text-left">Produk</th>
                                    <th class="p-4 text-left">Admin Peminta</th>
                                    <th class="p-4 text-center">Jumlah</th>
                                    <th class="p-4 text-left">Tgl Permintaan</th>
                                    <th class="p-4 text-center">Status</th>
                                    <th class="p-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data diisi oleh DataTables --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <div id="detail-modal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden modal-overlay opacity-0">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl modal-content transform scale-95 opacity-0">
            <div class="flex flex-col max-h-[90vh]">
                <div class="flex justify-between items-center p-5 border-b sticky top-0 bg-white z-10"><h3 id="detail-title" class="text-xl font-bold"></h3><button type="button" class="close-modal p-2 rounded-full hover:bg-gray-100"><i class="bi bi-x-lg"></i></button></div>
                <div class="p-6 space-y-5 overflow-y-auto">
                    <div class="flex items-start gap-4 p-4 rounded-lg bg-slate-50 border"><img id="detail-image" src="" class="w-20 h-20 rounded-md object-cover"><div><h5 id="detail-product-name" class="font-bold text-gray-800"></h5><p id="detail-variant-name" class="text-sm text-gray-600"></p></div></div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div><p class="text-sm font-medium text-gray-500">Status</p><div id="detail-status"></div></div>
                        <div><p class="text-sm font-medium text-gray-500">Jumlah Diminta</p><p id="detail-quantity" class="font-semibold text-lg"></p></div>
                        <div><p class="text-sm font-medium text-gray-500">Tanggal Permintaan</p><p id="detail-date" class="font-semibold"></p></div>
                        <div><p class="text-sm font-medium text-gray-500">Admin Peminta</p><p id="detail-admin" class="font-semibold"></p></div>
                    </div>
                    <div><p class="text-sm font-medium text-gray-500">Catatan Admin</p><p id="detail-notes" class="text-gray-700 italic border-l-4 pl-3 mt-1"></p></div>
                    <div id="staff-notes-container" class="hidden"><p class="text-sm font-medium text-gray-500">Catatan Staf (Alasan Ditolak)</p><p id="detail-staff-notes" class="text-red-700 italic border-l-4 border-red-400 pl-3 mt-1"></p></div>
                </div>
                <div class="p-5 border-t bg-gray-50 flex justify-end gap-3 sticky bottom-0"><button type="button" class="close-modal px-5 py-2 bg-gray-200 rounded-lg font-semibold">Tutup</button></div>
            </div>
        </div>
    </div>
    
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script>
    $(document).ready(function() {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        AOS.init({ duration: 600, once: true, offset: 20 });

        const requestData = @json($requests);
        const trendData = @json($trendData);
        let charts = {};
        
        const table = $('#requestTable').DataTable({
            data: requestData,
            dom: "<'flex flex-col md:flex-row items-center justify-between gap-4 mb-6'<'dt-length'l><'flex items-center gap-2'<'#status-filter-slot'><'dt-search'f>>>t<'flex flex-col md:flex-row items-center justify-between gap-4 mt-4'<'dt-info'i><'dt-paging'p>>",
            columns: [
                { data: 'id', className: 'font-mono text-indigo-600', render: data => `REQ-${String(data).padStart(3, '0')}` },
                { data: 'product_variant', render: (data) => {
                    if (!data || !data.product) return 'Produk tidak ditemukan';
                    const variantName = `${data.color}${data.ram ? ' / ' + data.ram : ''}`;
                    return `<div><span class="font-semibold text-gray-800">${data.product.name}</span><p class="text-xs text-gray-500">${variantName}</p></div>`;
                }},
                { data: 'admin.name', defaultContent: 'N/A' },
                { data: 'quantity', className: 'text-center font-bold' },
                { data: 'created_at', render: data => new Date(data).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }) },
                { data: 'status', className: 'text-center', render: data => `<span class="status-badge status-${data.toLowerCase()}">${data}</span>` },
                { 
                    data: null, orderable: false, searchable: false, className: 'text-center',
                    render: (data, type, row) => {
                        let actionButtons = `<button class="view-btn w-8 h-8 flex items-center justify-center rounded-md bg-gray-100 text-gray-600 border border-gray-300 hover:bg-gray-200" data-id="${row.id}" title="Lihat Detail"><i class="bi bi-eye-fill"></i></button>`;
                        if (row.status === 'pending') {
                            actionButtons += `<button class="action-btn w-8 h-8 flex items-center justify-center rounded-md bg-green-100 text-green-600 hover:bg-green-200" data-id="${row.id}" data-action="approved" title="Setujui"><i class="bi bi-check-lg"></i></button>
                                            <button class="action-btn w-8 h-8 flex items-center justify-center rounded-md bg-red-100 text-red-600 hover:bg-red-200" data-id="${row.id}" data-action="rejected" title="Tolak"><i class="bi bi-x-lg"></i></button>`;
                        }
                        return `<div class="flex justify-center items-center gap-2">${actionButtons}</div>`;
                    }
                }
            ],
            language: { search: "", searchPlaceholder: "Cari...", lengthMenu: "Tampil _MENU_", zeroRecords: "<div class='text-center p-10'><p>Belum ada permintaan.</p></div>", info: "Menampilkan _START_ - _END_ dari _TOTAL_ data", paginate: { next: ">", previous: "<" }},
            // [DIUBAH] Menghapus 'order' agar menggunakan urutan dari server
            order: []
        });
        
        $(`<select id="statusFilter" class="w-full md:w-auto"><option value="">Semua Status</option><option value="pending">Pending</option><option value="approved">Disetujui</option><option value="rejected">Ditolak</option></select>`).appendTo("#status-filter-slot");
        $('#requestTable_wrapper .dt-search').addClass('relative').find('input').before('<i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>');
        
        $('#statusFilter').on('change', function() {
            table.column(5).search(this.value).draw();
        });

        function renderTrendSparkline() {
            const ctx = document.getElementById('trendSparklineChart');
            if (!ctx) return;
            if (charts.sparkline) charts.sparkline.destroy();
            charts.sparkline = new Chart(ctx, {
                type: 'line',
                data: { labels: trendData.labels, datasets: [{ data: trendData.data, borderColor: 'rgba(99, 102, 241, 1)', borderWidth: 2, fill: true, backgroundColor: 'rgba(99, 102, 241, 0.1)', tension: 0.4, pointRadius: 0 }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { enabled: false } }, scales: { x: { display: false }, y: { display: false } } }
            });
        }
        
        renderTrendSparkline();
        
        const detailModal = $('#detail-modal');
        function openModal(modal) { modal.removeClass('hidden'); setTimeout(() => modal.removeClass('opacity-0').find('.modal-content').removeClass('opacity-0 scale-95'), 10); $('body').addClass('overflow-hidden'); }
        function closeModal(modal) { modal.addClass('opacity-0').find('.modal-content').addClass('opacity-0 scale-95'); setTimeout(() => { modal.addClass('hidden'); $('body').removeClass('overflow-hidden'); }, 300); }
        $('.close-modal').on('click', function() { closeModal($(this).closest('.modal-overlay')); });

        $('#requestTable tbody').on('click', '.view-btn', function() {
            const requestId = $(this).data('id');
            $.ajax({
                url: `/staff/requests/${requestId}`,
                type: 'GET',
                success: function(req) {
                    $('#detail-title').text(`Detail Permintaan REQ-${String(req.id).padStart(3, '0')}`);
                    const variant = req.product_variant;
                    const product = variant.product;
                    // [DIUBAH] Menggunakan URL gambar yang sudah diproses dari controller
                    $('#detail-image').attr('src', variant.display_image_url || 'https://placehold.co/80x80');
                    $('#detail-product-name').text(product.name);
                    $('#detail-variant-name').text(`${variant.color}${variant.ram ? ' / ' + variant.ram : ''}`);
                    $('#detail-status').html(`<span class="status-badge status-${req.status.toLowerCase()}">${req.status}</span>`);
                    $('#detail-quantity').text(`${req.quantity} unit`);
                    $('#detail-date').text(new Date(req.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }));
                    $('#detail-admin').text(req.admin ? req.admin.name : 'N/A');
                    $('#detail-notes').text(req.notes || 'Tidak ada catatan.');

                    const staffNotesContainer = $('#staff-notes-container');
                    if(req.staff_notes) {
                        $('#detail-staff-notes').text(req.staff_notes);
                        staffNotesContainer.show();
                    } else {
                        staffNotesContainer.hide();
                    }

                    openModal(detailModal);
                },
                error: function() { Swal.fire('Error', 'Gagal memuat detail permintaan.', 'error'); }
            });
        });

        $(document).on('click', '.action-btn', function() {
            const requestId = $(this).data('id');
            const action = $(this).data('action');
            
            if (action === 'rejected') {
                Swal.fire({
                    title: 'Tolak Permintaan?',
                    input: 'textarea',
                    inputLabel: 'Alasan Penolakan',
                    inputPlaceholder: 'Tuliskan alasan penolakan di sini... (opsional)',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Ya, Tolak!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        processRequest(requestId, 'rejected', result.value);
                    }
                });
            } else {
                Swal.fire({
                    title: 'Setujui Permintaan?',
                    text: "Aksi ini akan memindahkan stok ke daftar produk admin.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    confirmButtonText: 'Ya, Setujui!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        processRequest(requestId, 'approved', null);
                    }
                });
            }
        });

        function processRequest(id, status, notes) {
            $.ajax({
                url: `/staff/requests/${id}`,
                type: 'PATCH',
                data: { 
                    status: status,
                    staff_notes: notes 
                },
                success: function(response) {
                    Swal.fire('Berhasil!', response.success, 'success').then(() => location.reload());
                },
                error: function(xhr) {
                    Swal.fire('Gagal!', xhr.responseJSON.error || 'Terjadi kesalahan.', 'error');
                }
            });
        }
    });
    </script>
</body>
</html>

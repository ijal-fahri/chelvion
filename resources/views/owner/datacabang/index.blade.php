<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manajemen Cabang | CELVION</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .card { background-color: white; border-radius: 0.75rem; box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05); border: 1px solid #e5e7eb; transition: all 0.3s ease-in-out; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1); }
        .modal-overlay { transition: opacity 0.3s ease; }
        .modal-content { transition: transform 0.3s ease, opacity 0.3s ease; }
    </style>
</head>
<body>
    <div class="relative min-h-screen md:flex">
        @include('owner.partials.sidebar')
        <div class="flex-1 md:ml-64 flex flex-col">
            <main class="flex-grow p-4 sm:p-6 lg:p-8">
                @include('owner.partials.header', ['title' => 'Manajemen Cabang', 'subtitle' => 'Analisis performa cabang dan kelola data cabang.'])

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                    
                    <div class="lg:col-span-2 flex flex-col gap-6">
                        <div class="bg-white p-4 rounded-lg border shadow-sm">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="pilih-cabang" class="text-sm font-semibold text-slate-600">Pilih Cabang</label>
                                    <select id="pilih-cabang" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="all">Semua Cabang</option>
                                        @foreach ($semuaCabang as $cabang)
                                            <option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="pilih-periode" class="text-sm font-semibold text-slate-600">Pilih Periode</label>
                                    <select id="pilih-periode" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="month">Bulan Ini</option>
                                        <option value="7days">7 Hari Terakhir</option>
                                        <option value="today">Hari Ini</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4 border-t pt-4 flex items-center justify-center gap-2">
                                <button id="btn-tambah" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 flex items-center gap-2 text-sm font-semibold"><i class="bi bi-plus-circle"></i> Tambah</button>
                                <button id="btn-detail" class="bg-slate-100 text-slate-800 px-4 py-2 rounded-md hover:bg-slate-200 flex items-center gap-2 text-sm font-semibold" disabled><i class="bi bi-eye"></i> Detail</button>
                                <button id="btn-edit" class="bg-slate-100 text-slate-800 px-4 py-2 rounded-md hover:bg-slate-200 flex items-center gap-2 text-sm font-semibold" disabled><i class="bi bi-pencil"></i> Edit</button>
                                <button id="btn-hapus" class="bg-slate-100 text-slate-800 px-4 py-2 rounded-md hover:bg-slate-200 flex items-center gap-2 text-sm font-semibold" disabled><i class="bi bi-trash"></i> Hapus</button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6">
                            <div class="card p-5 flex items-center justify-between">
                                <div>
                                    <h4 class="text-slate-500 font-semibold text-sm">Total Pendapatan</h4>
                                    <p id="total-pendapatan" class="text-2xl font-bold text-slate-800">Rp 0</p>
                                    <p id="total-pendapatan-sub" class="text-xs text-gray-400 mt-1">--</p>
                                </div>
                                <div class="bg-green-100 text-green-600 p-4 rounded-full"><i class="bi bi-cash-coin text-2xl"></i></div>
                            </div>
                            <div class="card p-5 flex items-center justify-between">
                                <div>
                                    <h4 class="text-slate-500 font-semibold text-sm">Jumlah Transaksi</h4>
                                    <p id="jumlah-transaksi" class="text-2xl font-bold text-slate-800">0</p>
                                    <p id="jumlah-transaksi-sub" class="text-xs text-gray-400 mt-1">--</p>
                                </div>
                                <div class="bg-sky-100 text-sky-600 p-4 rounded-full"><i class="bi bi-receipt text-2xl"></i></div>
                            </div>
                             <div class="card p-5 flex items-center justify-between">
                                <div>
                                    <h4 class="text-slate-500 font-semibold text-sm">Order Selesai</h4>
                                    <p id="order-selesai" class="text-2xl font-bold text-slate-800">0</p>
                                    <p id="order-selesai-sub" class="text-xs text-gray-400 mt-1">--</p>
                                </div>
                                <div class="bg-indigo-100 text-indigo-600 p-4 rounded-full"><i class="bi bi-check2-circle text-2xl"></i></div>
                            </div>
                            {{-- [UPDATE] Card Pelanggan Baru diubah menjadi Total Cabang --}}
                            <div id="total-cabang-card" class="card p-5 flex items-center justify-between">
                                <div>
                                    <h4 class="text-slate-500 font-semibold text-sm">Total Cabang</h4>
                                    <p id="total-cabang" class="text-2xl font-bold text-slate-800">0</p>
                                    <p id="total-cabang-sub" class="text-xs text-gray-400 mt-1">--</p>
                                </div>
                                <div class="bg-blue-100 text-blue-600 p-4 rounded-full"><i class="bi bi-building text-2xl"></i></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="lg:col-span-3 flex flex-col gap-8">
                        <div class="card p-5"><h4 class="text-xl font-semibold text-slate-800 mb-4">Tren Pendapatan</h4><div class="h-80"><canvas id="tren-pendapatan-chart"></canvas></div></div>
                        <div class="card p-5"><h4 class="text-xl font-semibold text-slate-800 mb-4">Produk Terlaris</h4><div class="h-80"><canvas id="produk-terlaris-chart"></canvas></div></div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    {{-- Modal Detail --}}
    <div id="detail-modal" class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 hidden modal-overlay">
         <div class="relative bg-white w-full max-w-lg rounded-xl p-6 sm:p-8 modal-content opacity-0 -translate-y-10"><button class="absolute top-4 right-4 text-slate-500 hover:text-slate-800 text-2xl close-modal" data-modal-id="detail-modal">&times;</button><h3 id="detail-nama" class="text-2xl font-bold text-slate-800 mb-2">Nama Cabang</h3><hr class="mb-4"><div class="space-y-3 text-slate-600"><p><strong class="font-semibold text-slate-700">Alamat:</strong> <span id="detail-alamat" class="break-all">...</span></p><p><strong class="font-semibold text-slate-700">WhatsApp:</strong> <span id="detail-telepon">...</span></p></div><div class="mt-6 pt-4 border-t flex flex-col sm:flex-row items-center gap-3"><a id="detail-wa" href="#" target="_blank" class="w-full sm:w-auto flex-1 text-center bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 flex items-center justify-center gap-2 font-semibold"><i class="bi bi-whatsapp"></i> WhatsApp</a><a id="detail-maps" href="#" target="_blank" class="w-full sm:w-auto flex-1 text-center bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 flex items-center justify-center gap-2 font-semibold"><i class="bi bi-geo-alt-fill"></i> Google Maps</a></div></div>
    </div>
    {{-- Modal Form Tambah/Edit --}}
    <div id="form-modal" class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 hidden modal-overlay"><div class="relative bg-white w-full max-w-lg rounded-xl p-6 sm:p-8 modal-content opacity-0 -translate-y-10"><button class="absolute top-4 right-4 text-slate-500 hover:text-slate-800 text-2xl close-modal" data-modal-id="form-modal">&times;</button><h3 id="form-title" class="text-2xl font-bold text-slate-800 mb-4">Form Cabang</h3><form id="cabang-form"><div class="space-y-4"><div><label for="form-nama" class="text-sm font-semibold text-slate-600">Nama Cabang</label><input type="text" id="form-nama" name="nama_cabang" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required></div>
    {{-- [UPDATE] Form Alamat diubah menjadi Link Google Maps --}}
    <div><label for="form-alamat" class="text-sm font-semibold text-slate-600">Link Google Maps</label><input type="url" id="form-alamat" name="alamat" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="https://maps.app.goo.gl/..." required></div>
    <div><label for="form-whatsapp" class="text-sm font-semibold text-slate-600">Nomor WhatsApp</label><input type="tel" id="form-whatsapp" name="whatsapp" placeholder="Contoh: 081234567890" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required></div></div><div class="mt-6 pt-4 border-t flex justify-end gap-3"><button type="button" class="px-6 py-2 bg-slate-100 text-slate-800 rounded-md hover:bg-slate-200 font-semibold close-modal" data-modal-id="form-modal">Batal</button><button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-bold">Simpan</button></div></form></div></div>

    <script>
    $(document).ready(function() {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        let reportData = @json($reportData);
        let trenChart, produkChart;
        const detailModal = $('#detail-modal');
        const formModal = $('#form-modal');
        function formatRupiah(angka) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka); }
        function openModal(modal) { modal.removeClass('hidden'); setTimeout(() => modal.find('.modal-content').removeClass('opacity-0 -translate-y-10'), 10); }
        function closeModal(modal) { modal.find('.modal-content').addClass('opacity-0 -translate-y-10'); setTimeout(() => modal.addClass('hidden'), 300); }

        function updateDashboard(branchId = 'all', period = 'month') {
            const data = reportData[branchId];
            if (!data) return;
            $('#total-pendapatan').text(formatRupiah(data.metrics.totalPendapatan));
            $('#total-pendapatan-sub').text(data.metrics.totalPendapatanSub);
            $('#jumlah-transaksi').text(data.metrics.jumlahTransaksi.toLocaleString('id-ID'));
            $('#jumlah-transaksi-sub').text(data.metrics.jumlahTransaksiSub);
            $('#order-selesai').text(data.metrics.orderSelesai.toLocaleString('id-ID'));
            $('#order-selesai-sub').text(data.metrics.orderSelesaiSub);

            // [UPDATE] Logika untuk menampilkan card Total Cabang
            if (branchId === 'all') {
                $('#total-cabang-card').show();
                $('#total-cabang').text(data.metrics.totalCabang.toLocaleString('id-ID'));
                $('#total-cabang-sub').text(data.metrics.totalCabangSub);
            } else {
                $('#total-cabang-card').hide();
            }
            
            const actionButtons = $('#btn-detail, #btn-edit, #btn-hapus');
            if (branchId === 'all') {
                actionButtons.prop('disabled', true).removeClass('bg-indigo-100 text-indigo-700 hover:bg-indigo-200').addClass('bg-slate-100 text-slate-800');
            } else {
                actionButtons.prop('disabled', false).removeClass('bg-slate-100 text-slate-800').addClass('bg-indigo-100 text-indigo-700 hover:bg-indigo-200');
            }
            if (trenChart) trenChart.destroy();
            const trenCtx = document.getElementById('tren-pendapatan-chart').getContext('2d');
            const gradient = trenCtx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(79, 70, 229, 0.4)');
            gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');
            trenChart = new Chart(trenCtx, {
                type: 'line', data: { labels: data.trendPendapatan.labels, datasets: [{ label: 'Pendapatan (Juta Rp)', data: data.trendPendapatan.values, fill: true, backgroundColor: gradient, borderColor: 'rgba(79, 70, 229, 1)', borderWidth: 2.5, tension: 0.4, pointBackgroundColor: '#ffffff', pointBorderColor: 'rgba(79, 70, 229, 1)', pointBorderWidth: 2, pointRadius: 5, pointHoverRadius: 7, }] },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { callback: (value) => `Rp${value} Jt` }}, x: { grid: { display: false } } }, plugins: { legend: { display: false }, tooltip: { callbacks: { label: (context) => `Rp${new Intl.NumberFormat('id-ID').format(context.parsed.y * 1000000)}` }, backgroundColor: '#111827', padding: 10, cornerRadius: 8, displayColors: false } }, interaction: { intersect: false, mode: 'index' } }
            });
            if (produkChart) produkChart.destroy();
            produkChart = new Chart($('#produk-terlaris-chart'), { type: 'doughnut', data: { labels: data.produkTerlaris.labels, datasets: [{ data: data.produkTerlaris.values, backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444'] }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } } });
        }
        $('#pilih-cabang, #pilih-periode').on('change', () => updateDashboard($('#pilih-cabang').val()));
        $('#btn-tambah').on('click', function() { $('#cabang-form').trigger("reset").attr('data-mode', 'add'); $('#form-title').text('Tambah Cabang Baru'); openModal(formModal); });
        $('#btn-edit').on('click', function() {
            const branchId = $('#pilih-cabang').val();
            const data = reportData[branchId];
            if(data && data.info) {
                $('#form-title').text('Edit Data Cabang');
                $('#cabang-form').attr('data-mode', 'edit').attr('data-id', branchId);
                $('#form-nama').val(data.info.nama);
                $('#form-alamat').val(data.info.alamat);
                $('#form-whatsapp').val(data.info.telepon);
                openModal(formModal);
            }
        });
        $('#btn-detail').on('click', function(){
            const data = reportData[$('#pilih-cabang').val()];
            if(data && data.info){
                $('#detail-nama').text(data.info.nama);
                $('#detail-alamat').text(data.info.alamat);
                $('#detail-telepon').text(data.info.telepon);
                $('#detail-wa').attr('href', `https://wa.me/${data.info.whatsapp}`);
                // [UPDATE] href untuk maps langsung dari data
                $('#detail-maps').attr('href', data.info.gmaps);
                openModal(detailModal);
            }
        });
        $('#btn-hapus').on('click', function() {
            const branchId = $('#pilih-cabang').val();
            const branchName = reportData[branchId].info.nama;
            Swal.fire({
                title: 'Anda Yakin?', text: `Anda akan menghapus cabang "${branchName}". Aksi ini tidak dapat dibatalkan.`, icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/owner/data-cabang/${branchId}`, type: 'DELETE',
                        success: function(response) { Swal.fire('Terhapus!', response.success, 'success').then(() => location.reload()); },
                        error: function(xhr) { Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data.', 'error'); }
                    });
                }
            })
        });
        $('.close-modal').on('click', function() { closeModal($(this).closest('.modal-overlay')); });
        $('#cabang-form').on('submit', function(e) {
            e.preventDefault();
            const mode = $(this).attr('data-mode');
            const id = $(this).attr('data-id');
            const url = (mode === 'add') ? '{{ route("owner.datacabang.store") }}' : `/owner/data-cabang/${id}`;
            const method = (mode === 'add') ? 'POST' : 'PUT';
            $.ajax({
                url: url, type: method, data: $(this).serialize(),
                success: function(response) {
                    closeModal(formModal);
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: response.success, showConfirmButton: false, timer: 2000, timerProgressBar: true })
                    .then(() => location.reload());
                },
                error: function(xhr) {
                    let errorMsg = 'Terjadi kesalahan. Silakan coba lagi.';
                    if(xhr.responseJSON && xhr.responseJSON.errors) { errorMsg = Object.values(xhr.responseJSON.errors).map(e => e[0]).join('<br>'); }
                    Swal.fire('Gagal!', errorMsg, 'error');
                }
            });
        });
        updateDashboard();
    });
    </script>
</body>
</html>

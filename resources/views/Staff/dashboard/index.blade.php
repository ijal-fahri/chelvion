<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Stok | Staff Gudang</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Ikon & Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    
    {{-- Chart.js untuk Grafik --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    {{-- SweetAlert2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #6366f1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #4f46e5; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
        
        .status-badge { padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; text-transform: capitalize; }
        .status-baru { background-color: #dcfce7; color: #166534; }
        .status-second { background-color: #dbeafe; color: #1e40af; }
        
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
        
        @include('staff.partials.sidebar')

        <div class="flex-1 md:ml-64 flex flex-col">
            <main class="flex-grow p-4 sm:p-6 lg:p-8 animate-fade-in">
                
                @include('staff.partials.header', [
                    'title' => 'Dashboard Stok',
                    'subtitle' => 'Ringkasan aktivitas stok dan gudang hari ini.'
                ])
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center gap-5 transition-all duration-300 stat-card border-l-4 border-blue-500">
                        <div class="bg-blue-100 text-blue-600 p-4 rounded-full text-3xl"><i class="bi bi-boxes"></i></div>
                        <div>
                            <p class="text-gray-500 font-medium">Total Stok Produk</p>
                            <h3 class="text-2xl font-bold text-gray-800">{{ number_format($summary['totalStok'] ?? 0) }} Unit</h3>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center gap-5 transition-all duration-300 stat-card border-l-4 border-green-500">
                        <div class="bg-green-100 text-green-600 p-4 rounded-full text-3xl"><i class="bi bi-box-arrow-in-down"></i></div>
                        <div>
                            <p class="text-gray-500 font-medium">Barang Masuk (Hari Ini)</p>
                            <h3 class="text-2xl font-bold text-gray-800">{{ number_format($summary['barangMasukHariIni'] ?? 0) }}</h3>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center gap-5 transition-all duration-300 stat-card border-l-4 border-red-500">
                        <div class="bg-red-100 text-red-600 p-4 rounded-full text-3xl"><i class="bi bi-box-arrow-up"></i></div>
                        <div>
                            <p class="text-gray-500 font-medium">Barang Keluar (Hari Ini)</p>
                            <h3 class="text-2xl font-bold text-gray-800">{{ number_format($summary['barangKeluarHariIni'] ?? 0) }}</h3>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center gap-5 transition-all duration-300 stat-card border-l-4 border-amber-500">
                        <div class="bg-amber-100 text-amber-600 p-4 rounded-full text-3xl"><i class="bi bi-clipboard2-check-fill"></i></div>
                        <div>
                            <p class="text-gray-500 font-medium">Menunggu QC</p>
                            <h3 class="text-2xl font-bold text-gray-800">{{ number_format($summary['menungguQC'] ?? 0) }}</h3>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-8">
                        <div class="bg-white p-6 rounded-2xl shadow-lg">
                            <h4 class="text-xl font-semibold text-gray-700 mb-4">Aktivitas Stok (7 Hari Terakhir)</h4>
                            <div class="h-80"><canvas id="stockActivityChart"></canvas></div>
                        </div>

                        <div class="bg-white p-6 rounded-2xl shadow-lg">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-xl font-semibold text-gray-700">Daftar Stok Produk</h4>
                                <a href="{{ route('staff.manage.index') }}" class="text-sm font-semibold text-indigo-600 hover:underline">Lihat Semua</a>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm responsive-table">
                                    <thead class="bg-gray-50 text-gray-600 uppercase">
                                        <tr>
                                            <th class="p-3 font-semibold text-left">Nama Produk</th>
                                            <th class="p-3 font-semibold text-left">Kategori</th>
                                            <th class="p-3 font-semibold text-center">Total Stok</th>
                                            <th class="p-3 font-semibold text-center">Status</th>
                                            <th class="p-3 font-semibold text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="product-table-body">
                                        @forelse ($latestProducts as $product)
                                            <tr class="border-b hover:bg-gray-50">
                                                <td data-label="Nama Produk" class="p-3 font-semibold text-gray-800">{{ $product->name }}</td>
                                                <td data-label="Kategori" class="p-3 text-gray-500">{{ $product->category }}</td>
                                                <td data-label="Stok" class="p-3 text-center font-bold text-lg {{ $product->total_stock < 20 ? 'text-red-600' : 'text-gray-700' }}">{{ $product->total_stock }}</td>
                                                <td data-label="Status" class="p-3 text-center"><span class="status-badge status-{{ strtolower($product->status) }}">{{ $product->status }}</span></td>
                                                <td data-label="Aksi" class="p-3 text-center">
                                                    <button data-id="{{ $product->id }}" class="open-detail-modal text-indigo-600 font-semibold hover:underline">Lihat Detail</button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center p-8 text-gray-500">
                                                    <i class="bi bi-box-seam text-4xl block mb-2"></i>
                                                    Belum ada produk di gudang Anda.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-1 space-y-8">
                        <div class="bg-white p-6 rounded-2xl shadow-lg">
                            <h4 class="text-xl font-semibold text-gray-700 mb-4">Aksi Cepat</h4>
                            <div class="space-y-3">
                                <a href="{{ route('staff.inout') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold transition-colors">
                                    <i class="bi bi-arrow-left-right text-lg"></i> <span>Barang Masuk & Keluar</span>
                                </a>
                                 <a href="{{ route('staff.manage.index') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-700 font-semibold transition-colors">
                                    <i class="bi bi-boxes text-lg"></i> <span>Manajemen Stok</span>
                                </a>
                                <a href="{{ route('staff.kualitas') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-700 font-semibold transition-colors">
                                    <i class="bi bi-clipboard2-check-fill text-lg"></i> <span>Kontrol Kualitas</span>
                                </a>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-2xl shadow-lg">
                            <h4 class="text-xl font-semibold text-gray-700 mb-4">Komposisi Stok</h4>
                            <div class="h-48"><canvas id="stockCompositionChart"></canvas></div>
                            <div id="stock-composition-legend" class="mt-4 space-y-2 text-sm border-t pt-3">
                                {{-- Legend dihasilkan oleh JS --}}
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div id="product-detail-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 hidden modal-overlay">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto modal-content transform scale-95">
            <div class="flex justify-between items-center p-5 border-b sticky top-0 bg-white z-10">
                <h3 class="text-xl font-bold text-gray-800" id="modal-product-name"></h3>
                <button class="close-modal p-2 rounded-full hover:bg-gray-200"><i class="bi bi-x-lg text-xl text-gray-600"></i></button>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-gray-500 font-medium">Kategori</p>
                        <p class="font-semibold text-gray-800" id="modal-product-category"></p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-gray-500 font-medium">Kondisi</p>
                        <div id="modal-product-status"></div>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg col-span-2">
                        <p class="text-gray-500 font-medium">Total Stok (Draft & Published)</p>
                        <p class="font-semibold text-gray-800 text-lg" id="modal-product-stock"></p>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2 border-b pb-2">Detail Varian</h4>
                    <div id="modal-variants-list" class="space-y-2"></div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        $(document).ready(function () {
            AOS.init({ duration: 600, once: true });
            
            const allProductsData = @json($latestProducts);
            const stockActivityData = @json($stockActivityData);
            const stockCompositionData = @json($stockCompositionData);

            // Grafik Aktivitas Stok
            const stockCtx = document.getElementById('stockActivityChart').getContext('2d');
            new Chart(stockCtx, {
                type: 'bar', 
                data: { 
                    labels: stockActivityData.labels, 
                    datasets: [ 
                        { label: 'Barang Masuk', data: stockActivityData.in, backgroundColor: 'rgba(59, 130, 246, 0.7)', borderWidth: 1, borderRadius: 6 }, 
                        { label: 'Barang Keluar', data: stockActivityData.out, backgroundColor: 'rgba(239, 68, 68, 0.7)', borderWidth: 1, borderRadius: 6 } 
                    ] 
                },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, title: { display: true, text: 'Jumlah Unit' } }, x: { grid: { display: false } } }, plugins: { legend: { position: 'bottom' }, tooltip: { mode: 'index', intersect: false } }, interaction: { intersect: false, mode: 'index' } }
            });

            // Grafik Komposisi Stok
            const compositionCtx = document.getElementById('stockCompositionChart').getContext('2d');
            const chartColors = ['#4f46e5', '#38bdf8', '#fbbf24', '#f87171'];
            new Chart(compositionCtx, {
                type: 'doughnut', 
                data: { 
                    labels: stockCompositionData.labels, 
                    datasets: [{ data: stockCompositionData.values, backgroundColor: chartColors, borderWidth: 0, hoverOffset: 8 }] 
                },
                options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { display: false } } }
            });
            
            // Generate Legend untuk Komposisi Stok
            const legendContainer = $('#stock-composition-legend');
            legendContainer.empty();
            let totalStock = stockCompositionData.values.reduce((a, b) => a + b, 0);
            stockCompositionData.labels.forEach((label, index) => {
                const value = stockCompositionData.values[index];
                const percentage = totalStock > 0 ? ((value / totalStock) * 100).toFixed(1) : 0;
                legendContainer.append(`<div class="flex justify-between items-center"><span class="flex items-center gap-2"><div class="w-3 h-3 rounded-full" style="background-color: ${chartColors[index % chartColors.length]}"></div> ${label}</span><span class="font-semibold">${percentage}%</span></div>`);
            });
            
            const modal = $('#product-detail-modal');
            
            function openModal() { modal.removeClass('hidden'); setTimeout(() => { modal.removeClass('opacity-0'); modal.find('.modal-content').removeClass('scale-95'); }, 10); $('body').addClass('overflow-hidden'); }
            function closeModal() { modal.find('.modal-content').addClass('scale-95'); modal.addClass('opacity-0'); setTimeout(() => { modal.addClass('hidden'); }, 300); $('body').removeClass('overflow-hidden'); }
            
            $(document).on('click', '.open-detail-modal', function() {
                const productId = $(this).data('id');
                const data = allProductsData.find(p => p.id === productId);
                
                if (data) {
                    $('#modal-product-name').text(data.name);
                    $('#modal-product-category').text(data.category);
                    $('#modal-product-stock').text(`${data.total_stock} Unit`);
                    
                    const statusBadge = `<span class="status-badge status-${data.status.toLowerCase()}">${data.status}</span>`;
                    $('#modal-product-status').html(statusBadge);

                    const variantsList = $('#modal-variants-list');
                    variantsList.empty();
                    if (data.variants && data.variants.length > 0) {
                        data.variants.forEach(v => {
                            variantsList.append(`<div class="text-sm flex justify-between items-center border-b pb-2"><p>${v.color}${v.ram ? ` / ${v.ram}` : ''}</p><p class="font-semibold">${v.stock} unit</p></div>`);
                        });
                    } else {
                         variantsList.append('<p class="text-sm text-gray-500">Tidak ada varian terdaftar.</p>');
                    }
                    
                    openModal();
                }
            });

            $('.close-modal').click(closeModal);

            @if (session('success'))
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: '{{ session("success") }}', showConfirmButton: false, timer: 3000, timerProgressBar: true });
            @endif
        });
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard | E-Commerce</title>
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

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #6366f1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #4f46e5; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
    </style>
</head>
<body>
    <div class="relative min-h-screen md:flex">
        
        @include('admin.partials.sidebar')

        <div class="flex-1 md:ml-64 flex flex-col">
            <main class="flex-grow p-4 sm:p-6 lg:p-8 animate-fade-in">
                
                @include('admin.partials.header', [
                    'title' => 'Dashboard Admin',
                    'subtitle' => 'Ringkasan aktivitas toko Anda hari ini.'
                ])
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center gap-5 transition-all duration-300 stat-card border-l-4 border-sky-500">
                        <div class="bg-sky-100 text-sky-600 p-4 rounded-full text-3xl"><i class="bi bi-cash-coin"></i></div>
                        <div>
                            <p class="text-gray-500 font-medium">Pendapatan Hari Ini</p>
                            <h3 class="text-2xl font-bold text-gray-800">Rp{{ number_format($totalRevenueToday ?? 0, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center gap-5 transition-all duration-300 stat-card border-l-4 border-indigo-500">
                        <div class="bg-indigo-100 text-indigo-600 p-4 rounded-full text-3xl"><i class="bi bi-box-seam-fill"></i></div>
                        <div>
                            <p class="text-gray-500 font-medium">Total Produk</p>
                            <h3 class="text-2xl font-bold text-gray-800">{{ $productCount }}</h3>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center gap-5 transition-all duration-300 stat-card border-l-4 border-green-500">
                        <div class="bg-green-100 text-green-600 p-4 rounded-full text-3xl"><i class="bi bi-people-fill"></i></div>
                        <div>
                            <p class="text-gray-500 font-medium">Total User</p>
                            <h3 class="text-2xl font-bold text-gray-800">{{ $userCount }}</h3>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center gap-5 transition-all duration-300 stat-card border-l-4 border-amber-500">
                        <div class="bg-amber-100 text-amber-600 p-4 rounded-full text-3xl"><i class="bi bi-receipt"></i></div>
                        <div>
                            <p class="text-gray-500 font-medium">Total Order</p>
                            <h3 class="text-2xl font-bold text-gray-800">{{ $checkoutCount }}</h3>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-8">
                        <div class="bg-white p-6 rounded-2xl shadow-lg">
                            <h4 class="text-xl font-semibold text-gray-700 mb-4">Grafik Penjualan (7 Hari Terakhir)</h4>
                            <div><canvas id="salesChart"></canvas></div>
                        </div>

                        <div class="bg-white p-6 rounded-2xl shadow-lg">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-xl font-semibold text-gray-700">Orderan Terbaru</h4>
                                <a href="{{ route('admin.checkouts.index') }}" class="text-sm font-semibold text-indigo-600 hover:underline">Lihat Semua</a>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="p-3 font-semibold text-gray-600 text-left">ID Order</th>
                                            <th class="p-3 font-semibold text-gray-600 text-left">Pelanggan</th>
                                            <th class="p-3 font-semibold text-gray-600 text-left">Total</th>
                                            <th class="p-3 font-semibold text-gray-600 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($recentOrders ?? [] as $order)
                                            <tr class="border-b hover:bg-gray-50">
                                                <td class="p-3">#{{ $order->id }}</td>
                                                <td class="p-3 font-medium text-gray-700">{{ $order->user->name ?? 'N/A' }}</td>
                                                <td class="p-3">Rp{{ number_format($order->total_price ?? 0, 0, ',', '.') }}</td>
                                                <td class="p-3 text-center">
                                                    @if($order->status == 'pending')
                                                        <span class="px-2 py-1 text-xs font-semibold text-amber-800 bg-amber-100 rounded-full">Pending</span>
                                                    @elseif($order->status == 'success')
                                                        <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Success</span>
                                                    @else
                                                        <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Failed</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center p-8 text-gray-500"><i class="bi bi-receipt text-4xl block mb-2"></i>Belum ada orderan terbaru.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-1 space-y-8">
                        <div class="bg-white p-6 rounded-2xl shadow-lg">
                            <h4 class="text-xl font-semibold text-gray-700 mb-4">Produk Terlaris</h4>
                            <div class="space-y-4">
                                @forelse ($topProducts ?? [] as $product)
                                <div class="flex items-center gap-4">
                                    <img src="{{ $product->image_url ?? 'https://via.placeholder.com/150' }}" alt="{{ $product->name ?? '' }}" class="w-16 h-16 rounded-lg object-cover">
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $product->name ?? 'Nama Produk' }}</p>
                                        <p class="text-sm text-gray-500">{{ $product->sales_count ?? '0' }} Terjual</p>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center p-8 text-gray-500"><i class="bi bi-star-slash text-4xl block mb-2"></i>Data produk terlaris belum tersedia.</div>
                                @endforelse
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-2xl shadow-lg">
                            <h4 class="text-xl font-semibold text-gray-700 mb-4">Aksi Cepat</h4>
                            <div class="space-y-3">
                                <a href="{{ route('admin.products.index') }}" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold transition-colors">
                                    <i class="bi bi-box-seam-fill"></i> <span>Kelola Produk</span>
                                </a>
                                <a href="{{ route('admin.orders.index') }}" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-700 font-semibold transition-colors">
                                    <i class="bi bi-file-earmark-text-fill"></i> <span>Lihat Laporan Lengkap</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    {{-- [PERBAIKAN] Mendefinisikan data default di blok PHP untuk menghindari parse error --}}
    @php
        $defaultSalesData = [
            'labels' => ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
            'values' => [120000, 190000, 300000, 500000, 200000, 300000, 450000]
        ];
    @endphp

    <script>
        $(document).ready(function () {
             const ctx = document.getElementById('salesChart').getContext('2d');
             
             // [PERBAIKAN] Menggunakan variabel dari blok @php untuk sintaks yang lebih aman
             const salesData = @json($salesData ?? $defaultSalesData);

             new Chart(ctx, {
                type: 'line',
                data: {
                    labels: salesData.labels,
                    datasets: [{
                        label: 'Pendapatan (Rp)', data: salesData.values,
                        backgroundColor: 'rgba(79, 70, 229, 0.1)', borderColor: 'rgba(79, 70, 229, 1)',
                        borderWidth: 3, tension: 0.4, fill: true, pointBackgroundColor: 'rgba(79, 70, 229, 1)',
                        pointRadius: 5, pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, ticks: { callback: (value) => 'Rp' + new Intl.NumberFormat('id-ID').format(value) } } },
                    plugins: { legend: { display: false }, tooltip: { callbacks: { label: (context) => 'Rp' + new Intl.NumberFormat('id-ID').format(context.parsed.y) } } }
                }
            });

            @if (session('success'))
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: '{{ session("success") }}', showConfirmButton: false, timer: 3000, timerProgressBar: true });
            @endif
        });
    </script>
</body>
</html>


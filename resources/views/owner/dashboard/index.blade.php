<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Owner Dashboard | CELVION</title>
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
        
        @include('owner.partials.sidebar')

        <div class="flex-1 md:ml-64 flex flex-col">
            <main class="flex-grow p-4 sm:p-6 lg:p-8 animate-fade-in">
                
                @include('owner.partials.header', [
                    'title' => 'Dashboard Owner',
                    'subtitle' => 'Gambaran umum performa bisnis dan keuangan toko Anda.'
                ])
                
                {{-- Kartu Statistik dengan Nominal Penuh --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center justify-between transition-all duration-300 stat-card border-l-4 border-green-500">
                        <div>
                            <p class="text-gray-500 font-medium">Total Pendapatan</p>
                            <h3 class="text-xl lg:text-2xl font-bold text-gray-800">Rp{{ number_format($revenue30Days ?? 0, 0, ',', '.') }}</h3>
                            <p class="text-xs text-gray-400 mt-1">Selama 30 hari terakhir</p>
                        </div>
                        <div class="bg-green-100 text-green-600 p-4 rounded-full text-3xl"><i class="bi bi-graph-up-arrow"></i></div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center justify-between transition-all duration-300 stat-card border-l-4 border-indigo-500">
                        <div>
                            <p class="text-gray-500 font-medium">Estimasi Keuntungan</p>
                            <h3 class="text-xl lg:text-2xl font-bold text-gray-800">Rp{{ number_format($estimatedProfit ?? 0, 0, ',', '.') }}</h3>
                            <p class="text-xs text-gray-400 mt-1">Estimasi 30% dari pendapatan</p>
                        </div>
                        <div class="bg-indigo-100 text-indigo-600 p-4 rounded-full text-3xl"><i class="bi bi-wallet2"></i></div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center justify-between transition-all duration-300 stat-card border-l-4 border-sky-500">
                        <div>
                            <p class="text-gray-500 font-medium">Jumlah Pelanggan</p>
                            <h3 class="text-2xl font-bold text-gray-800">{{ number_format($customerCount ?? 0) }}</h3>
                             <p class="text-xs text-gray-400 mt-1">{{ $newCustomerCount ?? 0 }} pelanggan baru (30 hari)</p>
                        </div>
                        <div class="bg-sky-100 text-sky-600 p-4 rounded-full text-3xl"><i class="bi bi-people-fill"></i></div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center justify-between transition-all duration-300 stat-card border-l-4 border-amber-500">
                        <div>
                            <p class="text-gray-500 font-medium">Produk Terjual</p>
                            <h3 class="text-2xl font-bold text-gray-800">{{ number_format($productsSoldCount ?? 0) }}</h3>
                            <p class="text-xs text-gray-400 mt-1">Dalam 30 hari terakhir</p>
                        </div>
                        <div class="bg-amber-100 text-amber-600 p-4 rounded-full text-3xl"><i class="bi bi-bag-check-fill"></i></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-8">
                        <div class="bg-white p-6 rounded-2xl shadow-lg">
                            <h4 class="text-xl font-semibold text-gray-700 mb-4">Laporan Pendapatan Bulanan</h4>
                            <div class="h-80"><canvas id="monthlyRevenueChart"></canvas></div>
                        </div>

                        <div class="bg-white p-6 rounded-2xl shadow-lg">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-xl font-semibold text-gray-700">Transaksi Bernilai Tinggi Terbaru</h4>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="p-3 font-semibold text-gray-600 text-left">ID Transaksi</th>
                                            <th class="p-3 font-semibold text-gray-600 text-left">Pelanggan</th>
                                            <th class="p-3 font-semibold text-gray-600 text-left">Tanggal</th>
                                            <th class="p-3 font-semibold text-gray-600 text-left">Nilai</th>
                                            <th class="p-3 font-semibold text-gray-600 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($highValueTransactions as $transaction)
                                            <tr class="border-b hover:bg-gray-50">
                                                <td class="p-3">#{{ $transaction->id }}</td>
                                                <td class="p-3 font-medium text-gray-700">{{ $transaction->user->name ?? 'N/A' }}</td>
                                                <td class="p-3 text-gray-500">{{ $transaction->created_at->format('d M Y') }}</td>
                                                <td class="p-3 font-semibold">Rp{{ number_format($transaction->total_price, 0, ',', '.') }}</td>
                                                <td class="p-3 text-center"><span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Success</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center p-8 text-gray-500"><i class="bi bi-receipt text-4xl block mb-2"></i>Belum ada transaksi.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-1 space-y-8">
                        <div class="bg-white p-6 rounded-2xl shadow-lg">
                            <h4 class="text-xl font-semibold text-gray-700 mb-4">Kategori Produk Terlaris</h4>
                            <div class="h-64"><canvas id="categoryPieChart"></canvas></div>
                        </div>

                        <div class="bg-white p-6 rounded-2xl shadow-lg">
                            <h4 class="text-xl font-semibold text-gray-700 mb-4">Manajemen Toko</h4>
                            <div class="space-y-3">
                                <a href="{{ route('owner.datacabang') }}" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold transition-colors">
                                    <i class="bi bi-diagram-3-fill"></i> <span>Data Cabang</span>
                                </a>
                                 <a href="{{ route('owner.dataadmin.index') }}" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-700 font-semibold transition-colors">
                                    <i class="bi bi-person-badge-fill"></i> <span>Data Admin</span>
                                </a>
                                <a href="{{ route('owner.riwayat') }}" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-700 font-semibold transition-colors">
                                    <i class="bi bi-file-earmark-bar-graph-fill"></i> <span>Laporan Penjualan</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script>
        $(document).ready(function () {
            const revenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
            const categoryCtx = document.getElementById('categoryPieChart').getContext('2d');

            const monthlyRevenueData = @json($monthlyRevenueChartData);
            const categoryPieData = @json($categoryPieChartData);
            
            const gradient = revenueCtx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(79, 70, 229, 0.4)');
            gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');

            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: monthlyRevenueData.labels,
                    datasets: [{
                        label: 'Pendapatan (Rp)', data: monthlyRevenueData.values,
                        fill: true, backgroundColor: gradient, borderColor: 'rgba(79, 70, 229, 1)',
                        borderWidth: 2.5, tension: 0.4, pointBackgroundColor: '#ffffff',
                        pointBorderColor: 'rgba(79, 70, 229, 1)', pointBorderWidth: 2,
                        pointRadius: 5, pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, ticks: { callback: (value) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value) } } },
                    plugins: { legend: { display: false }, tooltip: { callbacks: { label: (context) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(context.parsed.y) } } }
                }
            });

            new Chart(categoryCtx, {
                type: 'pie',
                data: {
                    labels: categoryPieData.labels,
                    datasets: [{
                        label: 'Penjualan per Kategori',
                        data: categoryPieData.values,
                        backgroundColor: ['#4f46e5', '#6d28d9', '#10b981', '#f59e0b', '#3b82f6'],
                        hoverOffset: 8, borderWidth: 2, borderColor: '#ffffff'
                    }]
                },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { padding: 20, usePointStyle: true, pointStyle: 'circle' }
                            }
                        }
                    }
            });

            @if (session('success'))
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: '{{ session("success") }}', showConfirmButton: false, timer: 3000, timerProgressBar: true });
            @endif
        });
    </script>
</body>
</html>


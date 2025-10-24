<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manajemen Data Karyawan | CELVION</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; }
        .badge { padding: 4px 12px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; text-transform: capitalize; }
        .peran-kasir { background-color: #e0e7ff; color: #3730a3; }
        .peran-staff-gudang { background-color: #e2e8f0; color: #334155; }
        .status-tetap { background-color: #dcfce7; color: #166534; }
        .status-kontrak { background-color: #ffedd5; color: #9a3412; }
        .card-hover { transition: all 0.3s ease-in-out; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1); }
        .modal-overlay { transition: opacity 0.3s ease; }
        .modal-content { transition: transform 0.3s ease, opacity 0.3s ease; }
        
        @media (max-width: 767px) {
            .responsive-table thead { display: none; }
            .responsive-table tr { display: block; margin-bottom: 1rem; border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden; background-color: white; }
            .responsive-table td { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; border-bottom: 1px solid #f3f4f6; }
            .responsive-table td:last-child { border-bottom: none; }
            .responsive-table td::before { content: attr(data-label); font-weight: 600; margin-right: 1rem; color: #4b5563; }
        }
    </style>
</head>
<body>
    <div class="relative min-h-screen md:flex">
        @include('owner.partials.sidebar')

        <div class="flex-1 md:ml-64 flex flex-col">
            <main class="flex-grow p-4 sm:p-6 lg:p-8">
                @include('owner.partials.header', ['title' => 'Manajemen Data Karyawan', 'subtitle' => 'Kelola semua data karyawan di seluruh cabang.'])
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex flex-col justify-between card-hover">
                        <div>
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-gray-500">Ringkasan Karyawan</p>
                                    <h3 id="total-karyawan" class="text-3xl font-bold text-gray-800">0</h3>
                                </div>
                                <div class="bg-blue-100 text-blue-600 p-3 rounded-full text-2xl">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                            </div>
                            <div id="karyawan-avatars" class="flex items-center -space-x-2 mt-4">
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-100 flex justify-around text-center">
                            <div>
                                <p id="status-tetap-count" class="font-bold text-lg text-green-600">0</p>
                                <p class="text-xs text-gray-500">Karyawan Tetap</p>
                            </div>
                            <div>
                                <p id="status-kontrak-count" class="font-bold text-lg text-orange-500">0</p>
                                <p class="text-xs text-gray-500">Karyawan Kontrak</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-lg card-hover">
                        <h4 class="font-bold text-gray-700 mb-2">Distribusi Peran</h4>
                        <div class="h-48 flex items-center justify-center">
                            <canvas id="peranChart"></canvas>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-lg card-hover">
                        <h4 class="font-bold text-gray-700 mb-2">Karyawan per Cabang</h4>
                        <div class="h-48 flex items-center justify-center">
                             <canvas id="cabangChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-4 md:p-6 rounded-2xl shadow-lg">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
                        <div class="relative w-full sm:max-w-xs">
                            <input type="text" id="searchInput" placeholder="Cari nama atau email..." class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                        <button id="add-karyawan-btn" class="w-full sm:w-auto bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 flex items-center justify-center gap-2"><i class="bi bi-plus-circle-fill"></i><span>Tambah Karyawan</span></button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm responsive-table">
                            <thead class="bg-gray-50 text-gray-600 uppercase">
                                <tr>
                                    <th class="p-4 text-left">Info Karyawan</th>
                                    <th class="p-4 text-left">Peran</th>
                                    <th class="p-4 text-left">Cabang</th>
                                    <th class="p-4 text-left">Status</th>
                                    <th class="p-4 text-left">Masa Kerja / Kontrak</th>
                                    <th class="p-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="karyawan-table-body"></tbody>
                        </table>
                         <p id="noResults" class="text-center text-gray-500 py-8 hidden">Karyawan tidak ditemukan.</p>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div id="karyawan-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 hidden modal-overlay">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl modal-content opacity-0 transform -translate-y-10 max-h-[90vh] overflow-y-auto">
            <form id="karyawan-form">
                <div class="flex justify-between items-center p-5 border-b sticky top-0 bg-white z-10"><h3 id="modal-title" class="text-xl font-bold"></h3><button type="button" class="close-modal p-2"><i class="bi bi-x-lg"></i></button></div>
                <div class="p-6 space-y-4">
                    <input type="hidden" id="karyawanId">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Foto Profil</label>
                            <img id="photo-preview" src="https://ui-avatars.com/api/?name=?&background=eef2ff&color=7c3aed" class="w-full aspect-square rounded-full object-cover border-4 border-slate-100">
                            <label for="photo-upload" class="cursor-pointer bg-slate-100 text-slate-800 font-semibold py-2 px-4 rounded-lg text-sm block text-center mt-2 hover:bg-slate-200">Pilih Foto</label>
                            <input type="file" name="photo" id="photo-upload" class="hidden" accept="image/*">
                        </div>
                        <div class="md:col-span-2 space-y-4">
                            <div><label for="name" class="block mb-1 font-semibold text-sm">Nama Lengkap</label><input type="text" id="name" name="name" class="w-full px-3 py-2 border rounded-lg" required></div>
                            <div><label for="email" class="block mb-1 font-semibold text-sm">Email</label><input type="email" id="email" name="email" class="w-full px-3 py-2 border rounded-lg" required></div>
                            <div id="cabang-form-group"><label for="cabang" class="block mb-1 font-semibold text-sm">Cabang</label><select id="cabang" name="cabang_id" class="w-full px-3 py-2 border rounded-lg" required><option value="">Pilih</option>@foreach($cabangs as $cabang)<option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>@endforeach</select></div>
                        </div>
                    </div>
                    <div id="password-fields" class="space-y-2 pt-4 border-t">
                        <div>
                            <label for="password" class="block mb-1 font-semibold text-sm">Password</label>
                            <input type="password" id="password" name="password" class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <p id="password-helper-text" class="text-xs text-gray-500 italic"></p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t">
                        <div><label for="peran" class="block mb-1 font-semibold text-sm">Peran</label><select id="peran" name="usertype" class="w-full px-3 py-2 border rounded-lg" required><option value="">Pilih</option><option value="kasir">Kasir</option><option value="staf_gudang">Staff Gudang</option></select></div>
                        <div><label for="status" class="block mb-1 font-semibold text-sm">Status Karyawan</label><select id="status" name="status" class="w-full px-3 py-2 border rounded-lg" required><option value="Tetap">Karyawan Tetap</option><option value="Kontrak">Karyawan Kontrak</option></select></div>
                    </div>
                    <div id="contract-end-group" class="hidden">
                        <label for="contract_end" class="block mb-1 font-semibold text-sm">Tanggal Selesai Kontrak</label><input type="date" id="contract_end" name="contract_end" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>
                <div class="p-5 border-t bg-gray-50 flex justify-end gap-3 sticky bottom-0 z-10"><button type="button" class="close-modal px-4 py-2 bg-gray-200 rounded-lg">Batal</button><button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Simpan</button></div>
            </form>
        </div>
    </div>
    
    <script>
    $(document).ready(function() {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        let allKaryawanData = {};
        let peranChartInstance;
        let cabangChartInstance;

        function formatDate(dateString) {
            if (!dateString) return '—';
            const options = { day: '2-digit', month: 'short', year: 'numeric' };
            return new Date(dateString).toLocaleDateString('id-ID', options);
        }

        function renderTable(dataToRender) {
            const tableBody = $('#karyawan-table-body').empty();
            $('#noResults').toggleClass('hidden', Object.keys(dataToRender).length > 0);
            for (const id in dataToRender) {
                const data = dataToRender[id];
                const avatarUrl = data.photo || `https://ui-avatars.com/api/?name=${encodeURIComponent(data.name)}&background=random&color=fff`;
                let peranClass = data.peran.toLowerCase().replace(' ', '-');
                let workPeriod = '—';
                if (data.status === 'Tetap' && data.joining_date) workPeriod = `Bergabung ${formatDate(data.joining_date)}`;
                else if (data.status === 'Kontrak' && data.joining_date && data.contract_end) workPeriod = `${formatDate(data.joining_date)} - ${formatDate(data.contract_end)}`;
                
                const row = `<tr class="border-b hover:bg-gray-50"><td data-label="Info Karyawan" class="p-4"><div class="flex items-center gap-3"><img src="${avatarUrl}" class="w-10 h-10 rounded-full object-cover"><div><p class="font-semibold">${data.name}</p><p class="text-xs text-gray-500">${data.email}</p></div></div></td><td data-label="Peran" class="p-4"><span class="badge peran-${peranClass}">${data.peran}</span></td><td data-label="Cabang" class="p-4 text-gray-500">${data.cabang}</td><td data-label="Status" class="p-4"><span class="badge ${data.status === 'Tetap' ? 'status-tetap' : 'status-kontrak'}">${data.status}</span></td><td data-label="Masa Kerja / Kontrak" class="p-4 text-gray-500 text-xs">${workPeriod}</td><td data-label="Aksi" class="p-4"><div class="flex justify-center gap-2"><button class="edit-karyawan w-9 h-9 flex items-center justify-center rounded-md bg-amber-500 text-white hover:bg-amber-600" data-id="${id}" title="Edit"><i class="bi bi-pencil-fill"></i></button><button class="delete-karyawan w-9 h-9 flex items-center justify-center rounded-md bg-red-500 text-white hover:bg-red-600" data-id="${id}" title="Hapus"><i class="bi bi-trash-fill"></i></button></div></td></tr>`;
                tableBody.append(row);
            }
        }
        
        function updateDashboardStats(data) {
            $('#total-karyawan').text(data.stats.total);
            $('#status-tetap-count').text(data.stats.tetap);
            $('#status-kontrak-count').text(data.stats.kontrak);

            const avatarContainer = $('#karyawan-avatars').empty();
            const allKaryawan = Object.values(data.karyawan);
            const avatarsToShow = allKaryawan.slice(0, 4);
            const remaining = allKaryawan.length - avatarsToShow.length;
            avatarsToShow.forEach(k => {
                const avatarUrl = k.photo || `https://ui-avatars.com/api/?name=${encodeURIComponent(k.name)}&background=random&color=fff`;
                avatarContainer.append(`<img src="${avatarUrl}" class="w-10 h-10 rounded-full border-2 border-white object-cover" title="${k.name}">`);
            });
            if (remaining > 0) {
                avatarContainer.append(`<div class="w-10 h-10 rounded-full border-2 border-white bg-gray-200 text-gray-600 flex items-center justify-center text-xs font-bold" title="${remaining} karyawan lainnya">+${remaining}</div>`);
            }
            
            if (peranChartInstance) peranChartInstance.destroy();
            const peranCtx = document.getElementById('peranChart').getContext('2d');
            peranChartInstance = new Chart(peranCtx, {
                type: 'doughnut',
                data: { labels: data.peran_chart.labels, datasets: [{ data: data.peran_chart.values, backgroundColor: ['#6366f1', '#64748b', '#059669', '#f59e0b'], borderColor: '#ffffff', borderWidth: 2 }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 15 } } } }
            });

            if (cabangChartInstance) cabangChartInstance.destroy();
            const cabangCtx = document.getElementById('cabangChart').getContext('2d');
            cabangChartInstance = new Chart(cabangCtx, {
                type: 'bar',
                data: { labels: data.cabang_chart.labels, datasets: [{ label: 'Jumlah Karyawan', data: data.cabang_chart.values, backgroundColor: '#3b82f6', borderRadius: 5 }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } }, x: { grid: { display: false } } } }
            });
        }
        
        function loadData() {
            $.ajax({
                url: "{{ route('owner.datakaryawan.data') }}",
                type: 'GET',
                success: function(response) {
                    allKaryawanData = response.karyawan;
                    renderTable(allKaryawanData);
                    updateDashboardStats(response);
                },
                error: function() {
                    Swal.fire('Error', 'Gagal memuat data karyawan.', 'error');
                }
            });
        }

        const filterData = () => {
            const searchTerm = $('#searchInput').val().toLowerCase();
            const filtered = Object.fromEntries(Object.entries(allKaryawanData).filter(([id, k]) => k.name.toLowerCase().includes(searchTerm) || k.email.toLowerCase().includes(searchTerm)));
            renderTable(filtered);
        };
        $('#searchInput').on('keyup', filterData);
        
        const karyawanModal = $('#karyawan-modal');
        const openModal = () => { karyawanModal.removeClass('hidden'); setTimeout(() => karyawanModal.find('.modal-content').removeClass('opacity-0 -translate-y-10'), 10); };
        const closeModal = () => { karyawanModal.find('.modal-content').addClass('opacity-0 -translate-y-10'); setTimeout(() => karyawanModal.addClass('hidden'), 300); };

        $('#status').on('change', function() {
            const isContract = $(this).val() === 'Kontrak';
            $('#contract-end-group').toggleClass('hidden', !isContract);
            $('#contract_end').prop('required', isContract);
        });

        $('#photo-upload').on('change', (e) => {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = (event) => $('#photo-preview').attr('src', event.target.result);
                reader.readAsDataURL(e.target.files[0]);
            }
        });

        $('#add-karyawan-btn').on('click', () => {
            $('#modal-title').text('Tambah Karyawan Baru');
            $('#karyawan-form')[0].reset();
            $('#karyawanId').val('');
            $('#photo-preview').attr('src', 'https://ui-avatars.com/api/?name=?&background=eef2ff&color=7c3aed');
            $('#password-fields').show();
            $('#password').prop('required', true);
            $('#password-helper-text').text('Minimal 8 karakter.');
            $('#status').val('Tetap').trigger('change');
            openModal();
        });

        $('body').on('click', '.edit-karyawan', function() {
            const id = $(this).data('id');
            const data = allKaryawanData[id];
            if (data) {
                $('#modal-title').text('Edit Data Karyawan');
                $('#karyawanId').val(id); 
                $('#name').val(data.name); 
                $('#email').val(data.email); 
                $('#peran').val(data.usertype); 
                $('#cabang').val(data.cabang_id); 
                $('#status').val(data.status).trigger('change'); 
                $('#contract_end').val(data.contract_end);
                $('#photo-preview').attr('src', data.photo || `https://ui-avatars.com/api/?name=${encodeURIComponent(data.name)}&background=random&color=fff`);
                
                $('#password-fields').show();
                $('#password').prop('required', false).val('');
                $('#password-helper-text').text('Kosongkan jika tidak ingin mengubah password.');
                
                openModal();
            }
        });

        $('body').on('click', '.delete-karyawan', function() {
            const id = $(this).data('id');
            const name = allKaryawanData[id].name;
            Swal.fire({
                title: 'Anda yakin?', html: `Karyawan "<b>${name}</b>" akan dihapus.`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#6b7280', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/owner/datakaryawan/${id}`,
                        type: 'DELETE',
                        success: function(response) {
                            loadData();
                            Swal.fire('Terhapus!', response.success, 'success');
                        },
                        error: function(xhr) {
                            Swal.fire('Gagal!', xhr.responseJSON.error || 'Gagal menghapus karyawan.', 'error');
                        }
                    });
                }
            });
        });
        
        $('.close-modal').on('click', () => closeModal());
        
        $('#karyawan-form').on('submit', function(e) {
            e.preventDefault();
            const id = $('#karyawanId').val();
            const url = id ? `/owner/datakaryawan/${id}` : "{{ route('owner.datakaryawan.store') }}";
            const method = 'POST';
            
            const formData = new FormData(this);
            // [PERBAIKAN] Hapus _method spoofing karena route sudah benar menggunakan POST
            /*
            if (id) {
                formData.append('_method', 'PUT');
            }
            */

            $.ajax({
                url: url,
                method: method,
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    closeModal();
                    loadData();
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.success, timer: 2000, showConfirmButton: false });
                },
                error: function(xhr) {
                    let errorMsg = '<ul>';
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        errorMsg += `<li>${value[0]}</li>`;
                    });
                    errorMsg += '</ul>';
                    Swal.fire({ icon: 'error', title: 'Gagal!', html: errorMsg });
                }
            });
        });
        
        loadData();
    });
    </script>
</body>
</html>


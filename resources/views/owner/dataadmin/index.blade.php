<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manajemen Data Admin | CELVION</title>
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
        .role-admin-cabang { background-color: #e0e7ff; color: #3730a3; }
        .status-tetap { background-color: #dcfce7; color: #166534; }
        .status-kontrak { background-color: #ffedd5; color: #9a3412; }
        .card-hover { transition: all 0.3s ease-in-out; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1); }
        .modal-overlay { transition: opacity 0.3s ease; }
        .modal-content { transition: transform 0.3s ease, opacity 0.3s ease; }
    </style>
</head>
<body>
    <div class="relative min-h-screen md:flex">
        @include('owner.partials.sidebar')

        <div class="flex-1 md:ml-64 flex flex-col">
            <main class="flex-grow p-4 sm:p-6 lg:p-8">
                @include('owner.partials.header', ['title' => 'Manajemen Data Admin', 'subtitle' => 'Kelola akun administrator yang memiliki akses ke panel.'])
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex flex-col justify-between card-hover">
                        <div>
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-gray-500">Total Admin Cabang</p>
                                    <h3 id="total-admin" class="text-3xl font-bold text-gray-800">0</h3>
                                </div>
                                <div class="bg-blue-100 text-blue-600 p-3 rounded-full text-2xl">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                            </div>
                            <div id="admin-avatars" class="flex items-center -space-x-2 mt-4">
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
                        <h4 class="font-bold text-gray-700 mb-2">Admin per Cabang</h4>
                        <div class="h-48 flex items-center justify-center">
                             <canvas id="cabangChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-4 md:p-6 rounded-2xl shadow-lg">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
                        <div class="relative w-full sm:max-w-xs">
                            <input type="text" id="searchInput" placeholder="Cari admin..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                        <button id="add-admin-btn" class="w-full sm:w-auto bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center justify-center gap-2"><i class="bi bi-plus-circle-fill"></i><span>Tambah Admin</span></button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-600 uppercase">
                                <tr>
                                    <th class="p-4 text-left">Info Admin</th>
                                    <th class="p-4 text-left">Peran</th>
                                    <th class="p-4 text-left">Cabang</th>
                                    <th class="p-4 text-left">Status</th>
                                    <th class="p-4 text-left">Masa Kerja / Kontrak</th>
                                    <th class="p-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="admin-table-body"></tbody>
                        </table>
                         <p id="noResults" class="text-center text-gray-500 py-8 hidden">Admin tidak ditemukan.</p>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div id="admin-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 hidden modal-overlay">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl modal-content opacity-0 transform -translate-y-10">
            <form id="admin-form">
                <div class="flex justify-between items-center p-5 border-b"><h3 id="modal-title" class="text-xl font-bold text-gray-800"></h3><button type="button" class="close-modal p-2"><i class="bi bi-x-lg text-xl"></i></button></div>
                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    <input type="hidden" id="adminId">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Foto Profil</label>
                            <img id="photo-preview" src="https://ui-avatars.com/api/?name=?&background=eef2ff&color=7c3aed" class="w-full aspect-square rounded-full object-cover border-4 border-slate-100">
                            <label for="photo-upload" class="cursor-pointer bg-slate-100 text-slate-800 font-semibold py-2 px-4 rounded-lg text-sm block text-center mt-2 hover:bg-slate-200">Pilih Foto</label>
                            <input type="file" name="photo" id="photo-upload" class="hidden" accept="image/*">
                        </div>
                        <div class="md:col-span-2 space-y-4">
                            <div><label for="name" class="block mb-1 font-semibold text-sm">Nama Lengkap</label><input type="text" id="name" name="name" class="w-full px-3 py-2 border rounded-lg" required></div>
                            <div><label for="email" class="block mb-1 font-semibold text-sm">Alamat Email</label><input type="email" id="email" name="email" class="w-full px-3 py-2 border rounded-lg" required></div>
                            <div id="cabang-form-group"><label for="cabang" class="block mb-1 font-semibold text-sm">Cabang</label><select id="cabang" name="cabang_id" class="w-full px-3 py-2 border rounded-lg" required><option value="">Pilih Cabang</option>@foreach($cabangs as $cabang)<option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>@endforeach</select></div>
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
                        <div><label for="status" class="block mb-1 font-semibold text-sm">Status Karyawan</label><select id="status" name="status" class="w-full px-3 py-2 border rounded-lg" required><option value="Tetap">Karyawan Tetap</option><option value="Kontrak">Karyawan Kontrak</option></select></div>
                        <div id="contract-end-group" class="hidden">
                            <label for="contract_end" class="block mb-1 font-semibold text-sm">Tanggal Selesai Kontrak</label><input type="date" id="contract_end" name="contract_end" class="w-full px-3 py-2 border rounded-lg">
                        </div>
                    </div>
                </div>
                <div class="p-5 border-t bg-gray-50 flex justify-end gap-3"><button type="button" class="close-modal px-4 py-2 bg-gray-200 rounded-lg">Batal</button><button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Simpan</button></div>
            </form>
        </div>
    </div>
    
    <script>
    $(document).ready(function() {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        let allAdminData = {};
        let cabangChartInstance;

        function formatDate(dateString) {
            if (!dateString) return '—';
            const options = { day: '2-digit', month: 'short', year: 'numeric' };
            return new Date(dateString).toLocaleDateString('id-ID', options);
        }

        function renderTable(dataToRender) {
            const tableBody = $('#admin-table-body').empty();
            $('#noResults').toggleClass('hidden', Object.keys(dataToRender).length > 0);

            for (const id in dataToRender) {
                const data = dataToRender[id];
                const avatarUrl = data.photo || `https://ui-avatars.com/api/?name=${encodeURIComponent(data.name)}&background=random&color=fff`;
                let workPeriod = '—';
                if (data.status === 'Tetap' && data.joining_date) {
                    workPeriod = `Bergabung ${formatDate(data.joining_date)}`;
                } else if (data.status === 'Kontrak' && data.joining_date && data.contract_end) {
                    workPeriod = `${formatDate(data.joining_date)} - ${formatDate(data.contract_end)}`;
                }
                
                const row = `<tr class="border-b hover:bg-gray-50"><td class="p-4"><div class="flex items-center gap-3"><img src="${avatarUrl}" class="w-10 h-10 rounded-full object-cover"><div><p class="font-semibold">${data.name}</p><p class="text-xs text-gray-500">${data.email}</p></div></div></td><td class="p-4"><span class="badge role-admin-cabang">${data.role}</span></td><td class="p-4 text-gray-500">${data.cabang || 'N/A'}</td><td class="p-4"><span class="badge ${data.status === 'Tetap' ? 'status-tetap' : 'status-kontrak'}">${data.status}</span></td><td class="p-4 text-gray-500 text-xs">${workPeriod}</td><td class="p-4"><div class="flex justify-center md:justify-center gap-2"><button class="edit-admin w-9 h-9 flex items-center justify-center rounded-md text-white bg-amber-500 hover:bg-amber-600" data-id="${id}" title="Edit"><i class="bi bi-pencil-fill"></i></button><button class="delete-admin w-9 h-9 flex items-center justify-center rounded-md text-white bg-red-500 hover:bg-red-600" data-id="${id}" title="Hapus"><i class="bi bi-trash-fill"></i></button></div></td></tr>`;
                tableBody.append(row);
            }
        }
        
        function updateDashboard(data) {
            $('#total-admin').text(data.stats.total);
            $('#status-tetap-count').text(data.stats.tetap);
            $('#status-kontrak-count').text(data.stats.kontrak);

            const avatarContainer = $('#admin-avatars').empty();
            const admins = Object.values(data.admins);
            const avatarsToShow = admins.slice(0, 4);
            const remaining = admins.length - avatarsToShow.length;
            avatarsToShow.forEach(admin => {
                const avatarUrl = admin.photo || `https://ui-avatars.com/api/?name=${encodeURIComponent(admin.name)}&background=random&color=fff`;
                avatarContainer.append(`<img src="${avatarUrl}" class="w-10 h-10 rounded-full border-2 border-white object-cover" title="${admin.name}">`);
            });
            if (remaining > 0) {
                avatarContainer.append(`<div class="w-10 h-10 rounded-full border-2 border-white bg-gray-200 text-gray-600 flex items-center justify-center text-xs font-bold" title="${remaining} admin lainnya">+${remaining}</div>`);
            }

            if (cabangChartInstance) cabangChartInstance.destroy();
            const cabangCtx = $('#cabangChart').get(0).getContext('2d');
            cabangChartInstance = new Chart(cabangCtx, {
                type: 'doughnut',
                data: { 
                    labels: data.cabang_chart.labels, 
                    datasets: [{ 
                        data: data.cabang_chart.values, 
                        backgroundColor: ['#6366f1', '#818cf8', '#a78bfa', '#c4b5fd', '#60a5fa', '#38bdf8'],
                        borderColor: '#ffffff',
                        borderWidth: 2
                    }] 
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 15 } } } }
            });
        }

        function loadData() {
            $.ajax({
                url: "{{ route('owner.dataadmin.data') }}",
                type: 'GET',
                success: function(response) {
                    allAdminData = response.admins;
                    renderTable(allAdminData);
                    updateDashboard(response);
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Gagal memuat data admin.', 'error');
                }
            });
        }
        
        function filterData() {
            const searchTerm = $('#searchInput').val().toLowerCase();
            const filtered = Object.fromEntries(Object.entries(allAdminData).filter(([id, admin]) => admin.name.toLowerCase().includes(searchTerm) || admin.email.toLowerCase().includes(searchTerm)));
            renderTable(filtered);
        }
        
        $('#searchInput').on('keyup', filterData);
        
        const adminModal = $('#admin-modal');
        function openModal() { adminModal.removeClass('hidden'); setTimeout(() => adminModal.find('.modal-content').removeClass('opacity-0 -translate-y-10'), 10); }
        function closeModal() { adminModal.find('.modal-content').addClass('opacity-0 -translate-y-10'); setTimeout(() => adminModal.addClass('hidden'), 300); }

        $('#status').on('change', function() {
            $('#contract-end-group').toggleClass('hidden', $(this).val() !== 'Kontrak');
            $('#contract_end').prop('required', $(this).val() === 'Kontrak');
        });

        $('#photo-upload').on('change', (e) => {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = (event) => $('#photo-preview').attr('src', event.target.result);
                reader.readAsDataURL(e.target.files[0]);
            }
        });

        $('#add-admin-btn').on('click', () => {
            $('#modal-title').text('Tambah Admin Cabang');
            $('#admin-form')[0].reset();
            $('#adminId').val('');
            $('#password-fields').show();
            $('#password').prop('required', true);
            $('#password-helper-text').text('Minimal 8 karakter.');
            $('#photo-preview').attr('src', 'https://ui-avatars.com/api/?name=?&background=eef2ff&color=7c3aed');
            $('#status').val('Tetap').trigger('change');
            openModal();
        });

        $('body').on('click', '.edit-admin', function() {
            const id = $(this).data('id');
            const data = allAdminData[id];
            if (data) {
                $('#modal-title').text('Edit Data Admin');
                $('#adminId').val(id); 
                $('#name').val(data.name); 
                $('#email').val(data.email); 
                $('#status').val(data.status).trigger('change');
                $('#contract_end').val(data.contract_end); 
                $('#cabang').val(data.cabang_id);
                $('#photo-preview').attr('src', data.photo || `https://ui-avatars.com/api/?name=${encodeURIComponent(data.name)}&background=random&color=fff`);
                
                $('#password-fields').show();
                $('#password').prop('required', false).val('');
                $('#password-helper-text').text('Kosongkan jika tidak ingin mengubah password.');

                openModal();
            }
        });
        
        $('body').on('click', '.delete-admin', function() {
            const id = $(this).data('id');
            const name = allAdminData[id].name;
            Swal.fire({
                title: 'Anda yakin?', html: `Admin "<b>${name}</b>" akan dihapus.`, icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#d33', cancelButtonColor: '#6b7280', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/owner/dataadmin/${id}`,
                        type: 'DELETE',
                        success: function(response) {
                            loadData();
                            Swal.fire('Terhapus!', response.success, 'success');
                        },
                        error: function(xhr) {
                            Swal.fire('Gagal!', xhr.responseJSON.error || 'Gagal menghapus admin.', 'error');
                        }
                    });
                }
            });
        });
        
        $('.close-modal').on('click', () => closeModal());
        
        $('#admin-form').on('submit', function(e) {
            e.preventDefault();
            const id = $('#adminId').val();
            const url = id ? `/owner/dataadmin/${id}` : "{{ route('owner.dataadmin.store') }}";
            const method = 'POST';
            
            const formData = new FormData(this);
            // [DIHAPUS] Baris _method spoofing tidak diperlukan karena route sudah POST
            // if (id) {
            //     formData.append('_method', 'PUT');
            // }

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


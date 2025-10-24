<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Http\Controllers\Pelanggan\KategoriPelangganController;
use App\Http\Controllers\Pelanggan\OrderanPelangganController;
use App\Http\Controllers\Pelanggan\BantuanPelangganController;
use App\Http\Controllers\Pelanggan\DashboardController;
use App\Http\Controllers\Pelanggan\CartController;
use App\Http\Controllers\Pelanggan\CheckoutController;
use App\Http\Controllers\Pelanggan\ProfileController;
use App\Http\Controllers\Pelanggan\ProductController;
use App\Http\Controllers\Pelanggan\ReviewController;
// admin
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\CheckoutAdminController;
use App\Http\Controllers\Admin\AdminRequestController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\OrderController;
// kasir
use App\Http\Controllers\Kasir\KasirTradeController;
use App\Http\Controllers\Kasir\KasirDashboardController;
use App\Http\Controllers\Kasir\KasirTransaksiController;
use App\Http\Controllers\Kasir\KasirOnlineOrderController;
use App\Http\Controllers\Kasir\LaporanKasirController;
use App\Http\Controllers\Kasir\KasirRiwayatController;
use App\Http\Controllers\Kasir\KasirProfileController;
// staff
use App\Http\Controllers\Staff\StafStokController;
use App\Http\Controllers\Staff\StaffRequestController;
use App\Http\Controllers\Staff\StaffSecondStockController;
use App\Http\Controllers\Staff\StaffInOutController;
use App\Http\Controllers\Staff\StaffDashboardController;
// owner
use App\Http\Controllers\Owner\OwnerDataAdminController;
use App\Http\Controllers\Owner\OwnerDataCabangController;
use App\Http\Controllers\Owner\OwnerDataKaryawanController;
use App\Http\Controllers\Owner\OwnerDataVoucherController;
use App\Http\Controllers\Owner\OwnerDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ========================= HALAMAN PUBLIK ========================= //
Route::get('/', function () {
    $products = Product::with('variants')->latest()->take(8)->get();
    return view('welcome', compact('products'));
});

Route::get('pelanggan/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');


// ========================= RUTE AUTENTIKASI ========================= //
require __DIR__.'/auth.php';

// ========================= GRUP ROUTE USER (SETELAH LOGIN) ========================= //
Route::middleware('auth')->group(function () {

    // --- Manajemen Keranjang (Cart) ---
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
    Route::patch('/cart/update/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/cart/summary', [CartController::class, 'getSummary'])->name('cart.summary');

    // --- Proses Checkout ---
    Route::get('/checkout/create', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // --- Profil Pengguna ---
    Route::get('/profil', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/orders', [OrderanPelangganController::class, 'orders'])->name('pelanggan.orders.index');
    Route::get('/orders/{id}', [OrderanPelangganController::class, 'show'])->name('pelanggan.orders.show');

    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store')->middleware('auth');
});

 // routes/web.php

Route::get('pelanggan/kategori', [KategoriPelangganController::class, 'showCategory'])->name('kategori.index');
    Route::get('pelanggan/orderan', [OrderanPelangganController::class, 'index'])->name('orderan.index');
    Route::get('pelanggan/bantuan', [BantuanPelangganController::class, 'index'])->name('bantuan.index');
    // Rute untuk halaman detail produk publik
Route::get('/products/{product}', [ProductController::class, 'showDetail'])->name('products.show');


// =======================================================
// GRUP ROUTE UNTUK OWNER
// =======================================================
Route::middleware(['auth', 'owner'])->prefix('owner')->name('owner.')->group(function () {
    
    Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');
    
    Route::prefix('dataadmin')->name('dataadmin.')->group(function() {
        Route::get('/', [OwnerDataAdminController::class, 'index'])->name('index');
        Route::get('/data', [OwnerDataAdminController::class, 'getData'])->name('data');
        Route::post('/store', [OwnerDataAdminController::class, 'store'])->name('store');
        Route::post('/{user}', [OwnerDataAdminController::class, 'update'])->name('update');
        Route::delete('/{user}', [OwnerDataAdminController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('datakaryawan')->name('datakaryawan.')->group(function() {
        Route::get('/', [OwnerDataKaryawanController::class, 'index'])->name('index');
        Route::get('/data', [OwnerDataKaryawanController::class, 'getData'])->name('data');
        Route::post('/store', [OwnerDataKaryawanController::class, 'store'])->name('store');
        Route::post('/{user}', [OwnerDataKaryawanController::class, 'update'])->name('update');
        Route::delete('/{user}', [OwnerDataKaryawanController::class, 'destroy'])->name('destroy');
    });

    // [DIUBAH] Grup route untuk Manajemen Data Voucher
    Route::prefix('datavoucher')->name('datavoucher.')->group(function() {
        Route::get('/', [OwnerDataVoucherController::class, 'index'])->name('index');
        Route::get('/data', [OwnerDataVoucherController::class, 'getData'])->name('data');
        Route::post('/store', [OwnerDataVoucherController::class, 'store'])->name('store');
        Route::post('/{voucher}', [OwnerDataVoucherController::class, 'update'])->name('update');
        Route::delete('/{voucher}', [OwnerDataVoucherController::class, 'destroy'])->name('destroy');
    });

    // Rute statis Anda yang lain
    Route::get('/riwayat', function () { return view('owner.riwayat.index'); })->name('riwayat');
    Route::get('/profile', function () { return view('owner.profile.index'); })->name('profile');
    Route::get('/manage', function () { return view('owner.manage.index'); })->name('manage');
    
    // CRUD Cabang
    Route::get('/data-cabang', [OwnerDataCabangController::class, 'index'])->name('datacabang');
    Route::post('/data-cabang', [OwnerDataCabangController::class, 'store'])->name('datacabang.store');
    Route::put('/data-cabang/{id}', [OwnerDataCabangController::class, 'update'])->name('datacabang.update');
    Route::delete('/data-cabang/{id}', [OwnerDataCabangController::class, 'destroy'])->name('datacabang.destroy');
});


// =======================================================
// GRUP ROUTE UNTUK STAF GUDANG
// =======================================================
Route::middleware(['auth', 'staf_gudang'])->prefix('staff')->name('staff.')->group(function () {
    
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');

    Route::prefix('manage')->name('manage.')->group(function () {
        Route::get('/', [StafStokController::class, 'index'])->name('index');
        Route::post('/store', [StafStokController::class, 'store'])->name('store');
        Route::get('/products/{product}/edit', [StafStokController::class, 'edit'])->name('edit');
        Route::put('/variants/{variant}', [StafStokController::class, 'update'])->name('update');
        Route::delete('/products/{product}', [StafStokController::class, 'destroy'])->name('destroy');
        Route::delete('/variants/{variant}', [StafStokController::class, 'destroyVariant'])->name('variants.destroy');
    });

    Route::prefix('second-stock')->name('second.')->group(function() {
        Route::get('/', [StaffSecondStockController::class, 'index'])->name('index');
        Route::get('/data', [StaffSecondStockController::class, 'getData'])->name('data');
        Route::get('/summary', [StaffSecondStockController::class, 'getSummary'])->name('summary');
        Route::post('/{tradeIn}/submit', [StaffSecondStockController::class, 'submitToAdmin'])->name('submit');
    });

    Route::get('/requests', [StaffRequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/{stockRequest}', [StaffRequestController::class, 'show'])->name('requests.show');
    Route::patch('/requests/{stockRequest}', [StaffRequestController::class, 'update'])->name('requests.update');
    
    // [DIUBAH] Route untuk Monitoring Logistik (In/Out) dibuat dinamis
    Route::get('/inout', [StaffInOutController::class, 'index'])->name('inout');
    Route::get('/inout/data', [StaffInOutController::class, 'getData'])->name('inout.data');

    // Route statis lainnya
    Route::get('/kualitas', function () { return view('staff.kualitas.index'); })->name('kualitas');
    Route::get('/laporan', function () { return view('staff.laporan.index'); })->name('laporan');
    Route::get('/profile', function () { return view('staff.profile.index'); })->name('profile');
    Route::get('/notifikasi', function () { return view('staff.notifikasi.index'); })->name('notifikasi.index');
});




// =======================================================
// GRUP ROUTE KHUSUS KASIR
// =======================================================
Route::middleware(['auth', 'kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', [KasirDashboardController::class, 'index'])->name('dashboard');
    Route::get('/transaksi', [KasirTransaksiController::class, 'index'])->name('transaksi');
    Route::post('/transaksi/store', [KasirTransaksiController::class, 'store'])->name('transaksi.store');


    // Rute untuk Transaksi Online
    Route::get('/online', [KasirOnlineOrderController::class, 'index'])->name('online');
    Route::get('/online/data', [KasirOnlineOrderController::class, 'getData'])->name('online.data');
    Route::get('/online/summary', [KasirOnlineOrderController::class, 'getSummary'])->name('online.summary');
    Route::patch('/online/{order_number}/status', [KasirOnlineOrderController::class, 'updateStatus'])->name('online.updateStatus');

    // Rute untuk Riwayat Transaksi
    Route::get('/riwayat', [KasirRiwayatController::class, 'index'])->name('riwayat');
    Route::get('/riwayat/data', [KasirRiwayatController::class, 'getData'])->name('riwayat.data');

    // Rute untuk Tukar Tambah (Kualitas)
    Route::prefix('kualitas')->name('kualitas.')->group(function () {
        Route::get('/', [KasirTradeController::class, 'index'])->name('index');
        Route::get('/products', [KasirTradeController::class, 'getAvailableProducts'])->name('products');
        Route::get('/chart-data', [KasirTradeController::class, 'getChartData'])->name('chart-data');
        Route::post('/store', [KasirTradeController::class, 'store'])->name('store');
    });

    Route::get('/laporan', [LaporanKasirController::class, 'index'])->name('laporan'); 
    Route::get('/laporan/download', [LaporanKasirController::class, 'downloadPDF'])->name('laporan.download');

    Route::get('/profile', [KasirProfileController::class, 'index'])->name('profile');
// Rute untuk memproses update profil (via AJAX)
Route::post('/profile/update', [KasirProfileController::class, 'update'])->name('profile.update');
});

// ========================= GRUP ROUTE KHUSUS ADMIN ========================= //
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('dashboard');
    Route::view('/data-karyawan', 'admin.datakaryawan.index')->name('employees.index');
    
    // Grup route untuk Permintaan Stok
    Route::prefix('requests')->name('requests.')->group(function() {
        Route::get('/', [AdminRequestController::class, 'index'])->name('index');
        Route::post('/', [AdminRequestController::class, 'store'])->name('store');
        Route::get('/{stockRequest}', [AdminRequestController::class, 'show'])->name('show');
    });

    // Grup route untuk Manajemen Produk Admin
    Route::prefix('products')->name('products.')->group(function() {
        // Halaman utama
        Route::get('/', [AdminProductController::class, 'index'])->name('index');
        
        // API endpoints untuk data
        Route::get('/api/get', [AdminProductController::class, 'getProductsData'])->name('api.get');
        Route::get('/api/summary', [AdminProductController::class, 'getSummary'])->name('api.summary');
        Route::get('/api/new', [AdminProductController::class, 'getNewProducts'])->name('api.new');
        
        // Halaman produk baru yang di-approve
        Route::get('/new-approved', [AdminProductController::class, 'newApprovedProducts'])->name('new-approved');
        
        // CRUD operations
        Route::get('/{product}/edit', [AdminProductController::class, 'edit'])->name('edit');
        
        // [BARU] Route untuk mengubah status tampil produk di etalase
        Route::patch('/{product}/toggle-display', [AdminProductController::class, 'toggleDisplayStatus'])->name('toggleDisplay');

        Route::put('/{product}', [AdminProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [AdminProductController::class, 'destroy'])->name('destroy');
    });

    // Manajemen Order
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('orders/data', [OrderController::class, 'data'])->name('orders.data');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    // Manajemen Checkout
    Route::resource('checkouts', CheckoutAdminController::class);
    Route::get('/checkouts/invoice/{id}', [CheckoutAdminController::class, 'invoice'])->name('checkouts.invoice');
    Route::patch('/checkouts/{id}/update-status', [CheckoutAdminController::class, 'updateStatus'])->name('checkouts.updateStatus');

    // Profil Admin
    Route::get('/profile', function () {
        return view('admin.profile.index');
    })->name('profile');
});


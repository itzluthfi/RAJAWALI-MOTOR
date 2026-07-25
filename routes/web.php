<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\PengaturanTokoController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LayananDetailController;

Route::get('/', HomeController::class)->name('home');

Route::prefix('layanan')->name('layanan.')->group(function () {
    Route::get('/ganti-oli', [LayananDetailController::class, 'gantiOli'])->name('ganti-oli');
    Route::get('/tune-up', [LayananDetailController::class, 'tuneUp'])->name('tune-up');
    Route::get('/ban-spooring', [LayananDetailController::class, 'banSpooring'])->name('ban-spooring');
    Route::get('/kelistrikan', [LayananDetailController::class, 'kelistrikan'])->name('kelistrikan');
    Route::get('/injeksi', [LayananDetailController::class, 'injeksi'])->name('injeksi');
    Route::get('/ac-mobil', [LayananDetailController::class, 'acMobil'])->name('ac-mobil');
    Route::get('/body-repair', [LayananDetailController::class, 'bodyRepair'])->name('body-repair');
});

Route::prefix('admin')->group(function () {
    Route::get('/', fn () => redirect()->route(auth()->check() ? 'dashboard' : 'login'));

    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'create'])->name('login');
        Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    });

    Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

    Route::middleware('auth')->group(function () {
        $semuaPeran = 'owner,admin,kasir,gudang,montir';

        Route::get('/dashboard', fn () => view('dashboard.index'))->middleware("peran:{$semuaPeran}")->name('dashboard');

        Route::get('/kasir', KasirController::class)->middleware('peran:owner,admin,kasir')->name('kasir');

        Route::prefix('penjualan')->name('penjualan.')->middleware('peran:owner,admin,kasir')->group(function () {
            Route::get('/', fn () => view('penjualan.index'))->name('index');
            Route::get('/{id}', fn ($id) => view('penjualan.show', ['id' => $id]))->name('show');
        });

        Route::prefix('pembelian')->name('pembelian.')->middleware('peran:owner,admin,gudang')->group(function () {
            Route::get('/', fn () => view('pembelian.index'))->name('index');
            Route::get('/tambah', fn () => view('pembelian.form'))->name('create');
        });

        Route::prefix('retur')->name('retur.')->group(function () {
            Route::get('/', fn () => view('retur.index'))->middleware('peran:owner,admin,kasir,gudang')->name('index');
            Route::get('/penjualan/tambah', fn () => view('retur.form-penjualan'))->middleware('peran:owner,admin,kasir')->name('penjualan.create');
            Route::get('/pembelian/tambah', fn () => view('retur.form-pembelian'))->middleware('peran:owner,admin,gudang')->name('pembelian.create');
        });

        Route::prefix('service')->name('service.')->middleware('peran:owner,admin,kasir,montir')->group(function () {
            Route::get('/', fn () => view('service.index'))->name('index');
            Route::get('/tambah', fn () => view('service.form'))->name('create');
            Route::get('/{id}', fn ($id) => view('service.show', ['id' => $id]))->name('show');
        });

        Route::prefix('barang')->name('barang.')->middleware('peran:owner,admin,gudang')->group(function () {
            Route::get('/', [BarangController::class, 'index'])->name('index');
            Route::post('/', [BarangController::class, 'store'])->name('store');
            Route::put('/{barang}', [BarangController::class, 'update'])->name('update');
            Route::patch('/{barang}/toggle-aktif', [BarangController::class, 'toggleAktif'])->name('toggle-aktif');
            Route::post('/{barang}/barcode', [BarangController::class, 'tambahBarcode'])->name('barcode.store');
            Route::patch('/{barang}/barcode/{barcodeId}/utama', [BarangController::class, 'jadikanBarcodeUtama'])->name('barcode.utama');
        });

        Route::prefix('customer')->name('customer.')->middleware('peran:owner,admin,kasir')->group(function () {
            Route::get('/', [CustomerController::class, 'index'])->name('index');
            Route::post('/', [CustomerController::class, 'store'])->name('store');
            Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
            Route::patch('/{customer}/toggle-aktif', [CustomerController::class, 'toggleAktif'])->name('toggle-aktif');
        });

        Route::prefix('supplier')->name('supplier.')->middleware('peran:owner,admin,gudang')->group(function () {
            Route::get('/', [SupplierController::class, 'index'])->name('index');
            Route::post('/', [SupplierController::class, 'store'])->name('store');
            Route::put('/{supplier}', [SupplierController::class, 'update'])->name('update');
            Route::patch('/{supplier}/toggle-aktif', [SupplierController::class, 'toggleAktif'])->name('toggle-aktif');
        });

        Route::prefix('sales')->name('sales.')->middleware('peran:owner,admin')->group(function () {
            Route::get('/', [SalesController::class, 'index'])->name('index');
            Route::post('/', [SalesController::class, 'store'])->name('store');
            Route::put('/{sales}', [SalesController::class, 'update'])->name('update');
            Route::patch('/{sales}/toggle-aktif', [SalesController::class, 'toggleAktif'])->name('toggle-aktif');
        });

        Route::prefix('stok')->name('stok.')->middleware('peran:owner,admin,gudang')->group(function () {
            Route::get('/kartu', fn () => view('stok.kartu'))->name('kartu');
            Route::get('/rekap', fn () => view('stok.rekap'))->name('rekap');
            Route::get('/opname', fn () => view('stok.opname'))->name('opname');
            Route::get('/menipis', fn () => view('stok.menipis'))->name('menipis');
        });

        Route::prefix('keuangan')->name('keuangan.')->middleware('peran:owner,admin')->group(function () {
            Route::get('/piutang', fn () => view('keuangan.piutang'))->name('piutang');
            Route::get('/hutang', fn () => view('keuangan.hutang'))->name('hutang');
            Route::get('/kas', fn () => view('keuangan.kas'))->name('kas');
            Route::get('/bank', fn () => view('keuangan.bank'))->name('bank');
        });

        Route::prefix('laporan')->name('laporan.')->middleware('peran:owner,admin')->group(function () {
            Route::get('/', fn () => view('laporan.index'))->name('index');
            Route::get('/{jenis}', fn ($jenis) => view('laporan.tampil', ['jenis' => $jenis]))->name('tampil');
        });

        Route::prefix('pengaturan')->name('pengaturan.')->middleware('peran:owner')->group(function () {
            Route::get('/toko', [PengaturanTokoController::class, 'edit'])->name('toko');
            Route::post('/toko', [PengaturanTokoController::class, 'update'])->name('toko.update');
            Route::get('/user', fn () => view('pengaturan.user'))->name('user');
            Route::get('/audit', fn () => view('pengaturan.audit'))->name('audit');
        });

        Route::prefix('cetak')->name('cetak.')->middleware("peran:{$semuaPeran}")->group(function () {
            Route::get('/nota/{id}', fn ($id) => view('print.nota', ['id' => $id]))->name('nota');
            Route::get('/faktur/{id}', fn ($id) => view('print.faktur', ['id' => $id]))->name('faktur');
            Route::get('/tanda-terima-service/{id}', fn ($id) => view('print.tanda-terima-service', ['id' => $id]))->name('tanda-terima-service');
        });
    });
});

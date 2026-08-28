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

Route::get('/sitemap.xml', function () {
    $baseUrl = url('/');
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    $pages = [
        ['path' => '', 'priority' => '1.0', 'changefreq' => 'daily'],
        ['path' => '/layanan/ganti-oli', 'priority' => '0.8', 'changefreq' => 'weekly'],
        ['path' => '/layanan/tune-up', 'priority' => '0.8', 'changefreq' => 'weekly'],
        ['path' => '/layanan/ban-spooring', 'priority' => '0.8', 'changefreq' => 'weekly'],
        ['path' => '/layanan/kelistrikan', 'priority' => '0.8', 'changefreq' => 'weekly'],
        ['path' => '/layanan/injeksi', 'priority' => '0.8', 'changefreq' => 'weekly'],
        ['path' => '/layanan/ac-mobil', 'priority' => '0.8', 'changefreq' => 'weekly'],
        ['path' => '/layanan/body-repair', 'priority' => '0.8', 'changefreq' => 'weekly'],
    ];

    foreach ($pages as $p) {
        $xml .= '  <url>' . "\n";
        $xml .= '    <loc>' . $baseUrl . $p['path'] . '</loc>' . "\n";
        $xml .= '    <lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
        $xml .= '    <changefreq>' . $p['changefreq'] . '</changefreq>' . "\n";
        $xml .= '    <priority>' . $p['priority'] . '</priority>' . "\n";
        $xml .= '  </url>' . "\n";
    }

    $xml .= '</urlset>';

    return response($xml, 200, ['Content-Type' => 'application/xml']);
});

Route::get('/robots.txt', function () {
    $sitemapUrl = url('/sitemap.xml');
    $content = "User-agent: *\nDisallow: /admin/\nAllow: /\nSitemap: {$sitemapUrl}\n";
    return response($content, 200, ['Content-Type' => 'text/plain']);
});

Route::get('/login', fn () => redirect()->route(auth()->check() ? 'dashboard' : 'login'));

Route::prefix('admin')->group(function () {
    Route::get('/', fn () => redirect()->route(auth()->check() ? 'dashboard' : 'login'));

    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'create'])->name('login');
        Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    });

    Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

    Route::middleware('auth')->group(function () {
        $semuaPeran = 'owner,admin,kasir';

        Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, '__invoke'])->middleware("peran:{$semuaPeran}")->name('dashboard');
        Route::get('/notifikasi', fn () => view('notifikasi.index'))->middleware("peran:{$semuaPeran}")->name('notifikasi.index');

        Route::get('/kasir', [KasirController::class, 'index'])->middleware('peran:owner,admin,kasir')->name('kasir');
        Route::post('/kasir', [KasirController::class, 'store'])->middleware('peran:owner,admin,kasir')->name('kasir.store');
        Route::get('/kasir/harga-terakhir', [KasirController::class, 'hargaTerakhir'])->middleware('peran:owner,admin,kasir')->name('kasir.harga-terakhir');
        Route::post('/kasir/customer-cepat', [KasirController::class, 'quickStoreCustomer'])->middleware('peran:owner,admin,kasir')->name('kasir.customer-cepat');
        Route::get('/kasir/antrean-service', [KasirController::class, 'antreanService'])->middleware('peran:owner,admin,kasir')->name('kasir.antrean-service');
        Route::get('/kasir/antrean-service/{service}', [KasirController::class, 'detailAntreanService'])->middleware('peran:owner,admin,kasir')->name('kasir.antrean-service.detail');

        Route::prefix('penjualan')->name('penjualan.')->middleware('peran:owner,admin,kasir')->group(function () {
            Route::get('/', function (\Illuminate\Http\Request $request) {
                $query = \App\Models\Penjualan::query()->with(['customer', 'user'])->latest();

                if ($request->filled('search')) {
                    $search = $request->search;
                    $query->where(function ($q) use ($search) {
                        $q->where('nomor_nota', 'LIKE', "%{$search}%")
                          ->orWhereHas('customer', fn ($c) => $c->where('nama', 'LIKE', "%{$search}%"));
                    });
                }

                if ($request->filled('status') && $request->status !== 'semua') {
                    $query->where('status_bayar', $request->status);
                }

                $perPage = max(5, min(100, (int) $request->input('per_page', 15)));
                $penjualans = $query->paginate($perPage)->withQueryString();

                return view('penjualan.index', compact('penjualans'));
            })->name('index');

            Route::get('/{id}', function ($id) {
                $realId = \App\Services\IdHasher::decode($id);
                $penjualan = \App\Models\Penjualan::with(['customer', 'user', 'details.barang'])
                    ->where('nomor_nota', $id)
                    ->orWhere('id', $realId)
                    ->firstOrFail();

                return view('penjualan.show', compact('penjualan'));
            })->name('show');
        });

        Route::prefix('pembelian')->name('pembelian.')->middleware('peran:owner,admin,kasir')->group(function () {
            Route::get('/', [\App\Http\Controllers\PembelianController::class, 'index'])->name('index');
            Route::get('/tambah', [\App\Http\Controllers\PembelianController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\PembelianController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\PembelianController::class, 'show'])->name('show');
            Route::post('/{pembelian}/pelunasan', [\App\Http\Controllers\PembelianController::class, 'pelunasan'])->name('pelunasan');
        });

        Route::prefix('retur')->name('retur.')->group(function () {
            Route::get('/', [\App\Http\Controllers\ReturController::class, 'index'])->middleware('peran:owner,admin,kasir')->name('index');
            Route::get('/penjualan/tambah', [\App\Http\Controllers\ReturController::class, 'createPenjualan'])->middleware('peran:owner,admin,kasir')->name('penjualan.create');
            Route::post('/penjualan', [\App\Http\Controllers\ReturController::class, 'storePenjualan'])->middleware('peran:owner,admin,kasir')->name('penjualan.store');
            Route::get('/pembelian/tambah', [\App\Http\Controllers\ReturController::class, 'createPembelian'])->middleware('peran:owner,admin')->name('pembelian.create');
            Route::post('/pembelian', [\App\Http\Controllers\ReturController::class, 'storePembelian'])->middleware('peran:owner,admin')->name('pembelian.store');
            Route::get('/{id}', [\App\Http\Controllers\ReturController::class, 'show'])->middleware('peran:owner,admin,kasir')->name('show');
        });

        Route::prefix('service')->name('service.')->middleware('peran:owner,admin,kasir')->group(function () {
            Route::get('/', [\App\Http\Controllers\ServiceController::class, 'index'])->name('index');
            Route::get('/tambah', [\App\Http\Controllers\ServiceController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\ServiceController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\ServiceController::class, 'show'])->name('show');
            Route::patch('/{service}/status', [\App\Http\Controllers\ServiceController::class, 'updateStatus'])->name('status');
        });

        Route::prefix('barang')->name('barang.')->middleware('peran:owner,admin')->group(function () {
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

        Route::prefix('supplier')->name('supplier.')->middleware('peran:owner,admin')->group(function () {
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

        Route::prefix('stok')->name('stok.')->middleware('peran:owner,admin')->group(function () {
            Route::get('/kartu', [\App\Http\Controllers\StokController::class, 'kartu'])->name('kartu');
            Route::get('/rekap', [\App\Http\Controllers\StokController::class, 'rekap'])->name('rekap');
            Route::get('/opname', [\App\Http\Controllers\StokController::class, 'opname'])->name('opname');
            Route::post('/opname', [\App\Http\Controllers\StokController::class, 'simpanOpname'])->name('opname.store');
            Route::get('/menipis', [\App\Http\Controllers\StokController::class, 'menipis'])->name('menipis');
        });

        Route::prefix('keuangan')->name('keuangan.')->middleware('peran:owner,admin')->group(function () {
            Route::get('/piutang', [\App\Http\Controllers\KeuanganController::class, 'piutang'])->name('piutang');
            Route::post('/piutang/{penjualan}/pelunasan', [\App\Http\Controllers\KeuanganController::class, 'bayarPiutang'])->name('piutang.bayar');
            Route::get('/hutang', [\App\Http\Controllers\KeuanganController::class, 'hutang'])->name('hutang');
            Route::post('/hutang/{service}/pelunasan', [\App\Http\Controllers\KeuanganController::class, 'bayarHutang'])->name('hutang.bayar');
            Route::get('/kas', [\App\Http\Controllers\KeuanganController::class, 'kas'])->name('kas');
            Route::get('/kas-besar', [\App\Http\Controllers\KeuanganController::class, 'kasBesar'])->middleware('peran:owner')->name('kas-besar');
            Route::get('/bank', [\App\Http\Controllers\KeuanganController::class, 'bank'])->name('bank');
            Route::post('/transaksi', [\App\Http\Controllers\KeuanganController::class, 'storeKasFlow'])->name('transaksi.store');
        });

        Route::prefix('laporan')->name('laporan.')->middleware('peran:owner,admin')->group(function () {
            Route::get('/', [\App\Http\Controllers\LaporanController::class, 'index'])->name('index');
            Route::get('/{jenis}', [\App\Http\Controllers\LaporanController::class, 'tampil'])->name('tampil');
            Route::get('/{jenis}/pdf', [\App\Http\Controllers\LaporanController::class, 'downloadPdf'])->name('pdf');
        });

        Route::prefix('utility')->name('utility.')->middleware('peran:owner,admin')->group(function () {
            Route::get('/', [\App\Http\Controllers\UtilityController::class, 'index'])->name('index');
            Route::get('/preview-recalculate-stok', [\App\Http\Controllers\UtilityController::class, 'previewRecalculateStok'])->name('preview-recalculate-stok');
            Route::post('/hitung-ulang-stok', [\App\Http\Controllers\UtilityController::class, 'recalculateStok'])->name('recalculate-stok');
            Route::get('/preview-maintenance-hpp', [\App\Http\Controllers\UtilityController::class, 'previewMaintenanceHpp'])->name('preview-maintenance-hpp');
            Route::post('/maintenance-hpp', [\App\Http\Controllers\UtilityController::class, 'maintenanceHpp'])->name('maintenance-hpp');
            Route::post('/backup', [\App\Http\Controllers\UtilityController::class, 'backupDatabase'])->name('backup');
            Route::get('/backup/{filename}/download', [\App\Http\Controllers\UtilityController::class, 'downloadBackup'])->name('backup.download');
            Route::delete('/backup/{filename}', [\App\Http\Controllers\UtilityController::class, 'deleteBackup'])->name('backup.delete');
            Route::post('/import-barang', [\App\Http\Controllers\UtilityController::class, 'importBarang'])->name('import-barang');
            Route::post('/import-customer', [\App\Http\Controllers\UtilityController::class, 'importCustomer'])->name('import-customer');
        });

        Route::prefix('pengaturan')->name('pengaturan.')->middleware('peran:owner')->group(function () {
            Route::get('/toko', [PengaturanTokoController::class, 'edit'])->name('toko');
            Route::post('/toko', [PengaturanTokoController::class, 'update'])->name('toko.update');
            Route::get('/user', [\App\Http\Controllers\UserController::class, 'index'])->name('user');
            Route::post('/user', [\App\Http\Controllers\UserController::class, 'store'])->name('user.store');
            Route::put('/user/{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('user.update');
            Route::patch('/user/{user}/toggle-aktif', [\App\Http\Controllers\UserController::class, 'toggleAktif'])->name('user.toggle-aktif');
            Route::get('/audit', [\App\Http\Controllers\AuditLogController::class, '__invoke'])->name('audit');
        });

        Route::prefix('cetak')->name('cetak.')->middleware("peran:{$semuaPeran}")->group(function () {
            Route::get('/nota/{id}', [\App\Http\Controllers\CetakController::class, 'nota'])->name('nota');
            Route::get('/nota/{id}/pdf', [\App\Http\Controllers\CetakController::class, 'notaPdf'])->name('nota.pdf');
            Route::get('/faktur/{id}', [\App\Http\Controllers\CetakController::class, 'faktur'])->name('faktur');
            Route::get('/faktur/{id}/pdf', [\App\Http\Controllers\CetakController::class, 'fakturPdf'])->name('faktur.pdf');
            Route::get('/tanda-terima-service/{id}', [\App\Http\Controllers\CetakController::class, 'tandaTerimaService'])->name('tanda-terima-service');
            Route::get('/tanda-terima-service/{id}/pdf', [\App\Http\Controllers\CetakController::class, 'tandaTerimaServicePdf'])->name('tanda-terima-service.pdf');
            Route::get('/surat-jalan/{id}', [\App\Http\Controllers\CetakController::class, 'suratJalan'])->name('surat-jalan');
            Route::get('/surat-jalan/{id}/pdf', [\App\Http\Controllers\CetakController::class, 'suratJalanPdf'])->name('surat-jalan.pdf');
        });
    });
});

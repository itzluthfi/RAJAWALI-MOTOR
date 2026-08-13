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
        $semuaPeran = 'owner,admin,kasir,gudang,montir';

        Route::get('/dashboard', fn () => view('dashboard.index'))->middleware("peran:{$semuaPeran}")->name('dashboard');
        Route::get('/notifikasi', fn () => view('notifikasi.index'))->middleware("peran:{$semuaPeran}")->name('notifikasi.index');

        Route::get('/kasir', [KasirController::class, 'index'])->middleware('peran:owner,admin,kasir')->name('kasir');
        Route::post('/kasir', [KasirController::class, 'store'])->middleware('peran:owner,admin,kasir')->name('kasir.store');
        Route::get('/kasir/harga-terakhir', [KasirController::class, 'hargaTerakhir'])->middleware('peran:owner,admin,kasir')->name('kasir.harga-terakhir');

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

                $penjualans = $query->paginate(15)->withQueryString();

                return view('penjualan.index', compact('penjualans'));
            })->name('index');

            Route::get('/{id}', function ($id) {
                $penjualan = \App\Models\Penjualan::with(['customer', 'user', 'details.barang'])->find($id) 
                    ?? \App\Models\Penjualan::with(['customer', 'user', 'details.barang'])->where('nomor_nota', $id)->firstOrFail();

                return view('penjualan.show', compact('penjualan'));
            })->name('show');
        });

        Route::prefix('pembelian')->name('pembelian.')->middleware('peran:owner,admin,gudang')->group(function () {
            Route::get('/', [\App\Http\Controllers\PembelianController::class, 'index'])->name('index');
            Route::get('/tambah', [\App\Http\Controllers\PembelianController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\PembelianController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\PembelianController::class, 'show'])->name('show');
        });

        Route::prefix('retur')->name('retur.')->group(function () {
            Route::get('/', [\App\Http\Controllers\ReturController::class, 'index'])->middleware('peran:owner,admin,kasir,gudang')->name('index');
            Route::get('/penjualan/tambah', [\App\Http\Controllers\ReturController::class, 'createPenjualan'])->middleware('peran:owner,admin,kasir')->name('penjualan.create');
            Route::post('/penjualan', [\App\Http\Controllers\ReturController::class, 'storePenjualan'])->middleware('peran:owner,admin,kasir')->name('penjualan.store');
            Route::get('/pembelian/tambah', [\App\Http\Controllers\ReturController::class, 'createPembelian'])->middleware('peran:owner,admin,gudang')->name('pembelian.create');
            Route::post('/pembelian', [\App\Http\Controllers\ReturController::class, 'storePembelian'])->middleware('peran:owner,admin,gudang')->name('pembelian.store');
        });

        Route::prefix('service')->name('service.')->middleware('peran:owner,admin,kasir,montir')->group(function () {
            Route::get('/', [\App\Http\Controllers\ServiceController::class, 'index'])->name('index');
            Route::get('/tambah', [\App\Http\Controllers\ServiceController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\ServiceController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\ServiceController::class, 'show'])->name('show');
            Route::patch('/{service}/status', [\App\Http\Controllers\ServiceController::class, 'updateStatus'])->name('status');
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
            Route::get('/piutang', [\App\Http\Controllers\KeuanganController::class, 'piutang'])->name('piutang');
            Route::post('/piutang/{penjualan}/pelunasan', [\App\Http\Controllers\KeuanganController::class, 'bayarPiutang'])->name('piutang.bayar');
            Route::get('/hutang', [\App\Http\Controllers\KeuanganController::class, 'hutang'])->name('hutang');
            Route::post('/hutang/{service}/pelunasan', [\App\Http\Controllers\KeuanganController::class, 'bayarHutang'])->name('hutang.bayar');
            Route::get('/kas', [\App\Http\Controllers\KeuanganController::class, 'kas'])->name('kas');
            Route::get('/bank', [\App\Http\Controllers\KeuanganController::class, 'bank'])->name('bank');
            Route::post('/transaksi', [\App\Http\Controllers\KeuanganController::class, 'storeKasFlow'])->name('transaksi.store');
        });

        Route::prefix('laporan')->name('laporan.')->middleware('peran:owner,admin')->group(function () {
            Route::get('/', fn () => view('laporan.index'))->name('index');
            Route::get('/{jenis}', fn ($jenis) => view('laporan.tampil', ['jenis' => $jenis]))->name('tampil');
        });

        Route::prefix('utility')->name('utility.')->middleware('peran:owner,admin')->group(function () {
            Route::get('/', [\App\Http\Controllers\UtilityController::class, 'index'])->name('index');
            Route::post('/hitung-ulang-stok', [\App\Http\Controllers\UtilityController::class, 'recalculateStok'])->name('recalculate-stok');
            Route::post('/maintenance-hpp', [\App\Http\Controllers\UtilityController::class, 'maintenanceHpp'])->name('maintenance-hpp');
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
            Route::get('/nota/{id}', fn ($id) => view('print.nota', ['id' => $id]))->name('nota');
            Route::get('/faktur/{id}', fn ($id) => view('print.faktur', ['id' => $id]))->name('faktur');
            Route::get('/tanda-terima-service/{id}', fn ($id) => view('print.tanda-terima-service', ['id' => $id]))->name('tanda-terima-service');
            Route::get('/surat-jalan/{id}', fn ($id) => view('print.surat-jalan', ['id' => $id]))->name('surat-jalan');
        });
    });
});

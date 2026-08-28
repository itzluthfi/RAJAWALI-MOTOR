<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Barang;
use App\Models\Customer;
use App\Models\Group;
use App\Models\PembelianDetail;
use App\Models\Satuan;
use App\Models\StokMutasi;
use App\Services\DatabaseBackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class UtilityController extends Controller
{
    public function index(): View
    {
        $backups = DatabaseBackupService::getBackupList();
        return view('utility.index', compact('backups'));
    }

    public function backupDatabase(): RedirectResponse
    {
        try {
            $filename = DatabaseBackupService::createBackup();

            AuditLog::catat(
                'Backup Database',
                'Utility',
                $filename,
                'Membuat berkas cadangan database baru: ' . $filename
            );

            return back()->with('sukses', "Berkas cadangan database '{$filename}' berhasil dibuat!");
        } catch (Throwable $e) {
            Log::error('Gagal membuat backup database', ['pesan' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Gagal membuat backup database: ' . $e->getMessage()]);
        }
    }

    public function downloadBackup(string $filename): BinaryFileResponse|RedirectResponse
    {
        $safeFilename = basename($filename);
        $filepath = storage_path('app/backups/' . $safeFilename);

        if (!File::exists($filepath)) {
            return back()->withErrors(['error' => 'Berkas backup database tidak ditemukan di server.']);
        }

        return response()->download($filepath, $safeFilename);
    }

    public function deleteBackup(string $filename): RedirectResponse
    {
        try {
            $safeFilename = basename($filename);
            $filepath = storage_path('app/backups/' . $safeFilename);

            if (File::exists($filepath)) {
                File::delete($filepath);

                AuditLog::catat(
                    'Hapus Backup Database',
                    'Utility',
                    $safeFilename,
                    'Menghapus berkas cadangan database: ' . $safeFilename
                );

                return back()->with('sukses', "Berkas cadangan '{$safeFilename}' berhasil dihapus dari server.");
            }

            return back()->withErrors(['error' => 'Berkas backup tidak ditemukan.']);
        } catch (Throwable $e) {
            Log::error('Gagal menghapus backup database', ['pesan' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Gagal menghapus berkas backup: ' . $e->getMessage()]);
        }
    }

    public function previewRecalculateStok(): \Illuminate\Http\JsonResponse
    {
        $orphanCount = StokMutasi::whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('barangs')
                  ->whereColumn('barangs.id', 'stok_mutasis.barang_id');
        })->count();

        $totalBarang = Barang::where('aktif', true)->count();
        $totalMutasi = StokMutasi::count();

        return response()->json([
            'sukses' => true,
            'orphan_count' => $orphanCount,
            'total_barang' => $totalBarang,
            'total_mutasi' => $totalMutasi,
        ]);
    }

    public function previewMaintenanceHpp(): \Illuminate\Http\JsonResponse
    {
        $barangs = Barang::where('aktif', true)->select('id', 'kode', 'nama', 'hpp')->get();
        $perubahan = [];

        foreach ($barangs as $b) {
            $latestHpp = 0.0;

            // 1. Cek dari PembelianDetail
            $pembelianTerakhir = PembelianDetail::where('barang_id', $b->id)
                ->where('harga_beli', '>', 0)
                ->latest('id')
                ->first();

            if ($pembelianTerakhir && $pembelianTerakhir->harga_beli > 0) {
                $latestHpp = (float) $pembelianTerakhir->harga_beli;
            } else {
                // 2. Cek dari StokMutasi
                $mutasiTerakhir = StokMutasi::where('barang_id', $b->id)
                    ->where('hpp', '>', 0)
                    ->latest('tanggal')
                    ->latest('id')
                    ->first();

                if ($mutasiTerakhir && $mutasiTerakhir->hpp > 0) {
                    $latestHpp = (float) $mutasiTerakhir->hpp;
                }
            }

            if ($latestHpp > 0 && abs((float)$b->hpp - $latestHpp) >= 1) {
                $perubahan[] = [
                    'kode' => $b->kode,
                    'nama' => $b->nama,
                    'hpp_lama' => (float) $b->hpp,
                    'hpp_baru' => $latestHpp,
                    'selisih' => $latestHpp - (float)$b->hpp,
                ];
            }
        }

        return response()->json([
            'sukses' => true,
            'total_perubahan' => count($perubahan),
            'items' => $perubahan,
        ]);
    }

    public function recalculateStok(): RedirectResponse
    {
        try {
            $deletedCount = 0;
            DB::transaction(function () use (&$deletedCount) {
                // Hapus mutasi stok yatim (tanpa barang)
                $deletedCount = StokMutasi::whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                          ->from('barangs')
                          ->whereColumn('barangs.id', 'stok_mutasis.barang_id');
                })->delete();
            });

            AuditLog::catat(
                'Hitung Ulang Stok',
                'Utility',
                'Recalculate',
                "Hitung ulang stok berhasil diproses. Membersihkan {$deletedCount} log mutasi tidak valid."
            );

            return back()->with('sukses', "Proses hitung ulang stok selesai. Seluruh saldo barang telah sinkron dengan kartu stok ({$deletedCount} log mutasi yatim dibersihkan).");
        } catch (Throwable $e) {
            Log::error('Gagal hitung ulang stok', ['pesan' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Gagal hitung ulang stok: ' . $e->getMessage()]);
        }
    }

    public function maintenanceHpp(): RedirectResponse
    {
        try {
            $diubahCount = 0;
            DB::transaction(function () use (&$diubahCount) {
                $barangs = Barang::all();
                foreach ($barangs as $b) {
                    $latestHpp = 0.0;

                    // 1. Cari dari PembelianDetail
                    $pembelianTerakhir = PembelianDetail::where('barang_id', $b->id)
                        ->where('harga_beli', '>', 0)
                        ->latest('id')
                        ->first();

                    if ($pembelianTerakhir && $pembelianTerakhir->harga_beli > 0) {
                        $latestHpp = (float) $pembelianTerakhir->harga_beli;
                    } else {
                        // 2. Jika tidak ada di PembelianDetail, cari dari StokMutasi
                        $mutasiTerakhir = StokMutasi::where('barang_id', $b->id)
                            ->where('hpp', '>', 0)
                            ->latest('tanggal')
                            ->latest('id')
                            ->first();

                        if ($mutasiTerakhir && $mutasiTerakhir->hpp > 0) {
                            $latestHpp = (float) $mutasiTerakhir->hpp;
                        }
                    }

                    if ($latestHpp > 0 && abs((float)$b->hpp - $latestHpp) >= 1) {
                        $b->update(['hpp' => $latestHpp]);
                        $diubahCount++;
                    }
                }
            });

            AuditLog::catat(
                'Maintenance HPP',
                'Utility',
                'HPP',
                "Menyelaraskan HPP untuk {$diubahCount} master barang berdasarkan pembelian terakhir."
            );

            return back()->with('sukses', "Proses maintenance HPP selesai. Sebanyak {$diubahCount} barang telah otomatis diselaraskan dengan harga pembelian supplier terakhir.");
        } catch (Throwable $e) {
            Log::error('Gagal maintenance HPP', ['pesan' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Gagal maintenance HPP: ' . $e->getMessage()]);
        }
    }

    public function importBarang(Request $request): RedirectResponse
    {
        $request->validate([
            'file_csv' => ['required', 'file', 'mimes:txt,csv'],
        ]);

        try {
            $path = $request->file('file_csv')->getRealPath();
            $file = fopen($path, 'r');
            
            // Skip header
            fgetcsv($file, 1000, ',');

            $groupDefault = Group::firstOrCreate(['nama' => 'Sparepart']);
            $satuanDefault = Satuan::firstOrCreate(['nama' => 'PCS']);

            $imported = 0;
            DB::transaction(function () use ($file, $groupDefault, $satuanDefault, &$imported) {
                while (($row = fgetcsv($file, 1000, ',')) !== false) {
                    if (count($row) < 5) continue;

                    $kode = trim($row[0]);
                    $nama = trim($row[1]);
                    $hpp = (float) $row[2];
                    $hargaEceran = (float) $row[3];
                    $hargaGrosir = (float) $row[4];
                    $stokMin = isset($row[5]) ? (float) $row[5] : 5.0;

                    Barang::updateOrCreate(
                        ['kode' => $kode],
                        [
                            'nama' => $nama,
                            'group_id' => $groupDefault->id,
                            'satuan_id' => $satuanDefault->id,
                            'hpp' => $hpp,
                            'harga_eceran' => $hargaEceran,
                            'harga_grosir' => $hargaGrosir,
                            'stok_minimum' => $stokMin,
                            'aktif' => true
                        ]
                    );
                    $imported++;
                }
            });

            fclose($file);
            return back()->with('sukses', "Berhasil mengimpor {$imported} data barang dari berkas CSV.");
        } catch (Throwable $e) {
            Log::error('Gagal mengimpor barang', ['pesan' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Gagal mengimpor data barang: ' . $e->getMessage()]);
        }
    }

    public function importCustomer(Request $request): RedirectResponse
    {
        $request->validate([
            'file_csv' => ['required', 'file', 'mimes:txt,csv'],
        ]);

        try {
            $path = $request->file('file_csv')->getRealPath();
            $file = fopen($path, 'r');
            
            // Skip header
            fgetcsv($file, 1000, ',');

            $imported = 0;
            DB::transaction(function () use ($file, &$imported) {
                while (($row = fgetcsv($file, 1000, ',')) !== false) {
                    if (count($row) < 1) continue;

                    $nama = trim($row[0]);
                    $telepon = isset($row[1]) ? trim($row[1]) : null;
                    $alamat = isset($row[2]) ? trim($row[2]) : null;
                    $termin = isset($row[3]) ? (int) $row[3] : 0;

                    Customer::updateOrCreate(
                        ['nama' => $nama],
                        [
                            'telepon' => $telepon,
                            'alamat' => $alamat,
                            'termin_hari' => $termin,
                            'aktif' => true
                        ]
                    );
                    $imported++;
                }
            });

            fclose($file);
            return back()->with('sukses', "Berhasil mengimpor {$imported} data customer dari berkas CSV.");
        } catch (Throwable $e) {
            Log::error('Gagal mengimpor customer', ['pesan' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Gagal mengimpor data customer: ' . $e->getMessage()]);
        }
    }
}

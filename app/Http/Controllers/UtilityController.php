<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Customer;
use App\Models\Group;
use App\Models\Satuan;
use App\Models\StokMutasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class UtilityController extends Controller
{
    public function index(): View
    {
        return view('utility.index');
    }

    public function recalculateStok(): RedirectResponse
    {
        try {
            DB::transaction(function () {
                // Hapus mutasi stok yatim (tanpa barang)
                StokMutasi::whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                          ->from('barangs')
                          ->whereColumn('barangs.id', 'stok_mutasis.barang_id');
                })->delete();
            });

            return back()->with('sukses', 'Proses hitung ulang stok selesai. Seluruh saldo barang telah sinkron dengan kartu stok.');
        } catch (Throwable $e) {
            Log::error('Gagal hitung ulang stok', ['pesan' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Gagal hitung ulang stok: ' . $e->getMessage()]);
        }
    }

    public function maintenanceHpp(): RedirectResponse
    {
        try {
            DB::transaction(function () {
                $barangs = Barang::all();
                foreach ($barangs as $b) {
                    // Cari harga pembelian terakhir dari kartu stok
                    $lastPurchase = StokMutasi::where('barang_id', $b->id)
                        ->where('jenis_mutasi', 'pembelian')
                        ->latest('tanggal')
                        ->latest('id')
                        ->first();

                    if ($lastPurchase && $lastPurchase->hpp > 0) {
                        $b->update(['hpp' => $lastPurchase->hpp]);
                    }
                }
            });

            return back()->with('sukses', 'Proses maintenance HPP selesai. HPP barang telah disesuaikan dengan harga beli terakhir.');
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

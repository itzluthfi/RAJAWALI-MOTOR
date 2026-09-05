<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\StokMutasi;
use App\Services\StokService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StokController extends Controller
{
    /**
     * Halaman Terpadu: Pusat Stok
     * Menggabungkan Rekap & Valuasi, Stok Menipis, Kartu Mutasi, dan Stok Opname dalam 1 layar terpadu.
     */
    public function index(Request $request, StokService $stokService): View
    {
        $tab = (string) $request->input('tab', 'rekap');
        if (!in_array($tab, ['rekap', 'menipis', 'kartu', 'opname'], true)) {
            $tab = 'rekap';
        }

        $dariTanggal = $request->input('dari_tanggal', now()->startOfMonth()->toDateString());
        $sampaiTanggal = $request->input('sampai_tanggal', now()->toDateString());

        // 1. Ambil daftar barang aktif (1 query)
        $daftarBarang = Barang::where('aktif', true)
            ->with(['group', 'satuan'])
            ->orderBy('nama')
            ->get();

        // 2. Ambil stok aktual seluruh barang (1 query cepat)
        $stokMap = $stokService->stokBanyakBarang($daftarBarang->pluck('id'));

        // 3. Stat Cards Pusat Stok
        $totalItemAktif = $daftarBarang->count();

        $stokMenipis = $daftarBarang->filter(function (Barang $b) use ($stokMap) {
            $stokAktual = $stokMap[$b->id] ?? 0.0;
            return $stokAktual <= (float) $b->stok_minimum;
        })->map(function (Barang $b) use ($stokMap) {
            $b->stok_saat_ini = $stokMap[$b->id] ?? 0.0;
            return $b;
        })->values();

        $jumlahMenipis = $stokMenipis->count();
        $jumlahHabis = $daftarBarang->filter(fn (Barang $b) => ($stokMap[$b->id] ?? 0.0) <= 0)->count();

        // Hitung valuasi seluruh gudang saat ini
        $totalValuasiStok = 0.0;
        foreach ($daftarBarang as $b) {
            $stokAktual = $stokMap[$b->id] ?? 0.0;
            if ($stokAktual > 0) {
                $totalValuasiStok += $stokAktual * (float) $b->hpp;
            }
        }

        // Variabel tab-specific
        $rekap = [];
        $mutasi = [];
        $saldoAwal = 0.0;
        $barangTerpilih = null;
        $barangId = $request->input('barang_id', $daftarBarang->first()?->id);
        $barangListJson = [];

        // Tab: Rekap & Mutasi Periode
        if ($tab === 'rekap') {
            $saldoAwalMap = StokMutasi::query()
                ->whereDate('tanggal', '<', $dariTanggal)
                ->selectRaw('barang_id, COALESCE(SUM(masuk) - SUM(keluar), 0) as saldo')
                ->groupBy('barang_id')
                ->pluck('saldo', 'barang_id');

            $mutasiPeriode = StokMutasi::query()
                ->whereDate('tanggal', '>=', $dariTanggal)
                ->whereDate('tanggal', '<=', $sampaiTanggal)
                ->selectRaw('barang_id, SUM(masuk) as total_masuk, SUM(keluar) as total_keluar')
                ->groupBy('barang_id')
                ->get()
                ->keyBy('barang_id');

            foreach ($daftarBarang as $b) {
                $stokAwal = (float) ($saldoAwalMap[$b->id] ?? 0);
                $periode = $mutasiPeriode->get($b->id);
                $totalMasuk = (float) ($periode->total_masuk ?? 0);
                $totalKeluar = (float) ($periode->total_keluar ?? 0);

                $stokAkhir = $stokAwal + $totalMasuk - $totalKeluar;
                $nilaiHpp = $stokAkhir * (float) $b->hpp;

                // Tampilkan barang yang memiliki stok atau ada mutasi pada periode
                if ($stokAwal == 0 && $totalMasuk == 0 && $totalKeluar == 0 && $stokAkhir == 0) {
                    continue;
                }

                $rekap[] = [
                    'id' => $b->id,
                    'kode' => $b->kode,
                    'nama' => $b->nama,
                    'awal' => $stokAwal,
                    'masuk' => $totalMasuk,
                    'keluar' => $totalKeluar,
                    'akhir' => $stokAkhir,
                    'nilai' => $nilaiHpp,
                ];
            }
        }

        // Tab: Kartu Stok
        if ($tab === 'kartu' && $barangId) {
            $barangTerpilih = Barang::find($barangId);

            if ($barangTerpilih) {
                $saldoAwal = (float) StokMutasi::query()
                    ->where('barang_id', $barangId)
                    ->whereDate('tanggal', '<', $dariTanggal)
                    ->selectRaw('COALESCE(SUM(masuk) - SUM(keluar), 0) as saldo')
                    ->value('saldo');

                $mutasiRaw = StokMutasi::query()
                    ->where('barang_id', $barangId)
                    ->whereDate('tanggal', '>=', $dariTanggal)
                    ->whereDate('tanggal', '<=', $sampaiTanggal)
                    ->orderBy('tanggal')
                    ->orderBy('id')
                    ->get();

                $runningSaldo = $saldoAwal;
                foreach ($mutasiRaw as $m) {
                    $runningSaldo = $runningSaldo + (float) $m->masuk - (float) $m->keluar;
                    $mutasi[] = [
                        'tanggal' => $m->tanggal->format('d M Y'),
                        'jenis' => ucfirst($m->jenis_mutasi),
                        'dok' => $m->no_dokumen,
                        'masuk' => (float) $m->masuk,
                        'keluar' => (float) $m->keluar,
                        'saldo' => $runningSaldo,
                        'hpp' => (float) $m->hpp,
                        'keterangan' => $m->keterangan ?? '-',
                    ];
                }
            }
        }

        // Tab: Opname Stok
        if ($tab === 'opname') {
            $barangListJson = $daftarBarang->map(fn ($b) => [
                'id' => $b->id,
                'kode' => $b->kode,
                'nama' => $b->nama,
                'stok' => $stokMap[$b->id] ?? 0.0,
            ])->values();
        }

        return view('stok.index', compact(
            'tab',
            'daftarBarang',
            'dariTanggal',
            'sampaiTanggal',
            'totalItemAktif',
            'jumlahMenipis',
            'jumlahHabis',
            'totalValuasiStok',
            'stokMenipis',
            'rekap',
            'barangId',
            'barangTerpilih',
            'saldoAwal',
            'mutasi',
            'barangListJson'
        ));
    }

    /**
     * Redirect route lama ke tab terkait di Pusat Stok
     */
    public function kartu(Request $request): RedirectResponse
    {
        return redirect()->route('stok.index', array_merge(['tab' => 'kartu'], $request->query()));
    }

    public function rekap(Request $request): RedirectResponse
    {
        return redirect()->route('stok.index', array_merge(['tab' => 'rekap'], $request->query()));
    }

    public function menipis(Request $request): RedirectResponse
    {
        return redirect()->route('stok.index', array_merge(['tab' => 'menipis'], $request->query()));
    }

    public function opname(Request $request): RedirectResponse
    {
        return redirect()->route('stok.index', array_merge(['tab' => 'opname'], $request->query()));
    }

    public function simpanOpname(Request $request): RedirectResponse
    {
        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'stok_fisik' => 'required|numeric|min:0',
            'alasan' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            $barang = Barang::findOrFail($request->barang_id);
            $stokSistem = (float) StokMutasi::where('barang_id', $barang->id)
                ->selectRaw('COALESCE(SUM(masuk) - SUM(keluar), 0) as saldo')
                ->value('saldo');

            $selisih = (float) $request->stok_fisik - $stokSistem;

            if ($selisih !== 0.0) {
                StokMutasi::create([
                    'barang_id' => $barang->id,
                    'tanggal' => now()->toDateString(),
                    'jenis_mutasi' => 'penyesuaian',
                    'no_dokumen' => 'OPN-' . date('YmdHis'),
                    'masuk' => $selisih > 0 ? $selisih : 0,
                    'keluar' => $selisih < 0 ? abs($selisih) : 0,
                    'hpp' => $barang->hpp,
                    'keterangan' => "Penyesuaian Stok Opname (Sistem: {$stokSistem}, Fisik: {$request->stok_fisik}): {$request->alasan}",
                ]);

                \App\Models\AuditLog::catat(
                    'Penyesuaian Stok Opname',
                    'Stok',
                    $barang->kode,
                    "Penyesuaian stok {$barang->nama} dari {$stokSistem} menjadi {$request->stok_fisik} ({$request->alasan})"
                );
            }
        });

        return redirect()->route('stok.index', ['tab' => 'opname'])
            ->with('sukses', 'Penyesuaian stok opname berhasil disimpan dan mutasi stok telah diperbarui!');
    }
}

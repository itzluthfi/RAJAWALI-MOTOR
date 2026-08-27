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
    public function kartu(Request $request): View
    {
        $daftarBarang = Barang::where('aktif', true)->select('id', 'kode', 'nama')->orderBy('nama')->get();
        
        $barangId = $request->input('barang_id', $daftarBarang->first()?->id);
        $dariTanggal = $request->input('dari_tanggal', now()->startOfMonth()->toDateString());
        $sampaiTanggal = $request->input('sampai_tanggal', now()->toDateString());

        $mutasi = [];
        $saldoAwal = 0.0;
        $barangTerpilih = null;

        if ($barangId) {
            $barangTerpilih = Barang::find($barangId);
            
            // 1 query untuk saldo awal
            $saldoAwal = (float) StokMutasi::query()
                ->where('barang_id', $barangId)
                ->where('tanggal', '<', $dariTanggal)
                ->selectRaw('COALESCE(SUM(masuk) - SUM(keluar), 0) as saldo')
                ->value('saldo');

            // 1 query untuk mutasi dalam periode
            $mutasiRaw = StokMutasi::query()
                ->where('barang_id', $barangId)
                ->whereBetween('tanggal', [$dariTanggal, $sampaiTanggal])
                ->orderBy('tanggal')
                ->orderBy('id')
                ->get();

            $runningSaldo = $saldoAwal;
            foreach ($mutasiRaw as $m) {
                $runningSaldo = $runningSaldo + (float)$m->masuk - (float)$m->keluar;
                $mutasi[] = [
                    'tanggal' => $m->tanggal->format('d M Y'),
                    'jenis' => ucfirst($m->jenis_mutasi),
                    'dok' => $m->no_dokumen,
                    'masuk' => (float)$m->masuk,
                    'keluar' => (float)$m->keluar,
                    'saldo' => $runningSaldo,
                    'hpp' => (float)$m->hpp,
                    'keterangan' => $m->keterangan ?? '-',
                ];
            }
        }

        return view('stok.kartu', compact('daftarBarang', 'barangId', 'dariTanggal', 'sampaiTanggal', 'mutasi', 'saldoAwal', 'barangTerpilih'));
    }

    public function rekap(Request $request): View
    {
        $dariTanggal = $request->input('dari_tanggal', now()->startOfMonth()->toDateString());
        $sampaiTanggal = $request->input('sampai_tanggal', now()->toDateString());

        // 1. Ambil daftar barang aktif (1 Query)
        $daftarBarang = Barang::where('aktif', true)->select('id', 'kode', 'nama', 'hpp')->orderBy('nama')->get();

        // 2. Ambil seluruh saldo awal sebelum periode secara GROUP BY (1 Query, bukan N query)
        $saldoAwalMap = StokMutasi::query()
            ->where('tanggal', '<', $dariTanggal)
            ->selectRaw('barang_id, COALESCE(SUM(masuk) - SUM(keluar), 0) as saldo')
            ->groupBy('barang_id')
            ->pluck('saldo', 'barang_id');

        // 3. Ambil seluruh mutasi masuk & keluar dalam periode secara GROUP BY (1 Query, bukan 2N query)
        $mutasiPeriode = StokMutasi::query()
            ->whereBetween('tanggal', [$dariTanggal, $sampaiTanggal])
            ->selectRaw('barang_id, SUM(masuk) as total_masuk, SUM(keluar) as total_keluar')
            ->groupBy('barang_id')
            ->get()
            ->keyBy('barang_id');

        $rekap = [];
        $totalValuasiStok = 0.0;

        foreach ($daftarBarang as $b) {
            $stokAwal = (float) ($saldoAwalMap[$b->id] ?? 0);
            $periode = $mutasiPeriode->get($b->id);
            $totalMasuk = (float) ($periode->total_masuk ?? 0);
            $totalKeluar = (float) ($periode->total_keluar ?? 0);

            $stokAkhir = $stokAwal + $totalMasuk - $totalKeluar;
            $nilaiHpp = $stokAkhir * (float) $b->hpp;

            // Skip jika tidak ada pergerakan dan stok kosong
            if ($stokAwal == 0 && $totalMasuk == 0 && $totalKeluar == 0 && $stokAkhir == 0) {
                continue;
            }

            $totalValuasiStok += $nilaiHpp;

            $rekap[] = [
                'kode' => $b->kode,
                'nama' => $b->nama,
                'awal' => $stokAwal,
                'masuk' => $totalMasuk,
                'keluar' => $totalKeluar,
                'akhir' => $stokAkhir,
                'nilai' => $nilaiHpp,
            ];
        }

        return view('stok.rekap', compact('rekap', 'dariTanggal', 'sampaiTanggal', 'totalValuasiStok'));
    }

    public function menipis(StokService $stokService): View
    {
        $barangs = Barang::where('aktif', true)
            ->with(['group', 'satuan'])
            ->orderBy('nama')
            ->get();

        $stokMap = $stokService->stokBanyakBarang($barangs->pluck('id'));

        $stokMenipis = $barangs->filter(function (Barang $b) use ($stokMap) {
            $stokAktual = $stokMap[$b->id] ?? 0.0;
            return $stokAktual <= (float) $b->stok_minimum;
        })->map(function (Barang $b) use ($stokMap) {
            $b->stok_saat_ini = $stokMap[$b->id] ?? 0.0;
            return $b;
        });

        return view('stok.menipis', compact('stokMenipis'));
    }

    public function opname(Request $request, StokService $stokService): View
    {
        $daftarBarang = Barang::where('aktif', true)->select('id', 'kode', 'nama', 'hpp')->orderBy('nama')->get();
        $stokMap = $stokService->stokBanyakBarang($daftarBarang->pluck('id'));

        $barangListJson = $daftarBarang->map(fn ($b) => [
            'id' => $b->id,
            'kode' => $b->kode,
            'nama' => $b->nama,
            'stok' => $stokMap[$b->id] ?? 0.0,
        ]);

        return view('stok.opname', compact('daftarBarang', 'barangListJson'));
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
                    'jenis_mutasi' => 'opname',
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

        return back()->with('sukses', 'Penyesuaian stok opname berhasil disimpan dan mutasi stok telah diperbarui!');
    }
}

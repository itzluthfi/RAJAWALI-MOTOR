<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\KasFlow;
use App\Models\Penjualan;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class KeuanganController extends Controller
{
    public function kas(Request $request): View
    {
        if ($request->user()->peran !== 'owner') {
            abort(403, 'Akses Buku Kas Utama terbatas khusus Owner.');
        }

        $query = KasFlow::query();

        if ($sumber = $request->input('sumber')) {
            if ($sumber !== 'semua') {
                $query->where('sumber', $sumber);
            }
        }
        if ($dari = $request->input('dari_tanggal')) {
            $query->where('tanggal', '>=', $dari);
        }
        if ($sampai = $request->input('sampai_tanggal')) {
            $query->where('tanggal', '<=', $sampai);
        }
        if ($tipe = $request->input('tipe')) {
            if ($tipe !== 'semua') {
                $query->where('tipe', $tipe);
            }
        }
        if ($kategori = $request->input('kategori')) {
            if ($kategori !== 'semua') {
                $query->where('kategori', $kategori);
            }
        }
        if ($cari = $request->input('cari')) {
            $query->where(function ($q) use ($cari) {
                $q->where('keterangan', 'LIKE', "%{$cari}%")
                  ->orWhere('no_referensi', 'LIKE', "%{$cari}%");
            });
        }

        // Hitung total masuk & keluar periode filter
        $totalMasukPeriode = (float) (clone $query)->where('tipe', 'masuk')->sum('nominal');
        $totalKeluarPeriode = (float) (clone $query)->where('tipe', 'keluar')->sum('nominal');
        $surplusDefisit = $totalMasukPeriode - $totalKeluarPeriode;

        // Data terbaru paling atas (descending)
        $perPage = max(5, min(100, (int) $request->input('per_page', 25)));
        $mutasi = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate($perPage)->withQueryString();

        // Saldo kas tunai (laci)
        $saldoKasTunai = (float) KasFlow::where('sumber', 'kas')
            ->selectRaw('COALESCE(SUM(CASE WHEN tipe = "masuk" THEN nominal ELSE -nominal END), 0) as saldo')
            ->value('saldo');

        // Saldo bank/rekening
        $saldoBank = (float) KasFlow::where('sumber', 'bank')
            ->selectRaw('COALESCE(SUM(CASE WHEN tipe = "masuk" THEN nominal ELSE -nominal END), 0) as saldo')
            ->value('saldo');

        $saldoTotalSemua = $saldoKasTunai + $saldoBank;

        return view('keuangan.kas', [
            'mutasi' => $mutasi,
            'saldoKas' => $saldoTotalSemua,
            'saldoKasTunai' => $saldoKasTunai,
            'saldoBank' => $saldoBank,
            'totalMasukPeriode' => $totalMasukPeriode,
            'totalKeluarPeriode' => $totalKeluarPeriode,
            'surplusDefisit' => $surplusDefisit,
            'filter' => [
                'sumber' => $request->input('sumber', 'semua'),
                'dari_tanggal' => $request->input('dari_tanggal', ''),
                'sampai_tanggal' => $request->input('sampai_tanggal', ''),
                'tipe' => $request->input('tipe', 'semua'),
                'kategori' => $request->input('kategori', 'semua'),
                'cari' => $request->input('cari', ''),
            ]
        ]);
    }

    public function bank(Request $request): View
    {
        $query = KasFlow::query()->where('sumber', 'bank');

        if ($dari = $request->input('dari_tanggal')) {
            $query->where('tanggal', '>=', $dari);
        }
        if ($sampai = $request->input('sampai_tanggal')) {
            $query->where('tanggal', '<=', $sampai);
        }
        if ($tipe = $request->input('tipe')) {
            if ($tipe !== 'semua') {
                $query->where('tipe', $tipe);
            }
        }
        if ($cari = $request->input('cari')) {
            $query->where(function ($q) use ($cari) {
                $q->where('keterangan', 'LIKE', "%{$cari}%")
                  ->orWhere('no_referensi', 'LIKE', "%{$cari}%");
            });
        }

        $perPage = max(5, min(100, (int) $request->input('per_page', 25)));
        $mutasi = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate($perPage)->withQueryString();

        // Calculate running saldo bank
        $saldoBank = (float) KasFlow::where('sumber', 'bank')
            ->selectRaw('COALESCE(SUM(CASE WHEN tipe = "masuk" THEN nominal ELSE -nominal END), 0) as saldo')
            ->value('saldo');

        return view('keuangan.bank', [
            'mutasi' => $mutasi,
            'saldoBank' => $saldoBank,
            'filter' => [
                'dari_tanggal' => $request->input('dari_tanggal', ''),
                'sampai_tanggal' => $request->input('sampai_tanggal', ''),
                'tipe' => $request->input('tipe', 'semua'),
                'cari' => $request->input('cari', ''),
            ]
        ]);
    }

    public function kasBesar(Request $request): RedirectResponse
    {
        return redirect()->route('keuangan.kas');
    }

    public function piutang(Request $request): View
    {
        // Query penjualan tempo yang belum lunas
        $query = Penjualan::with('customer')
            ->where('metode_pembayaran', 'tempo')
            ->where('status_bayar', 'piutang');

        if ($cari = $request->string('cari')->trim()->value()) {
            $query->where(function ($q) use ($cari) {
                $q->where('nomor_nota', 'LIKE', "%{$cari}%")
                  ->orWhereHas('customer', fn ($c) => $c->where('nama', 'LIKE', "%{$cari}%"));
            });
        }

        $piutangs = $query->latest()->get();

        // Total Outstanding Piutang
        $totalPiutang = $piutangs->sum(fn ($p) => max(0, $p->total_akhir - $p->uang_muka));

        return view('keuangan.piutang', [
            'piutangs' => $piutangs,
            'totalPiutang' => $totalPiutang,
            'filter' => [
                'cari' => $request->input('cari', ''),
            ]
        ]);
    }

    public function hutang(Request $request): View
    {
        $queryPembelian = \App\Models\Pembelian::with('supplier')
            ->where('status_bayar', 'tempo');

        $queryService = Service::with('supplier')
            ->where('repaired_by', 'extern')
            ->where('status_lunas', false);

        if ($cari = $request->string('cari')->trim()->value()) {
            $queryPembelian->where(function ($q) use ($cari) {
                $q->where('nomor_pembelian', 'LIKE', "%{$cari}%")
                  ->orWhere('nomor_faktur_supplier', 'LIKE', "%{$cari}%")
                  ->orWhereHas('supplier', fn ($s) => $s->where('nama', 'LIKE', "%{$cari}%"));
            });
            $queryService->where(function ($q) use ($cari) {
                $q->where('nomor_dokumen', 'LIKE', "%{$cari}%")
                  ->orWhereHas('supplier', fn ($s) => $s->where('nama', 'LIKE', "%{$cari}%"));
            });
        }

        $pembelianHutangs = $queryPembelian->latest()->get();
        $serviceHutangs = $queryService->latest()->get();

        $totalHutangPembelian = (float) $pembelianHutangs->sum('total');
        $totalHutangService = (float) $serviceHutangs->sum('grand_total_supplier');
        $totalHutang = $totalHutangPembelian + $totalHutangService;

        return view('keuangan.hutang', [
            'pembelianHutangs' => $pembelianHutangs,
            'serviceHutangs' => $serviceHutangs,
            'totalHutang' => $totalHutang,
            'totalHutangPembelian' => $totalHutangPembelian,
            'totalHutangService' => $totalHutangService,
            'filter' => [
                'cari' => $request->input('cari', ''),
            ]
        ]);
    }

    public function storeKasFlow(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'tanggal' => ['required', 'date'],
                'tipe' => ['required', 'in:masuk,keluar'],
                'sumber' => ['required', 'in:kas,bank'],
                'kategori' => ['required', 'string', 'max:50'],
                'nominal' => ['required', 'numeric', 'min:0.01'],
                'keterangan' => ['nullable', 'string', 'max:255'],
            ]);

            DB::transaction(function () use ($validated) {
                KasFlow::create($validated);
            }, attempts: 3);

            return back()->with('sukses', 'Transaksi keuangan berhasil disimpan.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Gagal menyimpan transaksi keuangan', ['pesan' => $e->getMessage()]);
            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan transaksi: ' . $e->getMessage()]);
        }
    }

    public function bayarPiutang(Request $request, Penjualan $penjualan): RedirectResponse
    {
        try {
            $sisaPiutang = max(0, $penjualan->total_akhir - $penjualan->uang_muka);
            $nominalBayar = (float) $request->input('nominal_bayar', $sisaPiutang);

            if ($nominalBayar <= 0) {
                return back()->withErrors(['error' => 'Nominal bayar harus lebih dari 0.']);
            }

            DB::transaction(function () use ($penjualan, $nominalBayar, $sisaPiutang) {
                $bayarNyicil = min($nominalBayar, $sisaPiutang);
                $uangMukaBaru = $penjualan->uang_muka + $bayarNyicil;
                $isLunas = $uangMukaBaru >= $penjualan->total_akhir;

                $penjualan->update([
                    'uang_muka' => $uangMukaBaru,
                    'status_bayar' => $isLunas ? 'lunas' : 'piutang',
                    'bayar' => $isLunas ? $penjualan->total_akhir : $uangMukaBaru,
                    'kembali' => 0,
                ]);

                // Catat KasFlow
                KasFlow::create([
                    'tanggal' => now()->toDateString(),
                    'tipe' => 'masuk',
                    'sumber' => 'kas',
                    'kategori' => 'piutang',
                    'no_referensi' => $penjualan->nomor_nota,
                    'nominal' => $bayarNyicil,
                    'keterangan' => $isLunas 
                        ? "Pelunasan Piutang Nota {$penjualan->nomor_nota}"
                        : "Cicilan Piutang Nota {$penjualan->nomor_nota} (Rp " . number_format($bayarNyicil, 0, ',', '.') . ")",
                ]);
            });

            return back()->with('sukses', 'Pembayaran/cicilan piutang berhasil dicatat.');
        } catch (Throwable $e) {
            Log::error('Gagal melunasi/mencicil piutang', ['pesan' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Gagal memproses piutang: ' . $e->getMessage()]);
        }
    }

    public function bayarHutang(Service $service): RedirectResponse
    {
        try {
            DB::transaction(function () use ($service) {
                $service->update([
                    'status_lunas' => true,
                    'status' => 'lunas',
                ]);

                // Catat KasFlow keluar
                KasFlow::create([
                    'tanggal' => now()->toDateString(),
                    'tipe' => 'keluar',
                    'sumber' => 'kas',
                    'kategori' => 'hutang',
                    'no_referensi' => $service->nomor_dokumen,
                    'nominal' => $service->grand_total_supplier,
                    'keterangan' => "Pelunasan Hutang Service Outsourcing {$service->nomor_dokumen}",
                ]);
            });

            return back()->with('sukses', 'Hutang berhasil dilunasi.');
        } catch (Throwable $e) {
            Log::error('Gagal melunasi hutang', ['pesan' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Gagal melunasi hutang: ' . $e->getMessage()]);
        }
    }
}

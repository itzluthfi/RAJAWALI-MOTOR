<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Customer;
use App\Models\KasFlow;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Service;
use App\Models\StokMutasi;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(): View
    {
        return view('laporan.index');
    }

    public function tampil(Request $request, string $jenis): View|Response|\Illuminate\Http\RedirectResponse
    {
        $dariTanggal = $request->input('dari_tanggal', now()->startOfMonth()->toDateString());
        $sampaiTanggal = $request->input('sampai_tanggal', now()->toDateString());

        // Redirects to dedicated stock views if requested
        if ($jenis === 'kartu-stok') {
            return redirect()->route('stok.kartu', $request->query());
        }
        if ($jenis === 'rekap-stok') {
            return redirect()->route('stok.rekap', $request->query());
        }
        if ($jenis === 'stok-menipis') {
            return redirect()->route('stok.menipis', $request->query());
        }

        $laporanData = $this->ambilDataLaporan($jenis, $dariTanggal, $sampaiTanggal);

        return view('laporan.tampil', array_merge($laporanData, [
            'jenis' => $jenis,
            'dariTanggal' => $dariTanggal,
            'sampaiTanggal' => $sampaiTanggal,
        ]));
    }

    public function downloadPdf(Request $request, string $jenis): Response
    {
        $dariTanggal = $request->input('dari_tanggal', now()->startOfMonth()->toDateString());
        $sampaiTanggal = $request->input('sampai_tanggal', now()->toDateString());

        $laporanData = $this->ambilDataLaporan($jenis, $dariTanggal, $sampaiTanggal);
        $judul = ucwords(str_replace('-', ' ', $jenis));

        $pdf = Pdf::loadView('print.pdf.laporan', array_merge($laporanData, [
            'jenis' => $jenis,
            'judul' => $judul,
            'dariTanggal' => $dariTanggal,
            'sampaiTanggal' => $sampaiTanggal,
        ]))->setPaper('a4', 'landscape');

        return $pdf->download("Laporan_{$jenis}_{$dariTanggal}_sd_{$sampaiTanggal}.pdf");
    }

    private function ambilDataLaporan(string $jenis, string $dariTanggal, string $sampaiTanggal): array
    {
        $judul = ucwords(str_replace('-', ' ', $jenis));
        $rows = [];
        $ringkasan = [];

        switch ($jenis) {
            case 'penjualan-harian':
                $query = Penjualan::query()
                    ->whereDate('created_at', '>=', $dariTanggal)
                    ->whereDate('created_at', '<=', $sampaiTanggal)
                    ->selectRaw('DATE(created_at) as tgl, COUNT(id) as total_nota, SUM(subtotal) as subtotal, SUM(diskon) as total_diskon, SUM(total_akhir) as total_omzet, SUM(CASE WHEN status_bayar = "lunas" THEN total_akhir ELSE 0 END) as total_lunas, SUM(CASE WHEN status_bayar = "piutang" THEN (total_akhir - uang_muka) ELSE 0 END) as total_piutang')
                    ->groupBy(DB::raw('DATE(created_at)'))
                    ->orderBy('tgl', 'desc')
                    ->get();

                $totalOmzet = (float) $query->sum('total_omzet');
                $totalNota = (int) $query->sum('total_nota');
                $totalDiskon = (float) $query->sum('total_diskon');
                $totalPiutang = (float) $query->sum('total_piutang');

                $ringkasan = [
                    ['label' => 'Total Omzet Penjualan', 'nilai' => 'Rp ' . number_format($totalOmzet, 0, ',', '.'), 'warna' => 'text-emerald-700'],
                    ['label' => 'Total Nota Transaksi', 'nilai' => number_format($totalNota, 0, ',', '.') . ' Nota', 'warna' => 'text-ink'],
                    ['label' => 'Total Diskon Diberikan', 'nilai' => 'Rp ' . number_format($totalDiskon, 0, ',', '.'), 'warna' => 'text-rajawali'],
                    ['label' => 'Piutang Terbentuk', 'nilai' => 'Rp ' . number_format($totalPiutang, 0, ',', '.'), 'warna' => 'text-amber-700'],
                ];

                $rows = $query->map(fn ($r) => [
                    'kolom1' => date('d M Y', strtotime($r->tgl)),
                    'kolom2' => $r->total_nota . ' Nota',
                    'kolom3' => 'Rp ' . number_format((float)$r->total_diskon, 0, ',', '.'),
                    'kolom4' => 'Rp ' . number_format((float)$r->total_lunas, 0, ',', '.'),
                    'kolom5' => 'Rp ' . number_format((float)$r->total_piutang, 0, ',', '.'),
                    'kolom6' => 'Rp ' . number_format((float)$r->total_omzet, 0, ',', '.'),
                ]);
                break;

            case 'penjualan-per-barang':
                $query = PenjualanDetail::query()
                    ->whereHas('penjualan', function ($q) use ($dariTanggal, $sampaiTanggal) {
                        $q->whereDate('created_at', '>=', $dariTanggal)
                          ->whereDate('created_at', '<=', $sampaiTanggal);
                    })
                    ->with('barang')
                    ->selectRaw('barang_id, SUM(qty) as total_qty, SUM(subtotal) as total_omzet, SUM(hpp * qty) as total_hpp, SUM(subtotal - (hpp * qty)) as total_laba')
                    ->groupBy('barang_id')
                    ->orderBy('total_omzet', 'desc')
                    ->get();

                $totalQty = (float) $query->sum('total_qty');
                $totalOmzet = (float) $query->sum('total_omzet');
                $totalLaba = (float) $query->sum('total_laba');

                $ringkasan = [
                    ['label' => 'Total Barang Terjual', 'nilai' => number_format($totalQty, 0, ',', '.') . ' Pcs', 'warna' => 'text-ink'],
                    ['label' => 'Total Omzet Penjualan', 'nilai' => 'Rp ' . number_format($totalOmzet, 0, ',', '.'), 'warna' => 'text-emerald-700'],
                    ['label' => 'Total Laba Kotor', 'nilai' => 'Rp ' . number_format($totalLaba, 0, ',', '.'), 'warna' => 'text-blue-700'],
                    ['label' => 'Rata-rata Margin', 'nilai' => ($totalOmzet > 0 ? round(($totalLaba / $totalOmzet) * 100, 1) : 0) . '%', 'warna' => 'text-purple-700'],
                ];

                $rows = $query->map(fn ($r) => [
                    'kolom1' => ($r->barang->kode ?? '-') . ' - ' . ($r->barang->nama ?? 'Barang Terhapus'),
                    'kolom2' => number_format((float)$r->total_qty, 0, ',', '.') . ' Pcs',
                    'kolom3' => 'Rp ' . number_format((float)$r->total_omzet, 0, ',', '.'),
                    'kolom4' => 'Rp ' . number_format((float)$r->total_hpp, 0, ',', '.'),
                    'kolom5' => 'Rp ' . number_format((float)$r->total_laba, 0, ',', '.'),
                    'kolom6' => ($r->total_omzet > 0 ? round(((float)$r->total_laba / (float)$r->total_omzet) * 100, 1) : 0) . '%',
                ]);
                break;

            case 'penjualan-per-customer':
                $query = Penjualan::query()
                    ->whereDate('created_at', '>=', $dariTanggal)
                    ->whereDate('created_at', '<=', $sampaiTanggal)
                    ->with('customer')
                    ->selectRaw('customer_id, COUNT(id) as total_nota, SUM(total_akhir) as total_belanja, SUM(CASE WHEN status_bayar = "piutang" THEN (total_akhir - uang_muka) ELSE 0 END) as sisa_piutang')
                    ->groupBy('customer_id')
                    ->orderBy('total_belanja', 'desc')
                    ->get();

                $totalBelanja = (float) $query->sum('total_belanja');
                $totalPiutang = (float) $query->sum('sisa_piutang');

                $ringkasan = [
                    ['label' => 'Total Pelanggan Bertransaksi', 'nilai' => $query->count() . ' Customer', 'warna' => 'text-ink'],
                    ['label' => 'Total Nilai Pembelian', 'nilai' => 'Rp ' . number_format($totalBelanja, 0, ',', '.'), 'warna' => 'text-emerald-700'],
                    ['label' => 'Total Piutang Belum Lunas', 'nilai' => 'Rp ' . number_format($totalPiutang, 0, ',', '.'), 'warna' => 'text-rajawali'],
                ];

                $rows = $query->map(fn ($r) => [
                    'kolom1' => $r->customer->nama ?? 'Pelanggan Umum (Tanpa Member)',
                    'kolom2' => $r->customer->telepon ?? '-',
                    'kolom3' => $r->customer->plat_nomor ? ($r->customer->plat_nomor . ' (' . ($r->customer->jenis_kendaraan ?? '-') . ')') : '-',
                    'kolom4' => $r->total_nota . ' Nota',
                    'kolom5' => 'Rp ' . number_format((float)$r->sisa_piutang, 0, ',', '.'),
                    'kolom6' => 'Rp ' . number_format((float)$r->total_belanja, 0, ',', '.'),
                ]);
                break;

            case 'pembelian-harian':
                $query = Pembelian::query()
                    ->whereDate('tanggal', '>=', $dariTanggal)
                    ->whereDate('tanggal', '<=', $sampaiTanggal)
                    ->selectRaw('tanggal, COUNT(id) as total_faktur, SUM(total) as total_pembelian, SUM(CASE WHEN status_bayar = "lunas" THEN total ELSE 0 END) as total_lunas, SUM(CASE WHEN status_bayar = "tempo" THEN total ELSE 0 END) as total_tempo')
                    ->groupBy('tanggal')
                    ->orderBy('tanggal', 'desc')
                    ->get();

                $totalBeli = (float) $query->sum('total_pembelian');
                $totalFaktur = (int) $query->sum('total_faktur');
                $totalTempo = (float) $query->sum('total_tempo');

                $ringkasan = [
                    ['label' => 'Total Pengadaan Stok', 'nilai' => 'Rp ' . number_format($totalBeli, 0, ',', '.'), 'warna' => 'text-blue-700'],
                    ['label' => 'Total Faktur Pembelian', 'nilai' => $totalFaktur . ' Faktur', 'warna' => 'text-ink'],
                    ['label' => 'Hutang Tempo Supplier', 'nilai' => 'Rp ' . number_format($totalTempo, 0, ',', '.'), 'warna' => 'text-rajawali'],
                ];

                $rows = $query->map(fn ($r) => [
                    'kolom1' => $r->tanggal->format('d M Y'),
                    'kolom2' => $r->total_faktur . ' Faktur',
                    'kolom3' => 'Rp ' . number_format((float)$r->total_lunas, 0, ',', '.'),
                    'kolom4' => 'Rp ' . number_format((float)$r->total_tempo, 0, ',', '.'),
                    'kolom5' => '-',
                    'kolom6' => 'Rp ' . number_format((float)$r->total_pembelian, 0, ',', '.'),
                ]);
                break;

            case 'pembelian-per-supplier':
                $query = Pembelian::query()
                    ->whereDate('tanggal', '>=', $dariTanggal)
                    ->whereDate('tanggal', '<=', $sampaiTanggal)
                    ->with('supplier')
                    ->selectRaw('supplier_id, COUNT(id) as total_faktur, SUM(total) as total_pembelian, SUM(CASE WHEN status_bayar = "tempo" THEN total ELSE 0 END) as total_tempo')
                    ->groupBy('supplier_id')
                    ->orderBy('total_pembelian', 'desc')
                    ->get();

                $totalBeli = (float) $query->sum('total_pembelian');
                $totalTempo = (float) $query->sum('total_tempo');

                $ringkasan = [
                    ['label' => 'Total Supplier Aktif', 'nilai' => $query->count() . ' Vendor', 'warna' => 'text-ink'],
                    ['label' => 'Total Pengadaan Barang', 'nilai' => 'Rp ' . number_format($totalBeli, 0, ',', '.'), 'warna' => 'text-blue-700'],
                    ['label' => 'Hutang Belum Lunas', 'nilai' => 'Rp ' . number_format($totalTempo, 0, ',', '.'), 'warna' => 'text-rajawali'],
                ];

                $rows = $query->map(fn ($r) => [
                    'kolom1' => $r->supplier->nama ?? 'Supplier Terhapus',
                    'kolom2' => $r->supplier->telepon ?? '-',
                    'kolom3' => $r->supplier->alamat ?? '-',
                    'kolom4' => $r->total_faktur . ' Faktur',
                    'kolom5' => 'Rp ' . number_format((float)$r->total_tempo, 0, ',', '.'),
                    'kolom6' => 'Rp ' . number_format((float)$r->total_pembelian, 0, ',', '.'),
                ]);
                break;

            case 'laba-rugi-kotor':
                $query = Penjualan::query()
                    ->whereDate('created_at', '>=', $dariTanggal)
                    ->whereDate('created_at', '<=', $sampaiTanggal)
                    ->selectRaw('DATE(created_at) as tgl, COUNT(id) as total_nota, SUM(total_akhir) as total_omzet')
                    ->groupBy(DB::raw('DATE(created_at)'))
                    ->orderBy('tgl', 'desc')
                    ->get();

                $details = PenjualanDetail::query()
                    ->whereHas('penjualan', function ($q) use ($dariTanggal, $sampaiTanggal) {
                        $q->whereDate('created_at', '>=', $dariTanggal)
                          ->whereDate('created_at', '<=', $sampaiTanggal);
                    })
                    ->join('penjualans', 'penjualan_details.penjualan_id', '=', 'penjualans.id')
                    ->selectRaw('DATE(penjualans.created_at) as tgl, SUM(penjualan_details.hpp * penjualan_details.qty) as total_hpp')
                    ->groupBy(DB::raw('DATE(penjualans.created_at)'))
                    ->pluck('total_hpp', 'tgl');

                $totalOmzet = (float) $query->sum('total_omzet');
                $totalHpp = (float) $details->sum();
                $totalLaba = $totalOmzet - $totalHpp;
                $margin = $totalOmzet > 0 ? round(($totalLaba / $totalOmzet) * 100, 1) : 0;

                $ringkasan = [
                    ['label' => 'Total Omzet Penjualan', 'nilai' => 'Rp ' . number_format($totalOmzet, 0, ',', '.'), 'warna' => 'text-emerald-700'],
                    ['label' => 'Total HPP Modal', 'nilai' => 'Rp ' . number_format($totalHpp, 0, ',', '.'), 'warna' => 'text-steel'],
                    ['label' => 'Total Laba Kotor Bersih', 'nilai' => 'Rp ' . number_format($totalLaba, 0, ',', '.'), 'warna' => 'text-emerald-800'],
                    ['label' => 'Margin Keuntungan Rata-rata', 'nilai' => $margin . '%', 'warna' => 'text-purple-700'],
                ];

                $rows = $query->map(function ($r) use ($details) {
                    $hpp = (float) ($details[$r->tgl] ?? 0);
                    $omzet = (float) $r->total_omzet;
                    $laba = $omzet - $hpp;
                    $persen = $omzet > 0 ? round(($laba / $omzet) * 100, 1) : 0;

                    return [
                        'kolom1' => date('d M Y', strtotime($r->tgl)),
                        'kolom2' => $r->total_nota . ' Transaksi',
                        'kolom3' => 'Rp ' . number_format($omzet, 0, ',', '.'),
                        'kolom4' => 'Rp ' . number_format($hpp, 0, ',', '.'),
                        'kolom5' => 'Rp ' . number_format($laba, 0, ',', '.'),
                        'kolom6' => $persen . '%',
                    ];
                });
                break;

            case 'piutang':
                $query = Penjualan::query()
                    ->where('status_bayar', 'piutang')
                    ->with('customer')
                    ->latest()
                    ->get();

                $totalPiutang = $query->sum(fn ($p) => max(0, $p->total_akhir - $p->uang_muka));

                $ringkasan = [
                    ['label' => 'Total Outstanding Piutang', 'nilai' => 'Rp ' . number_format($totalPiutang, 0, ',', '.'), 'warna' => 'text-rajawali'],
                    ['label' => 'Total Nota Belum Lunas', 'nilai' => $query->count() . ' Nota', 'warna' => 'text-ink'],
                ];

                $rows = $query->map(function ($p) {
                    $sisa = max(0, $p->total_akhir - $p->uang_muka);
                    $jatuhTempo = $p->created_at->addDays($p->customer->termin_hari ?? 30);
                    $isOverdue = now()->greaterThan($jatuhTempo);

                    return [
                        'kolom1' => $p->nomor_nota,
                        'kolom2' => $p->customer->nama ?? 'Umum',
                        'kolom3' => $p->created_at->format('d M Y'),
                        'kolom4' => $jatuhTempo->format('d M Y') . ($isOverdue ? ' (LEWAT)' : ''),
                        'kolom5' => 'Rp ' . number_format((float)$p->uang_muka, 0, ',', '.'),
                        'kolom6' => 'Rp ' . number_format($sisa, 0, ',', '.'),
                    ];
                });
                break;

            case 'hutang':
                $queryPembelian = Pembelian::where('status_bayar', 'tempo')->with('supplier')->latest()->get();
                $queryService = Service::where('repaired_by', 'extern')->where('status_lunas', false)->with('supplier')->latest()->get();

                $totalHutang = $queryPembelian->sum('total') + $queryService->sum('grand_total_supplier');

                $ringkasan = [
                    ['label' => 'Total Hutang Supplier', 'nilai' => 'Rp ' . number_format($totalHutang, 0, ',', '.'), 'warna' => 'text-rajawali'],
                    ['label' => 'Faktur Pembelian Tempo', 'nilai' => $queryPembelian->count() . ' Faktur', 'warna' => 'text-ink'],
                    ['label' => 'Hutang Bengkel Rekanan', 'nilai' => $queryService->count() . ' Servis', 'warna' => 'text-blue-700'],
                ];

                $rows = collect();
                foreach ($queryPembelian as $p) {
                    $rows->push([
                        'kolom1' => $p->nomor_pembelian . ' (Pembelian Stok)',
                        'kolom2' => $p->supplier->nama ?? '-',
                        'kolom3' => $p->tanggal->format('d M Y'),
                        'kolom4' => $p->jatuh_tempo ? $p->jatuh_tempo->format('d M Y') : '-',
                        'kolom5' => 'Faktur: ' . ($p->nomor_faktur_supplier ?? '-'),
                        'kolom6' => 'Rp ' . number_format((float)$p->total, 0, ',', '.'),
                    ]);
                }
                foreach ($queryService as $s) {
                    $rows->push([
                        'kolom1' => $s->nomor_dokumen . ' (Service Extern)',
                        'kolom2' => $s->supplier->nama ?? '-',
                        'kolom3' => $s->tanggal_masuk->format('d M Y'),
                        'kolom4' => $s->tanggal_kembali ? $s->tanggal_kembali->format('d M Y') : '-',
                        'kolom5' => 'Servis Rekanan',
                        'kolom6' => 'Rp ' . number_format((float)$s->grand_total_supplier, 0, ',', '.'),
                    ]);
                }
                break;

            case 'kas-bank':
            case 'arus-kas':
            case 'kas':
                $query = KasFlow::query()
                    ->whereDate('tanggal', '>=', $dariTanggal)
                    ->whereDate('tanggal', '<=', $sampaiTanggal)
                    ->orderBy('tanggal', 'desc')
                    ->orderBy('id', 'desc')
                    ->get();

                $totalMasuk = (float) $query->where('tipe', 'masuk')->sum('nominal');
                $totalKeluar = (float) $query->where('tipe', 'keluar')->sum('nominal');
                $saldoBersih = $totalMasuk - $totalKeluar;

                $ringkasan = [
                    ['label' => 'Total Kas Masuk', 'nilai' => 'Rp ' . number_format($totalMasuk, 0, ',', '.'), 'warna' => 'text-emerald-700'],
                    ['label' => 'Total Kas Keluar', 'nilai' => 'Rp ' . number_format($totalKeluar, 0, ',', '.'), 'warna' => 'text-rajawali'],
                    ['label' => 'Arus Kas Bersih (Net)', 'nilai' => 'Rp ' . number_format($saldoBersih, 0, ',', '.'), 'warna' => $saldoBersih >= 0 ? 'text-emerald-800' : 'text-rajawali'],
                ];

                $rows = $query->map(fn ($k) => [
                    'kolom1' => $k->tanggal->format('d M Y'),
                    'kolom2' => ucfirst($k->kategori),
                    'kolom3' => $k->no_referensi ?? '-',
                    'kolom4' => $k->keterangan ?? '-',
                    'kolom5' => ($k->tipe === 'masuk' ? 'Kas Masuk' : 'Kas Keluar'),
                    'kolom6' => ($k->tipe === 'masuk' ? '+Rp ' : '-Rp ') . number_format((float)$k->nominal, 0, ',', '.'),
                ]);
                break;

            case 'service-bengkel':
                $query = Service::query()
                    ->whereDate('tanggal_masuk', '>=', $dariTanggal)
                    ->whereDate('tanggal_masuk', '<=', $sampaiTanggal)
                    ->with(['customer', 'montir'])
                    ->latest('tanggal_masuk')
                    ->get();

                $totalServis = $query->count();
                $totalOmzet = (float) $query->sum('grand_total_nett');

                $ringkasan = [
                    ['label' => 'Total Unit Diservis', 'nilai' => $totalServis . ' Motor', 'warna' => 'text-ink'],
                    ['label' => 'Total Omzet Servis', 'nilai' => 'Rp ' . number_format($totalOmzet, 0, ',', '.'), 'warna' => 'text-emerald-700'],
                    ['label' => 'Servis Selesai / Lunas', 'nilai' => $query->where('status_lunas', true)->count() . ' Unit', 'warna' => 'text-blue-700'],
                ];

                $rows = $query->map(fn ($s) => [
                    'kolom1' => $s->nomor_dokumen,
                    'kolom2' => $s->tanggal_masuk->format('d M Y'),
                    'kolom3' => ($s->customer->nama ?? '-') . ' (' . ($s->merk_type ?? '-') . ')',
                    'kolom4' => $s->montir->name ?? '-',
                    'kolom5' => strtoupper($s->status),
                    'kolom6' => 'Rp ' . number_format((float)($s->grand_total_nett ?? 0), 0, ',', '.'),
                ]);
                break;

            default:
                $ringkasan = [];
                $rows = collect();
                break;
        }

        return [
            'judul' => $judul,
            'ringkasan' => $ringkasan,
            'rows' => $rows,
            'headers' => $this->getHeaderKolom($jenis),
        ];
    }

    private function getHeaderKolom(string $jenis): array
    {
        return match ($jenis) {
            'penjualan-harian' => ['Tanggal', 'Jumlah Nota', 'Total Diskon', 'Bayar Lunas', 'Tempo/Piutang', 'Total Omzet'],
            'penjualan-per-barang' => ['Kode & Nama Barang', 'Qty Terjual', 'Total Omzet', 'HPP Modal', 'Laba Kotor', 'Margin (%)'],
            'penjualan-per-customer' => ['Nama Pelanggan', 'No HP / Telepon', 'Plat & Tipe Motor', 'Total Nota', 'Sisa Piutang', 'Total Belanja'],
            'pembelian-harian' => ['Tanggal', 'Jumlah Faktur', 'Bayar Lunas', 'Hutang Tempo', 'Keterangan', 'Total Pembelian'],
            'pembelian-per-supplier' => ['Nama Supplier', 'No HP / Telepon', 'Alamat', 'Total Faktur', 'Hutang Tempo', 'Total Belanja'],
            'laba-rugi-kotor' => ['Tanggal Penjualan', 'Jumlah Nota', 'Pendapatan Omzet', 'Beban Pokok (HPP)', 'Laba Kotor', 'Margin Laba'],
            'piutang' => ['No Nota', 'Nama Customer', 'Tanggal Transaksi', 'Jatuh Tempo', 'Uang Muka (DP)', 'Sisa Piutang'],
            'hutang' => ['No Dokumen', 'Nama Supplier / Rekanan', 'Tanggal Transaksi', 'Jatuh Tempo', 'Ref / Kategori', 'Total Hutang'],
            'kas-bank', 'arus-kas', 'kas' => ['Tanggal', 'Kategori', 'No Referensi', 'Keterangan', 'Jenis Arus', 'Nominal Kas'],
            'service-bengkel' => ['No Servis', 'Tanggal Masuk', 'Customer & Motor', 'Montir / Teknisi', 'Status Servis', 'Grand Total'],
            default => ['Kolom 1', 'Kolom 2', 'Kolom 3', 'Kolom 4', 'Kolom 5', 'Total'],
        };
    }
}

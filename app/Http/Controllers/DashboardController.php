<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Barang;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $peran = $user->peran;
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        // Common Data
        $omzetHariIni = (float) Penjualan::whereDate('created_at', $today)->sum('total_akhir');
        $notaHariIniCount = Penjualan::whereDate('created_at', $today)->count();
        
        $omzetKemarin = (float) Penjualan::whereDate('created_at', $yesterday)->sum('total_akhir');
        $growth = $omzetKemarin > 0 ? round((($omzetHariIni - $omzetKemarin) / $omzetKemarin) * 100, 1) : 0;

        $stokMenipisList = Barang::where('aktif', true)
            ->whereColumn('stok', '<=', 'stok_minimum')
            ->orderBy('stok', 'asc')
            ->take(6)
            ->get();

        $serviceAktif = Service::whereIn('status', ['masuk', 'dikerjakan'])
            ->with(['customer', 'montirUser'])
            ->latest()
            ->take(5)
            ->get();

        // Data Khusus OWNER
        $labaKotorHariIni = 0;
        $totalPiutang = 0;
        $totalHutang = 0;
        $penjualanBulanan = [];
        $auditLogs = collect();

        if ($peran === 'owner') {
            $labaKotorHariIni = (float) PenjualanDetail::whereHas('penjualan', function ($q) use ($today) {
                $q->whereDate('created_at', $today);
            })->sum(DB::raw('subtotal - (hpp * qty)'));

            $totalPiutang = (float) Penjualan::where('status_bayar', 'piutang')
                ->sum(DB::raw('total_akhir - uang_muka'));

            $totalHutang = (float) Pembelian::where('status_bayar', 'tempo')
                ->sum(DB::raw('total - bayar'));

            // Chart data 30 hari terakhir
            for ($i = 29; $i >= 0; $i--) {
                $tgl = now()->subDays($i)->toDateString();
                $penjualanBulanan[] = [
                    'tgl' => now()->subDays($i)->format('d M'),
                    'total' => (float) Penjualan::whereDate('created_at', $tgl)->sum('total_akhir'),
                ];
            }

            $auditLogs = AuditLog::latest()->take(6)->get();
        }

        // Data Khusus ADMIN
        $pembelianHariIni = 0;
        $piutangJatuhTempoList = collect();
        $hutangJatuhTempoList = collect();

        if (in_array($peran, ['owner', 'admin'], true)) {
            $pembelianHariIni = (float) Pembelian::whereDate('tanggal', $today)->sum('total');

            $piutangJatuhTempoList = Penjualan::where('status_bayar', 'piutang')
                ->with('customer')
                ->latest()
                ->take(5)
                ->get();

            $hutangJatuhTempoList = Pembelian::where('status_bayar', 'tempo')
                ->with('supplier')
                ->latest()
                ->take(5)
                ->get();
        }

        // Data Khusus KASIR
        $notaSayaHariIniCount = 0;
        $omzetSayaHariIni = 0;
        $transaksiSayaTerakhir = collect();

        if ($peran === 'kasir') {
            $notaSayaHariIniCount = Penjualan::where('user_id', $user->id)
                ->whereDate('created_at', $today)
                ->count();

            $omzetSayaHariIni = (float) Penjualan::where('user_id', $user->id)
                ->whereDate('created_at', $today)
                ->sum('total_akhir');

            $transaksiSayaTerakhir = Penjualan::where('user_id', $user->id)
                ->with('customer')
                ->latest()
                ->take(5)
                ->get();
        }

        return view('dashboard.index', compact(
            'omzetHariIni',
            'omzetKemarin',
            'growth',
            'notaHariIniCount',
            'stokMenipisList',
            'serviceAktif',
            'labaKotorHariIni',
            'totalPiutang',
            'totalHutang',
            'penjualanBulanan',
            'auditLogs',
            'pembelianHariIni',
            'piutangJatuhTempoList',
            'hutangJatuhTempoList',
            'notaSayaHariIniCount',
            'omzetSayaHariIni',
            'transaksiSayaTerakhir'
        ));
    }
}

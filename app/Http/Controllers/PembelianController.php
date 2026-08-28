<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Barang;
use App\Models\KasFlow;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\PembelianPembayaran;
use App\Models\StokMutasi;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PembelianController extends Controller
{
    public function index(Request $request): View
    {
        $query = Pembelian::with(['supplier', 'user', 'pembayarans'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_pembelian', 'LIKE', "%{$search}%")
                  ->orWhere('nomor_faktur_supplier', 'LIKE', "%{$search}%")
                  ->orWhereHas('supplier', fn ($s) => $s->where('nama', 'LIKE', "%{$search}%"));
            });
        }

        if ($request->filled('status') && $request->status !== 'semua') {
            if ($request->status === 'overdue') {
                $query->where('status_bayar', 'tempo')
                      ->whereNotNull('jatuh_tempo')
                      ->whereDate('jatuh_tempo', '<', now());
            } else {
                $query->where('status_bayar', $request->status);
            }
        }

        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal', '>=', $request->dari_tanggal);
        }

        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal', '<=', $request->sampai_tanggal);
        }

        $pembelians = $query->paginate(15)->withQueryString();

        return view('pembelian.index', compact('pembelians'));
    }

    public function create(): View
    {
        $suppliers = Supplier::where('aktif', true)->get();
        $barangs = Barang::where('aktif', true)->select('id', 'kode', 'nama', 'hpp', 'stok')->get();

        return view('pembelian.form', compact('suppliers', 'barangs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'nomor_faktur_supplier' => 'nullable|string|max:100',
            'tanggal' => 'required|date',
            'status_bayar' => 'required|in:lunas,tempo',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_beli' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $total = 0;
            foreach ($request->items as $item) {
                $total += $item['jumlah'] * $item['harga_beli'];
            }

            $isLunas = $request->status_bayar === 'lunas';
            $tanggalFaktur = Carbon::parse($request->tanggal);

            $pembelian = Pembelian::create([
                'nomor_pembelian' => Pembelian::buatNomorPembelian(),
                'supplier_id' => $request->supplier_id,
                'user_id' => auth()->id(),
                'nomor_faktur_supplier' => $request->nomor_faktur_supplier,
                'tanggal' => $request->tanggal,
                'total' => $total,
                'terbayar' => $isLunas ? $total : 0,
                'status_bayar' => $request->status_bayar,
                'jatuh_tempo' => !$isLunas ? $tanggalFaktur->copy()->addDays(30) : null,
                'tanggal_lunas' => $isLunas ? now() : null,
                'keterangan' => $request->keterangan,
            ]);

            foreach ($request->items as $item) {
                $subtotal = $item['jumlah'] * $item['harga_beli'];

                PembelianDetail::create([
                    'pembelian_id' => $pembelian->id,
                    'barang_id' => $item['barang_id'],
                    'jumlah' => $item['jumlah'],
                    'harga_beli' => $item['harga_beli'],
                    'subtotal' => $subtotal,
                ]);

                // Update stok barang
                $barang = Barang::findOrFail($item['barang_id']);
                $stokAwal = $barang->stok;
                $barang->stok += $item['jumlah'];
                
                // Update HPP jika harga beli baru diinput
                if ($item['harga_beli'] > 0) {
                    $barang->hpp = $item['harga_beli'];
                }
                $barang->save();

                // Catat mutasi stok masuk
                StokMutasi::create([
                    'barang_id' => $barang->id,
                    'jenis' => 'masuk',
                    'jumlah' => $item['jumlah'],
                    'stok_sebelum' => $stokAwal,
                    'stok_sesudah' => $barang->stok,
                    'referensi' => $pembelian->nomor_pembelian,
                    'keterangan' => 'Pembelian stok barang dari supplier',
                ]);
            }

            // Jika lunas, catat riwayat pembayaran & kas keluar
            if ($isLunas) {
                PembelianPembayaran::create([
                    'pembelian_id' => $pembelian->id,
                    'user_id' => auth()->id(),
                    'tanggal_bayar' => now(),
                    'nominal' => $total,
                    'sumber' => 'kas',
                    'keterangan' => 'Pembayaran Lunas Saat Transaksi Dibuat',
                ]);

                KasFlow::create([
                    'tanggal' => $request->tanggal,
                    'tipe' => 'keluar',
                    'sumber' => 'kas',
                    'kategori' => 'pembelian',
                    'no_referensi' => $pembelian->nomor_pembelian,
                    'nominal' => $total,
                    'keterangan' => "Pembelian {$pembelian->nomor_pembelian} - Supplier: " . ($pembelian->supplier->nama ?? '-'),
                ]);
            }

            AuditLog::catat(
                'Simpan Pembelian',
                'Pembelian',
                $pembelian->nomor_pembelian,
                "Total: Rp " . number_format($total, 0, ',', '.') . " ({$request->status_bayar})"
            );
        });

        return redirect()->route('pembelian.index')->with('sukses', 'Faktur Pembelian berhasil disimpan!');
    }

    public function show(string|int $id): View
    {
        $realId = \App\Services\IdHasher::decode($id);
        $pembelian = Pembelian::with(['supplier', 'user', 'details.barang', 'pembayarans.user'])
            ->where('nomor_pembelian', $id)
            ->orWhere('id', $realId)
            ->firstOrFail();

        return view('pembelian.show', compact('pembelian'));
    }

    public function pelunasan(Request $request, string|int $pembelian): RedirectResponse
    {
        $realId = is_numeric($pembelian) ? (int) $pembelian : \App\Services\IdHasher::decode((string) $pembelian);
        $pembelian = Pembelian::where('id', $realId)
            ->orWhere('id', $pembelian)
            ->orWhere('nomor_pembelian', $pembelian)
            ->firstOrFail();

        $sisaHutang = $pembelian->sisa_hutang;

        if ($pembelian->status_bayar === 'lunas' || $sisaHutang <= 0) {
            return back()->with('sukses', 'Faktur Pembelian ini sudah lunas.');
        }

        $request->validate([
            'nominal_bayar' => 'required|numeric|min:1',
            'tanggal_bayar' => 'required|date',
            'sumber' => 'required|in:kas,bank',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $nominalInput = (float) $request->nominal_bayar;
        $bayarNyicil = min($nominalInput, $sisaHutang);

        DB::transaction(function () use ($request, $pembelian, $bayarNyicil) {
            $terbayarBaru = (float) $pembelian->terbayar + $bayarNyicil;
            $isLunas = $terbayarBaru >= (float) $pembelian->total;
            $tanggalBayar = Carbon::parse($request->tanggal_bayar);

            // 1. Simpan Riwayat Cicilan / Pembayaran
            PembelianPembayaran::create([
                'pembelian_id' => $pembelian->id,
                'user_id' => auth()->id(),
                'tanggal_bayar' => $tanggalBayar,
                'nominal' => $bayarNyicil,
                'sumber' => $request->sumber,
                'keterangan' => $request->keterangan ?: ($isLunas ? 'Pelunasan Penuh Hutang' : 'Pembayaran Cicilan Hutang'),
            ]);

            // 2. Update status & saldo terbayar Pembelian
            $pembelian->update([
                'terbayar' => $terbayarBaru,
                'status_bayar' => $isLunas ? 'lunas' : 'tempo',
                'tanggal_lunas' => $isLunas ? $tanggalBayar : null,
            ]);

            // 3. Catat Arus Kas Keluar
            KasFlow::create([
                'tanggal' => $tanggalBayar->toDateString(),
                'tipe' => 'keluar',
                'sumber' => $request->sumber,
                'kategori' => 'pembelian',
                'no_referensi' => $pembelian->nomor_pembelian,
                'nominal' => $bayarNyicil,
                'keterangan' => ($isLunas ? 'Pelunasan ' : 'Cicilan ') . "hutang pembelian {$pembelian->nomor_pembelian} - Supplier: " . ($pembelian->supplier->nama ?? '-'),
            ]);

            // 4. Catat Audit Log
            AuditLog::catat(
                'Pembayaran Hutang Pembelian',
                'Pembelian',
                $pembelian->nomor_pembelian,
                "Pembayaran cicilan Rp " . number_format($bayarNyicil, 0, ',', '.') . " (" . ($isLunas ? 'LUNAS' : 'Sisa Rp ' . number_format($pembelian->sisa_hutang, 0, ',', '.')) . ") via {$request->sumber}"
            );
        });

        $pesanSukses = $pembelian->fresh()->status_bayar === 'lunas'
            ? "Hutang pembelian {$pembelian->nomor_pembelian} telah LUNAS sepenuhnya!"
            : "Pembayaran cicilan Rp " . number_format($bayarNyicil, 0, ',', '.') . " berhasil dicatat. Sisa hutang: Rp " . number_format($pembelian->fresh()->sisa_hutang, 0, ',', '.');

        return back()->with('sukses', $pesanSukses);
    }
}

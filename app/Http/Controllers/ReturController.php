<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Barang;
use App\Models\KasFlow;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Retur;
use App\Models\ReturDetail;
use App\Models\StokMutasi;
use App\Services\StokService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReturController extends Controller
{
    public function index(Request $request): View
    {
        $query = Retur::with(['customer', 'supplier', 'user'])->latest();

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal', '>=', $request->dari_tanggal);
        }

        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal', '<=', $request->sampai_tanggal);
        }

        $returs = $query->paginate(15)->withQueryString();

        return view('retur.index', compact('returs'));
    }

    public function createPenjualan(StokService $stokService): View
    {
        $penjualans = Penjualan::with('customer')->latest()->take(50)->get();
        $barangList = Barang::where('aktif', true)->get();
        $stokMap = $stokService->stokBanyakBarang($barangList->pluck('id'));

        $barangs = $barangList->map(fn (Barang $b) => [
            'id' => $b->id,
            'kode' => $b->kode,
            'nama' => $b->nama,
            'harga' => (float) $b->harga_eceran,
            'stok' => $stokMap[$b->id] ?? 0.0,
        ]);

        return view('retur.form-penjualan', compact('penjualans', 'barangs'));
    }

    public function storePenjualan(Request $request): RedirectResponse
    {
        $request->validate([
            'tanggal' => 'required|date',
            'alasan' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.jumlah' => 'required|numeric|min:0.001',
            'items.*.harga' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $total = 0;
            foreach ($request->items as $item) {
                $total += $item['jumlah'] * $item['harga'];
            }

            $penjualan = $request->filled('penjualan_id') ? Penjualan::find($request->penjualan_id) : null;

            $retur = Retur::create([
                'nomor_retur' => Retur::buatNomorRetur('penjualan'),
                'jenis' => 'penjualan',
                'penjualan_id' => $penjualan?->id,
                'customer_id' => $penjualan?->customer_id,
                'user_id' => auth()->id(),
                'tanggal' => $request->tanggal,
                'total' => $total,
                'alasan' => $request->alasan,
            ]);

            foreach ($request->items as $item) {
                $subtotal = $item['jumlah'] * $item['harga'];

                ReturDetail::create([
                    'retur_id' => $retur->id,
                    'barang_id' => $item['barang_id'],
                    'jumlah' => $item['jumlah'],
                    'harga' => $item['harga'],
                    'subtotal' => $subtotal,
                ]);

                // Retur Penjualan = Barang Kembali Ke Toko = STOK BERTAMBAH
                $barang = Barang::findOrFail($item['barang_id']);

                StokMutasi::create([
                    'barang_id' => $barang->id,
                    'tanggal' => $request->tanggal,
                    'jenis_mutasi' => 'masuk',
                    'no_dokumen' => $retur->nomor_retur,
                    'masuk' => $item['jumlah'],
                    'keluar' => 0,
                    'hpp' => $barang->hpp,
                    'keterangan' => "Retur penjualan barang dari customer: {$request->alasan}",
                ]);
            }

            // Pengeluaran Kas (Pengembalian Uang Ke Customer)
            KasFlow::create([
                'tanggal' => $request->tanggal,
                'tipe' => 'keluar',
                'sumber' => 'kas',
                'kategori' => 'penjualan',
                'no_referensi' => $retur->nomor_retur,
                'nominal' => $total,
                'keterangan' => "Pengembalian uang retur {$retur->nomor_retur}",
            ]);

            AuditLog::catat(
                'Simpan Retur Penjualan',
                'Retur',
                $retur->nomor_retur,
                "Total Pengembalian: Rp " . number_format($total, 0, ',', '.')
            );
        });

        return redirect()->route('retur.index')->with('sukses', 'Retur Penjualan berhasil disimpan!');
    }

    public function createPembelian(StokService $stokService): View
    {
        $pembelians = Pembelian::with('supplier')->latest()->take(50)->get();
        $barangList = Barang::where('aktif', true)->get();
        $stokMap = $stokService->stokBanyakBarang($barangList->pluck('id'));

        $barangs = $barangList->map(fn (Barang $b) => [
            'id' => $b->id,
            'kode' => $b->kode,
            'nama' => $b->nama,
            'hpp' => (float) $b->hpp,
            'stok' => $stokMap[$b->id] ?? 0.0,
        ]);

        return view('retur.form-pembelian', compact('pembelians', 'barangs'));
    }

    public function storePembelian(Request $request): RedirectResponse
    {
        $request->validate([
            'tanggal' => 'required|date',
            'alasan' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.jumlah' => 'required|numeric|min:0.001',
            'items.*.harga' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $total = 0;
            foreach ($request->items as $item) {
                $total += $item['jumlah'] * $item['harga'];
            }

            $pembelian = $request->filled('pembelian_id') ? Pembelian::find($request->pembelian_id) : null;

            $retur = Retur::create([
                'nomor_retur' => Retur::buatNomorRetur('pembelian'),
                'jenis' => 'pembelian',
                'pembelian_id' => $pembelian?->id,
                'supplier_id' => $pembelian?->supplier_id,
                'user_id' => auth()->id(),
                'tanggal' => $request->tanggal,
                'total' => $total,
                'alasan' => $request->alasan,
            ]);

            foreach ($request->items as $item) {
                $subtotal = $item['jumlah'] * $item['harga'];

                ReturDetail::create([
                    'retur_id' => $retur->id,
                    'barang_id' => $item['barang_id'],
                    'jumlah' => $item['jumlah'],
                    'harga' => $item['harga'],
                    'subtotal' => $subtotal,
                ]);

                // Retur Pembelian = Barang Dibalikkan Ke Supplier = STOK BERKURANG
                $barang = Barang::findOrFail($item['barang_id']);

                StokMutasi::create([
                    'barang_id' => $barang->id,
                    'tanggal' => $request->tanggal,
                    'jenis_mutasi' => 'keluar',
                    'no_dokumen' => $retur->nomor_retur,
                    'masuk' => 0,
                    'keluar' => $item['jumlah'],
                    'hpp' => $barang->hpp,
                    'keterangan' => "Retur pembelian barang ke supplier: {$request->alasan}",
                ]);
            }

            // Penerimaan Kas (Refund dari Supplier)
            KasFlow::create([
                'tanggal' => $request->tanggal,
                'tipe' => 'masuk',
                'sumber' => 'kas',
                'kategori' => 'pembelian',
                'no_referensi' => $retur->nomor_retur,
                'nominal' => $total,
                'keterangan' => "Penerimaan refund retur {$retur->nomor_retur}",
            ]);

            AuditLog::catat(
                'Simpan Retur Pembelian',
                'Retur',
                $retur->nomor_retur,
                "Total Refund: Rp " . number_format($total, 0, ',', '.')
            );
        });

        return redirect()->route('retur.index')->with('sukses', 'Retur Pembelian berhasil disimpan!');
    }

    public function show(string|int $id): View
    {
        $realId = \App\Services\IdHasher::decode($id);
        $retur = Retur::with(['customer', 'supplier', 'user', 'penjualan', 'pembelian', 'details.barang'])
            ->where('nomor_retur', $id)
            ->orWhere('id', $realId)
            ->firstOrFail();

        return view('retur.show', compact('retur'));
    }
}

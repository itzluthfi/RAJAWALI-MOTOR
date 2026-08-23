<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Barang;
use App\Models\KasFlow;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\StokMutasi;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PembelianController extends Controller
{
    public function index(Request $request): View
    {
        $query = Pembelian::with(['supplier', 'user'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_pembelian', 'LIKE', "%{$search}%")
                  ->orWhere('nomor_faktur_supplier', 'LIKE', "%{$search}%")
                  ->orWhereHas('supplier', fn ($s) => $s->where('nama', 'LIKE', "%{$search}%"));
            });
        }

        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status_bayar', $request->status);
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

            $pembelian = Pembelian::create([
                'nomor_pembelian' => Pembelian::buatNomorPembelian(),
                'supplier_id' => $request->supplier_id,
                'user_id' => auth()->id(),
                'nomor_faktur_supplier' => $request->nomor_faktur_supplier,
                'tanggal' => $request->tanggal,
                'total' => $total,
                'status_bayar' => $request->status_bayar,
                'jatuh_tempo' => $request->status_bayar === 'tempo' ? now()->addDays(30) : null,
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

            // Jika lunas, catat pengeluaran kas
            if ($request->status_bayar === 'lunas') {
                KasFlow::create([
                    'tanggal' => $request->tanggal,
                    'tipe' => 'keluar',
                    'sumber' => 'kas',
                    'kategori' => 'pembelian',
                    'no_referensi' => $pembelian->nomor_pembelian,
                    'nominal' => $total,
                    'keterangan' => "Pembelian {$pembelian->nomor_pembelian} - Supplier ID {$request->supplier_id}",
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
        $pembelian = Pembelian::with(['supplier', 'user', 'details.barang'])
            ->where('nomor_pembelian', $id)
            ->orWhere('id', $realId)
            ->firstOrFail();

        return view('pembelian.show', compact('pembelian'));
    }
}

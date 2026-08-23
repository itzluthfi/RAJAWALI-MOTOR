<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Customer;
use App\Models\KasFlow;
use App\Models\PengaturanToko;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\StokMutasi;
use App\Services\StokService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class KasirController extends Controller
{
    public function index(Request $request, StokService $stokService): View
    {
        $barang = Barang::query()
            ->where('aktif', true)
            ->with('barcodes')
            ->orderBy('nama')
            ->get();

        $stok = $stokService->stokBanyakBarang($barang->pluck('id'));

        $daftarBarang = $barang->map(fn (Barang $b) => [
            'id' => $b->id,
            'kode' => $b->kode,
            'barcode' => $b->barcodes->firstWhere('utama', true)?->barcode ?? $b->barcodes->first()?->barcode ?? $b->kode,
            'nama' => $b->nama,
            'harga' => (float) $b->harga_eceran,
            'hpp' => (float) $b->hpp,
            'stok' => $stok[$b->id] ?? 0.0,
        ])->values();

        $daftarCustomer = Customer::query()
            ->where('aktif', true)
            ->orderByRaw("CASE WHEN nama LIKE 'Umum%' THEN 0 ELSE 1 END")
            ->orderBy('nama')
            ->get(['id', 'nama', 'termin_hari'])
            ->map(fn (Customer $c) => [
                'id' => $c->id,
                'nama' => $c->nama,
                'termin' => $c->termin_hari,
            ])->values();

        $pengaturan = PengaturanToko::current();
        $peran = $request->user()->peran;

        return view('kasir.index', [
            'daftarBarangJson' => $daftarBarang,
            'daftarCustomerJson' => $daftarCustomer,
            'batasDiskonPersen' => $peran === 'kasir' ? (float) $pengaturan->batas_diskon_kasir_persen : 0,
            'izinkanStokMinus' => (bool) $pengaturan->izinkan_stok_minus,
            'bolehJualDibawahHpp' => in_array($peran, ['owner', 'admin'], true),
        ]);
    }

    public function store(Request $request, StokService $stokService): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.kode' => ['required', 'string'],
            'items.*.qty' => ['required', 'numeric', 'min:0.001'],
            'items.*.harga' => ['required', 'numeric', 'min:0'],
            'items.*.diskon' => ['nullable', 'numeric', 'min:0'],
            'diskon' => ['nullable', 'numeric', 'min:0'],
            'pajak' => ['nullable', 'numeric', 'min:0'],
            'bayar' => ['nullable', 'numeric', 'min:0'],
            'uang_muka' => ['nullable', 'numeric', 'min:0'],
            'metode_pembayaran' => ['required', 'string'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $penjualan = DB::transaction(function () use ($validated, $request) {
                $nomorNota = Penjualan::buatNomorNota();
                $subtotal = 0;

                $dataItems = [];
                foreach ($validated['items'] as $item) {
                    $barang = Barang::query()->where('kode', $item['kode'])->firstOrFail();
                    $qty = (float) $item['qty'];
                    $harga = (float) $item['harga'];
                    $diskonBaris = (float) ($item['diskon'] ?? 0);
                    $itemSubtotal = ($qty * $harga) - $diskonBaris;
                    $subtotal += $itemSubtotal;

                    $dataItems[] = [
                        'barang' => $barang,
                        'qty' => $qty,
                        'harga' => $harga,
                        'diskon' => $diskonBaris,
                        'hpp' => (float) $barang->hpp,
                        'subtotal' => $itemSubtotal,
                    ];
                }

                $diskon = (float) ($validated['diskon'] ?? 0);
                $pajak = (float) ($validated['pajak'] ?? 0);
                $uangMuka = (float) ($validated['uang_muka'] ?? 0);
                $totalAkhir = max(0, $subtotal - $diskon + $pajak);
                $bayarInput = (float) ($validated['bayar'] ?? 0);
                $bayar = ($validated['metode_pembayaran'] === 'tunai' || $bayarInput <= 0) ? $totalAkhir : $bayarInput;
                $kembali = max(0, $bayar - $totalAkhir);

                $penjualan = Penjualan::create([
                    'nomor_nota' => $nomorNota,
                    'customer_id' => $validated['customer_id'] ?? null,
                    'user_id' => $request->user()->id,
                    'subtotal' => $subtotal,
                    'diskon' => $diskon,
                    'pajak' => $pajak,
                    'total_akhir' => $totalAkhir,
                    'bayar' => $bayar,
                    'kembali' => $kembali,
                    'uang_muka' => $uangMuka,
                    'metode_pembayaran' => $validated['metode_pembayaran'],
                    'status_bayar' => ($validated['metode_pembayaran'] === 'tunai' || $bayar >= $totalAkhir) ? 'lunas' : 'piutang',
                    'catatan' => $validated['catatan'] ?? null,
                ]);

                foreach ($dataItems as $di) {
                    PenjualanDetail::create([
                        'penjualan_id' => $penjualan->id,
                        'barang_id' => $di['barang']->id,
                        'qty' => $di['qty'],
                        'harga_satuan' => $di['harga'],
                        'diskon' => $di['diskon'],
                        'hpp' => $di['hpp'],
                        'subtotal' => $di['subtotal'],
                    ]);

                    StokMutasi::create([
                        'barang_id' => $di['barang']->id,
                        'tanggal' => now()->toDateString(),
                        'jenis_mutasi' => 'penjualan',
                        'no_dokumen' => $nomorNota,
                        'masuk' => 0,
                        'keluar' => $di['qty'],
                        'hpp' => $di['hpp'],
                        'keterangan' => "Penjualan POS Nota {$nomorNota}",
                    ]);
                }

                // Catat Arus Kas Masuk (KasFlow)
                if ($penjualan->metode_pembayaran === 'tunai' && $totalAkhir > 0) {
                    KasFlow::create([
                        'tanggal' => now()->toDateString(),
                        'tipe' => 'masuk',
                        'sumber' => 'kas',
                        'kategori' => 'penjualan',
                        'no_referensi' => $nomorNota,
                        'nominal' => $totalAkhir,
                        'keterangan' => "Penjualan POS Nota {$nomorNota} (Tunai)",
                    ]);
                } elseif ($penjualan->metode_pembayaran === 'tempo' && $uangMuka > 0) {
                    KasFlow::create([
                        'tanggal' => now()->toDateString(),
                        'tipe' => 'masuk',
                        'sumber' => 'kas',
                        'kategori' => 'piutang',
                        'no_referensi' => $nomorNota,
                        'nominal' => $uangMuka,
                        'keterangan' => "Uang Muka Penjualan Nota {$nomorNota}",
                    ]);
                }

                return $penjualan;
            });

            return response()->json([
                'sukses' => true,
                'pesan' => "Transaksi {$penjualan->nomor_nota} berhasil disimpan.",
                'nomor_nota' => $penjualan->nomor_nota,
                'penjualan_id' => $penjualan->id,
                'cetak_url' => route('cetak.nota', $penjualan->id),
            ]);
        } catch (Throwable $e) {
            Log::error('Gagal memproses transaksi kasir POS', ['pesan' => $e->getMessage()]);

            return response()->json([
                'sukses' => false,
                'pesan' => 'Gagal memproses transaksi kasir. ' . $e->getMessage(),
            ], 422);
        }
    }

    public function hargaTerakhir(Request $request): JsonResponse
    {
        $customerId = $request->input('customer_id');
        $barangId = $request->input('barang_id');

        if (!$customerId || !$barangId) {
            return response()->json(['harga' => null]);
        }

        $lastDetail = \App\Models\PenjualanDetail::query()
            ->whereHas('penjualan', function ($q) use ($customerId) {
                $q->where('customer_id', $customerId);
            })
            ->where('barang_id', $barangId)
            ->latest('id')
            ->first();

        return response()->json([
            'harga' => $lastDetail ? (float) $lastDetail->harga_satuan : null,
            'tanggal' => $lastDetail ? $lastDetail->created_at->format('d M Y') : null,
            'nota' => $lastDetail ? $lastDetail->penjualan->nomor_nota : null,
        ]);
    }
}

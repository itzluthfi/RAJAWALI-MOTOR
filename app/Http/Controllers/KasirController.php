<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Barang;
use App\Models\Customer;
use App\Models\KasFlow;
use App\Models\PengaturanToko;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Service;
use App\Models\ServiceDetail;
use App\Models\ServiceJasa;
use App\Models\StokMutasi;
use App\Models\User;
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
            ->with(['barcodes', 'group'])
            ->orderBy('nama')
            ->get();

        $stok = $stokService->stokBanyakBarang($barang->pluck('id'));

        $daftarBarang = $barang->map(function (Barang $b) use ($stok) {
            $primaryBarcode = $b->barcode ?: ($b->barcodes->firstWhere('utama', true)?->barcode ?? $b->barcodes->first()?->barcode ?? '');
            $allBc = $b->barcodes->pluck('barcode')->toArray();
            if ($b->barcode && !in_array($b->barcode, $allBc, true)) {
                $allBc[] = $b->barcode;
            }

            return [
                'id' => $b->id,
                'kode' => $b->kode,
                'barcode' => $primaryBarcode ?: $b->kode,
                'qrcode' => $b->qrcode ?: '',
                'all_barcodes' => $allBc,
                'nama' => $b->nama,
                'is_jasa' => false,
                'harga' => (float) $b->harga_eceran,
                'harga_grosir' => (float) ($b->harga_grosir > 0 ? $b->harga_grosir : $b->harga_eceran),
                'min_qty_grosir_1' => (float) ($b->min_qty_grosir_1 ?? 3),
                'harga_grosir_1' => (float) ($b->harga_grosir_1 > 0 ? $b->harga_grosir_1 : ($b->harga_grosir > 0 ? $b->harga_grosir : $b->harga_eceran)),
                'min_qty_grosir_2' => (float) ($b->min_qty_grosir_2 ?? 24),
                'harga_grosir_2' => (float) ($b->harga_grosir_2 > 0 ? $b->harga_grosir_2 : ($b->harga_grosir_1 > 0 ? $b->harga_grosir_1 : $b->harga_eceran)),
                'hpp' => (float) $b->hpp,
                'stok' => (float) ($stok[$b->id] ?? 0.0),
            ];
        });

        // Load standalone active Jasa & append to POS catalog
        $jasas = \App\Models\Jasa::where('aktif', true)->orderBy('nama')->get();
        $daftarJasa = $jasas->map(function (\App\Models\Jasa $j) {
            return [
                'id' => 'JSA-' . $j->id,
                'kode' => $j->kode,
                'barcode' => $j->kode,
                'nama' => $j->nama,
                'is_jasa' => true,
                'kategori' => $j->kategori ?? 'Jasa Servis',
                'harga' => (float) $j->tarif,
                'harga_grosir' => (float) $j->tarif,
                'min_qty_grosir_1' => 1,
                'harga_grosir_1' => (float) $j->tarif,
                'min_qty_grosir_2' => 1,
                'harga_grosir_2' => (float) $j->tarif,
                'hpp' => 0.0,
                'stok' => 999999, // unlimited service
            ];
        });

        $daftarKatalogLengkap = $daftarBarang->concat($daftarJasa)->values();

        $daftarCustomer = Customer::query()
            ->where('aktif', true)
            ->orderByRaw("CASE WHEN nama LIKE 'Umum%' THEN 0 ELSE 1 END")
            ->orderBy('nama')
            ->get(['id', 'nama', 'plat_nomor', 'jenis_kendaraan', 'kategori', 'telepon', 'no_wa', 'termin_hari'])
            ->map(fn (Customer $c) => [
                'id' => $c->id,
                'nama' => $c->nama,
                'plat' => $c->plat_nomor,
                'motor' => $c->jenis_kendaraan,
                'kategori' => $c->kategori ?? 'umum',
                'telepon' => $c->telepon ?? $c->no_wa ?? '',
                'termin' => $c->termin_hari ?? 30,
            ])->values();

        $montirs = User::query()
            ->where('aktif', true)
            ->whereIn('peran', ['montir', 'admin', 'owner'])
            ->orderBy('name')
            ->get(['id', 'name', 'peran'])
            ->map(fn (User $u) => [
                'id' => $u->id,
                'nama' => $u->name,
                'peran' => $u->peran,
            ]);

        $antreanService = Service::query()
            ->whereIn('status', ['masuk', 'dikerjakan', 'selesai'])
            ->where('status_lunas', false)
            ->with(['customer', 'montir', 'details.barang', 'jasas'])
            ->latest('id')
            ->get()
            ->map(fn (Service $s) => [
                'id' => $s->id,
                'nomor_dokumen' => $s->nomor_dokumen,
                'tanggal_masuk' => $s->tanggal_masuk->format('d M Y'),
                'customer_nama' => $s->customer->nama ?? 'Umum',
                'customer_id' => $s->customer_id,
                'plat_nomor' => $s->customer->plat_nomor ?? '-',
                'merk_type' => $s->merk_type ?? $s->customer->jenis_kendaraan ?? '-',
                'montir_nama' => $s->montir->name ?? '-',
                'status' => $s->status,
                'total_biaya' => (float) ($s->grand_total_nett ?? 0),
                'keluhan' => $s->keluhan ?? '',
            ]);

        $pengaturan = PengaturanToko::current();
        $peran = $request->user()->peran;

        return view('kasir.index', [
            'daftarBarangJson' => $daftarKatalogLengkap,
            'daftarCustomerJson' => $daftarCustomer,
            'montirsJson' => $montirs,
            'antreanServiceJson' => $antreanService,
            'batasDiskonPersen' => $peran === 'kasir' ? (float) $pengaturan->batas_diskon_kasir_persen : 0,
            'izinkanStokMinus' => (bool) $pengaturan->izinkan_stok_minus,
            'printerStrukAktif' => (bool) $pengaturan->printer_struk_aktif,
            'printerFakturAktif' => (bool) $pengaturan->printer_faktur_aktif,
            'bolehJualDibawahHpp' => in_array($peran, ['owner', 'admin'], true),
        ]);
    }

    public function store(Request $request, StokService $stokService): JsonResponse
    {
        $validated = $request->validate([
            'tipe_transaksi' => ['nullable', 'in:penjualan,service_spk,service_langsung,service_pelunasan'],
            'service_id' => ['nullable', 'exists:services,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'plat_nomor' => ['nullable', 'string', 'max:30'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'merk_type' => ['nullable', 'string', 'max:100'],
            'montir_id' => ['nullable', 'exists:users,id'],
            'keluhan' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.kode' => ['required', 'string'],
            'items.*.nama' => ['nullable', 'string'],
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

        $tipeTransaksi = $validated['tipe_transaksi'] ?? 'penjualan';

        try {
            $result = DB::transaction(function () use ($validated, $request, $stokService, $tipeTransaksi) {
                $pengaturan = PengaturanToko::current();

                // -------------------------------------------------------------
                // CASE 1: SIMPAN SPK SERVIS BARU (MOTOR MASUK & DITINGGAL)
                // -------------------------------------------------------------
                if ($tipeTransaksi === 'service_spk') {
                    $nomorDokumen = Service::buatNomorDokumen();
                    
                    // Pastikan ada customer (jika belum ada, hubungkan ke customer default atau pertama)
                    $customerId = $validated['customer_id'] ?? Customer::firstOrCreate(
                        ['nama' => 'Umum (Servis ' . ($validated['plat_nomor'] ?? 'Motor') . ')'],
                        ['plat_nomor' => $validated['plat_nomor'] ?? '', 'jenis_kendaraan' => $validated['merk_type'] ?? '']
                    )->id;

                    $totalSupplier = 0.0;
                    $totalNett = 0.0;

                    $service = Service::create([
                        'nomor_dokumen' => $nomorDokumen,
                        'tanggal_masuk' => now()->toDateString(),
                        'customer_id' => $customerId,
                        'montir_id' => $validated['montir_id'] ?? null,
                        'merk_type' => $validated['merk_type'] ?? null,
                        'keluhan' => $validated['keluhan'] ?? null,
                        'catatan' => $validated['catatan'] ?? null,
                        'repaired_by' => 'intern',
                        'status' => 'dikerjakan',
                        'status_lunas' => false,
                        'grand_total_supplier' => 0,
                        'grand_total_nett' => 0,
                    ]);

                    foreach ($validated['items'] as $item) {
                        $barang = Barang::where('kode', $item['kode'])->first();
                        $qty = (float) $item['qty'];
                        $harga = (float) $item['harga'];
                        $namaItem = $item['nama'] ?? ($barang?->nama ?? $item['kode']);
                        $isJasa = ($item['kode'] === 'JASA') || ($barang && $barang->group && str_contains(strtolower($barang->group->nama), 'jasa'));

                        if ($isJasa) {
                            ServiceJasa::create([
                                'service_id' => $service->id,
                                'nama_jasa' => $namaItem,
                                'harga_supplier' => (float) ($barang?->hpp ?? 0),
                                'harga_nett' => $harga,
                            ]);
                            $totalSupplier += (float) ($barang?->hpp ?? 0);
                            $totalNett += $harga;
                        } else {
                            if (!$barang) {
                                throw new \RuntimeException("Barang dengan kode '{$item['kode']}' tidak ditemukan.");
                            }
                            $subtotal = $qty * $harga;
                            ServiceDetail::create([
                                'service_id' => $service->id,
                                'barang_id' => $barang->id,
                                'qty' => $qty,
                                'harga' => $harga,
                                'subtotal' => $subtotal,
                            ]);
                            $totalNett += $subtotal;
                        }
                    }

                    $service->update([
                        'grand_total_supplier' => $totalSupplier,
                        'grand_total_nett' => $totalNett,
                    ]);

                    AuditLog::catat(
                        'Buat SPK Servis di Kasir',
                        'Service',
                        $nomorDokumen,
                        "Servis motor {$service->merk_type} (Plat: {$validated['plat_nomor']}) Montir: " . ($service->montir->name ?? 'Belum Ditunjuk')
                    );

                    return [
                        'sukses' => true,
                        'pesan' => "Tanda Terima Servis {$nomorDokumen} berhasil diterbitkan!",
                        'nomor_dokumen' => $nomorDokumen,
                        'tipe' => 'service_spk',
                        'cetak_url' => route('cetak.tanda-terima-service', $nomorDokumen),
                    ];
                }

                // -------------------------------------------------------------
                // CASE 2: PENJUALAN KASIR POS / SERVIS LANGSUNG / PELUNASAN
                // -------------------------------------------------------------
                $nomorNota = Penjualan::buatNomorNota();
                $subtotal = 0;
                $dataItems = [];

                foreach ($validated['items'] as $item) {
                    $barang = Barang::query()->where('kode', $item['kode'])->first();
                    $qty = (float) $item['qty'];
                    $harga = (float) $item['harga'];
                    $diskonBaris = (float) ($item['diskon'] ?? 0);
                    $namaItem = $item['nama'] ?? ($barang?->nama ?? $item['kode']);
                    $isJasa = ($item['kode'] === 'JASA') || ($barang && $barang->group && str_contains(strtolower($barang->group->nama), 'jasa'));

                    if (!$barang && $isJasa) {
                        $groupJasa = \App\Models\Group::firstOrCreate(['nama' => 'Jasa Bengkel'], ['aktif' => true]);
                        $satuanJasa = \App\Models\Satuan::firstOrCreate(['nama' => 'Jasa'], ['aktif' => true]);
                        $barang = Barang::firstOrCreate(
                            ['kode' => 'JASA'],
                            [
                                'nama' => $namaItem,
                                'group_id' => $groupJasa->id,
                                'satuan_id' => $satuanJasa->id,
                                'hpp' => 0,
                                'harga_eceran' => $harga,
                                'aktif' => true,
                            ]
                        );
                    }

                    if (!$barang) {
                        throw new \RuntimeException("Barang dengan kode '{$item['kode']}' tidak ditemukan.");
                    }

                    // Cek ketersediaan stok fisik (kecuali Jasa)
                    $stokSekarang = $stokService->stokSaatIni($barang);
                    if (! $pengaturan->izinkan_stok_minus && ! $isJasa && $stokSekarang < $qty) {
                        throw new \RuntimeException("Stok barang '{$barang->nama}' (Kode: {$barang->kode}) tidak mencukupi. Sisa stok: {$stokSekarang}, diminta: {$qty}.");
                    }

                    $itemSubtotal = ($qty * $harga) - $diskonBaris;
                    $subtotal += $itemSubtotal;

                    $dataItems[] = [
                        'barang' => $barang,
                        'is_jasa' => $isJasa,
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

                if ($validated['metode_pembayaran'] === 'tunai') {
                    $bayar = $bayarInput > 0 ? $bayarInput : $totalAkhir;
                } else {
                    $bayar = $bayarInput;
                }

                $kembali = max(0, $bayar - $totalAkhir);

                $catatanTambahan = $validated['catatan'] ?? '';
                if (!empty($validated['plat_nomor'])) {
                    $catatanTambahan = trim("Plat: {$validated['plat_nomor']} (" . ($validated['merk_type'] ?? 'Motor') . ") | " . $catatanTambahan, " |");
                }
                if (!empty($validated['telepon'])) {
                    $catatanTambahan = trim("WA: {$validated['telepon']} | " . $catatanTambahan, " |");
                    if (!empty($validated['customer_id'])) {
                        $cObj = Customer::find($validated['customer_id']);
                        if ($cObj && empty($cObj->telepon) && !str_contains(strtolower($cObj->nama), 'umum')) {
                            $cObj->update(['telepon' => $validated['telepon'], 'no_wa' => $validated['telepon']]);
                        }
                    }
                }

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
                    'catatan' => $catatanTambahan ?: null,
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

                    // Hanya catat mutasi stok keluar jika bukan tipe jasa
                    if (! $di['is_jasa']) {
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

                // Jika pelunasan dari Service yang sudah ada sebelumnya
                if ($tipeTransaksi === 'service_pelunasan' && !empty($validated['service_id'])) {
                    $service = Service::find($validated['service_id']);
                    if ($service) {
                        $service->update([
                            'status' => 'selesai',
                            'status_lunas' => true,
                            'grand_total_nett' => $totalAkhir,
                        ]);
                    }
                }

                return [
                    'sukses' => true,
                    'pesan' => "Transaksi {$penjualan->nomor_nota} berhasil disimpan.",
                    'nomor_nota' => $penjualan->nomor_nota,
                    'penjualan_id' => $penjualan->id,
                    'tipe' => 'penjualan',
                    'cetak_url' => route('cetak.nota', $penjualan->id),
                ];
            });

            return response()->json($result);
        } catch (Throwable $e) {
            Log::error('Gagal memproses transaksi kasir POS', ['pesan' => $e->getMessage()]);

            return response()->json([
                'sukses' => false,
                'pesan' => 'Gagal memproses transaksi kasir: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function quickStoreCustomer(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'telepon' => ['nullable', 'string', 'max:25'],
            'plat_nomor' => ['nullable', 'string', 'max:20'],
            'jenis_kendaraan' => ['nullable', 'string', 'max:100'],
            'kategori' => ['nullable', 'in:umum,grosir,langganan,mitra,eceran'],
        ]);

        $telepon = !empty($validated['telepon']) ? trim($validated['telepon']) : null;

        $customer = Customer::create([
            'nama' => trim($validated['nama']),
            'telepon' => $telepon,
            'no_wa' => $telepon,
            'plat_nomor' => !empty($validated['plat_nomor']) ? strtoupper(trim($validated['plat_nomor'])) : null,
            'jenis_kendaraan' => !empty($validated['jenis_kendaraan']) ? trim($validated['jenis_kendaraan']) : null,
            'kategori' => $validated['kategori'] ?? 'umum',
            'aktif' => true,
        ]);

        AuditLog::catat('Tambah Customer Cepat', 'Customer', (string) $customer->id, "Customer: {$customer->nama} (Kasir POS)");

        return response()->json([
            'sukses' => true,
            'pesan' => "Customer '{$customer->nama}' berhasil ditambahkan dan langsung aktif!",
            'customer' => [
                'id' => $customer->id,
                'nama' => $customer->nama,
                'plat' => $customer->plat_nomor,
                'motor' => $customer->jenis_kendaraan,
                'kategori' => $customer->kategori ?? 'umum',
                'telepon' => $customer->telepon ?? '',
                'termin' => 30,
            ],
        ]);
    }

    public function antreanService(): JsonResponse
    {
        $antrean = Service::query()
            ->whereIn('status', ['masuk', 'dikerjakan', 'selesai'])
            ->where('status_lunas', false)
            ->with(['customer', 'montir', 'details.barang', 'jasas'])
            ->latest('id')
            ->get()
            ->map(fn (Service $s) => [
                'id' => $s->id,
                'nomor_dokumen' => $s->nomor_dokumen,
                'tanggal_masuk' => $s->tanggal_masuk->format('d M Y'),
                'customer_nama' => $s->customer->nama ?? 'Umum',
                'customer_id' => $s->customer_id,
                'plat_nomor' => $s->customer->plat_nomor ?? '-',
                'merk_type' => $s->merk_type ?? $s->customer->jenis_kendaraan ?? '-',
                'montir_nama' => $s->montir->name ?? '-',
                'status' => $s->status,
                'total_biaya' => (float) ($s->grand_total_nett ?? 0),
                'keluhan' => $s->keluhan ?? '',
            ]);

        return response()->json([
            'sukses' => true,
            'antrean' => $antrean,
        ]);
    }

    public function detailAntreanService(Service $service): JsonResponse
    {
        $service->load(['customer', 'montir', 'details.barang', 'jasas']);

        $items = [];
        foreach ($service->details as $d) {
            if ($d->barang) {
                $items[] = [
                    'id' => $d->barang->id,
                    'kode' => $d->barang->kode,
                    'nama' => $d->barang->nama,
                    'is_jasa' => false,
                    'qty' => (float) $d->qty,
                    'harga' => (float) $d->harga,
                    'diskon' => 0,
                    'subtotal' => (float) $d->subtotal,
                ];
            }
        }

        foreach ($service->jasas as $j) {
            $items[] = [
                'id' => null,
                'kode' => 'JASA',
                'nama' => $j->nama_jasa,
                'is_jasa' => true,
                'qty' => 1,
                'harga' => (float) $j->harga_nett,
                'diskon' => 0,
                'subtotal' => (float) $j->harga_nett,
            ];
        }

        return response()->json([
            'sukses' => true,
            'service' => [
                'id' => $service->id,
                'nomor_dokumen' => $service->nomor_dokumen,
                'customer_id' => $service->customer_id,
                'customer_nama' => $service->customer->nama ?? 'Umum',
                'plat_nomor' => $service->customer->plat_nomor ?? '',
                'merk_type' => $service->merk_type ?? '',
                'montir_id' => $service->montir_id,
                'montir_nama' => $service->montir->name ?? '',
                'keluhan' => $service->keluhan ?? '',
                'items' => $items,
                'grand_total' => (float) $service->grand_total_nett,
            ],
        ]);
    }

    public function hargaTerakhir(Request $request): JsonResponse
    {
        $customerId = $request->input('customer_id');
        $barangId = $request->input('barang_id');

        if (!$customerId || !$barangId) {
            return response()->json(['harga' => null]);
        }

        $lastDetail = PenjualanDetail::query()
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

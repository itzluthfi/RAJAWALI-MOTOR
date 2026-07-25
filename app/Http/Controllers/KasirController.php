<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Customer;
use App\Models\PengaturanToko;
use App\Services\StokService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KasirController extends Controller
{
    public function __invoke(Request $request, StokService $stokService): View
    {
        $barang = Barang::query()
            ->where('aktif', true)
            ->with('barcodes')
            ->orderBy('nama')
            ->get();

        $stok = $stokService->stokBanyakBarang($barang->pluck('id'));

        $daftarBarang = $barang->map(fn (Barang $b) => [
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
}

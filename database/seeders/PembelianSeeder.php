<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\KasFlow;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\StokMutasi;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class PembelianSeeder extends Seeder
{
    public function run(): void
    {
        $supplierAstra = Supplier::where('nama', 'LIKE', '%Astra%')->first() ?? Supplier::first();
        $supplierSinar = Supplier::where('nama', 'LIKE', '%Sinar%')->first() ?? Supplier::first();
        $userOwner = User::first();
        $barang1 = Barang::first();
        $barang2 = Barang::skip(1)->first() ?? $barang1;

        if (! $supplierAstra || ! $userOwner || ! $barang1) {
            return;
        }

        // Pembelian 1 - Lunas
        $pembelian1 = Pembelian::create([
            'nomor_pembelian' => 'PB2026080001',
            'supplier_id' => $supplierAstra->id,
            'user_id' => $userOwner->id,
            'nomor_faktur_supplier' => 'INV-ASTRA-9021',
            'tanggal' => now()->subDays(5)->toDateString(),
            'total' => 3500000,
            'status_bayar' => 'lunas',
            'keterangan' => 'Restock rutin busi & oli mesin matic',
        ]);

        PembelianDetail::create([
            'pembelian_id' => $pembelian1->id,
            'barang_id' => $barang1->id,
            'jumlah' => 20,
            'harga_beli' => $barang1->hpp > 0 ? $barang1->hpp : 45000,
            'subtotal' => 900000,
        ]);

        PembelianDetail::create([
            'pembelian_id' => $pembelian1->id,
            'barang_id' => $barang2->id,
            'jumlah' => 10,
            'harga_beli' => $barang2->hpp > 0 ? $barang2->hpp : 260000,
            'subtotal' => 2600000,
        ]);

        KasFlow::create([
            'tanggal' => now()->subDays(5)->toDateString(),
            'tipe' => 'keluar',
            'sumber' => 'kas',
            'kategori' => 'pembelian',
            'no_referensi' => $pembelian1->nomor_pembelian,
            'nominal' => 3500000,
            'keterangan' => 'Pembelian PB2026080001 - PT Astra Otoparts',
        ]);

        // Pembelian 2 - Tempo
        Pembelian::create([
            'nomor_pembelian' => 'PB2026080002',
            'supplier_id' => $supplierSinar->id,
            'user_id' => $userOwner->id,
            'nomor_faktur_supplier' => 'SJ-SMJ-441',
            'tanggal' => now()->subDays(2)->toDateString(),
            'total' => 1200000,
            'status_bayar' => 'tempo',
            'jatuh_tempo' => now()->addDays(28)->toDateString(),
            'keterangan' => 'Pembelian kampas rem & filter udara',
        ]);
    }
}

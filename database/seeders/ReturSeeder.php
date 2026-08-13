<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\KasFlow;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Retur;
use App\Models\ReturDetail;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReturSeeder extends Seeder
{
    public function run(): void
    {
        $userOwner = User::first();
        $penjualan = Penjualan::first();
        $pembelian = Pembelian::first();
        $barang = Barang::first();

        if (! $userOwner || ! $barang) {
            return;
        }

        // Retur Penjualan
        $returJual = Retur::create([
            'nomor_retur' => 'RJ2026080001',
            'jenis' => 'penjualan',
            'penjualan_id' => $penjualan?->id,
            'customer_id' => $penjualan?->customer_id,
            'user_id' => $userOwner->id,
            'tanggal' => now()->subDays(3)->toDateString(),
            'total' => 45000,
            'alasan' => 'Ukuran kampas rem tidak cocok dengan tipe motor',
        ]);

        ReturDetail::create([
            'retur_id' => $returJual->id,
            'barang_id' => $barang->id,
            'jumlah' => 1,
            'harga' => 45000,
            'subtotal' => 45000,
        ]);

        KasFlow::create([
            'tanggal' => now()->subDays(3)->toDateString(),
            'tipe' => 'keluar',
            'sumber' => 'kas',
            'kategori' => 'penjualan',
            'no_referensi' => $returJual->nomor_retur,
            'nominal' => 45000,
            'keterangan' => 'Pengembalian uang retur RJ2026080001',
        ]);

        // Retur Pembelian
        $returBeli = Retur::create([
            'nomor_retur' => 'RB2026080001',
            'jenis' => 'pembelian',
            'pembelian_id' => $pembelian?->id,
            'supplier_id' => $pembelian?->supplier_id,
            'user_id' => $userOwner->id,
            'tanggal' => now()->subDay()->toDateString(),
            'total' => 150000,
            'alasan' => 'Kemasan barang rusak dari pihak ekspedisi vendor',
        ]);

        ReturDetail::create([
            'retur_id' => $returBeli->id,
            'barang_id' => $barang->id,
            'jumlah' => 2,
            'harga' => 75000,
            'subtotal' => 150000,
        ]);

        KasFlow::create([
            'tanggal' => now()->subDay()->toDateString(),
            'tipe' => 'masuk',
            'sumber' => 'kas',
            'kategori' => 'pembelian',
            'no_referensi' => $returBeli->nomor_retur,
            'nominal' => 150000,
            'keterangan' => 'Penerimaan refund retur RB2026080001',
        ]);
    }
}

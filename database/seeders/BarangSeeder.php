<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Group;
use App\Models\Satuan;
use App\Models\StokMutasi;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $sparepart = Group::where('nama', 'Sparepart')->firstOrFail();
        $oli = Group::where('nama', 'Oli')->firstOrFail();
        $pcs = Satuan::where('nama', 'PCS')->firstOrFail();
        $set = Satuan::where('nama', 'SET')->firstOrFail();
        $botol = Satuan::where('nama', 'BOTOL')->firstOrFail();

        $daftar = [
            [
                'kode' => 'DISVCBSTK', 'nama' => 'DISC PAD VARIO CBS', 'group_id' => $sparepart->id, 'satuan_id' => $pcs->id,
                'harga_beli_terakhir' => 15000, 'hpp' => 15000, 'harga_eceran' => 20000, 'harga_grosir' => 17000,
                'stok_minimum' => 10, 'lokasi_rak' => 'A-01', 'aktif' => true, 'barcode' => '8991020001', 'stok_awal' => 24,
            ],
            [
                'kode' => 'OLIFED1L', 'nama' => 'OLI FEDERAL MATIC 1L', 'group_id' => $oli->id, 'satuan_id' => $botol->id,
                'harga_beli_terakhir' => 36000, 'hpp' => 36000, 'harga_eceran' => 45000, 'harga_grosir' => 40000,
                'stok_minimum' => 10, 'lokasi_rak' => 'B-03', 'aktif' => true, 'barcode' => '8991020002', 'stok_awal' => 3,
            ],
            [
                'kode' => 'KMPRMVAR', 'nama' => 'KAMPAS REM VARIO', 'group_id' => $sparepart->id, 'satuan_id' => $set->id,
                'harga_beli_terakhir' => 27000, 'hpp' => 27000, 'harga_eceran' => 35000, 'harga_grosir' => 30000,
                'stok_minimum' => 8, 'lokasi_rak' => 'A-05', 'aktif' => true, 'barcode' => '8991020003', 'stok_awal' => 12,
            ],
            [
                'kode' => 'BUSINGKC', 'nama' => 'BUSI NGK CPR8EA', 'group_id' => $sparepart->id, 'satuan_id' => $pcs->id,
                'harga_beli_terakhir' => 13000, 'hpp' => 13000, 'harga_eceran' => 18000, 'harga_grosir' => 15000,
                'stok_minimum' => 15, 'lokasi_rak' => 'A-09', 'aktif' => false, 'barcode' => '8991020004', 'stok_awal' => 40,
            ],
            [
                'kode' => 'AKIGSYU5', 'nama' => 'AKI GS ASTRA GTZ5S', 'group_id' => $sparepart->id, 'satuan_id' => $pcs->id,
                'harga_beli_terakhir' => 155000, 'hpp' => 155000, 'harga_eceran' => 185000, 'harga_grosir' => 170000,
                'stok_minimum' => 5, 'lokasi_rak' => 'C-01', 'aktif' => true, 'barcode' => '8991020005', 'stok_awal' => 6,
            ],
        ];

        foreach ($daftar as $data) {
            $barang = Barang::updateOrCreate(
                ['kode' => $data['kode']],
                [
                    'nama' => $data['nama'],
                    'group_id' => $data['group_id'],
                    'satuan_id' => $data['satuan_id'],
                    'harga_beli_terakhir' => $data['harga_beli_terakhir'],
                    'hpp' => $data['hpp'],
                    'harga_eceran' => $data['harga_eceran'],
                    'harga_grosir' => $data['harga_grosir'],
                    'stok_minimum' => $data['stok_minimum'],
                    'lokasi_rak' => $data['lokasi_rak'],
                    'aktif' => $data['aktif'],
                ]
            );

            $barang->barcodes()->updateOrCreate(
                ['barcode' => $data['barcode']],
                ['utama' => true]
            );

            if (! StokMutasi::where('barang_id', $barang->id)->where('no_dokumen', 'SALDO-AWAL')->exists()) {
                StokMutasi::create([
                    'barang_id' => $barang->id,
                    'tanggal' => now()->toDateString(),
                    'jenis_mutasi' => 'penyesuaian',
                    'no_dokumen' => 'SALDO-AWAL',
                    'masuk' => $data['stok_awal'],
                    'keluar' => 0,
                    'hpp' => $data['hpp'],
                    'keterangan' => 'Saldo awal data contoh',
                ]);
            }
        }
    }
}

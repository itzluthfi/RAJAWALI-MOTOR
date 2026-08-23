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
        $sparepart = Group::firstOrCreate(['nama' => 'Sparepart']);
        $oli = Group::firstOrCreate(['nama' => 'Oli']);
        $jasaGroup = Group::firstOrCreate(['nama' => 'Jasa & Service']);
        
        $pcs = Satuan::firstOrCreate(['nama' => 'PCS']);
        $set = Satuan::firstOrCreate(['nama' => 'SET']);
        $botol = Satuan::firstOrCreate(['nama' => 'BOTOL']);
        $satuanJasa = Satuan::firstOrCreate(['nama' => 'JASA']);

        $daftar = [
            // SPAREPART & OLI UTAMA
            [
                'kode' => 'DISVCBSTK', 'nama' => 'DISC PAD VARIO CBS', 'group_id' => $sparepart->id, 'satuan_id' => $pcs->id,
                'harga_beli_terakhir' => 15000, 'hpp' => 15000, 'harga_eceran' => 20000, 'harga_grosir' => 17000,
                'stok_minimum' => 10, 'lokasi_rak' => 'A-01', 'aktif' => true, 'barcode' => '8991020001', 'stok_awal' => 24,
            ],
            [
                'kode' => 'OLIFED1L', 'nama' => 'OLI FEDERAL MATIC 1L', 'group_id' => $oli->id, 'satuan_id' => $botol->id,
                'harga_beli_terakhir' => 36000, 'hpp' => 36000, 'harga_eceran' => 45000, 'harga_grosir' => 40000,
                'stok_minimum' => 10, 'lokasi_rak' => 'B-03', 'aktif' => true, 'barcode' => '8991020002', 'stok_awal' => 15,
            ],
            [
                'kode' => 'KMPRMVAR', 'nama' => 'KAMPAS REM VARIO', 'group_id' => $sparepart->id, 'satuan_id' => $set->id,
                'harga_beli_terakhir' => 27000, 'hpp' => 27000, 'harga_eceran' => 35000, 'harga_grosir' => 30000,
                'stok_minimum' => 8, 'lokasi_rak' => 'A-05', 'aktif' => true, 'barcode' => '8991020003', 'stok_awal' => 12,
            ],
            [
                'kode' => 'BUSINGKC', 'nama' => 'BUSI NGK CPR8EA', 'group_id' => $sparepart->id, 'satuan_id' => $pcs->id,
                'harga_beli_terakhir' => 13000, 'hpp' => 13000, 'harga_eceran' => 18000, 'harga_grosir' => 15000,
                'stok_minimum' => 15, 'lokasi_rak' => 'A-09', 'aktif' => true, 'barcode' => '8991020004', 'stok_awal' => 40,
            ],
            [
                'kode' => 'AKIGSYU5', 'nama' => 'AKI GS ASTRA GTZ5S TUBELESS', 'group_id' => $sparepart->id, 'satuan_id' => $pcs->id,
                'harga_beli_terakhir' => 155000, 'hpp' => 155000, 'harga_eceran' => 185000, 'harga_grosir' => 170000,
                'stok_minimum' => 5, 'lokasi_rak' => 'C-01', 'aktif' => true, 'barcode' => '8991020005', 'stok_awal' => 8,
            ],
            [
                'kode' => 'BANFDR9080', 'nama' => 'BAN FDR SPORT XR 90/80-14 TUBELESS', 'group_id' => $sparepart->id, 'satuan_id' => $pcs->id,
                'harga_beli_terakhir' => 185000, 'hpp' => 185000, 'harga_eceran' => 225000, 'harga_grosir' => 205000,
                'stok_minimum' => 5, 'lokasi_rak' => 'D-01', 'aktif' => true, 'barcode' => '8991020006', 'stok_awal' => 10,
            ],
            [
                'kode' => 'OLIYAMA1L', 'nama' => 'OLI YAMALUBE SUPER MATIC 1L', 'group_id' => $oli->id, 'satuan_id' => $botol->id,
                'harga_beli_terakhir' => 42000, 'hpp' => 42000, 'harga_eceran' => 52000, 'harga_grosir' => 47000,
                'stok_minimum' => 10, 'lokasi_rak' => 'B-04', 'aktif' => true, 'barcode' => '8991020007', 'stok_awal' => 20,
            ],
            [
                'kode' => 'OLISHELL08', 'nama' => 'OLI SHELL ADVANCE AX7 0.8L', 'group_id' => $oli->id, 'satuan_id' => $botol->id,
                'harga_beli_terakhir' => 38000, 'hpp' => 38000, 'harga_eceran' => 48000, 'harga_grosir' => 43000,
                'stok_minimum' => 10, 'lokasi_rak' => 'B-05', 'aktif' => true, 'barcode' => '8991020008', 'stok_awal' => 18,
            ],
            [
                'kode' => 'VBELTVAR125', 'nama' => 'VANBELT V-BELT VARIO 125/150 ORIGINAL', 'group_id' => $sparepart->id, 'satuan_id' => $pcs->id,
                'harga_beli_terakhir' => 110000, 'hpp' => 110000, 'harga_eceran' => 140000, 'harga_grosir' => 125000,
                'stok_minimum' => 5, 'lokasi_rak' => 'A-12', 'aktif' => true, 'barcode' => '8991020009', 'stok_awal' => 6,
            ],
            [
                'kode' => 'ROLLERVAR125', 'nama' => 'ROLLER CVT VARIO 125 SET (15Gram)', 'group_id' => $sparepart->id, 'satuan_id' => $set->id,
                'harga_beli_terakhir' => 35000, 'hpp' => 35000, 'harga_eceran' => 48000, 'harga_grosir' => 42000,
                'stok_minimum' => 5, 'lokasi_rak' => 'A-14', 'aktif' => true, 'barcode' => '8991020010', 'stok_awal' => 15,
            ],
            [
                'kode' => 'FLTRVAR125', 'nama' => 'FILTER UDARA VARIO 125 / 150', 'group_id' => $sparepart->id, 'satuan_id' => $pcs->id,
                'harga_beli_terakhir' => 38000, 'hpp' => 38000, 'harga_eceran' => 50000, 'harga_grosir' => 44000,
                'stok_minimum' => 8, 'lokasi_rak' => 'A-15', 'aktif' => true, 'barcode' => '8991020011', 'stok_awal' => 14,
            ],
            [
                'kode' => 'LMPOSRAMH6', 'nama' => 'LAMPU LED OSRAM T19 M5 H6 PUTIH', 'group_id' => $sparepart->id, 'satuan_id' => $pcs->id,
                'harga_beli_terakhir' => 55000, 'hpp' => 55000, 'harga_eceran' => 75000, 'harga_grosir' => 65000,
                'stok_minimum' => 5, 'lokasi_rak' => 'E-02', 'aktif' => true, 'barcode' => '8991020012', 'stok_awal' => 10,
            ],

            // DATA SEEDER JASA & LAYANAN BENGKEL
            [
                'kode' => 'JSA-TB-BAN', 'nama' => 'JASA TAMBAL BAN TUBELESS', 'group_id' => $jasaGroup->id, 'satuan_id' => $satuanJasa->id,
                'harga_beli_terakhir' => 0, 'hpp' => 0, 'harga_eceran' => 15000, 'harga_grosir' => 15000,
                'stok_minimum' => 0, 'lokasi_rak' => 'BENK-01', 'aktif' => true, 'barcode' => 'JSA001', 'stok_awal' => 999,
            ],
            [
                'kode' => 'JSA-TB-PRM', 'nama' => 'JASA TAMBAL BAN DALAM / PRESS', 'group_id' => $jasaGroup->id, 'satuan_id' => $satuanJasa->id,
                'harga_beli_terakhir' => 0, 'hpp' => 0, 'harga_eceran' => 12000, 'harga_grosir' => 12000,
                'stok_minimum' => 0, 'lokasi_rak' => 'BENK-01', 'aktif' => true, 'barcode' => 'JSA002', 'stok_awal' => 999,
            ],
            [
                'kode' => 'JSA-STL-RNT', 'nama' => 'JASA SETEL & PELUMAS RANTAI', 'group_id' => $jasaGroup->id, 'satuan_id' => $satuanJasa->id,
                'harga_beli_terakhir' => 0, 'hpp' => 0, 'harga_eceran' => 10000, 'harga_grosir' => 10000,
                'stok_minimum' => 0, 'lokasi_rak' => 'BENK-01', 'aktif' => true, 'barcode' => 'JSA003', 'stok_awal' => 999,
            ],
            [
                'kode' => 'JSA-ISI-NGN', 'nama' => 'JASA ISI / TAMBAH ANGIN NITROGEN', 'group_id' => $jasaGroup->id, 'satuan_id' => $satuanJasa->id,
                'harga_beli_terakhir' => 0, 'hpp' => 0, 'harga_eceran' => 5000, 'harga_grosir' => 5000,
                'stok_minimum' => 0, 'lokasi_rak' => 'BENK-01', 'aktif' => true, 'barcode' => 'JSA004', 'stok_awal' => 999,
            ],
            [
                'kode' => 'JSA-GNT-OLI', 'nama' => 'JASA GANTI OLI MESIN / GARDAN', 'group_id' => $jasaGroup->id, 'satuan_id' => $satuanJasa->id,
                'harga_beli_terakhir' => 0, 'hpp' => 0, 'harga_eceran' => 5000, 'harga_grosir' => 5000,
                'stok_minimum' => 0, 'lokasi_rak' => 'BENK-01', 'aktif' => true, 'barcode' => 'JSA005', 'stok_awal' => 999,
            ],
            [
                'kode' => 'JSA-SRV-CVT', 'nama' => 'JASA SERVIS CVT MATIC', 'group_id' => $jasaGroup->id, 'satuan_id' => $satuanJasa->id,
                'harga_beli_terakhir' => 0, 'hpp' => 0, 'harga_eceran' => 35000, 'harga_grosir' => 35000,
                'stok_minimum' => 0, 'lokasi_rak' => 'BENK-01', 'aktif' => true, 'barcode' => 'JSA006', 'stok_awal' => 999,
            ],
            [
                'kode' => 'JSA-SRV-RNG', 'nama' => 'JASA SERVIS RINGAN / TUNE UP', 'group_id' => $jasaGroup->id, 'satuan_id' => $satuanJasa->id,
                'harga_beli_terakhir' => 0, 'hpp' => 0, 'harga_eceran' => 45000, 'harga_grosir' => 45000,
                'stok_minimum' => 0, 'lokasi_rak' => 'BENK-01', 'aktif' => true, 'barcode' => 'JSA007', 'stok_awal' => 999,
            ],
            [
                'kode' => 'JSA-PRB-REM', 'nama' => 'JASA PERBAIKAN / GANTI KAMPAS REM', 'group_id' => $jasaGroup->id, 'satuan_id' => $satuanJasa->id,
                'harga_beli_terakhir' => 0, 'hpp' => 0, 'harga_eceran' => 10000, 'harga_grosir' => 10000,
                'stok_minimum' => 0, 'lokasi_rak' => 'BENK-01', 'aktif' => true, 'barcode' => 'JSA008', 'stok_awal' => 999,
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

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
        $oli = Group::firstOrCreate(['nama' => 'Oli & Cairan']);
        $ban = Group::firstOrCreate(['nama' => 'Ban & Kaki-kaki']);
        $kelistrikan = Group::firstOrCreate(['nama' => 'Kelistrikan & Aki']);
        
        $pcs = Satuan::firstOrCreate(['nama' => 'PCS']);
        $set = Satuan::firstOrCreate(['nama' => 'SET']);
        $botol = Satuan::firstOrCreate(['nama' => 'BOTOL']);
        $dus = Satuan::firstOrCreate(['nama' => 'DUS']);

        $daftar = [
            // 1. OLI & PELUMAS MESIN / TRANSMISI / REM
            [
                'kode' => 'OLIAHM08L', 'nama' => 'OLI AHM MPX2 0.8L MATIC', 'group_id' => $oli->id, 'satuan_id' => $botol->id,
                'harga_beli_terakhir' => 42000, 'hpp' => 42000, 'harga_eceran' => 52000, 'harga_grosir' => 48000,
                'min_qty_grosir_1' => 3, 'harga_grosir_1' => 48000, 'min_qty_grosir_2' => 24, 'harga_grosir_2' => 45000,
                'stok_minimum' => 10, 'lokasi_rak' => 'B-01', 'aktif' => true, 'barcode' => '8991020001', 'stok_awal' => 36,
            ],
            [
                'kode' => 'OLIAHMPX1', 'nama' => 'OLI AHM MPX1 0.8L BEBEK / SPORT', 'group_id' => $oli->id, 'satuan_id' => $botol->id,
                'harga_beli_terakhir' => 41000, 'hpp' => 41000, 'harga_eceran' => 50000, 'harga_grosir' => 46000,
                'min_qty_grosir_1' => 3, 'harga_grosir_1' => 46000, 'min_qty_grosir_2' => 24, 'harga_grosir_2' => 44000,
                'stok_minimum' => 10, 'lokasi_rak' => 'B-02', 'aktif' => true, 'barcode' => '8991020002', 'stok_awal' => 24,
            ],
            [
                'kode' => 'OLIFED1L', 'nama' => 'OLI FEDERAL MATIC 1L', 'group_id' => $oli->id, 'satuan_id' => $botol->id,
                'harga_beli_terakhir' => 36000, 'hpp' => 36000, 'harga_eceran' => 45000, 'harga_grosir' => 41000,
                'min_qty_grosir_1' => 3, 'harga_grosir_1' => 41000, 'min_qty_grosir_2' => 24, 'harga_grosir_2' => 38500,
                'stok_minimum' => 10, 'lokasi_rak' => 'B-03', 'aktif' => true, 'barcode' => '8991020003', 'stok_awal' => 20,
            ],
            [
                'kode' => 'OLIYAMA1L', 'nama' => 'OLI YAMALUBE SUPER MATIC 1L', 'group_id' => $oli->id, 'satuan_id' => $botol->id,
                'harga_beli_terakhir' => 42000, 'hpp' => 42000, 'harga_eceran' => 52000, 'harga_grosir' => 48000,
                'min_qty_grosir_1' => 3, 'harga_grosir_1' => 48000, 'min_qty_grosir_2' => 24, 'harga_grosir_2' => 44000,
                'stok_minimum' => 10, 'lokasi_rak' => 'B-04', 'aktif' => true, 'barcode' => '8991020004', 'stok_awal' => 25,
            ],
            [
                'kode' => 'OLISHELL08', 'nama' => 'OLI SHELL ADVANCE AX7 0.8L', 'group_id' => $oli->id, 'satuan_id' => $botol->id,
                'harga_beli_terakhir' => 38000, 'hpp' => 38000, 'harga_eceran' => 48000, 'harga_grosir' => 43500,
                'min_qty_grosir_1' => 3, 'harga_grosir_1' => 43500, 'min_qty_grosir_2' => 24, 'harga_grosir_2' => 40000,
                'stok_minimum' => 10, 'lokasi_rak' => 'B-05', 'aktif' => true, 'barcode' => '8991020005', 'stok_awal' => 18,
            ],
            [
                'kode' => 'OLIGARDAN', 'nama' => 'OLI GARDAN AHM MATIC 120ML', 'group_id' => $oli->id, 'satuan_id' => $botol->id,
                'harga_beli_terakhir' => 12000, 'hpp' => 12000, 'harga_eceran' => 17000, 'harga_grosir' => 15000,
                'min_qty_grosir_1' => 3, 'harga_grosir_1' => 15000, 'min_qty_grosir_2' => 24, 'harga_grosir_2' => 13500,
                'stok_minimum' => 15, 'lokasi_rak' => 'B-06', 'aktif' => true, 'barcode' => '8991020006', 'stok_awal' => 48,
            ],
            [
                'kode' => 'MYKREMDO3', 'nama' => 'MINYAK REM JUMBO DOT 3 50ML', 'group_id' => $oli->id, 'satuan_id' => $botol->id,
                'harga_beli_terakhir' => 7000, 'hpp' => 7000, 'harga_eceran' => 12000, 'harga_grosir' => 10000,
                'min_qty_grosir_1' => 3, 'harga_grosir_1' => 10000, 'min_qty_grosir_2' => 24, 'harga_grosir_2' => 8500,
                'stok_minimum' => 10, 'lokasi_rak' => 'B-07', 'aktif' => true, 'barcode' => '8991020007', 'stok_awal' => 24,
            ],
            [
                'kode' => 'COOLANTAHM', 'nama' => 'AIR RADIATOR COOLANT AHM 500ML', 'group_id' => $oli->id, 'satuan_id' => $botol->id,
                'harga_beli_terakhir' => 15000, 'hpp' => 15000, 'harga_eceran' => 22000, 'harga_grosir' => 19000,
                'min_qty_grosir_1' => 3, 'harga_grosir_1' => 19000, 'min_qty_grosir_2' => 24, 'harga_grosir_2' => 17000,
                'stok_minimum' => 8, 'lokasi_rak' => 'B-08', 'aktif' => true, 'barcode' => '8991020008', 'stok_awal' => 15,
            ],

            // 2. SPAREPART FAST MOVING & CVT
            [
                'kode' => 'DISVCBSTK', 'nama' => 'DISC PAD VARIO CBS', 'group_id' => $sparepart->id, 'satuan_id' => $pcs->id,
                'harga_beli_terakhir' => 15000, 'hpp' => 15000, 'harga_eceran' => 20000, 'harga_grosir' => 18000,
                'min_qty_grosir_1' => 3, 'harga_grosir_1' => 18000, 'min_qty_grosir_2' => 24, 'harga_grosir_2' => 16500,
                'stok_minimum' => 10, 'lokasi_rak' => 'A-01', 'aktif' => true, 'barcode' => '8991020009', 'stok_awal' => 24,
            ],
            [
                'kode' => 'KMPRMVAR', 'nama' => 'KAMPAS REM BELAKANG TROMOL VARIO/BEAT', 'group_id' => $sparepart->id, 'satuan_id' => $set->id,
                'harga_beli_terakhir' => 27000, 'hpp' => 27000, 'harga_eceran' => 35000, 'harga_grosir' => 31000,
                'min_qty_grosir_1' => 3, 'harga_grosir_1' => 31000, 'min_qty_grosir_2' => 24, 'harga_grosir_2' => 29000,
                'stok_minimum' => 8, 'lokasi_rak' => 'A-02', 'aktif' => true, 'barcode' => '8991020010', 'stok_awal' => 16,
            ],
            [
                'kode' => 'BUSINGKC', 'nama' => 'BUSI NGK CPR8EA (BEAT / VARIO / SCOOPY)', 'group_id' => $sparepart->id, 'satuan_id' => $pcs->id,
                'harga_beli_terakhir' => 13000, 'hpp' => 13000, 'harga_eceran' => 18000, 'harga_grosir' => 16000,
                'min_qty_grosir_1' => 3, 'harga_grosir_1' => 16000, 'min_qty_grosir_2' => 24, 'harga_grosir_2' => 14500,
                'stok_minimum' => 15, 'lokasi_rak' => 'A-03', 'aktif' => true, 'barcode' => '8991020011', 'stok_awal' => 40,
            ],
            [
                'kode' => 'BUSIDENSO', 'nama' => 'BUSI DENSO U24EPR-9 BEBEK / MATIC', 'group_id' => $sparepart->id, 'satuan_id' => $pcs->id,
                'harga_beli_terakhir' => 12500, 'hpp' => 12500, 'harga_eceran' => 18000, 'harga_grosir' => 15500,
                'min_qty_grosir_1' => 3, 'harga_grosir_1' => 15500, 'min_qty_grosir_2' => 24, 'harga_grosir_2' => 14000,
                'stok_minimum' => 15, 'lokasi_rak' => 'A-04', 'aktif' => true, 'barcode' => '8991020012', 'stok_awal' => 30,
            ],
            [
                'kode' => 'VBELTVAR125', 'nama' => 'VANBELT V-BELT VARIO 125/150 ORIGINAL AHM', 'group_id' => $sparepart->id, 'satuan_id' => $pcs->id,
                'harga_beli_terakhir' => 110000, 'hpp' => 110000, 'harga_eceran' => 140000, 'harga_grosir' => 128000,
                'min_qty_grosir_1' => 3, 'harga_grosir_1' => 128000, 'min_qty_grosir_2' => 12, 'harga_grosir_2' => 118000,
                'stok_minimum' => 5, 'lokasi_rak' => 'A-05', 'aktif' => true, 'barcode' => '8991020013', 'stok_awal' => 10,
            ],
            [
                'kode' => 'VBELTBEATFI', 'nama' => 'VANBELT V-BELT BEAT FI / SCOOPY FI', 'group_id' => $sparepart->id, 'satuan_id' => $pcs->id,
                'harga_beli_terakhir' => 85000, 'hpp' => 85000, 'harga_eceran' => 110000, 'harga_grosir' => 98000,
                'min_qty_grosir_1' => 3, 'harga_grosir_1' => 98000, 'min_qty_grosir_2' => 12, 'harga_grosir_2' => 92000,
                'stok_minimum' => 5, 'lokasi_rak' => 'A-06', 'aktif' => true, 'barcode' => '8991020014', 'stok_awal' => 12,
            ],
            [
                'kode' => 'ROLLERVAR125', 'nama' => 'ROLLER CVT VARIO 125 SET (15 Gram)', 'group_id' => $sparepart->id, 'satuan_id' => $set->id,
                'harga_beli_terakhir' => 35000, 'hpp' => 35000, 'harga_eceran' => 48000, 'harga_grosir' => 43000,
                'min_qty_grosir_1' => 3, 'harga_grosir_1' => 43000, 'min_qty_grosir_2' => 24, 'harga_grosir_2' => 39000,
                'stok_minimum' => 5, 'lokasi_rak' => 'A-07', 'aktif' => true, 'barcode' => '8991020015', 'stok_awal' => 15,
            ],
            [
                'kode' => 'ROLLERBEAT', 'nama' => 'ROLLER CVT BEAT FI SET (13 Gram)', 'group_id' => $sparepart->id, 'satuan_id' => $set->id,
                'harga_beli_terakhir' => 32000, 'hpp' => 32000, 'harga_eceran' => 45000, 'harga_grosir' => 40000,
                'min_qty_grosir_1' => 3, 'harga_grosir_1' => 40000, 'min_qty_grosir_2' => 24, 'harga_grosir_2' => 36000,
                'stok_minimum' => 5, 'lokasi_rak' => 'A-08', 'aktif' => true, 'barcode' => '8991020016', 'stok_awal' => 18,
            ],
            [
                'kode' => 'FLTRVAR125', 'nama' => 'FILTER UDARA VARIO 125 / 150', 'group_id' => $sparepart->id, 'satuan_id' => $pcs->id,
                'harga_beli_terakhir' => 38000, 'hpp' => 38000, 'harga_eceran' => 50000, 'harga_grosir' => 45000,
                'min_qty_grosir_1' => 3, 'harga_grosir_1' => 45000, 'min_qty_grosir_2' => 24, 'harga_grosir_2' => 41000,
                'stok_minimum' => 8, 'lokasi_rak' => 'A-09', 'aktif' => true, 'barcode' => '8991020017', 'stok_awal' => 14,
            ],
            [
                'kode' => 'FLTRBEATFI', 'nama' => 'FILTER UDARA BEAT FI / SCOOPY FI', 'group_id' => $sparepart->id, 'satuan_id' => $pcs->id,
                'harga_beli_terakhir' => 34000, 'hpp' => 34000, 'harga_eceran' => 45000, 'harga_grosir' => 40000,
                'min_qty_grosir_1' => 3, 'harga_grosir_1' => 40000, 'min_qty_grosir_2' => 24, 'harga_grosir_2' => 37000,
                'stok_minimum' => 8, 'lokasi_rak' => 'A-10', 'aktif' => true, 'barcode' => '8991020018', 'stok_awal' => 20,
            ],
            [
                'kode' => 'RNTAISETGLP', 'nama' => 'RANTAI & GIR SET (GEAR SET) SUPRA X 125', 'group_id' => $sparepart->id, 'satuan_id' => $set->id,
                'harga_beli_terakhir' => 145000, 'hpp' => 145000, 'harga_eceran' => 185000, 'harga_grosir' => 170000,
                'min_qty_grosir_1' => 2, 'harga_grosir_1' => 170000, 'min_qty_grosir_2' => 10, 'harga_grosir_2' => 160000,
                'stok_minimum' => 4, 'lokasi_rak' => 'A-11', 'aktif' => true, 'barcode' => '8991020019', 'stok_awal' => 8,
            ],

            // 3. KELISTRIKAN & AKI
            [
                'kode' => 'AKIGSYU5', 'nama' => 'AKI GS ASTRA GTZ5S TUBELESS (BEAT/VARIO/MIO)', 'group_id' => $kelistrikan->id, 'satuan_id' => $pcs->id,
                'harga_beli_terakhir' => 155000, 'hpp' => 155000, 'harga_eceran' => 185000, 'harga_grosir' => 172000,
                'min_qty_grosir_1' => 3, 'harga_grosir_1' => 172000, 'min_qty_grosir_2' => 10, 'harga_grosir_2' => 165000,
                'stok_minimum' => 5, 'lokasi_rak' => 'C-01', 'aktif' => true, 'barcode' => '8991020020', 'stok_awal' => 12,
            ],
            [
                'kode' => 'AKIYUASAYTZ5', 'nama' => 'AKI YUASA YTZ5S MF KERING', 'group_id' => $kelistrikan->id, 'satuan_id' => $pcs->id,
                'harga_beli_terakhir' => 160000, 'hpp' => 160000, 'harga_eceran' => 195000, 'harga_grosir' => 180000,
                'min_qty_grosir_1' => 3, 'harga_grosir_1' => 180000, 'min_qty_grosir_2' => 10, 'harga_grosir_2' => 172000,
                'stok_minimum' => 5, 'lokasi_rak' => 'C-02', 'aktif' => true, 'barcode' => '8991020021', 'stok_awal' => 10,
            ],
            [
                'kode' => 'LMPOSRAMH6', 'nama' => 'LAMPU LED OSRAM T19 M5 H6 PUTIH', 'group_id' => $kelistrikan->id, 'satuan_id' => $pcs->id,
                'harga_beli_terakhir' => 55000, 'hpp' => 55000, 'harga_eceran' => 75000, 'harga_grosir' => 68000,
                'min_qty_grosir_1' => 3, 'harga_grosir_1' => 68000, 'min_qty_grosir_2' => 12, 'harga_grosir_2' => 62000,
                'stok_minimum' => 5, 'lokasi_rak' => 'C-03', 'aktif' => true, 'barcode' => '8991020022', 'stok_awal' => 15,
            ],

            // 4. BAN LUAR & BAN DALAM
            [
                'kode' => 'BANFDR9080', 'nama' => 'BAN FDR SPORT XR 90/80-14 TUBELESS', 'group_id' => $ban->id, 'satuan_id' => $pcs->id,
                'harga_beli_terakhir' => 185000, 'hpp' => 185000, 'harga_eceran' => 225000, 'harga_grosir' => 210000,
                'min_qty_grosir_1' => 3, 'harga_grosir_1' => 210000, 'min_qty_grosir_2' => 10, 'harga_grosir_2' => 198000,
                'stok_minimum' => 5, 'lokasi_rak' => 'D-01', 'aktif' => true, 'barcode' => '8991020023', 'stok_awal' => 10,
            ],
            [
                'kode' => 'BANIRC8090', 'nama' => 'BAN IRC ENVIRO 80/90-14 TUBELESS', 'group_id' => $ban->id, 'satuan_id' => $pcs->id,
                'harga_beli_terakhir' => 165000, 'hpp' => 165000, 'harga_eceran' => 205000, 'harga_grosir' => 190000,
                'min_qty_grosir_1' => 3, 'harga_grosir_1' => 190000, 'min_qty_grosir_2' => 10, 'harga_grosir_2' => 178000,
                'stok_minimum' => 5, 'lokasi_rak' => 'D-02', 'aktif' => true, 'barcode' => '8991020024', 'stok_awal' => 12,
            ],
            [
                'kode' => 'BANDLMIRC14', 'nama' => 'BAN DALAM IRC 2.50/2.75-14', 'group_id' => $ban->id, 'satuan_id' => $pcs->id,
                'harga_beli_terakhir' => 24000, 'hpp' => 24000, 'harga_eceran' => 35000, 'harga_grosir' => 30000,
                'min_qty_grosir_1' => 3, 'harga_grosir_1' => 30000, 'min_qty_grosir_2' => 20, 'harga_grosir_2' => 27000,
                'stok_minimum' => 10, 'lokasi_rak' => 'D-03', 'aktif' => true, 'barcode' => '8991020025', 'stok_awal' => 25,
            ],
            [
                'kode' => 'PENTILTUBEL', 'nama' => 'PENTIL BAN TUBELESS BESI / KARET', 'group_id' => $ban->id, 'satuan_id' => $pcs->id,
                'harga_beli_terakhir' => 4000, 'hpp' => 4000, 'harga_eceran' => 10000, 'harga_grosir' => 7500,
                'min_qty_grosir_1' => 5, 'harga_grosir_1' => 7500, 'min_qty_grosir_2' => 50, 'harga_grosir_2' => 5500,
                'stok_minimum' => 20, 'lokasi_rak' => 'D-04', 'aktif' => true, 'barcode' => '8991020026', 'stok_awal' => 50,
            ],
        ];

        foreach ($daftar as $item) {
            $stokAwal = $item['stok_awal'] ?? 0;
            $barcode = $item['barcode'] ?? null;
            unset($item['stok_awal'], $item['barcode']);

            $barang = Barang::firstOrCreate(
                ['kode' => $item['kode']],
                $item
            );

            // Mutasi stok awal
            if ($stokAwal > 0) {
                StokMutasi::firstOrCreate(
                    [
                        'barang_id' => $barang->id,
                        'no_dokumen' => 'STOK-AWAL-' . $barang->kode,
                    ],
                    [
                        'tanggal' => now()->toDateString(),
                        'jenis_mutasi' => 'penyesuaian',
                        'masuk' => $stokAwal,
                        'keluar' => 0,
                        'hpp' => $barang->hpp,
                        'keterangan' => 'Saldo Stok Awal Toko',
                    ]
                );
            }

            // Daftarkan barcode
            if ($barcode) {
                \App\Models\BarangBarcode::firstOrCreate(
                    ['barcode' => $barcode],
                    ['barang_id' => $barang->id]
                );
            }
        }
    }
}

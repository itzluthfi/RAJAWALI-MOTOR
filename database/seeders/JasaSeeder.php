<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Jasa;
use Illuminate\Database\Seeder;

class JasaSeeder extends Seeder
{
    public function run(): void
    {
        $daftarJasa = [
            // 1. OLI & PELUMASAN
            [
                'kode' => 'JSA-GNT-OLI',
                'nama' => 'Jasa Ganti Oli Mesin / Gardan',
                'kategori' => 'Oli & Pelumasan',
                'tarif' => 10000,
                'komisi_montir' => 3000,
                'keterangan' => 'Penggantian oli mesin atau oli transmisi matic + cek level oli',
            ],
            [
                'kode' => 'JSA-STL-RNT',
                'nama' => 'Jasa Setel & Pelumas Rantai Roda',
                'kategori' => 'Oli & Pelumasan',
                'tarif' => 10000,
                'komisi_montir' => 3000,
                'keterangan' => 'Penyetelan ketegangan rantai dan pelumasan chain lube',
            ],

            // 2. SERVIS BERKALA & TUNE UP
            [
                'kode' => 'JSA-TUNEUP-MT',
                'nama' => 'Jasa Tune Up & Gurah Injeksi Matic',
                'kategori' => 'Servis Berkala & Tune Up',
                'tarif' => 50000,
                'komisi_montir' => 20000,
                'keterangan' => 'Pembersihan throttle body, injektor cleaner, busi, dan reset ECU',
            ],
            [
                'kode' => 'JSA-TUNEUP-BB',
                'nama' => 'Jasa Tune Up & Bersih Karburator Bebek/Sport',
                'kategori' => 'Servis Berkala & Tune Up',
                'tarif' => 40000,
                'komisi_montir' => 15000,
                'keterangan' => 'Pembersihan karburator, spuyer, filter udara, dan setel angin',
            ],
            [
                'kode' => 'JSA-SRV-RINGAN',
                'nama' => 'Jasa Servis Ringan & Cek 10 Titik Motor',
                'kategori' => 'Servis Berkala & Tune Up',
                'tarif' => 35000,
                'komisi_montir' => 12000,
                'keterangan' => 'Pengecekan rem, busi, aki, tekanan ban, baut bodi, dan kelistrikan',
            ],

            // 3. SERVIS CVT & TRANSMISI MATIC
            [
                'kode' => 'JSA-SRV-CVT',
                'nama' => 'Jasa Servis & Pembersihan CVT Lengkap',
                'kategori' => 'Servis CVT & Matic',
                'tarif' => 40000,
                'komisi_montir' => 15000,
                'keterangan' => 'Bongkar pulley depan-belakang, cuci part CVT, dan gemuk grease CVT',
            ],
            [
                'kode' => 'JSA-GNT-VBLT',
                'nama' => 'Jasa Pasang Vanbelt / Roller CVT',
                'kategori' => 'Servis CVT & Matic',
                'tarif' => 25000,
                'komisi_montir' => 10000,
                'keterangan' => 'Bongkar pasang sabuk v-belt baru dan roller',
            ],
            [
                'kode' => 'JSA-GNT-KMPGND',
                'nama' => 'Jasa Pasang Kampas Ganda & Mangkok Kopling',
                'kategori' => 'Servis CVT & Matic',
                'tarif' => 30000,
                'komisi_montir' => 10000,
                'keterangan' => 'Penggantian sepatu kampas ganda kopling matic',
            ],

            // 4. PENGEREMAN
            [
                'kode' => 'JSA-KMPS-DPN',
                'nama' => 'Jasa Ganti Kampas Rem Depan (Disc / Tromol)',
                'kategori' => 'Pengereman',
                'tarif' => 15000,
                'komisi_montir' => 5000,
                'keterangan' => 'Pasang kampas rem depan, pembersihan kaliper, dan setel jarak main',
            ],
            [
                'kode' => 'JSA-KMPS-BLK',
                'nama' => 'Jasa Ganti Kampas Rem Belakang',
                'kategori' => 'Pengereman',
                'tarif' => 15000,
                'komisi_montir' => 5000,
                'keterangan' => 'Bongkar pasang brake shoe/pad belakang dan setel rem',
            ],
            [
                'kode' => 'JSA-KURAS-MYK',
                'nama' => 'Jasa Kuras & Ganti Minyak Rem (Bleeding)',
                'kategori' => 'Pengereman',
                'tarif' => 25000,
                'komisi_montir' => 10000,
                'keterangan' => 'Kuras saluran minyak rem dan buang angin palsu sistem hidrolik',
            ],

            // 5. BAN & KAKI-KAKI
            [
                'kode' => 'JSA-TB-TUBEL',
                'nama' => 'Jasa Tambal Ban Tubeless (String / Cacing)',
                'kategori' => 'Ban & Kaki-kaki',
                'tarif' => 15000,
                'komisi_montir' => 5000,
                'keterangan' => 'Penambalan kebocoran ban tubeless menggunakan tire patch',
            ],
            [
                'kode' => 'JSA-TB-DALAM',
                'nama' => 'Jasa Tambal Ban Dalam / Press Panas',
                'kategori' => 'Ban & Kaki-kaki',
                'tarif' => 12000,
                'komisi_montir' => 4000,
                'keterangan' => 'Bongkar pasang dan press ban dalam',
            ],
            [
                'kode' => 'JSA-PSG-BAN',
                'nama' => 'Jasa Pasang Ban Luar / Tubeless',
                'kategori' => 'Ban & Kaki-kaki',
                'tarif' => 15000,
                'komisi_montir' => 5000,
                'keterangan' => 'Bongkar pasang ban baru ke velg motor',
            ],
            [
                'kode' => 'JSA-GNT-BEAR',
                'nama' => 'Jasa Ganti Bearing / Laher Roda Depan/Belakang',
                'kategori' => 'Ban & Kaki-kaki',
                'tarif' => 20000,
                'komisi_montir' => 7000,
                'keterangan' => 'Pelepasan laher aus dan pasang bearing roda baru',
            ],
            [
                'kode' => 'JSA-SRV-SHOCK',
                'nama' => 'Jasa Servis Shock Depan (Ganti Seal & Oli Shock)',
                'kategori' => 'Ban & Kaki-kaki',
                'tarif' => 60000,
                'komisi_montir' => 25000,
                'keterangan' => 'Bongkar suspensi teleskopik depan, kuras oli shock, dan ganti seal oli',
            ],
            [
                'kode' => 'JSA-STL-VELG',
                'nama' => 'Jasa Setel Velg Jari-jari (Spoke Wheel)',
                'kategori' => 'Ban & Kaki-kaki',
                'tarif' => 35000,
                'komisi_montir' => 15000,
                'keterangan' => 'Penyetelan kelurusan dan balance velg jari-jari',
            ],

            // 6. KELISTRIKAN & PENGAPIAN
            [
                'kode' => 'JSA-GNT-AKI',
                'nama' => 'Jasa Pasang Aki & Cek Sistem Pengisian',
                'kategori' => 'Kelistrikan & Pengapian',
                'tarif' => 10000,
                'komisi_montir' => 3000,
                'keterangan' => 'Pemasangan aki baru dan uji voltase kiprok/spul',
            ],
            [
                'kode' => 'JSA-GNT-BUSI',
                'nama' => 'Jasa Pasang Busi & Cek Pengapian',
                'kategori' => 'Kelistrikan & Pengapian',
                'tarif' => 10000,
                'komisi_montir' => 3000,
                'keterangan' => 'Bongkar pasang busi dan cek celah elektroda',
            ],
            [
                'kode' => 'JSA-SRV-STARTER',
                'nama' => 'Jasa Servis Dinamo Starter & Ganti Carbon Brush',
                'kategori' => 'Kelistrikan & Pengapian',
                'tarif' => 45000,
                'komisi_montir' => 15000,
                'keterangan' => 'Bongkar dinamo stater, bersihkan rotor, dan ganti arang starter',
            ],

            // 7. SERVIS BERAT & TURUN MESIN
            [
                'kode' => 'JSA-GNT-PISTON',
                'nama' => 'Jasa Ganti Piston / Silinder Blok Mesin',
                'kategori' => 'Turun Mesin (Overhaul)',
                'tarif' => 150000,
                'komisi_montir' => 60000,
                'keterangan' => 'Bongkar silinder head, korter/ganti blok dan piston set baru',
            ],
            [
                'kode' => 'JSA-SKIR-KLEP',
                'nama' => 'Jasa Skir Klep & Ganti Seal Klep Mesin',
                'kategori' => 'Turun Mesin (Overhaul)',
                'tarif' => 100000,
                'komisi_montir' => 40000,
                'keterangan' => 'Penyetelan payung klep, skir dudukan klep, dan ganti seal klep',
            ],
            [
                'kode' => 'JSA-TURUN-MSN',
                'nama' => 'Jasa Turun Mesin Total (Overhaul Mesin)',
                'kategori' => 'Turun Mesin (Overhaul)',
                'tarif' => 250000,
                'komisi_montir' => 100000,
                'keterangan' => 'Bongkar total belah mesin, pembersihan kruk as, ganti stang seher & paking',
            ],
        ];

        foreach ($daftarJasa as $item) {
            Jasa::firstOrCreate(
                ['kode' => $item['kode']],
                array_merge($item, ['aktif' => true])
            );
        }
    }
}
